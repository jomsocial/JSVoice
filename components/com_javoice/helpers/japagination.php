<?php 
/**
 * ------------------------------------------------------------------------
 * JA Voice Package for Joomla 2.5 & 3.x
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.pagination');

class JAPagination extends JPagination
{
	var $_divid = null;
	var $_link = null;
	var $item_active    		= '';
	var	$item_inactive  		= '';
	var	$item_list_render 		= '';
	var	$item_list_footer 		= '';
	var	$item_item_inactive2 	= '';
	/**
	 * Constructor
	 *
	 * @param	int		The total number of items
	 * @param	int		The offset of the item to start at
	 * @param	int		The number of items to display per page
	 * @param 	string	div id will be updated by ajax
	 */
	function __construct($total, $limitstart, $limit, $divid = '',$link='')
	{
		global $javconfig;
		$mainframe = JFactory::getApplication();
		
		
		// Value/Type checking
		$this->total		= (int) $total;
		$this->limitstart	= (int) max($limitstart, 0);
		$this->limit		= (int) max($limit, 0);
		
		if ($this->limit > $this->total) {
			$this->limitstart = 0;
		}

		if (!$this->limit)
		{
			$this->limit = $total;
			$this->limitstart = 0;
		}

		if ($this->limitstart > $this->total) {
			$this->limitstart -= $this->limitstart % $this->limit;
		}

		// Set the total pages and current page values
		if($this->limit > 0)
		{
			$this->set( 'pages.total', ceil($this->total / $this->limit));
			$this->set( 'pages.current', ceil(($this->limitstart + 1) / $this->limit));
		}

		// Set the pagination iteration loop values
		$displayedPages	= $javconfig["plugin"]->get("displaynumberpagination",10);
		$this->set( 'pages.start', (floor(($this->get('pages.current') -1) / $displayedPages)) * $displayedPages +1);
		if ($this->get('pages.start') + $displayedPages -1 < $this->get('pages.total')) {
			$this->set( 'pages.stop', $this->get('pages.start') + $displayedPages -1);
		} else {
			$this->set( 'pages.stop', $this->get('pages.total'));
		}

		// If we are viewing all records set the view all flag to true
		$this->_viewall = false;
		if ($this->limit == $total) {
			$this->_viewall = true;
		}
		$this->_divid = $divid;
		$this->_link = $link;
	}
	
	function _item_active(&$item)
	{
		$mainframe = JFactory::getApplication();
		$item->link2 = str_replace("index.php","index2.php",$item->link);	
		$item->link2 =$item->link;
		//$pos = strpos($item->link2,"?");
		//if (!$pos) 
		//{
		//	$item->link2 .= "?tmpl=component&amp;option=com_javoice&amp;layout=paging";
		//}else {
			$item->link2 .= "&amp;tmpl=component&amp;option=com_javoice&amp;layout=paging";
		//}		
		if(jRequest::getInt('status')){
			$item->link2 .='&amp;status='.jRequest::getInt('status');
		}
		
		
		if($this->_link){
			return "<li>&nbsp;<a href=\"".$item->link."\" title=\"".$item->text."\" onclick=\"jav_ajaxPagination('".$item->link2."','".$this->_divid."'); return false;\">".$item->text."</a>&nbsp;</li>";
		}		
		//end				
		return "<li>&nbsp;<a href=\"".JRoute::_($item->link)."\" title=\"".$item->text."\" onclick=\"jav_ajaxPagination('".$item->link2."','".$this->_divid."'); return false;\">".$item->text."</a>&nbsp;</li>";
	}

	function _item_inactive(&$item)
	{
		$mainframe = JFactory::getApplication();
		return "<li>&nbsp;<span>".$item->text."</span>&nbsp;</li>";
	}
	
	function _item_inactive2(&$item)
	{
		$mainframe = JFactory::getApplication();
		return "<li class=\"active\">&nbsp;<span>".$item->text."</span>&nbsp;</li>";
	}
	
	function _list_render($list)
	{
		// Initialize variables
		$lang = JFactory::getLanguage();
		$html = "<ul class=\"pagination\">";
		$html .= '';
		// Reverse output rendering for right-to-left display
		if($lang->isRTL())
		{
			$html .= $list['start']['data'];
			$html .= $list['previous']['data'];
	
			$list['pages'] = array_reverse( $list['pages'] );
	
			foreach( $list['pages'] as $page ) {
				if($page['data']['active']) {
					//  $html .= '<strong>';
				}
	
				$html .= $page['data'];
	
				if($page['data']['active']) {
					// $html .= '</strong>';
				}
			}
	
			$html .= $list['next']['data'];
			$html .= $list['end']['data'];
			// $html .= '&#171;';
		}
		else
		{
			$html .= $list['start']['data'];
			$html .= $list['previous']['data'];
	
			foreach( $list['pages'] as $page )
			{
				/*if($page['data']['active']) {
					// $html .= '<strong>';
				}*/
	
				$html .= $page['data'];
	
				/*if($page['data']['active']) {
					//  $html .= '</strong>';
				}*/
			}
	
			$html .= $list['next']['data'];
			$html .= $list['end']['data'];
			// $html .= '&#171;';
	
		}
		$html .= '';
		$html .= "</ul>";
		return $html;
	}
	
	function _list_footer($list)
	{
		// Initialize variables
		$mainframe = JFactory::getApplication();
	
		$overrideJP = JPATH_THEMES.DS.$mainframe->getTemplate().DS.'html'.DS.'japagination.php';
		if(file_exists($overrideJP)){
			require $overrideJP;
			if(function_exists('japagination_list_footer')){
				$item_list_footer = 'japagination_list_footer';
			}
		}
		/*$chromePath = JPATH_THEMES.DS.$mainframe->getTemplate().DS.'html'.DS.'pagination.php';
		if (file_exists($chromePath))
		{
			require_once ($chromePath);
			if(function_exists('pagination_list_footer')){
				$item_list_footer = 'pagination_list_footer';
			}	
		}*/
		if($item_list_footer){
			return $item_list_footer($list);
		}else{
			$lang = JFactory::getLanguage();
			$html = "<div class=\"list-footer\">\n";
		
			if ($lang->isRTL())
			{
				$html .= "\n<div class=\"counter\">".$list['pagescounter']."</div>";
				$html .= $list['pageslinks'];
				$html .= "\n<div class=\"limit\">".JText::_('DISPLAY_NUM').$list['limitfield']."</div>";
			}
			else
			{
				$html .= "\n<div class=\"limit\">".JText::_('DISPLAY_NUM').$list['limitfield']."</div>";
				$html .= $list['pageslinks'];
				$html .= "\n<div class=\"counter\">".$list['pagescounter']."</div>";
			}
		
			$html .= "\n<input type=\"hidden\" name=\"limitstart\" value=\"".$list['limitstart']."\" />";
			$html .= "\n</div>";
		
			return $html;
		}
	}
	/*
	 * Convert data 
	 * */
	function convertdata($items){
	
		$items->link2 = str_replace("index.php","index2.php",$items->link);	
		$items->link2 = $items->link;
		$items->link2 .= "&amp;tmpl=component&amp;option=com_javoice&amp;layout=paging";
		if(!$this->_link){
			$items->link = JRoute::_($items->link);
		}
		$items->_divid = $this->_divid;
		return $items;
	}
	/**
	 * Create and return the pagination page list string, ie. Previous, Next, 1 2 3 ... x
	 *
	 * @access	public
	 * @return	string	Pagination page list string
	 * @since	1.0
	 */
	function getPagesLinks()
	{
		$mainframe = JFactory::getApplication();
	
		$overrideJP = JPATH_THEMES.DS.$mainframe->getTemplate().DS.'html'.DS.'japagination.php';
		if(file_exists($overrideJP)){
			require $overrideJP;
			if (function_exists('japagination_item_active')) {
				$item_active = 'japagination_item_active';
			}
			if(function_exists('japagination_item_inactive')){
				$item_inactive = 'japagination_item_inactive';
			}
			if(function_exists('japagination_item_inactive2')){
				$item_inactive = 'japagination_item_inactive2';
			}
			if (function_exists('japagination_list_render')) {
				$item_list_render = 'japagination_list_render';
			}
			
		}
		/*$chromePath = JPATH_THEMES.DS.$mainframe->getTemplate().DS.'html'.DS.'pagination.php';
		if (file_exists($chromePath))
		{
			require_once ($chromePath);
			if (function_exists('pagination_item_active')) {
				$item_active = 'pagination_item_active';
			}
			if(function_exists('pagination_item_inactive')){
				$item_inactive = 'pagination_item_inactive';
			}
			if(function_exists('pagination_item_inactive2')){
				$item_inactive = 'pagination_item_inactive2';
			}
			if (function_exists('pagination_list_render')) {
				$item_list_render = 'pagination_list_render';
			}
			
		}*/
		$lang = JFactory::getLanguage();
		// Build the page navigation list
		$data = $this->_buildDataObject();
		$list = array();
		
		
		// Build the select list
		if ($data->all->base !== null) {
			$list['all']['active'] = true;
			$list['all']['data'] = isset($item_active)?$item_active($this->convertdata($data->all)):$this->_item_active($data->all);
		} else {
			$list['all']['active'] = false;
			$list['all']['data'] = isset($item_inactive)?$item_inactive($data->all):$this->_item_inactive($data->all);
		}

		if ($data->start->base !== null) {
			$list['start']['active'] = true;
			$list['start']['data'] = isset($item_active)?$item_active($this->convertdata($data->start)):$this->_item_active($data->start);
		} else {
			$list['start']['active'] = false;
			$list['start']['data'] = isset($item_inactive)?$item_inactive($data->start):$this->_item_inactive($data->start);
		}
		if ($data->previous->base !== null) {
			$list['previous']['active'] = true;
			$list['previous']['data'] = isset($item_active)?$item_active($this->convertdata($data->previous)):$this->_item_active($data->previous);
		} else {
			$list['previous']['active'] = false;
			$list['previous']['data'] = isset($item_inactive)?$item_inactive($data->previous):$this->_item_inactive($data->previous);
		}

		$list['pages'] = array(); //make sure it exists
		foreach ($data->pages as $i => $page)
		{
			if ($page->base !== null) {
				$list['pages'][$i]['active'] = true;
				$list['pages'][$i]['data'] = isset($item_active)?$item_active($this->convertdata($page)):$this->_item_active($page);
			} else {
				$list['pages'][$i]['active'] = false;
				$list['pages'][$i]['data'] = isset($item_item_inactive2)?$item_item_inactive2($page):$this->_item_inactive2($page);
			}
		}

		if ($data->next->base !== null) {
			$list['next']['active'] = true;
			$list['next']['data'] = isset($item_active)?$item_active($this->convertdata($data->next)):$this->_item_active($data->next);
		} else {
			$list['next']['active'] = false;
			$list['next']['data'] = isset($item_inactive)?$item_inactive($data->next):$this->_item_inactive($data->next);
		}
		if ($data->end->base !== null) {
			$list['end']['active'] = true;
			$list['end']['data'] = isset($item_active)?$item_active($this->convertdata($data->end)):$this->_item_active($data->end);
		} else {
			$list['end']['active'] = false;
			$list['end']['data'] = isset($item_inactive)?$item_inactive($this->end):$this->_item_inactive($data->end);
		}
		if($this->total > $this->limit){
			return isset($item_list_render)?$item_list_render($list):$this->_list_render($list);
		}
		else{
			return '';
		}
	}
	function _swithLink($link)
	{
		if($this->_link)
			return $this->_link.$link."&limit=".$this->limit;
		else 
			//return JRoute::_($link);
			return $link;
	}	
	function _buildDataObject()
	{
		// Initialize variables
		$data = new stdClass();

		$data->all	= new JPaginationObject(JText::_('VIEW_ALL'));
		if (!$this->_viewall) {
			$data->all->base	= '0';
			$data->all->link	= $this->_swithLink("&limitstart=");
		}

		// Set the start and previous data objects
		$data->start	= new JPaginationObject(JText::_('START'));
		$data->previous	= new JPaginationObject(JText::_('PREV'));

		if ($this->get('pages.current') > 1)
		{
			$page = ($this->get('pages.current') -2) * $this->limit;
			
			
			
			$page = $page == 0 ? '' : $page; //set the empty for removal from route

			$data->start->base	= '0';
			$data->start->link	= $this->_swithLink("&limitstart=");
			$data->previous->base	= $page;
			$data->previous->link    = $this->_swithLink("&amp;limitstart=".$page."&limit=".$this->limit);
		}

		// Set the next and end data objects
		$data->next	= new JPaginationObject(JText::_('NEXT'));
		$data->end	= new JPaginationObject(JText::_('END'));

		if ($this->get('pages.current') < $this->get('pages.total'))
		{
			$next = $this->get('pages.current') * $this->limit;
			$end  = ($this->get('pages.total') -1) * $this->limit;

			$data->next->base	= $next;
			$data->next->link    = $this->_swithLink("&amp;limitstart=".$next."&limit=".$this->limit);
			//$data->next->link	= $this->_swithLink("&limitstart=".$next);
			$data->end->base	= $end;
			$data->end->link    = $this->_swithLink("&amp;limitstart=".$end."&limit=".$this->limit);
			//$data->end->link	= $this->_swithLink("&limitstart=".$end);
		}

		$data->pages = array();
		$stop = $this->get('pages.stop');
		for ($i = $this->get('pages.start'); $i <= $stop; $i ++)
		{
			$offset = ($i -1) * $this->limit;

			$offset = $offset == 0 ? '' : $offset;  //set the empty for removal from route
			$data->pages[$i] = new JPaginationObject($i);
			if ($i != $this->get('pages.current') || $this->_viewall)
			{
				$data->pages[$i]->base	= $offset;
				$data->pages[$i]->link    = $this->_swithLink("&amp;limitstart=".$offset."&limit=".$this->limit);
				//$data->pages[$i]->link	= $this->_swithLink("&limitstart=".$offset);
			}
		}
		return $data;
	}	

}
?>