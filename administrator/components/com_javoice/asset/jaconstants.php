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
defined('_JEXEC') or die('Retricted Access');

class javoiceConstant{
	
	
	function get_Variable_Email()
	{
		$variable = array();
		$variable[0]->value = '[USER_NAME]';
		$variable[0]->text = 'USER_NAME - User\'s name';
		$variable[1]->value = '[USER_EMAIL]';
		$variable[1]->text = 'USER_EMAIL - User\' email';
		$variable[2]->value = '[ADMIN_NAME]';
		$variable[2]->text = 'ADMIN_NAME - Administrator\'s name';
		$variable[3]->value = '[ADMIN_EMAIL]';
		$variable[3]->text = 'ADMIN_EMAIL - Administrator\'s email';
		$variable[4]->value = '[CONTACT_EMAIL]';
		$variable[4]->text = 'CONTACT_EMAIL - Email for user contacting';
		$variable[5]->value = '[SITE_URL]';
		$variable[5]->text = 'SITE_URL - Website\'s URL';
		$variable[6]->value = '[SITE_NAME]';
		$variable[6]->text = 'SITE_NAME - Site name';
		return $variable;
	}
	
	function getEmailConfig()
	{
		global $javconfig;
		$mainframe = JFactory::getApplication();
		$emailConfig = array();
		$emailConfig['site_contact_email'] = 'jooms@joomsolutions.com';
		$emailConfig['site_title'] = $jbconfig['emails']->get('sitname');
		$emailConfig['root_url'] = $mainframe->getCfg('live_site');
		$emailConfig['fromemail'] = $jbconfig['emails']->get('fromemail');
		$emailConfig['fromname'] = $jbconfig['emails']->get('fromname');
		$emailConfig['admin_email'] = 'phuonglhvn@gmail.com';
		$emailConfig['admin_name'] = 'Lai Huu Phuong';
		return $emailConfig;
	}
	
	public static function get_Email_Group()
	{
		$result = array(
		'Ja Voice - '.JText::_("VOICE"),
		'Ja Voice - '.JText::_("HEADER__FOOTER")
		);
		return $result;
	}
} 
?>
