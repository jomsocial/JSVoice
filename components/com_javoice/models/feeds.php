<?php

defined ( '_JEXEC' ) or die ( 'Restricted access' );

jimport ( 'joomla.application.component.model' );

class JAVoiceModelfeeds extends JAVBModel {
	var $_db = NULL;
	function __construct()
	{
		$this->_db = JFactory::getDBO();
		parent::__construct();
		
	}	
	function getItem($id = 0) {
		$table = $this->getTable ('','Table');
		if (! $id) {
			$cid = JRequest::getVar ( 'cid', array (0 ), '', 'array' );
			JArrayHelper::toInteger ( $cid, array (0 ) );
			
			if (isset ( $cid [0] ) && $cid [0] > 0) {
				$id = $cid [0];
			}
		}
		
		if ($id) {
			$table->load ( $id );
		}
		
		return $table;
	}
	function getvar()
	{
		$mainframe = JFactory::getApplication();
		$list = array();
		$option='feeds';
		$list['filter_order'] 		= $mainframe->getUserStateFromRequest( $option.'viewarchive.filter_order',	'filter_order',	'f.feed_last_update',	'post' );	
		$list['filter_order_Dir'] 	= $mainframe->getUserStateFromRequest( $option.'viewarchive.filter_order_Dir',	'filter_order_Dir',	'DESC',				'word' );

		$list['limit'] 				= Jrequest::getInt('limit',20);
		$list['limitstart'] 		= Jrequest::getInt('limitstart',0);

		$list['search'] 			= $mainframe->getUserStateFromRequest( $option.'.search',						'search',			'',				'string' );

		return  $list;
	}
	function getWhere($lists){
		$search				= $lists['search'];
		$search	= JString::strtolower( $search );
		$where = ' ';		
		if ($search) {
			$where .= ' AND LOWER(f.title) LIKE '.$this->_db->Quote( '%'.$this->_db->getEscaped( $search, true ).'%', false );
		}		
	}	
	function getItems($where = '', $orderby = '', $limitstart = 0, $limit = 0, $fields = '', $joins = '') {		
		$db = JFactory::getDBO ();
		$query = " SELECT f.* ";
		if ($fields)
			$query .= " ,$fields ";
		$query .= " FROM #__jav_feeds as f ";
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
	function getTotal($where = '', $joins = '') {
		$db = JFactory::getDBO ();
		$query = " SELECT COUNT(f.id) ";
		$query .= " FROM #__jav_feeds as f ";
		if ($joins)
			$query .= " $joins ";
		$query .= " WHERE 1 $where ";
		
		$db->setQuery ( $query );
		
		return $db->loadResult ();
	}
	function getPagination(&$lists, $total) {
		if ($lists ['limit'] == 0)
			$lists ['limit'] = $total > 100 ? 100 : $total;
		elseif ($lists ['limit'] >= $total)
			$lists ['limitstart'] = 0;
		jimport ( 'joomla.html.pagination' );
		$pagination = new JPagination ( $total, $lists ['limitstart'], $lists ['limit'] );
		return $pagination;
	}
	
	function getWhereClause($feed) {
		
		$where = " AND i.published = 1";
		if($feed->filter_forums_id)
			$where .= " AND i.forums_id IN ($feed->filter_forums_id)";
		if($feed->filter_voicetypes_id){
			$modelvoicetypesstatus = JAVBModel::getInstance ( 'voicetypesstatus', 'javoiceModel' );
			$filter_voicetypes_id = explode(",", $feed->filter_voicetypes_id);
			$voice_types_id = array();
			foreach ($filter_voicetypes_id AS $vi){
				$parseItems = $modelvoicetypesstatus->getItemsDyamic("s.*"," AND s.voice_types_id=$vi");
				
				if(count($parseItems)>0){
					foreach($parseItems AS $pi){
						if($pi->allow_show == 1){
								array_push($voice_types_id, $pi->id);
						}
						else if($pi->parent_id >0){
							$pi_parent = $modelvoicetypesstatus->getItem($pi->parent_id);
							if($pi->allow_show == 1){
								array_push($voice_types_id, $pi->id);
							}else if($pi->allow_show == -1 && $pi_parent->allow_show == 1){
								array_push($voice_types_id, $pi->id);
							}
						}	
					}
				}
			} 
			
			$voice_types_ids = '';
			if(count($voice_types_id)>0){
				$voice_types_ids = implode(",", $voice_types_id);
			}
			$where .= " AND s.id IN ($voice_types_ids)";
		}
		if ($feed->filter_item_ids)
			$where .= " AND i.id IN ($feed->filter_item_ids  )";
		if ($feed->filter_creator_ids)
			$where .= " AND i.user_id IN ($feed->filter_creator_ids  )";
		if(isset($feed->filter_date)&&intval($feed->filter_date)>0){
			$time = time()-($feed->filter_date * 24 * 60 * 60);
			$where .=" AND i.create_date > ".(int) $time;
		}
		
		
		$where.=" AND s.published = 1 ";
		if($feed->filter_status==0){
			$where.=" AND s.allow_voting = 1";
		}elseif($feed->filter_status==1){
			$where.=" AND s.allow_voting = 0 ";
		}
		
		return $where;
	
	}
	
	function storeFeed() {		
		$feed = $this->getItem ();
		$user = JFactory::getUser();		
		
		$post = JRequest::get ( 'request', JREQUEST_ALLOWHTML );
		$authorisedViewLevels = $user->getAuthorisedViewLevels();		
		if (is_array ( $post ['filter_forums_id'] )) {
			if (in_array ( "-1", $post ['filter_forums_id'] )){
  				$model_forums = JAVBModel::getInstance('forums','javoiceModel');				
				$where = " AND f.published =1 ";
				
				foreach($authorisedViewLevels as $gkey=>$gVal){
					$gid[] = $gVal;	
					if($where == " AND f.published =1 "){
						$where .= " AND (f.gids_view LIKE '%0%' OR f.gids_view LIKE '%$gVal%'";
					}else{
						$where .= " OR f.gids_view LIKE '%$gVal%'";
					}
				}
				if($where != " AND f.published =1 ") $where .=")";								
  				$forums_id = $model_forums->getDyamicItems($where,0,0,'','f.id');
  				if(!$forums_id){
					JError::raiseWarning ( 1001, JText::_("CAN_NOT_FORUM" ) );
					return FALSE;  								
  				}
				$post ['filter_forums_id'] = implode(",",$forums_id);
			}
			else
				$post ['filter_forums_id'] = implode ( ',', $post ['filter_forums_id'] );
		}
		if (is_array ( $post ['filter_voicetypes_id'] )) {
			if (in_array ( "-1", $post ['filter_voicetypes_id'] )){
				
				$model_voice_types = JAVBModel::getInstance('voicetypes','javoiceModel');	
				$joins = "	INNER JOIN #__jav_forums_has_voice_types as fv ON fv.voice_types_id = t.id
						 	INNER JOIN #__jav_forums as f ON f.id = fv.forums_id";
				
				$where = " AND t.published=1 AND f.gids_view LIKE '%$gid%' AND f.published =1";
				
				$where = " AND t.published=1 ";				
				foreach($authorisedViewLevels as $gkey=>$gVal){					
					if($where == " AND t.published=1 "){
						$where .= " AND (f.gids_view LIKE '%0%' OR f.gids_view LIKE '%$gVal%'";
					}else{
						$where .= " OR f.gids_view LIKE '%$gVal%'";
					}
				}
				if($where != " AND t.published=1 ") $where .=") AND f.published =1 ";
								
				$voice_types_id = $model_voice_types->getDyamicItems($where,'',0,0,'DISTINCT t.id',$joins);				
			  	if(!$voice_types_id){
					JError::raiseWarning ( 1001, JText::_("CAN_NOT_VOICE_TYPE" ) );
					return FALSE;  								
  				}				
				$post ['filter_voicetypes_id'] = implode(",",$voice_types_id);
			}
			else
				$post ['filter_voicetypes_id'] = implode ( ',', $post ['filter_voicetypes_id'] );
		}
		if (! $feed->bind ( $post )) {
			JError::raiseWarning ( 1001, JText::_("BIND_DATA_FALSE" ) );
			return FALSE;
		}
		
		$errors = $feed->check ();
		if ($errors) {
			JError::raiseWarning ( 1001, implode ( "<br>", $errors ) );
			JRequest::setVar ( 'feed', $feed );
			return FALSE;
		}
		if (! $feed->feed_alias)
			$feed->feed_alias = JFilterOutput::stringURLSafe ( $feed->feed_name );
			
		$where = " AND (f.feed_name='$feed->feed_name' OR f.feed_alias = '$feed->feed_alias') AND f.user_id=$user->id";
		
		if ($feed->id)
			$where .= " AND f.id!=$feed->id ";
		$feeds = $this->getItems ( $where );
		if (count ( $feeds ) > 0) {
			JError::raiseWarning ( 1001, JText::_("TITLE_OR_ALIAS_IS_ALREADY_EXIST" ) );
			JRequest::setVar ( 'feed', $feed );
			return FALSE;
		}
		
		$feed->feed_last_update = time ();
		$feed->user_id = $user->id;
		if (! $feed->store ()) {
			JError::raiseWarning ( 1001, JText::_("FAILURE_TO_SAVE_FEED" ) );
			JRequest::setVar ( 'feed', $feed );
			return FALSE;
		}
		
		return $feed->id;
	}
	function getContentRss($feed, $items) {
		$mainframe = JFactory::getApplication();
		$rss = new stdClass ( );
		if (isset ( $feed->feed_alias ))
			$rss->id = $feed->feed_alias;
		else
			$rss->id = time ();
		
		$rss->feed_title = htmlspecialchars ( $feed->feed_name );
		$rss->feed_alias = $feed->feed_alias;
		$rss->feed_description = $feed->feed_description;
		$rss->feed_link = JURI::root ();
		
		/** structure rss*/
		$rss->feed_type = $feed->feed_type;
		$rss->feed_cache = intval ( $feed->feed_cache ) > 0 ? 1 : 0;
		;
		/** source temps*/
		$rss->filename = JPATH_COMPONENT . "/views/feeds/feeds/feeds" . $rss->id . ".xml";
		/** image */
		$rss->image_title = $mainframe->getCfg ( 'sitename' );
		$rss->image_url = isset($feed->image_url)?$feed->image_url:'';
		$rss->image_href = JURI::root ();
		$rss->image_description = $mainframe->getCfg ( 'sitename' );
		$rss->rsscontents = array ();
		
		if ($items) {
			foreach ( $items as $item ) {
				$row = new stdClass ( );
				$row->title = htmlspecialchars ( $item->title );
				$row->link = JRoute::_ ( JURI::root () . 'index.php?option=com_javoice&view=items&layout=item&cid=' . $item->id . '&type=' . $item->voice_types_id . '&forums=' . $item->forums_id );
				$row->guid = $row->link;
				$row->content = $item->content;
				$row->date = null;
				if(isset($item->created_date)){
					$itemDate = JFactory::getDate ( JHTML::_ ( 'date', $item->created_date, JText::_('DATE_FORMAT_LC2' ) ) );
					$row->date = $itemDate->toUnix ();
				}				
				$row->soure = JURI::root ();
				$row->user_id = $item->user_id;
				$rss->rsscontents [] = $row;
			}
		}
	
		$rss->feed_numWords = $feed->msg_numWords > 0 ? $feed->msg_numWords : 10000;
		return $rss;
	}
	function getRssType($selected = '') {
		//rss type list
		$rssType = array();
		$rssType [] = JHTML::_ ( 'select.option', '0.91', 'RSS 0.91' );
		$rssType [] = JHTML::_ ( 'select.option', '1.0', 'RSS 1.0' );
		$rssType [] = JHTML::_ ( 'select.option', '2.0', 'RSS 2.0' );
		$rssType [] = JHTML::_ ( 'select.option', "MBOX", "MBOX" );
		$rssType [] = JHTML::_ ( 'select.option', "OPML", "OPML" );
		$rssType [] = JHTML::_ ( 'select.option', "ATOM", "ATOM" );
		$rssType [] = JHTML::_ ( 'select.option', "ATOM0.3", "ATOM 0.3" );
		$rssType [] = JHTML::_ ( 'select.option', "HTML", "HTML" );
		$rssType [] = JHTML::_ ( 'select.option', "JS", "JS" );
		$lists = JHTML::_ ( 'select.genericlist', $rssType, 'feed_type', 'class="inputbox"', 'value', 'text', $selected, 'feed_type' );
		return $lists;
	}
	function getItemsStatus($selected = '') {
		//rss type list
		$itemsStatus = array();
		$itemsStatus [] = JHTML::_ ( 'select.option', '2',JText::_('ALL_STATUS' ) );
		$itemsStatus [] = JHTML::_ ( 'select.option', '0', JText::_('OPEN' ) );
		$itemsStatus [] = JHTML::_ ( 'select.option', '1', JText::_('COMPLETED' ) );
		$lists = JHTML::_ ( 'select.genericlist', $itemsStatus, 'filter_status', 'class="inputbox"', 'value', 'text', $selected, 'filter_status' );
		return $lists;
	}	
	function getLists(&$lists,$feed){

		$numWords[] = JHTML::_('select.option','0','All');
		for ($i=25;$i<=250;$i+=25) {
			$numWords[] = JHTML::_('select.option',$i,$i);
		}
		$lists['numWordsList'] = JHTML::_('select.genericList', $numWords, 'msg_numWords', 'class="inputbox"','value', 'text', $feed->msg_numWords, 'msg_numWords' );
		
		$authorformats[] = JHTML::_( 'select.option', 'NAME','Name Only');
		$authorformats[] = JHTML::_( 'select.option', 'EMAIL','Email Only');
		$authorformats[] = JHTML::_( 'select.option', 'NAME&EMAIL','Name and Email');
		$lists['renderAuthorList'] = JHTML::_('select.genericList', $authorformats, 'feed_renderAuthorFormat', 'class="inputbox"','value', 'text',$feed->feed_renderAuthorFormat, 'feed_renderAuthorFormat' );
		
		$renderHTML[] = JHTML::_( 'select.option', '1','Yes');
		$renderHTML[] = JHTML::_( 'select.option', '0','No');
		$lists['renderHTMLList'] =JHTML::_( 'select.genericList',$renderHTML, 'feed_renderHTML', 'class="inputbox"','value', 'text',$feed->feed_renderHTML , 'feed_renderHTML');
		
		
		$renderImages[]   = JHTML::_( 'select.option', "1","Yes");
		$renderImages[]   = JHTML::_( 'select.option', "0","No");
		$lists['renderImagesList'] = JHTML::_( 'select.genericList', $renderImages, 'feed_renderImages', 'class="inputbox"','value', 'text',$feed->feed_renderImages );
	
		$renderPubl[] = JHTML::_( 'select.option', "1","Yes");
		$renderPubl[] = JHTML::_( 'select.option', "0","No");
		$lists['renderPublishedList'] = JHTML::_( 'select.genericList', $renderPubl, 'published', 'class="inputbox"','value', 'text', $feed->published);		
	}
	function getForumsIds($gid){		
		$filter_forums_title = JRequest::getVar('filter_forums_title',NULL);			
		$model_forums = JAVBModel::getInstance('forums','javoiceModel');
		$where = " AND f.gids_view LIKE '%$gid%' AND f.published =1";
		if($filter_forums_title){
			$temp = str_replace(",","','",$filter_forums_title);
			$where .=" AND f.title IN ('".$temp."')";				
		}
		$forums = $model_forums->getDyamicItems($where,0,0,'','f.id');
		if($forums){
			return implode(",",$forums);
		}else{
			return 0;
		}	
	}
	function getVoiceTypesIds($gid){
		$filter_voicetypes_title = JRequest::getVar('filter_voicetypes_title',NULL);		
		
		$model_voice_types = JAVBModel::getInstance('voicetypes','javoiceModel');
		$joins = "	INNER JOIN #__jav_forums_has_voice_types as fv ON fv.voice_types_id = t.id
				 	INNER JOIN #__jav_forums as f ON f.id = fv.forums_id";
		
		$where = " AND t.published=1 AND f.gids_view LIKE '%$gid%' AND f.published =1";
			if($filter_voicetypes_title){
			$temp = str_replace(",","','",$filter_voicetypes_title);
			$where .=" AND t.title IN ('".$temp."')";				
		}
		$voice_types = $model_voice_types->getDyamicItems($where,'',0,0,'DISTINCT t.id',$joins);
		if($voice_types){
			return implode(",",$voice_types);
		}else return 0;	
	}
	function dopublish($publish){
		$db		= JFactory::getDBO();
		
		$ids = JRequest::getVar('cid', array());		
		$ids = implode( ',', $ids );
		
		$query = "UPDATE #__jav_feeds"
		. " SET published = " . intval( $publish )
		. " WHERE id IN ( $ids )"
		;
		$db->setQuery( $query );
		if (!$db->query()) {
			return false;
		}
		return true;		
	}
	function remove(){
		$db = JFactory::getDBO ();
		$cids = JRequest::getVar ( 'cid', null, 'request', 'array' );
		if(!$cids)return FALSE;
		$cid = implode(",",$cids);
		$query = "DELETE FROM #__jav_feeds WHERE id IN ($cid)";
		$db->setQuery ( $query );
		if (! $db->query ())
			return FALSE;
		return TRUE;
	}
}
?>