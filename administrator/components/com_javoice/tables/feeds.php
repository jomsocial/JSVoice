<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );


/**
 * @package		Joomla
 * @subpackage	JobBoard
 */
class Tablefeeds extends JTable
{
	/** @var int Primary key */
	var $id				= null;
	/** @var string */					
	var $feed_name 			= "";
	
	var $feed_alias			= "";
	
	/** @var int */
	var $feed_description	= "";	
	/** @var string */  
	var $feed_type		= "0.91";
	/** @var string */  
	var $feed_cache		= 0;
	
	var $feed_imgUrl = "";
	
	var $feed_button = "";
	
	var $feed_renderAuthorFormat = "NAME";
	
	var $feed_renderHTML = "";
	
	var $feed_renderImages = "";
	
	var $feed_system = 0;
	
	var $msg_count =20;
	
	var $msg_numWords = 225;
	
	var $filter_forums_id = -1;
	
	var $filter_voicetypes_id = -1;
	
	var $filter_date = null;
	
	var $filter_item_ids = null;
	
	var $filter_creator_ids = null;
		
	var $published = 1;

	var $feed_last_update = 0;
	/**
	* @param database A database connector object
	*/
	function __construct(&$db)
	{
		parent::__construct( '#__jav_feeds', 'id', $db );
	}
	
	function check()
	{
		$error = array ();
		/** check error data */
		if ($this->feed_name =='') $error [] = JText::_("TITLE_MUST_NOT_BE_NULL" );
		if (! isset ( $this->msg_count ))
			$error [] = JText::_("NUMBER_VOICES_MUST_NOT_BE_NULL" );
		elseif ($this->msg_count!='' && ! is_numeric ( $this->msg_count ))
			$error [] = JText::_("NUMBER_VOICES_MUST_BE_NUMBER" );
		if (! isset ( $this->feed_cache ))
			$error [] = JText::_("CACHE_TIMES_MUST_NOT_BE_NULL" );
		elseif ($this->feed_cache!='' &&! is_numeric ( $this->feed_cache ))
			$error [] = JText::_("CACHE_TIMES_MUST_BE_NUMBER" );
		if (! isset ( $this->filter_date ))
			$error [] = JText::_("CREATE_VOICE_BEFORE_DAYS_AGO_MUST_NOT_BE_NULL" );
		elseif ($this->filter_date!='' &&! is_numeric ( $this->filter_date ))
			$error [] = JText::_("CREATE_VOICE_BEFORE_DAYS_AGO_MUST_BE_NUMBER" );	
		if($this->filter_creator_ids){
			$temps = explode(",",$this->filter_creator_ids);
			foreach ($temps as $temp){
				if(!is_numeric($temp))$error[]= JText::_('INCLUDED_CREATORID_IS_NOT_AVAIABLE')	;
				break;		
			}
		}
		if($this->filter_item_ids){
			$temps = explode(",",$this->filter_item_ids);
			foreach ($temps as $temp){
				if(!is_numeric($temp))$error[]= JText::_('INCLUDE_VOICEID_IS_NOT_AVAIABLE')	;
				break;		
			}
		}		
		return $error;		
	}
	/*function load($key)	
	{
		parent::load($key);
		$this->feed_name = self::_decode($this->feed_name);
		$this->feed_name = JFilterInput::clean($this->feed_name);
		$this->feed_alias = self::_decode($this->feed_alias);
		$this->feed_alias = JFilterInput::clean($this->feed_alias);
		$this->feed_description = self::_decode($this->feed_description);
		return $this;
	}*/
	protected function _decode($source)
	{
		static $ttr;

		if(!is_array($ttr))
		{
			// entity decode
			$trans_tbl = get_html_translation_table(HTML_ENTITIES);
			foreach($trans_tbl as $k => $v) {
				$ttr[$v] = utf8_encode($k);
			}
		}
		$source = strtr($source, $ttr);
		// convert decimal
		$source = preg_replace('/&#(\d+);/me', "utf8_encode(chr(\\1))", $source); // decimal notation
		// convert hex
		$source = preg_replace('/&#x([a-f0-9]+);/mei', "utf8_encode(chr(0x\\1))", $source); // hex notation
		return $source;
	}
}