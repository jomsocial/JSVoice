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
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.view');

/**
 * @package		Joomla
 * @subpackage	Contacts
 */
class javoiceViewemailtemplates extends JAVBView 
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
				
			case 'show_duplicate':
				$this->show_duplicate();	
				break;	
			case 'show_import':
				$this->show_import();
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
		JToolBarHelper::title ( JText::_('EMAIL_TEMPLATE_MANAGER' ) );
		switch ($task) {
			case 'add' :
			case 'edit' :
				{
					JToolBarHelper::apply ();
					JToolBarHelper::save ();
					JToolBarHelper::cancel ();
				}
				break;
			case 'show_duplicate' :
			case 'show_import' :
			case 'show_export' :
				break;
			default :
				{
					JToolBarHelper::custom ( 'show_duplicate', 'copy', '', JText::_('COPY_TO' ) );
					JToolBarHelper::custom ( 'show_import', 'upload', '', JText::_('IMPORT' ), false );
					JToolBarHelper::custom ( 'export', 'export', '', JText::_('EXPORT' ) );
					JToolBarHelper::publishList ();
					JToolBarHelper::unpublishList ();
					JToolBarHelper::deleteList ( JText::_('ARE_YOU_SURE_TO_DELETE' ) );
					JToolBarHelper::editList ();
					JToolBarHelper::addNew();
				}
				break;
		}
  	}
  	
  	/**
  	* Display List of items
  	*/
  	function displayListItems(){	
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
        $option_1 = $option.'.emailtemplates';
		$search	= $mainframe->getUserStateFromRequest( "$option_1.search", 'search', 	'',	'string' );
		$lists['search']	= JString::strtolower($search );
		$lists['order']		= $mainframe->getUserStateFromRequest( $option_1.'.filter_order',		'filter_order',		'name',	'cmd' );
		$lists['order_Dir']	= $mainframe->getUserStateFromRequest( $option_1.'.filter_order_Dir',	'filter_order_Dir',	'',				'word' );
		$lists['search']	= $mainframe->getUserStateFromRequest( "$option_1.search", 'search', 	'',	'string' );
		$lists['option']	= $option;
		
		$filter_state		= $mainframe->getUserStateFromRequest( $option_1.'.filter_state', 'filter_state', '',	'word' );				
		$filter_lang		= $mainframe->getUserStateFromRequest( $option_1.'.filter_lang', 'filter_lang', 'en-GB',	'string' );				
		// state filter
		$lists['state']	= JHTML::_('grid.state',  $filter_state );
		
  	    $languages = $this->getModel('emailtemplates')->getLanguages(0);  	    
		$languages   = JHTML::_('select.genericlist', $languages, 'filter_lang', 'class="inputbox" size="1" onChange="$(\'task\').value=\'\'; form.submit()"','language', 'name', $filter_lang );	
		$this->assignRef('languages', $languages);
		$this->assignRef('filter_lang', $filter_lang);
		
		$arr_group = javoiceConstant::get_Email_Group();  
        // get data items
        
        $model = $this->getModel();
        
        $items = $model->getItems(); 
        $this->assign('counts', count($items));
        if($items) $items = $model->group_filter($items, $arr_group, 'group');
        $this->assignRef('items', $items);

       	
        $en_items = array();
        if($filter_lang!='en-GB'){
        	$en_items = $model->getItems('en-GB');
        	if($en_items){
        		$en_items = $model->group_filter($en_items, $arr_group, 'group');        		        		
        	}
        }       
        $this->assign('en_items', $en_items);
             
//        for ($i=0;$i<count($items);$i++) 
//        	$items[$i]->group  = $arr_group[$items[$i]->group];
        
//        $pagination = &$this->get('Pagination');
        
        $this->assignRef('arr_group', $arr_group);
        $this->assignRef('lists', $lists);
       
//        $this->assignRef('pagination', $pagination);
        
  	}
  	/**
  	* Display edit form
  	*/
  	function displayForm(){  	    
  		$option = JRequest::getCmd('option');
  	    $item = $this->get('Item');  
  	    	
		if(!$item->language) $item->language = 'en-GB';
  	    $languages = $this->getModel('emailtemplates')->getLanguages(0);  	    
		$languages   = JHTML::_('select.genericlist', $languages, 'language', 'class="inputbox" size="1"','language', 'name', $item->language );	
		$this->assignRef('languages',$languages);
		
		$PARSED_EMAIL_TEMPLATES_CONFIG = $this->getModel('emailtemplates')->parse_email_config();
				
		$this->assignRef('comment', $PARSED_EMAIL_TEMPLATES_CONFIG['emails'][$item->name]['comment']);
		
		/// get message tags
	    $tags = array();
	    if (isset($PARSED_EMAIL_TEMPLATES_CONFIG['emails'][$item->name]['tagsets']))
	    foreach ((array)$PARSED_EMAIL_TEMPLATES_CONFIG['emails'][$item->name]['tagsets'] as $ts){
	        $tags = array_merge_recursive($tags, $PARSED_EMAIL_TEMPLATES_CONFIG['tagset'][$ts]);
	    }
	    if (isset($PARSED_EMAIL_TEMPLATES_CONFIG['emails'][$item->name]['tags']))
	    foreach ((array)$PARSED_EMAIL_TEMPLATES_CONFIG['emails'][$item->name]['tags'] as $k => $v){
	        $tags[$k] = $v;
	    }
		if (count($tags) <=1) {
			if (isset($PARSED_EMAIL_TEMPLATES_CONFIG['emails'][$item->name]['tagsets']))
			foreach ((array)$PARSED_EMAIL_TEMPLATES_CONFIG['emails']['default']['tagsets'] as $ts){
				$tags = array_merge_recursive($tags, $PARSED_EMAIL_TEMPLATES_CONFIG['tagset'][$ts]);
			}
		}
		$i = 0;
	    $tags_to_assign = array();
	    foreach ($tags as $k=>$v){
	    	$row = new stdClass();
	    	$row->value = '{' . $k . '}';
	    	$row->text = '{' . $k . '} - '  . $v;
			$tags_to_assign[$i] = $row;
			$i++;
	    }
	    $default = array();
	    $default[] = JHTML::_('select.option',  '', JText::_('PLEASE_CHOOSE_AN_OPTION_BELOW_AND_IT_WILL_BE_INSERTED_INTO_EMAIL_MESSAGE'));
	    $tags_to_assign = array_merge($default, $tags_to_assign);
	    $tags_to_assign   = JHTML::_('select.genericlist', $tags_to_assign, 'tags', 'class="small" style="background-color: buttonface; width:100%; color: black;" onclick="insertVariable(this)" size="20"','value', 'text');	
	   
	    $this->assignRef('tags', $tags_to_assign);
    
    	
  	    // clean item data
		$put[] = JHTML::_('select.option',  '1', JText::_('JYES' ));
		$put[] = JHTML::_('select.option',  '0', JText::_('JNO' ));
		$option_group = array();
		$arr_group = javoiceConstant::get_Email_Group();
		for($i = 0, $n = count($arr_group); $i < $n; $i++){
		$option_group[] = JHTML::_('select.option',$i,$arr_group[$i]);
		}
		$html_group = JHTML::_('select.genericlist',   $option_group, 'group', 'class="inputbox" size="1"', 'value', 'text', $item->group);
		
		
		// If not a new item, trash is not an option
		
		if ( !$item->id) {
			$item->published = 1;
		}
		$published = JHTML::_('select.radiolist',  $put, 'published', '', 'value', 'text', $item->published );
		
		// clean item data
		JFilterOutput::objectHTMLSafe( $item, ENT_QUOTES, '' );
		
		$editor = JFactory::getEditor();

		$item->name = JRequest::getVar('tpl', $item->name);
		
		$this->assignRef('editor',$editor);
		$this->assignRef('group',$html_group);
		
  	    $this->assignRef('option', $option);
  	    $this->assignRef('published', $published);
  	    $this->assignRef('item', $item);
	}
	
	
	function show_duplicate(){
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();		
		$filter_lang		= $mainframe->getUserStateFromRequest( $option.'.emailtemplates.filter_lang', 'filter_lang', 'en-GB',	'string' );
		
		$this->assign('option', $option);		
		
		$languages = $this->getModel('emailtemplates')->getLanguages(0);
		/*foreach ($languages as $k=>$lang){
			if ($lang->language==$filter_lang) {
				unset($languages[$k]);
			}
		}*/
		$languages   = JHTML::_('select.genericlist', $languages, 'filter_lang', 'class="inputbox" size="1"','language', 'name', $filter_lang );
		$this->assign('languages', $languages);
		
		
		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger ( $cid, array (0 ) );
		$cid = implode(',', $cid);
		$this->assign('cid', $cid);
	}
	
	function show_import(){
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$this->assign('option', $option);		
		
		$languages = $this->getModel('emailtemplates')->getLanguages(0);
		
		$languages   = JHTML::_('select.genericlist', $languages, 'filter_lang', 'class="inputbox" size="1"','language', 'name', '' );
		$this->assign('languages', $languages);				
	}
		
}
?>