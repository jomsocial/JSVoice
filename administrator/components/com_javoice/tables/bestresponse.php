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

class Tablebestresponse extends JTable{
 	/** @var int */
 	var $id=0;
 	/** @var int */
 	var $jos_jav_items_id=0;
 	/** @var text */
 	var $content=null;
 	function __construct(&$db){
 		parent::__construct( '#__jav_best_response', 'id', $db );
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
		if(!isset($this->id))
			$error[]=JText::_("ID_MUST_NOT_BE_NULL");
		elseif(!is_numeric($this->id))
			$error[]=JText::_("ID_MUST_BE_NUMBER");
		if(!isset($this->jos_jav_items_id))
			$error[]=JText::_("JOS_JAV_ITEMS_ID_MUST_NOT_BE_NULL");
		elseif(!is_numeric($this->jos_jav_items_id))
			$error[]=JText::_("JOS_JAV_ITEMS_ID_MUST_NOT_BE_NUMBER");

			return $error;
		}
}
?>