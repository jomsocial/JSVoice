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
 * @package		Joomla
 * @subpackage	Contacts
 */
class javoiceViewvoicetypesstatus extends JAVBView {
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
			case 'group' :
				$this->edit ();
				break;
			case 'color':{
				$cid = JRequest::getVar ( 'cid', array (0 ), '', 'array' );
				JArrayHelper::toInteger ( $cid, array (0 ) );
				$id=0;
				if($cid)$id=$cid[0];
				$this->assign('id',$id);
				break;
			}
			case 'checkgroup':{
				$status_ids = JRequest::getVar ( 'status_id', array (), '', 'array' );
				$status    	= $this->getStatusItems($status_ids);
				$this->assign('status',$status);
				break;
			}	
			default :
				{
					$this->displayItems ();
					break;
				}
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
		$text = 'JA Status';
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
					JToolBarHelper::addNew ( 'add', 'Add Status' );
					JToolBarHelper::addNew ( 'addgroup', 'Add New Group' );
					JToolBarHelper::deleteList ();
				}
		}
	}
	
	function displayItems() {
		
		$model = $this->getModel ( 'voicetypesstatus' );
		
		$lists = $model->_getVars ();
		
		$modelvoicetypes = JAVBModel::getInstance ( 'voicetypes', 'javoiceModel' );
		
		$voicetypes = $modelvoicetypes->getItems ( '', ' t.ordering ' );
		
		$default = 0;
		
		if ($voicetypes)
			$default = $voicetypes [0]->id;
		
		if (! $lists ['voice_types_id'])
			$lists ['voice_types_id'] = $default;
		
		if (! is_array ( $voicetypes ))
			$voicetypes = array ();
		$displayVoicetypes = JHTML::_ ( 'select.genericlist', $voicetypes, 'voice_types_id', 'class="inputbox" size="1" onchange="form.submit()"', 'id', 'title', $lists ['voice_types_id'] );

		$where_more = $model->getWhereClause ( $lists );
		
			/**
		 * 
		 */	
		$lists['parent_search'] = 0;
		if($lists['search']){
			$where='';
			$where .= " AND s.parent_id=0 AND s.voice_types_id =".(int)$lists['voice_types_id']." AND s.title ='{$lists['search']}'";
			
			$joins = "INNER JOIN #__jav_voice_types AS t ON s.voice_types_id=t.id";
			
			$parents_search = $model->getItems ( $where, '' , 0, 1,'',$joins );
			if($parents_search)$lists['parent_search'] = $parents_search[0]->id;
			
		}		
		//order by
		$where='';
		$orderby = $lists ['filter_order'] . ' ' . $lists ['filter_order_Dir'];
		
		$where .= " AND s.parent_id=0 AND s.voice_types_id =".(int)$lists['voice_types_id'];
		
		$joins = "INNER JOIN #__jav_voice_types AS t ON s.voice_types_id=t.id";
		
		$parents = $model->getItems ( $where, $orderby , 0, 0,'',$joins );
		
		$all_aprent = 1;
		if($lists['search'])$all_aprent=0;
		$items = $model->parseItems ( $parents, $orderby,'',2,$where_more ,$all_aprent,$lists['parent_search']);
			
		$total = count($items);

		//limit
		
		if ($lists ['limit'] > $total) {
			$lists ['limitstart'] = 0;
		}
		if ($lists ['limit'] == 0)
			$lists ['limit'] = $total;
		
		jimport ( 'joomla.html.pagination' );
		$pageNav = new JPagination ( $total, $lists ['limitstart'], $lists ['limit'] );
				
		$isstart =1;
		if($voicetypes)
			$isstart = 0;	
		$this->assign ( 'isstart', $isstart );			
		$this->assign ( 'displayVoicetypes', $displayVoicetypes );
		$this->assign ( 'items', $items );
		$this->assign ( 'lists', $lists );
		$this->assign ( 'pageNav', $pageNav );
	
	}
	
	function edit($item = null) {
		$isgroup = 0;
		$isedit = 0;
		if ($this->getLayout () == 'group')
			$isgroup = 1;
		
		$model = $this->getModel ( 'voicetypesstatus' );
		
		if (! $item) {
			$item = $this->get ( 'Item' );
			if ($item->id > 0)
				$isedit = 1;
			if (JAVoiceHelpers::isPostBack ()) {
				$post = JRequest::get ( 'request', JREQUEST_ALLOWHTML );
				$item->bind ( $post );
			}
		}
		
		$items_voice_type = 0;
		if ($isedit) {
			$modelitems = JAVBModel::getInstance ( 'items', 'javoiceModel' );
			$items_voice_type = $modelitems->getTotal ( " AND  i.voice_type_status_id = $item->id " );
		}
		
		if (! $isedit) {
			$voice_types_id = JRequest::getInt ( 'voice_types_id', 0 );
			if ($voice_types_id)
				$item->voice_types_id = $voice_types_id;
		}
		JFilterOutput::objectHTMLSafe ( $item, ENT_QUOTES, '' );
		
		$tree = $model->displaySelect ( $item->voice_types_id, $item->parent_id, " id='parent_id' name='parent_id' class=\"inputbox\" " );
		
		$modelvoicetypes = JAVBModel::getInstance ( 'voicetypes', 'javoiceModel' );
		
		$voicetypes = $modelvoicetypes->getItems ( '' );
		if (! is_array ( $voicetypes ))
			$voicetypes = array ();
		
		$urlrequeststatus = "index.php?tmpl=component&option=com_javoice&view=voicetypesstatus&task=changestatusbyvoicetypeid&isparent=1";
		
		$onchange = "onchange=\"changeStatus('$urlrequeststatus',this.value)\";";
		if ($isgroup)
			$onchange = '';
		$disable = '';
		if ( $isedit) {
			
			if ($items_voice_type > 0 || $isgroup)
				$disable = ' disabled="disabled" ';
		
		}
		$displayVoicetypes = JHTML::_ ( 'select.genericlist', $voicetypes, 'voice_types_id', "class=\"inputbox\" style=\"height:22px\" $disable $onchange ", 'id', 'title', $item->voice_types_id );
		
		$this->assign ( 'displayVoicetypes', $displayVoicetypes );
		
		$number = JRequest::getVar ( 'number', 0 );
		
		$this->assignRef ( 'isgroup', $isgroup );
		$this->assignRef ( 'item', $item );
		$this->assignRef ( 'tree', $tree );
		
		$this->assignRef ( 'number', $number );
	}
	function getStatusItems($status_ids){
		foreach ($status_ids AS $status_id){
			$model = $this->getModel ( 'voicetypesstatus' );	
			$items = $model->getItem ($status_id);
			if($items->parent_id == 0){
				$where = ' AND s.`parent_id`='.$items->id;
				$total = $model->getTotal($where);
				if($total > 0){
					return 1;
					break;
				}
			}
		}
		return 0;
	}
}	
