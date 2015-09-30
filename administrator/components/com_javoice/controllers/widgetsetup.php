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

class javoiceControllerwidgetsetup extends JAVoiceController {
	
	function __construct($default = array()) {
		parent::__construct ( $default );
	}
	
	function display($cachable = false, $urlparams = false) {
		$user = JFactory::getUser ();
		if ($user->id == 0) {
			JError::raiseWarning ( 1001, JText::_("YOU_MUST_BE_SIGNED_IN" ) );
			$this->setRedirect ( JRoute::_ ( "index.php?option=com_user&view=login" ) );
			return;
		}
		$task =$this->getTask();
		switch ($task){
			case 'widgetlist':
				JRequest::setVar('layout','widgetlist');
				break;
			default:
				JRequest::setVar('layout','default');
				break;				
		}
		parent::display ($cachable = false, $urlparams = false);
	}
}
?>