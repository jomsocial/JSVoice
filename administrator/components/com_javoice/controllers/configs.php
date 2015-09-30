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
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' ); 

/**
 * This controller is used for JAConfiguration feature of the component
 */
class JAvoiceControllerconfigs extends JAVoiceController {
	
	/**
	 * Constructor
	 */
	function __construct($jbconfig = array()) {
		parent::__construct ( $jbconfig );
		//checkPermission_Administrator('configuration');    
	}
	
	/**
	 * Display current configs of the component to administrator
	 * 
	 */
	function display($cachable = false, $urlparams = false) {
		parent::display ($cachable = false, $urlparams = false);
	}
	
	/**
	 * Save configuration record
	 */
	function save() {		
		$option = JRequest::getCmd('option');
		$model = $this->getModel ( 'configs' );
		$item = $model->getItems ();
		
		$data = $item->data;
		$params = class_exists('JRegistry')? new JRegistry($data) : new JParameter($data);
		
		//JRequest::checkToken () or jexit ( 'Invalid Token' );
		$task = $this->getTask ();
		$cache = JFactory::getCache ( $option );
		$cache->clean ();
		
		$group = JRequest::getVar ( 'group', 'systems' );
		if ($group == '')
			$group = 'systems';
		
		$paramsField = JRequest::getVar ( $group, null, 'request', 'array' );
		// Build parameter INI string
		$access = JRequest::getVar ( 'access', NULL );
		if (isset ( $access ))
			$paramsField ['access'] = $access;
			
		if ($paramsField) {
			if($group=='plugin'){
				if(!isset($paramsField['enable_avatar'])){
					$paramsField['enable_avatar'] = 0;
				}
				if(!isset($paramsField['enable_activity_stream'])){
					$paramsField['enable_activity_stream'] = 0;
				}
				if(!isset($paramsField['enable_bbcode'])){
					$paramsField['enable_bbcode'] = 0;
				}				
				
				if(!isset($paramsField['enable_youtube'])){
					$paramsField['enable_youtube'] = 0;
				}
				if(!isset($paramsField['enable_tweetmeme'])){
					$paramsField['enable_tweetmeme'] = 0;
				}
				if(!isset($paramsField['enable_smileys'])){
					$paramsField['enable_smileys'] = 0;
				}
				if(!isset($paramsField['enable_after_the_deadline'])){
					$paramsField['enable_after_the_deadline'] = 0;
				}
				if(!isset($paramsField['enable_addtoany'])){
					$paramsField['enable_addtoany'] = 0;
				}
				if(!isset($paramsField['enable_addthis'])){
					$paramsField['enable_addthis'] = 0;
				}
				if(!isset($paramsField['is_attach_image'])){
					$paramsField['is_attach_image'] = 0;
				}
				if(!isset($paramsField['is_enable_captcha'])){
					$paramsField['is_enable_captcha'] = 0;
				}
				if(!isset($paramsField['is_enable_captcha_user'])){
					$paramsField['is_enable_captcha_user'] = 0;
				}
				if(!isset($paramsField['enable_pathway'])){
					$paramsField['enable_pathway'] = 0;
				}
				if(!isset($paramsField['enable_your_items'])){
					$paramsField['enable_your_items'] = 0;
				}				
				if(!isset($paramsField['enable_login_form'])){
					$paramsField['enable_login_form'] = 0;
				}
				if(!isset($paramsField['enable_button_create_new'])){
					$paramsField['enable_button_create_new'] = 0;
				}
				$paramsField ['attach_file_type'] = implode ( ",", $paramsField ['attach_file_type'] );				
			}
			foreach ( $paramsField as $k => $v ) {
				$params->set ( $k, $v );
			
			}
			if ($group == 'systems') {
				$user_groups = JRequest::getVar ( 'user_group', array (), 'post', 'array' );
				
				$user_group = 0;
				if ($user_groups ) {
					$user_group = implode(",",$user_groups);
					foreach ($user_groups as $k=>$v){
						if($v=="0"){
							$user_group=0;
							break;
						}
					}				
				} else
					$user_group = 0;
					
				$params->set ( 'user_group', $user_group );
			}
			
			
			$post ['data'] = $params->toString ();
			
			$model->setState ( 'request', $post );
			if ($id = $model->store ()) {
				
				$msg = JText::_('UPDATED_CONFIGURATION_SUCCESSFULLY' );
			
			} else {
				
				Jrequest::setVar ( 'task', $group );
				Jrequest::setVar ( 'layout', $group );
				Jrequest::setVar ( 'group', $group );
				parent::display ();
				return FALSE;
			}
		}
		if ($task != 'saveIFrame') {
			$this->setRedirect ( "index.php?option=$option&view=configs&group=$group", $msg );
		} else {
			return TRUE;
		}
		return true;
	}
	function saveIFrame() {
		$system = JRequest::getVar ( 'system', '' );
		$helper = new JAVoiceHelpers ( );
		$objects = array ();
		if ($this->save ()) {
			$model = & $this->getModel ( 'configs' );
			$item = $model->getItems ();
			
			$data = $item->data;
			$params = class_exists('JRegistry') ? new JRegistry($data) : new JParameter($data);
			$message [] = JText::_("SAVE_DATA_SUCCESSFULLY" );
			$objects [] = $helper->parseProperty ( "html", "#system-message", $helper->message ( 0, $message ) );
		} else {
			$message [] = JText::_("ERROR_OCCURRED_DATA_NOT_SAVED" );
			$objects [] = $helper->parseProperty ( "html", "#system-message", $helper->message ( 1, $message ) );
		}	
		$helper = new JAVoiceHelpers ( );		
		echo $helper->parse_JSON_new ( $objects );
		exit ();
	}
	function saveAddUser() {
		$result = TRUE;
		$helper = new JAVoiceHelpers ( );
		$objects = array ();
		
		$model = $this->getModel ( 'configs' );
		$data = $model->getItems ();
		$item = JTable::getInstance ( 'configs', 'Table' );
		$item->bind ( $data );
		
		$data = $item->data;
		$params = class_exists('JRegistry') ? new JRegistry($data) : new JParameter($data);
		
		$group = JRequest::getVar ( 'group', NULL );
		if (! $group)
			$result = FALSE;
		
		if ($result) {
			$cid = JRequest::getVar ( 'cid', array (0 ), '', 'array' );
			$user_id = '';
			$cids = array ();
			
			if ($cid)
				$cids [] = implode ( ',', $cid );
			
			if ($params->get ( 'permissions', '' ) != '')
				$cids [] = $params->get ( 'permissions', '' );
			
			if ($cids)
				$user_id = implode ( ',', $cids );
			
			$params->set ( 'permissions', $user_id );
			$item->group = $group;
			$item->data = $params->toString ();
			
			if ($item->store ()) {
				$objects [] = $helper->parseProperty ( "reload", "#reload" . $item->id, 1 );
				$message [] = JText::_("SAVE_DATA_SUCCESSFULLY" );
				$objects [] = $helper->parseProperty ( "html", "#system-message", $helper->message ( 0, $message ) );
			} else {
				$message [] = JText::_("ERROR_OCCURRED_DATA_NOT_SAVED" );
				$objects [] = $helper->parseProperty ( "html", "#system-message", $helper->message ( 1, $message ) );
			}
		} else {
			$message [] = JText::_("ERROR_OCCURRED_DATA_NOT_SAVED" );
			$objects [] = $helper->parseProperty ( "html", "#system-message", $helper->message ( 1, $message ) );
		}
		
		$helper = new JAVoiceHelpers ( );		
		echo $helper->parse_JSON_new ( $objects );
		exit ();
	}
	function remove() {
		$group = JRequest::getVar ( 'group', NULL );
		$cid = JRequest::getVar ( 'cid', array (0 ), '', 'array' );
		if (! isset ( $group ) || count ( $cid ) == 0) {
			$message = JText::_("ERROR_OCCURRED_DATA_NOT_SAVED" );
			$this->setRedirect ( "index.php?option=com_javoice&view=configs&group=permissions", $message );
		} else {
			$model = $this->getModel ( 'configs' );
			$data = $model->getItems ();
			$config = JTable::getInstance ( 'configs', 'Table' );
			$config->bind ( $data );
			
			$params = class_exists('JRegistry') ? new JRegistry($config->data) : new JParameter($config->data);
			$user = $params->get ( 'permissions', NULL );
			$user_new = array ();
			
			$error = $this->checkDeletePermission ( $cid );
			
			if ($user) {
				$user = explode ( ",", $user );
				$user_new = array_diff ( $user, $cid );
			} else {
				$model_permissions = JAVBModel::getInstance ( 'permissions', 'javoiceModel' );
				/*				if ($type== 'admin') {
						$where_more .= " AND u.usertype in ('Manager','Administrator','Super Administrator')";
				}	*/
				$items = $model_permissions->getItems ( '', 1000000, 0, '' );
				if (count ( $items ) > 0) {
					foreach ( $items as $item ) {
						if (! in_array ( $item->id, $cid )) {
							$user_new [] = $item->id;
						}
					}
				}
			}
			if ($user_new)
				$user_new = implode ( ',', $user_new );
			else
				$user_new = '';
			$user_new = 'permissions=' . $user_new;
			$config->data = $params->set ( 'permissions', $user_new );
			$config->group = $group;
			if ($config->store ()) {
				if ($error) {
					foreach ( $error as $err ) {
						JError::raiseWarning ( 1001, $err );
						$this->setRedirect ( "index.php?option=com_javoice&view=configs&group=permissions" );
					}
				} else {
					$message = JText::_("DELETE_DATA_SUCCESSFULLY" );
					$this->setRedirect ( "index.php?option=com_javoice&view=configs&group=permissions", $message );
				}
			} else {
				$message = JText::_("ERROR_OCCURRED_DATA_NOT_SAVED" );
				$this->setRedirect ( "index.php?option=com_javoice&view=configs&group=permissions", $message );
			}
		}
		return TRUE;
	}
	function checkDeletePermission(&$cid) {
		$cid_not = array ();
		$error = array ();
		$currentUser = JFactory::getUser ();
		// Access checks.
		$allow = $currentUser->authorise('core.delete', 'com_users');
		if($allow){
			foreach ( $cid as $id ) {
				$user = JFactory::getUser ( $id );
				if($currentUser->id == $user->id){
					$error [] = JText::_("YOU_CAN_NOT_REMOVE_YOURSELF");
					$cid_not [] = $id;
				}else{
					$result	= new JObject;
					$action = array('core.admin');					
					$result->set($action, $user->authorise($action, 'com_users'));					
					if($result->get("core.admin") == 1){
						$error [] = JText::_("YOU_CANT_REMOVE_ADMIN");
						$cid_not [] = $id;
					}else{
						
					}					
				}													
			}	
		}else{
			$error [] = JText::_("YOU_NOT_HAVE_PERMISSION_REMOVE_MODERATOR");	
		}		
		if ($cid_not)
			$cid = array_diff ( $cid, $cid_not );
		return $error;
	}
	function makedefault() {
		$option = JRequest::getCmd('option');
		$cid = JRequest::getVar ( 'cid', array (0 ), '', 'array' );
		$group = JRequest::getVar ( 'group', NULL );
		if ($cid [0]) {
			$model = $this->getModel ( 'configs' );
			$item = $model->getItems ();
			$data = $item->data;
			$params =class_exists('JRegistry') ? new JRegistry($data) : new JParameter($data);
			$old_system = $params->get ( 'run_system' );
			$params->set ( 'run_system', $cid [0] );
			$post ['data'] = $params->toString ();
			$post ['id'] = $item->id;
			$model->setState ( 'request', $post );
			if ($model->store ()) {
				
				$msg = JText::_('INFORMATION_CHANGES_HAVE_BEEN_SAVED' );
			
			} else {
				$msg = JText::_('ERROR_OCCURRED_DATA_NOT_SAVED' );
			}
		} else
			$msg = JText::_('ERROR_YOU_MUST_CHOOSE_A_SYSTEM_DEFAULT' );
		
		$this->setRedirect ( "index.php?option=$option&view=configs&group=$group", $msg );
	}
}
?>