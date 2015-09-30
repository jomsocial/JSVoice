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

class javoiceControlleractionslog extends JAVoiceController {
	
	function __construct($default = array()) {
		parent::__construct ( $default );
		// Register Extra tasks
		JRequest::setVar ( 'view', 'actionslog' );
	}
	
	function display($cachable = false, $urlparams = false) {
		$user = JFactory::getUser ();
		if ($user->id == 0) {
			JError::raiseWarning ( 1001, JText::_("YOU_MUST_BE_SIGNED_IN" ) );
			$this->setRedirect ( JRoute::_ ( "index.php?option=com_user&view=login" ) );
			return;
		}
		parent::display ($cachable = false, $urlparams = false);
	}

	function remove() {
		$model = $this->getModel ( 'forums' );
	 	$errors=$model->remove ( );
		if (count ( $errors ) > 0) {
			foreach ($errors as $error)
			JError::raiseWarning ( 1001, $error);
		} else
				$msg = JText::_("SELETE_DATA_SUCCESSFULLY" );
		$this->setRedirect ( 'index.php?option=com_javoice&view=forums', $msg );
	}
}
?>