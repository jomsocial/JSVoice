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

defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport ( 'joomla.application.component.model' );

class JAVoiceModelItems extends JAVBModel {
	
	var $_pagination = NULL;
	var $_total = 0;
	var $_arrayUser = array();
	function __construct()
	{
		$this->_total = 0;
		parent::__construct();
		
	}	
	
	
	function getItems($where_more = '', $order = '', $limitstart = 0, $limit = 20, $fields = '', $joins = '') {		
		$db = JFactory::getDBO ();
		global $javconfig;
		$items = array ();
		require_once JPATH_SITE . DS . 'components' . DS . 'com_javoice' . DS . 'models' . DS . 'forums.php';
		$model_forums = new JAVoiceModelforums ( );
		$fids = '0';
		$tem = $model_forums->getForumByPermission ();
			
		if (! JRequest::getInt ( 'forums' )) {
			if ($tem)
				$fids = implode ( ',', $tem );
			if(JRequest::getCmd("view") != "feeds")	
				$where_more .= " and i.forums_id in ($fids)";
		} elseif (! in_array ( JRequest::getInt ( 'forums' ), $tem )) {
			return $items;
		}
		
		if (! $order) {
			$order = ' i.total_vote_up DESC';
		}
		
		if ($fields)
			$fields = "DISTINCT i.*, $fields ";
		else
			$fields = 'DISTINCT i.*';
		
		$where_key = '';
		$key = JRequest::getVar ( 'key' );
		
		if ($key) {
			$exactSearch = explode("\"", $key);
			$searchTag = '';
			//use tag search
			if($javconfig["systems"]->get("is_enable_tagging", 0)){											
				if(count($exactSearch) >=3){
					$where_tag = " and(";					
					foreach ($exactSearch as $search){																											
						if($search && $search!=" "){
							
							$search = $db->Quote ( $search . '%' );
							if($where_tag == " and("){												
								$where_tag .= "name LIKE " . $search;
							}else{
								$where_tag .= " OR name LIKE " . $search;
							}													
						}						
					}
					$where_tag .= ")";					
				}else{
					
					$search = $db->Quote ( '%'. $key . '%' );
					$where_tag = "AND name LIKE " . $search;					
				}
				$queryTag = "SELECT id FROM #__jav_tags WHERE published=1 ".$where_tag;
				$db->setQuery($queryTag);
				
				
				
				if (version_compare(JVERSION, '3.0', 'ge'))
				{
					$listTag = $db->loadColumn();
				}else{
					$listTag = $db->loadResultArray();
				}
				
				$strListTag = implode(",",$listTag);
				if (empty($strListTag))
					$strListTag = 0;
				
				$queryTag = "SELECT i.id FROM #__jav_items as i INNER JOIN #__jav_tags_voice as tv ON i.id=tv.voiceID WHERE tv.tagID in ($strListTag)";
				$db->setQuery($queryTag);
				
				if (version_compare(JVERSION, '3.0', 'ge'))
				{
					$listTag = $db->loadColumn();
				}else{
					$listTag = $db->loadResultArray();
				}
				if(count($listTag) >= 1) {
					$searchTag = " OR i.id in ($strListTag)";
				}
				//$fields = " DISTINCT i.id,".$fields;
			}
			if(count($exactSearch) >=3){
				$where_exct_key .= " and(";				
				foreach ($exactSearch as $search){					
					//if($search)
					if($search && $search!=" "){
						$search = $db->Quote ( $search . '%' );
						if($where_exct_key == " and("){												
							$where_exct_key .= "i.title LIKE " . $search . " OR i.content LIKE " . $search;
						}else{
							$where_exct_key .= " OR i.title LIKE " . $search . " OR i.content LIKE " . $search;
						}													
					}		
				}
				$where_key = $where_exct_key .$searchTag.")";
			}else{
				if (strpos ( $key, '+' ) !== FALSE || strpos ( $key, '-' ) !== FALSE || strpos ( $key, '*' ) !== FALSE || strpos ( $key, '>' ) !== FALSE || strpos ( $key, '<' ) !== FALSE || strpos ( $key, '"' ) !== FALSE) {
					$key = $db->Quote ( $key . '*' );
				} else {
					$key = $db->Quote ( $key . '*' );
				}
				if($searchTag){					
					$where_key = " and (MATCH (i.title, i.content) AGAINST (" . $key . " IN BOOLEAN MODE)".$searchTag.")";
				}else{
					$where_key = " and MATCH (i.title, i.content) AGAINST (" . $key . " IN BOOLEAN MODE)";
				}	
			}			
		}
		
		$this->_total = $this->getTotal ( $where_more . ' ' . $where_key, $joins );
			
		$sql = "SELECT $fields  " . "\n FROM #__jav_items as i " . "\n  $joins" . "\n WHERE 1 $where_more $where_key" . "\n ORDER BY $order"; 
		if($limit){
			$sql .= "\n LIMIT $limitstart, $limit";
		}
		
		$db->setQuery ( $sql );
		//echo($db->getQuery ());exit;		
		$items = $db->loadObjectList ();
		return $items;
	}
	
	function getCurrentTotal(){
		return $this->_total;
	}
		
	function getTotal($where_more = '', $joins = '') {
		global $javconfig;
		$db = JFactory::getDBO ();
		require_once JPATH_SITE . DS . 'components' . DS . 'com_javoice' . DS . 'models' . DS . 'forums.php';
		$model_forums = new JAVoiceModelforums ( );
		$fids = '0';
		$tem = $model_forums->getForumByPermission ();
		
		$forum_id = JRequest::getInt ( 'forums' );
		if (! JRequest::getInt ( 'forums' )) {
			if ($tem)
				$fids = implode ( ',', $tem );
			$where_more .= " and i.forums_id in ($fids)";
		}else{
			$where_more .= " AND i.forums_id = '$forum_id'";
		}
		
		$order = JRequest::getVar('order');
		
		if($order == 'create_date desc'){
			if($javconfig["systems"]->get('is_set_time_new_voice', 1)){
				$lagNewVoice = $javconfig["systems"]->get('time_for_new_voice', 7200); 					
				$where_more .= ' and (i.create_date +'.$lagNewVoice.') >='.time();
			}else{
				$where_more .= ' and i.create_date>=' . $_SESSION ['JAV_LAST_VISITED'];
			}
		}
		$status = JRequest::getInt('status');
		
		if($status){
			$where_more .= " AND i.voice_type_status_id = '$status'";
		}
	
		$where_key = '';
		$key = JRequest::getVar ( 'key' );
		
		if ($key) {
			$exactSearch = explode("\"", $key);
			$searchTag = '';
			//use tag search
			if($javconfig["systems"]->get("is_enable_tagging", 0)){											
				if(count($exactSearch) >=3){
					$where_tag = " and(";					
					foreach ($exactSearch as $search){																											
						if($search && $search!=" "){
							
							$search = $db->Quote ( $search . '%' );
							if($where_tag == " and("){												
								$where_tag .= "name LIKE " . $search;
							}else{
								$where_tag .= " OR name LIKE " . $search;
							}													
						}						
					}
					$where_tag .= ")";					
				}else{
					
					$search = $db->Quote ( '%'. $key . '%' );
					$where_tag = "AND name LIKE " . $search;					
				}
				$queryTag = "SELECT id FROM #__jav_tags WHERE published=1 ".$where_tag;
				$db->setQuery($queryTag);
				
				
				
				if (version_compare(JVERSION, '3.0', 'ge'))
				{
					$listTag = $db->loadColumn();
				}else{
					$listTag = $db->loadResultArray();
				}
				
				$strListTag = implode(",",$listTag);
				if (empty($strListTag))
					$strListTag = 0;
				
				$queryTag = "SELECT i.id FROM #__jav_items as i INNER JOIN #__jav_tags_voice as tv ON i.id=tv.voiceID WHERE tv.tagID in ($strListTag)";
				$db->setQuery($queryTag);
				
				if (version_compare(JVERSION, '3.0', 'ge'))
				{
					$listTag = $db->loadColumn();
				}else{
					$listTag = $db->loadResultArray();
				}
				if(count($listTag) >= 1) {
					$searchTag = " OR i.id in ($strListTag)";
				}
				//$fields = " DISTINCT i.id,".$fields;
			}
			if(count($exactSearch) >=3){
				$where_exct_key .= " and(";				
				foreach ($exactSearch as $search){					
					//if($search)
					if($search && $search!=" "){
						$search = $db->Quote ( $search . '%' );
						if($where_exct_key == " and("){												
							$where_exct_key .= "i.title LIKE " . $search . " OR i.content LIKE " . $search;
						}else{
							$where_exct_key .= " OR i.title LIKE " . $search . " OR i.content LIKE " . $search;
						}													
					}		
				}
				$where_key = $where_exct_key .$searchTag.")";
			}else{
				if (strpos ( $key, '+' ) !== FALSE || strpos ( $key, '-' ) !== FALSE || strpos ( $key, '*' ) !== FALSE || strpos ( $key, '>' ) !== FALSE || strpos ( $key, '<' ) !== FALSE || strpos ( $key, '"' ) !== FALSE) {
					$key = $db->Quote ( $key . '*' );
				} else {
					$key = $db->Quote ( $key . '*' );
				}
				if($searchTag){					
					$where_key = " and (MATCH (i.title, i.content) AGAINST (" . $key . " IN BOOLEAN MODE)".$searchTag.")";
				}else{
					$where_key = " and MATCH (i.title, i.content) AGAINST (" . $key . " IN BOOLEAN MODE)";
				}	
			}			
		}
		$where_more .= $where_key;
		
		$query = "SELECT count(i.id) FROM #__jav_items as i" . "\n  $joins" . "\n WHERE 1 $where_more ";
		$db->setQuery ( $query );
		$total = $db->loadResult ();
		return $total;
	}
	
	/**
	 * Get page navigator object
	 * @return 
	 */
	function getPagination($limitstart = 0, $limit = 20, $divId = '') {
		if ($this->_pagination == null) {
			jimport ( 'joomla.html.pagination' );
			require_once (JPATH_COMPONENT_SITE . '/helpers/japagination.php');
		}
		if (! $this->_total) {
			$this->_total = $this->getTotal ( ' and i.voice_types_id=' . JRequest::getInt ( 'type' ) . ' and i.published=1' );
		}
		$this->_pagination = new JAPagination ( $this->_total, $limitstart, $limit, $divId );
		
		return $this->_pagination;
	}
	
	function getWhereClause($lists) {
		//where clause  		
		$where = array ();
		if ($lists ['search']) {
			if (is_numeric ( $lists ['search'] ))
				$where [] = " i.id ='" . $lists ['search'] . "' ";
			else
				$where [] = " i.title LIKE '%" . $lists ['search'] . "%' ";
		}
		if ($lists ['voicetypes'] != 0) {
			$where [] = " t.id={$lists['voicetypes']}";
		}
		if ($lists ['forums'] != 0) {
			$where [] = " f.id={$lists['forums']}";
		}
		if ($lists ['status'] != 0) {
			$where [] = " i.voice_type_status_id={$lists['status']}";
		}
		$where = count ( $where ) ? 'AND ' . implode ( ' AND ', $where ) : '';
		
		return $where;
	}
	function getWhereWidget(&$where_more) {
		$creator = JRequest::getVar ( 'creator', NULL );
		$created_before = JRequest::getVar ( 'created_before', NULL );
		$created_after = JRequest::getVar ( 'created_after', NULL );
		
		if (isset ( $creator ) && $creator != '') {
			$temps = explode ( ",", $creator );
			$statusStr = array ();
			foreach ( $temps as $temp ) {
				$statusStr [] = "u.username LIKE '%$temp%'";
			}
			$where = count ( $statusStr ) ? ' AND ( ' . implode ( ' OR ', $statusStr ) . ")" : '' . ")";
			$userids = $this->getUserID ( $where );
			if ($userids) {
				$where_more .= " AND i.user_id IN(" . implode ( ",", $userids ) . ")";
			}
		}
		if (isset ( $created_before ) && $created_before != '') {
			$created_before = strtotime ( $created_before );
			if (isset ( $created_before ) && is_numeric ( $created_before )) {
				$where_more .= " AND i.create_date >" . ( int ) $created_before;
			}
		}
		if (isset ( $created_after ) && $created_after != '') {
			$created_after = strtotime ( $created_after );
			if (isset ( $created_after ) && is_numeric ( $created_after )) {
				$where_more .= " AND i.create_date <" . ( int ) $created_after;
			}
		}
	
	}
	function getUserID($where) {
		$query = " SELECT u.id FROM #__users as u WHERE 1 $where";
		$db = JFactory::getDBO ();
		$db->setQuery ( $query );
		//return $db->loadResultArray ();
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			return $db->loadColumn();
		}else{
			return $db->loadResultArray();
		}
	}
	
	function isExistUserID($userID){
		$query = " SELECT id FROM #__users as u WHERE id={$userID}";
		$db = JFactory::getDBO ();
		$db->setQuery ( $query );
		$return = $db->loadResult();
		if($return){
			$this->_arrayUser[] = $return;
			return true; 
		}else{
			return false;
		}
	}
	
	function deleteUserIDInVoice($userID){
		$query = "UPDATE `#__jav_items` as v SET v.`user_id` = '0' WHERE v.`user_id` ={$userID}";
		$db = JFactory::getDBO ();
		$db->setQuery ( $query );
		//die($db->getQuery());
		$db->Query ();
	}
	
	function parseItems(&$items) {
		$count = count ( $items );
		if ($count > 0) {
			$modelvoicetypesstatus = JAVBModel::getInstance ( 'voicetypesstatus', 'javoiceModel' );
			for($i = 0; $i < $count; $i ++) {
				$item = &$items [$i];
				$item->voice_type_status_title = '';
				if($item->voice_type_status_id){
					$status = $modelvoicetypesstatus->getItem ( $item->voice_type_status_id );
					$item->voice_type_status_title = $status->title;
				}				
				
				if($item->user_id && !in_array($item->user_id, $this->_arrayUser) ){
					if($this->isExistUserID($item->user_id)){
						$user = JFactory::getUser ( $item->user_id );	
					}else{						
						$this->deleteUserIDInVoice($item->user_id);
					}					
				}
				
				if (isset($user) && $user->username)
					$item->user_name = $user->username;
				elseif (isset ( $item->guest_name ) && $item->guest_name != '') {
					$item->user_name = $item->guest_name;
				} else
					$item->user_name = 'Anonymous';
				$admin_response = $this->getAmin_responseByItem ( $item->id );
				if ($admin_response) {
					foreach ( $admin_response as $temp ) {
						if ($temp->type == 'admin_response')
							$item->rid = $temp->id;
						if ($temp->type == 'best_answer')
							$item->bid = $temp->id;
					}
				}
			}
		}
	}
	function parseItem(&$item) {
		$cid = array ();
		$model_voicetype = JAVBModel::getInstance ( 'voicetypes', 'javoiceModel' );
		$voice_type = $model_voicetype->getItem ( $item->voice_types_id );
		$item->ttitle = $voice_type ? $voice_type->title : '';
		$model_forum = JAVBModel::getInstance ( 'forums', 'javoiceModel' );
		$cid = array ();
		$cid [0] = $item->forums_id;
		$forum = $model_forum->getItem ( $cid );
		$item->ftitle = $forum ? $forum->title : '';
		$model_voicetypesstatus = JAVBModel::getInstance ( 'voicetypesstatus', 'javoiceModel' );
		$voicetypesstatus = $model_voicetypesstatus->getItem ( $item->voice_type_status_id );
		$item->stitle = $voicetypesstatus ? $voicetypesstatus->title : '';
		$item->class_css = $voicetypesstatus->class_css;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $cid
	 * @return unknown
	 */
	function getItem($cid = array(0)) {
		
		$table = $this->getTable ( 'items', 'Table' );
		// Load the current item if it has been defined
		if (! $cid || @! $cid [0]) {
			$cid = JRequest::getVar ( 'cid', array (0 ), '', 'array' );
			JArrayHelper::toInteger ( $cid, array (0 ) );
		}
		
		if ($cid [0]) {
			$table->load ( $cid [0] );
		}
		
		return $table;
		
	}
	
	function getForums($where_more = '', $order = '', $join = '') {
		$db = JFactory::getDBO ();
		
		$list = array ();
		
		if (! $order) {
			$order = ' f.ordering';
		}
		
		$sql = "SELECT f.* FROM #__jav_forums as f " . $join . "\n WHERE 1=1 $where_more" . "\n ORDER BY $order";
		$db->setQuery ( $sql );
		$list = $db->loadObjectList ();
		
		return $list;
	}
	
	function getVoiceTypes($where_more = ' and vt.published=1', $order = '') {
		$db = JFactory::getDBO ();
		
		$types = array ();
		
		if (! $order) {
			$order = ' vt.ordering';
		}
		
		$sql = "SELECT vt.* FROM #__jav_voice_types as vt " . "\n WHERE 1=1 $where_more" . "\n ORDER BY $order";
		$db->setQuery ( $sql );
		$types = $db->loadObjectList ();
		
		if ($types) {
			foreach ( $types as $k => $type ) {
				$sql = "SELECT count(*) ".
						"\n FROM #__jav_forums_has_voice_types as vt" . 
						"\n INNER JOIN #__jav_forums as f on f.id=vt.forums_id" . 
						"\n WHERE 1=1 and vt.voice_types_id='{$type->id}' and f.published=1";
				$db->setQuery ( $sql );
				$types [$k]->total = $db->loadResult ();
			}
		}
		
		return $types;
	}
	
	function getVoiceType($id) {
		static $type;
		if ($type)
			return $type;
		
		$db = JFactory::getDBO ();
		
		$sql = "SELECT * FROM #__jav_voice_types " . "\n WHERE id='$id'";
		$db->setQuery ( $sql );
		$type = $db->loadObject ();
		
		return $type;
	}
	
	function _getVars() {
		static $lists;
		if ($lists)
			return $lists;
		
		global $javconfig;
		
		$lists = array ();
		$lists ['order'] = JRequest::getString ( 'order', 'i.total_vote_up desc' );
		$lists ['order_Dir'] = JRequest::getCmd ( 'order_Dir', '' );
		$lists ['limit'] = JRequest::getInt ( 'limit', $javconfig ['systems']->get ( 'display_num', 20 ) );
		$lists ['limitstart'] = JRequest::getInt ( 'limitstart', 0 );
		
		$lists ['search'] = JRequest::getString ( 'search', '' );
		$lists ['forums'] = JRequest::getInt ( 'forums', '0' );
		$lists ['voicetypes'] = JRequest::getInt ( 'voicetypes', '0' );
		return $lists;
	}
	function _getVars_admin() {
		global $javconfig;
		$mainframe = JFactory::getApplication();
		$option = 'items';
		$lists = array ();
		$lists ['filter_order'] = $mainframe->getUserStateFromRequest ( $option . '.filter_order', 'filter_order', 'i.id', 'string' );
		$lists ['filter_order_Dir'] = $mainframe->getUserStateFromRequest ( $option . '.filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
		$lists ['limit'] = $mainframe->getUserStateFromRequest ( $option . 'limit', 'limit', $javconfig ['systems']->get ( 'number_items', 20 ), 'int' );
		$lists ['limitstart'] = $mainframe->getUserStateFromRequest ( $option . '.limitstart', 'limitstart', 0, 'int' );
		$lists ['search'] = $mainframe->getUserStateFromRequest ( $option . '.search', 'search', '', 'string' );
		$lists ['forums'] = $mainframe->getUserStateFromRequest ( $option . '.forums', 'forums', '0', 'int' );
		$lists ['voicetypes'] = $mainframe->getUserStateFromRequest ( $option . '.voicetypes', 'voicetypes', '0', 'int' );
		$lists ['status'] = JRequest::getVar('status');
		return $lists;
	}
	
	function store() {
		global $javconfig;
		
		$row = $this->getItem ();
		$old_status_id = $row->voice_type_status_id;
		$post = $this->getState ( 'request' );
		$user = JFactory::getUser ();
		
		if (isset ( $post ['create_date'] )) {
			$time = strtotime ( trim ( $post ['create_date'] ) );
			if (is_numeric ( $time ))
				$post ['create_date'] = $time;
		} else {
			$post ['create_date'] = $row->create_date ? $row->create_date : time ();
		}
		$post ['update_date'] = time ();
		
		if (! $row->id) {
			if (! isset ( $post ['voice_type_status_id'] )) {
				$post ['voice_type_status_id'] = $this->get_status_default ( $post ['forums_id'], $post ['voice_types_id'] );
			}
			
			if ($user->id) {
				$post ['user_id'] = $user->id;
			}
			
			if (! isset ( $post ['published'] )) {
				$post ['published'] = abs ( $javconfig ['systems']->get ( 'item_needs_approved', 1 ) - 1 );
			}
		}
		
		if (! $row->bind ( $post )) {
			return $row->getError ();
		}
		
		if (($erros = $row->check ())) { //print_r($erros);exit;
			return implode ( "<br/>", $erros );
		}
		jimport ( 'joomla.filesystem.file' );		
		
		/* If new status is spam or close, clear number spam*/
		$model_status = JAVBModel::getInstance ( 'voicetypesstatus', 'javoiceModel' ); 
		$type = $this->getVoiceType ( $row->voice_types_id );
		$status = $model_status->getItem ( $row->voice_type_status_id );
		
		$parent_status = $model_status->getItem ( $status->parent_id );
		//use field return vote or not
		$spamorclosed = 0;
		if($status->return_vote == -1){
			if($parent_status->return_vote) $spamorclosed = $parent_status->return_vote;
		}else{
			$spamorclosed = $status->return_vote;
		}
		
		if($status && $parent_status){
			if(!$status->allow_show) 	$row->number_spam = 0;
			elseif(!$status->allow_voting)	$row->number_spam = 0;
			elseif ($status->allow_show == - 1 && $parent_status->allow_show == 0)	$row->number_spam = 0;
			elseif ($status->allow_voting == - 1 && $parent_status->allow_voting == 0) 	$row->number_spam = 0;
		}
		
//		if ($spamorclosed) {
//			$row->number_spam = 0;
//		}
		
		if (! $row->store ()) {
			return $row->getError ();
		}
		
		/* Add JomSocial:: activity Stream*/		
		if ($row->user_id) {						
			$cid = JRequest::getVar ( 'cid', array (), '', 'array' );
			$type->title = strtolower ( $type->title );
			if (! $cid) {
				$user_id = $row->user_id;
				$action = 'add';
				$title = sprintf(JText::_('JOMSOCIAL_ACTIVITI_STREAM_TITLE_NEW_ITEM'), $type->title, $row->id, $row->voice_types_id, $row->forums_id, $row->title);
			} else {
				$user_id = $user->id;
				$action = 'update';
				$title = sprintf(JText::_('JOMSOCIAL_ACTIVITI_STREAM_TITLE_UPDATED_ITEM'), $type->title, $row->id, $row->voice_types_id, $row->forums_id, $row->title);				
			}
			JAVoiceHelpers::JomSocial_addActivityStream($user_id, $title, $row->id, $action);
		}
		/* End*/
				
		
		//if ($old_status_id != $row->voice_type_status_id && ($old_status_id || $row->voice_type_status_id)) {
			$model_voicetypesstatus = JAVBModel::getInstance ( 'voicetypesstatus', 'javoiceModel' );
			$model_voicetypesstatus->changeStatus ( $old_status_id, $row );
			
			if ( ($type->total_votes > - 1) && $spamorclosed) {
				$this->voices_back($row);
			}
			
		//}
		
		return $row;
	}
	
	function voices_back($item){		
		$logs = $this->getLogs ( " and item_id={$item->id}" );
		if ($logs) {
			
			foreach ( $logs as $log ) {
				//$user = JFactory::getUser ( $log->user_id );
				$user_no_session = new JUser($log->user_id);
				$total_voted = ( int ) $user_no_session->getParam ( 'total-voted-' . $item->voice_types_id );
				$total_voted = (int)($total_voted - abs ( $log->votes ));
				if($total_voted<0) $total_voted = 0;
				$user_no_session->setParam ( 'total-voted-' . $item->voice_types_id,  $total_voted);
				$user_no_session->save ();
			}
			$this->clearLogs ( " and item_id={$item->id}" );
		}
	}
	
	function get_status_default($forums_id, $type_id) {
		if (! $forums_id || ! $type_id)
			return 0;
		
		$db = JFactory::getDBO ();
		
		$sql = "SELECT voice_type_status_id FROM #__jav_forums_has_voice_types " . "\n WHERE forums_id=" . $forums_id . " and voice_types_id=" . $type_id;
		$db->setQuery ( $sql );
		return $db->loadResult ();
	}
	
	function vote($item_id, $votes = 0) {
		//Thieu vu Send mail khi user vote
		$db = JFactory::getDBO ();
		
		$user = JFactory::getUser ();
		$row = $this->getItem ( array ($item_id ) );
		
		$update = array ();
		
		$old_votes = NULL;
		
		$log = $this->getLog ( $item_id, $user->id );
		if (isset ( $log->votes )) {
			$old_votes = ( int ) $log->votes;
		}
		elseif ($user->id) {
			/* Add JomSocial:: activity Stream*/
			$title = sprintf(JText::_('JOMSOCIAL_ACTIVITI_STREAM_TITLE_VOTE'), $votes, $row->id, $row->voice_types_id, $row->forums_id, $row->title);
			JAVoiceHelpers::JomSocial_addActivityStream($user->id, $title, $row->id, 'vote');
			/* End*/
		}
		//get vote left of user with voice type
		$votes_left = $this->getVotes_left ( $user->id );
		
		if ($row) {
			//get info voice type
			$type = $this->getVoiceType ( $row->voice_types_id );
			
			if ($type->total_votes >= 0) {
				//Total_Voted = 10, $votes				
				$total_voted_new = $this->getTotal_Voted ( $user->id ) + (abs ( $votes ) - abs ( $old_votes ));
				if ((abs ( $votes ) - abs ( $old_votes )) > $votes_left) {
					$total_voted_new = $votes_left;
					$votes = $votes_left - $old_votes;
					JRequest::setVar ( 'votes', $votes );
				}
				if ((abs ( $votes ) - abs ( $old_votes )) > $votes_left)
					return;
			}
			
			if (is_numeric ( $old_votes )) {
				/* Update total votes up, votes down, .. */
				if ($votes > 0) {
					if ($old_votes > 0) {
						$update [] = " total_vote_up=" . (( int ) $row->total_vote_up + ($votes - $old_votes));
					} elseif ($old_votes < 0) {
						$update [] = " total_vote_up=" . (( int ) $row->total_vote_up + $votes);
						$update [] = " number_vote_up=" . (( int ) $row->number_vote_up + 1);
						
						$update [] = " total_vote_down=" . (( int ) $row->total_vote_down - abs ( $old_votes ));
						$update [] = " number_vote_down=" . (( int ) $row->number_vote_down - 1);
					} else { //$old_votes=0
					//$update [] = " number_vote_neutral=" . (( int ) $row->number_vote_neutral - 1);
					//$update [] = " total_vote_up=" . (( int ) $row->total_vote_up + $votes);
					//$update [] = " number_vote_up=" . (( int ) $row->number_vote_up + 1);
					}
				} elseif ($votes < 0) {
					if ($old_votes < 0) {
						$update [] = " total_vote_down=" . (( int ) $row->total_vote_down + (abs ( $votes ) - abs ( $old_votes )));
					} elseif ($old_votes > 0) {
						$update [] = " total_vote_up=" . (( int ) $row->total_vote_up - $old_votes);
						$update [] = " number_vote_up=" . (( int ) $row->number_vote_up - 1);
						
						$update [] = " total_vote_down=" . (( int ) $row->total_vote_down + abs ( $votes ));
						$update [] = " number_vote_down=" . (( int ) $row->number_vote_down + 1);
					} else { //$old_votes=0
					//$update [] = " number_vote_neutral=" . (( int ) $row->number_vote_neutral - 1);
					//$update [] = " total_vote_down=" . (( int ) $row->total_vote_down + abs ( $votes ));
					//$update [] = " number_vote_down=" . (( int ) $row->number_vote_down + 1);
					}
				
				}
				
			/*else { //$votes = 0
					if ($old_votes > 0) {
						$update [] = " total_vote_up=" . (( int ) $row->total_vote_up - $old_votes);
						$update [] = " number_vote_up=" . (( int ) $row->number_vote_up - 1);
						$update [] = " number_vote_neutral=" . (( int ) $row->number_vote_neutral + 1);
					} elseif ($old_votes < 0) {
						$update [] = " total_vote_down=" . (( int ) $row->total_vote_down - abs ( $old_votes ));
						$update [] = " number_vote_down=" . (( int ) $row->number_vote_down - 1);
						$update [] = " number_vote_neutral=" . (( int ) $row->number_vote_neutral + 1);
					} else { //$old_votes=0
					// do nothing
					}					
				}*/
			} else { // first time vote
				

				if ($votes > 0) {
					$update [] = " total_vote_up=" . (( int ) $row->total_vote_up + $votes);
					$update [] = " number_vote_up=" . (( int ) $row->number_vote_up + 1);
				} elseif ($votes < 0) {
					$update [] = " total_vote_down=" . (( int ) $row->total_vote_down + abs ( $votes ));
					$update [] = " number_vote_down=" . (( int ) $row->number_vote_down + 1);
				} else { //$old_votes=0
				//$update [] = " number_vote_neutral=" . (( int ) $row->number_vote_neutral + 1);
				}
			
			}
		}
		//print_r($update);exit;
		if ($update) {
			$sql = "UPDATE #__jav_items SET " . implode ( ', ', $update ) . "\n WHERE id='$item_id'";
			$db->setQuery ( $sql ); //print_r($db->getQuery ( $sql ));exit;
			$db->query () or die ( 'JAV_Error' );
		
		}
		/* Set Log */
		if ($log) {
			$this->setLog ( $item_id, $votes );
		} else {
			$this->setLog ( $item_id, $votes, true );
		}
		
		if ($row && $type->total_votes >= 0) {
			/* Update total votes left for user */
			$this->setTotal_Voted ( $total_voted_new );
		}
		
		return;
	}
	
	function getVotes_left($user_id = null) {
		$type_id = JRequest::getInt ( 'type' );
		$type = $this->getVoiceType ( $type_id );
		
		$type_votes_left = $type->total_votes;
		if ($type_votes_left == - 1)
			return $type_votes_left;
		
		$user = JFactory::getUser ( $user_id );
		$total_voted = $this->getTotal_Voted ( $user_id );
		
		if ($user_id || $user->id) {
			$votes_left = $type_votes_left - $total_voted;
		} else {
			//$var = md5('total-voted-'.$type_id);
			$votes_left = $type_votes_left - $total_voted;
		}
		
		if (! isset ( $_COOKIE [md5 ( 'jav-first-time-voted' )] ))
			$_SESSION ['first_votes'] = true;
		if($votes_left<0) $votes_left = 0;
		return $votes_left;
	}
	
	function getTotal_Voted($user_id = null) {
		$user = JFactory::getUser ( $user_id );
		
		$type_id = JRequest::getInt ( 'type' );
		
		if ($user_id || $user->id) {
			$user_no_session = new JUser($user->id);
			$total_voted = ( int ) $user_no_session->getParam ( 'total-voted-' . $type_id, 0 );
		} else {
			$var = md5 ( 'total-voted-' . $type_id );
			$total_voted = isset ( $_COOKIE [$var] ) ? ( int ) $_COOKIE [$var] : 0;
		}
		
		return $total_voted;
	}
	function setTotal_Voted($total_voted) {
		$type_id = JRequest::getInt ( 'type' );
		$user = JFactory::getUser ();
		
		if ($user->id) {
			$user->setParam ( 'total-voted-' . $type_id, $total_voted );
			$user->save ();
		} else {
			$var = md5 ( 'total-voted-' . $type_id );
			setcookie ( $var, $total_voted );
			$_COOKIE [$var] = $total_voted;
		}
		
		setcookie ( md5 ( 'jav-first-time-voted' ), 1 );
		$_COOKIE [md5 ( 'jav-first-time-voted' )] = 1;
	
	}
	
	function getLog($item_id, $user_id = 0) {
		if (! $item_id)
			return FALSE;
		$row = new stdClass ( );
		if ($user_id) {
			$db = JFactory::getDBO ();
			$time = time ();
			$sql = "SELECT * FROM #__jav_logs WHERE user_id='{$user_id}' and item_id='{$item_id}' and time_expired>=$time";
			$db->setQuery ( $sql );
			$row = $db->loadObject ();
		
		} else {
			$var = md5 ( 'jav-view-item' ) . $item_id;
			if (isset ( $_COOKIE [$var] )) {
				$row->votes = ( int ) $_COOKIE [$var];
			}
		}
		
		return $row;
	}
	
	public function getLogs($where) {
		$db = JFactory::getDBO ();
		
		$time = time ();
		
		$sql = "SELECT * FROM #__jav_logs WHERE 1 $where and time_expired>=$time";
		$db->setQuery ( $sql ); //echo $db->getQuery ( $sql );exit;
		$rows = $db->loadObjectList ();
		
		return $rows;
	}
	
	function clearLogs($where) {
		$db = JFactory::getDBO ();
		$sql = "DELETE FROM #__jav_logs WHERE 1 $where";
		$db->setQuery ( $sql ); //echo $db->getQuery ( $sql );exit;
		return $db->query ();
	}
	
	function setLog($item_id, $votes, $new = 0) {
		global $javconfig;
		$db = JFactory::getDBO ();
		$user = JFactory::getUser ();
		if ($user->id) {
			if ($new) {
				$ip = $_SERVER ['REMOTE_ADDR'];
				$time_expired = ( int ) $javconfig ['systems']->get ( 'timeline', 1 ) * 60 * 60 * 1000 + time ();
				$sql = "INSERT INTO #__jav_logs " . "\n (`votes`, `item_id`, `user_id`, `time_expired`, `remote_addr`)" . "\n Values ( '{$votes}', '$item_id', '{$user->id}', '$time_expired', '$ip')";
				$db->setQuery ( $sql ); //echo $db->getQuery ( $sql );exit;
				$db->query () or die ( 'JAV_Error' );
			} else {
				$sql = "UPDATE #__jav_logs SET votes='{$votes}'" . "\n WHERE item_id='{$item_id}'";
				$db->setQuery ( $sql );
				$db->query () or die ( 'JAV_Error' );
			}
		} else {
			$var = md5 ( 'jav-view-item' ) . $item_id;
			setcookie ( $var, $votes, ( int ) $javconfig ['systems']->get ( 'timline', 30 ) * 24 * 60 * 60 * 1000 );
			$_COOKIE [$var] = $votes;
		}
	}
	
	function getYourItems($user_id = 0) {
		global $javconfig;
		$db = JFactory::getDBO ();
		
		$time = time ();
		$items = $logIDs = array ();
		if ($user_id) {
			$sql = "SELECT item_id FROM #__jav_logs WHERE user_id='{$user_id}' and time_expired>=$time";
			$db->setQuery ( $sql );
			//$logIDs = $db->loadResultArray ();
			if (version_compare(JVERSION, '3.0', 'ge'))
			{
				$logIDs = $db->loadColumn();
			}else{
				$logIDs = $db->loadResultArray();
			}
		} else {
			$cookies = $_COOKIE;
			if ($cookies) {
				foreach ( $cookies as $k => $value ) {
					if (strpos ( $k, md5 ( 'jav-view-item' ) ) !== FALSE) {
						$logIDs [] = substr ( $k, strlen ( md5 ( 'jav-view-item' ) ) );
					}
				}
			}
		}
		
		if ($logIDs) {
			$logIDs = implode ( ',', $logIDs );
			$where_more = " and i.id in ($logIDs) and i.published=1 ";
			$model_status = JAVBModel::getInstance ( 'voicetypesstatus', 'javoiceModel' );
			
			/* BEGIN: Show items are activing Only and allow voting */
			$model_status = JAVBModel::getInstance ( 'voicetypesstatus', 'javoiceModel' );
			$list_status = $model_status->getListTreeStatus ();
			
			$status_ids = array ();
			foreach ( $list_status as $k => $status ) {
				if ($status->parent_id != 0 && ($status->allow_show == 1 || ($status->allow_show == - 1 && $list_status [$status->parent_id]->allow_show == 1)) && ($status->allow_voting == 1 || ($status->allow_voting == - 1 && $list_status [$status->parent_id]->allow_voting == 1))) {
					$status_ids [] = $status->id;
				}
			}
			if (! $status_ids)
				$status_ids = array (0 );
			$where_more .= " and ( i.voice_type_status_id in (" . implode ( ',', $status_ids ) . ") or i.voice_type_status_id=0)";
			/* END: Show items are activing Only and allow voting*/
			$model_voice_type = JAVBModel::getInstance ( 'voicetypes', 'javoiceModel' );
			$voice_type_id = $model_voice_type->getDyamicItems ( ' AND t.published =1 ', '', 0, 0, 't.id' );
			
			if ($voice_type_id) {
				$voice_type_ids = implode ( ",", $voice_type_id );
				$where_more .= " and i.voice_types_id in (" . $voice_type_ids . ")";
			}
			$model_forum = & JAVBModel::getInstance ( 'forums', 'javoiceModel' );
			$forum_id = $model_forum->getDyamicItems ( ' AND f.published =1 ', 0, 0, '', 'f.id' );
			
			if ($forum_id) {
				$forum_ids = implode ( ",", $forum_id );
				$where_more .= " and i.forums_id in (" . $forum_ids . ")";
			}
			$join = " LEFT JOIN #__jav_voice_type_status as s ON s.id=i.voice_type_status_id";
			$fields_join = ' s.title as status_title, s.class_css as status_class_css, s.allow_voting as status_allow_voting, s.parent_id as status_parent_id, s.published as status_publishded';
			$fields_join .= ', lg.votes';
			$join .= " LEFT JOIN #__jav_logs as lg ON (lg.item_id=i.id and lg.user_id='$user_id')";
			
			$items = $this->getItems ( $where_more, 'i.create_date', 0, $javconfig["plugin"]->get("number_of_your_items",5), $fields_join, $join );
			
			$items = $this->parseItems_params ( $items );
		}
		
		return $items;
	}		
	
	function update_total_items($types_id, $forums_id,$cid=0, $old_forums_id=0, $old_voice_types_id=0) {
		$db = JFactory::getDBO ();
		//if edit a voice
		if($cid && (($types_id!=$old_voice_types_id)||($forums_id!=$old_forums_id))){
			$sql = "UPDATE #__jav_forums_has_voice_types SET total_items=total_items-1" . "\n WHERE voice_types_id='{$old_voice_types_id}' and forums_id='{$old_forums_id}'";
			$db->setQuery ( $sql ); //echo $db->getQuery ( $sql );
			$db->query ();
			
			$sql = "UPDATE #__jav_forums_has_voice_types SET total_items=total_items+1" . "\n WHERE voice_types_id='{$types_id}' and forums_id='{$forums_id}'";
			$db->setQuery ( $sql ); //echo $db->getQuery ( $sql );exit;
			$db->query ();			
		}
		//if add new a voice
		else if(!$cid){
			$sql = "UPDATE #__jav_forums_has_voice_types SET total_items=total_items+1" . "\n WHERE voice_types_id='{$types_id}' and forums_id='{$forums_id}'";
			$db->setQuery ( $sql ); //echo $db->getQuery ( $sql );exit;
			$db->query ();	
		}
								
		return true;
	}
	
	function spam($item_id, $mode = 'spam', $value = 1) {
		
		$db = JFactory::getDBO ();
		$sql = "UPDATE #__jav_items SET number_$mode=number_$mode+($value)" . "\n WHERE id='$item_id'";
		$db->setQuery ( $sql );
		$db->query ();
		//print_r($db);exit;
	}
	
	function parseItems_params($items, $type = null) {
		global $javconfig;
		//print_r($items);exit;
		$db = JFactory::getDBO ();
		
		$helper = new JAVoiceHelpers ( );
		$Itemid = JRequest::getInt ( 'Itemid' );
		
		if ($type) {
			$types [0] = $type;
		} else {
			$types = $this->getVoiceTypes ();
		
		}
		
		$array_votes = array ();
		
		if ($types) {
			foreach ( $types as $type ) {
				$params_type = class_exists('JRegistry')? new JRegistry($type->vote_option) : new JParameter($type->vote_option);
				$array_votes [$type->id] ['value'] = $params_type->get ( 'votes_value' ) ? str_replace ( "###", ',', $params_type->get ( 'votes_value' ) ) : '';
				$array_votes [$type->id] ['text'] = $params_type->get ( 'votes_text' ) ? $db->Quote ( str_replace ( "###", ',', $params_type->get ( 'votes_text' ) ) ) : '';
				$array_votes [$type->id] ['description'] = $params_type->get ( 'votes_description' ) ? $db->Quote ( str_replace ( "###", ',', htmlspecialchars ( $params_type->get ( 'votes_description' ) ) ) ) : '';
			}
		}
		
		$currentUser = JFactory::getUser ();
		require_once (JPATH_SITE . DS . 'components' . DS . 'com_javoice' . DS . 'models' . DS . 'forums.php');
		$model_forums = new JAVoiceModelforums ( );
		
		$searchword = JRequest::getString ( 'key' );
		require_once (JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_search' . DS . 'helpers' . DS . 'search.php');
		
		if ($items) {
			$model_status = JAVBModel::getInstance ( 'voicetypesstatus', 'javoiceModel' );
			foreach ( $items as $k => $item ) {
				
				if (! $item->status_publishded) {
					$items [$k]->voice_type_status_id = 0;
				}
				
				$items [$k]->content = $helper->showItem ( $items [$k]->content, 0 );
				//if user use anonymous in this item
				if(!($javconfig['systems']->get('use_anonymous', 0) && $item->use_anonymous)){
					if (isset ( $item->user_id ) && $item->user_id > 0) {
						$user = JFactory::getUser ( $item->user_id );										
						if($javconfig['plugin']->get('displayname', 'username') == "name"){						
							$creat_by = $user->name;
						}else if($javconfig['plugin']->get('displayname', 'username') == "username"){
							$creat_by = $user->username;
						}else{
							$creat_by = JText::_("ANONYMOUS");
						}
					} elseif (isset ( $item->guest_name ) && $item->guest_name != '') {
						if($javconfig['plugin']->get('displayname', 'username') == "anonymous"){
							$creat_by = JText::_("ANONYMOUS");
						}else{
							$creat_by = $item->guest_name;
						}
					} else {
						$creat_by = JText::_('ANONYMOUS' );
					}
				}else{
					$creat_by = JText::_('ANONYMOUS' );
				}
				
				$link = JRoute::_ ( 'index.php?option=com_javoice&view=items&layout=item&cid=' . $item->id . '&type=' . $item->voice_types_id . '&amp;forums=' . $item->forums_id . '&amp;Itemid=' . $Itemid );
				$items [$k]->create_date_store = $item->create_date;
				$items [$k]->create_date = $helper->generatTimeStamp ( $item->create_date );
				$items [$k]->update_date = $helper->generatTimeStamp ( $item->update_date );
				$items [$k]->create_by = $creat_by;
				$items [$k]->link = $link;
				
				$items [$k]->list_vote_value = '';
				$items [$k]->list_vote_text = '\'\'';
				$items [$k]->list_vote_description = '\'\'';
				$items [$k]->has_down = 0;
				
				$check = true;
				/* Check status has closed */
				if ($item->voice_type_status_id && $item->status_allow_voting == 0) {
					$items [$k]->list_vote_msg = $db->Quote ( JText::_('VOTING_IS_CLOSED' ) );
					$check = false;
				} elseif ($item->voice_type_status_id && $item->status_allow_voting == - 1) {
					$parent_status = $model_status->getItem ( $item->status_parent_id );
					if ($parent_status->allow_voting == 0) {
						$items [$k]->list_vote_msg = $db->Quote ( JText::_('VOTING_IS_CLOSED' ) );
						$check = false;
					}
					else{
						 $item->status_allow_voting = $parent_status->allow_voting;
					}
				}
				
				if ($check) {
					$user_can_post = $user_can_view = 0;
					
					$forum = $model_forums->getItem ( array ($item->forums_id ) );
					$forum->gids_post = str_replace ( "\n\n", "\n", $forum->gids_post );
					$forum->gids_vote = str_replace ( "\n\n", "\n", $forum->gids_post );
					$lits_user_can_posts = explode ( "\n", $forum->gids_post );
					$lits_user_can_views = explode ( "\n", $forum->gids_view );	
					$levels = $currentUser->getAuthorisedViewLevels();	
					$levels[] = 0;						
					foreach($levels as $gkey=>$gVal){
						if (in_array ( $gVal, $lits_user_can_posts )) {
							$user_can_post = 1;
						}
						if (in_array ( $gVal, $lits_user_can_views )) {
							$user_can_view = 1;
						}
					}
					
					
					if (! $user_can_view) {
						unset ( $items [$k] );
					} else {
						if ($user_can_post && isset ( $array_votes [$item->voice_types_id] ['value'] )) {
							$items [$k]->list_vote_value = $array_votes [$item->voice_types_id] ['value'];
							$items [$k]->list_vote_text = $array_votes [$item->voice_types_id] ['text'];
							$items [$k]->list_vote_description = $array_votes [$item->voice_types_id] ['description'];
							$items [$k]->list_vote_msg = '\'\'';
							
							$values = explode ( ',', $array_votes [$item->voice_types_id] ['value'] );
							
							foreach ( $values as $value ) {
								if (intval ( $value ) < 0) {
									$items [$k]->has_down = 1;
									break;
								}
							}
						
						} else {
							if ($currentUser->id)
								$items [$k]->list_vote_msg = '<span class=error>' . JText::_('YOU_DO_NOT_HAVE_PERMISSION_TO_VOTE' ) . '<span>';
							else
								$items [$k]->list_vote_msg = JText::_('LOGINREGISTER_TO_VOTE' );
							$items [$k]->list_vote_msg = $db->Quote ( $items [$k]->list_vote_msg );
						}
					}
				}
				
				if ($searchword && isset ( $items [$k] )) {
					
					$searchwords = preg_split ( "/\s+/u", $searchword );
					$needle = $searchwords [0];
					$maxchars = $javconfig ['systems']->get ( 'maxchars', 100 );
					if($maxchars==-1) $maxchars = strlen($item->content);
					
					SearchHelper::prepareSearchContent ( $item->content, $maxchars, $needle );
					$searchwords = array_unique ( $searchwords );
					
					$searchRegex = '#(';
					$x = 0;
					foreach ( $searchwords as $hlword ) {
						$searchRegex .= ($x == 0 ? '' : '|');
						$searchRegex .= preg_quote ( $hlword, '#' );
						$x ++;
					}
					$searchRegex .= ')#iu';
				
					$items [$k]->content = preg_replace ( $searchRegex, '<span class="highlight">\0</span>', $item->content );
					$items [$k]->title = preg_replace ( $searchRegex, '<span class="highlight">\0</span>', $item->title );
				}
			
			}
		}
		return $items;
	}
	
	function published($publish) {
		$db = JFactory::getDBO ();
		
		$ids = JRequest::getVar ( 'cid', array () );
		$ids = implode ( ',', $ids );
		
		$query = "UPDATE #__jav_items" . " SET published = " . intval ( $publish ) . " WHERE id IN ( $ids )";
		$db->setQuery ( $query );
		if (! $db->query ()) {
			return false;
		}
		foreach (JRequest::getVar ( 'cid', array () ) as $id){
			$item = $this->getItem( array($id) );
			
			$logs = $this->getLogs ( " and item_id=$id" );
			
			if ($logs) {
				foreach ( $logs as $log ) {
				
					$user = JFactory::getUser ( $log->user_id );
					$user_no_session = new JUser($log->user_id);
					$total_voted = ( int ) $user_no_session->getParam ( 'total-voted-' . $item->voice_types_id );
					
					if($publish == 0){
						$total_voted = (int)($total_voted - abs ( $log->votes ));
					}
					else{
						$total_voted = (int)($total_voted + abs ( $log->votes ));
					}
					if($total_voted<0) $total_voted = 0;
					
					$user_no_session->setParam ( 'total-voted-' . $item->voice_types_id,  $total_voted);
					$user_no_session->save ();
				}
			}
		}
		
		return true;
	}
	
	function saveOrder() {
		$order = JRequest::getVar ( 'order', null, 'request', 'array' );
		$cid = JRequest::getVar ( 'cid', null, 'request', 'array' );
		$total = count ( $cid );
		if ($total > 0) {
			JArrayHelper::toInteger ( $order, array (0 ) );
			$row = & $this->getTable ( 'items','Table' );
			
			for($i = 0; $i < $total; $i ++) {
				$row->load ( ( int ) $cid [$i] );
				if ($row->ordering != $order [$i]) {
					$row->ordering = $order [$i];
					
					if (! $row->store ()) {
						return false;
					}
				}
			}
		}
		return true;
	}
	
	function delete($id) {
		$item = $this->getItem( array($id) );
		if(!$item->voice_types_id){
			return array(JText::_('ITEM_NOT_FOUND'));
		}
		
		$type = $this->getVoiceType ( $item->voice_types_id );
		
		$query = "DELETE FROM #__jav_items WHERE id=$id";
		$this->_db->setQuery ( $query );
		
		if($this->_db->query ()){
			
			/* Return votes for users voted*/
			if ( $type->total_votes > - 1) {
				$this->voices_back($item);
			}
			
			/* Add JomSocial:: activity Stream*/
			if ($item->user_id) {										
				$action = 'remove';
				$title = sprintf(JText::_('JOMSOCIAL_ACTIVITI_STREAM_TITLE_REMOVE'), $type->title, $item->id, $item->voice_types_id, $item->forums_id, $item->title);
				JAVoiceHelpers::JomSocial_addActivityStream($item->user_id, $title, $item->id, $action);
			}
			/* End*/
			
			/* Get Forums count */
			$query = "Select count(*) From #__jav_items  WHERE `voice_types_id`={$item->voice_types_id} and `forums_id`={$item->forums_id}";
			$this->_db->setQuery ( $query );
			$total_items = $this->_db->loadResult ();
			
			/* Update Forums count */
			$query = "Update #__jav_forums_has_voice_types SET `total_items`='$total_items' WHERE `voice_types_id`={$item->voice_types_id} and `forums_id`={$item->forums_id}";
			$this->_db->setQuery ( $query );
			$this->_db->query ();
			return '';
		}	
		else{
			return $this->_db->getError();
		}
			
	}
	
	function initError() {
		$mess = new stdClass ( );
		$mess->title = JText::_("TITLE_MUST_NOT_BE_NULL" );
		
		$mess->status = JText::_("STATUS_MUST_NOT_BE_NULL_OR_NUMBER" );
		
		$mess->forum = JText::_("FORUMS_MUST_NOT_BE_NUMBER_OR_NUMBER" );
		
		$mess->voice_type = JText::_("VOICE_TYPES_MUST_NOT_BE_NUMBER_OR_NUMBER" );
		
		$mess->number_vote_up = JText::_("NUMBER_VOTE_UP_MUST_NOT_BE_NULL_OR_NUMBER" );
		
		$mess->total_vote_up = JText::_("TOTAL_VOTE_UP_MUST_NOT_BE_NULL_OR_NUMBER" );
		
		$mess->number_vote_down = JText::_("NUMBER_VOTE_DOWN_MUST_NOT_BE_NULL_OR_NUMBER" );
		
		$mess->total_vote_down = JText::_("TOTAL_VOTE_DOWN_MUST_NOT_BE_NULL_OR_NUMBER" );
		
		$mess->number_vote_neutral = JText::_("NUMBER_NEUTRAL_MUST_NOT_BE_NULL_OR_NUMBER" );
		
		$mess->number_spam = JText::_("NUMBER_SPAM_MUST_NOT_BE_NULL_OR_NUMBER" );
		
		//$mess->number_duplicate = JText::_("NUMBER_DUPLICATE_MUST_NOT_BE_NULL_OR_NUMBER" );
		
		//$mess->number_inappropriate = JText::_("NUMBER_INAPPROPRIATE_MUST_NOT_BE_NULL_OR_NUMBER" );
		
		return $mess;
	}
	
	function getAdmin_responses($where_more = '', $limitstart = 0, $limit = 20, $orderby = 'r.id', $fiels = '', $joins = '') {
		$lists = array ();
		$db = JFactory::getDBO ();
		$sql = "SELECT r.* $fiels " . "\n FROM #__jav_admin_responses as r $joins" . "\n WHERE 1 $where_more" . "\n ORDER BY $orderby" . "\n LIMIT $limitstart, $limit";
		$db->setQuery ( $sql );
		
		$items = $db->loadObjectList ();
		return $items;
	}
	function getTotalAdmin_responses($where_more = '', $joins = '') {
		$db = JFactory::getDBO ();
		$sql = "SELECT count(DISTINCT r.id)  " . "\n FROM #__jav_admin_responses as r $joins" . "\n WHERE 1 $where_more";
		$db->setQuery ( $sql );
		
		return $db->loadResult ();
	
	}
	
	function getAdmin_response($cid = array()) {
		
		$table = $this->getTable ( 'Admin_responses', 'Table' );
		// Load the current item if it has been defined
		if (! $cid) {
			$cid = JRequest::getVar ( 'cid', array (0 ), '', 'array' );
			JArrayHelper::toInteger ( $cid, array (0 ) );
		}
		
		if ($cid) {
			$table->load ( $cid [0] );
		}
		
		return $table;
	}
	function getAmin_responseByItem($item_id) {
		$db = JFactory::getDBO ();
		$query = "SELECT * FROM #__jav_admin_responses WHERE item_id=$item_id";
		$db->setQuery ( $query );
		return $db->loadObjectList ();
	}
	function parseAdmin_response(&$items) {
		$count = count ( $items );
		if ($count > 0) {
			for($i = 0; $i < $count; $i ++) {
				$item = &$items [$i];
				if (! isset ( $item->item_title )) {
					$cid [0] = $item->item_id;
					$row = $this->getItem ( $cid );
					$item->item_title = $row ? $row->title : '';
					$item->voice_types_id = $row->voice_types_id;
				}
				if ($item->user_id) {
					$user = JFactory::getUser ( $item->user_id );
					$item->username = $user ? $user->username : '';
				}
			}
		}
	}
	
	function store_admin_response() {
		global $javconfig;
		
		$db = JFactory::getDBO ();
		$post = $this->getState ( 'request' );
		$item = $this->getAdmin_response ();
		if (! isset ( $item->item_id ) || ! $item->item_id)
			$item_id = JRequest::getInt ( 'item_id' );
		else
			$item_id = $item->item_id;
			
		if (! $item->bind ( $post )) {
			return $item->getError ();
		}
		
		if (($erros = $item->check ())) {
			return implode ( "<br/>", $erros );
		}
		//
		if (!$item->store ()) {
			return $item->getError ();
		}
		
		/* Add JomSocial:: activity Stream*/
		$cid = JRequest::getVar ( 'cid', array (), '', 'array' );
		if ($item->user_id && !$cid[0]) {
			$row = $this->getItem(array($item->item_id));
			$title = sprintf(JText::_('JOMSOCIAL_ACTIVITI_STREAM_TITLE_REPLY'), $row->id, $row->voice_types_id, $row->forums_id, $row->title);
			
			JAVoiceHelpers::JomSocial_addActivityStream($item->user_id, $title, $row->id, 'reply');
		}
		/* End*/				
		
		return $item;
	}
	
	function delete_admin_response($responseID){
		$response = $this->getAdmin_response(array($responseID));
		
		$row = $this->getItem(array($response->item_id));
		
		$db = JFactory::getDBO ();
		$query = "DELETE FROM #__jav_admin_responses WHERE id=$responseID";
		$db->setQuery($query);
		
		if($db->query()){
			/* Add JomSocial:: activity Stream*/
			if ($response->user_id) {			
				$title = sprintf(JText::_('JOMSOCIAL_ACTIVITI_STREAM_TITLE_REMOVE_REPLY'), $row->id, $row->voice_types_id, $row->forums_id, $row->title);
				JAVoiceHelpers::JomSocial_addActivityStream($response->user_id, $title, $row->id, 'removereply');
			}
			/* End*/
		}
	}
	
	function formatFilesInDir($dir, $task = "", $user_id = 0, $id = 0) {
		$fileindir = $this->getFilesInDir ( $dir );
		if (! $fileindir || (count ( $fileindir ) == 0))
			return '';
		$arr_files = array ();
		$check = "";
		$d = 0;
		foreach ( $fileindir as $file ) {
			$d ++;
			$i = $id + $d;
			$newfile = new stdClass ( );
			$newfile->file_name = $file;
			$newfile->icon = $this->geticons ( $file );
			$newfile->size = intval ( filesize ( $dir . DS . $file ) / 1024 ) + 1;
			
			//$link = JRoute::_ ( JURI::root () . "index.php?option=com_javoice&controller=items&task=$task" );				
			
			if(strpos($dir, "admin_response") !== false){
				$newfile->name = $newfile->icon."<a href=\"" . JRoute::_ ( "index.php?task=".$task."&file=".$file."&id=".$id."&user_id=".$user_id."&downloadresponse=1" ) . "\" target=_blank>$file</a>
						  (" . $newfile->size . " kb)";
			}else{
				$newfile->name = $newfile->icon."<a href=\"" . JRoute::_ ( "index.php?task=".$task."&file=".$file."&id=".$id."&user_id=".$user_id ) . "\" target=_blank>$file</a>
						  (" . $newfile->size . " kb)";			
			}
			$arr_files [] = $newfile;
		}		
		return $this->makeListFile ( $arr_files );
	}
	
	function makeListFile($lists, $canRemove = false) {
		$dlist = JRequest::getVar ( 'dlist' );
		$ncount = count ( $lists );
		if ($ncount == 0)
			return '';
		$items = array ();
		for($i = 0; $i < count ( $lists ); $i ++) {
			$file = $lists [$i];
			if ($canRemove) {
				if (is_array ( $dlist ) && in_array ( $file->file_name, $dlist )) {
					$items [] = "<input type=\"checkbox\" checked name=\"dlist[]\" value=\"$file->file_name\"/> " . $file->name;
				} else {
					$items [] = "<input type=\"checkbox\" name=\"dlist[]\" value=\"$file->file_name\"/>" . $file->name;
				}
			} else
				$items [] = $file->name;
		}
		if ($ncount > 1) {
			$str = implode ( "</li>\n<li>", $items );
			return "<ul class=\"att_list\"><li>" . $str . "</li></ul>";
		} else
			return "<ul class=\"att_list\"><li>" . $items [0] . "</li></ul>";
	}
	function geticons($file) {
		$file = substr ( $file, - 3 );
		$attach_icons = $this->attach_icons ();
		$icon = JURI::root () . "components/com_javoice/asset/images/icons/";
		if (array_key_exists ( $file, $attach_icons )) {
			$icon .= $attach_icons [$file];
		} else {
			$icon .= $attach_icons ["attach"];
		}
		return "<img src=\"$icon\" alt=\"File type:" . $file . "\" /> ";
	}
	
	function attach_icons() {
		return array ("bmp" => "bmp.gif", "doc" => "doc.gif", "gif" => "gif.gif", "jpe" => "jpe.gif", "jpeg" => "jpeg.gif", "jpg" => "jpg.gif", "pdf" => "pdf.gif", "php" => "php.gif", "png" => "png.gif", "psd" => "psd.gif", "rtf" => "rtf.gif", "txt" => "txt.gif", "zip" => "zip.gif", "tml" => "html.gif", "htm" => "html.gif", "attach" => "attach.gif" );
	}
	
	function getFilesInDir($dir) {
		if (! JFolder::exists ( $dir ))
			return null;
		return JFolder::files ( $dir );
	}
}
?>