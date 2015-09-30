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
defined ( '_JEXEC' ) or die ( 'Restricted access' );

jimport ( 'joomla.application.component.view' );

/**
 * @package		Joomla
 * @subpackage	Contacts
 */
class javoiceViewitems extends JAVBView {
	
	function display($tpl = null) {
	//Load left menu
		if (! JRequest::getVar("ajax") && JRequest::getVar('tmpl') != 'component' && JRequest::getVar('viewmenu', 1) != 0) {
			$file = JPATH_COMPONENT.'/views/jaview/view.html.php';
			if (file_exists($file)) {
				include_once($file);
			}
		}
		switch ($this->getLayout ()) {
			
			case 'form' :
				$this->edit ();
				
				break;
			case 'response':
				$this->response();
				break;
			default :
				{
					$this->displayItems ();
					
					break;
				}
		}
		$this->addToolbar();
		parent::display ( $tpl );
		// Display menu footer
		if (!JRequest::getVar("ajax") && JRequest::getVar('tmpl') != 'component' && JRequest::getVar('viewmenu', 1) != 0) {
			$file = JPATH_COMPONENT_ADMINISTRATOR . "/views/jaview/tmpl/footer.php";
			if (file_exists($file)) {
				include_once($file);
			}
		}
	}
	function displayItems() {
		
		JHTML::_ ( 'behavior.calendar' );
		
		$model = $this->getModel ( 'items' );

		$lists = $model->_getVars_admin ();

		$lists['create_date'] = JRequest::getVar('createdate',NULL);
				
		$modelvoicetypes = JAVBModel::getInstance ( 'voicetypes', 'javoiceModel' );
		
		$voicetypes = $modelvoicetypes->getItems ( );			

		if($voicetypes && !$lists['voicetypes']){
			$lists['voicetypes']=$voicetypes[0]->id;
		}
		$has_answer = 0;
		if($voicetypes){
			foreach ($voicetypes as $voicetype){
				if($voicetype->id ==$lists['voicetypes'] ){
					$has_answer = $voicetype->has_answer;
				}
			}
		}
		$tabs=$this->getTabs($voicetypes,$lists['voicetypes']);
		
		$modelforums = JAVBModel::getInstance ( 'forums', 'javoiceModel' );
		
		$join = " 	INNER JOIN #__jav_forums_has_voice_types t ON f.id=t.forums_id ";
		$where = " AND t.voice_types_id='".(int)$lists['voicetypes']."'";		
		
		$forums = $modelforums->getItems ( $where,0,0,' f.id desc','',$join );
		
		if (! is_array ( $forums ))
			
			$forums = array ();
		
		$forumsHTML = array_merge ( array (JHTML::_ ( 'select.option', '0', JText::_('SELECT_FORUMS' ), 'id', 'title' ) ), $forums );
		
		$displayForums = JHTML::_ ( 'select.genericlist', $forumsHTML, 'forums', 'class="inputbox" size="1" onchange="form.submit()"', 'id', 'title', $lists['forums'] );
		
		$modelvoicetypesstatus = JAVBModel::getInstance ( 'voicetypesstatus', 'javoiceModel' );
		
		$displayStatus = $modelvoicetypesstatus->displaySelectOptgroup ( $lists['voicetypes'] ,$lists['status'],"name='status' id ='status' onchange=\"form.submit()\"",'',JText::_("STATUS"));

		$fiel = " f.title as forums_title,t.title as voice_types_title,t.has_answer";
		
		$join = " 	INNER JOIN #__jav_forums as f ON f.id=i.forums_id 
		
 					INNER JOIN #__jav_voice_types as t ON t.id=i.voice_types_id ";
		
		$where = $model->getWhereClause ( $lists);
		if($lists['create_date'])$where.=" AND i.create_date >=".(int)$lists['create_date'];

		//echo $where;
		//order by
		$orderby = $lists ['filter_order'] . ' ' . $lists ['filter_order_Dir'];
		
		$total = $model->getTotal ( $where ,$join);
		//limit
		
		if ($lists ['limit'] > $total) {
			$lists ['limitstart'] = 0;
		}
		if ($lists ['limit'] == 0){$lists ['limitstart'] = 0;}
		
		jimport ( 'joomla.html.pagination' );
		
		$pageNav = new JPagination ( $total, $lists ['limitstart'], $lists ['limit'] );
		
		$items = $model->getItems ( $where, $orderby, $lists ['limitstart'], $lists ['limit'], $fiel, $join );

		$model->parseItems ( $items );
				
		
		$this->assign ( 'displayForums', $displayForums );
		
		$this->assign ( 'items', $items );
		
		$this->assign ( 'tabs', $tabs );
		
		$this->assign ( 'lists', $lists );
		
		$this->assign ( 'displayStatus', $displayStatus );
		
		$this->assign ( 'pageNav', $pageNav );
		
		$this->assign ( 'has_answer', $has_answer );
		
		
	}
	function addToolbar(){
		$text = 'JA Items';
		JToolBarHelper::title ( JText::_ ( $text ) );
		$task = JRequest::getWord ( 'task', '' );
		switch ($task) {
			case 'add' :
			case 'save' :
			case 'apply' :
			case 'edit' :
				{
					JToolBarHelper::apply ();
					JToolBarHelper::save ();
					JToolBarHelper::cancel ();
				}
				break;
			default :
				{
					JToolBarHelper::publishList();
					JToolBarHelper::unpublishList();
					//JToolBarHelper::editListX();
					JToolBarHelper::addNew();
					JToolBarHelper::deleteList ( JText::_('WARNING_BEFOR_DELETED' ) );
				}
		}
	}
	function edit($item = null) {
		global $javconfig;
		JHTML::_ ( 'behavior.calendar' );
		
		$model = $this->getModel ( 'items' );
		$voicetype = JRequest::getVar('voicetypes',NULL);

		if (! $item) {
			
			$item = $this->get ( 'Item' );
			
			$postback = JRequest::getVar ( 'postback' );
			
			if (! $postback) {
				
				$post = JRequest::get ( 'request', JREQUEST_ALLOWHTML );
				
				$item->bind ( $post );
				
				if (! is_numeric ( $item->create_date ))
					$item->create_date = strtotime ( $item->create_date );
			}
			if($javconfig["systems"]->get("is_enable_tagging", 0)){
				$modelTags 	= JAVBModel::getInstance ( 'tags', 'javoiceModel' );				
				$listTags 	= $modelTags->getTagByVoice($item->id);
				$this->assign ( 'listTags', $listTags ); 				
			}			
		}
		if (! $item->create_date)
			$item->create_date = time ();
		
		$number = JRequest::getVar ( 'numbet', 0 );
		
		$modelforums = JAVBModel::getInstance ( 'forums', 'javoiceModel' );
		

		$urlrequestforum = "index.php?tmpl=component&option=com_javoice&view=forums&task=changeforumbyvoicetypeid";
		
		$urlrequeststatus = "index.php?tmpl=component&option=com_javoice&view=voicetypesstatus&task=changestatusbyvoicetypeid";
		
		$modelvoicetypes = JAVBModel::getInstance ( 'voicetypes', 'javoiceModel' );
	
		$voicetypes = $modelvoicetypes->getItems ( '', ' t.ordering ', 0, 0, '', '' );
		
		if (! is_array ( $voicetypes ))
			$voicetypes = array ();
		
		$onchange = "changeForumByVoiceType('$urlrequestforum',this.value,'$urlrequeststatus');";

		$displayVoicetypes = JHTML::_ ( 'select.genericlist', $voicetypes, 'voice_types_id', "class=\"inputbox\" size=\"1\" onchange=$onchange ", 'id', 'title', $voicetype );
		
		$modelvoicetypesstatus = JAVBModel::getInstance ( 'voicetypesstatus', 'javoiceModel' );

		$fids = '0';
		
		$tem = $modelforums->getForumByPermission ();
		
		if ($tem)
			$fids = implode ( ',', $tem );
		
		$where = " AND f.id in ($fids) ";
		
		$joins = " INNER JOIN #__jav_forums_has_voice_types as vt ON f.id  = vt.forums_id ";
		
		$where.=" AND vt.voice_types_id = $voicetype";
		$forums = $modelforums->getItems ( $where,1000,0,'f.title','',$joins);							
		
		$displayForums = JHTML::_ ( 'select.genericlist', $forums, 'forums_id', "class=\"inputbox\" size=\"1\" ", 'id', 'title', $item->forums_id );
				
		if (! is_array ( $forums ))
			
			$forums = array ();
				
		$displaystatus = $modelvoicetypesstatus->displaySelectOptgroup ( $voicetype, $item->voice_type_status_id, "id='voice_type_status_id' name='voice_type_status_id'  " );
		
		$mess = $model->initError ();
		
		$user = JFactory::getUser ( $item->user_id );
		
		$this->assign ( 'displayVoicetypes', $displayVoicetypes );
		
		$this->assign ( 'displayForums', $displayForums );
		
		$this->assign ( 'displaystatus', $displaystatus );
		
		$this->assign ( 'user', $user );
		
		$this->assignRef ( 'voicetype', $voicetype );
		
		$this->assignRef ( 'item', $item );
		
		$this->assignRef ( 'number', $number );
		
		$this->assignRef ( 'mess', $mess );
				
		$this->assign("is_show_embed_video", $javconfig['plugin']->get('is_show_embed_video',1));
		$this->assign("enable_smileys", $javconfig['plugin']->get('enable_smileys',1));
		$this->assign("enable_bbcode", $javconfig['plugin']->get('enable_bbcode',1));
		$this->assign("enable_after_the_deadline", $javconfig['plugin']->get('enable_after_the_deadline',1));
		$this->assign("is_attach_image", $javconfig['plugin']->get('is_attach_image',1));
		$this->assign("total_attach_file", $javconfig['plugin']->get('total_attach_file',1));			
		$this->assign("max_size_attach_file", $javconfig['plugin']->get('max_size_attach_file',1));
		$this->assign("is_enable_captcha", $javconfig['plugin']->get('is_enable_captcha',1));
		$this->assign("is_enable_captcha_user", $javconfig['plugin']->get('is_enable_captcha_user',1));
		//$this->assign("textAreaID", "newVoiceContentReply");
	
	}
  	function getTabs($voicetypes,$active) {	
		$option = JRequest::getCmd('option');
	  	$tabs='';	  		
  		if(count($voicetypes)>0){
				$tabs.= '<fieldset class="adminform">
							<div class="submenu-box">
								<div class="submenu-pad">
									<ul id="submenu" class="configuration">';
				foreach ($voicetypes as $voicetype){
					$tabs.='<li><a href="index.php?option='.$option.'&view=items&voicetypes='.$voicetype->id.'"';
					if ($active == $voicetype->id) {
						$tabs .= ' class="active" ';
					}		
					$tabs.= '>';
					$tabs .= $voicetype->title.'</a></li>';	
				}
				$tabs.=	'				</ul>
									<div class="clr"></div>
								</div>
							</div>
							<div class="clr"></div>
						</fieldset>';
  		}
		return $tabs;
	}	
	function response(){
		global $javconfig;
		$model = JAVBModel::getInstance ( 'items', 'javoiceModel' );
		$type = JRequest::getVar('type','admin_response');
		if (!isset($item)) {			
			$item = $model->getAdmin_response();
		}	
		
		$cid[0] = $item->item_id;
		$row = $model->getItem($cid)	;
		$item->item_title = $row?$row->title:'';
		$response = JFactory::getUser($item->user_id);
		$item->responsename = $response?$response->username:''; 
		$this->assign('item',$item);
		$this->assign('type',$type);
		
		$this->assign("is_show_embed_video", $javconfig['plugin']->get('is_show_embed_video',1));
		$this->assign("enable_smileys", $javconfig['plugin']->get('enable_smileys',1));
		$this->assign("enable_bbcode", $javconfig['plugin']->get('enable_bbcode',1));
		$this->assign("enable_after_the_deadline", $javconfig['plugin']->get('enable_after_the_deadline',1));
		$this->assign("is_attach_image", $javconfig['plugin']->get('is_attach_image',1));
		$this->assign("total_attach_file", $javconfig['plugin']->get('total_attach_file',1));			
		$this->assign("max_size_attach_file", $javconfig['plugin']->get('max_size_attach_file',1));
		$this->assign("is_enable_captcha", $javconfig['plugin']->get('is_enable_captcha',1));
		$this->assign("is_enable_captcha_user", $javconfig['plugin']->get('is_enable_captcha_user',1));
		$this->assign("textAreaID", "newVoiceContentReply");
	}
}	