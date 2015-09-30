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

class Tableactionslog extends JTable{
 	/** @var int */
 	var $id=0;
 	/** @var int */
 	var $type='';
 	/** @var text */
 	var $details=null;
 	/** @var int */
 	var $ref_id=0;
 	/** @var int */
 	var $user_id=0;
 	/** @var text */
 	var $remote_addr=0;
 	/** @var int */
 	var $time=0;
 	function __construct(&$db){
 		parent::__construct( '#__jav_actions_log', 'id', $db );
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
}
?>