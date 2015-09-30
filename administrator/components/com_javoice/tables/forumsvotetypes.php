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

defined('_JEXEC') or die('Restricted access');

class Tableforumsvotetypes extends JTable{
 	/** @var int */
 	var $forums_id=0;
 	/** @var int */
 	var $voice_types_id=0;
 	/** @var int */
 	var $total_items=0;
 	/** @var tinyint */
 	var $vote_counting=0;
 	/** @var text */
 	var $vote_option=null;
 	/** @var int */
 	var $voice_type_status_id=0;
 	function __construct(&$db){
 		parent::__construct( '#__jav_forums_has_voice_types', 'id', $db );
 }
 	function bind( $array, $ignore='' ){
		if (key_exists( 'params', $array ) && is_array( $array['params'] ))
		{
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = $registry->toString();
		}
		return parent::bind($array, $ignore);
 	}
	function check(){
		$error=array();
		/** check error data */
		if(!isset($this->forums_id))
			$error[]=JText::_("FORUMS_ID_MUST_NOT_BE_NULL");
		elseif(!is_numeric($this->forums_id))
			$error[]=JText::_("FORUMS_ID_MUST_BE_NUMBER");
		if(!isset($this->voice_types_id))
			$error[]=JText::_("VOICE_TYPES_ID_MUST_NOT_BE_NULL");
		elseif(!is_numeric($this->voice_types_id))
			$error[]=JText::_("VOICE_TYPES_ID_MUST_BE_NUMBER");
		if(!isset($this->total_items))
			$error[]=JText::_("TOTAL_ITEMS_MUST_NOT_BE_NULL");
		elseif(!is_numeric($this->total_items))
			$error[]=JText::_("TOTAL_ITEMS_MUST_BE_NUMBER");
		if(!isset($this->vote_counting))
			$error[]=JText::_("VOTE_COUNTING_MUST_NOT_BE_NULL");
		elseif(!is_numeric($this->vote_counting))
			$error[]=JText::_("VOTE_COUNTING_MUST_BE_NUMBER");
		if(!isset($this->voice_type_status_id))
			$error[]=JText::_("VOICE_TYPE_STATUS_ID_MUST_NOT_BE_NULL");
		elseif(!is_numeric($this->voice_type_status_id))
			$error[]=JText::_("VOICE_TYPE_STATUS_ID_MUST_BE_NUMBER");

			return $error;
		}
}
?>