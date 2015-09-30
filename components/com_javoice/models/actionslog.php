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
defined('_JEXEC') or die();


jimport('joomla.application.component.model');

class javoiceModelactionslog extends JAVBModel
{
	var $_db=null;
	var $_table=null;
	
	
 function __construct()
	{
		parent::__construct();
		$this->_db=JFactory::getDBO();
	}	
    /**
    * @return JTable Configuration table object
    */
    
 	function &_getTable(){
	    $this->_table = JTable::getInstance('logs', 'Table');	    	
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
	
 function _getVars(){
		$mainframe = JFactory::getApplication();
		$option = 'actionslogs';
		$list = array();
		$list['filter_order'] 		= $mainframe->getUserStateFromRequest( $option.'.filter_order',	'filter_order',	'l.time',	'cmd' );
		$list['filter_order_Dir'] 	= $mainframe->getUserStateFromRequest( $option.'.filter_order_Dir',	'filter_order_Dir',	'',				'word' );
		$list['limit'] 				= $mainframe->getUserStateFromRequest( $option.'list_limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$list['limitstart'] 		= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );
		$list['search'] 			= $mainframe->getUserStateFromRequest( $option.'.search',						'search',			'',	'string' );
		$list['types'] 			= $mainframe->getUserStateFromRequest( $option.'.types',						'types',			'',	'string' );
		$list['runby'] 			= $mainframe->getUserStateFromRequest( $option.'.runby',						'runby',			'0',	'int' );
		return  $list;			
	}

 	function getWhereClause($lists){ 
 		//where clause 
 		$where=array(); 
 		if($lists['search']){ 
 			$where[]=" l.id ='".$lists['search']."' " ; 
 			$where[]=" l.user_id ='".$lists['search']."' " ; 
 		}
 		$where		= count( $where ) ? ' WHERE ' . implode( ' OR ', $where ) : ''; 
 		if($lists['types']!='-Select Type-' && $lists['types']!='')
 			$where.=" AND l.type='{$lists['types']}'";
 		if($lists['runby']!=0){
 			if($lists['runby']==1)$where.=" AND l.user_id > 0";
 			elseif($lists['runby']==2) $where.=" AND l.user_id =-1";
 		}
  		Return $where; 
 	} 

	function getTotal($where_more = '') {
		$db = JFactory::getDBO ();
		
		$query = "SELECT count(l.id) FROM #__jav_actions_log as l" 
				. "\n WHERE 1=1 $where_more";
		$db->setQuery ( $query );
		return $db->loadResult ();
	}
	
	function getItems($where_more = '', $limitstart = 0, $limit = 0, $order = '') {
		$db = JFactory::getDBO ();
		
		if (! $order) {
			$order = ' l.id';
		}
		if (! $limit)
			$limit = 100;
		
		$sql = "SELECT * " 
				. "\n FROM #__jav_actions_log  as l " 
				. "\n WHERE 1=1 $where_more" 
				. "\n ORDER BY $order " 
				. "\n LIMIT $limitstart, $limit";
		
		$db->setQuery ( $sql );
		return $db->loadObjectList ();
	}
	function getDistinctItems($where_more = '', $limitstart = 0, $limit = 0, $order = '',$join = '') {
		$lists = array();
		$db = JFactory::getDBO ();
		
		if (! $order) {
			$order = ' l.id';
		}
		if (! $limit)
			$limit = 100;
		
		$sql = "SELECT DISTINCT l.ref_id  " 
				. "\n FROM #__jav_actions_log  as l $join" 
				. "\n WHERE 1=1 $where_more" 
				. "\n ORDER BY $order " 
				. "\n LIMIT $limitstart, $limit";

		$db->setQuery ( $sql );
		
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$ids = $db->loadColumn();
		}else{
			$ids = $db->loadResultArray();
		}
		if($ids){
			foreach ($ids as $id){
				$sql = "SELECT * FROM #__jav_actions_log as l "
				. "\n WHERE l.ref_id =$id ORDER BY l.time DESC LIMIT 1";
				$db->setQuery($sql);
				$obj = $db->loadObject();
				if($obj)$lists[]=$obj; 				
			}
		}
		return $lists;
	}	
	function getTypes() {
		$db = JFactory::getDBO ();
		
		$sql = "SELECT distinct(l.type)  " 
				. "\n FROM #__jav_actions_log  as l " 
				. "\n ORDER BY l.type " ;

		$db->setQuery ( $sql );
		return $db->loadObjectList ();
	}	
	function makeLog($user_id,$type,$details='',$ref_id=0,$time=0){
		$db=JFactory::getDBO();
		$action=new stdClass();
		if(!$user_id || !$type)
			return FALSE;
		$action->user_id=$user_id;
		$action->type=$type;
		$action->details=$details;
		$action->ref_id=$ref_id;
		if($time)
			$action->time=$time;
		else
			$action->time=time();
		$action->remote_addr=$_SERVER['REMOTE_ADDR'] ;
		if($db->insertObject('#__jav_actions_log',$action)){
			return TRUE;
		}
		else{
			return FALSE;
		}
		
	}
	function parseLLogs(&$items){

		$count=count(items);
		if($count>0){
			for($i=0;$i<$count;$i++){
				$item = $items[$i];
				if($item->user_id==-1){
					$item->username='Cron';
				}elseif ($item->user_id>0){
					$user=JFactory::getUser($item->user_id);
					$item->username=$user->user_name;
				}
				$item->time=date('d/M/Y',$item->time);
			}
		}
	}	
	function parseItems($items){
		$model_items = JAVBModel::getInstance ( 'items', 'javoiceModel' );
		$count=count($items);
		$temps = array();
		if($count>0){
			for($i=0;$i<$count;$i++){
				$temp = $items[$i];
				$cid[0]=$items[$i]->ref_id;
				$item = $model_items->getItem($cid);
				$temp->item_title=$item?$item->title:'';
				$temp->voice_type_id = $item->voice_types_id ;
				if($items[$i]->user_id==-1){
					$temp->username='Cron';
				}elseif ($items[$i]->user_id>0){
					$user=JFactory::getUser($items[$i]->user_id);
					$temp->username=$user->username;
				}
				$temp->time=date('d/M/Y',$items[$i]->time);
				$temps[]=$temp;
			}
		}
		return $temps;
	}
 } 

 ?>