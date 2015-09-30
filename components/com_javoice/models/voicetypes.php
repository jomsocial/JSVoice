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
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.model' );

class javoiceModelvoicetypes extends JAVBModel {
	var $_table = null;
	
	function __construct() {
		parent::__construct ();
	}
	/**
	 * Get configuration item
	 * @return Table object
	 */
	function _getTable() {
		$this->_table = JTable::getInstance ( 'voicetypes', 'Table' );
	}
	/**
	 * Get configuration item
	 * @return Table object
	 */
	
	function getItem($id = 0) {
		static $item;
		if (isset ( $item )) {
			return $item;
		}
		if (! $id) {
			$cid = JRequest::getVar ( 'cid', array (0 ), '', 'array' );
			JArrayHelper::toInteger ( $cid, array (0 ) );
			
			if (isset ( $cid [0] ) && $cid [0] > 0) {
				$id = $cid [0];
			}
		}
		$this->_getTable ();
		
		if ($id) {
			$this->_table->load ( $id );
		}
		
		return $this->_table;
	}
	
	function _getVars() {
		$mainframe = JFactory::getApplication();
		$option = 'voicetypes';
		$list = array ();
		$list ['filter_order'] = $mainframe->getUserStateFromRequest ( $option . '.filter_order', 'filter_order', 't.ordering', 'cmd' );
		$list ['filter_order_Dir'] = $mainframe->getUserStateFromRequest ( $option . '.filter_order_Dir', 'filter_order_Dir', '', 'word' );
		$list ['limit'] = $mainframe->getUserStateFromRequest ( $option . 'list_limit', 'limit', $mainframe->getCfg ( 'list_limit' ), 'int' );
		$list ['limitstart'] = $mainframe->getUserStateFromRequest ( $option . '.limitstart', 'limitstart', 0, 'int' );
		$list ['search'] = $mainframe->getUserStateFromRequest ( $option . '.search', 'search', '', 'string' );
		return $list;
	}
	
	function getWhereClause($lists) {
		//where clause 
		$where = array ();
		if ($lists ['search']) {
			if (is_numeric ( $lists ['search'] ))
				$where [] = " t.id ='" . $lists ['search'] . "' ";
			else
				$where [] = " t.title LIKE '%" . $lists ['search'] . "%' ";
		}
		$where = count ( $where ) ? " AND " . implode ( ' AND ', $where ) : '';
		Return $where;
	}
	
	function getItems($where = '', $orderby = '', $limitstart = 0, $limit = 0, $fields = '', $joins = '') {
		$db = JFactory::getDBO ();
		$query = " SELECT t.* ";
		if ($fields)
			$query .= " ,$fields ";
		$query .= " FROM #__jav_voice_types as t ";
		if ($joins)
			$query .= " $joins ";
		$query .= " WHERE 1 $where ";
		if ($orderby)
			$query .= " ORDER BY $orderby ";
		if ($limit > 0)
			$query .= " LIMIT $limitstart,$limit ";
		
		$db->setQuery ( $query );
		
		return $db->loadObjectList ();
	}

	function getDyamicItems($where = '', $orderby = '', $limitstart = 0, $limit = 0, $fields = '', $joins = '',$type='') {
		$db = JFactory::getDBO ();
		$query='';		
		if ($fields)
			$query .= "SELECT $fields ";
		else 
			$query .= " SELECT t.* ";
			
		$query .= " FROM #__jav_voice_types as t ";
		if ($joins)
			$query .= " $joins ";
		$query .= " WHERE 1 $where ";
		if ($orderby)
			$query .= " ORDER BY $orderby ";
		if ($limit > 0)
			$query .= " LIMIT $limitstart,$limit ";
		
		$db->setQuery ( $query );
		if($type=='object'){
			return $db->loadObjectList();
		}	
		else{
			
			if (version_compare(JVERSION, '3.0', 'ge'))
			{
				return $db->loadColumn();
			}else{
				return $db->loadResultArray();
			}
		}
	}
		
	function getTotal($where = '', $joins = '') {
		$db = JFactory::getDBO ();
		$query = " SELECT COUNT(t.id) " . " FROM #__jav_voice_types as t " . "\n  $joins" . " WHERE 1 $where ";
		$db->setQuery ( $query );
		return $db->loadResult ();
	}
	
	function getOrdering($item) {
		$neworder = 0;
		$query = 'SELECT ordering AS value, title AS text' . ' FROM #__jav_voice_types' . ' ORDER BY ordering';
		
		if (is_object($item))
		{
			$item = $item->ordering;
		}

		if (isset($item->id))
		{
			$neworder = 0;
		}
		else
		{
			if ($neworder)
			{
				$neworder = 1;
			}
			else
			{
				$neworder = -1;
			}
		}
		return JHtmlList::ordering('ordering', $query, '', $item, $neworder);
		
		//return JHTML::_ ( 'list.specificordering', $item, $item->id, $query );
	}
	function published($publish) {
		$db = JFactory::getDBO ();
		
		$ids = JRequest::getVar ( 'cid', array () );
		$ids = implode ( ',', $ids );
		
		$query = "UPDATE #__jav_voice_types" . " SET published = " . intval ( $publish ) . " WHERE id IN ( $ids )";
		$db->setQuery ( $query );
		if (! $db->query ()) {
			return false;
		}
		return true;
	}
	function selected($vote_counting) {
		$db = JFactory::getDBO ();
		
		$ids = JRequest::getVar ( 'cid', array () );
		$ids = implode ( ',', $ids );
		
		$query = "UPDATE #__jav_voice_types" . " SET vote_counting = " . intval ( $vote_counting ) . " WHERE id IN ( $ids )";
		$db->setQuery ( $query );
		if (! $db->query ()) {
			return false;
		}
		return true;
	}
	function saveOrder()
	{		
		$mainframe = JFactory::getApplication();
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Initialize variables
		$db			= JFactory::getDBO();

		$cid		= JRequest::getVar( 'cid', array(0), 'request', 'array' );
		$order		= JRequest::getVar( 'order', array (0), 'request', 'array' );
		$total		= count($cid);
		$conditions	= array ();

		JArrayHelper::toInteger($cid, array(0));
		JArrayHelper::toInteger($order, array(0));

		// Instantiate an article table object
		$row = $this->getTable ( 'voicetypes','Table' );

		// Update the ordering for items in the cid array
		for ($i = 0; $i < $total; $i ++)
		{
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					JError::raiseError( 500, $db->getErrorMsg() );
					return false;
				}
			}
		}
		// execute updateOrder for each group
		$row->reorder(1);
		return TRUE;
	}		
	function remove() {
		$db=JFactory::getDBO();
		$cids = JRequest::getVar ( 'cid', null, 'request', 'array' );
		$count = count ( $cids );
		$errors = array ();
		$id_used=array();
		$is_fail=array();
		if ($count > 0) {
			$model_items = JAVBModel::getInstance ( 'items', 'javoiceModel' );
			$model_forums = JAVBModel::getInstance ( 'forums', 'javoiceModel' );
			foreach ( $cids as $cid ) {
				$total_item = $model_items->getTotal ( " AND i.voice_types_id=" . ( int ) $cid );
				$join = " INNER JOIN #__jav_forums_has_voice_types as t ON f.id = t.forums_id";
				$total_forum = $model_forums->getTotal ( " AND t.voice_types_id =" . ( int ) $cid, $join );
				if ($total_forum > 0 || $total_item > 0) {
					if($total_forum > 0){
						$id_used_forum[]=$this->getItem($cid)->title;
					}
					if($total_item >0){
						$id_used_items[]=$this->getItem($cid)->title;
					}	
				}else {
					$query = "DELETE FROM #__jav_voice_types WHERE id=$cid";
					$db->setQuery ( $query );
					if(!$db->query ()){
						$is_fail[]=$cid;
					}
				}
			}
			if(count($id_used_forum)>0 || count($id_used_items)>0){
				$errors [] = '';
				if(count($id_used_forum)>0){		
					$errors [] .="[Voice type: ".implode(',',$id_used_forum)."]". JText::_('VOICE_TYPE_IS_BEING_USED_BY_FORUMS' );
				}
				if(count($id_used_items)>0){		
					$errors [] .="[Voice type: ".implode(',',$id_used_items)."]". JText::_('VOICE_TYPE_IS_BEING_USED_BY_ITEMS' );
				}	
			}
			if(count($is_fail)>0){
				$errors[]= "[ID: ".implode(',',$is_fail)."]".JText::_('FAILURE_TO_DELETE_VOICE_TYPE');
			}
		}
		return $errors;
	}
}

?>
