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

class javoiceControllerpermissions extends JAVoiceController {
	
	function __construct($default = array()) {
		parent::__construct ( $default );
		// Register Extra tasks
		JRequest::setVar ( 'view', 'permissions' );
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
		$data = $_POST;
		$userid=$_POST['id'];
		if(!$userid){
			$errors[] = JText::_('NOT_FIND_THIS_USER');
			return FALSE;
		}
		$user=JFactory::getUser($userid);
		if(!$user){
			$errors[] = JText::_('NOT_FIND_THIS_USER');
			return FALSE;
		}
		$permissions = $data['permissions'];

		$permission = 0;
		if($permissions){
			foreach ($permissions as $per){
				$permission = (intval($permission) | intval($per));
			}
			$user->setParam ( 'permissions', $permission );
			if(!$user->save()){
				$errors[] = JText::_('ERROR_OCCURRED_DATA_NOT_SAVED');
				return FALSE; 
			}
		}
		return $userid;
	}
	function saveIFrame() {
		
		$errors = array ();
		$id = $this->save ( $errors );
		$helper = new JAVoiceHelpers ( );
		$objects = array ();
		
		if ($id) {
			$objects [] = $helper->parseProperty ( "reload", "#reload" . $item->id, 1 );
			$message [] = JText::_("SAVE_DATA_SUCCESSFULLY" );
			$objects [] = $helper->parseProperty ( "html", "#system-message", $helper->message ( 0, $message ) );
		
		} else {
			$objects [] = $helper->parseProperty ( "html", "#system-message", $helper->message ( 1, $errors ) );
		}
		
		$helper = new JAVoiceHelpers ( );		
		echo $helper->parse_JSON_new ( $objects );
		exit ();
	}

}
?>