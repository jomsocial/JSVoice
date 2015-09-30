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

class JAVoiceModelforums extends JAVBModel {
	var $_db = null;
	var $_table = null;
	
	function __construct() {
		parent::__construct ();
	}
	/**
	 * @return JTable Configuration table object
	 */
	
	function _getTable() {
		$this->_table = JTable::getInstance ( 'forums', 'Table' );
	}
	function getTotal($where_more = '', $joins = '') {
		$db = JFactory::getDBO ();
		
		$query = "SELECT count(f.id) FROM #__jav_forums as f" . "\n  $joins" . "\n WHERE 1=1 $where_more";
		$db->setQuery ( $query );
		return $db->loadResult ();
	}
	
	function getItems($where_more = '', $limit = 0, $limitstart = 0, $order = '', $fields = '', $joins = '') {
		$db = JFactory::getDBO ();
		
		if (! $order) {
			$order = ' f.ordering';
		}
		
		if ($fields)
			$fields = "f.*,$fields ";
		else
			$fields = 'f.*';
		
		if (! $limit)
			$limit = 100;
		
		$sql = "SELECT $fields 
				\n FROM #__jav_forums as f
				\n $joins
				\n WHERE 1=1 $where_more
				\n ORDER BY $order
				\n LIMIT $limitstart, $limit";
		
		$db->setQuery ( $sql );//echo $db->getQuery();
		$layout = JRequest::getVar ( 'layout', '' );
		
		if($layout == "add" || $layout == "form"){
			$forum = array ();		
			$forum [0] = new stdClass ( );
			$forum [0]->id = 0;
			$forum [0]->title = "-".JText::_("SELECT_ONE")."-";			
			$forum = array_merge_recursive($forum, $db->loadObjectList ());					
		}else{
			$forum = $db->loadObjectList ();	
		}		
		
		//print_r($forum);die(); 
		return $forum;
	}
	function getDyamicItems($where_more = '',  $limitstart = 0, $limit = 0, $order = '', $fields = '', $joins = '') {
		$db = JFactory::getDBO ();
		
		if (! $order) {
			$order = ' f.ordering';
		}
		
		if ($fields)
			$fields = "$fields ";
		else
			$fields = 'f.*';
		
		if (! $limit)
			$limit = 100;
		
		$sql = "SELECT $fields " . "\n FROM #__jav_forums as f " . "\n $joins" . "\n WHERE 1=1 $where_more" . "\n ORDER BY $order " . "\n LIMIT $limitstart, $limit";

		$db->setQuery ( $sql ); //echo $db->getQuery ( $sql ), '<br>';
		
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			return $db->loadColumn();
		}else{
			return $db->loadResultArray();
		}
	}	
	function getItem($cid = array(0)) {
		
		$edit = JRequest::getVar ( 'edit', true );
		if (! $cid || @! $cid [0]) {
			$cid = JRequest::getVar ( 'cid', array (0 ), '', 'array' );
		
		}
		$this->_getTable ();
		JArrayHelper::toInteger ( $cid, array (0 ) );
		if ($edit) {
			$this->_table->load ( $cid [0] );
		}
		
		return $this->_table;
	}
	
	function _getVars() {	
		$mainframe = JFactory::getApplication();
		$option = 'forums';
		
		$list = array ();
		$list ['filter_order'] = $mainframe->getUserStateFromRequest ( $option . '.filter_order', 'filter_order', 'f.ordering', 'cmd' );
		
		$list ['filter_order_Dir'] = $mainframe->getUserStateFromRequest ( $option . '.filter_order_Dir', 'filter_order_Dir', '', 'word' );
		
		$list ['limit'] = $mainframe->getUserStateFromRequest ( $option . 'list_limit', 'limit', $mainframe->getCfg ( 'list_limit' ), 'int' );
		
		$list ['limitstart'] = $mainframe->getUserStateFromRequest ( $option . '.limitstart', 'limitstart', 0, 'int' );
		
		$list ['search'] = $mainframe->getUserStateFromRequest ( $option . '.search', 'search', '', 'string' );
		
		$list ['search'] = strtolower ( $list ['search'] );
		
		return $list;
	}
	
	function getOrdering($item) {
		$query = 'SELECT ordering AS value, title AS text' . ' FROM #__jav_forums' . ' ORDER BY ordering';
		if(version_compare(JVERSION, '3.0', 'ge')){
			$neworder = 0;	
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
		}else{
			return JHTML::_( 'list.specificordering', $item, $item->id, $query );
		}
	}
	
	function published($publish) {

		$db = JFactory::getDBO ();
		
		$ids = JRequest::getVar ( 'cid', array () );
		$ids = implode ( ',', $ids );
		
			$query = "UPDATE #__jav_forums" . 

			" SET published = " . intval ( $publish ) . 

			" WHERE id IN ( $ids )";
			
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
		$row = $this->getTable ( 'forums','Table' );

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
		$db = JFactory::getDBO ();
		$cids = JRequest::getVar ( 'cid', null, 'request', 'array' );
		$count = count ( $cids );
		$errors = array ();
		$id_used = array ();
		$is_fail = array ();
		if ($count > 0) {
			$model_items = JAVBModel::getInstance ( 'items', 'javoiceModel' );
			foreach ( $cids as $cid ) {
				$total_item = $model_items->getTotal ( " AND i.forums_id = " . ( int ) $cid );
				if ($total_item > 0) {
					$id_used [] = $cid;
				} else {
					$query = "DELETE FROM #__jav_forums WHERE id=$cid";
					$db->setQuery ( $query );
					if (! $db->query ()) {
						$is_fail [] = $cid;
					}
				}
			}
			if (count ( $id_used ) > 0) {
				$errors [] = "[ID: " . implode ( ',', $id_used ) . "]" . JText::_('FORUM_IS_BEING_USED_BY_ITEMS' );
			}
			if (count ( $is_fail ) > 0) {
				$errors [] = "[ID: " . implode ( ',', $is_fail ) . "]" . JText::_('FAILURE_TO_DELETE_FORUM' );
			}
		}
		return $errors;
	
	}
	
	function parseVoiceTypes($forums_id, &$voicetypes) {
		
		$count = count ( $voicetypes );
		
		if ($count > 0) {
			
			for($i = 0; $i < $count; $i ++) {
				
				$forums_status = $this->getForumStatus ( $forums_id, $voicetypes [$i]->id );
				
				$voicetypes [$i]->voice_type_selected = 0;
				
				$voicetypes [$i]->voice_type_status_id = 0;
				
				if (isset ( $forums_status ) && count ( $forums_status ) > 0) {
					
					$voicetypes [$i]->voice_type_status_id = $forums_status [0]->voice_type_status_id;
					
					$voicetypes [$i]->voice_type_selected = $forums_status [0]->voice_types_id > 0 ? 1 : 0;
				
				}
			}
		}
	
	}
	function parseVoiceTypePostBack($post, &$voicetypes) {
		
		$count = count ( $voicetypes );
		
		if ($count > 0) {
			
			for($i = 0; $i < $count; $i ++) {
				
				$voicetypes [$i]->voice_type_selected = isset ( $post ['voice_type'] [$voicetypes [$i]->id] ) ? 1 : 0;
				
				$voicetypes [$i]->voice_type_status_id = isset ( $post ['voice_type_status'] [$voicetypes [$i]->id] ) ? $post ['voice_type_status'] [$voicetypes [$i]->id] : 0;
			
			}
		}
	}
	
	function parseGroupUser(&$gtree, $atts = 0) {
		if ($atts) {
			$obj = new stdClass ( );
			$obj->value = 0;
			$obj->text = JText::_("GUEST" );
			$obj->disable = 0;
			$array = array (0 => $obj );
			$gtree = array_merge ( $array, $gtree );
		}
		$count = count ( $gtree );
		
		if ($count > 0) {
			
			for($i = 0; $i < $count; $i ++) {
				
				$gt = &$gtree [$i];
				
				$gt->text = $gt->text . "[ $gt->value ]";
			
			}
		}
	}
	
	function saveDefaultStatus($post) {
		$db = JFactory::getDBO ();
		if (isset ( $post ['voice_type'] )) {
			$voice_types = $post ['voice_type'];
			
			$voice_types_status = $post ['voice_type_status'];
			
			$forums_id = $post ['id'];
			
			$lists = array ();
			
			if (count ( $voice_types ) > 0) {
				
				foreach ( $voice_types as $voice_type ) {
					$obj = new stdClass ( );
					$obj_old = $this->getForumStatus ( $forums_id, $voice_type );
					if ($obj_old) {
						$obj = $obj_old [0];
					}
					$obj->forums_id = $forums_id;
					
					$obj->voice_types_id = $voice_type;
					if (! isset ( $voice_types_status [$voice_type] )) {
						return FALSE;
					}
					$obj->voice_type_status_id = $voice_types_status [$voice_type];
					
					$lists [] = $obj;
				}
			}
			
			if ($this->deleteForumStatus ( $forums_id )) {
				
				if (count ( $lists ) > 0) {
					
					foreach ( $lists as $item ) {
						
						if (! $db->insertObject ( '#__jav_forums_has_voice_types', $item )) {
							return FALSE;
						}
					}
				}
			
			} else {
				
				return FALSE;
			}
		} else {
			return FALSE;
		}
		return TRUE;
	}
	
	function getForumStatus($forums_id, $voice_types_id = null) {
		$db = JFactory::getDBO ();
		
		$query = "	SELECT * FROM #__jav_forums_has_voice_types
		
					WHERE   forums_id=$forums_id ";
		
		if (isset ( $voice_types_id ))
			$query .= " AND voice_types_id=$voice_types_id ";
		
		$db->setQuery ( $query );
		
		return $db->loadObjectList ();
	
	}
	
	function deleteForumStatus($forums_id) {
		$db = JFactory::getDBO ();
		
		$query = "	DELETE FROM #__jav_forums_has_voice_types
		
					WHERE   forums_id=$forums_id";
		
		$db->setQuery ( $query );
		
		return $db->query ();
	
	}
	
	function getItemsbyType($type_id) {
		if (! intval ( $type_id ))
			return 0;
		$db = JFactory::getDBO ();
		
		$sql = "SELECT * FROM #__jav_forums_has_voice_types as ft " . "\n WHERE ft.voice_types_id=" . ( int ) $type_id;
		
		$db->setQuery ( $sql ); //print_r($db->getQuery ( $sql ));exit;
		$items = $db->loadObjectList ();
		
		return $items;
	}
	
	function getForumByPermission() {
		$mainframe = JFactory::getApplication();
		$user = JFactory::getUser ();
		$where = '';
		if (! $mainframe->isAdmin ())
			$where = ' and f.published=1 ';
		$forums = $this->getItems ( $where );
		$tem = array ();
		if ($forums) {
			foreach ( $forums as $f ) {
				if(isset($f->gids_view)){
					$permission_view = explode ( "\n", $f->gids_view );
					if (!in_array ( 0, $permission_view )) {
						foreach($user->getAuthorisedViewLevels() as $gkey=>$gVal){
							if (in_array ( $gVal, $permission_view )) {
								$tem [] = $f->id;
								break;
							}					
						}									
					}else{
						$tem [] = $f->id;						
					}				
				}
			}
		}		
		return $tem;
	}
	
	function getForumByPermissionVote() {
		$mainframe = JFactory::getApplication();
		$user = JFactory::getUser ();
		$where = '';
		if (! $mainframe->isAdmin ())
			$where = ' and f.published=1 ';
		$forums = $this->getItems ( $where );
		$tem = array ();
		if ($forums) {
			foreach ( $forums as $f ) {
				if(isset($f->gids_post)){
					$permission_vote = explode ( "\n", $f->gids_post );
					$levels = $user->getAuthorisedViewLevels();
					$levels[] = 0;
					foreach($levels as $gkey=>$gVal){
						if (in_array ( $gVal, $permission_vote )) {
							$tem [] = $f->id;
							break;
						}					
					}
				}
			}
		}
		return $tem;
	}
}

?>