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

jimport( 'joomla.application.component.view' );

/**
 * @package Joomla
 * @subpackage javoice
 */
class javoiceViewcustomcss extends JAVBView
{
    /**
     * Display the view
     */
    function display($tmpl = null)
    {
    //Load left menu
		if (! JRequest::getVar("ajax") && JRequest::getVar('tmpl') != 'component' && JRequest::getVar('viewmenu', 1) != 0) {
			$file = JPATH_COMPONENT.'/views/jaview/view.html.php';
			if (file_exists($file)) {
				include_once($file);
			}
		}
		$task = JRequest::getVar("task", '');
		switch ($task){
			case 'edit':				
				$this->displayForm();
				break;			
			default:
				$this->displayListItems();
		}
		$this->addToolbar();
		parent::display($tmpl);
    	// Display menu footer
		if (!JRequest::getVar("ajax") && JRequest::getVar('tmpl') != 'component' && JRequest::getVar('viewmenu', 1) != 0) {
			$file = JPATH_COMPONENT_ADMINISTRATOR . "/views/jaview/tmpl/footer.php";
			if (file_exists($file)) {
				include_once($file);
			}
		}
  	}
  	
	function addToolbar(){
  		$task = JRequest::getWord ( 'task', '' );
		$controller = JRequest::getVar ( 'view', NULL );
		switch ($controller) {
			case 'customcss' :
				$title = JText::_("CUSTOM_CSS" );
				break;			
			case 'managelang':
				$title = JText::_("CUSTOM_LANGUAGE" );
				break;
			default:
				$title = JText::_("CUSTOM_TEMPLATE" );
				break;					
		}
		JToolBarHelper::title ($title );
		if ($task == 'edit'){
			JToolBarHelper::save ();
			JToolBarHelper::cancel();
		}
  	}
  	/**
  	* Display List of items
  	*/
  	function displayListItems(){
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
        $option_1 = $option.'.customcss';
		$search	= $mainframe->getUserStateFromRequest( "$option_1.search", 'search', 	'',	'string' );
		$lists['search']	= JString::strtolower($search );
		$lists['search']	= $mainframe->getUserStateFromRequest( "$option_1.search", 'search', 	'',	'string' );
		$lists['option']	= $option;
	
		$path = JPATH_COMPONENT_SITE.DS.'asset'.DS.'css';
		
		$files = JFolder::files($path, '\.css$', false, false);

        $this->assignRef('lists', $lists);
        $this->assignRef('path', $path);
        $this->assignRef('files', $files);
        
  	}
  	/**
  	* Display edit form
  	*/
  	function displayForm(){
  		$option = JRequest::getCmd('option');
		$file = JRequest::getVar('file', '');
		$template=JAVoiceHelpers::checkFileTemplate($file);
		$content='';
		if($template){
			$content=JFile::read($template);
		}
		else{
			
			$filename = JPATH_COMPONENT_SITE.'/asset/css/'.$file;
			
			if(JFile::exists($filename))
				$content = JFile::read($filename);
		}
  	    $this->assignRef('content', $content);
  	    $this->assignRef('file', $file);
  	    $this->assignRef('filename', $filename);
  	    $this->assignRef('option', $option);
	}
}
?>