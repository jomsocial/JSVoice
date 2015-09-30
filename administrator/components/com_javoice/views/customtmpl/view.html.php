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
class javoiceViewcustomtmpl extends JAVBView
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
			case 'add':
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
		$layouts = $this->_getLayouts($list);

        $this->assignRef('layouts', $layouts);       
        
  	}
  	/**
  	* Display edit form
  	*/
  	function displayForm(){
  		$option = JRequest::getCmd('option');
		$folder = JRequest::getVar('folder', '');		
		$file = JRequest::getVar('file', '');
  		$template=JAVoiceHelpers::checkFileTemplate($file,'html',$folder);
		$content='';
		if($template){
			$content=JFile::read($template);
		}else{
			
			$filename = JPATH_COMPONENT_SITE.'/views/'.$folder.'/tmpl/'.$file;
			
			if(JFile::exists($filename))
				$content = JFile::read($filename);
		}		
		
  	    $this->assignRef('content', $content);
  	    $this->assignRef('file', $file);
  	    $this->assignRef('folder', $folder);
  	    $this->assignRef('option', $option);
	}
	
	
	function _getLayouts(&$list){
		require_once(JPATH_ADMINISTRATOR.'/components/com_javoice/views/customtmpl/tmpl/ilink.php');
		$handler		= new iLink('javoice', '', '');				
		
		$return = false;
		$path = JPATH_SITE.'/components/com_javoice/views';
		
		if (JFolder::exists($path)) {
			$views = JFolder::folders($path);
		} else {
			return $return;
		}
		
		if (is_array($views) && count($views))
		{
			//$this->addChild(new iLinkNode('Views', null, 'Select the view'), true);
			$return = true;
			foreach ($views as $k=>$view)
			{
				if (strpos($view, '_') === false) {
					// Load view metadata if it exists
					$xmlpath = $path.'/'.$view.'/metadata.xml';
					if (JFile::exists($xmlpath)) {
						$data = $handler->_getXML($xmlpath, 'view');
					} else {
						$data = null;
					}
					
					//$url = 'url[option]=com_'.$this->_com.'&amp;url[view]='.$view;
					if ($data) {
						if ($data->attributes('hidden') != 'true') {
							$m = $data->getElementByPath('message');
							if ($m) {
								$message = $m->data();
							}
							$list[$k]['folder'] = array($data->attributes('title'), $message, $view);							
							
							$xmlpath = $path.'/'.$view.'/tmpl/metadata.xml';
							if (JFile::exists($xmlpath)) {
								$list[$k]['nodes'] = $this->_getXML($xmlpath, 'layout');
							} else {
								$list[$k]['nodes'] = null;
							}													
						}
					} 
				}
			}
		}
		
		return $list;
	}
	
	function _getXML($path, $xpath='control')
	{
		// Initialize variables
		$result = null;
		// load the xml metadata
		if (file_exists( $path )) {
			//$xml = JFactory::getXMLParser('Simple');
			$xml = new JSimpleXML();
			if ($xml->loadFile($path)) {
				if (isset( $xml->document )) {
					$list = $xml->document->_children;
					foreach ($list as $k=>$row){
						$result[$k]['filename'] =  $row->attributes('filename').'.php';
						$result[$k]['description'] =  $row->message[0]->_data;
						
					}
				}
			}
		}
		return $result;
	}
}
?>