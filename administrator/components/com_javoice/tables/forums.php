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

class Tableforums extends JTable {
	/** @var int */
	var $id = 0;
	/** @var varchar */
	var $title = null;
	/** @var text */
	var $description = null;
	/** @var int */
	var $ordering = 0;
	/** @var tinyint */
	var $published = 1;
	/** @var varchar */
	var $gids_view = NULL;
	/** @var varchar */
	var $gids_post = NULL;
	/** @var varchar */
	var $alias = null;
	/** @var varchar */
	
	function __construct(&$db) {
		parent::__construct ( '#__jav_forums', 'id', $db );
	}
	function bind($array, $ignore = '') {
		if (key_exists ( 'params', $array ) && is_array ( $array ['params'] )) {
			$registry = new JRegistry ( );
			$registry->loadArray ( $array ['params'] );
			$array ['params'] = $registry->toString ();
		}
		return parent::bind ( $array, $ignore );
	}
	function check() {
		$error = array ();
		/** check error data */
		if ($this->title =='') $error [] = JText::_("TITLE_MUST_NOT_BE_NULL" );
		if (! isset ( $this->ordering ))
			$error [] = JText::_("ORDERING_MUST_NOT_BE_NULL" );
		elseif (! is_numeric ( $this->ordering ))
			$error [] = JText::_("ORDERING_MUST_BE_NUMBER" );
		return $error;
	}
}
?>