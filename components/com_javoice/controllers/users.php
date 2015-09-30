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
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

defined ( '_JEXEC' ) or die ( 'Restricted access' );

class JAVoiceControllerUsers extends JAVFController {
	
	function display($cachable = false, $urlparams = false) {
		parent::display ($cachable = false, $urlparams = false);
	}
	
	
	function setEmailNotificationReferences() {
		$db = JFactory::getDBO ();
		$user = JFactory::getUser ();
		
		if ($user->id) {	
			$user->setParam ( 'votedVoiceUpdateNotification', JRequest::getInt("votedVoiceUpdateNotification",0) );
			$user->setParam ( 'receive', JRequest::getInt("receive",0) );	
			$user->setParam ( 'often', JRequest::getInt('often',0));
			$user->save();			
		}
		
		$object = array();
		$k = 0;
		$object[$k] = new stdClass();
		$object[$k]->id = '#jav-email-preference .jav-msg-successful';
		$object[$k]->attr = 'html';
		$object[$k]->content = JText::_('SAVING_SUCCESSFULL');						
		
		$helper = new JAVoiceHelpers ( );		
		echo $helper->parse_JSON_new ( $object );
		exit ();
	}
}

?>