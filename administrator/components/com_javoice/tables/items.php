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

class Tableitems extends JTable{
 	/** @var int */
 	var $id=0;
 	/** @var int */
 	var $voice_type_status_id=0;
 	/** @var int */
 	var $forums_id=0;
 	/** @var int */
 	var $voice_types_id=0;
 	/** @var varchar */
 	var $title=null;
 	/** @var text */
 	var $content=null;
 	/** @var int */
 	var $user_id=0;
 	/** @var datetime */
 	var $create_date=NULL;
 	/** @var datetime */
 	var $update_date=NULL;
 	
 	/** @var int */
 	var $number_vote_up=0;
 	/** @var int */
 	var $total_vote_up=0;
 	/** @var int */
 	var $number_vote_down=0;
 	/** @var int */
 	var $total_vote_down=0;
 	/** @var int */
 	var $number_vote_neutral=0;
 	/** @var int */
 	var $number_spam=0;
 	/** @var int */
 	//var $number_duplicate=0;
 	/** @var int */
 	//var $number_inapproprivate=0;
 	/** @var tinyint */
 	var $published=0;
 	
 	/** @var tinyint */
 	var $data=0;
 	
 	var $guest_name= null;
 	var $guest_email=null;
 	var $guest_website=null;
 	var $guest_file =0;
	/** @var tinyint */
	var $use_anonymous=0;
	/** @var tinyint */
	var $is_private=0;
 	
 	function __construct(&$db){
 		parent::__construct( '#__jav_items', 'id', $db );
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
		if($this->title=='')
			$error[]=JText::_("TITLE_MUST_NOT_BE_NULL");		
		if(!isset($this->id))
			$error[]=JText::_("ID_MUST_NOT_BE_NULL");
		elseif(!is_numeric($this->id))
			$error[]=JText::_("ID_MUST_BE_NUMBER");

		if(!isset($this->forums_id))
			$error[]=JText::_("FORUMS_ID_MUST_NOT_BE_NULL");
		elseif(!is_numeric($this->forums_id))
			$error[]=JText::_("FORUMS_ID_MUST_BE_NUMBER");
		if(!isset($this->voice_types_id))
			$error[]=JText::_("VOICE_TYPES_ID_MUST_NOT_BE_NULL");
		elseif(!is_numeric($this->voice_types_id))
			$error[]=JText::_("VOICE_TYPES_ID_MUST_BE_NUMBER");
		if(!isset($this->user_id))
			$error[]=JText::_("USER_ID_MUST_NOT_BE_NULL");
		elseif(!is_numeric($this->user_id))
			$error[]=JText::_("USER_ID_MUST_NUMBER");
		if(!isset($this->number_vote_up))
			$error[]=JText::_("NUMBER_VOTE_UP_MUST_NOT_BE_NULL");
		elseif(!is_numeric($this->number_vote_up))
			$error[]=JText::_("NUMBER_VOTE_UP_MUST_BE_NUMBER");
		if(!isset($this->total_vote_up))
			$error[]=JText::_("TOTAL_VOTE_UP_MUST_NOT_BE_NULL");
		elseif(!is_numeric($this->total_vote_up))
			$error[]=JText::_("TOTAL_VOTE_UP_MUST_BE_NUMBER");
		if(!isset($this->number_vote_down))
			$error[]=JText::_("NUMBER_VOTE_DOWN_MUST_NOT_BE_NULL");
		elseif(!is_numeric($this->number_vote_down))
			$error[]=JText::_("NUMBER_VOTE_DOWN_MUST_NUMBER");
		if(!isset($this->total_vote_down))
			$error[]=JText::_("TOTAL_VOTE_DOWN_MUST_NOT_BE_NULL");
		elseif(!is_numeric($this->total_vote_down))
			$error[]=JText::_("TOTAL_VOTE_DOWN_MUST_BE_NUMBE");
		
		if(!isset($this->number_spam))
			$error[]=JText::_("NUMBER_SPAM_MUST_NOT_BE_NULL");
		elseif(!is_numeric($this->number_spam))
			$error[]=JText::_("NUMBER_SPAM_MUST_BE_NUMBER");
		/*if(!isset($this->number_duplicate))
			$error[]=JText::_("NUMBER_DUPLICATE_MUST_NOT_BE_NULL");
		elseif(!is_numeric($this->number_duplicate))
			$error[]=JText::_("NUMBER_DUPLICATE_MUST_BE_NUMBER");
		if(!isset($this->number_inapproprivate))
			$error[]=JText::_("NUMBER_INAPPROPRIVATE_MUST_NOT_BE_NULL");
		elseif(!is_numeric($this->number_inapproprivate))
			$error[]=JText::_("NUMBER_INAPPROPRIVATE_MUST_BE_NUMBER");*/

		return $error;
	}
}
?>