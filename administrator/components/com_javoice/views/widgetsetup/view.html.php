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
class javoiceViewwidgetsetup extends JAVBView {
	function display($tpl = null) {
	//Load left menu
		if (! JRequest::getVar("ajax") && JRequest::getVar('tmpl') != 'component' && JRequest::getVar('viewmenu', 1) != 0) {
			$file = JPATH_COMPONENT.'/views/jaview/view.html.php';
			if (file_exists($file)) {
				include_once($file);
			}
		}
		switch ($this->getLayout ()) {
			case 'widgetlist' :
				$this->widgetlist ();
				break;
			default :
				{
					$this->displayItems ();
					break;
				}
		}
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
				
		$modelvoicetypes = JAVBModel::getInstance ( 'voicetypes', 'javoiceModel' );
		
		$voicetypes = $modelvoicetypes->getDyamicItems ( ' AND t.published=1 ','t.title DESC',0, 0,' t.alias ' );
		$strvoicetypes='';
		if($voicetypes)
			$strvoicetypes = implode(", ",$voicetypes);
		
		$voicetypesID = $modelvoicetypes->getDyamicItems ( ' AND t.published=1 ','t.title DESC',0, 0,' t.id ' );
		$voicetypesID = isset($voicetypesID)?implode(",", $voicetypesID):'';
		$where_more='';
		$modelforums = JAVBModel::getInstance ( 'forums', 'javoiceModel' );
		$where_more .= " and f.published=1 and ft.voice_types_id in ($voicetypesID)";
		$join =  'INNER JOIN #__jav_forums_has_voice_types as ft ON ft.forums_id=f.id';
		
		$forums = $modelforums->getDyamicItems ( $where_more, 0, 4, ' f.title desc','f.alias', $join);
		$strforum='';
		if($forums){
			$strforum = implode(", ",$forums);
		}		
		
		$script = " <script type=\"text/javascript\" src=\"".JURI::root()."components/com_javoice/asset/js/ja.widget.js\"></script>"
		."\n <script type=\"text/javascript\">"
		."\n 	JAV_Widget.show({"
		."\n 		url: '".JURI::root()."',"
		."\n 		voicetypes:'$strvoicetypes',//null for all"
		."\n 		forums: '$strforum',//null for all, it may be number, alias, or title"		
		."\n 		number_voices:20,"
		."\n 		alignment: 'right',"
		."\n 		mode_display: 'image2', //image1 for horizontal, image2 for vertical, text for horizontal text"
		."\n 		width : '520',"
		."\n 		height : '600',"		
		."\n 		feedback_title : 'Feedback'"
		."\n 	})"
		."\n </script>";
		$script =  htmlentities($script);
		$this->assignRef('script',$script);
		$this->assignRef('strforum',$strforum);
		$this->assignRef('strvoicetypes',$strvoicetypes);
	}
	function widgetlist(){
				
		$modelvoicetypes = JAVBModel::getInstance ( 'voicetypes', 'javoiceModel' );
		$modelvoicetypesstatus = JAVBModel::getInstance ( 'voicetypesstatus', 'javoiceModel' );
		
		$voicetypes = $modelvoicetypes->getDyamicItems ( ' AND t.published=1 ','t.title DESC',0, 0,' t.alias ' );
		$strvoicetypes='';
		if($voicetypes)
			$strvoicetypes = implode(", ",$voicetypes);
		
		$voicetypesID = $modelvoicetypes->getDyamicItems ( ' AND t.published=1 ','t.title DESC',0, 0,' t.id ' );
		$voicetypesID = is_array($voicetypesID)?implode(",", $voicetypesID):'';
		$where_more='';
		$modelforums = JAVBModel::getInstance ( 'forums', 'javoiceModel' );
		$where_more .= " and f.published=1 and ft.voice_types_id in ($voicetypesID)";
		$join =  'INNER JOIN #__jav_forums_has_voice_types as ft ON ft.forums_id=f.id';
		
		$forums = $modelforums->getDyamicItems ( $where_more, 0, 4, ' f.title desc','DISTINCT f.alias', $join);
		$strforum='';
		if($forums){
			$strforum = implode(", ",$forums);
		}		
		
		$status = $modelvoicetypesstatus->getItemsDyamic('DISTINCT s.title','','',0,0,1);
		$statusStr = "";
		if($status){
			$statusStr = implode(",",$status);
		}
		
		$script ="<div id = 'jav_widget_content'></div>" 
		."\n <script type=\"text/javascript\" src=\"".JURI::root()."components/com_javoice/asset/js/ja.widget.js\"></script>"
		."\n <script type=\"text/javascript\">"
		."\n 	JAV_Widget.show({"
		."\n 		url: '".JURI::root()."',"
		."\n 		voicetypes:'$strvoicetypes',//null for all"
		."\n 		forums: '$strforum',//null for all, it may be number, alias, or title"		
		."\n 		number_voices:20,"
		."\n 		width : '800',"
		."\n 		height : '600',"		
		."\n 		status : '',//null for all"
		."\n 		creator : '',//null for all"
		."\n 		created_before : '',//null for all"
		."\n 		created_after : '',//null for all"
		."\n 		link_target : '_blank',"
		."\n 		type : 'iframe',"
		."\n 		view_all_button : 'yes'"		
		."\n 	})"
		."\n </script>";
		
		$script =  htmlentities($script);
		$this->assignRef('script',$script);
		$this->assignRef('strforum',$strforum);
		$this->assignRef('strvoicetypes',$strvoicetypes);		
	}
	function getTabs() {		
		$option = JRequest::getCmd('option');
		$items = array('widgetsetup'=>"Widget Setup","widgetlist"=>"Widget List");
		$task = JRequest::getVar('task','widgetsetup');
		$tabs = '';
		
		$tabs .= '<fieldset class="adminform">
					<div class="submenu-box">
						<div class="submenu-pad">
							<ul id="submenu" class="configuration">';
		foreach ( $items as $key=>$value ) {
			$activetab = '';
			if ($key == $task) {
				$activetab = ' class="active focused" ';
			}
			$tabs .= "<li id=\"submenu-li-$key\">
			<a  $activetab  href=\"index.php?option=$option&view=widgetsetup&task=$key\">";
			$tabs .= $value . '</a></li> ';
		}
		$tabs .= '				</ul>
							<div class="clr"></div>
						</div>
					</div>
					<div class="clr"></div>
				</fieldset>';
		echo  $tabs;
	}	
	
}	
