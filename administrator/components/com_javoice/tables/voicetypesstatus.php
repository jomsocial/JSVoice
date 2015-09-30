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

class Tablevoicetypesstatus extends JTable {
	/** @var int */
	var $id = 0;
	/** @var int */
	var $voice_types_id = 0;
	/** @var int */
	var $parent_id = 0;
	/** @var varchar */
	var $title = null;
	/** @var varchar */
	var $class_css = null;
	/** @var varchar */
	var $name = null;
	/** @var tinyint */
	var $show_on_tab = 0;
	/** @var tinyint */
	var $published = 1;
	/** */
	var $return_vote = 0;
	var $allow_voting = - 1;
	
	var $allow_show = - 1;
	/** */
	var $ordering = 0;
	var $alias = null;
	
	function __construct(&$db) {
		parent::__construct ( '#__jav_voice_type_status', 'id', $db );
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
		if (! isset ( $this->title ) || $this->title == '')
			$error [] = JText::_("TITLE_MUST_NOT_BE_NULL" );
		if (! isset ( $this->voice_types_id ))
			$error [] = JText::_("VOICE_TYPES_ID_MUST_NOT_BE_NULL" );
		elseif (! is_numeric ( $this->voice_types_id ) || $this->voice_types_id == 0)
			$error [] = JText::_("VOICE_TYPES_ID_MUST_BE_NUMBER" );
		elseif (isset ( $this->parent_id ) && ! is_numeric ( $this->parent_id ))
			$error [] = JText::_("PARENT_ID_MUST_BE_NUMBER" );
		return $error;
	}
}
?>
