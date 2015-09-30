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
defined ( '_JEXEC' ) or die ( 'Restricted access' );

jimport ( 'joomla.application.component.view' );

/**
 * Enter description here...
 *
 */
class JAVoiceViewForums extends JAVBView {
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $tpl
	 */
	function display($tpl = null) {
	//Load left menu
		if (! JRequest::getVar("ajax") && JRequest::getVar('tmpl') != 'component' && JRequest::getVar('viewmenu', 1) != 0) {
			$file = JPATH_COMPONENT.'/views/jaview/view.html.php';
			if (file_exists($file)) {
				include_once($file);
			}
		}
		switch ($this->getLayout ()) {
			case 'form' :
				{
					$this->edit ();
				}
				break;
			
			default :
				{
					$this->displayItems ();
				}
				break;
		
		}
		$this->addToolbar();
		parent::display ( $tpl );
		// Display menu footer
		if (!JRequest::getVar("ajax") && JRequest::getVar('tmpl') != 'component' && JRequest::getVar('viewmenu', 1) != 0) {
			$file = JPATH_COMPONENT_ADMINISTRATOR . "/views/jaview/tmpl/footer.php";
			if (file_exists($file)) {
				include_once($file);
			}
		}
	}
	
	function addToolbar(){
		$text = 'JA Forums';
		JToolBarHelper::title ( JText::_ ( $text ) );
		$task = JRequest::getWord ( 'task', '' );
		switch ($task) {
			case 'add' :
			case 'save' :
			case 'apply' :
			case 'edit' :
				{
					JToolBarHelper::apply ();
					JToolBarHelper::save ();
					JToolBarHelper::cancel ();
				}
				break;
			default :
				{
					JToolBarHelper::publishList();
					JToolBarHelper::unpublishList();
					//JToolBarHelper::editListX();
					JToolBarHelper::addNew ();
					JToolBarHelper::deleteList ( JText::_('WARNING_BEFOR_DELETED' ) );
				}
		}
	}
	/**
	 * Enter description here...
	 *
	 */
	function displayItems() {
		
		$db=JFactory::getDBO();
		$model = $this->getModel ();
		
		$lists = $model->_getVars ();
		$where_more='';
		$order = '';
		if (isset ( $lists ['filter_order'] ) && $lists ['filter_order_Dir'] != '') {
			$order = $lists ['filter_order'] . ' ' . @$lists ['filter_order_Dir'];
		}
		
		if (isset ( $lists ['search'] ) && $lists ['search']) {
			
			if (is_numeric ( $lists ['search'] )) {
				$where_more .= " AND f.id ='" . ( int ) $lists ['search'] . "' ";
			}
			$where_more .= " AND f.title LIKE " . $db->Quote ( '%'.$lists ['search'].'%' );
		}
		$total = $model->getTotal ( $where_more );
		
		if ($lists ['limit'] > $total) {
			$lists ['limitstart'] = 0;
		}
		if ($lists ['limit'] == 0){$lists ['limitstart'] = 0;}
		
		jimport ( 'joomla.html.pagination' );
		
		$pageNav = new JPagination ( $total, $lists ['limitstart'], $lists ['limit'] );
		
		$items = $model->getItems ( $where_more, $lists ['limit'], $lists ['limitstart'], trim ( $order ) );
		
		$modelvoicetypes = JAVBModel::getInstance ( 'voicetypes', 'javoiceModel' );
		if($items){
			foreach ($items as $item){
				$where = " AND ft.forums_id = $item->id";
				$joins = " INNER JOIN #__jav_forums_has_voice_types as ft ON t.id = ft.voice_types_id";
				$voicetypes = $modelvoicetypes->getDyamicItems ($where, 't.title', 0, 0, 't.title', $joins);
				$strVoice = '';
				if($voicetypes)$strVoice = implode(", ",$voicetypes);
				$item->strvoice=$strVoice;
			}
		}
		
		
		$modelvoicetypesstatus = JAVBModel::getInstance ( 'voicetypesstatus', 'javoiceModel' );
		$voicetypes = $modelvoicetypes->getItems ( ' AND t.published=1',' t.ordering DESC' );
		$voicetypesstatus = $modelvoicetypesstatus->getItems ( ' AND s.published=1',' s.ordering DESC' );
		$isstart =1;
		if($voicetypes && $voicetypesstatus)
			$isstart = 0;		
		$this->assign ( 'isstart', $isstart );	
		$this->assign ( 'items', $items );
		$this->assign ( 'lists', $lists );
		$this->assign ( 'pageNav', $pageNav );
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $item
	 */
	function edit($item = null) {
		
		$model = $this->getModel ();
		$number = JRequest::getVar('numbet',0);		
		
		$postBack = JAVoiceHelpers::isPostBack ();
		
		if (! $item) {
						
			$item = $this->get ( 'Item' );
			
			if ($postBack) {
				
				$post = JRequest::get ( 'request', JREQUEST_ALLOWHTML );
				
				$item->bind ( $post );
				
				$item->gids_view_selected = $post ['gids_view'];
				
				$item->gids_post_selected = $post ['gids_post'];
				
				$item->gids_vote_selected = $post ['gids_vote'];
			
			} else {
				
				$item->gids_post_selected=array();
				
				if($item->gids_post!='')
				
					$item->gids_post_selected = explode ( "\n", $item->gids_post );
					
				$item->gids_view_selected=array();
				
				if($item->gids_view!='')	
							
					$item->gids_view_selected = explode ( "\n", $item->gids_view );
					
				$item->gids_vote_selected=array();
				
			}
		}
		$modelvoicetypes = JAVBModel::getInstance ( 'voicetypes', 'javoiceModel' );
		$modelvoicetypesstatus = JAVBModel::getInstance ( 'voicetypesstatus', 'javoiceModel' );
		$voicetypes = $modelvoicetypes->getItems ( '',' t.ordering' );
		
		if ($postBack) {
			
			$model->parseVoiceTypePostBack ( $post, $voicetypes );
		
		} else {
			
			$model->parseVoiceTypes ( $item->id, $voicetypes );
		}
		$lists = $modelvoicetypesstatus->displaySelectList ( $voicetypes,JText::_("DO_NOT_USE_STATUS") );
		
		$ordering = $model->getOrdering ( $item );
		$gtree = JAVoiceHelpers::getGroupUser();

		//$gtree = $acl->get_group_children_tree ( null, 'USERS', false );

		$model->parseGroupUser ( $gtree ,1);
		
		$this->assignRef ( 'item', $item );
		
		$this->assignRef ( 'gtree', $gtree );
		
		$this->assignRef ( 'voicetypes', $voicetypes );
		
		$this->assignRef ( 'lists', $lists );
		
		$this->assignRef ( 'ordering', $ordering );
		
		$this->assignRef ( 'number', $number );
		
		
	}	
}	