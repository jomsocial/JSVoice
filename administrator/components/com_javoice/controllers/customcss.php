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

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
/**
* This controller is used for JAEmail feature of the component
*/
class javoiceControllercustomcss extends JAVoiceController {
/**
     * Constructor
     */
    function __construct( $location = array() )
    {
        parent::__construct( $location ); 
        if(!JAVoiceHelpers::checkPermission(array('core.admin'))){
        	JError::raiseWarning(1001,'You have no permission this task');
        	$this->setRedirect('index.php?option=com_javoice&view=voice');
        	return FALSE;
        }
        // Register Extra tasks
        $this->registerTask( 'apply',   'save' );        
    }
    
    /**
    * Display current customcss of the component to administrator
    * 
    */
    function display($cachable = false, $urlparams = false){
    	
    	switch($this->getTask())
		{			
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
        $this->setRedirect("index.php?option=$option&view=customcss");
	    return TRUE; 	  
 	}           
	
	/**
	* Save categories record
	*/
	function save(){
		$option = JRequest::getCmd('option');
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$content = JRequest::getVar('content', '', 'post', 'string', JREQUEST_ALLOWRAW );
		
		$file = JRequest::getVar('file', '');
		$path='';
		$template=JAVoiceHelpers::checkFileTemplate($file);
		if($template)
			$path=$template;
		else
			$path = JPATH_COMPONENT_SITE.DS.'asset'.DS.'css'.DS.$file;
		$msg='';
		if(JFile::exists($path)){
			$res = JFile::write($path, $content);
			if ($res) {
				$msg = JText::_('SAVE_DATA_SUCCESSFULLY').': '.$file;
			} else {
				JError::raiseWarning(1001,JText::_("ERROR_OCCURRED_DATA_NOT_SAVED")." ".$file);	
			}			
		}
		else 
			JError::raiseWarning(1001,JText::_("FILE_NOT_FOUND_TO_EDIT"));

		
		switch ( $this->_task ) {
			case 'apply':
				$this->setRedirect( "index.php?option=$option&view=customcss&task=edit&file=$file" , $msg );
				break;

			case 'save':
			default:
				$this->setRedirect( "index.php?option=$option&view=customcss", $msg );
				break;
		}
		return TRUE;
	}
				
}