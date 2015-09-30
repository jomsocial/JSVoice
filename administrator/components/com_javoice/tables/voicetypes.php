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

class Tablevoicetypes extends JTable{
 	/** @var int */
 	var $id=0;
 	/** @var varchar */
 	var $title=null;
 	/** @var tinyint */
 	var $total_votes=1;
 	/** @var text */
 	var $vote_option=null;
 	/** @var int */
 	var $ordering=0;
 	/** @var tinyint */
 	var $published=1;
 	/** @var text */
 	var $description=null; 	
 	/** @var varchar */
 	var $search_title=null; 	
 	/** @var text */
 	var $search_description=null; 
 	
 	/** @var text */
 	var $search_button=null; 
 	
 	/** @var tinyint */
 	var $display_voting=1;		

 	/** @var tinyint */
 	var $has_answer=0;	
 	/** @var varchar */
 	var $alias = NULL;
 	var $language_response = null; 
 	
 	function __construct(&$db){
 		parent::__construct( '#__jav_voice_types', 'id', $db );
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
		if(!$this->title)
			$error[]=JText::_("TITLE_MUST_NOT_BE_NULL");		
		if(!isset($this->id))
			$error[]=JText::_("ID_MUST_NOT_BE_NULL");
		elseif(!is_numeric($this->id))
			$error[]=JText::_("ID_MUST_BE_NUMBER");
		if(!isset($this->total_votes))
			$error[]=JText::_("TOTAL_VOTES_MUST_NOT_BE_NULL");
		elseif(!is_numeric($this->total_votes))
			$error[]=JText::_("TOTAL_VOTES_MUST_BE_NUMBER");
		if(!isset($this->vote_option))
			$error[]=JText::_("VOTE_OPTIONS_MUST_NOT_BE_NULL");
		if(!isset($this->ordering))
			$error[]=JText::_("ORDERING_MUST_NOT_BE_NULL");
		elseif(!is_numeric($this->ordering))
			$error[]=JText::_("ORDERING_MUST_BE_NUMBER");

			return $error;
		}
}
?>