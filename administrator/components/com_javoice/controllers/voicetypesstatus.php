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

class javoiceControllervoicetypesstatus extends JAVoiceController {
	
	function __construct($default = array()) {
		parent::__construct ( $default );
		// Register Extra tasks        
		$this->registerTask ( 'apply', 'save' );
		$this->registerTask ( 'unpublish', 'publish' );
		$this->registerTask ( 'show', 'showtab' );
		$this->registerTask ( 'unshow', 'showtab' );
		$this->registerTask ( 'nosystem', 'system' );
	}
	
	function display($cachable = false, $urlparams = false) {
		$user = JFactory::getUser ();
		$task = JRequest::getVar ( 'task' );
		if ($user->id == 0) {
			JError::raiseWarning ( 1001, JText::_("YOU_MUST_BE_SIGNED_IN" ) );
			$this->setRedirect ( JRoute::_ ( "index.php?option=com_user&view=login" ) );
			return;
		}
		if ($task == 'edit' || $task == 'add') {
			JRequest::setVar ( 'layout', 'form' );
		} elseif ($task == 'editgroup' || $task == 'addgroup') {
			JRequest::setVar ( 'layout', 'group' );
		}elseif ($task == 'color'){	
			JRequest::setVar ( 'layout', 'color' );
		}elseif ($task == 'checkgroup'){
			JRequest::setVar ( 'layout', 'checkgroup' );
		}
		parent::display ($cachable = false, $urlparams = false);
	}
	
	function cancel() {
		$this->setRedirect ( 'index.php?option=com_javoice&view=voicetypesstatus' );
		return TRUE;
	}

	function save(& $errors = '', & $reload = 0) {
		$task = $this->getTask ();
		
		$model = $this->getModel ( 'voicetypesstatus' );
		
		$item = $model->getItem ();
		
		$old_parent_id= $item->parent_id ;
		
		$post = JRequest::get ( 'request' );

		$isEdit = isset ( $post ['id'] ) ? $post ['id'] : 0;
		
		if (! $item->bind ( $post )) {
			$errors [] = JText::_("ERROR_OCCURRED_CAN_NOT_BIND_THE_DATA" );
			
			return FALSE;
		}
		$errors = $item->check ();
		if (count ( $errors ) > 0) {
			
			return FALSE;
		
		}
		if($item->alias=='')$item->alias= JFilterOutput::stringURLSafe($item->title);
		if (! $item->store ()) {
			
			$errors [] = JText::_("ERROR_OCCURRED_DATA_NOT_SAVED" );
			
			return FALSE;
		} else {
			
			$condition = 'parent_id = '.(int) $item->parent_id.' AND voice_types_id = '.(int)$item->voice_types_id;
			$item->reorder($condition);
			
			if (! $item->parent_id && $isEdit) {
				if (! $model->changeAllGroup ( $item->id, $item->voice_types_id )) {
					$errors [] = JText::_("ERROR_OCCURRED_DATA_NOT_SAVED" );
					
					return FALSE;
				}
			}
		}
		if ($task != 'saveIFrame' || !$isEdit) {
			
			$link = 'index.php?option=com_javoice&view=voicetypesstatus&voice_types_id=' . $item->voice_types_id;
			if ($this->getTask () == 'apply')
				$link .= "&task=edit&cid[]=" . $item->id;
			$msg = JText::_('SAVE_DATA_SUCCESSFULLY' );
			$this->setRedirect ( $link, $msg );
			
		}	
		if($item->parent_id != $old_parent_id)$reload=1;	
		return $item->id;
	}
	function saveIFrame() {
		
		$post = JRequest::get ( 'request' );
		$number = $post ['number'];
		$errors = array ();
		$reload=0;
		$id = $this->save ( $errors ,$reload);
		$helper = new JAVoiceHelpers ( );
		$objects = array ();
		
		if ($id) {			
			$model = $this->getModel ( 'voicetypesstatus' );
			
			$modelvoicetype = $this->getModel ( 'voicetypes' );
			
			$item = $model->getItem ( $id );
			
			$voicetype = $modelvoicetype->getItem ( $item->voice_types_id );
			
			if($post['id']=='0')
				$objects [] = $helper->parseProperty ( "reload", "#reload" . $item->id, 1 );
			else 
				$objects [] = $helper->parseProperty ( "html", "#system-message", $helper->message ( 0, JText::_("SAVE_DATA_SUCCESSFULLY" ) ) );				
			$objects [] = $helper->parseProperty ( "html", "#title" . $item->id, $item->title );
			
			$objects [] = $helper->parseProperty ( "val", "#voice_types_id", $item->voice_types_id );
			
			$objects [] = $helper->parseProperty ( "reload", "#reload" . $item->id, $reload );
			
			$objects [] = $helper->parseProperty ( "html", "#vtitle" . $item->id, $voicetype->title );
			
			$objects [] = $helper->parseProperty ( "style", "#class_css" . $item->id, "background:$item->class_css;width:70px; height:15px;;float:left" );
			
			$objects [] = $helper->parseProperty ( "html", "#name" . $item->id, $item->name );
			
			$objects [] = $helper->parsePropertyPublish ( "html", "#publish" . $item->id, $item->published, $number );		
			
			$objects [] = $helper->parsePropertyPublish ( "html", "#show_on_tab" . $item->id, $item->show_on_tab, $number,'hidden' ,'Show','Hidden');
			
			$objects [] = $helper->parseProperty ( "html", "#title" . $item->id, $item->title );	
			
			//$objects [] = $helper->parsePropertyPublisha ( $item->id, $item->published );
			

		} else {
			$objects [] = $helper->parseProperty ( "html", "#system-message", $helper->message ( 1, $errors ) );
		}
		$helper = new JAVoiceHelpers ( );		
		echo $helper->parse_JSON_new ( $objects );
		exit ();
	}
	function saveColor(){		
		$model = $this->getModel ( 'voicetypesstatus' );
		$color = JRequest::getVar('class_css',NULL);
		if($model->updateColor($color)){
			echo $color;
		}
		exit ();		
	}
	function saveorder() {
		$model = $this->getModel ( 'voicetypesstatus' );
		$msg = '';
		if (! $model->saveOrder ()) {
			JError::raiseWarning ( 1001, JText::_('ERROR_OCCURRED_DATA_NOT_SAVED' ) );
		} else {
			$msg = JText::_('NEW_ORDER_SAVED' );
		}
		$this->setRedirect ( 'index.php?option=com_javoice&view=voicetypesstatus', $msg );
	}
		
	function publish() {
		$model = $this->getModel ( 'voicetypesstatus' );
		$task = $this->getTask ();
		$published = 1;
		if ($task == 'unpublish')
			$published = 0;
		if (! $model->published ( $published )) {
			if ($task == 'unpublish')
				JError::raiseWarning ( 1001, JText::_('ERROR_OCCURRED_DATA_NOT_SAVED' ) );
			else
				JError::raiseWarning ( 1001, JText::_('ERROR_OCCURRED_DATA_NOT_SAVED' ) );
		} else {
			if ($task == 'unpublish')
				$msg = JText::_('SAVE_DATA_SUCCESSFULLY' );
			else
				$msg = JText::_('SAVE_DATA_SUCCESSFULLY' );
		}
		$this->setRedirect ( 'index.php?option=com_javoice&view=voicetypesstatus', $msg );
	}
	
	function showtab() {
		$model = $this->getModel ( 'voicetypesstatus' );
		$task = $this->getTask ();
		$show = 1;
		if ($task == 'unshow')
			$show = 0;
		if (! $model->showtab ( $show )) {
			if ($task == 'unshow')
				JError::raiseWarning ( 1001, JText::_('ERROR_OCCURRED_DATA_NOT_SAVED' ) );
			else
				JError::raiseWarning ( 1001, JText::_('ERROR_OCCURRED_DATA_NOT_SAVED' ) );
		} else {
			if ($task == 'unshow')
				$msg = JText::_('SAVE_DATA_SUCCESSFULLY' );
			else
				$msg = JText::_('SAVE_DATA_SUCCESSFULLY' );
		}
		$this->setRedirect ( 'index.php?option=com_javoice&view=voicetypesstatus', $msg );
	}
	function system() {
		$model = $this->getModel ( 'voicetypesstatus' );
		$task = $this->getTask ();
		$system = 1;
		if ($task == 'unsystem')
			$system = 0;
		if (! $model->system ( $system )) {
			
			JError::raiseWarning ( 1001, JText::_('ERROR_OCCURRED_DATA_NOT_SAVED' ) );
		} else {
			$msg = JText::_('SAVE_DATA_SUCCESSFULLY' );
		}
		$this->setRedirect ( 'index.php?option=com_javoice&view=voicetypesstatus', $msg );
	}
	function remove() {
		$model = $this->getModel ( 'voicetypesstatus' );

		$errors=$model->remove ();
		
		if (count ( $errors ) > 0) {
			foreach ($errors as $error){
				JError::raiseWarning ( 1001, $error );
			}
		} else
			$msg = JText::_("SAVE_DATA_SUCCESSFULLY" );
		$this->setRedirect ( 'index.php?option=com_javoice&view=voicetypesstatus', $msg );
	}
	function changestatusbyvoicetypeid() {
		$model = $this->getModel ( 'voicetypesstatus' );
		$voice_types_id = JRequest::getInt ( 'voice_types_id', 0 );
		$isparent = JRequest::getInt ( 'isparent', 0 );
		if (! $isparent)
			$displaystatus = $model->displaySelectOptgroup ( $voice_types_id, 0, "id='voice_type_status_id' name='voice_type_status_id'" );
		else
			$displaystatus = $model->displaySelect ( $voice_types_id, 0, "id='parent_id' name='parent_id'" );
		echo $displaystatus;		
		exit ();
	}
}
?>