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

ignore_user_abort ( 1 );

define ( 'NOSHUTDOWNFUNC', 0 );

if (! NOSHUTDOWNFUNC) {
	register_shutdown_function ( 'needToRun' );
}
else {
	needToRun ();
}
function needToRun() {
	sendMailChangeStatus ();
	$timerun = getDayWeekly ( 0 );//Monday of weekday;
	writeFileCirculatory (604800,2,'weekly',$timerun);
	$timerun = mktime(0,0,0,date('m'),date('d'),date('Y'));//start today
	writeFileCirculatory (86400,3,'daily',$timerun);
	cron_change_status();	
	countItemInForum();
}
function countItemInForum(){
	$time = time();
	
	$temdata = getTempData(" AND name ='Count Items' ");

	if($time < $temdata->value)return FALSE ;
	$db= JFactory::getDBO();
	$status_spam_ids =array();
	$query = "SELECT forums_id,voice_types_id 	FROM #__jav_forums_has_voice_types";
	$db->setQuery($query);
	$items = $db->loadObjectList();
	$count = count($items);
	if($count){
		for ($i=0;$i<$count;$i++){
			$item = $items[$i];
			if(isset($status_spam_ids[$item->voice_types_id]))
				$status_spam_ids[$item->voice_types_id] = getStatusSpam_voiceType($item->voice_types_id);
			$query = "SELECT COUNT(id) FROM #__jav_items WHERE published = 1 AND forums_id=$item->forums_id AND voice_types_id=$item->voice_types_id ";
			if(isset($status_spam_ids[$item->voice_types_id]))$query.=" AND voice_type_status_id NOT IN ({$status_spam_ids[$item->voice_types_id]}) ";
			$db->setQuery($query);
			$count_item  = $db->loadResult();
			$query = " UPDATE #__jav_forums_has_voice_types SET total_items =".(int)$count_item." WHERE forums_id=$item->forums_id AND voice_types_id=$item->voice_types_id ";
			$db->setQuery($query);
			$db->query();
		}
	}
	if($temdata)
		$time = $temdata->value + 24*60*60;
	else 
		$time = mktime();
	updateNextRun(4,'Count Items',$time,$temdata);
}
function getStatusSpam_voiceType($voicety_id){
	  $model_status = JAVBModel::getInstance ( 'voicetypesstatus', 'javoiceModel' );
      $list_status = $model_status->getListTreeStatus($voicety_id,FALSE);
      
      $status_ids = array();
      foreach ($list_status as $k=>$status){
		if($status->allow_show==-1){					
			$status->allow_show = $list_status[$status->parent_id]->allow_show;					
		}
		
		if(!$status->allow_show){
			$status_ids[]=$status->id;
		}
  	
      }  
      $str = '';
      if($status_ids)$str = implode(",",$status_ids);
     return $str;
}
function getItems($where,$order){
		$db = JFactory::getDBO ();
		$sql = "SELECT *  ".
				"\n FROM #__jav_items as i ".
				"\n WHERE 1 $where" . 				
				"\n ORDER BY $order ";
		$db->setQuery ( $sql );//print_r($db->getQuery ( $sql ));exit;
		$items = $db->loadObjectList ();//print_r($db->getQuery ( $sql ));exit;

		return $items;	
}
/**
 * Enter description here...
 * default :run weekly
 * @param unknown_type $nextrun: time next run
 * @param unknown_type $id : tempdata
 * @param unknown_type $name: tempdata
 * @param unknown_type $timerun:time run
 * @return unknown
 */
function writeFileCirculatory($nextrun=604800,$id=2,$name = 'weekly',$timerun=0) {
	global $javconfig;
	$url = JURI::root() ;
	$temp = getTempData ( " AND name='$name' " );
	if ($temp) {
		$time = time ();
		if ($time > intval($temp->value) + intval($nextrun)) {
			
			//write file to send mail weekly

			$path = JPATH_COMPONENT_SITE . DS . "asset" . DS . "emails" . DS . "change";
			$filename = $path . DS . "emails_{$name}" . "_" . time () . '.ini';
			//$model_items = & JModel::getInstance ( 'items', 'javoiceModel' );
			$where = " AND i.create_date BETWEEN $temp->value AND $timerun AND  i.published=1";
			$items = getItems ( $where,' i.create_date DESC ');
			
			$model_voice_types = JAVBModel::getInstance ( 'voicetypes', 'javoiceModel' );
			$where = " AND t.published = 1 ";
			$voice_types = $model_voice_types->getItems ( $where, " t.title ", 0, 1000 );

			$users =getListUser("%{$name}=1%");
			if(!$voice_types || !$items ||!$users){
				updateNextRun($id,$name,$timerun,$temp);return FALSE;
			}
			
			$items = parseVoiceTypes ( $voice_types, $items );

			$helper = new JAVoiceHelpers ( );
			$header = $helper->getEmailTemplate ( "mailheader" );
			$footer = $helper->getEmailTemplate ( "mailfooter" );
			$email="Javnotify_to_user_new_voice_{$name}";
			$mail = $helper->getEmailTemplate ( $email );
			
			$mailcontent = $header->emailcontent . "\n" . $mail->emailcontent . "\n\n" . $footer->emailcontent;
			
			$filters = $helper->getFilterConfig ();
			$details = "";
			$details.= '<UL>';
			foreach ( $voice_types as $voice_type ) {
				if($items [$voice_type->id]->list){
					
					$details.="<LI>";
					$details.="<STRONG>".$voice_type->title."</STRONG>" ;
					$details.="<OL>";				
					foreach ( $items [$voice_type->id]->list as $item ) {
						$details.="<LI>";
						$link=$helper->getLink(JRoute::_("index.php?option=com_javoice&view=items&layout=item&cid=$item->id&type=$item->voice_types_id&forums=$item->forums_id"));
						$details .="<a href=\"$link\">".  $item->title."</a>";
						$integrate = $javconfig['integrate']?$javconfig['integrate']->get('run_system',''):'';
						
						$details .="<span style='color:#7F7F7F'>(";
						$details .=JText::_("CREATE_DATE").": ".date('d/M/Y',$item->create_date);
						$params = class_exists('JRegistry')? new JRegistry($item->data) : new JParameter($item->data);
						$system_total = $integrate."_total";
						if($params->get($system_total)){
							$details .=", ".JText::_("TOTAL_COMMENTS").": ";
							$details .=$params->get($system_total);
						}
						$details .=")</span>"	;									
					}				
					$details.="</OL>";
				}
			}
			$details.= "</UL>";
			$filters['{ITEM_DETAILS}']=$details;
			
			if (is_array ( $filters )) {
				foreach ( $filters as $key => $value ) {
					$subject = str_replace ( $key, $value, $mail->subject );
					$mailcontent = str_replace ( $key, $value, $mailcontent );
				}
			}
			$mailcontent = str_replace ( "\n", "###", $mailcontent );
			$content [] = "mailcontent=" . $mailcontent;
			$content [] = "subject=" . $subject;
			$user = implode ( ",", $users );
			$content [] = "user_id={$user}";
			$contents = implode ( "\n", $content );
			$model_sendmail = JAVBModel::getInstance ( 'sendmail', 'javoiceModel' );
			$model_sendmail->writeLogFileChange ( $contents, $filename );
			
			updateNextRun($id,$name,$timerun,$temp);
		}
	}

	return TRUE;
}
function updateNextRun($id,$name,$timerun,$temp = null){
	$db = JFactory::getDBO();
	$temp_new = new stdClass ( );
	$temp_new->id = $id;
	$temp_new->name = $name;
	$temp_new->value = $timerun;
	if (! $temp) {
		$db->insertObject ( "#__jav_temp_data", $temp_new );
	}
	else {
		$db->updateObject ( "#__jav_temp_data", $temp_new, 'id' );
	}	
}
function getListUser($where = '%weekly=1%'){
	$db = JFactory::getDBO();
	$query = " SELECT id FROM #__users WHERE params LIKE '$where' ";
	$db->setQuery($query);
	if (version_compare(JVERSION, '3.0', 'ge'))
	{
		return $db->loadColumn();
	}else{
		return $db->loadResultArray();
	}
}
function parseVoiceTypes($voice_types, $items) {
	$temps = array ();
	foreach ( $voice_types as $voice_type ) {
		foreach ( $items as $item ) {
			if ($item->voice_types_id == $voice_type->id) {
				$temps [$voice_type->id]->list [] = $item;
			}
		}
	}
	return $temps;
}
function getTempData($where = '') {
	$db = JFactory::getDBO ();
	$query = "SELECT * FROM #__jav_temp_data WHERE 1 $where LIMIT 1";
	$db->setQuery ( $query );
	return $db->loadObject ();
}
function setTempData($where, $fiels, $values) {
	$db = JFactory::getDBO ();
	$query = "UPDATE #__jav_temp_data($fiels)value('$values') WHERE $where ";
	$db->setQuery ( $query );
	return $db->query ();
}
function getDayWeekly($numdate = 0) {
	//-----------------------------
	$now = time ();
	$num = date ( "w" );
	if ($num == $numdate) {
		$sub = 0;
	}
	else {
		$sub = $num - $numdate;
	}
	$WeekMon = mktime ( 0, 0, 0, date ( "m", $now ), date ( "d", $now ) - $sub, date ( "Y", $now ) ); //monday week begin calculation
	return $WeekMon;
}
function sendMailChangeStatus() {
	$delete =FALSE;
	$model_sendmail = JAVBModel::getInstance ( 'sendmail', 'javoiceModel' );
	if ($model_sendmail->checkIn ()) {
		$path = JPATH_COMPONENT_SITE . DS . "asset" . DS . "emails" . DS . "change";
		$params = $model_sendmail->readFile ( $path );
		if ($params) {
			$user_id = $params->get ( 'user_id', '' );
			if ($user_id != '') $user_ids = explode ( ',', $user_id );

			if ($user_ids) {
				$count = count ( $user_ids );
				if ($count > 0) {
					
					$helper = new JAVoiceHelpers ( );
					$subject = $params->get ( 'subject', '' );
					$mailcontent = $params->get ( 'mailcontent', '' );
					$mailcontent = str_replace("###","\n",$mailcontent);
					$n = 100;
					if (100 >= $count) {
						$n = $count;
						$delete=TRUE;
					}
					$del_user_ids=array();
					$link = '';
					for($i = 0; $i < $n; $i ++) {
						$del_user_ids[$i] = $user_ids [$i];
						$user = JFactory::getUser ( $user_ids [$i] );
						$email = $user->email;
						$nameto = $user->username;
						$filters = array ();
						$filters ['{USERS_USERNAME}'] = $user->username;						
						$filters ['{USERS_EMAIL}'] = $user->email;
						$link.=$helper->getLink(JRoute::_("index.php?option=com_javoice&view=users&uid=".$user->id."&tab=2"));										
						$filters['{EMAIL_PREFERENCE_LINK}'] = "<a href=\"$link\">".JText::_("TURN_OFF_OR_EDIT_YOUR_EMAIL_NOTIFICATIONS")."<a>";
						$helper->sendmail ( $email, $nameto, $subject, $mailcontent, $filters,'','','', FALSE );
					}
				}else $delete=TRUE;
			
			}else $delete=TRUE;
		}else $delete=TRUE;
		if($delete){
			$model_sendmail->deleteFile();
		}else{
			$new_user = array_diff($user_ids,$del_user_ids);
			$new_user = implode(",",$new_user);
			$params->set('user_id',$new_user);
			$content = $params->toString();
			$model_sendmail->writeLogFileChange($content);
		}
	}

}

function cron_change_status() {
	global $javconfig;
	$total_vote_spam = $javconfig ['systems'] ? $javconfig ['systems']->get ( 'total_vote_spam', 30 ) : 30;
	if ($total_vote_spam) {
		$model_items = JAVBModel::getInstance ( 'items', 'javoiceModel' );
		$model_status = JAVBModel::getInstance ( 'voicetypesstatus', 'javoiceModel' );
		$model_actionslog = JAVBModel::getInstance ( 'actionslog', 'javoiceModel' );
		
		$where = " AND (i.number_spam + i.number_duplicate + i.number_inapproprivate) >= $total_vote_spam ";
		$items = $model_items->getItems ( $where, ' i.id', 0, 1000 );
		$count = count ( $items );
		if ($count > 0) {
			for($i = 0; $i < $count; $i ++) {
				$item = $items [$i];
				$status_old = $model_status->getItem ( $item->voice_type_status_id );
				$status_id = $javconfig ['systems'] ? $javconfig ['systems']->get ( 'status_spam_' . $item->voice_types_id, 0 ) : 0;
				$status = changeStatus ( $item, $status_id, $model_status );
				if ($status) {
					changeLog ( $item->voice_types_id, $item->id, $model_items );
					$details = JText::_("CHANGE_THE_STATUS_OF_VOICES" );
					if ($status_old) $details .= " " . JText::_("FROM" ) . " " . $status_old->title;
					$details .= " " . JText::_("TO" ) . " " . $status->title;
					$model_actionslog->makeLog ( - 1, 'Report spam', $details, $item->id,time());
				}
			}
		}
	}
}
function changeLog($voicetype_id, $item_id, $model_items) {
	$model_voicetype = JAVBModel::getInstance ( 'voicetypes', 'javoiceModel' );
	$voicetype = $model_voicetype->getItem ( $voicetype_id );
	
	$logs = $model_items->getLogs ( " and item_id={$item_id}" );
	if ($logs) {
		if ($voicetype->total_votes > - 1) {
			foreach ( $logs as $log ) {
				$user = JFactory::getUser ( $log->user_id );
				$votes_left = ( int ) $user->getParam ( 'votes_left-' . $voicetype->id );
				
				$user->setParam ( 'votes_left-' . $voicetype->id, $votes_left + abs ( $log->votes ) );
				$user->save ();
			}
		}
		$model_items->clearLogs ( " and item_id={$item_id}" );
	}
}
function changeStatus($item, $status_id, $model_status) {
	if ($status_id) {
		$db = JFactory::getDBO ();
		$status = $model_status->getItem ( $status_id );
		if ($status) {
			$item->voice_type_status_id = $status_id;
			$item->number_spam = 0;
			$item->number_duplicate = 0;
			$item->number_inapproprivate = 0;
			if ($db->updateObject ( '#__jav_items', $item, 'id' )) {
				return $status;
			}
		}
	}
	return FALSE;
}
