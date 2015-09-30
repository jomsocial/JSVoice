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
defined('_JEXEC') or die('Restricted access');

/**
 * @package		Joomla
 * @subpackage	Config
 */

class javoiceControllermanagelang extends JAVoiceController{
	/**
	 * Constructor
	 */
	function __construct( $default = array())
	{
		parent::__construct( $default );
	    if(!JAVoiceHelpers::checkPermission(array('core.admin'))){
        	JError::raiseWarning(1001,'You have no permission this task');
        	$this->setRedirect('index.php?option=com_javoice&view=voice');
        	return FALSE;
        }			
		$this->registerTask( 'apply', 'save');	
	}

	/**
	 * Display the list of language
	 */
	function display($cachable = false, $urlparams = false)
	{	
		parent::display($cachable = false, $urlparams = false);		
	}
	
	/**
	 * cancel  save file
	 * @return redirect to language manager
	 **/
	function cancel(){
		$option = JRequest::getCmd('option');
		$client = JRequest::getVar('client', 0);
		$this->setRedirect("index.php?option=$option&view=managelang&client=$client");
	}
	
	/**
	 * save  language file
	 * @return void
	 **/
	function save(){
		$option = JRequest::getCmd('option');
		jimport('joomla.filesystem.file');
		$post	= JRequest::get('post');
		$langcontent = JRequest::getVar( 'datalang','','','', JREQUEST_ALLOWHTML);
		$file = $post['path_lang'].DS.$post['filename'].DS.$post['filename'].'.'.$option.'.ini';		
		JFile::write($file, $langcontent);	
		if($this->getTask() == 'apply'){
			$this->setRedirect('index.php?option='.$option.'&view=managelang&task=edit&layout=form&client='.$post['client'].'&lang='.$post['filename'], JText::_('UPDATED_LANGUAGE_FILE_SUCCESSFULLY'));
		}else{
			$this->setRedirect('index.php?option='.$option.'&view=managelang&client='.$post['client'], JText::_('UPDATED_LANGUAGE_FILE_SUCCESSFULLY'));
		}
	}
}
?>