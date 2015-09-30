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

jimport('joomla.application.component.view');

class javoiceViewTags extends JAVBView
{

	function display($tpl = null) {		
		switch ($this->getLayout ()) {
			case 'form' :
				$this->edit ();				
				break;
			case 'showlist':
				$this->getList ();				
				break;
			default :
				{
					$this->displayItems ();					
					break;
				}	
		}				
		parent::display($tpl);
	}
	
	function getList(){		
		$model = $this->getModel();		
		$list = $model->getTagsList();
		if($list){
			echo '<ul>';
			foreach ($list as $item){
				echo '<li onClick="fill(\''.addslashes($item->name).'\');">'.$item->name.'</li>';	
			}			
			echo '</ul>';
		}		
		exit();
	}
	
	function displayItems(){
		$mainframe = JFactory::getApplication();
		$user = JFactory::getUser();
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option.$view.'.limitstart', 'limitstart', 0, 'int');
		$filter_order = $mainframe->getUserStateFromRequest($option.$view.'filter_order', 'filter_order', 'id', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest($option.$view.'filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');
		$filter_state = $mainframe->getUserStateFromRequest($option.$view.'filter_state', 'filter_state', -1, 'int');
		$search = $mainframe->getUserStateFromRequest($option.$view.'search', 'search', '', 'string');
		$search = JString::strtolower($search);
		$model = $this->getModel();
	
		$tags = $model->getData();
		
		$this->assignRef('rows', $tags);
		$total = $model->getTotal();

		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $limit);
		$this->assignRef('page', $pageNav);
	
		$lists = array ();
		$lists['search'] = $search;
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;
	
		$filter_state_options[] = JHTML::_('select.option', -1, JText::_('_SELECT_STATE_'));
		$filter_state_options[] = JHTML::_('select.option', 1, JText::_('PUBLISHED'));
		$filter_state_options[] = JHTML::_('select.option', 0, JText::_('UNPUBLISHED'));
		$lists['state'] = JHTML::_('select.genericlist', $filter_state_options, 'filter_state', 'onchange="this.form.submit();"', 'value', 'text', $filter_state);
	
		$this->assignRef('lists', $lists);
	
		JToolBarHelper::title(JText::_('TAGS'));
		
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::deleteList(JText::_('ARE_YOU_SURE_YOU_WANT_TO_DELETE_SELECTED_TAGS'), 'remove', JText::_('DELETE'));		
		JToolBarHelper::addNew();		
	}
	
	function edit(){		
		$model = $this->getModel();		
		$tag = $model->getitem();		
		JFilterOutput::objectHTMLSafe( $tag );
		if(!$tag->id)
			$tag->published=1;
		$this->assignRef('row', $tag);
		
		$lists = array ();
		$lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $tag->published);
		$this->assignRef('lists', $lists);
		(JRequest::getInt('cid'))? $title = JText::_('EDIT_TAG') : $title = JText::_('ADD_TAG');
		JToolBarHelper::title($title);
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
	}
}
