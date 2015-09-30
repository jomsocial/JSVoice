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
class javoiceViewvoice extends JAVBView {
	function display($tpl = null) {
		//Load left menu
		if (! JRequest::getVar("ajax") && JRequest::getVar('tmpl') != 'component' && JRequest::getVar('viewmenu', 1) != 0) {
			$file = JPATH_COMPONENT.'/views/jaview/view.html.php';
			if (file_exists($file)) {
				include_once($file);
			}
		}
		
		$layout = JRequest::getVar ( 'layout', 'statistic' );
		if($layout == 'statistic'){
			$this->statistic ();
		}
						
		$this->setLayout ( $layout );
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
	function addToolbar(){
		JToolBarHelper::title ( 'JA Voice', 'generic.png' );
	}
	function statistic() {
		
		$user = JFactory::getUser ();
		
		$latvisited = strtotime ( $user->lastvisitDate )?strtotime ( $user->lastvisitDate ):0;
		
		$model_voicetype = JAVBModel::getInstance ( 'voicetypes', 'javoiceModel' );
		
		$voiceTypes = $model_voicetype->getItems ( '', ' t.title ' );
		
		$model = JAVBModel::getInstance ( 'actionslog', 'javoiceModel' );
		
		$model_items = JAVBModel::getInstance ( 'items', 'javoiceModel' );
		
		$join = " 	INNER JOIN #__jav_voice_types as t ON t.id=i.voice_types_id";
		
		$lifetime = array ();
		$lastest = array();
		
		if (count ( $voiceTypes ) > 0) {
			
			foreach ( $voiceTypes as $voiceType ) {
				
				$where = " AND t.id= $voiceType->id ";//AND i.create_date > $latvisited ";
				
				$lifetime[] = $model_items->getTotal ( $where, $join );

				$join = " 	INNER JOIN #__jav_forums as f ON f.id=i.forums_id 
		
 				INNER JOIN #__jav_voice_types as t ON t.id=i.voice_types_id ";				
				$where.=" AND i.create_date > $latvisited ";
				$lastest[] = $model_items->getTotal ( $where, $join );				
			}
		}
		$where_more = " AND l.type ='Report spam' AND l.user_id = -1 ";
		$join = " INNER JOIN #__jav_items as i ON i.id = l.ref_id ";
		$lastest_spam = $model->getDistinctItems($where_more,0, 10,' l.time DESC',$join);
		$lastest_spam = $model->parseItems($lastest_spam);
		
		$where_more = " AND (l.type ='Report spam' OR l.type ='Change status')  AND l.user_id >0 ";
		$join = " INNER JOIN #__jav_items as i ON i.id = l.ref_id  ";
		$lastest_change = $model->getDistinctItems($where_more,0, 10,' l.time DESC',$join);
		
		$lastest_change = $model->parseItems($lastest_change);
		
		$where=" AND r.type='admin_response'";
		$join = " INNER JOIN #__jav_items as i ON i.id = r.item_id ";
		$admin_response = $model_items->getAdmin_responses($where, 0, 10,' r.id DESC','',$join);
		$model_items->parseAdmin_response($admin_response);
		
		$this->assign ( 'lastest_spam', $lastest_spam );
		$this->assign ( 'latvisited', $latvisited );
		$this->assign ( 'lastest_change', $lastest_change );
		$this->assign ( 'admin_response', $admin_response );
		$this->assign ( 'voiceTypes', $voiceTypes );
		$this->assign ( 'lastest', $lastest );
		$this->assign ( 'lifetime', $lifetime );
	}
	
	
		
	function menu() {
		global $JVVERSION;
		
		$latest_version ='';
		$version_link = JAVoiceHelpers::get_Version_Link();
		$layout = JRequest::getVar ( 'layout', 'statistic' );
		$cid = JRequest::getVar ( 'cid' );
		if (is_array ( $cid )) {
			JArrayHelper::toInteger ( $cid );
			$cid = $cid [0];
		}
	
		$latest_version = $this->get ( 'LatestVersion' );
		if ($latest_version)
		{
			$version_link['latest_version']['info'] = 'http://wiki.joomlart.com/wiki/JA_Voice/Overview';
			$version_link['latest_version']['upgrade'] = 'http://www.joomlart.com/forums/downloads.php?do=cat&id=139';
			
			$iLatest_version = str_replace('.', '', $latest_version);
			$iLatest_version = trim($iLatest_version);
			$iLatest_version = intval($iLatest_version);
		}
		else 
		{
			$version_link['latest_version']['info'] = '';
			$version_link['latest_version']['upgrade'] = '';
		}
		//$xml = JFactory::getXMLParser( 'simple' );
		$xml = new JSimpleXML();
     	$file = JPATH_COMPONENT.'/javoice.xml';
		$xml->loadFile( $file );
		$out = $xml->document;
		if($out == false){
			$current_version = $JVVERSION;		
		}else{
			$allComments = $out->children();
			foreach ( $allComments as $blogpost) {							
				if($blogpost->name() == "version"){					
					$current_version = $blogpost->data();
					break;
				}
			}			
		}	
		
		$iCurrent_version = str_replace('.', '', $current_version);
		$iCurrent_version = trim($iCurrent_version);
		$iCurrent_version = intval($iCurrent_version);
		?>
		<fieldset class="adminform">
					<div class="submenu-box">
						<div class="submenu-pad">
		<ul id="submenu" class="configuration">
			<li><a href="index.php?option=com_javoice&layout=statistic"
				class="<?php
				if ($layout == null || $layout == 'statistic')
					echo 'active'?>">
										<?php
				echo JText::_('STATISTICS' );
				?>
									</a></li>
			<li><a href="index.php?option=com_javoice&layout=supportandlicense"
				class="<?php
				if ($layout == 'supportandlicense' || $layout == 'verify')
					echo 'active'?>">
										<?php
				echo JText::_('DOCUMENTATION_AND_SUPPORT' );
				?>
									</a></li>			
			<li style="float: right;line-height:30px;">
				<?php 
				if(empty($iLatest_version)){
					 echo JText::_('VERSION' ). ' <b>'.$current_version.'</b>';
				}elseif(!empty($iLatest_version) && $iLatest_version <= $iCurrent_version){
					echo JText::_('YOUR_VERSION'). ': <b><a href="'.$version_link['current_version']['info'].'" target="_blank">'.$current_version.'</a></b>&nbsp;&nbsp;'.JText::_('LATEST_VERSION' ).': <b><a href="'.$version_link['latest_version']['info'].'" target="_blank">'.$latest_version.'</a></b>&nbsp;&nbsp;<font color="Blue"> <i>('.JText::_('SYSTEM_RUNNING_THE_LATEST_VERSION' ).')</i></font>'; 
				}elseif(!empty($iLatest_version) && $iLatest_version > $iCurrent_version){
					echo JText::_('YOUR_VERSION'). ': <b><a href="'.$version_link['current_version']['info'].'" target="_blank">'.$current_version.'</a></b>&nbsp;&nbsp;'.JText::_('LATEST_VERSION' ).': <b>';
					echo isset($version_link['latest_version'])?'<a href="'.$version_link['latest_version']['info'].'" target="_blank">'.$latest_version.'</a>':$latest_version;echo '</b>&nbsp;&nbsp;<span style="background-color:rgb(255,255,0);color:Red;font-weight:bold;">'.JText::_('NEW_VERSION_AVAILABLE'). '</span>';
					if (isset($version_link['latest_version'])) echo '<a target="_blank" href="'.$version_link['latest_version']['upgrade'].'" title="'.JText::_('CLICK_HERE_TO_DOWNLOAD_LATEST_VERSION').'">'.JText::_('UPGRADE_NOW' ).'</a>';
				}?>					
			</li>
		</ul>
		<div class="clr"></div>
						</div>
					</div>
					<div class="clr"></div>
				</fieldset>
<?php
	}

}
?>