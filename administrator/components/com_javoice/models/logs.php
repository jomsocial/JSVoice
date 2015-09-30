<?php
defined('_JEXEC') or die();
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

jimport('joomla.application.component.model');

class javoiceModellogs extends JAVBModel
{
	var $_db=null;
	var $_table=null;
	
	
 function __construct()
	{
		parent::__construct();
		$this->_db=JFactory::getDBO();
	}	
    /**
    * @return JTable log table object
    */
    
 function &_getTable(){
	   	if($this->_table == null){
        	$this->_table = JTable::getInstance('logs', 'Table');
        }	    	
		return $this->_table;
	}
	/**
	* Get configuration item
	* @return Table object
	*/
	
 function getItem($id=0){
		static $item;
		if (isset($item)) {
			return $item;
		}
		
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));	
			
		if(isset($cid[0]) && $cid[0]>0){
			$id = $cid[0];
		}
		$this->_getTable();
        
		if ($id) {
			$this->_table->load($id);
		}				
		return $this->_table;
	}
	
 function getvar(){
		$mainframe = JFactory::getApplication();
		$option = 'logs';
		$list = array();
		$list['filter_order'] 		= $mainframe->getUserStateFromRequest( $option.'.filter_order',	'filter_order',	'c.ordering',	'cmd' );
		$list['filter_order_Dir'] 	= $mainframe->getUserStateFromRequest( $option.'.filter_order_Dir',	'filter_order_Dir',	'',				'word' );
		$list['limit'] 				= $mainframe->getUserStateFromRequest( $option.'list_limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$list['limitstart'] 		= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );
		$list['search'] 			= $mainframe->getUserStateFromRequest( $option.'.search',						'search',			'',	'string' );
		return  $list;			
	}

 	function getWhereClause($lists){ 
 		//where clause 
 		$where=array(); 
 		if($lists['search']){ 
 		$where[]=" l.id ='".$lists['search']."' " ; 
 		$where[]=" l.user_id ='".$lists['search']."' " ; 
 		$where[]=" l.item_id ='".$lists['search']."' " ; 
 		} 
 		$where		= count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : ''; 
  		Return $where; 
 	} 

 	function getItems($where,$type=0,$orderby='',$limited=''){ 
 		if($type) 
 			$query=" SELECT COUNT(l.id) ";  
 		else 
 			$query=" SELECT l.id, l.user_id, l.item_id, l.time_expired, l.remote_addr  "; 
 		$query.=" FROM #__jav_logs as l ";  
 		$query.=" $where ";  
 		if(!$type){ 
 			$query.=$orderby ;  
 			$query.=$limited ;  
 		} 
 		$this->_db->setQuery($query); 
 		if($type) 
 			return $this->_db->loadResult(); 
 		else 
 			return $this->_db->loadObjectList(); 
 	} 

 	function published($publish){
		$db		= JFactory::getDBO();
		
		$ids = JRequest::getVar('cid', array());		
		$ids = implode( ',', $ids );
		
		$query = "UPDATE #__jav_logs"
		. " SET published = " . intval( $publish )
		. " WHERE id IN ( $ids )"
		;
		$db->setQuery( $query );
		if (!$db->query()) {
			return false;
		}
		return true;
			} 

 	function saveOrder()
	{
		$order=JRequest::getVar('order',null,'post','array');
		$cid=JRequest::getVar('cid',null,'post','array');
		$total=count($cid);
		if($total>0)
		{
			JArrayHelper::toInteger($order,array(0));
			$row= $this->getTable('logs','Table');
			
			for($i=0;$i<$total;$i++)
			{
				$row->load((int)$cid[$i]);				
				if($row->ordering !=$order[$i])
				{
					$row->ordering =$order[$i];
					
					if(!$row->store())
					{
						return false;
					}
				}
			}
		}
		return true;		
	} 

 	function delete($id){
		$query="DELETE FROM #__jav_logs WHERE id=$id";
		$this->_db->setQuery($query);
		return $this->_db->query();
	} 

 } 

 ?>