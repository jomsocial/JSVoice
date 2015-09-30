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

class TableAdmin_responses extends JTable{
	/** @var int */
 	var $id=0;
 	/** @var int */
 	var $item_id=0;
 	/** @var int */
 	var $user_id=0;
 	
 	/** @var text */
 	var $content=null; 	
 	/** @var varchar */
 	var $type=null; 	
 	function __construct(&$db){
 		parent::__construct( '#__jav_admin_responses', 'id', $db );
 	} 	
 	
 	function check(){
 		$error=array();
		/** check error data */
		if(!isset($this->item_id))
			$error[]=JText::_("PLEASE_SELECT_VOICES");
		if(!isset($this->content)){
			$error[]=JText::_("PLEASE_FILL_IN_THE_CONTENT");		
			return $error;
		}
		return ;
 	}
}
?>