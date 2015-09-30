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
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' ); 

jimport( 'joomla.application.component.model' );

/**
 * @package Joomla
 * @subpackage javoice
 */
class JAvoiceModelconfigs extends JAVBModel
{
    var $_data;
    var $_table;
    /**
    * Return the query is used to retrive all row from database
    * @return string The query is used 
    */    
	    
    
    /**
    * Get configuration table instance
    * @return JTable Configuration table object
    */
    function &_getTable(){
        if($this->_table == null){
        	$this->_table = JTable::getInstance('configs', 'Table');
		}
		return $this->_table;
	}		
	
	function publishComponent($component,$publish=0){
		$query =" UPDATE #__components SET enabled = $publish WHERE option ='$component' ";
		$this->_db->setQuery($query);
		return $this->_db->query();		
	}
	function getItems(){
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$group = JRequest::getVar('group', 'systems');
		$db = JFactory::getDBO();
		
		$query = "SELECT * "
        		 ."FROM #__jav_configs as s WHERE s.group='".$group."'";
		$db->setQuery($query);
		$items = $db->loadObjectList();
		if(!$items){
			$items[0] = new stdClass();
			$items[0]->id = 0;
			$items[0]->data = '';
		}
		return $items[0];
	}
	/**
	* Get configuration item
	* @return Config Table object
	*/
	function getItem($cid=0){
		static $item;
		if (isset($item)) {
			return $item;
		}
		$group = JRequest::getVar('group', 'systems');
		$table = $this->_getTable();

		// Load the current item if it has been defined
		if(!$cid){
			$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
			JArrayHelper::toInteger($cid, array(0));
		}
		if($cid)
			$table->load($cid[0]);
		$table->group = $group;
	    $item = $table;
	    return $item;	    
	}	
	/** 
	* Store configuration item
	* @param array The post array
	*/
	function store(){
	    // Initialize variables
		$db		= JFactory::getDBO();
		$row	= $this->getItem();
		$post	= $this->getState( 'request' );
		if (!$row->bind( $post )) {
			JError::raiseWarning(1001,JText::_("ERROR_OCCURRED_CAN_NOT_BIND_THE_DATA" ));
			return FALSE;
		}
		if (!$row->check())
		{
			JError::raiseWarning(1001,JText::_("FAILLURE_TO_SAVE_DATA" ));
			return FALSE;			
		}
		if (!$row->store())
		{
			JError::raiseWarning(1001,JText::_("FAILLURE_TO_SAVE_DATA" ));
			return FALSE;
		}
		$row->checkin();
		return $row->id;
	}	
	function parse(&$params,$voicetypes){
		$count=count($voicetypes);
		
		if($count>0){
			for($i=0;$i<$count;$i++){	
				$title='';			
				$voicetype=$voicetypes[$i];				
				$status_id=$params->get("status_spam_{$voicetype->id}",0);
				if($status_id){
					$modelvoicetypesstatus = JAVBModel::getInstance ( 'voicetypesstatus', 'javoiceModel' );
					$status=$modelvoicetypesstatus->getItem($status_id);
					if($status){
						$title.=$status->title;
						if($status->parent_id){
							$parents=$modelvoicetypesstatus->getItem($status->parent_id);
							if($parents)
								$title="<span style='font-weight:bold;' id='jav_parent_title_$voicetype->id'>".$parents->title."</span>: "
								."<span id='jav_title_$voicetype->id'>".$title."</span>";
						}
					}
				}
				if($title=='')$title="<span style='font-weight:bold;' id='jav_parent_title_$voicetype->id'>----</span>: "."<span id='jav_title_$voicetype->id'>---</span>";
				$params->set("status_spam_title_{$voicetype->id}",$title);
			}
		}
	}
}
?>