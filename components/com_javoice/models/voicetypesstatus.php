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

class javoiceModelvoicetypesstatus extends JAVBModel {
	var $_table = null;
	
	function __construct() {
		parent::__construct ();
	}
	/**
	 * @return JTable Configuration table object
	 */
	
	function _getTable() {
		$this->_table = JTable::getInstance ( 'voicetypesstatus', 'Table' );
	}
	
	/**
	 * Get configuration item
	 * @return Table object
	 */
	
	function getItem($id = 0) {		
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
		$option = 'voicetypesstatus';
		$lists = array ();
		$lists ['filter_order'] = $mainframe->getUserStateFromRequest ( $option . '.filter_order', 'filter_order', 's.ordering', 'cmd' );
		$lists ['filter_order_Dir'] = $mainframe->getUserStateFromRequest ( $option . '.filter_order_Dir', 'filter_order_Dir', '', 'word' );
		$lists ['limit'] = $mainframe->getUserStateFromRequest ( $option . 'list_limit', 'limit', $mainframe->getCfg ( 'list_limit' ), 'int' );
		$lists ['limitstart'] = $mainframe->getUserStateFromRequest ( $option . '.limitstart', 'limitstart', 0, 'int' );
		$lists ['search'] = $mainframe->getUserStateFromRequest ( $option . '.search', 'search', '', 'string' );
		$lists ['statusparents'] = $mainframe->getUserStateFromRequest ( $option . '.statusparents', 'statusparents', '0', 'int' );
		$lists ['voice_types_id'] = $mainframe->getUserStateFromRequest ( $option . '.voice_types_id', 'voice_types_id', '0', 'int' );
		return $lists;
	}
	function getWhereClause($lists) {
		//where clause 
		$where1 = array ();
		
		$where = array ();
		if ($lists ['search']) {
			if (is_numeric ( $lists ['search'] ))
				$where1 [] = " s.id ='" . $lists ['search'] . "' ";
			else {
				$where1 [] = " s.title LIKE '%" . $lists ['search'] . "%' ";
				$where1 [] = " s.name LIKE '%" . $lists ['search'] . "%' ";
			}
		}
		$where1 = count ( $where1 ) ? '' . implode ( ' OR ', $where1 ) : '';
		if ($where1 != '') $where [] = "(" . $where1 . ")";
		if ($lists ['voice_types_id'] != 0) {
			$where [] = " s.voice_types_id={$lists['voice_types_id']}";
		}
		if ($lists ['statusparents'] != 0) {
			$w = " s.parent_id={$lists['statusparents']} ";
			if ($lists ['search'] == '') {
				$w .= " OR s.id= {$lists['statusparents']} ";
			}
			$where [] = " ($w) ";
		}
		$where = count ( $where ) ? ' AND ' . implode ( ' AND ', $where ) : '';
		
		Return $where;
	}
	
	function getItems($where = '', $orderby = '', $limitstart = 0, $limit = 0, $fields = '', $joins = '') {
		$db = JFactory::getDBO ();
		$query = " 	SELECT s.* ";
		if ($fields) $query .= " ,$fields ";
		$query .= "	FROM #__jav_voice_type_status AS s";
		if ($joins) $query .= " $joins";
		$query .= " WHERE 1 $where ";
		if ($orderby) $query .= " ORDER BY $orderby ";
		if ($limit != 0) $query .= " LIMIT $limitstart,$limit ";
		$db->setQuery ( $query );
		return $this->_db->loadObjectList ();
	}
	
	function getTotal($where, $joins = '') {
		$db = JFactory::getDBO ();
		$query = " 	SELECT COUNT(s.id)" . "\n FROM #__jav_voice_type_status AS s" . "\n $joins" . "\n WHERE 1 $where ";
		$db->setQuery ( $query );
		return $db->loadResult ();
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $parents: lists parent
	 * @param unknown_type $order :order by childerent
	 * @param unknown_type $published: publish or unpublish
	 * @param unknown_type $isspam: 0 return status do not spam,1 return status spam,2 all in
	 * $where_more search status: search child status
	 * @return list status include parent child, parent child....
	 */
	function parseItems($parents, $order = 's.title', $published = '',$isspam=2,$where_more='',$allparent=1,$parent_search=0) {
		$items = array ();
		$count = count ( $parents );
		if ($count > 0) {
			$orderby = " s.parent_id ASC ";
			if ($order) $orderby .= ',' . $order;
			foreach ( $parents as $parent ) {
				
				if($parent_search!=$parent->id)				
					$where = $where_more." AND  s.parent_id = $parent->id";
				else{
					$where = " AND  s.parent_id = $parent->id";
				}
				if ($published != '') $where_more .= ' and s.published=' . ( int ) $published;
				$fields = "t.title as vtitle";
				$joins = "INNER JOIN #__jav_voice_types AS t ON s.voice_types_id=t.id";
				$temps = $this->getItems ( $where, $orderby, 0, 0, $fields, $joins );
								
				//print_r($temps);echo '<br>--------<br>';
				if($allparent)$items[] = $parent;		
										
				if ($temps) {
					$count = count($temps);
					for ( $i=0;$i<$count;$i++ ) {						
						$spam = 0;
						if($temps[$i]->allow_show==-1){								
							if(!$parent->allow_show)$spam = 1;				
						}elseif (!$temps[$i]->allow_show){
							$spam = 1;
						}
						if($isspam!=2){
							if($isspam==1){
								if(!$spam)
									unset($temps[$i]);
							}else{
								if($spam)
									unset($temps[$i]);
							}
						}
					}						
					$total_status = count($temps);
					if($isspam==1){
						if($total_status>0){
							$items[] =  $parent;
							$items = array_merge ( $items, $temps );
							
						}
					}else{
						if($total_status>0)
						{		
							if(!$allparent)$items[] = $parent;					
							$items = array_merge ( $items, $temps );
						}
					}
				}	
						
			}
		}
		
		return $items;
	}
	
	function updateColor($class_css, $id=0){
		$db = JFactory::getDBO ();
		if (! $id) {
			$cid = JRequest::getVar ( 'cid', array (0 ), '', 'array' );
			JArrayHelper::toInteger ( $cid, array (0 ) );
			
			if (isset ( $cid [0] ) && $cid [0] > 0) {
				$id = $cid [0];
			}
		}
		$class_css = $db->Quote("#".$class_css);
		$query = "UPDATE #__jav_voice_type_status" . " SET class_css = " . $class_css . " WHERE id = " . intval ( $id );		
		$db->setQuery ( $query );		
		if (! $db->query ()) {
			return false;
		}
		return true;		
	}
	
	function getItemsDyamic($fiel, $where = '', $orderby = '', $limitstart = 0, $limit = 0,$typereturn=0) {
		$db = JFactory::getDBO ();
		$query = " 	SELECT $fiel 
 		 			FROM #__jav_voice_type_status AS s 
 					INNER JOIN #__jav_voice_types AS t ON s.voice_types_id=t.id ";
		if ($where) $query .= " WHERE 1 $where ";
		if ($orderby) $query .= " ORDER BY $orderby ";
		if ($limit > 0) $query .= " LIMIT $limitstart,$limit ";
		
		$db->setQuery ( $query );
		if($typereturn){
			if (version_compare(JVERSION, '3.0', 'ge'))
			{
				return $db->loadColumn();
			}else{
				return $db->loadResultArray();
			}
		}	
		else{
			return $db->loadObjectList ();
		}	
	}
	
	
	function published($publish) {
		$db = JFactory::getDBO ();
		
		$ids = JRequest::getVar ( 'cid', array () );
		$ids = implode ( ',', $ids );
		
		$query = "UPDATE #__jav_voice_type_status" . " SET published = " . intval ( $publish ) . " WHERE id IN ( $ids ) ";
		if($publish==0)$query.=" OR parent_id IN($ids) ";
		$db->setQuery ( $query );
		if (! $db->query ()) {
			return false;
		}
		return true;
	}
	
	
	function showtab($show) {
		$db = JFactory::getDBO ();
		
		$ids = JRequest::getVar ( 'cid', array () );
		$ids = implode ( ',', $ids );
		
		$query = "UPDATE #__jav_voice_type_status" . " SET show_on_tab = " . intval ( $show ) . " WHERE id IN ( $ids )";
		$db->setQuery ( $query );
		if (! $db->query ()) {
			return false;
		}
		return true;
	}
	
	
	function system($system) {
		$db = JFactory::getDBO ();
		
		$ids = JRequest::getVar ( 'cid', array () );
		$ids = implode ( ',', $ids );
		
		$query = "UPDATE #__jav_voice_type_status" . " SET system = " . intval ( $system ) . " WHERE id IN ( $ids )";
		$db->setQuery ( $query );
		if (! $db->query ()) {
			return false;
		}
		return true;
	}
	
	
	function delete($id){
		$db = JFactory::getDBO();
		$query = "DELETE FROM #__jav_voice_type_status WHERE id=$id";
		$db->setQuery ( $query );	
		return $db->query();	
	}
	
	
	function remove() {
		$db = JFactory::getDBO ();
		$cids = JRequest::getVar ( 'cid', null, 'request', 'array' );
		$count = count ( $cids );
		$errors = array ();
		$id_used = array ();
		$is_fail = array ();
		$is_parent = array ();
		if ($count > 0) {
			foreach ( $cids as $id ) {
				$status = $this->getItem ( $id );
				if ($status) {
					//Check delete status
					if ($status->parent_id) {
						$model_items = JAVBModel::getInstance ( 'items', 'javoiceModel' );
						$join = " INNER JOIN #__jav_forums_has_voice_types as t ON f.id = t.forums_id";
						$model_forum = JAVBModel::getInstance ( 'forums', 'javoiceModel' );
						$total_item = $model_items->getTotal ( " AND i.voice_type_status_id=$id " );
						$total_forum = $model_forum->getTotal ( " AND t.voice_type_status_id =$id ", $join );
						if ($total_item > 0 || $total_forum > 0) {
							$id_used [] = $id;
						}
						else {						
							if (!$this->delete($id))
								$is_fail [] = $id;
							else {
								
								$condition = 'parent_id = ' . ( int ) $status->parent_id . ' AND voice_types_id = ' . ( int ) $status->voice_types_id;
								$status->reorder ( $condition );
							
							}
						}
					}
					else {
						$child = $this->getTotal ( ' AND s.parent_id = ' . ( int ) $id );
						if ($child > 0) $is_parent [] = $id;
						else {
							if(!$this->delete($id))
								$is_fail [] = $id;
							else {
								$condition = 'parent_id = 0 AND voice_types_id = ' . ( int ) $status->voice_types_id;
								$status->reorder ( $condition );								
							}
						}
					}				
				}
			}
			if (count ( $id_used ) > 0) {
				$errors [] = "[ID: " . implode ( ',', $id_used ) . "]" . JText::_('STATUS_IS_BEING_USED_BY_ITEMS_OR_FORUMS' );
			}
			if (count ( $is_fail ) > 0) {
				$errors [] = "[ID: " . implode ( ',', $is_fail ) . "]" . JText::_('FAILURE_TO_DELETE_STATUS' );
			}
			if (count ( $is_parent ) > 0) {
				$errors [] = "[ID: " . implode ( ',', $is_parent ) . "]" . JText::_('YOU_MUST_DELETE_ALL_STATUS_IN_GROUP' );
			}
		}
		return $errors;
	}
	
	
	function saveOrder() {
		$mainframe = JFactory::getApplication();
		// Check for request forgeries
		JRequest::checkToken () or jexit ( 'Invalid Token' );
		
		// Initialize variables
		$db = JFactory::getDBO ();
		
		$cid = JRequest::getVar ( 'cid', array (0 ), 'request', 'array' );
		$order = JRequest::getVar ( 'order', array (0 ), 'request', 'array' );
		$total = count ( $cid );
		$conditions = array ();
		
		JArrayHelper::toInteger ( $cid, array (0 ) );
		JArrayHelper::toInteger ( $order, array (0 ) );
		
		// Instantiate an article table object
		$row = $this->getTable ( 'voicetypesstatus','Table');
		
		// Update the ordering for items in the cid array
		for($i = 0; $i < $total; $i ++) {
			$row->load ( ( int ) $cid [$i] );
			if ($row->ordering != $order [$i]) {
				$row->ordering = $order [$i];
				if (! $row->store ()) {
					JError::raiseError ( 500, $db->getErrorMsg () );
					return false;
				}
				// remember to updateOrder this group
				$condition = 'parent_id = ' . ( int ) $row->parent_id . ' AND voice_types_id = ' . ( int ) $row->voice_types_id;
				$found = false;
				foreach ( $conditions as $cond )
					if ($cond [1] == $condition) {
						$found = true;
						break;
					}
				if (! $found) $conditions [] = array ($row->id, $condition );
			}
		}
		// execute updateOrder for each group
	
		foreach ( $conditions as $cond ) {
			$row->load ( $cond [0] );
			$row->reorder ( $cond [1] );
		}	
		return TRUE;
	}
	
	function changeAllGroup($parent_id, $voice_type_id) {
		$db = JFactory::getDBO ();
		
		$query = "UPDATE #__jav_voice_type_status" . " SET voice_types_id = " . intval ( $voice_type_id ) . " WHERE parent_id = " . intval ( $parent_id );
		$db->setQuery ( $query );
		if (! $db->query ()) {
			return false;
		}
		return true;
	}
	
	
	function getLog($item_id){
		$db=JFactory::getDBO();
		$sql = "SELECT user_id	
				FROM #__jav_logs WHERE item_id = ". (int) $item_id;
		$db->setQuery($sql);
		
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			return $db->loadColumn();
		}else{
			return $db->loadResultArray();
		}	
	}
	
	function writeLogChange($old_stauts_id,$item){
		$url = JURI::root();
		$path = JPATH_COMPONENT_SITE.DS."asset".DS."emails".DS."change";
		$filename = $path.DS.'emails_'.$item->id."_".time().'.ini';	
		if(!file_exists($filename)){
			$users = $this->getLog($item->id);
			if($users){
				if (intval($item->voice_type_status_id) != intval($old_stauts_id)) {
					$helper = new JAVoiceHelpers ( );
					$mail=$helper->getEmailTemplate("Javnotify_to_user_item_change_status");
					if(!$mail)return FALSE;
					$header=$helper->getEmailTemplate("mailheader");
					$footer=$helper->getEmailTemplate("mailfooter");
					$mailcontent=$header->emailcontent."\n".$mail->emailcontent."\n\n".$footer->emailcontent;
					$filters = $helper->getFilterConfig ();			
					$model_item = JAVBModel::getInstance ( 'items', 'javoiceModel' );
					$model_item->parseItem($item);
					$filters["ITEM_TITLE"]=$item->title;
					$link = $_SERVER['HTTP_HOST'].str_replace($_SERVER['HTTP_HOST'], '', JRoute::_("index.php?option=com_javoice&view=items&layout=item&cid=$item->id&type=$item->voice_types_id&forums=$item->forums_id"));
					$filters['ITEM_TITLE_WITH_LINK']="<a href=\"$link\">".$item->title."</a>";
					$filters["ITEM_FORUM"]= $item->ftitle;
					$filters["ITEM_VOICE_TYPE"]= $item->ttitle;
					$filters["ITEM_DESCRIPTON"]= $item->content;
					$filters["ITEM_CREATE_DATE"]= date('d/M/Y',$item->create_date);
					$user = JFactory::getUser($item->user_id);
					$filters["ITEM_CREATE_BY"]= $user->username;
					$filters["ITEM_NUM_OF_VOTERS"]= ($item->number_vote_up + $item->number_vote_down);
					$filters["ITEM_NUM_OF_VOTERS_DOWN"]= ($item->number_vote_down).", ";
					$filters["ITEM_TOTAL_VOTE_DOWN"]= ($item->total_vote_down);
					$filters["ITEM_NUM_OF_VOTERS_UP"]= ($item->number_vote_up).", ";
					$filters["ITEM_TOTAL_VOTE_UP"]= $item->total_vote_up;	
					$filters["ITEM_NEW_STATUS_WITH_COLOR"]="<span style='background:{$item->class_css}'>".($item->stitle)."</span>";
					$filters["ITEM_NEW_STATUS"]=$item->stitle;
					
					$old_status = $this->getItem($old_stauts_id);
					if($old_status->title==''){
						$old_status->class_css='';
						$old_status->title = JText::_('HAVING_NO_STATUS');
					}
					$filters["ITEM_OLD_STATUS"]="<span style='background:{$old_status->class_css}'>".($old_status->title)."</span>";
					$subject=$mail->subject?$mail->subject:'';
					if (is_array ( $filters )) {
						foreach ( $filters as $key => $value ) {
							$subject = str_replace ( "{".$key."}", $value, $subject );
							$mailcontent = str_replace ( "{".$key."}", $value, $mailcontent );
						}
					}	
					$details='';
					$mailcontent = str_replace("\n","###",$mailcontent);
					$details.="subject=".$subject;	
					$details.="\nmailcontent={$mailcontent}";				
					$user = implode(",",$users); 
					$details.="\nuser_id={$user}";		
					$details.="\nold_status_id=".$old_stauts_id;	
					$model_sendmail = JAVBModel::getInstance ( 'sendmail', 'javoiceModel' );
					$model_sendmail->writeLogFileChange($details,$filename);
				}
			}
		}
	}
	
	
	function changeStatus($old_status_id, $item) {
		//echo $old_status_id;echo $item->voice_type_status_id;
		$model_actionslog = JAVBModel::getInstance ( 'actionslog', 'javoiceModel' ); 
		$user = JFactory::getUser();
		$type= JText::_("CHANGE_STATUS");

		$old_status = $this->getItem($old_status_id);
		$old_status->title = $old_status->title ?$old_status->title:JText::_("NONE_STATUS"); 
		$details = JText::_("CHANGE_STATUS_ITEMS_FROM").$old_status->title;
		$this->getTable('','Table');
		$new_status = $this->getItem($item->voice_type_status_id);
		$new_status->title = $new_status->title ?$new_status->title:JText::_("NONE_STATUS"); 
		$details.= " ".JText::_("TO")." ". $new_status->title;
		$ref_id = $item->id;
		$time = time();
		$model_actionslog->makeLog($user->id,$type,$details,$ref_id,$time);

		$this->writeLogChange($old_status_id,$item);				
	}

	function displaySelectList($voicetypes,$all='') {
		$lists = array ();
		foreach ( $voicetypes as $voicetype ) {
			$attrs = " style='width:100px;' name='voice_type_status[$voicetype->id]' id='voice_type_status_$voicetype->id'";
			$lists [$voicetype->id] = $this->displaySelectOptgroup ( $voicetype->id, $voicetype->voice_type_status_id, $attrs,'',$all );
		}
		return $lists;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $voice_type_id :
	 * @param unknown_type $voice_type_status_id: selected status
	 * @param unknown_type $atts
	 * @return unknown
	 */
	function displaySelect($voice_type_id, $voice_type_status_id, $attrs) {
		$where = " AND s.voice_types_id=$voice_type_id AND s.parent_id=0 ";
		$items = $this->getItems ( $where, ' s.title ', 0, 0 );
		$list = "<select $attrs>";
		if (count ( $items ) > 0) {
			foreach ( $items as $item ) {
				if ($item->id == $voice_type_status_id) {
					$list .= "<option selected=\"selected\" class=\"$item->class_css\" value=\"" . $item->id . "\">" . $item->title . "</option>\n";
				}
				else {
					$list .= "<option class=\"$item->class_css\" value=\"" . $item->id . "\">" . $item->title . "</option>\n";
				}
			}
		}
		$list .= "</select>";
		return $list;
	}
	function displaySelectOptgroup($voice_type_id, $voice_type_status_id, $attrs, $published = '',$all=0) {
		$where = " AND s.voice_types_id=$voice_type_id AND s.parent_id=0 ";
		if ($published != '') $where .= ' and s.published=' . ( int ) $published;
		$parents = $this->getItems ( $where, ' s.title ', 0, 0 );
		
		$items = $this->parseItems ( $parents, '', $published,2,'',0 );
		
		$list = "<select $attrs>";
		$list .= "<option value=''>";
		if($all)$list .=$all; 
		$list .= "</option>\n";
		if ($items) {
			foreach ( $items as $item ) {
				if (! $item->parent_id) {
					$list .= "<optgroup style='background:{$item->class_css}; font-size: 80%;' label=\"$item->title\">" . $item->title . "</optgroup>\n";
				}
				else {
					if ($item->id == $voice_type_status_id) {
						$list .= "<option selected=\"selected\" value=\"" . $item->id . "\">" . $item->title . "</option>\n";
					}
					else {
						$list .= "<option value=\"" . $item->id . "\">" . $item->title . "</option>\n";
					}
				}
			}
		}
		$list .= "</select>";
		
		return $list;
	}
	
	function getListTreeStatus($type_id='0', $cache=true, $published=1) {
		if($cache){
			static $list_status_tree;
			if (isset ( $list_status_tree )) return $list_status_tree;
		}
		$type_id = JRequest::getInt ( 'type', 0 )?JRequest::getInt ( 'type' ):$type_id;
		$db = JFactory::getDBO ();
		$where = '';
		if($type_id){
			$where .= " and s.voice_types_id in ($type_id) ";
		}
		if($published){
			$where .= ' and s.published=1';
		}
		// get a list of the menu items
		// excluding the current menu item and its child elements
		$query = 'SELECT distinct s.id, s.class_css, s.allow_show, s.allow_voting, s.title,IFNULL(s.parent_id,0) parent_id' . ' FROM #__jav_voice_type_status s' . ' WHERE 1 ' . $where . ' ORDER BY s.ordering';
		$db->setQuery ( $query );
		$treeItems = $db->loadObjectList (); //print_r($treeItems);exit;
		if ($treeItems) {
			// first pass - collect children
			foreach ( $treeItems as $v ) { // iterate through the menu items
				$pt = $v->parent_id; // we use parent as our array index
				// if an array entry for the parent doesn't exist, we create a new array
				$list = @$children [$pt] ? $children [$pt] : array ();
				
				// we push our item onto the array (either the existing one for the specified parent or the new one
				array_push ( $list, $v );
				// We put out updated list into the array
				$children [$pt] = $list;
			}
		}
		
		// second pass - get an indent list of the items
		$list = $this->treerecurse ( 0, '', array (), $children, 9999, 0, 0 );
		
		$list_status_tree = $list;
		return $list_status_tree;
	}
	
	function builtTreeStatus($list, $selected = '', $itemid, $name = 'parent_id', $size = 10, $options = '') {
		$treeItems = array ();
		$type_id = JRequest::getInt ( 'type' );
		$output = '';
		foreach ( $list as $item ) {
			$str_selected = '';
			if ($item->id == $selected) $str_selected = 'selected';
			if ($item->parent_id == 0) {
				$output .= "<dt class='{$item->class_css}'>$item->title</dt>";
			}
			else {
				$output .= '<dd><ul class="ul-jav-status"><li><a onclick="jav_change_status(' . $itemid . ', ' . $item->id . ', ' . $type_id . ');return false;" href="#" class="' . $str_selected . '">' . $item->title . '</a></li></ul></dd>';
//				$output .= '<dd>
//								<ol>
//									<li><a onclick="jav_change_status(' . $itemid . ', ' . $item->id . ', ' . $type_id . ');return false;" href="#" class="' . $str_selected . '">' . $item->title . '</a></li>																																         
//								</ol>
//							</dd>';
			}
		}
		$output .= '<dt>
						<a onclick="jav_change_status(' . $itemid . ', 0);return false;" href="#" class="' . $str_selected . '">' . JText::_('REMOVE' ) . '</a>																																         
					</dt>';
		
		return $output;
	}
	
	function treerecurse($id, $indent, $list, &$children, $maxlevel = 9999, $level = 0, $type = 1) {
		if (@$children [$id] && $level <= $maxlevel) {
			foreach ( $children [$id] as $v ) {
				$id = $v->id;
				
				/*if ( $type ) {
					$pre 	= '<sup>|_</sup>&nbsp;';
					$spacer = '.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				} else {
					$pre 	= '- ';
					$spacer = '&nbsp;&nbsp;';
				}*/
				
				if ($v->parent_id == 0) {
					$txt = $v->title;
				}
				else {
					$txt = $v->title;
				}
				
				$pt = $v->parent_id;
				$list [$id] = $v;
				$list [$id]->treename = "$indent$txt";
				$list [$id]->children = count ( @$children [$id] );
				$list = $this->treeRecurse ( $id, $indent, $list, $children, $maxlevel, $level + 1, $type );
			}
		}
		
		return $list;
	}
	
	function is_spam($status_id=0, $status=null){
		if(!$status) $status = $this->getItem($status_id);
		$parent_status = $this->getItem($status->parent_id);
		
		if($status->allow_show==-1){					
			$status->allow_show = $parent_status->allow_show;					
		}
		
		if($status->allow_show){
			return 0;
		}
		return 1;
	}
	
	function is_closed($status_id){
		$status = $this->getItem($status_id);
		$parent_status = $this->getItem($status->parent_id);
		if($status->allow_voting==-1){					
			$status->allow_voting = $parent_status->allow_voting;					
		}
		if($status->allow_show==-1){					
			$status->allow_show = $parent_status->allow_show;					
		}
		
		if(!$status->allow_voting && $status->allow_show){
			return 1;
		}
		return 0;
	}
}

?>