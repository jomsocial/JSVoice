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
class javoiceControllercustomtmpl extends JAVoiceController {
/**
     * Constructor
     */
    function __construct(  )
    {
    	
        parent::__construct(  );     
          if(!JAVoiceHelpers::checkPermission(array('core.admin'))){
        	JError::raiseWarning(1001,'You have no permission this task');
        	$this->setRedirect('index.php?option=com_javoice&view=voice');
        	return FALSE;
        }               
        // Register Extra tasks
        $this->registerTask( 'apply',   'save' );        
    }
    
    /**
    * Display current customtmpl of the component to administrator
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
        $this->setRedirect("index.php?option=$option&view=customtmpl");
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
		$folder = JRequest::getVar('folder', '');
		
		$template=JAVoiceHelpers::checkFileTemplate($file,'html',$folder);
		
		if($template)
			$path=$template;
		else
			$path = JPATH_COMPONENT_SITE.DS.'views'.DS.$folder.DS.'tmpl'.DS.$file;
			
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
				$this->setRedirect( "index.php?option=$option&view=customtmpl&task=edit&file=$file&folder=$folder" , $msg );
				break;

			case 'save':
			default:
				$this->setRedirect( "index.php?option=$option&view=customtmpl", $msg );
				break;
		}
		return TRUE;
	}		
}