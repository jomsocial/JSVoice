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

defined ( '_JEXEC' ) or die ( 'Restricted access' );

jimport ( 'joomla.application.component.view' );

class JAVoiceViewUsers extends JAVBView {
	/**
	 * Display the view
	 */
	function display($tmpl = null) {
		switch ($this->getLayout ()) {			
			case 'emailref' :
				$this->setLayout('default');
				$this->display_email_notification_references ();
				break;
            case 'login':
                $this->showLogin();
                break;				
			default:
				$this->show_statistic();
				
		}
		parent::display ( $tmpl );
	}
	
	function show_statistic(){		
		$model_items = JAVBModel::getInstance('items', 'JAVoiceModel');		
		$types = $model_items->getVoiceTypes ();
		$this->assignRef ( 'types', $types );
				
		$config = new JConfig();
		$pagetitle = @JFactory::getUser(JRequest::getInt('uid'))->username .JText::_('S_VOICE_STATISTICS_AT'). ' ' . $config->sitename;
		$document	= JFactory::getDocument();
		$document->setTitle( $pagetitle );
	}
	
	function getItems(){
		require_once JPATH_SITE.DS.'components'.DS.'com_javoice'.DS.'controllers'.DS.'items.php';
		$items_controller = new JAVoiceControllerItems();
		$items = $items_controller->getItems();
		$this->assignRef ( 'items', $items );
				
		$html = $this->loadTemplate('items');
		
		return $html;
	}
	
	
	/**
	 * This function dieplays email preference center.
	 */
	function display_email_notification_references() {
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO ();
		$user = JFactory::getUser ();
		$this->assignRef('user', $user);		
		
		$object = array();
		$k = 0;
		$object[$k] = new stdClass();
		$object[$k]->id = '#jav-mainbox-emailref';
		$object[$k]->attr = 'html';
		$object[$k]->content = $this->loadTemplate('emailref');
				
		$helper = new JAVoiceHelpers ( );		
		echo $helper->parse_JSON_new ( $object );
		exit ();
	}
	
	function getPatway(){
		global $javconfig;
		$enable_pathway = $javconfig['plugin']->get('enable_pathway', 1);
		if(!$enable_pathway) return '';
		
    	require_once JPATH_SITE.DS.'components'.DS.'com_javoice'.DS.'helpers'.DS.'route.php';
    	$helper = new JavoiceHelperRoute();
    	$list = $helper->getList();
    	$this->assignRef('list', $list);
    	
    	$separator = $helper->setSeparator();
    	$this->assignRef('separator', $separator);
    	
    	$html = $this->loadTemplate('pathway');	
		return $html;
    }
    
    function showLogin(){                
        // form RPX Login
        if(JPluginHelper::isEnabled('system', 'plg_jarpxnow')){
            $plg = JPluginHelper::getPlugin("system","plg_jarpxnow");
            $plgparams = class_exists('JRegistry')? new JRegistry($plg->params) : new JParameter($plg->params);
            
            $application = $plgparams->get('application');
            $token_url = urlencode($_SERVER['HTTP_REFERER']);
            
            //$token_url = urlencode(JURI::root());        
            
            $this->assign ("application", $application);
            $this->assign ("token_url", $token_url);
            
            $_SESSION['ses_url'] = $_SERVER['HTTP_REFERER'];
            
            $base_url = "";
			if ($enale_form_login) {
				$base_url = $_SERVER['HTTP_REFERER'];
				$base_url = base64_encode ( $base_url );
			}
			$this->assignRef ( 'base_url', $base_url );

        }
    } 
	   
}
?>