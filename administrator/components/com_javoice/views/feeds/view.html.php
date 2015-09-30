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
class JAVoiceViewfeeds extends JAVBView {
	
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
		$text = 'JA Feeds';
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
		$model = $this->getModel();
		$lists = $model->getvar();
		$where = $model->getWhere($lists);
		$total = $model->getTotal($where);
		$pageNav = $model->getPagination($lists,$total);
		$order = $lists['filter_order']." ".$lists['filter_order_Dir'];
		$items = $model->getItems($where,$order,$lists['limitstart'],$lists['limit']);
		$this->assignRef('items',$items);
		$this->assignRef('lists',$lists);
		$this->assignRef('pageNav',$pageNav);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $item
	 */
	function edit() {
		$user = JFactory::getUser();
		$model = $this->getModel();
  		$model_forums = JAVBModel::getInstance('forums','javoiceModel');
  		$model_voice_types = JAVBModel::getInstance('voicetypes','javoiceModel');		
		//$frontpage = $model->getFrontpageInfo($cid);
		$feed = JRequest::getVar('feed',NULL);
		if(!$feed)
			$feed		= $model->getItem();
		$lists['rssTypeList'] = $model->getRssType($feed->feed_type);
		$model->getLists($lists,$feed);
//		Filter feeds - begin -

		$gid ='';
		if(isset($user->gid)){			
			$gid = $user->gid;
		}
		$where = " AND f.gids_view LIKE '%$gid%' AND f.published =1";
		$forums = $model_forums->getItems($where);
		$lists['forumStr'] = "";
		$lists['filter_forums_id'] = "";
		if($forums){
			$str = array();
			foreach ($forums as $forum) {
				$str[] = $forum->title;
			}
			$lists['forumStr'] = implode(",",$str);
			$selected="";
			if($feed->filter_forums_id)
				$selected = explode(",",$feed->filter_forums_id);
			$forumsHTML = array_merge ( array (JHTML::_ ( 'select.option', '-1', JText::_('ALL' ), 'id', 'title' ) ),$forums  );		
			$lists['filter_forums_id'] = JHTML::_ ( 'select.genericlist', $forumsHTML, 'filter_forums_id[]', 'class="inputbox" size="10" multiple="multiple"', 'id', 'title', $selected );
		}
		$joins = "	INNER JOIN #__jav_forums_has_voice_types as fv ON fv.voice_types_id = t.id
				 	INNER JOIN #__jav_forums as f ON f.id = fv.forums_id";
		
		$where = " AND t.published=1 AND f.gids_view LIKE '%$gid%' AND f.published =1";
		
		$voice_types = $model_voice_types->getDyamicItems($where,'',0,0,'DISTINCT t.*',$joins,'object');
		$lists['filter_voice_types_id'] ="";
		$lists['voice_typesStr'] = "";
		if($voice_types){
			$str = array();
			foreach ($voice_types as $voice_type) {
				$str[] = $voice_type->title;
			}
			$lists['voice_typesStr'] = implode(",",$str);			
			$selected="";
			if($feed->filter_voicetypes_id)
				$selected = explode(",",$feed->filter_voicetypes_id);
			$voice_typesHTML = array_merge ( array (JHTML::_ ( 'select.option', '-1', JText::_('ALL' ), 'id', 'title' ) ),$voice_types  );		
			
			$lists['filter_voice_types_id'] = JHTML::_ ( 'select.genericlist', $voice_typesHTML, 'filter_voicetypes_id[]', 'class="inputbox" size="10" multiple="multiple"', 'id', 'title', $selected );
		}
		
		$this->assignRef('lists', $lists);			
		$this->assign('feed',$feed);			
	}
}	