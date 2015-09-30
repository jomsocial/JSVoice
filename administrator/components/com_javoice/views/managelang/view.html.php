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
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

/**
 * Jamanagelang View
 *
 * @package    Joomla.Jashowcase
 * @subpackage Components
 */
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class javoiceViewmanagelang extends JAVBView
{
	/**
	 * managelang view display method
	 * @return void
	 **/
	function display($tmpl = null){
	//Load left menu
		if (! JRequest::getVar("ajax") && JRequest::getVar('tmpl') != 'component' && JRequest::getVar('viewmenu', 1) != 0) {
			$file = JPATH_COMPONENT.'/views/jaview/view.html.php';
			if (file_exists($file)) {
				include_once($file);
			}
		}
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$task = JRequest::getVar("task", '');
		switch ($task){		
			case 'edit':
				$this->show_form();
				break;							
			default:
				$this->show_list();
		}
		$this->addToolbar();
		parent::display();
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
	
	function show_form(){
		$option = JRequest::getCmd('option');
		$lang = JRequest::getVar('lang', '');
		
		$cids = JRequest::getVar('cid', array(), '', 'array');
		if($cids){
			$lang = $cids[0];
		}
		
		$client = JApplicationHelper::getClientInfo(JRequest::getVar('client', '1', '', 'int'));
		$path_dest = JLanguage::getLanguagePath($client->path);
		$root = $path_dest.DS.$lang.DS.$lang.'.'.$option.'.ini';
		
		$data = '';
		$root = JPath::clean($root);
		if($lang!='' && JFile::exists($root)){
			$data = JFile::read($root);			
		}
		else {
			if($client->id){
				$root = JPATH_COMPONENT_ADMINISTRATOR.DS.'language'.DS.$lang.'.'.$option.'.ini';
			}
			else{
				$root = JPATH_COMPONENT_ADMINISTRATOR.DS.'language'.DS.$lang.'.'.$option.'.ini';
			}
			$root = JPath::clean($root);
			if(JFile::exists($root)){
				$data = JFile::read($root);
			}
			else{
				$root = $path_dest.DS.'en-GB'.DS.'en-GB'.'.'.$option.'.ini';
				if($root){
					$data = JFile::read($root);
				}
			}				
		}
		$file = $lang.'.'.$option.'.ini';
		
		$this->assignRef('data', $data);
		$this->assignRef('filename', $file);
		$this->assignRef('lang', $lang);
		$this->assignRef('client', $client);
		$this->assignRef('path_lang', $path_dest);
		$this->assignRef('task', $task);
		$this->assignRef('root', $root);
		
	}
	/**
	* Compiles a list of installed languages
	*/
	function show_list()
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		// Initialize some variables
		$db		= JFactory::getDBO();
		$client	= JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));
		$rows	= array ();			
		
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );
	
		//load folder filesystem class		
		
		$path = JLanguage::getLanguagePath($client->path);
		$dirs = JFolder::folders( $path );
		$i = 0; $data['name'] = '';
		foreach ($dirs as $dir)
		{
			
				$files = JFolder::files( $path.DS.$dir, '^([-_A-Za-z]*)\.xml$' );
				foreach ($files as $file)
				{
					$data = JApplicationHelper::parseXMLLangMetaFile($path.DS.$dir.DS.$file);
					$row 			= new StdClass();
					$row->id 		= $i;
					$row->language 	= substr($file,0,-4);
			
					if (!is_array($data)) {
						continue;
					}
					foreach($data as $key => $value) {
						$row->$key = $value;
					}
					
					// if current than set published
					$params = JComponentHelper::getParams('com_languages');
					if ( $params->get($client->name, 'en-GB') == $row->language) {
						$row->published	= 1;
					} else {
						$row->published = 0;
					}
					
					$row->checked_out = 0;
					$row->mosname = JString::strtolower( str_replace( " ", "_", $row->name ) );
					$rows[] = $row;
				}
				$i++;
			
		}	

		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $i, $limitstart, $limit );

		$rows = array_slice( $rows, $pageNav->limitstart, $pageNav->limit );

		$this->assignRef('rows', $rows);
		$this->assignRef('page', $pageNav);
		$this->assignRef('client', $client);
	}

}