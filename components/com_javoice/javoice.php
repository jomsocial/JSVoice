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
/*
 * DEVNOTE: This is the 'main' file. 
 * It's the one that will be called when we go to the Javoice component. 
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
if (! defined('DS')) {
	define('DS',DIRECTORY_SEPARATOR);
}
//Require the submenu for component
jimport('joomla.application.component.table');
jimport('joomla.application.component.model');
jimport('joomla.utilities.date');
jimport('joomla.application.component.controller');

JLoader::register('JAVBModel', JPATH_COMPONENT_ADMINISTRATOR.'/models/model.php');
JLoader::register('JAVFController', JPATH_COMPONENT.'/controllers/controller.php');
JLoader::register('JAVBView', JPATH_COMPONENT_ADMINISTRATOR.'/views/view.php');


JTable::addIncludePath(JPATH_SITE .  '/administrator/components/com_javoice/tables');
JAVBModel::addIncludePath(JPATH_SITE .  '/components/com_javoice/models');

/* Require Helper */
require_once (JPATH_SITE .  '/components/com_javoice/helpers/jahelper.php');
$GLOBALS['javconfig'] = array();
JAVoiceHelpers::get_config_system();
global $javconfig;
require_once JPATH_COMPONENT_SITE .  '/asset/cron/cron.php';
if (isset($javconfig['systems']) && $javconfig['systems']->get('is_turn_off_javoice', 0)) {
    if (!JAVoiceHelpers::check_access())
        return;
}

if (!isset($_SESSION['JAV_LAST_VISITED'])) {
    if (isset($_COOKIE['JAV_LAST_VISITED']))
        $_SESSION['JAV_LAST_VISITED'] = $_COOKIE['JAV_LAST_VISITED'];
    else
        $_SESSION['JAV_LAST_VISITED'] = strtotime(date("Y-m-d") . " -3 days");
    setcookie('JAV_LAST_VISITED', time());
}

if (!defined('JAVOICE_GLOBAL_CSS')) {
    $mainframe = JFactory::getApplication();
    JHTML::stylesheet('components/com_javoice/asset/css/ja.voice.css');
    if (file_exists(JPATH_BASE .  '/templates/' . $mainframe->getTemplate() . '/css/ja.voice.css')) {
        JHTML::stylesheet('templates/' . $mainframe->getTemplate() . '/css/ja.voice.css');
    }
    
    JHTML::stylesheet('components/com_javoice/asset/css/ja.popup.css');
    if (file_exists(JPATH_BASE . '/templates/' . $mainframe->getTemplate() . '/css/ja.popup.css')) {
        JHTML::stylesheet('templates/' . $mainframe->getTemplate() . '/css/ja.popup.css');
    }
    
    JHTML::stylesheet('components/com_javoice/asset/css/ja.ie.php');
    if (file_exists(JPATH_BASE . '/templates/' . $mainframe->getTemplate() . '/css/ja.ie.php')) {
        JHTML::stylesheet('templates/' . $mainframe->getTemplate() . '/css/ja.ie.php');
    }
    
    $lang = JFactory::getLanguage();
    if ($lang->isRTL()) {
        JHTML::stylesheet('components/com_javoice/asset/css/ja.voice_rtl.css');
        if (file_exists(JPATH_BASE . '/templates/' . $mainframe->getTemplate() . '/css/ja.voice_rtl.css')) {
            JHTML::stylesheet('templates/' . $mainframe->getTemplate() . '/css/ja.voice_rtl.css');
        }
    }
    
    define('JAVOICE_GLOBAL_CSS', true);
}
if (!defined('JAVOICE_GLOBAL_JS')) {
	
	JHTML::_('behavior.framework', true);
	
	$javersion = new JVersion();
	$document = JFactory::getDocument();
	$document->addScript('//code.jquery.com/jquery-1.8.3.min.js');
	
    JHtml::_('script','components/com_javoice/asset/js/ja.voice.js');
    JHtml::_('script','components/com_javoice/asset/js/ja.popup.js');
    
    define('JAVOICE_GLOBAL_JS', true);
}

if (!JRequest::getCmd('view'))
    JRequest::setVar('view', 'items');
$controller = JRequest::getCmd('view');

require_once (JPATH_SITE . '/components/com_javoice/controller.php');
$view = $controller;
if ($controller) {
    $path = JPATH_SITE . '/components/com_javoice/controllers/' . $controller . '.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}

// Create the controller
$classname = 'JAVoiceController' . ucfirst($controller);
$controller = new $classname();

$task = JRequest::getVar('task', null, 'default', 'cmd');

$controller->execute($task);

// Redirect if set by the controller
$controller->redirect();
?>