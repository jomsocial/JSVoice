<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the WebLinks component
 *
 * @static
 * @package		Joomla
 * @subpackage	Weblinks
 * @since 1.0
 */
$viewmenu = JRequest::getVar('viewmenu', 1);
if ($viewmenu) {
	// check menuId
	if (!isset($_GET['view']) || (isset($_GET['view']) && $_GET['view'] == 'voice')) {
		if (isset($_SESSION['menuId'])) {
			unset($_SESSION['menuId']);
		}
	}
	
	$viewmenu = JRequest::getVar('viewmenu',1);
	if (!$viewmenu) {
		parent::display($tpl);
		return;
	}else {
		
		$path = str_replace (JPATH_BASE, '', dirname(__FILE__));
		$path = 'administrator'.str_replace ('\\', '/', $path).'/assets/';

		JHTML::stylesheet ($path.'style.css');
		JHTML::script ($path.'menu.js');
		
		if (JRequest::getVar('menuId',0)) {
			$_SESSION['menuId'] = JRequest::getVar('menuId',0);
		}
		require_once (dirname(__FILE__).'/menu.class.php');								
		include (dirname(__FILE__).'/tmpl/main.php');
	}
}		
		
	    	
