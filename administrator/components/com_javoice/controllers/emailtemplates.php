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

class javoiceControlleremailtemplates extends JAVoiceController
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
			case 'show_duplicate':
				JRequest::setVar( 'layout', 'duplicate' );	
				break;
			case 'show_import':
				JRequest::setVar( 'layout', 'import' );	
				break;			
		}
		
		parent::display($cachable = false, $urlparams = false);
    }
    
    /**
    * Cancel current operation
    * 
    */
    function cancel(){
		$option = JRequest::getCmd('option');
        $this->setRedirect("index.php?option=$option&view=emailtemplates");
    }   
    /**
    * Remove a jaemail row
    * 
    */   
    function remove(){
 		$option = JRequest::getCmd('option');
    	// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
        
        $model = $this->getModel('emailtemplates');
        $item = $model->getItem();
        if($item->system==1){
        	 JError::raiseWarning(1001,JText::_("THIS_EMAILS_DOES_NOT_ALLOW_DELETION"));
        	 $this->setRedirect("index.php?option=$option&view=emailtemplates");
        	 return FALSE;
        }
        if(($n = $model->remove()) < 0){
             JError::raiseWarning( 500, $row->getError() );
		}
		
		$msg = JText::_("DELETE_EMAIL_TEMPLATES_SUCCESSFULLY");
		$this->setRedirect("index.php?option=$option&view=emailtemplates",$msg);
	}
	
	
	/**
	* Save categories record
	*/
	function save(){
		$option = JRequest::getCmd('option');
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		$cache =  JFactory::getCache($option);
		$cache->clean();

		$model	= $this->getModel('emailtemplates');
		$post	= JRequest::get('post');
		
		// allow name only to contain html
		//print_r($post);exit;
		$paramsField = JRequest::getVar( 'params', null, 'post', 'array' );				
		if ($paramsField)
		{	
			$params = class_exists('JRegistry') ? new JRegistry('') : new JParameter('');		
			foreach ($paramsField as $k => $v) {
				$params->set($k, $v);					
			}					
			$post['params'] = $params->toString();
		}

		$post['content'] = JRequest::getVar('content', JREQUEST_ALLOWRAW);
		$post['subject'] = JRequest::getVar('subject', JREQUEST_ALLOWRAW);
		$model->setState( 'request', $post );
		
		if ($id = $model->store()) {
			if ((isset($cid[0]))&&($cid[0]!=0))
				$msg = JText::_('UPDATED_EMAIL_TEMPLATES_SUCCESSFULLY');
			else 
				$msg = JText::_('CREATED_EMAIL_TEMPLATES_SUCCESSFULLY');
		} else {
			$msg = JText::_('ERROR_OCCURRED_SAVE_THE_EMAIL_TEMPLATE_IS_NOT_SUCCESSFUL');
		}
		
		switch ( $this->_task ) {
			case 'apply':
				$this->setRedirect( "index.php?option=$option&view=emailtemplates&task=edit&cid[]=$id" , $msg );
				break;

			case 'save':
			default:
				$this->setRedirect( "index.php?option=$option&view=emailtemplates", $msg );
				break;
		}
	}	
	
	/**
	* change Is_Published status
	*/
	/**
	 * publish or unpublish list item 
	 * @return void
	 **/
	function unpublish(){
		$this->publish(0);
	}
	function publish($publish=1){
		$option = JRequest::getCmd('option');
		$model = $this->getModel('emailtemplates');
				
		if (!$model->dopublish($publish)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
			exit();
		}
		$cache = & JFactory::getCache($option);
		$cache->clean();	
		$this->setRedirect("index.php?option=$option&view=emailtemplates", JText::_("UPDATED_EMAIL_TEMPLATES_SUCCESSFULLY"));
	}
	
	function duplicate(){
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$model = $this->getModel('emailtemplates');
		if(!$model->do_duplicate()){
			JError::raiseWarning(1, JText::_('THE_PROCESS_OF_COPYING_ERRORS_OCCUR'));
			return $this->setRedirect( "index.php?option=$option&view=emailtemplates" );
		}
		$filter_lang = $mainframe->getUserStateFromRequest( $option.'.emailtemplates.filter_lang', 'filter_lang', 'en-GB',	'string' );		
		return $this->setRedirect( "index.php?option=$option&view=emailtemplates&filter_lang=$filter_lang", JText::_('COPY_EMAIL_TEMPLATE_SUCCESSFULLY') );
		
	}
	
	function import(){
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$cache = JFactory::getCache($option);
		$cache->clean();
		
		jimport('joomla.filesystem.file');
		$model = $this->getModel('emailtemplates');
		
		if(isset($_FILES['userfile']) && $_FILES['userfile']['name']!=''){
			$desk = JPATH_COMPONENT_ADMINISTRATOR.DS.'temp'.DS.substr($_FILES['userfile']['name'], 0, strlen($_FILES['userfile']['name'])-4).time().rand().substr($_FILES['userfile']['name'], -4, 4);

			if(JFile::upload($_FILES['userfile']['tmp_name'], $desk)){
				$filecontent = JFile::read($desk);
				if(!$model->import($filecontent)){
					return $this->setRedirect( "index.php?option=$option&view=emailtemplates" );
				}
				
				$filter_lang = $mainframe->getUserStateFromRequest( $option.'.emailtemplates.filter_lang', 'filter_lang', 'en-GB',	'string' );	
				return $this->setRedirect( "index.php?option=$option&view=emailtemplates&filter_lang=$filter_lang", JText::_('IMPORT_EMAIL_TEMPLATE_SUCCESSFULLY') );
			}
			unset($_FILES['userfile']);
			JError::raiseWarning(1, JText::_('ERROR_OCCURRED_UPLOAD_FAILED'));
			return $this->setRedirect( "index.php?option=$option&view=emailtemplates&task=show_import" );
		}
		
		JError::raiseWarning(1, JText::_('PLEASE_CHOOSE_FILE_TO_UPLOAD'));
		return $this->setRedirect( "index.php?option=$option&view=emailtemplates&task=show_import" );							
	}
	
	function export(){
		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger ( $cid, array (0 ) );
		if(!$cid) {
			JError::raiseWarning(1, JText::_('PLEASE_SELECT_EMAIL_TEMPLATE'));
			return $this->setRedirect( "index.php?option=com_javocie&view=emailtemplates");
		}
		
		$cid =  implode(',', $cid);
		$model = $this->getModel('emailtemplates');
		$items = $model->getItemsbyWhere($cid);
		
		if(!$items) {
			JError::raiseWarning(1, JText::_('PLEASE_SELECT_EMAIL_TEMPLATE'));
			return $this->setRedirect( "index.php?option=com_javocie&view=emailtemplates");
		}
		
		$content = '';
		foreach ($items as $item){
			$content .= JAVoiceHelpers::temp_export($item);
		}
		
		$filename = 'javoice_email_templates.ini';
		$ctype="text/plain"; 	
		
		if ($content) {
			header("Pragma: public"); // required
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false); // required for certain browsers
			header("Content-Type: $ctype; name=\"".basename($filename)."\";");
			header("Content-Disposition: attachment; filename=\"".basename($filename)."\";" );
			header("Content-Transfer-Encoding: utf-8");
			header("Content-Length: ".strlen($content));
			echo $content;exit;
		}
		else {
			JError::raiseWarning(1, JText::_('CONTENT_IS_EMPTY'));
			return $this->setRedirect( "index.php?option=com_javocie&view=emailtemplates");
		}
	}		
}
?>