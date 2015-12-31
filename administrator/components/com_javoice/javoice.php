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
if (! defined('DS')) {
	define('DS',DIRECTORY_SEPARATOR);
}
JLoader::register('JAVBController', JPATH_COMPONENT.'/controllers/controller.php');
JLoader::register('JAVBView', JPATH_COMPONENT.'/views/view.php');
JLoader::register('JAVBModel', JPATH_COMPONENT.'/models/model.php');

if (version_compare(JVERSION, '3.0', 'ge')){
	require_once (JPATH_COMPONENT .  "/asset/simplexml.php");
}

/*
 * Make sure the user is authorized to view this page
 */
/* Require Helper */
require_once (JPATH_SITE.'/components/com_javoice/helpers/jahelper.php');
$GLOBALS['javconfig'] = array();  
$JAVoiceHelpers = new JAVoiceHelpers();  
$JAVoiceHelpers->get_config_system();

require_once JPATH_COMPONENT_ADMINISTRATOR . '/asset/jaconstants.php';
// Require the base controller
require_once (JPATH_COMPONENT . '/controller.php');

//Require the submenu for component

//require_once (JPATH_COMPONENT.'/views/jaview/view.html.php');

if(!defined('JAVOICE_GLOBAL_SKIN')){
// load mootool
	
	JHTML::_('behavior.framework', true);
	
	$document = JFactory::getDocument();
	
	$document->addScript('//code.jquery.com/jquery-1.8.3.min.js');
	
	$document->addStyleSheet(JURI::root().'administrator/components/com_javoice/asset/css/ja.voice.css');
	
	if(version_compare(JVERSION, '3.0', 'ge')){
		$document->addStyleSheet(JUri::root().'administrator/components/com_javoice/asset/css/ja.voice.j3x.css');
	}
	
	$document->addStyleSheet(JURI::root().'components/com_javoice/asset/css/ja.voice.css');
	
	if(JRequest::getVar('group', '')!='plugin'){
		$document->addStyleSheet(JURI::root().'components/com_javoice/asset/css/ja.popup.css');
	}
	//JHTML::_('script', 'ja.voice.js',JURI::root().'administrator/components/com_javoice/asset/js/');
	$document->addScript(JURI::root().'administrator/components/com_javoice/asset/js/ja.voice.js');
	//JHTML::_('script', 'ja.popup.js',JURI::root().'administrator/components/com_javoice/asset/js/');
	$document->addScript(JURI::root().'administrator/components/com_javoice/asset/js/ja.popup.js');
	
   	define('JAVOICE_GLOBAL_SKIN', true);
}

jimport('joomla.application.component.model'); 
JAVBModel::addIncludePath(JPATH_ROOT.'/components/com_javoice/models');

		
if($controller = JRequest::getCmd('view', 'voice')) {	
	$path = JPATH_COMPONENT.'/controllers/'.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}
$view = $controller;

// Create the controller
$classname = 'JAVoiceController' . ucfirst ( $controller );
$controller = new $classname ( );

$task = JRequest::getVar ( 'task', null, 'default', 'cmd' );

// Perform the Request task
$controller->execute ( $task );




// Redirect if set by the controller
$controller->redirect ();
