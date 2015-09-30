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
class javoiceViewconfigs extends JAVBView {
	/**
	 * Display the view
	 */
	function display($tmpl = null) {
	//Load left menu
		
		if (! JRequest::getVar("ajax") && JRequest::getVar('tmpl') != 'component' && JRequest::getVar('viewmenu', 1) != 0) {
			$file = JPATH_COMPONENT.'/views/jaview/view.html.php';
			if (file_exists($file)) {
				include_once($file);
			}
		}
		$task = JRequest::getVar ( 'task', NULL );
		$group = JRequest::getVar ( 'group', 'systems' );
		$model = $this->getModel ( 'configs' );
		$item = $model->getItems ();
		$data = $item->data;
		$params = class_exists('JRegistry') ? new JRegistry($data) : new JParameter($data);				
		
		if($task) $this->setLayout ( $task );
		else $this->setLayout ( $group );
		switch ($group){
			case 'integrate':
				$this->integrate ();
				break;
			case 'systems':
				if($task) $this->changeStatus ();
				else $this->system ( $params );
				break;
			case 'permissions':
				if($task) $this->editpermissions ( $params );
				else $this->permissions ( $params );
				break;							
		}
		
		$this->assignRef ( 'group', $group );
		$this->assignRef ( 'params', $params );
		$this->assignRef ( 'cid', $item->id );
		
		$this->addToolbar();
		
		parent::display ( $tmpl );
		
		// Display menu footer
		if (!JRequest::getVar("ajax") && JRequest::getVar('tmpl') != 'component' && JRequest::getVar('viewmenu', 1) != 0) {
			$file = JPATH_COMPONENT_ADMINISTRATOR . "/views/jaview/tmpl/footer.php";
			if (file_exists($file)) {
				include_once($file);
			}
		}
	}
	function addToolbar(){
		$group = JRequest::getWord ( 'group', '' );
		JToolBarHelper::title ( JText::_('CONFIGURATION_MANAGER' ) );
		
		if ($group == 'integrate')
			JToolBarHelper::makeDefault ( 'makedefault' );
		elseif($group=='permissions'){
			JToolBarHelper::addNew('add','Add User');
			JToolBarHelper::deleteList();
		}
		else
			JToolBarHelper::save ();
	}
	
	function integrate() {
		$model = $this->getModel ( 'configs' );
		
		$application = array (
							"intensedebate" => "IntenseDebate", 
							"disqus" => "Disqus",
							'jacomment'=>"JA Comment Component",
							'jcomments'=>"JComment Component"
							);
		$this->assignRef ( 'application', $application );
		
		$com_jacomment = JAVoiceHelpers::checkComponent('com_jacomment');
		$this->assignRef ( 'com_jacomment', $com_jacomment );
		
		$com_jomcomment = JAVoiceHelpers::checkComponent('com_jomcomment');
		$this->assignRef ( 'com_jomcomment', $com_jomcomment );
		
		$com_jcomment = JAVoiceHelpers::checkComponent('com_jcomments');
		$this->assignRef ( 'com_jcomments', $com_jcomment );
		
		$task = JRequest::getVar ( 'task', NULL );
		$number = JRequest::getInt ( 'number', 0 );
		$cid = JRequest::getVar ( 'cid', array (0 ), '', 'array' );
		$system = '';
		if ($cid) $system = $cid [0];
		$modelvoicetypes = JAVBModel::getInstance ( 'voicetypes', 'javoiceModel' );
		$voicetypes = $modelvoicetypes->getItems ( ' AND t.published=1 ' );
		$tabs = '';
		$active = '';
		if (count ( $voicetypes ) > 0) {
			$active = JRequest::getVar ( 'voicetypes', $voicetypes [0]->id );
			$tabs = $this->getVoiceTypeTabs ( $voicetypes, $active, $system );
		}
		
		$this->assignRef ( 'application', $application );
		$this->assignRef ( 'task', $task );
		$this->assignRef ( 'number', $number );
		$this->assignRef ( 'system', $system );
		$this->assignRef ( 'active', $active );
		$this->assignRef ( 'tabs', $tabs );
		$this->assignRef ( 'voicetypes', $voicetypes );
		
		
	}
	function system($params) {
		$helper = new JAVoiceHelpers ( );		
		$modelvoicetypes = JAVBModel::getInstance ( 'voicetypes', 'javoiceModel' );
		$voicetypes = $modelvoicetypes->getItems ( ' AND t.published=1 ' );
		$model = $this->getModel ( 'configs' );
		$model->parse ( $params, $voicetypes );
		$groupUsers =$params->get('user_group',null);
		$groupUser="";
		if(isset($groupUsers) && $groupUsers)$groupUser = explode(",",$groupUsers);
		else $groupUser=0;
		$groupUsers = $helper->getGroupUser ( '', 'user_group[]', 'class="inputbox" size="5" multiple="multiple"', $groupUser, 1 );
		$this->assign ( 'groupUsers', $groupUsers );		
		$this->assignRef ( 'voicetypes', $voicetypes );
	}
	function changeStatus() {
		$model = JAVBModel::getInstance ( 'voicetypesstatus', 'javoiceModel' );
		$where = '';
		$where .= " AND s.parent_id=0 ";
		$joins = "INNER JOIN #__jav_voice_types AS t ON s.voice_types_id=t.id";
		$cid = JRequest::getVar ( 'cid', array (0 ), '', 'array' );
		$voice_types_id = '';
		if ($cid) $voice_types_id = $cid [0];
		$where .= " AND s.voice_types_id=$voice_types_id";
		$orderby = '';
		$parents = $model->getItems ( $where, $orderby, 0, 0, '', $joins );
		$where_more = " AND s.voice_types_id = $voice_types_id";
		$items = $model->parseItems ( $parents, $orderby, '', 1 ,'',0);
		
		$this->assign ( 'items', $items );
		$this->assign ( 'voice_types_id', $voice_types_id );
	
	}
	function permissions($params) {		
					
		$model = JAVBModel::getInstance ( 'permissions', 'javoiceModel' );
		$lists = $model->_getVars ();
		$where_more = '';
		$order = '';
		if (isset ( $lists ['filter_order'] ) && $lists ['filter_order'] != '') {
			$order = $lists ['filter_order'] . ' ' . @$lists ['filter_order_Dir'];
		}
		$ids = $params->get ( 'permissions', '' );
		$more = '';
		$joins = '';
		
		if ($ids != '') {
			$where_more .= " AND a.id IN($ids)";
		}else {
			$joins = ' LEFT JOIN #__user_usergroup_map AS map2'
					. ' ON map2.user_id = a.id';
			$where_more .= " AND map2.group_id IN (SELECT id FROM #__usergroups WHERE parent_id=6)";
		}
		jimport ( 'joomla.html.pagination' );
		$total = $model->getTotal ( $where_more,$joins);
		$pageNav = new JPagination ( $total, $lists ['limitstart'], $lists ['limit'] );
		
		
		$items = $model->getItems ( $where_more, $lists ['limit'], $lists ['limitstart'], trim ( $order ) );
		
		$this->assign ( 'items', $items );
		$this->assign ( 'lists', $lists );
		$this->assign ( 'pageNav', $pageNav );
	}
	function editpermissions($params) {								
		$mainframe = JFactory::getApplication();
		$items = array();
		$option = 'permissions';
		$helper = new JAVoiceHelpers ( );
		//$postback = $helper->isPostBack ();
		$model = JAVBModel::getInstance ( 'permissions', 'javoiceModel' );
		$lists = $model->_getVars ();
		$where = "";				
		$lists ['groupname'] = JRequest::getInt("groupname",0); 		
		if($lists ['groupname']){
			$where = ' AND map2.group_id = '.$lists ['groupname'];	
		}
		$searchName = JRequest::getVar("search","");
		if($searchName){
			$where .= " AND a.username LIKE '%{$searchName}%'";	
		}
		$listUser = $params->get('permissions');
		
		if($listUser){
			$where .= " AND a.id NOT IN(".$listUser.")";
		}
		
		//if ($postback) {									
		$items = $model->getItems ($where);						
		//}
		$this->assign ( 'items', $items );

		$groupUser = $helper->getGroupUser ( '', 'groupname', 'class="inputbox" size="1"', $lists ['groupname'], 1 );		
		$groupUser = JHTML::_('select.genericlist',   $groupUser, 'groupname', 'class="inputbox" size="1"', 'value', 'text', $lists ['groupname']);
		$this->assign ( 'groupUser', $groupUser );
		//$this->assign ( 'postback', $postback );
		$this->assign ( 'lists', $lists );
		$this->assign ( 'params', $params );
		$this->assign ( 'items', $items );
	
	}
	function getVoiceTypeTabs($voicetypes, $active, $system) {	
		$option = JRequest::getCmd('option');
		$tabs = '';
		
		$tabs .= '<fieldset class="border-radius">
					<div class="submenu-box">
						<div class="submenu-pad">
							<ul id="submenu-tmpl" class="configuration">';
		if($voicetypes){						
			foreach ( $voicetypes as $voicetype ) {
				$activetab = '';
				if ($active == $voicetype->id) {
					$activetab = ' class="active focused" ';
				}
				$tabs .= "<li id=\"submenu-li-$voicetype->id\"  $activetab>
				<a onclick=\"displayTextArea('$voicetype->id','$system');return false;\" href=\"index.php?option=$option&view=items&voicetypes=$voicetype->id \">";
				$tabs .= $voicetype->title . '</a></li> | ';
			}
		}
		$tabs .= '				</ul>
							<div class="clr"></div>
						</div>
					</div>
					<div class="clr"></div>
				</fieldset>';
		return $tabs;
	}
	
	function getTabs() {
		$option = JRequest::getCmd('option');
		$group = JRequest::getVar ( 'group', '' );
		$tabs = '<div class="submenu-box">
						<div class="submenu-pad">
							<ul id="submenu" class="configuration">
								<li><a href="index.php?option=' . $option . '&view=configs&group=systems"';
		if ($group == 'systems' || $group == '') {
			$tabs .= ' class="active" ';
		}
		$tabs .= '>';
		$tabs .= JText::_('GENERAL' ) . '</a></li>';
								
		$tabs .= '<li><a href="index.php?option=' . $option . '&view=configs&group=integrate"';
		if ($group == 'integrate') {
			$tabs .= ' class="active" ';
		}
		$tabs .= '>';
		
		$tabs .= JText::_('COMMENT_SYSTEM' ) . '</a></li>';
		
		$tabs .= '<li><a href="index.php?option=' . $option . '&view=configs&group=permissions"';
		if ($group == 'permissions') {
			$tabs .= ' class="active" ';
		}
		$tabs .= '>';
		
		$tabs .= JText::_('ADMIN__MODERATOR' ) . '</a></li>';
		
		$tabs .= '<li><a href="index.php?option=' . $option . '&view=configs&group=plugin"';
		if ($group == 'plugin') {
			$tabs .= ' class="active" ';
		}
		$tabs .= '>';
		
		$tabs .= JText::_('LAYOUT__PLUGINS' ) . '</a></li>';
				
		$tabs .= '				</ul>
							<div class="clr"></div>
						</div>
					</div>
					<div class="clr"></div>';
		// $tab1s.= " <script type=\"text/javascript\" src=\"".JURI::root()."components/com_javoice/asset/js/ja.widget.js\"></script>";
		
		// $tab1s.= "\n <script type=\"text/javascript\">"
				// ."\n 	JAV_Widget.show({"
				// ."\n 		url: 'http://javoice.joomlart.com/',"
				// ."\n 		voicetypes:'',//null for all"
				// ."\n 		forums: '',//null for all, it may be number, alias, or title"		
				// ."\n 		number_voices:5,"
				// ."\n 		alignment: 'right',"
				// ."\n 		mode_display: 'image2', //image1 for horizontal, image2 for vertical, text for horizontal text"
				// ."\n 		width : '520',"
				// ."\n 		height : '600',"		
				// ."\n 		feedback_title : 'Feedback'"
				// ."\n 	})"
				// ."\n </script>";
		return $tabs;
	}
	
}
?>