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
defined ( '_JEXEC' ) or die ( 'Restricted access' );

class javoiceControllerforums extends JAVoiceController {
	
	function __construct($default = array()) {
		parent::__construct ( $default );
		// Register Extra tasks
		JRequest::setVar ( 'view', 'forums' );
		$this->registerTask ( 'add', 'edit' );
		$this->registerTask ( 'apply', 'save' );
		$this->registerTask ( 'accesspublic', 'access' );
		$this->registerTask ( 'accessregistered', 'access' );
		$this->registerTask ( 'accessspecial', 'access' );
	}
	
	function display($cachable = false, $urlparams = false) {
		$user = JFactory::getUser ();
		if ($user->id == 0) {
			JError::raiseWarning ( 1001, JText::_("YOU_MUST_BE_SIGNED_IN" ) );
			$this->setRedirect ( JRoute::_ ( "index.php?option=com_user&view=login" ) );
			return;
		}
		if ($this->getTask () == 'add' || $this->getTask () == 'edit') {
			JRequest::setVar ( 'edit', true );
			JRequest::setVar ( 'layout', 'form' );
		}
		parent::display ($cachable = false, $urlparams = false);
	}
	
	function cancel() {
		$this->setRedirect ( 'index.php?option=com_javoice&view=forums' );
		return TRUE;
	}
	
	function save(&$errors) {
		$model = $this->getModel ( 'forums' );
		$task = $this->getTask ();
		$item = $model->getItem ();
		$post = JRequest::get ( 'request');
		$voice_types = isset($post ['voice_type'])?$post ['voice_type']:NULL;
		if (! $item->bind ( $post )) {
			$errors [] = JText::_("ERROR_OCCURRED_CAN_NOT_BIND_THE_DATA" );
			
			return FALSE;
		}
		$item->title = trim($item->title);
		$errors = $item->check ();
		if (count ( $errors ) > 0) {
			return FALSE;
		}		
		$where = " AND f.title = '$item->title' AND f.id !=$item->id ";
		$count = $model->getTotal($where);
		if($count>0){
			$errors[] = JText::_("ERROR_OCCURRED_DUPLICATE_FOR_FORUM_TITLE" );
			return FALSE;
		}
		if(count($voice_types)==0){
			$errors[] = JText::_("ERROR_OCCURRED_YOU_MUST_APPLIES_VOICE_TYPE_IN_THIS_FORUM" );
			return FALSE;		
		}
		if (is_array ( $item->gids_view ) && count ( $item->gids_view ) > 0)
			$item->gids_view = implode ( "\n", $item->gids_view );
		else
			$item->gids_view = '';
		
		if (is_array ( $item->gids_post ) && count ( $item->gids_post ) > 0)
			$item->gids_post = implode ( "\n", $item->gids_post );
		else
			$item->gids_post = '';
				
		if($item->alias=='')$item->alias= JFilterOutput::stringURLSafe($item->title);
		if (! $item->store ()) {
			//print_r($item);exit;
			$errors [] = JText::_("ERROR_OCCURRED_DATA_NOT_SAVED" );
			return FALSE;
		} else {
			$post ['id'] = $item->id;			
			$item->reorder(1);			
			if (! $model->saveDefaultStatus ( $post )) {
				$errors [] = JText::_("ERROR_OCCURRED_YOU_MUST_APPLIES_VOICE_TYPE_IN_THIS_FORUM" );
				return FALSE;
			}
		}
		
		return $item->id;
	}
	function saveIFrame() {
		
		$post = JRequest::get ( 'request', JREQUEST_ALLOWHTML );
		$number = $post ['number'];
		$errors = array ();
		$id = $this->save ( $errors );
		$helper = new JAVoiceHelpers ( );
		$objects = array ();
		
		if ($id) {
			
			$model = $this->getModel ( 'forums' );
			
			$item = $model->getItem ();
			if($post['id']=='0')
				$objects [] = $helper->parseProperty ( "reload", "#reload" . $item->id, 1 );	
			else 
				$objects [] = $helper->parseProperty ( "html", "#system-message", $helper->message ( 0, JText::_("SAVE_DATA_SUCCESSFULLY" ) ) );
		
			$objects [] = $helper->parseProperty ( "html", "#title" . $item->id, $item->title );
			
			$objects [] = $helper->parsePropertyPublish ( "html", "#publish" . $item->id, $item->published, $number );
			
			$modelvoicetypes = JAVBModel::getInstance ( 'voicetypes', 'javoiceModel' );
			$where = " AND ft.forums_id = $item->id";
			$joins = " INNER JOIN #__jav_forums_has_voice_types as ft ON t.id = ft.voice_types_id";
			$voicetypes = $modelvoicetypes->getDyamicItems ($where, 't.title', 0, 0, 't.title', $joins);
			$strVoice = '';
			if($voicetypes)$strVoice = implode(", ",$voicetypes);
			$item->strvoice=$strVoice;
		
			$objects [] = $helper->parseProperty ( "html", "#voice-types-" . $item->id, $item->strvoice );
			
			$objects [] = $helper->parseProperty ( "value", "#order" . $item->id, $item->ordering );
			
			//$objects [] = $helper->parsePropertyPublisha ( $item->id, $item->published );
			
		} else {
			$objects [] = $helper->parseProperty ( "html", "#system-message", $helper->message ( 1, $errors ) );
		}
		$helper = new JAVoiceHelpers ( );		
		echo $helper->parse_JSON_new ( $objects );
		exit ();
	}
	function saveorder() {
		$model = $this->getModel ( 'forums' );
		$msg = '';
		if (! $model->saveOrder ()) {
			JError::raiseWarning ( 1001, JText::_('ERROR_OCCURRED_DATA_NOT_SAVED' ) );
		} else {
			$msg = JText::_('NEW_ORDER_SAVED' );
		}
		$this->setRedirect ( 'index.php?option=com_javoice&view=forums', $msg );
	}

	function publish() {
		$model = $this->getModel ( 'forums' );
		if (! $model->published ( 1 )) {
			JError::raiseWarning ( 1001, JText::_('ERROR_OCCURRED_DATA_NOT_SAVED' ) );
		} else {
			$msg = JText::_('SAVE_DATA_SUCCESSFULLY' );
		}
		$this->setRedirect ( 'index.php?option=com_javoice&view=forums', $msg );
	}
	
	function unpublish() {
		$model = $this->getModel ( 'forums' );
		if (! $model->published ( 0 )) {
			JError::raiseWarning ( 1001, JText::_('ERROR_OCCURRED_DATA_NOT_SAVED' ) );
		} else {
			$msg = JText::_('SAVE_DATA_SUCCESSFULLY' );
		}
		$this->setRedirect ( 'index.php?option=com_javoice&view=forums', $msg );
	}
	
	function remove() {
		$model = $this->getModel ( 'forums' );
	 	$errors=$model->remove ( );
		if (count ( $errors ) > 0) {
			foreach ($errors as $error)
			JError::raiseWarning ( 1001, $error);
		} else
				$msg = JText::_("DELETE_DATA_SUCCESSFULLY" );
		$this->setRedirect ( 'index.php?option=com_javoice&view=forums', $msg );
	}
	function changeforumbyvoicetypeid(){
		$model = $this->getModel('forums');
		$voice_types_id=JRequest::getInt('voice_types_id',0);	
		$joins=" INNER JOIN #__jav_forums_has_voice_types as vt ON f.id  = vt.forums_id  ";
		$where=" AND vt.voice_types_id 	= $voice_types_id ";
		$forums = $model->getItems ( $where,0,0,' f.title ','',$joins );
		
		if (! is_array ( $forums ))
			$forums = array ();
		$displayForums = JHTML::_ ( 'select.genericlist', $forums, 'forums_id', "class=\"inputbox\" size=\"1\" ", 'id', 'title', 0 );
		echo $displayForums;
		exit;
	}	
}
?>