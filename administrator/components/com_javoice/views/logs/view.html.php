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
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.view');

/**
 * @package		Joomla
 * @subpackage	Contacts
 */
class javoiceViewlogs extends JAVBView 
{
	function display($tpl = null)
	{
	//Load left menu
		if (! JRequest::getVar("ajax") && JRequest::getVar('tmpl') != 'component' && JRequest::getVar('viewmenu', 1) != 0) {
			$file = JPATH_COMPONENT.'/views/jaview/view.html.php';
			if (file_exists($file)) {
				include_once($file);
			}
		}
		$layout=JRequest::getVar('layout','default');
		switch ($layout){
			case 'form':
				$this->edit();
				break;
			default:{
				$layout='default';
				$this->displayItems();
				break;
			}
		}		
		$this->setLayout($layout);
		parent::display($tpl);
		// Display menu footer
		if (!JRequest::getVar("ajax") && JRequest::getVar('tmpl') != 'component' && JRequest::getVar('viewmenu', 1) != 0) {
			$file = JPATH_COMPONENT_ADMINISTRATOR . "/views/jaview/tmpl/footer.php";
			if (file_exists($file)) {
				include_once($file);
			}
		}
	}
	function displayItems()
	{
		$model = $this->getModel('logs');
		$lists = $model->getvar();

		$filter_order	= $lists['filter_order'];
		$filter_order_Dir	= $lists['filter_order_Dir']; 
		$limit		= $lists['limit']; 
		$limitstart	= $lists['limitstart']; 
		$search		= $lists['search']; 
		$search	= JString::strtolower( $search );		

		$where=$model->getWhereClause($lists);
		//echo $where;
		//order by
		$orderby = '  ORDER BY '. $filter_order .' '. $filter_order_Dir;
		$total = $model->getItems($where,1);
		//limit
	    if ($limit > $total) {
	        $limitstart = 0;
	    }	
	    if($limit==0)$limit=$total;
		$limited="  LIMIT ". $limitstart . ','. $limit;
				
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $lists['limitstart'], $lists['limit'] );
		
		$items=$model->getItems($where,0,$orderby,$limited);
		
		$this->assign('items',$items);
		$this->assign('lists',$lists);
		$this->assign('pageNav',$pageNav);
	}
	function edit($item=null){

		if(!$item){
			$item = $this->get('Item');
			$postback = JRequest::getVar('postback');
			if(!$postback){
	            $post    = JRequest::get('request', JREQUEST_ALLOWHTML);
	            $item->bind($post);
			}		
		}
		$this->assignRef('item',$item);		
	}
}	