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
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

JTable::addIncludePath(JPATH_COMPONENT.'/tables');

class javoiceModelTags extends JAVBModel{
	function getData($list =  array()) {		
		$mainframe = JFactory::getApplication();
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');
		$db = JFactory::getDBO();
		/*
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option.$view.'.limitstart', 'limitstart', 0, 'int');
		$filter_order = $mainframe->getUserStateFromRequest($option.$view.'filter_order', 'filter_order', 'id', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest($option.$view.'filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');
		$filter_state = $mainframe->getUserStateFromRequest($option.$view.'filter_state', 'filter_state', -1, 'int');
		$search = $mainframe->getUserStateFromRequest($option.$view.'search', 'search', '', 'string');
		$search = JString::strtolower($search);
		*/
		$query = "SELECT * FROM #__jav_tags WHERE id>0";
	
		if ($list['filter_state'] > -1) {
			$query .= " AND published={$list['filter_state']}";
		}
	
		if ($list['search']) {
			$query .= " AND LOWER( name ) LIKE ".$db->Quote('%'.$list['search'].'%');
		}
	
		if (!$list ['filter_order']) {
			$list ['filter_order'] = "name";
		}
	
		$query .= " ORDER BY {$list ['filter_order']} {$list ['filter_order_Dir']}";
	
		$db->setQuery($query, $list ['limitstart'], $list ['limit']);
		$rows = $db->loadObjectList();
		return $rows;
	}
	
	function _getVars() {	
		$mainframe = JFactory::getApplication();
		
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');
		
		$list = array ();
		$list ['limit'] = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$list ['limitstart'] = $mainframe->getUserStateFromRequest($option.$view.'.limitstart', 'limitstart', 0, 'int');
		$list ['filter_order'] = $mainframe->getUserStateFromRequest($option.$view.'filter_order', 'filter_order', 'id', 'cmd');
		$list ['filter_order_Dir'] = $mainframe->getUserStateFromRequest($option.$view.'filter_order_Dir', 'filter_order_Dir', 'DESC', 'word');
		$list['filter_state'] = $mainframe->getUserStateFromRequest($option.$view.'filter_state', 'filter_state', -1, 'int');
		$list ['search'] = $mainframe->getUserStateFromRequest($option.$view.'search', 'search', '', 'string');
		$list ['search'] = isset($search)?JString::strtolower($search):'';
		
		return $list;
	}
	
	function addVoiceToTag($id,$javtags){		
		if(!$javtags || count($javtags) <=0) return;		
		$db 	= JFactory::getDBO();
		$query  = "SELECT tagID FROM #__jav_tags_voice WHERE voiceID=".$id;		
		$db->setQuery($query);
		
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$results = $db->loadColumn();
		}else{
			$results = $db->loadResultArray();
		}
		foreach ($javtags as $item){
			//add first			
			if(!in_array($item,$results)){
				$query  = "INSERT INTO #__jav_tags_voice(tagID,voiceID) VALUE('".$item."','".$id."')";									
				$db->setQuery($query);
				//echo $db->getQuery(); 
				$db->Query();
			}
		}
		
		foreach ($results as $item){
			//delete			
			if(!in_array($item,$javtags)){
				$query  = "DELETE FROM #__jav_tags_voice WHERE tagID='".$item."' AND voiceID='".$id."'";		
				$db->setQuery($query);
				//echo $db->getQuery();
				$db->Query();			
			}
		}	
		//die();
	}
	
	function getTagByVoice($voiceID){
		$db = JFactory::getDBO();
		$db->setQuery("SELECT t.* FROM #__jav_tags as t INNER JOIN #__jav_tags_voice as tv ON t.id=tv.tagID WHERE tv.voiceID='".$voiceID."' AND t.published = 1");
		//die($db->getQuery());
		return $db->loadObjectList();
	}
	
	
	function getTagsList(){
		global $javconfig;
		$db = JFactory::getDBO();		
		$queryString = JRequest::getVar("queryString", "");
		$currentText = JRequest::getVar("currenttext", "");
		$list = explode($javconfig['systems']->get('characters_separating_tags',','), $currentText);
		$searchArray = array();
		
		for($i = 0; $i < count($list);$i++){
			if($list[$i]){	 	
				$searchArray[] = $list[$i]; 
			} 			
		}
		
		$queryString = $db->Quote ($queryString."%");
		if(count($searchArray)){
			$db->setQuery("SELECT * FROM #__jav_tags WHERE published=1 AND name LIKE $queryString AND name NOT IN('" . implode ( "','", $searchArray ) . "')");	
		}else{
			$db->setQuery("SELECT * FROM #__jav_tags WHERE published=1 AND name LIKE $queryString");
		}
		//die($db->getQuery());
		return $db->loadObjectList();
	}
	
	function getitem(){
		$cid = JRequest::getVar('cid');		
		$row = $this->getTable ( 'Tags', 'Table' );
		if(!is_array($cid)){				
			$row->load($cid);
		}else{
			$row->load($cid[0]);
		}
		return $row;
	}
	
	function getTotal() {	
		$mainframe = JFactory::getApplication();
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');
		$db = JFactory::getDBO();
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');
		$filter_state = $mainframe->getUserStateFromRequest($option.$view.'filter_state', 'filter_state', 1, 'int');
		$search = $mainframe->getUserStateFromRequest($option.$view.'search', 'search', '', 'string');
		$search = JString::strtolower($search);
	
		$query = "SELECT COUNT(*) FROM #__jav_tags WHERE id>0";
		
		if ($filter_state > -1) {
			$query .= " AND published={$filter_state}";
		}
		
		if ($search) {
			$query .= " AND LOWER( name ) LIKE ".$db->Quote('%'.$search.'%');
		}
		
		$db->setQuery($query);
		$total = $db->loadresult();
		return $total;
	}
	
	function published($publish) {
		$db = JFactory::getDBO ();
		
		$ids = JRequest::getVar ( 'cid', array () );
		$ids = implode ( ',', $ids );
		
		$query = "UPDATE #__jav_tags" . " SET published = " . intval ( $publish ) . " WHERE id IN ( $ids )";
		$db->setQuery ( $query );
		if (! $db->query ()) {
			return false;
		}
		return true;
	}		

	function remove() {			
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		$cid = JRequest::getVar('cid');		
		$row = $this->getTable ( 'Tags', 'Table' );
		foreach ($cid as $id) {
			$row->load($id);
			$row->delete($id);
			$query = "DELETE FROM #__jav_tags_voice WHERE tagID='".$id."'";
			$db->setQuery($query);			
			$db->Query();			
		}
		$cache = JFactory::getCache('com_javoice');
		$cache->clean();
		$mainframe->redirect('index.php?option=com_javoice&view=tags', JText::_('DELETE_COMPLETED'));
	}
	
	function save($post,$noRedirect=0) {
		global $javconfig;		
		$mainframe = JFactory::getApplication();
		$row = $this->getTable ( 'Tags', 'Table' );
		$name 	= JRequest::getVar("name",'');
		if($javconfig["systems"]->get("tag_to_be_lower_case",0)){
			$name = strtolower($name);	
		}
		$publish= JRequest::getVar("published",1);
		if($post){
			if($javconfig["systems"]->get("tag_to_be_lower_case",0)){
				$post["name"] = strtolower($post["name"]);	
			}
			$post["name"] = trim($post["name"]);		
			if (!$row->bind($post)) {
				$mainframe->redirect('index.php?option=com_javoice&view=tags', $row->getError(), 'error');
			}			
		}else if(!JRequest::get('post')){									
			if($this->checkExistTag($name)){				
				if($noRedirect == 0){					
					$k++;
					$object [$k] = new stdClass ( );
					$object [$k]->id = '#system-message';
					$object [$k]->attr = 'html';
					$object [$k]->content = $errors;
					
					$helper = new JAVoiceHelpers ( );		
					echo $helper->parse_JSON_new ( $object );
					exit ();						
				}								
			}						
			$post["name"] 	 = $name;
			$post["publish"] = $publish;
			$post["name"] = trim($post["name"]);			
			if (!$row->bind($post)) {
				$mainframe->redirect('index.php?option=com_javoice&view=tags', $row->getError(), 'error');
			}
		}else{
			if (!$row->bind(JRequest::get('post'))) {
				$mainframe->redirect('index.php?option=com_javoice&view=tags', $row->getError(), 'error');
			}
		}
			
		if (!$row->check()) {
			$mainframe->redirect('index.php?option=com_javoice&view=tags&cid='.$row->id, $row->getError(), 'error');
		}

		if (!$row->store()) {
			$mainframe->redirect('index.php?option=com_javoice&view=tags', $row->getError(), 'error');
		}

		$cache = JFactory::getCache('com_javoice');
		$cache->clean();
		
		if($noRedirect) return $row->id;
				
		$helper = new JAVoiceHelpers ( );
		$errors = JText::_("TAG_SAVED");
		$objects [] = $helper->parseProperty ( "html", "#system-message", $errors );
		$objects [] = $helper->parseProperty ( "reload", "#reload" . 1, 1 );
		
		$helper = new JAVoiceHelpers ( );		
		echo $helper->parse_JSON_new ( $objects );
		exit ();					
	}

	function addTag(){

		$mainframe = JFactory::getApplication();
		$tag=JRequest::getString('tag');
		$tag = str_replace('-','',$tag);

		$response = new JObject;
		$response->set('name',$tag);


		require_once(JPATH_COMPONENT_ADMINISTRATOR.'/lib/JSON.php');
		$json=new Services_JSON;

		if (empty($tag)){
			$response->set('msg',JText::_('YOU_NEED_TO_ENTER_A_TAG',true));
			echo $json->encode($response);
			$mainframe->close();
		}

		$db = JFactory::getDBO();
		$query = "SELECT COUNT(*) FROM #__jav_tags WHERE name=".$db->Quote($tag);
		$db->setQuery($query);
		$result = $db->loadResult();

		if ($result>0){
			$response->set('msg',JText::_('TAG_ALREADY_EXISTS',true));
			echo $json->encode($response);
			$mainframe->close();
		}

		$row = JTable::getInstance('K2Tag', 'Table');
		$row->name=$tag;
		$row->published=1;
		$row->store();

		$cache = JFactory::getCache('com_javoice');
		$cache->clean();

		$response->set('id', $row->id);
		$response->set('status','success');
		$response->set('msg', JText::_('TAG_ADDED_TO_AVAILABLE_TAGS_LIST',true));
		echo $json->encode($response);

		$mainframe->close();

	}
	
	function checkExistTag($word, $id=0){
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		$word = trim($word);
		if($id){
			$query = "SELECT id FROM #__jav_tags WHERE name = '".$word."' AND id !='".$id."'";
		}else{
			$query = "SELECT id FROM #__jav_tags WHERE name = '".$word."'";
		}
		$db->setQuery($query);				
		return $db->LoadResult();
	}
	
	function tags(){
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
 		$word = JRequest::getString('q', null);
		$word = $db->Quote($db->getEscaped($word, true).'%', false);
		$query = "SELECT name FROM #__jav_tags WHERE name LIKE ".$word;
		$db->setQuery($query);
		
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$result = $db->loadColumn();
		}else{
			$result = $db->loadResultArray();
		}
		require_once(JPATH_COMPONENT_ADMINISTRATOR.'/lib/JSON.php');
		$json=new Services_JSON;
		echo $json->encode($result);
		$mainframe->close();
	}
	function getTagName($tagID){
		$db = JFactory::getDBO();
		$query = "SELECT name FROM #__jav_tags WHERE id ='".$tagID."'";
		$db->setQuery($query);
		return $db->loadResult();
	}
}