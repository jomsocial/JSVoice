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

class javoiceControllerfeeds extends JAVoiceController
{
	
 function __construct( $default = array() )
    {
        parent::__construct( $default );
        // Register Extra tasks    
        if(!JAVoiceHelpers::checkPermission(array('core.admin'))){
        	JError::raiseWarning(1001,'You have no permission this task');
        	$this->setRedirect('index.php?option=com_javoice&view=voice');
        	return FALSE;
        }         
        $this->registerTask( 'add',        'edit' );
        $this->registerTask( 'apply',    'save' );
        $this->registerTask( 'unpublish',    'publish' );
    }
    
   
    /**
    * Display current jaemail of the component to administrator
    * 
    */
    function display($cachable = false, $urlparams = false){
    	
    	switch($this->getTask())
		{
			case 'add'     :
			{
				JRequest::setVar( 'hidemainmenu', 1 );
				JRequest::setVar( 'edit', false );
				JRequest::setVar( 'layout', 'form' );				
			} break;
			case 'edit':
			{
				JRequest::setVar( 'hidemainmenu', 1 );
				JRequest::setVar( 'edit', true );
				JRequest::setVar( 'layout', 'form' );				
			} break;	
		}
		
		parent::display($cachable = false, $urlparams = false);
    }
    
    /**
    * Cancel current operation
    * 
    */
    function cancel(){
		$option = JRequest::getCmd('option');
        $this->setRedirect("index.php?option=$option&view=feeds");
    }   
    /**
    * Remove a jaemail row
    * 
    */   
    function remove(){   
		 $option = JRequest::getCmd('option');
    	// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
        
        $model = $this->getModel('feeds');

        if(!$model->remove()){
			
			$msg = JText::_("FAILURE_TO_DELETE_FEEDS");
			$this->setRedirect("index.php?option=com_javoice&view=feeds",$msg);
			return FALSE;
		}
		
		$msg = JText::_("SUCCESSFULLY_DELETE_FEEDS");
		$this->setRedirect("index.php?option=com_javoice&view=feeds",$msg);
		return TRUE;
	}
	
	
	/**
	* Save categories record
	*/
	function save()
	{
		$model = $this->getModel('feeds');
		$id=$model->storeFeed();
		if ($id) {			
			$msg = JText::_('SUCCESSFULLY_SAVE_TO_FEED');
			$link = "index.php?option=com_javoice&view=feeds";
			if($this->getTask()=='apply')$link .="&task=edit&cid[]=".$id;
			$this->setRedirect($link,$msg);
		} else {
			JRequest::setVar('layout','form');			
			parent::display();
		}
	}
	
	/**
	* change Is_Published status
	*/
	/**
	 * publish or unpublish list item 
	 * @return void
	 **/
	function publish(){
		$option = JRequest::getCmd('option');
		$publish=$this->getTask()=='publish'?1:0;
		$model = $this->getModel('feeds');
				
		if (!$model->dopublish($publish)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
			exit();
		}
		$cache = JFactory::getCache($option);
		$cache->clean();	
		$this->setRedirect("index.php?option=com_javoice&view=feeds", JText::_("UPDATED_FEEDS_SUCCESSFULLY"));
	}
}
?>