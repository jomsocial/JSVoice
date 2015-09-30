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
defined('_JEXEC') or die('Restricted access');

class TableTags extends JTable {

	var $id = null;
	var $name = null;
	var $published = null;

	function __construct( & $db) {

		parent::__construct('#__jav_tags', 'id', $db);
	}

	function check() {

		if (trim($this->name) == '') {
			$this->setError(JText::_('TAG_CANNOT_BE_EMPTY'));
			return false;
		}
		$this->name = str_replace('-','',$this->name);
		return true;
	}

}
