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

class javoiceControllerlogs extends JAVoiceController
{
	
 function __construct( $default = array() )
    {
        parent::__construct( $default );
        // Register Extra tasks
        JRequest::setVar('view','logs');
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
    
 function edit(){
    	JRequest::setVar('edit', true);    	
        JRequest::setVar('layout','form');        
        parent::display();    	
    }
    
 function cancel(){
	    $this->setRedirect('index.php?option=com_javoice&view=logs');
	    return TRUE; 	  
 	}    
	
 function save(){
		$model= $this->getModel('logs');
		$item = $model->getItem();
        $post    = JRequest::get('request', JREQUEST_ALLOWHTML);
        if (!$item->bind( $post )) 
       	{
		    JError::raiseWarning(1001,JText::_("ERROR_OCCURRED_CAN_NOT_BIND_THE_DATA"));
		    $this->edit();
		    return FALSE;
	    }  
	    $errors=$item->check();
		if (count($errors)>0)
	    {	    	
			foreach ($errors as $error){
				JError::raiseWarning(1001,$error);
			}
	    	$this->edit();	    	
		    return FALSE;
	    }	  
		if (!$item->store())
	    {
		    JError::raiseWarning(1001,JText::_("ERROR_OCCURRED_DATA_NOT_SAVED"));
		    $this->edit();
		    return FALSE;
	    }
	    $link='index.php?option=com_javoice&view=logs';
	    $msg=JText::_('SAVE_DATA_SUCCESSFULLY');
	    $this->setRedirect($link,$msg);
	    return TRUE;
	}
	
 function saveorder()
	{
		$model = $this->getModel('logs');
		$msg='';
		if(!$model->saveOrder())
		{
			JError::raiseWarning(1001, JText::_('ERROR_OCCURRED_DATA_NOT_SAVED'));
		}
		else 
		{
			$msg=JText::_('NEW_ORDER_SAVED');
		}
		$this->setRedirect( 'index.php?option=com_javoice&view=logs',$msg);
	}
	
 function publish()
	{
		$model = $this->getModel('logs');
		if(!$model ->published(1))
		{
			JError::raiseWarning(1001, JText::_('ERROR_OCCURRED_DATA_NOT_SAVED'));
		} else {
			$msg = JText::_('SAVE_DATA_SUCCESSFULLY');
		}
		$this->setRedirect('index.php?option=com_javoice&view=logs',$msg);
	}
	
 function unpublish()
	{
		$model = $this->getModel('logs');
		if(!$model->published(0))
		{
			JError::raiseWarning(1001, JText::_('ERROR_OCCURRED_DATA_NOT_SAVED'));
		} 
		else 
		{
			$msg = JText::_('SAVE_DATA_SUCCESSFULLY');
		}
		$this->setRedirect('index.php?option=com_javoice&view=logs',$msg);
	}
	
 function remove(){
		$model = $this->getModel('logs');
		$cids=JRequest::getVar('cid',null,'post','array');	
		$error=array();	
		foreach ($cids as $cid) {
			if(!$model->delete($cid))
				$error=$cid;
		}
		if(count($error)>0){
			$err=implode(",",$error);
			JError::raiseWarning(1001,JText::_('ERROR_OCCURRED_UNABLE_TO_DELETE_THE_ITEMS_WITH_ID').': '." [$err]");	
		}else 
			$msg=JText::_("DELETE_DATA_SUCCESSFULLY");
		$this->setRedirect('index.php?option=com_javoice&view=logs',$msg);
	}			
}
?>