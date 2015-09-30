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
defined('_JEXEC') or die('Restricted access');

class javoiceControllervoice extends JAVoiceController
{
	
 function __construct( $default = array() )
    {
        parent::__construct( $default );
        // Register Extra tasks
        JRequest::setVar('view','voice');
        $this->registerTask( 'add',        'edit' );
        $this->registerTask( 'apply',    'save' );
    }
    
 	function display($cachable = false, $urlparams = false){	    	
    	$user = JFactory::getUser();
        if ($user->id==0)
        {
        	JError::raiseWarning(1001,JText::_("YOU_MUST_BE_SIGNED_IN"));
        	$this->setRedirect(JRoute::_("index.php?option=com_user&view=login"));
        	return ;
        }	        
		parent::display($cachable = false, $urlparams = false);
    } 
    
	function reissue(){
		$email 		= trim(JRequest::getString('email', ''));
		$paymentid  = trim(JRequest::getString('payment_id', ''));
		if(!$email){
			JError::raiseWarning(1, JText::_('PLEASE_ENTER_YOUR_EMAIL_ADDRESS_OR_USERNAME'));
  			$this->setRedirect('index.php?option=com_javoice&view=voice&layout=verify');
  			return;
		}
		
  		if(!$paymentid){
  			JError::raiseWarning(1, JText::_('PLEASE_ENTER_YOUR_PAYMENTID'));
  			$this->setRedirect('index.php?option=com_javoice&view=voice&layout=verify');
  			return;
  		}
		  		
  		$msg = '';
  		$obj = new JAPermissions();
  		
  		if($obj->verify_new_license_key($email, $paymentid)){
  			$msg = JText::_('YOUR_LICENSE_HAS_BEEN_SUCCESSFULLY_VERIFIED');
  		}
  		$this->setRedirect('index.php?option=com_javoice&view=voice&layout=supportandlicense', $msg);  		  		
  	}
}
?>