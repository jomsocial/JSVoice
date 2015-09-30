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

class Tablelogs extends JTable{
 	/** @var int */
 	var $id=0;
 	/** @var int */
 	var $user_id=0;
 	/** @var int */
 	var $item_id=0;
 	/** @var int */
 	var $time_expired=0;
 	/** @var varchar */
 	var $remote_addr=null;
 	function __construct(&$db){
 		parent::__construct( '#__jav_logs', 'id', $db );
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
		if(!isset($this->user_id))
			$error[]=JText::_("USER_ID_MUST_NOT_BE_NULL");
		elseif(!is_numeric($this->user_id))
			$error[]=JText::_("USER_ID_MUST_BE_NUMBER");
		if(!isset($this->item_id))
			$error[]=JText::_("ITEM_ID_MUST_NOT_BE_NULL");
		elseif(!is_numeric($this->item_id))
			$error[]=JText::_("ITEM_ID_MUST_BE_NUMBER");

			return $error;
		}
}
?>