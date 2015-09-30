<?php
/*
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

class JAVoiceControllerItems extends JAVFController {
	var $_totalc = 0;
	public function __construct($default = array()) {
		parent::__construct ( $default );
		require_once (JPATH_SITE.DS.'components'.DS.'com_javoice'.DS.'helpers'.DS.'jacaptcha'.DS.'jacapcha.php');
		$this->registerTask ( 'duplicate', 'spam' );
		$this->registerTask ( 'Inappropriate', 'spam' );
	}
	
	public function display($cachable = false, $urlparams = false) {
		parent::display ($cachable = false, $urlparams = false);
	}
	
	public function getItems() {
		global $javconfig;
		$mainframe = JFactory::getApplication();
		// Get the page/component configuration
		$params = $mainframe->getParams ();
		
		// parameters
		$gl_cid = $params->def ( 'item_id', '' );
		$gl_uid = $params->def ( 'user_id', '' );
		
		$model = $this->getModel ( 'items' );
		$user = JFactory::getUser ();		
		$lists = $model->_getVars ();//print_r($lists);exit;
		$type = $model->getVoiceType ( JRequest::getInt ( 'type', 1 ) );
		
		if(JRequest::getVar('layout', 'default')=='item'){
			$lists['limit'] = 1;
		}
		
		$where_more = '';
		$order = '';
		if (isset ( $lists ['order'] ) && $lists ['order'] != '') {
			if ($lists ['order'] == 'create_date desc') {				
				if($javconfig["systems"]->get('is_set_time_new_voice', 1)){
					$lagNewVoice = $javconfig["systems"]->get('time_for_new_voice', 7200); 					
					$where_more .= ' and (i.create_date +'.$lagNewVoice.') >='.time();
				}else{
					$where_more .= ' and i.create_date>=' . $_SESSION ['JAV_LAST_VISITED'];
				}
			}
			$order = $lists ['order'] . ' ' . @$lists ['order_Dir'];
		}
				
		if (JRequest::getInt ( 'type' )) {
			$where_more .= " and i.voice_types_id='" . JRequest::getInt ( 'type' ) . "'";
		}
		
		if (JRequest::getInt ( 'forums' )) {
			$where_more .= " and i.forums_id='" . JRequest::getInt ( 'forums' ) . "'";
		}
		
		if (JRequest::getVar ( 'forums_id' )) {			
			if(JRequest::getVar ( 'forums_id' ) == "no_forum"){				
				return false;
			}else{
				$where_more .= " and i.forums_id in (" . JRequest::getVar ( 'forums_id' ) . ")";	
			}
		}
		
		if (JRequest::getInt ( 'uid' ) && JRequest::getVar ( 'view' ) == 'users') {
			$where_more .= ' and i.user_id=' . JRequest::getInt ( 'uid' );
		} elseif (intval ( $gl_uid )) {
			$where_more .= ' and i.user_id=' . ( int ) $gl_uid;
		}
		
		/* BEGIN: Show items are activing Only */
		$model_status = JAVBModel::getInstance ( 'voicetypesstatus', 'javoiceModel' );
		$list_status = $model_status->getListTreeStatus ( 0, false, 0 );
		$model->getWhereWidget ( $where_more );
		$status = JRequest::getVar ( 'status', NULL );		
		if ($status) {
			if (is_numeric ( $status ))
				$where_more .= " AND  i.voice_type_status_id='" . ( int ) $status . "' ";
			else {
				$temps = explode ( ",", $status );
				$statusStr = array ();
				foreach ( $temps as $temp ) {
					$statusStr [] = "s.title LIKE '%$temp%'";
				}
				$where_more .= count ( $statusStr ) ? ' AND ( ' . implode ( ' OR ', $statusStr ) . ")" : '' . ")";
			}
		} else {
			if (JRequest::getString ( 'key', '' ) == '') {
				$status_ids = array ();
				foreach ( $list_status as $k => $status ) {
					if (($status->parent_id != 0 && ! $model_status->is_spam ( '', $status )) || JRequest::getWord ( 'layout' ) == 'item') {
						$status_ids [] = $status->id;
					}
				}
			} else {
				$status_ids = array ();
				foreach ( $list_status as $k => $status ) {
					if (($status->parent_id > 0 && (! $model_status->is_spam ( '', $status ) || $model_status->is_closed ( '', $status ))) || JRequest::getWord ( 'layout' ) == 'item') {
						$status_ids [] = $status->id;
					}
				}
			}
			if (! $status_ids)
				$status_ids = array (0 );
			$where_more .= " and ( i.voice_type_status_id in (" . implode ( ',', $status_ids ) . ") or i.voice_type_status_id=0)";
		}
		/* END: Show items are activing Only */
		
		if ($cid = JRequest::getVar ( 'cid', array (), '', 'array' )) {
			JArrayHelper::toInteger ( $cid );
			if ($cid) {
				$cid = implode ( ',', $cid );
				$where_more .= " and i.id in ($cid)";
			}
		} elseif (intval ( $gl_cid )) {
			$where_more .= " and i.id=" . ( int ) $gl_cid;
		}
		$where_more .= ' and i.published=1';
		if($javconfig["systems"]->get("is_enable_tagging", 0) && JRequest::getVar("tagid",0)){
			$where_more .= " and tv.tagID='".JRequest::getVar("tagid",0)."'";
		}		
			
		$join = " LEFT JOIN #__jav_voice_type_status as s ON s.id=i.voice_type_status_id";
		$fields_join = ' s.title as status_title, s.class_css as status_class_css, s.allow_voting as status_allow_voting, s.parent_id as status_parent_id, s.published as status_publishded';
		
		if($javconfig["systems"]->get("is_enable_tagging", 0) && JRequest::getVar("tagid",0)){			
			$join .= " INNER JOIN #__jav_tags_voice as tv ON i.id=tv.voiceID";
			$fields_join .= ", tv.tagID";			
		}
		
		if ($user->id) {
			$fields_join .= ', lg.votes';
			$join .= " LEFT JOIN #__jav_logs as lg ON (lg.item_id=i.id and lg.user_id='$user->id')";
		}
		if(JRequest::getVar("pagingtype") == "autoscroll"){
			//page
			$next_page = (int)JRequest::getVar("javpage");
			$lists ['limitstart'] = $next_page*$lists ['limit'];			
		}
		$items = $model->getItems ( $where_more, trim ( $order ), $lists ['limitstart'], $lists ['limit'], $fields_join, $join );
		$this->_totalc = $model->getCurrentTotal();
		if($items){		
		$items = $model->parseItems_params ( $items, $type);
		//print_r($items);exit;
		foreach ($items as $k=>$item){
			//if($javconfig['plugin']->get('is_attach_image',1)){
				$path = JPATH_ROOT.DS."images".DS."stories".DS."ja_voice".DS.$item->id;
				$item->attachs[] = $model->formatFilesInDir($path,'download',$item->user_id,$item->id);
			//}			
			
			$helper = new JAVoiceHelpers();
			$item->avatar = $helper->getAvatar($item->user_id);
			
			if($item->user_id){
				$userinfo 			= JFactory::getUser($item->user_id);
				$item->username 	= $userinfo->username;
				$item->userparams 	= $userinfo->getParameters();	
				
				$items[$k] = $item;
			}
		}
		}
		return $items;
	}
	function currentTotal(){
		return $this->_totalc;
	}
	
	function deletereply(){
		if(!JAVoiceHelpers::checkPermissionAdmin()) return;
		
		$itemID 	= JRequest::getInt("item_id");
		$responeid	= JRequest::getInt("responeid");
		$model = $this->getModel ( 'items' );
		$model->delete_admin_response($responeid);
		
		$helper = new JAVoiceHelpers ( );
				
		$file_path =  JPATH_ROOT.DS."images".DS."stories".DS."ja_voice".DS."admin_response".DS.$responeid;					
		if (is_dir($file_path)) {
			jimport( 'joomla.filesystem.folder' );
			JFolder::delete($file_path);

		}															    					  				
		
		$object= new stdClass ( );
		$object->id = '#jav-box-item-'.$itemID.' .jav-item-response';
		$object->attr = 'html';
		$object->content = '<div class="jav-response-text" style="display: none;"></div><div class="jav-upload-form" style="display: none;"></div><span class="add-response" id ="jav-add-respone-'.$itemID.'">
						    	<a id="link-response-'.$itemID.'" onclick="return show_frm_response(\'#link-response-'.$itemID.'\', '.$itemID.')" href="javascript:void(0)" class="inline-edit-prompt">'.JText::_('ADD_RESPONSE').'</a>
						    </span><input type="hidden" id="jav-content-respone-'.$itemID.'" value=""/>
						    <div id="jav-container-response-'.$itemID.'" style="display: none;"></div>
						    <div id="jav-form-response-'.$itemID.'" style="display: none;">	  
							 	<input type="button" onclick="jav_submit_admin_response('.$itemID.', 0);" value="'.JText::_('SAVE').'" name="commit" id="bestanswer-commit-'.$itemID.'"/>
							    <input type="button" value="'.JText::_('CANCEL').'" name="cancel" onclick="hide_frm_response(\'#link-response-'.$itemID.'\', '.$itemID.', 0)"/> 
					      </div>';
		$objects[]=$object;
						
		$helper = new JAVoiceHelpers ( );		
		echo $helper->parse_JSON_new ( $objects );
		exit ();		
	}
	
	/**
	 * Save item record
	 */
	function save() {
		global $javconfig;
		// Check for request forgeries
		//JRequest::checkToken() or jexit( 'Invalid Token' );
		$Itemid = JRequest::getInt ( 'Itemid' );		
		if(!$Itemid) $Itemid = JAVoiceHelpers::get_Itemid(array('option'=>'com_javoice', 'view'=>'items'));		
		$cid = JRequest::getVar ( 'cid', array (), '', 'array' );
		$model = $this->getModel ( 'items' );
		$user = JFactory::getUser();
		if($cid){					
			if(!JAVoiceHelpers::checkPermissionAdmin()){
				$isAllowRegisterEdit = 0;
				if($javconfig["systems"]->get("is_edit_delete_voice",0)){
					$item = $model->getItem();								
					$userEId = $user->get( 'id' );
					if($userEId == $item->user_id){
						$timeE = $javconfig["systems"]->get("time_for_edit_voice", 900);											
						if($timeE != -1 || time() < ($item->create_date+$timeE)){
							$isAllowRegisterEdit = 1;
						}
					}	
				}
				if(!$isAllowRegisterEdit) return;
			}			
		}		
		
		JArrayHelper::toInteger ( $cid );
		$helper = new JAVoiceHelpers ( );	
		
		$post = JRequest::get ( 'request' );
		if($post['title']){
			$post['title'] = trim($post['title']);
		}
		$lang = JFactory::getLanguage();
		$langName = $lang->getName();
//		if(strpos("English", $langName) !== false){
//			$post["title"] = $helper->addSpaceInLongTitle($post["title"]);	
//		}		
		// allow name only to contain html
		if(JRequest::getVar("javNameOfTextarea","newVoiceContent")=="newVoiceContentReply"){
			$post ['content'] = JRequest::getString ( 'newVoiceContentReply', '' );
		}else{
			$post ['content'] = JRequest::getString ( 'newVoiceContent', '' );
		}
		$post ['content'] = $helper->removeEmptyBBCode($post ['content']);		
		$post ['voice_types_id'] = JRequest::getInt ( 'type' );
		$post ['forums_id'] = JRequest::getInt ( 'forums_id' );
		$model->setState ( 'request', $post );
		$objects = array ();
		if(!$cid)				
			$this->checkDataPhp($objects);
		
		$row = $model->store ();
		
		if (isset ( $row->id )) {
			if($javconfig['plugin']->get("is_attach_image", 0)){
				//delete file in store image if remove file
				jimport( 'joomla.filesystem.folder' );
				jimport('joomla.filesystem.file');
					
				$listFile = JRequest::getVar('listfile', 0);
				
				$file_path 			 =  JPATH_ROOT.DS."images".DS."stories".DS."ja_voice".DS.$row->id;
				
				$listFileOfComments  =  JFolder::files($file_path);																			
				
				if($listFileOfComments){
					foreach ($listFileOfComments as $listFileOfComment){
						if($listFile){
							if(!in_array($listFileOfComment, $listFile)){
								JFile::delete($file_path.DS.$listFileOfComment);									
							}
						}else{	
							JFile::delete($file_path.DS.$listFileOfComment);															
						}
					}
				}
											
				if($listFile){
					if(isset($_SESSION['javtemp'])){						
						$listFileTemp = JFolder::files($_SESSION['javtemp']);									
						if ($listFileTemp) {
							foreach ($listFileTemp as $file){
								if (!in_array($file, $listFile, true)) {
									JFile::delete($_SESSION['javtemp'].DS.$file);									
								}
							}
						}									
						JRequest::setVar("listfile", implode(',', $listFile));									
														
						//move file
						$target_path =  JPATH_ROOT.DS."images".DS."stories".DS."ja_voice".DS.$row->id;																
						
						if (!is_dir($target_path)){
							JFolder::create($target_path);					   		
					    }							   
					    
					    if ($listFileTemp) {
					       JFolder::copy($_SESSION['javtemp'], $target_path,'', true);	
					    }		 
					    
					    JFolder::delete($_SESSION['javtemp']);					   
					    					   
					    unset($_SESSION['javtemp']);
					    unset($_SESSION['javnameFolder']);						   						    																																																
					}											   							  
		 		}
			}
						
			if($javconfig["systems"]->get("is_enable_tagging",0)){
		 		//Insert tag.
				$javtags = JRequest::getVar("javtag", "");
				$modelTags = $this->getModel('tags'); 
				$modelTags->addVoiceToTag($row->id,$javtags);
			}
			
			if (intval($javconfig['systems']->get("is_notify_admin", 0))) {
				// send mail to administrator or author
				$helper->sendMailWhenNewVoice($row, $Itemid);
			}
			
			if (! $cid) {								
				$this->vote ( '', $post ['votes'] );
				$model->update_total_items ( $row->voice_types_id, $row->forums_id );
				$model->vote ( $row->id, $post ['votes'] );								
				
				
				if ($row->voice_type_status_id) {
					$status_is_spam = $this->getModel ( 'voicetypesstatus' )->is_spam ( $row->voice_type_status_id );
				}
				
				if ($row->published && (! $row->voice_type_status_id || ! $status_is_spam)) {
					$object= new stdClass ( );
					$object->id = '';
					$object->attr = 'reload';
					
					if (trim($javconfig['systems']->get("page_redirect", 'item')) == 'item') {
						$object->content = JRoute::_ ( 'index.php?option=com_javoice&view=items&layout=item&cid=' . $row->id . '&type=' . $row->voice_types_id . '&forums=' . $row->forums_id . '&Itemid=' . $Itemid . '&' . md5 ( 'save_successfull' ) );
					}
					else {
						$object->content = JURI::current();
					}
					
					$object->content = str_replace('&amp;', '&', $object->content);
					$objects[]=$object; 
					
					$helper = new JAVoiceHelpers ( );		
					echo $helper->parse_JSON_new ( $objects );
					exit ();
				}
				
				if (isset ( $post ['create_full'] ) && $post ['create_full'] == 1) {
					$url = str_replace('&amp;', '&', JRoute::_ ( 'index.php?option=com_javoice&type=' . $post ['type'] . '&' . md5 ( 'save_successfull' ) ));
					return $this->setRedirect ( $url);
				}
				JRequest::setVar ( 'layout', 'after_save' );
			} else {
				$object= new stdClass ( );
				$object->id = '#jav-box-item-' . $row->id . ' .jav-item-title';
				$object->attr = 'html';
				$object->content = $row->title;
				$objects[]=$object;												
				
				$row->content = str_replace("\n", '<br/>', $row->content);
				$object = new stdClass ( );
				$object->id = '#jav-box-item-' . $row->id . ' .jav-item-content';
				$object->attr = 'html';
				$object->content = html_entity_decode($helper->showItem($row->content));
				$objects[]=$object;
				
				$dir = JPATH_ROOT.DS."images".DS."stories".DS."ja_voice".DS.$row->id;				
				$files = $model->formatFilesInDir($dir,'download',$user->id,$row->id);
				$content =$files;

				$object= new stdClass ( );
				$object->id = '#jav_file_upload_' . $row->id;
				$object->attr = 'html';
				$object->content = $content;
				$objects[]=$object;
								
				$object = new stdClass ( );
				$object->id = '#jav-box-item-' . $row->id . ' .created-at';
				$object->attr = 'html';
				$object->content = JText::_('UPDATED_ON' ) . ' ' . JAVoiceHelpers::generatTimeStamp ( $row->update_date );
				$objects[]=$object;
				
				if($javconfig["systems"]->get("is_enable_tagging",0)){
					$object = new stdClass ( );
					$object->id = '#jav_item_tags';
					$object->attr = 'html';
					$object->content = "";
					
					$tagList = $modelTags->getTagByVoice($row->id);
					$spaterTags = $javconfig["systems"]->get("characters_separating_tags", ",");
					$tagContent = "<b>".JText::_("TAG_LIST").':</b>';
					foreach ($tagList as $tagItem => $tag){
						$tagContent .= ' <a title="'.$tag->name.'" href="'.JRoute::_('index.php?option=com_javoice&amp;view=items&amp;layout=item&amp;tagid='.$tag->id.'&amp;Itemid='.$Itemid).'">'.$tag->name.'</a>';
						if($tagItem <= (count($tagList)-1)) $tagContent .=$spaterTags; 
					}
					$objects[]=$object;
					$object->content = $tagContent;				
				}				
				if($javconfig["systems"]->get("use_anonymous",0)){
					$object= new stdClass ( );
					$object->id = '#jav-box-item-' . $row->id . ' .jav-createdby';
					$object->attr = 'html';
																
					if($row->use_anonymous){
						$object->content = '<small>'.JText::_('BY').'</small> '.JText::_("ANONYMOUS");	
					}else{						
						if (isset ( $row->user_id ) && $row->user_id > 0) {
							$user = JFactory::getUser ( $row->user_id );										
							if($javconfig['plugin']->get('displayname', 'username') == "name"){						
								$creat_by = $user->name;
							}else if($javconfig['plugin']->get('displayname', 'username') == "username"){
								$creat_by = $user->username;
							}else{
								$creat_by = JText::_("ANONYMOUS");
							}
						} elseif (isset ( $row->guest_name ) && $row->guest_name != '') {
							if($javconfig['plugin']->get('displayname', 'username') == "anonymous"){
								$creat_by = JText::_("ANONYMOUS");
							}else{
								$creat_by = $row->guest_name;
							}
						} else {
							$creat_by = JText::_('ANONYMOUS' );
						}
						$object->content = '<small>'.JText::_('BY').'</small>
											<a href="'.JRoute::_('index.php?option=com_javoice&view=users&uid='.$row->user_id.'&amp;Itemid='.$Itemid).'" class="user">'.$creat_by.'</a>';
					}
					$objects[]=$object;
				}
				$helper = new JAVoiceHelpers ( );		
				echo $helper->parse_JSON_new ( $objects );
				exit ();				
			}
		} else {
			$object= new stdClass ( );
			$object->id = '#jav-popup-error-posting';
			$object->attr = 'html';
			$object->content = JText::_('ERROR_FOUND_FAIL_TO_SAVE_IDEA'). '<br/>'. $row;
			$objects[]=$object;
			
			$object= new stdClass ( );
			$object->id = '#jav-popup-error-posting';
			$object->attr = 'css';
			$object->content = 'display,block';
			$objects[]=$object;		
			
			$helper = new JAVoiceHelpers ( );		
			echo $helper->parse_JSON_new ( $objects );
			exit ();			
		}
		
		return parent::display ();
	}
	function checkDataPhp(&$objects){
		global $javconfig;
		$result = true;
		$isShowCaptcha 			= 0;
		
		$is_enable_captcha		= $javconfig['plugin']->get('is_enable_captcha',1);
		$is_enable_captcha_user = $javconfig['plugin']->get('is_enable_captcha_user',1);
		
		$currentUserInfo		= JFactory::getUser();
		if((($currentUserInfo->guest && $is_enable_captcha) || (!$currentUserInfo->guest && $is_enable_captcha_user))&& ($currentUserInfo->get ( 'aid' )<2)){
			$isShowCaptcha		= 1;		
		}
				
		$helper = new JAVoiceHelpers ( );
		if($isShowCaptcha){
			$captcha = JRequest::getVar ( 'captcha', '' );											
		    if(!$this->validatecaptchaaddnew($captcha)){					

				$object = new stdClass ( );
				$object->id = '#err_invalidjavTextCaptcha';
				$object->attr = 'html';
				$object->content =JText::_("YOUR_CAPTCHA_IS_INVALID");
				$objects[]=$object;	
				$result=FALSE;												
			}else{
				$object = new stdClass ( );
				$object->id = '#err_javTextCaptcha';
				$object->attr = 'html';
				$object->content ="";
				$objects[]=$object;																														
			}
		}
		
		$user = JFactory::getUser();
		if($user->id==0){
			$name = JRequest::getVar('guest_name','');
			$email = JRequest::getVar('guest_email','');
			if($name=='' || $email==''){
				$object = new stdClass ( );
				$object->id = '#jav_profile_msg';
				$object->attr = 'html';
				$object->content =JText::_("PLEASE_ENTER_NAME_AND_EMAIL");
				$objects[]=$object;	
				$result=FALSE;				
			}elseif (!$helper->validate_email($email)){		
				$object = new stdClass ( );
				$object->id = '#jav_profile_msg';
				$object->attr = 'html';
				$object->content =JText::_("YOUR_EMAIL_ISNT_VALID");
				$objects[]=$object;		
				$result=FALSE;							
			}else{
				$object = new stdClass ( );
				$object->id = '#jav_profile_msg';
				$object->attr = 'html';
				$object->content ="";
				$objects[]=$object;						
			}
		}
		
		if(!$result){	
			$object = new stdClass ( );
			$object->id = 'jav_error_checkphp';
			$object->attr = 'html';
			$object->content =1;	
			$objects[]=$object;	
			
			$helper = new JAVoiceHelpers ( );
			echo $helper->parse_JSON_new ( $objects );
			exit ();						
		}								
						
	}
	function validatecaptchaaddnew($arg){
    	$captcha = new jacapcha();
		$captcha->text_entered = $arg;
	    $captcha->validateText("addnew");
        if($captcha->valid_text){
           return true; 
        }else{
           return false;
        }	
	}	
	function vote() {
		
		$model = $this->getModel ( 'items' );
		$cid = JRequest::getInt ( 'cid' );
		if (! $cid)
			return;
		
		$votes = JRequest::getInt ( 'votes' );
		
		$this->getModel ( 'items' )->vote ( $cid, $votes );
		
		JRequest::setVar ( 'layout', 'vote' );
		
		parent::display ();
	}
	
	function spam() {
		global $javconfig;
		
		$db = JFactory::getDBO ();
		$model = $this->getModel ( 'items' );
		$cid = JRequest::getInt ( 'cid' );
		if (! $cid)
			return;
		
		$var = md5 ( 'jav-view-item-' . trim ( $this->getTask () ) ) . $cid;
		
		if (isset ( $_COOKIE [$var] )) {
			$k=0;
			$object [$k] = new stdClass ( );
			$object [$k]->id = '#flags-'.$cid;
			$object [$k]->attr = 'html';
			$object [$k]->content = ' |' . JText::_('YOU_HAVE_FLAGGED' ); //$db->Quote ( ' |' . JText::_('YOU_HAVE_FLAGGED' ) );
			$k ++;
			
			$helper = new JAVoiceHelpers ( );		
			echo $helper->parse_JSON_new ( $object );
			exit ();						
		}
		
		$this->getModel ( 'items' )->spam ( $cid, $this->getTask () );
		/* Set Log */
		setcookie ( $var, 1, time () + $javconfig ['systems']->get ( 'timelinespam', 86400 ) ); //$javconfig['systems']->get('timelinespam', 86400)
		$_COOKIE [$var] = 1;
		
		$type_id = JRequest::getInt ( 'type' );
		$link = JURI::base () . 'index.php?option=com_javoice&amp;view=items&amp;task=undo_spam&amp;&oldtask=' . $this->getTask () . '&amp;cid=' . $cid . '&amp;tmpl=component&amp;type=' . $type_id . '&amp;Itemid=' . JRequest::getInt ( 'Itemid' );
		
		$msg = '| ' . JText::_('FLAGGED_IT_WILL_BE_REMOVED_WHEN_ENOUGH_PEOPLE_FLAG_IT' );
		$msg .= ' <a href="javascript:void(0)" onclick="jav_ajax_load(\'' . $link . '\', \'' . $type_id . '\')">[' . JText::_('UNDO' ) . ']</a>';
		
		$k=0;
		$object [$k] = new stdClass ( );
		$object [$k]->id = '#flags-'.$cid;
		$object [$k]->attr = 'html';
		$object [$k]->content = $msg;//$db->Quote ( $msg );		
		
		$helper = new JAVoiceHelpers ( );		
		echo $helper->parse_JSON_new ( $object );
		exit ();		
	}
	
	function undo_spam() {
		global $javconfig;
		
		$db = JFactory::getDBO ();
		$model = $this->getModel ( 'items' );
		$cid = JRequest::getInt ( 'cid' );
		if (! $cid)
			return;
		
		$item = $model->getItem ();
		
		$Itemid = JRequest::getInt ( 'Itemid' );
		$var = md5 ( 'jav-view-item-' . trim ( JRequest::getWord ( 'oldtask' ) ) ) . $cid;
		
		$this->getModel ( 'items' )->spam ( $cid, trim ( JRequest::getWord ( 'oldtask' ) ), - 1 );
		
		if (isset ( $_COOKIE [$var] )) {
			setcookie ( $var, null, 0 );
			unset ( $_COOKIE [$var] );
		}
		
		$type_id = JRequest::getInt ( 'type' );
		$link_spam = JURI::base () . 'index.php?option=com_javoice&amp;view=items&amp;task=spam&amp;cid=' . $item->id . '&amp;tmpl=component&amp;type=' . $item->voice_types_id . '&amp;Itemid=' . $Itemid;
		
		$msg = '<small>' . JText::_('IS_IT' ) . '</small>					
				<a href="javascript:void(0)" onclick="jav_ajax_load(\'' . $link_spam . '\', \'' . $item->voice_types_id . '\'); ">' . JText::_('SPAM' ) . '</a>';
		$k=0;	
		$object [$k] = new stdClass ( );
		$object [$k]->id = '#flags-'.$cid;
		$object [$k]->attr = 'html';
		$object [$k]->content = $msg . ' |' . JText::_('UNDO_SUCCESSFULLY' );//$db->Quote ( $msg . ' |' . JText::_('UNDO_SUCCESSFULLY' ) );
		
		$helper = new JAVoiceHelpers ( );		
		echo $helper->parse_JSON_new ( $object );
		exit ();		
	}
	
	function change_status() {		
		if (! JAVoiceHelpers::checkPermissionAdmin ()) {			
			$k=0;	
			$object [$k] = new stdClass ( );
			$object [$k]->id = '#jav-msg-loading';
			$object [$k]->attr = 'html';
			$object [$k]->content = JText::_("SORRY_YOU_DONT_PERMISSION_TO_EDIT_THIS" );
			$k++;
			$object [$k] = new stdClass ( );
			$object [$k]->id = '#jav-msg-loading';
			$object [$k]->attr = 'css';
			$object [$k]->content = 'display,block';
			
			$helper = new JAVoiceHelpers ( );		
			echo $helper->parse_JSON_new ( $object );
			exit ();						
		}
		$db = JFactory::getDBO ();
		$model = $this->getModel ( 'items' );
		$model_status = $this->getModel ( 'voicetypesstatus' );
		
		$user = JFactory::getUser ();
		$type_id = JRequest::getInt ( 'type' );
		$type = $model->getVoiceType ( $type_id );
		
		$cid = JRequest::getInt ( 'cid' );
		if (! $cid)
			return;
		$statusid = JRequest::getInt ( 'statusid' );
		
		$post = array ();
		$post ['cid[]'] = $cid;
		$post ['voice_type_status_id'] = $statusid;
		
		$model->setState ( 'request', $post );
		if ($row = $model->store ()) {
			/* Return msg */
			if ($statusid) {				
				$status_is_spam = $model_status->is_spam ( $statusid );
				$status_is_closed = $model_status->is_closed ( $statusid );
				$status = $model_status->getItem ( $statusid );
				
				$msg = '<a style="background: ' . $status->class_css . '" onclick="jav_show_all_status(' . $cid . '); return false;" href="#" class="jav-tag inline-edit">' . $status->title . '</a>';
				if ($status_is_spam || $status_is_closed) {
					$msg .= ' <small class="note" style="">' . JText::_('WILL_BE_REMOVED_FROM_ACTIVE_SUGGESTIONS' ) . '</small>';
				}
			} else {
				$msg = '<a onclick="jav_show_all_status(' . $cid . '); return false;" href="#" class="jav-tag inline-edit">' . JText::_('SET_STATUS__CLOSE' ) . '</a>';
			}
			
			$k=0;	
			$object [$k] = new stdClass ( );
			$object [$k]->id = '#jav-box-item-'.$cid.' .jav-status-title';
			$object [$k]->attr = 'html';
			$object [$k]->content = $msg;
			$k++;
			$object [$k] = new stdClass ( );
			$object [$k]->id = '#jav-box-item-'.$cid.' .statuses';
			$object [$k]->attr = 'css';
			$object [$k]->content = 'display,none';		
		} else {
			$k=0;	
			$object [$k] = new stdClass ( );
			$object [$k]->id = '#jav-box-item-'.$cid.' .jav-item-status';
			$object [$k]->attr = 'html';
			$object [$k]->content = JText::_('CHANGE_STATUS_NOT_SUCCESSFULL' );			
		}

		$helper = new JAVoiceHelpers ( );		
		echo $helper->parse_JSON_new ( $object );
		exit ();
	}
	
	function updateTotalComments() {
		global $javconfig;
		
		$total = JRequest::getInt ( 'total' );
		if ($total < 0 || $total > 99999999999)
			return;
		
		$model = $this->getModel ( 'items' );
		$item = $model->getItem ();
		
		$system_comment = $javconfig ['integrate']->get ( 'run_system', 'intensedebate' );
		$data = class_exists('JRegistry')? new JRegistry($item->data) : new JParameter($item->data);
		$data->set ( $system_comment . '_total', $total );
		
		$data = $data->toString ();
		
		$model->setState ( 'request', array ('data' => $data ) );
		$model->store ();
	}
	
	function admin_response() {
		if (! JAVoiceHelpers::checkPermissionAdmin ()) {
			$k=0;	
			$object [$k] = new stdClass ( );
			$object [$k]->id = '#jav-msg-loading';
			$object [$k]->attr = 'html';
			$object [$k]->content = JText::_("SORRY_YOU_DONT_PERMISSION_TO_EDIT_THIS" );
			$k++;
			$object [$k] = new stdClass ( );
			$object [$k]->id = '#jav-msg-loading';
			$object [$k]->attr = 'css';
			$object [$k]->content = 'display,block';
			
			$helper = new JAVoiceHelpers ( );		
			echo $helper->parse_JSON_new ( $object );
			exit ();						
		}
		
		global $javconfig;
		$option = JRequest::getCmd('option');
		
		$user = JFactory::getUser ();
		
		JArrayHelper::toInteger ( $cid );
		
		$model = $this->getModel ( 'items' );
		$helper = new JAVoiceHelpers ( );
		$post = JRequest::get ( 'request' );
		
		// allow name only to contain html
		if(JRequest::getVar("javNameOfTextarea","newVoiceContent")=="newVoiceContentReply"){
			$post ['content'] = trim ( JRequest::getVar ( 'newVoiceContentReply', '', 'request', 'string', JREQUEST_ALLOWRAW ) );
		}else{
			$post ['content'] = trim ( JRequest::getVar ( 'newVoiceContent', '', 'request', 'string', JREQUEST_ALLOWRAW ) );
		}

		$post ['content'] = $helper->removeEmptyBBCode($post ['content']);
		$post ['user_id'] = $user->id;
		$post ['item_id'] = JRequest::getInt ( 'item_id' );		
		
		$admin_responses = $model->getAdmin_responses ( " and item_id={$post['item_id']} and type='admin_response'" );
		
		if ($admin_responses) {
			$post ['cid'] = $admin_responses [0]->id;
		}
		$post ['type'] = 'admin_response';
		
		$model->setState ( 'request', $post );
		$row = $model->store_admin_response ();
		if (isset ( $row->id )) {
			if($javconfig['plugin']->get("is_attach_image", 0)){
				jimport( 'joomla.filesystem.folder' );
				jimport('joomla.filesystem.file');
					
				//delete file in store image if remove file
				$listFile = JRequest::getVar('listfile', 0);				
				$file_path 			 =  JPATH_ROOT.DS."images".DS."stories".DS."ja_voice".DS."admin_response".DS.$row->id;
				
				$listFileOfComments  =  JFolder::files($file_path);																			
				
				if($listFileOfComments){
					foreach ($listFileOfComments as $listFileOfComment){
						if($listFile){
							if(!in_array($listFileOfComment, $listFile)){
								JFile::delete($file_path.DS.$listFileOfComment);									
							}
						}else{	
							JFile::delete($file_path.DS.$listFileOfComment);															
						}
					}
				}
				//die($_SESSION['javReplyTemp']."--");							
				if($listFile){
					if(isset($_SESSION['javReplyTemp'])){						
						$listFileTemp = JFolder::files($_SESSION['javReplyTemp']);									
						if ($listFileTemp) {
							foreach ($listFileTemp as $file){
								if (!in_array($file, $listFile, true)) {
									JFile::delete($_SESSION['javReplyTemp'].DS.$file);									
								}
							}
						}									
						JRequest::setVar("listfile", implode(',', $listFile));									
														
						//move file
						$target_path =  JPATH_ROOT.DS."images".DS."stories".DS."ja_voice".DS."admin_response".DS.$row->id;																
						
						if (!is_dir($target_path)){
							JFolder::create($target_path);					   		
					    }							   
					    
					    if ($listFileTemp) {
					       JFolder::copy($_SESSION['javReplyTemp'], $target_path,'', true);	
					    }		 
					    
					    JFolder::delete($_SESSION['javReplyTemp']);
					    																	    				   
					    unset($_SESSION['javtemp']);
					    unset($_SESSION['javReplyNameFolder']);						   						    																																																
					}											   							  
		 		}			 																																
			}
			
			$file_path   =  JPATH_ROOT.DS."images".DS."stories".DS."ja_voice".DS."admin_response".DS.$row->id;
			$listFiles 	 =  $helper-> preloadfile($row->id, "response");
			$attachFiles =  $model->formatFilesInDir($file_path,'download',$row->user_id,$row->id);						
			
			$object = array ();
			$k = 0;
			$link_respone = "link-response-" . $row->item_id;
			$delete_response = "link-delete-response-". $row->item_id;
			$object [$k] = new stdClass ( );
			$object [$k]->id = '#jav-box-item-' . $row->item_id . ' .jav-response-text';
			$object [$k]->attr = 'html';			
			$object [$k]->content = '<div class="jav-author">' . JText::_('ADMIN_RESPONSE' ) . '</div>
									 <span>' . html_entity_decode($helper->showItem($row->content)) . '</span>
									 <div class="jav-response-upload">'.$attachFiles.'<div>
									 <span class="editable">
									 	<small>
										 <a class="user" 
										 	href="' . JRoute::_ ( "index.php?option=com_javoice&view=users&uid=" . $user->id ) . '">' . $user->username . '</a>
										 </small>' . ' | 
										 <a onclick="return show_frm_response(\'#link-response-'.$row->id.'\', '.$row->item_id.', '.$row->id.')" href="javascript:void(0)" id ="'.$link_respone.'" class="edit-link">'. JText::_('EDIT' ) . '</a> | 
										 <a onclick="return delete_reply_voice(\'#link-response-'.$row->id.'\', '.$row->item_id.', '.$row->id.', \''.JText::_("DO_YOU_WANT_TO_DELETE_THIS_REPLY").'\')" href="javascript:void(0)" id ="'.$delete_response.'" class="edit-link">'. JText::_('DELETE' ) . '</a>
									 </span>';
			$k ++;			
			
			$object [$k] = new stdClass ( );
			$object [$k]->id = '#jav-box-item-' . $row->item_id.' .jav-upload-form';
			$object [$k]->attr = 'html';
			$object [$k]->content = $listFiles;
			$k ++;			
			
			//update content
			$object [$k] = new stdClass ( );
			$object [$k]->id = '#jav-content-respone-' . $row->item_id;
			$object [$k]->attr = 'value';
			$object [$k]->content = $row->content;
			$k ++;
			
			//update again button
			//if(!$post["cid"]){							
				$object [$k] = new stdClass ( );
				$object [$k]->id = '#jav-form-response-' . $row->item_id;
				$object [$k]->attr = 'html';
				$object [$k]->content = '<input type="button" onclick="jav_submit_admin_response('.$row->item_id.', '.$row->id.');" value="'.JText::_('SAVE').'" name="commit" id="bestanswer-commit-'.$row->item_id.'"/>
									  	<input type="button" value="'.JText::_('CANCEL').'" name="cancel" onclick="hide_frm_response(\'#link-response-'.$row->item_id.'\', '.$row->item_id.', \'1\''.')"/>';
				$k ++;
			//}
			
			$object [$k] = new stdClass ( );
			$object [$k]->id = '#jav-form-response-' . $row->item_id;			
			$object [$k]->attr = 'css';
			$object [$k]->content = 'display,none';
			$k++;		
						
			$object [$k] = new stdClass ( );
			$object [$k]->id = '#jav-box-item-' . $row->item_id . ' .jav-response-text';
			$object [$k]->attr = 'css';
			$object [$k]->content = 'display,block';
			$k ++;
									
			$helper = new JAVoiceHelpers ( );		
			echo $helper->parse_JSON_new ( $object );
			exit ();						
		} else {
			//echo JText::_('ERROR_FOUND_FAIL_TO_SAVE_IDEA' );
		}
	
	}
	
	function mark_best_answer() {
		
		if (! JAVoiceHelpers::checkPermissionAdmin ()) {
			$k=0;	
			$object [$k] = new stdClass ( );
			$object [$k]->id = '#jav-msg-loading';
			$object [$k]->attr = 'html';
			$object [$k]->content = JText::_("SORRY_YOU_DONT_PERMISSION" );
			$k++;
			$object [$k] = new stdClass ( );
			$object [$k]->id = '#jav-msg-loading';
			$object [$k]->attr = 'css';
			$object [$k]->content = 'display,block';
			
			$helper = new JAVoiceHelpers ( );		
			echo $helper->parse_JSON_new ( $object );
			exit ();						
		}
		
		$option = JRequest::getCmd('option');
		
		$user = JFactory::getUser ();
		
		JArrayHelper::toInteger ( $cid );
		
		$model = $this->getModel ( 'items' );
		$helper = new JAVoiceHelpers ( );
		
		$post = JRequest::get ( 'request' );
		//echo 22;exit;
		// allow name only to contain html
		if(JRequest::getCmd("javNameOfTextarea","newVoiceContent")=="newVoiceContentReply"){
			$post ['content'] = trim ( JRequest::getVar ( 'newVoiceContentReply', '', 'request', 'string', JREQUEST_ALLOWRAW ) );
		}else{
			$post ['content'] = trim ( JRequest::getVar ( 'content', '', 'request', 'string', JREQUEST_ALLOWRAW ) );
		}				
		
		$helper->removeEmptyBBCode($post ['content']);//echo $post ['content'];exit;
		
		$post ['user_id'] = $user->id;
		$post ['item_id'] = JRequest::getInt ( 'item_id' );
		$post ['type'] = 'best_answer';
		$response = $model->getAdmin_responses ( " and item_id=" . $post ['item_id'] . " and type='best_answer'" );
		if ($response) {
			JRequest::setVar ( 'cid', $response [0]->id );
		}
		
		$model->setState ( 'request', $post );
		$row = $model->store_admin_response (); //print_r($row);exit;
		if (isset ( $row->id )) {
			$object = array ();
			$k = 0;
			//echo html_entity_decode($helper->showItem($row->content));exit;
			$object [$k] = new stdClass ( );
			$object [$k]->id = '#jav-box-item-' . $row->item_id . ' .jav-bestanswer-text';
			$object [$k]->attr = 'html';
			$object [$k]->content 	 = '<label><em>' . JText::_('BEST_ANSWER' ) . '</em></label><span>' . html_entity_decode($helper->showItem($row->content)) . '</span>';
			$object [$k]->content 	.= '<span class="editable"><a onclick="return show_frm_bestanswer(\'#link-bestanswer-' . $row->item_id . '\', ' . $row->item_id . ', ' . $row->id . ')" href="javascript:void(0)" class="edit-link">' . JText::_('EDIT' ) . '</a></span>';
			$object [$k]->content 	.= '<input type="hidden" id="jav-content-bestanswer-' . $row->item_id . '" value="' . htmlentities(nl2br($row->content)) . '"/>';
			$k ++;
			
			$object [$k] = new stdClass ( );
			$object [$k]->id = '#bestanswer-' . $row->item_id;
			$object [$k]->attr = 'html';
			$object [$k]->content = $row->content;
			$k ++;
			
			$object [$k] = new stdClass ( );
			$object [$k]->id = '#frm-bestanswer-' . $row->item_id;
			$object [$k]->attr = 'css';
			$object [$k]->content = 'display,none';
			$k ++;
			
			$object [$k] = new stdClass ( );
			$object [$k]->id = '#jav-box-item-' . $row->item_id . ' .jav-bestanswer-text';
			$object [$k]->attr = 'css';
			$object [$k]->content = 'display,block';
			$k ++;
			
			$helper = new JAVoiceHelpers ( );		
			echo $helper->parse_JSON_new ( $object );
			exit ();						
		} else {
			//echo JText::_('ERROR_FOUND_FAIL_TO_SAVE_IDEA' );
		}
	}
	function ja_login() {
		$module = JModuleHelper::getModule ( 'mod_login' );
		$module_login = JModuleHelper::renderModule ( $module );
		JRequest::setVar ( 'task', 'items' );
		echo $module_login;
		exit ();
	}
	function submitemded() {
		$helper = new JAVoiceHelpers ( );
		
		$post = JRequest::get ( 'request' );
		$description = $post['newVoiceContent'];
		$objects =array();
		if (! $helper->checkYoutubeLink ( $post ['txtYouTubeUrl'] )) {
			$object  = new stdClass ( );
			$object->id = '#jav_msg_succesfull_embed';
			$object->attr = 'css';
			$object->content = 'display,block';
			$objects[]=$object;
		
		} else {		
			$object = new stdClass ( );
			$object->id = '#newVoiceContent';
			$object->attr = 'value';
			$object->content = $description.'[youtube ' . $post ['txtYouTubeUrl'] . ' youtube]';
			$objects[]=$object;
			$object= new stdClass ( );
			$object->id = '#jav_msg_succesfull_embed';
			$object->attr = 'css';
			$object->content = 'display,none';	
			$objects[]=$object;		
		}
		$object = new stdClass ( );
		$object->id = 'jav_error_checkphp';
		$object->attr = 'html';
		$object->content =1;	
		$objects[]=$object;	
		
		$helper = new JAVoiceHelpers ( );		
		echo $helper->parse_JSON_new ( $objects );
		exit ();
	}
	function checkFileUpload($file){
		global $javconfig;
		$filename = basename($file['myfile']['name']);
  		$ext = substr($filename, strrpos($filename, '.') + 1);
  		$attachFileTypes	= $javconfig['plugin']->get('attach_file_type', "doc,docx,pdf,txt,zip,rar,jpg,bmp,gif,png");
  		$attachFileTypes = explode(",", $attachFileTypes);
  			  		  		
  		if(in_array($ext, $attachFileTypes)){
  			return true;
  		}
  		
  		return false;
	}	
	
	function uploadFile(){
		global $javconfig;
		$helper = new JAVoiceHelpers();
		$maxSize = (int)$helper->getSizeUploadFile("byte");									
		if (isset($_FILES['myfile']['name']) && $_FILES['myfile']['size']>0 && $_FILES['myfile']['size']<= $maxSize && $_FILES['myfile']['tmp_name']!=''){																													
			jimport( 'joomla.filesystem.folder' );
			jimport('joomla.filesystem.file');						
														
			$fileexist = 0;    
			$img = ''; $link = '';
			$totalFile = 0;
			// Edit upload location here
			
			$fname = basename($_FILES['myfile']['name']);
			$fname = strtolower(str_replace(' ', '', $fname));
			$folder = time().rand().DIRECTORY_SEPARATOR;			 						
			//$folder = JPATH_ROOT.DS."images".DS."stories".DS."ja_voice";						
			
			if(!isset($_SESSION['javnameFolder'])) $_SESSION['javnameFolder'] = $folder;
		   	else $folder = $_SESSION['javnameFolder'];
		   	
			$destination_path = JPATH_ROOT.DS."tmp".DS."ja_voice".DS.$folder;
			
		  	if (!isset($_SESSION['javtemp'])) {
		   		$_SESSION['javtemp'] = $destination_path;
		   	}
						   	
		   	$target_path = $destination_path . '/'.$fname;
		   			   	
		   	if (!is_dir($destination_path)){
		   		JFolder::create($destination_path);		   		   				   				   		
		    }
		    $id = JRequest::getInt("id", 0);
			$listFiles 		= JRequest::getVar("listfile");			
			if (count($listFiles)<$javconfig['plugin']->get("total_attach_file", 0)) {				
				//rebuilt listfile					
				foreach ($listFiles as $listFile){												
					$type = substr(strtolower(trim($listFile)), -3, 3);
					if($type=='ocx'){
					 	$type = "doc";
					}																	
					$img .= "<div style='float: left; clear: both;'><span><input type='checkbox' onclick='javCheckTotalFile()' name='listfile[]' value='$listFile' checked></span>&nbsp;&nbsp;<img src='".Juri::root()."/components/com_javoice/asset/images/icons/". $type .".gif' alt='". $type ."' /> " .$listFile . "</div>";
					$totalFile++; 				
				}
				//load file uncheck
				$listFilesInFolders  = JFolder::files($destination_path);					
				foreach ($listFilesInFolders as $listFilesInFolder){
					if(!in_array($listFilesInFolder, $listFiles)){
						$type = substr(strtolower(trim($listFilesInFolder)), -3, 3);
						if($type=='ocx'){
						 	$type = "doc";
						}							
						$img .= "<div style='float: left; clear: both;'><span><input type='checkbox' onclick='javCheckTotalFile()' name='listfile[]' value='$listFilesInFolder' disabled='disabled'></span>&nbsp;&nbsp;<img src='".Juri::root()."/components/com_javoice/asset/images/icons/". $type .".gif' alt='". $type ."' /> " .$listFilesInFolder . "</div>";
						$totalFile++;		
					}
				}
				$listFilesInFolders  = JFolder::files(JPATH_ROOT.DS."images".DS."stories".DS."ja_voice".DS.$id);					
				foreach ($listFilesInFolders as $listFilesInFolder){
					if(!in_array($listFilesInFolder, $listFiles)){
						$type = substr(strtolower(trim($listFilesInFolder)), -3, 3);
						if($type=='ocx'){
						 	$type = "doc";
						}							
						$img .= "<div style='float: left; clear: both;'><span><input type='checkbox' onclick='javCheckTotalFile()' name='listfile[]' value='$listFilesInFolder' disabled='disabled'></span>&nbsp;&nbsp;<img src='".Juri::root()."/components/com_javoice/asset/images/icons/". $type .".gif' alt='". $type ."' /> " .$listFilesInFolder . "</div>";
						$totalFile++;
					}
				}
				
				if(file_exists($target_path) || file_exists(JPATH_ROOT.DS."images".DS."stories".DS."ja_voice".DS.$id.DS.$fname)){
						$fileexist = 1;
				}
				elseif(@move_uploaded_file($_FILES['myfile']['tmp_name'], $target_path)){			   		 	   	
			   		 $totalFile++;		   		 
			   		 $type = substr(strtolower(trim($_FILES['myfile']['name'])), -3, 3);
			   		 	   		 
					 if($type=='ocx'){
					 	$type = "doc";
					 }			
					 $img .= "<input type='checkbox' name='listfile[]' onclick='javCheckTotalFile()' value='$fname' checked>&nbsp;&nbsp;<img src='".Juri::root()."/components/com_javoice/asset/images/icons/". $type .".gif' /> " .$fname . "<br />";					 
			   }
			}
			
			echo '<script language="javascript" type="text/javascript">
            	   		var par = window.parent.document;			   		
            			function stopUpload(par, listfile, count, totalUpload){					  		  
            				par.getElementById(\'jav_err_myfile\').innerHTML = "";   			  					  
            				par.new_item.target = "_self";
            				par.new_item.task.value = "save";
            				
            				par.getElementById(\'jav_upload_process\').style.display=\'none\';
            				par.getElementById(\'jav_result_upload\').innerHTML = listfile;
            				par.new_item.myfile.value = "";
            				if(eval(count)>=totalUpload){
            						if(1<=totalUpload){
            							par.new_item.myfile.disabled = true;
            							par.getElementById(\'jav_err_myfile\').style.display = "block";		
            					par.getElementById(\'jav_err_myfile\').innerHTML = "'. JText::_("YOU_ADDED").'" + totalUpload + " '. JText::_("FILES").'!";
            						}else{						  		
            							par.new_item.myfile.disabled = true;
            							par.getElementById(\'jav_err_myfile\').style.display = "block";
            					par.getElementById(\'jav_err_myfile\').innerHTML = "'. JText::_("YOU_ADDED").'" + totalUpload + " '. JText::_("FILE").'!";
            				}  																
            				}					  
            				return true;   
						}
					</script>';
								   			 
			if($fileexist){				
				echo '<script language="javascript" type="text/javascript">								
						var par = window.parent.document;	
						par.getElementById(\'jav_err_myfile\').style.display = "block";												
						par.getElementById(\'jav_err_myfile\').innerHTML = "<span class=\'err\' style=\'color:red\'>'. JText::_("THIS_FILE_EXISTED") .'</span>";									
						par.getElementById("jav_upload_process").style.display="none";
						par.new_item.task.value  = "save";
					  </script>';														
			}else{
				echo '<script language="javascript" type="text/javascript">stopUpload(par, "'.$img.'", '.$totalFile.', '.$javconfig['plugin']->get("total_attach_file").')</script>';			
			}
		}elseif (isset($_FILES['myfile']['name'])){
			echo '<script type="text/javascript">					
					var par = window.parent.document;
					var content = "";
					if(document.body){
						document.body.innerHTML = "";
					}		
					par.getElementById(\'jav_upload_process\').style.display=\'none\';
					par.new_item.myfile.value = "";
					par.getElementById(\'jav_err_myfile\').style.display = "block";
					par.getElementById(\'jav_err_myfile\').innerHTML = "'. JText::_("LIMITATION_OF_UPLOAD_IS").$helper->getSizeUploadFile().'.";  		
					par.new_item.myfile.focus();					
					par.new_item.task.value  = "save";
				</script>';
		}
	}
	
	
	function uploadReplyFile(){
		if (! JAVoiceHelpers::checkPermissionAdmin ()) {
			$k=0;	
			$object [$k] = new stdClass ( );
			$object [$k]->id = '#jav-msg-loading';
			$object [$k]->attr = 'html';
			$object [$k]->content = JText::_("SORRY_YOU_DONT_PERMISSION_TO_EDIT_THIS" );
			$k++;
			$object [$k] = new stdClass ( );
			$object [$k]->id = '#jav-msg-loading';
			$object [$k]->attr = 'css';
			$object [$k]->content = 'display,block';
			
			$helper = new JAVoiceHelpers ( );		
			echo $helper->parse_JSON_new ( $object );
			exit ();						
		}
		
		global $javconfig;
		$helper = new JAVoiceHelpers();
		$maxSize = (int)$helper->getSizeUploadFile("byte");									
		if (isset($_FILES['myfile']['name']) && $_FILES['myfile']['size']>0 && $_FILES['myfile']['size']<= $maxSize && $_FILES['myfile']['tmp_name']!=''){																													
			jimport( 'joomla.filesystem.folder' );
			jimport('joomla.filesystem.file');
									
			$deleteSession = JRequest::getInt("deleteSession");
			//echo '<script type="text/javascript">alert("'.$deleteSession.'");</script>';
			
			if($deleteSession){				
				unset($_SESSION['javReplyNameFolder']);
				unset($_SESSION['javReplyTemp']);
			}
			
			$fileexist = 0;    
			$img = '';
			$totalFile = 0;
			// Edit upload location here
			
			$fname = basename($_FILES['myfile']['name']);
			$fname = strtolower(str_replace(' ', '', $fname));
			$folder = time().rand().DIRECTORY_SEPARATOR;			 						
			//$folder = JPATH_ROOT.DS."images".DS."stories".DS."ja_voice";						
						
			if(!isset($_SESSION['javReplyNameFolder'])) $_SESSION['javReplyNameFolder'] = $folder;
		   	else $folder = $_SESSION['javReplyNameFolder'];
		   	
		   	//echo '<script type="text/javascript">alert("'.str_replace('\\','',$folder).'");</script>';
		   	
			$destination_path = JPATH_ROOT.DS."tmp".DS."ja_voice".DS.$folder;
			
		  	if (!isset($_SESSION['javReplyTemp'])) {
		   		$_SESSION['javReplyTemp'] = $destination_path;
		   	}
						   	
		   	$target_path = $destination_path . '/'.$fname;
		   			   	
		   	if (!is_dir($destination_path)){
		   		JFolder::create($destination_path);		   		   				   				   		
		    }
		    $id = JRequest::getInt("responeid", 0);
			$listFiles 		= JRequest::getVar("listfile");
						
			if (count($listFiles)<$javconfig['plugin']->get("total_attach_file", 0)) {				
				//rebuilt listfile					
				foreach ($listFiles as $listFile){												
					$type = substr(strtolower(trim($listFile)), -3, 3);
					if($type=='ocx'){
					 	$type = "doc";
					}																	
					$img .= "<div style='float: left; clear: both;'><span><input type='checkbox' onclick='javCheckTotalFileReply()' name='listfile[]' value='$listFile' checked></span>&nbsp;&nbsp;<img src='".Juri::root()."components/com_javoice/asset/images/icons/". $type .".gif' alt='". $type ."' /> " .$listFile . "</div>";
					$totalFile++; 				
				}
				//load file uncheck
				$listFilesInFolders  = JFolder::files($destination_path);					
				foreach ($listFilesInFolders as $listFilesInFolder){
					if(!in_array($listFilesInFolder, $listFiles)){
						$type = substr(strtolower(trim($listFilesInFolder)), -3, 3);
						if($type=='ocx'){
						 	$type = "doc";
						}							
						$img .= "<div style='float: left; clear: both;'><span><input type='checkbox' onclick='javCheckTotalFileReply()' name='listfile[]' value='$listFilesInFolder' disabled='disabled'></span>&nbsp;&nbsp;<img src='".Juri::root()."components/com_javoice/asset/images/icons/". $type .".gif' alt='". $type ."' /> " .$listFilesInFolder . "</div>";
						$totalFile++;		
					}
				}
				$listFilesInFolders  = JFolder::files(JPATH_ROOT.DS."images".DS."stories".DS."ja_voice".DS."admin_response".DS.$id);					
				foreach ($listFilesInFolders as $listFilesInFolder){
					if(!in_array($listFilesInFolder, $listFiles)){
						$type = substr(strtolower(trim($listFilesInFolder)), -3, 3);
						if($type=='ocx'){
						 	$type = "doc";
						}							
						$img .= "<div style='float: left; clear: both;'><span><input type='checkbox' onclick='javCheckTotalFileReply()' name='listfile[]' value='$listFilesInFolder' disabled='disabled'></span>&nbsp;&nbsp;<img src='".Juri::root()."components/com_javoice/asset/images/icons/". $type .".gif' alt='". $type ."' /> " .$listFilesInFolder . "</div>";
						$totalFile++;
					}
				}
				
				if(file_exists($target_path) || file_exists(JPATH_ROOT.DS."images".DS."stories".DS."ja_voice".DS."admin_response".DS.$id.DS.$fname)){
						$fileexist = 1;
				}
				elseif(@move_uploaded_file($_FILES['myfile']['tmp_name'], $target_path)){			   		 	   
			   		 $totalFile++;		   		 
			   		 $type = substr(strtolower(trim($_FILES['myfile']['name'])), -3, 3);
			   		 	   		 
					 if($type=='ocx'){
					 	$type = "doc";
					 }			
					 $img .= "<input type='checkbox' name='listfile[]' onclick='javCheckTotalFileReply()' value='$fname' checked>&nbsp;&nbsp;<img src='".Juri::root()."components/com_javoice/asset/images/icons/". $type .".gif' /> " .$fname . "<br />";					 
			   }
			}
			
			echo '<script language="javascript" type="text/javascript">
        	   		var par = window.parent.document;			   		
        			function stopUpload(par, listfile, count, totalUpload){					  		  
        				par.getElementById(\'jav_err_myfilereply\').innerHTML = "";   			  					  
        				par.new_reply_item.target = "_self";
        				//par.new_reply_item.task.value = "save";
        				
        				par.getElementById(\'jav_reply_upload_process\').style.display=\'none\';
        				par.getElementById(\'jav_result_reply_upload\').innerHTML = listfile;
        				par.new_reply_item.myfile.value = "";
        				if(eval(count)>=totalUpload){
        						if(1<=totalUpload){
        							par.new_reply_item.myfile.disabled = true;
        							par.getElementById(\'jav_err_myfilereply\').style.display = "block";
        							par.getElementById(\'jav_err_myfilereply\').innerHTML = "'. JText::_("YOU_ADDED").'" + totalUpload + " '. JText::_("FILES").'!";
        						}else{						  		
        							par.new_reply_item.myfile.disabled = true;
        							par.getElementById(\'jav_err_myfilereply\').style.display = "block";
        							par.getElementById(\'jav_err_myfilereply\').innerHTML = "'. JText::_("YOU_ADDED").'" + totalUpload + " '. JText::_("FILE").'!";
        						} 
        				}					  
        				return true;   
        			}
        		</script>';
								   			 
			if($fileexist){				
				echo '<script language="javascript" type="text/javascript">								
						var par = window.parent.document;
						par.getElementById(\'jav_err_myfilereply\').style.display = "block";													
						par.getElementById(\'jav_err_myfilereply\').innerHTML = "<span class=\'err\' style=\'color:red\'>'. JText::_("THIS_FILE_EXISTED") .'</span>";									
						par.getElementById("jav_reply_upload_process").style.display="none";
						//par.new_reply_item.task.value = "save";
					  </script>';														
			}else{
				echo '<script language="javascript" type="text/javascript">stopUpload(par, "'.$img.'", '.$totalFile.', '.$javconfig['plugin']->get("total_attach_file").')</script>';			
			}
		}elseif (isset($_FILES['myfile']['name'])){
			echo '<script type="text/javascript">					
					var par = window.parent.document;
					var content = "";
					if(document.body){
						document.body.innerHTML = "";
					}		
					par.getElementById(\'jav_reply_upload_process\').style.display=\'none\';
					par.new_reply_item.myfile.value = "";
					par.getElementById(\'jav_err_myfilereply\').style.display = "block";
					par.getElementById(\'jav_err_myfilereply\').innerHTML = "'. JText::_("LIMITATION_OF_UPLOAD_IS").$helper->getSizeUploadFile().'.";  		
					par.new_reply_item.myfile.focus();			
					//par.new_reply_item.task.value = "save";		
				</script>';
		}
	}
	
	
	
	function download()
	{
		$file_name = JRequest::getVar('file');
		$id = JRequest::getInt('id');		
		$downloadResponse = JRequest::getInt('downloadresponse', 0);
		if($downloadResponse) 
			$path = JPATH_ROOT.DS."images".DS."stories".DS."ja_voice".DS."admin_response".DS.$id;
		else 			
			$path = JPATH_ROOT.DS."images".DS."stories".DS."ja_voice".DS.$id;
		$this->fucdownload($path,$file_name);
	}	
	function fucdownload($folder,$filename){
		$filename = trim($filename);
		$filename = $folder."/".$filename; //server specific
		$file_extension = strtolower(substr(strrchr($filename,"."),1));
		if (!file_exists( $filename ) )
		{
			die("NO file HERE");
		}
		switch( $file_extension )
		{
		case "pdf": $ctype="application/pdf"; break;
		case "exe": $ctype="application/octet-stream"; break;
		case "zip": $ctype="application/zip"; break;
		case "doc": $ctype="application/msword"; break;
		case "xls": $ctype="application/vnd.ms-excel"; break;
		case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
		case "gif": $ctype="image/gif"; break;
		case "png": $ctype="image/png"; break;
		case "jpe": case "jpeg":
		case "jpg": $ctype="image/jpg"; break;
		default: $ctype="application/force-download";
		}
		header("Pragma: public"); // required
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false); // required for certain browsers
		header("Content-Type: $ctype");
		$name_download = basename($filename);
		header("Content-Disposition: attachment; filename=\"$name_download\";" );
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".@filesize($filename));
		@readfile($filename) or die("file not found.");
		exit(); 		
	}
	function displaycaptchaaddnew(){
    	$captcha = new jacapcha();
    	$captcha->buildImage("addnew");
    	exit;
    }

	function remove() {
		global $javconfig;
		$model = $this->getModel ( 'items' );
		if (! JAVoiceHelpers::checkPermissionAdmin ()) {
			if(!JAVoiceHelpers::checkPermissionAdmin()){
				$isAllowRegisterEdit = 0;
				if($javconfig["systems"]->get("is_edit_delete_voice",0)){
					$item = $model->getItem();
					$user = JFactory::getUser ();								
					$userEId = $user->get( 'id' );
					if($userEId == $item->user_id){
						$timeE = $javconfig["systems"]->get("time_for_edit_voice", 900);											
						if($timeE != -1 || time() < ($item->create_date+$timeE)){
							$isAllowRegisterEdit = 1;
						}
					}	
				}
				if(!$isAllowRegisterEdit){
					$k=0;	
					$object [$k] = new stdClass ( );
					$object [$k]->id = '#jav-msg-loading';
					$object [$k]->attr = 'html';
					$object [$k]->content = JText::_("SORRY_YOU_DONT_PERMISSION_TO_EDIT_THIS" );
					$k++;
					$object [$k] = new stdClass ( );
					$object [$k]->id = '#jav-msg-loading';
					$object [$k]->attr = 'css';
					$object [$k]->content = 'display,block';
					
					$helper = new JAVoiceHelpers ( );		
					echo $helper->parse_JSON_new ( $object );
					exit ();										
				}
			}												
		}
		
		$cid = JRequest::getInt ( 'cid' );
		$error = array ();
		if($cid){
			$error = $model->delete ( $cid );
			
			if ($error) {
				$err = implode ( ",", $error );
				
				$k=0;	
				$object [$k] = new stdClass ( );
				$object [$k]->id = '#jav-msg-loading';
				$object [$k]->attr = 'html';
				$object [$k]->content = $err;
				$k++;
				$object [$k] = new stdClass ( );
				$object [$k]->id = '#jav-msg-loading';
				$object [$k]->attr = 'css';
				$object [$k]->content = 'display,block';
				
				$helper = new JAVoiceHelpers ( );		
				echo $helper->parse_JSON_new ( $object );
				exit ();										
			} else{
				$k=0;	
				$object [$k] = new stdClass ( );
				$object [$k]->id = '#jav-msg-succesfull';
				$object [$k]->attr = 'html';
				$object [$k]->content = JText::_("DELETE_DATA_SUCCESSFULLY" );
				$k++;
				$object [$k] = new stdClass ( );
				$object [$k]->id = '#jav-msg-succesfull';
				$object [$k]->attr = 'timeout';
				$object [$k]->content = '10000,hide';		
				$k++;
				$object [$k] = new stdClass ( );
				$object [$k]->id = '#jav-msg-succesfull';
				$object [$k]->attr = 'css';
				$object [$k]->content = 'display,block';
				$k++;
				$object [$k] = new stdClass ( );
				$object [$k]->id = '#jav-box-item-'.$cid;
				$object [$k]->attr = 'css';
				$object [$k]->content = 'display,none';
				
				$helper = new JAVoiceHelpers ( );		
				echo $helper->parse_JSON_new ( $object );
				exit ();			
			}
		}
		else{
			$k=0;	
			$object [$k] = new stdClass ( );
			$object [$k]->id = '#jav-msg-loading';
			$object [$k]->attr = 'html';
			$object [$k]->content = JText::_("ITEM_NOT_FOUND" );
			$k++;
			$object [$k] = new stdClass ( );
			$object [$k]->id = '#jav-msg-loading';
			$object [$k]->attr = 'css';
			$object [$k]->content = 'display,block';
			
			$helper = new JAVoiceHelpers ( );		
			echo $helper->parse_JSON_new ( $object );
			exit ();
		}				
	}
	
	function load_forums(){
		$type_id = JRequest::getInt('type');
		$model_forums = $this->getModel('forums');
		
		$where_more .= ' and f.published=1 and ft.voice_types_id=' . $type_id;
		$join = 'INNER JOIN #__jav_forums_has_voice_types as ft ON f.id=ft.forums_id';
		
		$forums = $model_forums->getItems ( $where_more, 100, 0, 'f.ordering', '', $join );
		$out = JHTML::_('select.genericlist',  $forums, 'forums_id', 'class="inputbox"', 'id', 'title' );
				
		$db = JFactory::getDBO();
		// $out = $db->Quote($out);
		
		$k=0;	
		$object [$k] = new stdClass ( );
		$object [$k]->id = '#jav-forums';
		$object [$k]->attr = 'html';
		$object [$k]->content = $out;
		if(!$forums){
			$k++;
			$object [$k] = new stdClass ( );
			$object [$k]->id = '#jav-msg-error';
			$object [$k]->attr = 'css';
			$object [$k]->content = 'display,block';
		}
		
		$helper = new JAVoiceHelpers ( );		
		echo $helper->parse_JSON_new ( $object );
		exit ();
	}
}
?>