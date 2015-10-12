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
// no direct access
defined('_JEXEC') or die('Restricted access');
if(!defined('DS')){
	define('DS', DIRECTORY_SEPARATOR);
}
/**
 * Enter description here...
 *
 */
jimport('joomla.html.parameter');
// Component Helper
jimport('joomla.application.component.helper');

$GLOBALS ['JVPRODUCTKEY'] = 'COM_JAVOICE';

class JAVoiceHelpers {
	/**
	 * Enter description here...
	 * giapnd add
	 * @return unknown
	 */
	public static function isPostBack() {
		if (JRequest::getVar ( 'task' ) == 'add')
			return FALSE;
		return count ( $_POST );
	}
	/**
	 * giapnd add
	 */
	function generatDate($timestamp, $mid = 0, $format = "d/M/Y H:i:s") {
		if (intval ( $timestamp ) == 0) {
			return "<span class=\"small\"> ". JText::_('NOT_AVAILABLE')."</span>";
		}
		$cal = explode ( " ", date ( $format, $timestamp ) );
		if ($mid != 0) {
			if ($cal [0] == date ( "d/M/Y" )) {
				return JText::_("TODAY");
			} else {
				return $cal [0];
			}
		} else {
			return $cal [0] . " ".JText::_('AT')." " . $cal [1];
		}
	}
	
	function getSizeUploadFile($action = ''){
	  	global $javconfig;
	  	$maxSizeServer = (int)$this->checkUploadSize();
	  	$maxSize = $javconfig["plugin"]->get("max_size_attach_file", $maxSizeServer);
	  	$maxSizeAttach = min($maxSize, $maxSizeServer);
	  	if($action){
	  		return min($maxSize, $maxSizeServer) * 1000000;
	  	}else{ 
	  		return min($maxSize, $maxSizeServer)."M";
	  	}		
	}
  
  	function checkUploadSize(){    	
  		if ( ! $filesize = ini_get('upload_max_filesize') ) {
            $filesize = "5M";
        }
		
        if ( $postsize = ini_get('post_max_size') ) {
            return min($filesize, $postsize);
        } else {
            return $filesize;
        }
  	}				 	
	public static function checkPermission($actions = array()){		
		$result	= new JObject;
		$user = JFactory::getUser();
		
		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, 'com_users'));
		}
		
		if($result->get($actions[0]) == 1){
			return true;
		}
		
		return false;				
	}
	/**
	 * giapnd add
	 * return path template current
	 */
	public static function checkFileTemplate($file,$type='css',$folder=''){
		$client	= JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));
		$tBaseDir = $client->path.'/templates';		
		$template=JAVoiceHelpers::templateDefaulte();
		$fileName='';
		if($template){
			$tBaseDir.='/'.$template->name;
			$fileName=$tBaseDir.'/'.$type.'/';
			if($folder)$fileName.=$folder.'/tmpl';
			$fileName.='/'.$file;
			if(!JFile::exists($fileName))
				return FALSE;
		}		
		return $fileName;
	}	
	
	function showMessenger($mes){				
		$k=0;
		$object [$k] = new stdClass ( );
		$object [$k]->id = '#system-message';
		$object [$k]->attr = 'html';
		$object [$k]->content = $this->message ( 0, $mes );
						
		echo $this->parse_JSON_new ( $object );
		exit ();
		
	}
	
	function addSpaceInLongTitle($string){
		$arr_strs = explode(" ", $string);		
		$string = "";		
		
		foreach ($arr_strs as $str){						 		
			if(strlen($str) >15){			
				$subStr = $str;
				$str 	= "";				
				while(strlen($subStr) >15){
					$str   .= substr($subStr, 0 , 15)." ";										
					$subStr = substr($subStr, 15);
				}				
				$str .= $subStr;
			}				 				
			$string .= $str.' ';			
		}
		
		return $string;
	}
	
	public static function templateDefaulte(){
		
		$client	= JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));
		$tBaseDir = $client->path.'/templates';
		//get template xml file info
		$rows = array();
		$rows = JAVoiceHelpers::parseXMLTemplateFiles($tBaseDir);	
		
		$template='';
 		for($i = 0; $i < count($rows); $i++)  {
 		
			if(JAVoiceHelpers::isTemplateDefault($rows[$i]->directory, $client->id))
				$template=$rows[$i];
		}	
		
		return $template;	
	}
	public static function parseXMLTemplateFiles($templateBaseDir)
	{
		// Read the template folder to find templates
		jimport('joomla.filesystem.folder');
		$templateDirs = JFolder::folders($templateBaseDir);

		$rows = array();

		// Check that the directory contains an xml file
		foreach ($templateDirs as $templateDir)
		{
			if(!$data = JAVoiceHelpers::parseXMLTemplateFile($templateBaseDir, $templateDir)){
				continue;
			} else {
				$rows[] = $data;
			}
		}

		return $rows;
	}
	public static function isTemplateDefault($template, $clientId)
	{
		$db = JFactory::getDBO();

		// Get the current default template
		$query = ' SELECT template '
				.' FROM #__template_styles '
				.' WHERE client_id = ' . (int) $clientId;
		$db->setQuery($query);
		$defaultemplate = $db->loadResult();

		return $defaultemplate == $template ? 1 : 0;
	}
	public static function parseXMLTemplateFile($templateBaseDir, $templateDir)
	{
		// Check of the xml file exists
		if(!is_file($templateBaseDir.'/'.$templateDir.'/templateDetails.xml')) {
			return false;
		}

		$xml = JApplicationHelper::parseXMLInstallFile($templateBaseDir.'/'.$templateDir.'/templateDetails.xml');

		if ($xml['type'] != 'template') {
			return false;
		}

		$data = new StdClass();
		$data->directory = $templateDir;

		foreach($xml as $key => $value) {
			$data->$key = $value;
		}

		$data->checked_out = 0;
		$data->mosname = JString::strtolower(str_replace(' ', '_', $data->name));

		return $data;
	}	
	function temp_export($item) {
		$content = '## ************** ' . JText::_('BEGIN_EMAIL_TEMPLATE' ) . ': ' . $item ['name'] . ' ****************##' . "\r\n\r\n";
	
		$content .= '[Email_Template name="' . $item ['name'] . '"';
	
		$content .= ' published="' . $item ['published'] . '" group="' . ( int ) $item ['group'] . '" language="' . $item ['language'] . '"]' . "\r\n";
		
		$content .= '[title]' . "\r\n";
		$content .= $item ['title'] . "\r\n";
		
		$content .= '[subject]' . "\r\n";
		$content .= $item ['subject'] . "\r\n";
		
		$content .= '[content]' . "\r\n";
		$content .= $item ['content'] . "\r\n";
		
		$content .= '[EmailFromName]' . "\r\n";
		$content .= $item ['email_from_name'] . "\r\n";
		
		$content .= '[EmailFromAddress]' . "\r\n";
		$content .= $item ['email_from_address'] . "\r\n";
		$content .= '[/Email_Template]' . "\r\n\r\n";
		$content .= '## ************** ' . JText::_('END_EMAIL_TEMPLATE' ) . ': ' . $item ['name'] . ' ****************##' . "\r\n\r\n\r\n\r\n\r\n\r\n";
		
		return $content;
	}
	public static function getGroupUser($where='',$name='',$attr='',$selected='',$default=0){
		$db = JFactory::getDbo();
		$db->setQuery(
			'SELECT a.id AS value, a.title AS text, COUNT(DISTINCT b.id) AS level' .
			' FROM #__usergroups AS a' .
			' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt' .
			' GROUP BY a.id' .
			' ORDER BY a.lft ASC'
		);
		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseNotice(500, $db->getErrorMsg());
			return null;
		}

		for ($i=0,$n=count($options); $i < $n; $i++) {
			$options[$i]->text = str_repeat('- ',$options[$i]->level).$options[$i]->text;
		}

		if($default){
			$options 	= array_merge ( array (JHTML::_ ( 'select.option', '0', JText::_ ( '-Select Group-' ), 'value', 'text' ) ), $options );
		}
		
		return $options;
	}
	public static function displayNote($message,$type){
		?>
		<div id="jav-system-message">
					<?php echo $message;?>
		</div>		
		<script>
		jQuery(document).ready( function($) {
			var coo = getCookie('hidden_message_<?php echo $type?>');
			if(coo==1)
				$('#jav-system-message').attr('style','display:none');
			else
				$('#jav_help').html('<?php echo JText::_('CLOSE')?>');
		});	
		</script>		
		<?php 	
	}
	/**
	 * end giapnd add
	 */
	/**
	 * Enter description here...
	 *
	 */
	public static function get_config_system() {
		global $javconfig;	
		$mainframe = JFactory::getApplication();
		if (defined ( 'COMPONENT_JAVOICE_CONFIG' ))
			return;
		
		$setup = new stdClass ( );
		$db = JFactory::getDBO ();
		$setup = new stdClass ( );
		$q = 'SELECT * FROM #__jav_configs';
		$db->setQuery ( $q );
		$rows = $db->loadObjectList ();
		if ($rows) {
			foreach ( $rows as $row ) {
				$javconfig [$row->group] = class_exists('JRegistry')? new JRegistry($row->data) : new JParameter($row->data);
			}
		}
		if(!isset($javconfig['plugin'])) $javconfig['plugin'] = class_exists('JRegistry')? new JRegistry('') : new JParameter('');
		define ( 'COMPONENT_JAVOICE_CONFIG', true );
	}
	
	/* Enter description here...
	 *
	 * @param unknown_type $timeStamp
	 * @param unknown_type $mid
	 * @return unknown
	 */
	public static function generatTimeStamp($timeStamp, $mid = 0) {
		$ago = 0;
		if ($mid == 0) {
			$cal = (time () - $timeStamp);
		} else {
			$cal = ($timeStamp - time ());
			if ($cal < 0) {
				$cal = 0 - $cal;
				$ago = 1;
			}
		}
		$d = floor ( $cal / 24 / 60 / 60 );
		$h = floor ( ($cal / 60 / 60 - $d * 24) );
		$m = floor ( $cal / 24 / 60 / 60 / 30 );
		
		if ($mid == 0) {
			if ($d < 3) {
				$str = "<span class=\"small\">" . ($h+$d*24) . "h ago</span>";
			} /*elseif ($d == 1) {
				$str = "<span class=\"class_yesterday\">" . JText::_('YESTERDAY' ) . " " . "</span><span class=\"small\"> +" . $h . "h</span>";
			} elseif ($d == 2) {
				$str = "<span class=\"class_2dayago\">2 " . JText::_('DAYS' ) . " " . JText::_('AGO' ) . "</span>";
			} else {
				//$str = generatDate($timeStamp,1);
				$str = "<span class=\"time_show\">" . $d . "d," . $h . "h " . JText::_('AGO' ) . ".</span>";
			}*/
			elseif($d<120){
				$str = "<span class=\"class_2dayago\"> ". $d. JText::_('DAYS' ) . " " . JText::_('AGO' ) . "</span>";
			}
			else{
				$str = "<span class=\"time_show\"> ". $m. JText::_('MONTHS' ) . " " . JText::_('AGO' ) . "</span>";
			}
			return $str;
		} else {
			if ($d == 0) {
				$str = "<span class=\"class_today\">" . JText::_('TODAY' ) . "</span>";
			} else {
				if ($ago == 1) {
					if ($d == 1) {
						$str = "<span class=\"class_yesterday\">" . JText::_('YESTERDAY' ) . "<span class=\"small\"> +" . $h . "h</span>";
					
					} else {
						//$str = generatDate($timeStamp,1);
						$str = "<span class=\"time_show\">" . $d . "d," . $h . "h " . JText::_('AGO' ) . ".</span>";
					}
				} else {
					if ($d == 1) {
						$str = "<span class=\"class_tomorrow\">" . JText::_('TOMORROW' ) . "</span>";
					} else {
						//$str = generatDate($timeStamp,1);
						$str = "<span class=\"time_show\">" . $d . "d," . $h . "h.</span>";
					}
				}
			}
			return $str;
		}
	}
	
	function check_access() {
		global $javconfig;
		$mainframe = JFactory::getApplication();
		$access = isset($javconfig['systems'])?$javconfig['systems']->get('access', 0):0;
		$user = JFactory::getUser ();
		
		// Check to see if the user has access to view the full article
		$check = 0;
		foreach($user->getAuthorisedViewLevels() as $gkey=>$gVal){
			if ($gVal == $access) {
				$check =1;
				break;
			}
		}
		//print_r($user->guest);die;
		//if current user not allow
		if (! $check) {
			//move to loging form
			if($user->guest){
				// Redirect to login
				$uri = JFactory::getURI ();
				$return = $uri->toString ();
				
				$url = 'index.php?option=com_users&view=login';
				$url .= '&return=' . base64_encode ( $return );
				
				
				//$url	= JRoute::_($url, false);
				$mainframe->redirect ( $url, JText::_('YOU_MUST_LOGIN_FIRST' ) );
			}
			//show messager
			else{
				if(isset($javconfig['systems'])){
					$msg = JText::_(@$javconfig['systems']->get('display_message', 'This site is down for maintenance. Please check back again soon.'));
				}	
				else{
					$msg = JText::_('THIS_SITE_IS_DOWN_FOR_MAINTENANCE_PLEASE_CHECK_BACK_AGAIN_SOON');
				}
				JError::raiseWarning ( 403, $msg );
				return false;
			}
		} 
		//if allow
		else {
			//do not need to raise a notice when allow
			return true;						
		}		
	}
	
	function parse_JSON_new($objects){
		if (! $objects)
			return;
		if(function_exists("json_decode")){			
			$html = json_encode($objects);
		}else{				
			require_once (JPATH_COMPONENT.DS . "/helpers/JSON.php");
			$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
			$result = $json->decode($result);							
		}					
		return $html;
	}
	
	function parse_JSON($objects) {
		if (! $objects)
			return;
		$db = JFactory::getDBO ();
		
		$html = '';
		$item_tem = array ();
		foreach ( $objects as $i => $row ) {
			$tem = array ();
			$item_tem [$i] = '{';
			foreach ( $row as $k => $value ) {
				//$value = $db->Quote($value);
				$tem [$i] [] = "'$k' : " . $db->Quote ( $value ) . "";
			}
			$item_tem [$i] .= implode ( ',', $tem [$i] );
			$item_tem [$i] .= '}';
		}
		
		if ($item_tem)
			$html = implode ( ',', $item_tem );
		
		return $html;
	}
	function parseProperty($type = 'html', $id = 0, $value = '',$reload=0) {
		$object = new stdClass ( );
		$object->type = $type;
		$object->id = $id;
		$object->value = $value;
		if($reload)$object->reload=$reload;
		return $object;
	}
	function parsePropertyPublish($type = 'html', $id = 0,$publish=0,$number=0,$function='publish',$title='Publish',$un='Unpublish') {
		$object = new stdClass ( );
		$object->type = $type;
		$object->id = $id;
		if(!$publish){
			$html = '<a  href="javascript:void(0);" onclick="return listItemTask(\'cb'.$number.'\',\''.$function.'\')" title=\''.$title.'\'><img id="i5" border="0" src="components/com_javoice/asset/images/publish_x.png" alt="Publish"/></a>';
		}
		else {
			$function='un'.$function;
			$html = '<a  href="javascript:void(0);" onclick="return listItemTask(\'cb'.$number.'\',\''.$function.'\')" title=\''.$un.'\'><img id="i5" border="0" src="components/com_javoice/asset/images/tick.png" alt="Unpublish"/></a>';
		}
					
		$object->value = $html;
		return $object;
	}
	function message($iserror = 1, $messages) {
		if ($iserror){
			$content = '<dt class="error">Error</dt>
					<dd class="error message fade">
						<ul id="jav-error">';
			foreach ($messages as $message){
				$content.='<li>' . $message . '</li>';
			}
			$content.='			</ul>
					</dd>';
		}
		else{
			$content = '<dt class="message">Message</dt>
						<dd class="message message fade">
						<ul>';
			if ($messages && is_array($messages)){
				foreach ($messages as $message){
					$content.='<li>' . $message . '</li>';
				}
			}
			else {
				$content.='<li>' . $messages . '</li>';
			}
			$content.='			</ul>
					</dd>';
		}
		return $content;
	}	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $template
	 * @return unknown
	 */
	function getEmailTemplate($temp_name) {
		
		$db=JFactory::getDBO();
		
		$client = JApplicationHelper::getClientInfo ( 0 );
		$params = JComponentHelper::getParams ( 'com_languages' );
		$language = $params->get ( $client->name, 'en-GB' );
		
		$query = "SELECT * FROM #__jav_email_templates WHERE name='$temp_name' and language='$language' and  published=1";
		$db->setQuery ( $query );
		$template = $db->loadObject ();
		
		if (! $template && $language != 'en-GB') {
			$query = "SELECT * FROM #__jav_email_templates WHERE name='$temp_name' and language='en-GB' and  published=1";
			$db->setQuery ( $query );
			$template = $db->loadObject ();
		}
		return $template;
	}
	function getFilterConfig(){
		global $javconfig;
		$mainframe = JFactory::getApplication();
		$filters['{CONFIG_ROOT_URL}']= $mainframe->getCfg ('live_site');
		$filters['{CONFIG_SITE_TITLE}'] = $mainframe->getCfg ('live_site');
		$filters['{ADMIN_EMAIL}'] = $javconfig['systems']->get('fromemail');
		$filters['{SITE_CONTACT_EMAIL}'] = $javconfig['systems']->get('fromemail');
		return $filters;	
	}
	function getLink($link,$title='')
	{
		$link = $_SERVER['HTTP_HOST'].str_replace($_SERVER['HTTP_HOST'], '', $link);
		if($title!='')$link="<a href='$link'>$title</a>";
		return $link;
	}	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $to
	 * @param unknown_type $nameto
	 * @param unknown_type $subject
	 * @param unknown_type $content
	 * @param unknown_type $filters
	 * @param unknown_type $from
	 * @param unknown_type $fromname
	 * @param unknown_type $attachment
	 * @param unknown_type $header
	 * @return unknown
	 */
	function sendmail($to, $nameto, $subject, $content, $filters = "", $from = '', $fromname = '', $attachment = array(), $header = true) {
		global $javconfig;
		
		if ($header) {
			$header = $this->getEmailTemplate ( "mailheader" );
			$footer = $this->getEmailTemplate ( "mailfooter" );
			if ($header)
				$content = $header->emailcontent . "\n" . $content . "\n\n";
			if ($footer) {
				$content .= $footer->emailcontent;
			}
		}

		if (is_array ( $filters )) {
			foreach ( $filters as $key => $value ) {
				$subject = str_replace ( $key, $value, $subject );
				$content = str_replace ( $key, $value, $content );
			}
		}
		
		$content = stripslashes ( $content );
		$subject = stripslashes ( $subject );
		
		if (! $from)
			$from = $javconfig ['systems']->get ( 'fromemail' );
		if (! $fromname)
			$fromname = $javconfig ['systems']->get ( 'fromname' );
		$sendmail = $javconfig['systems']->get('enabled');
		$mail = null;
		if ($sendmail == 2) {
			//echo mail
			if(is_array($to)) $to = implode(', ', $to);
			echo JText::_("SENDER") .' '. $fromname . ' (' . $from . ")" . "<br>";
			echo JText::_("SEND_TO") .' '. $nameto . ' (' .$to . ")" . "<br>";
			echo JText::_("SUBJECT") .' '. $subject . "<br />";
			echo JText::_('CONTENT') .' ' . str_replace ( "\n", "<br/>", $content ) . "<br />-----------------------------<br />";			
			return true;
		} elseif ($sendmail == 1) {
			//send email
			$mail = JFactory::getMailer ();
			$mail->setSender ( array ($from, $fromname ) );
			$mail->addRecipient ( $to );
			$mail->setSubject ( $subject );
			$mail->setBody ( str_replace ( "\n", "<br/>", $content ) );
			
			if ($javconfig ['systems']->get ( 'sendmode' ))
				$mail->IsHTML ( true );
			else
				$mail->IsHTML ( false );
			
			if ($javconfig ['systems']->get ( 'ccemail' ) != "")
				$mail->addCc ( explode ( ',', $javconfig ['systems']->get ( 'ccemail' ) ) );
			
			if ($attachment)
				$mail->addAttachment ( $attachment );
				//
			$sent = $mail->Send ();
			if ($mail->ErrorInfo != '')	return false;
			return $sent;
		}
		return false;
	}
	
	/**
	 * This function validate one email address.
	 * $email           Email to validate.
	 * return   1 if this email is valid, 0 otherwise.
	 */
	function validate_email($email) {
		// Create the syntactical validation regular expression
		$regexp = "/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i";
		
		// Presume that the email is invalid
		$valid = 0;
		
		// Validate the syntax
		if (preg_match ( $regexp, $email )) {
			$valid = 1;
		} else {
			$valid = 0;
		}
		
		return $valid;
	
	}
	
	public static function checkPermissionAdmin(){
		global $javconfig;
		
		$user = JFactory::getUser();
		$permissions = isset($javconfig['permissions'])?$javconfig['permissions']:null;
		
		$result	= new JObject;

		$actions = array(
			'core.admin', 'core.manage'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, 'com_users'));
		}
		if($result->get("core.admin") == 1 || $result->get("core.manage") == 1){
			return true;
		}else{
			if ($permissions) {
				$permissions = $permissions->get ( 'permissions' );
				$permissions = explode ( ',', $permissions );
				
				if(is_array($permissions)){
					if(in_array($user->id, $permissions ) && $user->id) return true;
				}
			}						
		}						
		return false;
	}
	
/**
	 * Enter description here...
	 *
	 * @param unknown_type $URL
	 * @param unknown_type $req
	 * @return unknown
	 */
	function curl_getdata($URL, $req) {		
		$proxy = JRequest::getInt('enable_proxy', 0);
		if($proxy){
			$proxy_address 	= JRequest::getVar('proxy_address', '');
			$proxy_port 	= JRequest::getInt('proxy_port', '');
			$proxystr 		= "$proxy_address:$proxy_port";
			$proxy_user 	= JRequest::getInt('proxy_user', '');  
			$proxy_pass 	= JRequest::getInt('proxy_pass', '');
			$proxyUserPass  = "$proxy_user:$proxy_pass";
			$proxyType		= JRequest::getInt('proxy_type', 'CURLPROXY_HTTP');
		}else{
			$db = JFactory::getDBO ();
			$sql = "select * from #__jav_configs where `group`='license'";
			$db->setQuery ( $sql );
			$result = $db->loadObject ();
			$data = isset ( $result->data ) ? $result->data : '';
			$params = class_exists('JRegistry')? new JRegistry($data) : new JParameter($data);

			$proxy 			= $params->get('enable_proxy', 0); 						
			$proxy_address 	= $params->get('proxy_address', ''); 			
			$proxy_port 	= $params->get('proxy_port', ''); 			
			$proxystr 		= "$proxy_address:$proxy_port";
			$proxy_user 	= $params->get('proxy_user', '');			
			$proxy_pass 	= $params->get('proxy_pass', '');			
			$proxyUserPass  = "$proxy_user:$proxy_pass";
			$proxyType		= $params->get('proxy_type', 'CURLPROXY_HTTP'); 						
		}
				
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $ch, CURLOPT_URL, $URL );
		curl_setopt ( $ch, CURLOPT_TIMEOUT, 10 );
		curl_setopt ( $ch, CURLOPT_POST, TRUE );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $req );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		
		if($proxy){				
			curl_setopt($ch, CURLOPT_PROXY, $proxystr);						
			curl_setopt($ch, CURLOPT_PROXYTYPE, $proxyType);
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyUserPass);				 
		}
		
		$result = curl_exec ( $ch );				
		curl_close ( $ch );		
		return $result;	
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $host
	 * @param unknown_type $path
	 * @param unknown_type $req
	 * @return unknown
	 */
	function socket_getdata($host, $path, $req) {
		$proxy = JRequest::getInt('enable_proxy', 0);
		$data = '';
		
		$header = "POST $path HTTP/1.0\r\n";
		$header .= "Host: " . $host . "\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "User-Agent:      Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1) Gecko/20061010 Firefox/2.0\r\n";
		$header .= "Content-Length: " . strlen ( $req ) . "\r\n\r\n";
		$header .= $req;
		$fp = @fsockopen ( $host, 80, $errno, $errstr, 60 );
		if(!$fp) return ;
		@fwrite ( $fp, $header );
		
		$i = 0;
		do {
			$header .= @fread ( $fp, 1 );
		} while ( ! preg_match ( '/\\r\\n\\r\\n$/', $header ) );
		
		while ( ! @feof ( $fp ) ) {
			$data .= @fgets ( $fp, 128 );
		}
		fclose ( $fp );
		
		//print_r($data);die();
		return $data;
	}

	public static function get_Version_Link()
	{
		$link = array();
				
		$link['current_version']['info'] = 'http://pm.joomlart.com/browse/JAVOICE';
		$link['current_version']['upgrade'] = 'http://www.joomlart.com/forums/downloads.php?do=cat&id=139';
		
		return $link;		
	}
	
	function get_license_type(){
		global $javconfig;
		$type = strtolower($javconfig['license']->get('type', ''));
		if ($type=='professional'){
			return 'Professional';
		}
		elseif ($type=='standard'){
			return 'Standard';
		}
		else return 'Trial';
	}	
		
	function populateDB ($sqlfile, $db, &$error) {
		$change_md_sqls = JAVoiceHelpers::splitSql($sqlfile);
		foreach ($change_md_sqls as $query) 
		{
			$query = trim($query);
			if ($query != '') 
			{
				$db->setQuery($query);
				if (!$db->query()) 
				{
					$error[] =" Not run ".$query;
				} 
			}
		}
		return $error;
	}
	
	function splitSql($sqlfile)
	{
		$sql = file_get_contents($sqlfile);
		$sql = trim($sql);
		$sql = preg_replace("/\n\#[^\n]*/", '', "\n".$sql);
		$buffer = array ();
		$ret = array ();
		$in_string = false;
	
		for ($i = 0; $i < strlen($sql) - 1; $i ++) {
			if ($sql[$i] == ";" && !$in_string)
			{
				$ret[] = substr($sql, 0, $i);
				$sql = substr($sql, $i +1);
				$i = 0;
			}
	
			if ($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\")
			{
				$in_string = false;
			}
			elseif (!$in_string && ($sql[$i] == '"' || $sql[$i] == "'") && (!isset ($buffer[0]) || $buffer[0] != "\\"))
			{
				$in_string = $sql[$i];
			}
			if (isset ($buffer[1]))
			{
				$buffer[0] = $buffer[1];
			}
			$buffer[1] = $sql[$i];
		}
	
		if (!empty ($sql))
		{
			$ret[] = $sql;
		}
		return ($ret);
	}
	public static function Install_Db(){
		//Check data exists
		$db = JFactory::getDBO();
		
		if(JAVoiceHelpers::table_exists('#__jav_configs')){
			$path = JPATH_ADMINISTRATOR . '/components/com_javoice/installer/sql/install.javoice.sql';
				
			$error = null;
			if (file_exists($path)) {
				JAVoiceHelpers::populateDB($path, $db, $error);
				if ($error) {
					$error = implode("<br/>", $error);
					return JError::raiseError(1, $error);
				}
			} else {
				JError::raiseWarning(1, JText::_('SQL_FILE_NOT_FOUND_ERROR') . '<br /><br />');
			}
		}
		;
		
		$q = "SELECT data FROM #__jav_configs";
		$db->setQuery($q);
		$data = $db->loadResult();
		
		if (! $data) {		
			//Install sample data
			$path_sample = JPATH_ADMINISTRATOR . '/components/com_javoice/installer/sql/install.configData.sql';
				
			$error = null;
			if (file_exists($path_sample)) {
				JAVoiceHelpers::populateDB($path_sample, $db, $error);
				if ($error) {
					$error = implode("<br/>", $error);
					return JError::raiseError(1, $error);
				}
			} else {
				JError::raiseWarning(1, JText::_('SQL_FILE_NOT_FOUND_ERROR') . '<br /><br />');
			}
		}
		
		/*
		
		$lis_sql_path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_javoice' . DS . 'installer' . DS . 'sql';
		
		$regex = '/upgrade_(.*?)(\.sql)$/i';
		$dk = opendir($lis_sql_path);
		while (false !== ($filename = readdir($dk))) {
			if (preg_match($regex, $filename)) {
				if (JAVoiceHelpers::table_exists('#__jav_email_templates') && $filename == 'upgrade_v1.0.0.sql') {
					JAVoiceHelpers::populateDB(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_javoice' . DS . 'installer' . DS . 'sql' . DS . $filename, $db, $error);
					
				}
			}
		}
		*/
	}
	
	function table_exists($table){
		$db = JFactory::getDBO ();				
		$table = $db->replacePrefix($table);
		$query = "SHOW TABLES LIKE '".$table."'";
		$db->setQuery ( $query );
		return $db->loadResult ();					
	}
	
	function check_value_exists($table, $columnName, $value){
		$db = JFactory::getDBO ();
		
		if(JAVoiceHelpers::table_exists($table))
			return false;
		
		$query = "SELECT count(*) FROM ". $table ." WHERE ".$columnName." ='".$value."'";
		$db->setQuery ( $query );
		return $db->loadResult ();
	}
	
	function check_field_exist($field_name, $table){
		$db = JFactory::getDBO ();
		
		if(!$field_name || !$table) return false;
		$query = "SELECT count(id) FROM #__ja_form_fields WHERE  `published`=1 AND `field_name`='$field_name' and `table_name`='$table'";
		$db->setQuery ( $query );
		return $db->loadResult ();
	}
	
	function checkField_inserted($tableName, $columnName)
	{
		//Global variable, $db for database name, these variable are common in whole application so stored in a global variable, alternatively you can pass this as parameters of function.
		
		$jconfig = new JConfig();
		$databasename = $jconfig->db;
		
		//Getting table fields through mysql built in function, passing db name and table name
		$tableFields = mysql_list_fields($databasename, $jconfig->dbprefix.$tableName);
		
		//loop to traverse tableFields result set
		for($i=0;$i<mysql_num_fields($tableFields);$i++){
			//Using mysql_field_name function to compare with column name passed. If they are same function returns 1
			
			if(mysql_field_name($tableFields, $i)==$columnName)
			return 1;
		} //end of loop
		return 0;
	}
	
    function checkYoutubeLink($url){
        //if (!preg_match('/(\?|&)v=([0-9a-z_-]+)(&|$)/si', $url)) {
//            return false;
//        }
        if(stristr($url, 'youtube.com') === FALSE) {
            return false;    
        }
        return true;        
    }
    
    function repairYoutubeLink($url){
        if(stristr($url, 'watch') === FALSE) { 
            return $url;   
            
        }else{
            $arr = explode("watch?v=", $url);

            if(stristr($url, '&') === FALSE) {
                $code = $arr[1];
            }else{
                $arr2 = explode("&", $arr[1]);
                $code = $arr2[0];
            }
            return 'http://www.youtube.com/v/'.$code;
        }
    }
    
    function showYoutube($str, $showYoutube=true){
        if($this->checkYoutubeLink($str)){
            $pattern = "/\[youtube (.*?) youtube\]/";
            preg_match_all($pattern, $str, $matches);
                
            $arr0 = $matches[0];
            foreach($matches[1] as $v){
            	if($showYoutube)
                	$arr1[] ='<br /><object width="450" height="295"><param name="movie" value="'.$this->repairYoutubeLink($v).'"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="'.$this->repairYoutubeLink($v).'" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="450" height="295"></embed></object><br />';
                else 
                	$arr1[] ='<a target="_blank" href="'.$this->repairYoutubeLink($v).'">'. $this->repairYoutubeLink($v) .'</a>';
            }
            
            $obj = str_replace($arr0, $arr1, $str);
            return $obj;
        }else{
            return $str;   
        }
        
    }
    function showItem($str){  
    	global $javconfig;
    	
    	if(!isset($javconfig['plugin'])){
    		$javconfig['plugin'] = class_exists('JRegistry')? new JRegistry('') : new JParameter('');
    	}
    	
		$is_show_embed_video 	= $javconfig['plugin']->get('is_show_embed_video',1);
		$str = $this->replaceBBCodeToHTML($str, $is_show_embed_video);
		
		
		$enable_smileys			= $javconfig['plugin']->get('enable_smileys',1);
		if($enable_smileys) 	
    		$str = 	$this->showSmiley($str);
    		    				    	
        //$str = $this->showYoutube($str, $is_show_embed_video);
        
                        
        return $str;
    }
    
	function removeEmptyBBCode($comment){
		$tags = array (
						'/\[LARGE\]\s*\[\/LARGE\]/iUs',						
						'/\[MEDIUM\]\s*\[\/MEDIUM\]/iUs',	      
	      			    '/\[B\]\s*\[\/B\]/iUs',
	      			    '/\[I\]\s*\[\/I\]/iUs',
	      			    '/\[U]\s*\[\/U\]/iUs',
	      				'/\[S\]\s*\[\/S\]/iUs',
	      				'/\[\*\]\s*\[\/\*\]/iUs',
	      				'/\[\#\]\s*\[\/\#\]/iUs',
	      				'/\[SUB\]\s*\[\/SUB\]/iUs',
	      				'/\[SUP\]\s*\[\/SUP\]/iUs',
	      				'/\[QUOTE]\s*\[\/QUOTE\]/iUs',
	      				'/\[LINK\]\s*\[\/LINK\]/iUs',
	     				'/\[IMG\]\s*\[\/IMG\]/iUs',
	     				'/\[YOUTUBE\]\s*\[\/YOUTUBE\]/iUs'
	    			  );
	    while(1){			  	    			  	    
	    	$comment = preg_replace($tags, '', $comment);	    	
	    		    	
	    	for($i =0; $i < count($tags); $i++){
	    		preg_match($tags[$i], $comment, $matched);	    		
	    		if($matched){	    			
	    			break;	
	    		}
	    	}
	    	
	    	if($i == count($tags)){
	    		break;
	    	}
	    }	    
	    return $comment;			  		    
	}
    
    
    
	function replaceBBCodeToHTML($text, $is_show_embed_video){				
		if (class_exists ( 'DCODE' )) {
			$myDcode = new DCODE ();		
			//  (this is the full set)
			$myDcode->setTags ("LARGE", "MEDIUM", "HR", "B", "I", "U", "S", "UL", "OL", "SUB", "SUP", "QUOTE", "LINK", "IMG", "YOUTUBE");
			$text = $myDcode->parse ($text);
		}
		return $text;
	}
    
    function showSmiley($str){
        $array = array(
                    ':)'        =>      '0px 0px',
                    ':D'        =>      '-12px 0px',
                    'xD'        =>      '-24px 0px',
                    ';)'        =>      '-36px 0px',
                    ':p'        =>      '-48px 0px',
                    '^_^'       =>      '0px -12px',
                    ':$'        =>      '-12px -12px',
                    'B)'        =>      '-24px -12px',
                    ':*'        =>      '-36px -12px',
                    '(3'        =>      '-48px -12px',
                    ':S'        =>      '0px -24px',
                    ':|'        =>      '-12px -24px',
                    '=/'        =>      '-24px -24px',
                    ':x'        =>      '-36px -24px',
                    'o.0'       =>      '-48px -24px',
                    ':o'        =>      '0px -36px',
                    ':('        =>      '-12px -36px',
                    ':@'        =>      '-24px -36px',
                    ":'("       =>      '-36px -36px'
                );
        
        $key = array_keys($array);
      
        foreach($array as $k => $v){    
            $span[] = '<span class="jav-smiley"><span style="background-position: '.$v.';"><span>'.$k.'</span></span></span>';                                    
        }
		                
        $str = str_replace($key, $span, $str);                        
        return $str;           
    }
	function replaceURLWithHTMLLinks($text) {
		global $javconfig;
    	$text = " ".$text;
    	if(1==0){
	    	$text = preg_replace('/(?<!S)((http(s?):\/\/)|(www.))+([a-zA-Z0-9\/*+-_?&;:%=.,#]+)/', '<a href="http$3://$4$5" target="_blank" rel="nofollow">$4$5</a>', $text);	    
	    	$text = preg_replace('/(?<!S)([a-zA-Z0-9_.\-]+\@[a-zA-Z][a-zA-Z0-9_.\-]+[a-zA-Z]{2,6})/', '<a href="mailto://$1" rel="nofollow">$1</a>', $text);
    	}else{
    		$text = preg_replace('/(?<!S)((http(s?):\/\/)|(www.))+([a-zA-Z0-9\/*+-_?&;:%=.,#]+)/', '<a href="http$3://$4$5" target="_blank">$4$5</a>', $text);	    
	    	$text = preg_replace('/(?<!S)([a-zA-Z0-9_.\-]+\@[a-zA-Z][a-zA-Z0-9_.\-]+[a-zA-Z]{2,6})/', '<a href="mailto://$1">$1</a>', $text);
    	}    	
    	return $text;
	}
  	
	function preloadfile($cid, $action="") {		
		jimport ( 'joomla.filesystem.folder' );
		
		$img = "";
		if($action == "response" || $action == "adminresponse"){			
			$destination_path = JPATH_ROOT.DS."images".DS."stories".DS."ja_voice".DS."admin_response".DS.$cid;			
		}else{
			$destination_path = JPATH_ROOT.DS."images".DS."stories".DS."ja_voice".DS.$cid;
		}	
		if (is_dir ( $destination_path )) {			
			$files = JFolder::files ( $destination_path );			
			
			foreach ( $files as $file ) {
				$type = substr ( strtolower ( $file ), - 3, 3 );
				$temp = explode ( ".", $file );
				$type = strtolower ( $temp [1] );
				if($action == "admin" || $action == "adminresponse"){
					$imgLink = "../components/com_javoice/asset/images/icons/". $type .".gif";
				}else{
					$imgLink = "components/com_javoice/asset/images/icons/". $type .".gif";
				}	
				if($action == "response" || $action == "adminresponse")					
					$img .= "<input type='checkbox' name='listfile[]' onclick='javCheckTotalFileReply()' value='$file' checked>&nbsp;&nbsp;<img src='".$imgLink."'/> " .$file . "<br />";
				else 
					$img .= "<input type='checkbox' name='listfile[]' onclick='javCheckTotalFile()' value='$file' checked>&nbsp;&nbsp;<img src='".$imgLink."'/> " .$file . "<br />";															
			}
		}
		return $img;
	}
	
    function getAvatar($userID=0){
    	global $javconfig;        		    			
		$mainframe = JFactory::getApplication();
    	if(isset($javconfig['plugin']) && !$javconfig['plugin']->get('enable_avatar')){
			return $avatar;
		}
			
    	$avatar = '';					
        $avatarSize = $javconfig['plugin']->get('avatar_size',1);
        $src = JURI::root() . 'components/com_javoice/asset/images/avatar-large.png';
         
        if($avatarSize == 1){
            $size = "height:18px; width:18px;";
        }else if($avatarSize == 2){
            $size = "height:24px; width:24px;";
        }else if($avatarSize == 3){
            $size = "height:40px; width:40px;";
        }    
         	
    	if(!$userID)   return array($src, $size); 		    		
    		    	
    	$user = JFactory::getUser($userID);
    	$params = class_exists('JRegistry')? new JRegistry($user->params) : new JParameter($user->params);
    	
        if($params->get('providerName','')=='Twitter' ||$params->get('providerName','')=='Facebook'){
            if($params->get('photo')){
            	$avatar = $params->get('photo',''); 
            }     	
        }
        
        if(!$avatar){					
	   		switch($javconfig['plugin']->get('type_avatar')){
			    case 1:
			    	if($this->checkComponent('com_comprofiler'))
			        	$avatar = $this->getAvatarCB($userID);
			        break;								
				case 2:
					if ($this->checkComponent ( 'com_kunena' ))
						$avatar = $this->getAvatarKunena ( $userID );
					else if($this->checkComponent ( 'com_fireboard' ))	
						$avatar = $this->getAvatarFireboard ( $userID );												
					break;				    
				case 4:
					if($this->checkComponent('com_community'))
						$avatar = $this->getAvatarJomSocial($userID);			    			   
				    break;			    
				case 3:					
				    $avatar= $this->getAvatarGravatar($user->email, $javconfig['plugin']->get('avatar_size', 1), $defaultAvatar);
				    break;			
			}								
        }
        if(!$avatar) $avatar = $src;

        return $avatar = array($avatar, $size) ;        				
    }	    
    
	function getAvatarKunena($userID){
		if (file_exists ( JPATH_SITE.DS."components".DS."com_kunena".DS."lib".DS."kunena.user.class.php" )) {
			require_once (JPATH_SITE.DS."components".DS."com_kunena".DS."lib".DS."kunena.user.class.php");
			$app = JFactory::getApplication();
			$document = JFactory::getDocument();
			$fbConfig = CKunenaConfig::getInstance();
			//print_r($fbConfig);die();			
		
		
			if ($fbConfig->avatar_src == 'fb') {
				//get avatar image from database			
				$db = JFactory::getDBO ();
				
				$sql = "SELECT `avatar` FROM #__fb_users WHERE `userid`='{$userID}'";
				
				$db->setQuery ( $sql );
				//die($db->getQuery ());
				$imgPath = $db->loadResult ();
				
				if ($imgPath) {
					$fireboardAvatar = '';
					if (@! is_null ( $fbConfig->version ) && @isset ( $fbConfig->version ) && @$fbConfig->version == '1.0.1') {
						$fireboardAvatar = 'components/com_kunena/' . $imgPath;
					} else {
						$fireboardAvatar = 'images/fbfiles/avatars/' . $imgPath;
					}
					
					//check exist image of user				
					if (file_exists ( JPATH_SITE .DS . $fireboardAvatar )) {
						return JURI::root () . $fireboardAvatar;
					} else {
						// Return false if Image file doesn't exist.
						return false;
					}
				} else {
					// user don't use avatar.
					return false;
				}
			}
		}
		return false;
	}
    
    function getAvatarFireboard($userID){    	    	
    	$fireConfig = JPATH_SITE . '/administrator/components/com_fireboard/fireboard_config.php';
    	
    	//Version is 1.0.5
		if(!file_exists( $fireConfig) ){						
			$fireConfig	= JPATH_SITE . '/components/com_fireboard/sources/fb_config.class.php';				
			if( file_exists($fireConfig) )
			{				
				require_once( $fireConfig );
				global $fbConfig;
				
				$fireConfig	= new fb_config();
				$fireConfig->load();
			}			
		}	
				
		//check 
     	if( !is_object($fireConfig) && !file_exists($fireConfig) ){
	        return false;
		}
		
		//Version < 1.0.5
    	if( !is_object( $fireConfig ) ){
			require( $fireConfig );						
			$fireArray	= new stdclass();	
			global $fbConfig;			
			$fireArray->avatar_src	= $fbConfig['avatar_src'];
			$fireArray->version		= $fbConfig['version'];						
			$fireConfig	= $fireArray;
		}
		
    	if($fireConfig->avatar_src == 'fb'){
			//get avatar image from database			
			$db = JFactory::getDBO();
        
        	$sql = "SELECT `avatar` FROM #__fb_users WHERE `userid`='{$userID}'";
        
        	$db->setQuery( $sql );                	   		    
			
		    $imgPath   = $db->loadResult();
		    
		    if($imgPath){
		        $fireboardAvatar    = '';		        
				if(@!is_null($fireConfig->version) && @isset($fireConfig->version) && @$fireConfig->version == '1.0.1')
				{
				    $fireboardAvatar    = '/components/com_fireboard/avatars/' . $imgPath;
				} else {				   
				    $fireboardAvatar    = '/images/fbfiles/avatars/' . $imgPath;
				}
				
				//check exist image of user
				if(file_exists(JPATH_SITE . $fireboardAvatar)){
					return  JURI::root() . $fireboardAvatar;
				} else {
				    // Return false if Image file doesn't exist.
				    return false;
				}
			}else {				
			    // user don't use avatar.
			    return false;
			}
		} 
			return false;        						    	
    }
    
    function getAvatarGravatar($email, $avatarSize, $defaultAvatar){
    	$imgSource = false;
    	switch ($avatarSize){
    			case 1:
    				$imgSource   = 'http://www.gravatar.com/avatar.php?gravatar_id=' . md5($email)
		                    . '&amp;default=' . urlencode($defaultAvatar)
							. '&amp;size=18';    				    				
    				break;
    			case 2:    				
    				$imgSource   = 'http://www.gravatar.com/avatar.php?gravatar_id=' . md5($email)
		                    . '&amp;default=' . urlencode($defaultAvatar)
							. '&amp;size=26';    			     									    				
    				break;
    			default:
    				$imgSource   = 'http://www.gravatar.com/avatar.php?gravatar_id=' . md5($email)
		                    . '&amp;default=' . urlencode($defaultAvatar)
							. '&amp;size=42';    				    		     				    				
    	}	
    	return $imgSource;
    }
    
    function getAvatarCB($userID){    		   
	    // Load the template name from the database
        $db = JFactory::getDBO();
        
        $sql = "SELECT `avatar` FROM #__comprofiler WHERE `user_id`='{$userID}' AND `avatarapproved`='1'";
        
        $db->setQuery( $sql );        
        $imgName  = $db->loadResult();                
		if($imgName){			
		    if(file_exists(JPATH_SITE . '/components/com_comprofiler/images/' . $imgName)){
		    	$imgPath   = JURI::root() . '/components/com_comprofiler/images/' . $imgName;
		        return $imgPath; 
		    }else if (file_exists(JPATH_SITE . '/images/comprofiler/' . $imgName)){
			    $imgPath   = JURI::root() . '/images/comprofiler/' . $imgName;
			    return $imgPath;
		    }else
			    return false;
		}else			
		    return false;				
    }  

    function getAvatarJomSocial($userID){
    	$jspath = JPATH_ROOT.DS.'components'.DS.'com_community';
		include_once($jspath.DS.'libraries'.DS.'core.php');
		 
		// Get CUser object
		$user = CFactory::getUser($userID);
		$avatarUrl = $user->getThumbAvatar();
		
		return $avatarUrl;
    }
	
    public static function checkComponent($component){
    	$db = JFactory::getDBO();
		$query =" SELECT Count(*) FROM #__extensions as c WHERE c.element ='$component'"	;		
		$db->setQuery($query);		
		return $db->loadResult();
	}	

	public static function JomSocial_addActivityStream($actor, $title, $cid, $action='add'){
		global $javconfig;
		
		if (JAVoiceHelpers::checkComponent ( 'com_community' ) && (! isset ( $javconfig ['plugin'] ) || $javconfig ['plugin']->get ( 'enable_activity_stream', 1 ))) {
			require_once (JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php');
			require_once (JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'activities.php');
			
			include_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'userpoints.php');
			
			$act = new stdClass ( );
			$act->cmd = 'com_javoice.voice.'.$action;			
						
			$userPointModel	= CFactory::getModel( 'Userpoints' );	
			// Test command, with userpoint command. If is unpublished do not proceed into adding to activity stream.
			$point			= $userPointModel->getPointData( $act->cmd );
			$points = 0;
			if( $point && !$point->published )
			{
				$points = 1;
			}
			elseif($point) $points = $point->points;
						
			$act->actor = $actor;
			$act->target = $actor; // no target
			$act->title = JText::_ ( $title );
			$act->content = JText::_('THIS_IS_THE_BODY' );
			$act->app = 'com_javoice.voice';
			$act->cid = $cid;
			$act->points = $points;
			
			CFactory::load ( 'libraries', 'activities' );
			CActivityStream::add ( $act );
			
			
			/* Add points for user */
			CuserPoints::assignPoint($act->cmd, $actor);
		}
	}		

	public static function get_Itemid($find = array()) {
		$app = JFactory::getApplication();
		$menu		= $app->getMenu();
		$active		= $menu->getMenu();						
		$Itemid = 9999;
		//$a = JMenuSite::load();		
		foreach ( $active as $row ) {			
			if (@$row->query ['option'] == $find ['option'] && @$row->query ['view'] == $find ['view'] && (! isset ( $row->query ['layout'] ) || ! isset ( $find ['layout'] ) || (isset ( $row->query ['layout'] ) && isset ( $find ['layout'] ) && $row->query ['layout'] == $find ['layout']))) {
				$Itemid = $row->id;
				break;
			}
		}
		return $Itemid;
	}
	
	/**
	 * Send mail when there is new voice posted
	 * 
	 * @param integer $voiceItem Item object
	 * @param integer $Itemid 	 Item id menu
	 * 
	 * @return void
	 */
	function sendMailWhenNewVoice($voiceItem, $Itemid = 0)
	{
		global $javconfig;
		
		$app = JFactory::getApplication();
		//$app->isAdmin();
		//$url = $app->isAdmin() ? JURI::root() : JURI::base();
		$url = $_SERVER['HTTP_HOST'] . str_replace($_SERVER['HTTP_HOST'], '', JRoute::_ ('index.php?option=com_javoice&view=items&layout=item&cid=' . $voiceItem->id . '&type=' . $voiceItem->voice_types_id . '&forums=' . $voiceItem->forums_id . '&Itemid=' . $Itemid . '&' . md5('save_successfull')));
		
		$user = JFactory::getUser($voiceItem->user_id);
		
		$mail = $this->getEmailTemplate("Javnotify_to_admin_new_item");
		
		$adminEmail = $javconfig['systems']->get('fromemail');
		$adminName = JText::_("ADMINISTRATOR");
		
		$filters = array();
		$filters['{ITEM_TITLE}'] = $voiceItem->title;
		$filters['{CONFIG_SITE_TITLE}'] = $app->getCfg('sitename');
		$filters['{USERS_USERNAME}'] = $user->username;
		$filters['{ITEM_TITLE_WITH_LINK}'] = '<a href="' . $url . '" target="_blank">' . $voiceItem->title . '</a>';
		$filters['{ITEM_DESCRIPTON}'] = $voiceItem->content;
		
		$link = $this->getLink(JRoute::_("index.php?option=com_javoice&view=users&uid=$user->id&tab=2",true,0));
		$filters['{EMAIL_PREFERENCE_LINK}'] = "<a href=\"$link\">" . JText::_("TURN_OFF_OR_EDIT_YOUR_EMAIL_NOTIFICATIONS") . "<a>";
		
		$this->sendmail($adminEmail, $adminName, $mail->subject, $mail->emailcontent, $filters);
		
		return;
	}
}

class JAPermissions{
	var $host = 'www.joomlart.com';
	//var $host = 'www2.dev.joomlart.com';
	var $path = "/member/jaeclicense.php";
	/**
	 * Enter description here...
	 *
	 */
	function display_form_activate_step1() {
		?>
		<script type="text/javascript">
			function validate(){
				if(jQuery('#key').val().trim()==''){
					alert('<?php echo JText::_("PLEASE_ENTER_YOUR_LICENSE_KEY" )?>');
					jQuery('#key').focus();
					return false;
				}
				document.adminForm.disabled = true;
				return true;
			}		
		</script>
		<div style="width: 100%; text-align: center">
			<center>
			<form action="index.php" name="adminForm" method="post">
			<h3><?php echo JText::_('WELCOME_TO_THE_JA_VOICE')?></h3>
			<fieldset style="width: 50%; text-align: center">	
				<legend><?php echo JText::_("ACTIVATE_CODE" )?></legend>
			    <table align="center" id="radio-box" width="100%">
					
					<tr id="license_key_box">
						<td align="left" style="margin-left: 20px">
							<label for="email"><?php echo JText::_("ENTER_YOUR_LICENSE_KEY" )?></label>
							<span class="buynow">[<a href="http://www.joomlart.com/member/signup.php?group=javoice"><?php echo JText::_("BUY_NOW" )?></a></span> &amp; 
							<span class="moreinfo"><a href="http://javoice.joomlart.com"><?php  echo JText::_("MORE_INFO" )?></a>]</span>
							<br />
							<textarea rows="6" cols="80" name="key" id="key"></textarea>
			
						</td>
					</tr>
					<tr>
						<td align="left">
							<input type="hidden" name="option" value="com_javoice" /> 
							<input type="hidden" name="action" value="verify_license_key" /> 
							<input type="submit" name="submit"	onclick="return validate()"	value="<?php echo JText::_("NEXT" )?>" />
						</td>
					</tr>
				</table>
			</fieldset>
			</form>
			</center>
		</div>
	<?php
	}
		
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $key
	 * @param unknown_type $action
	 * @return unknown
	 */
	function verify_new_license_key($email = '',$payment_id= '', $action = true) {		
		global $javconfig;		
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO ();
		if (isset ( $_SESSION [md5 ( $_SERVER ['HTTP_HOST'] . 'javcheking' )] ))
			unset ( $_SESSION [md5 ( $_SERVER ['HTTP_HOST'] . 'javcheking' )] );
		
		$domain = $_SERVER ['HTTP_HOST'];
		$base = $mainframe->getSiteUrl ();
		
		if (! $email) {
			$email 		= $javconfig["license"]->get("email");
			$payment_id = $javconfig["license"]->get("payment_id");
		}
		
		if (! $email || ! $domain) {
			return FALSE;
		}
		
		if (strtolower ( substr ( $domain, 0, 3 ) ) == 'www') {
			$domain = substr ( $domain, strpos ( $domain, '.' ) + 1 );
		}
		
		$req = 'domain=' . $domain;
		//$req .= '&key=' . rawurlencode ( $key );
		$req .= '&email=' . rawurlencode ( $email );
		$req .= '&payment_id=' . rawurlencode ( $payment_id );
		//$req .= '&base=' . rawurlencode ( $base );
		$req .= '&action=verify_license_javoice';	
		$URL = "http://{$this->host}{$this->path}";
		
		if (! function_exists ( 'curl_version' )) {
			if (! ini_get ( 'allow_url_fopen' )) {
				JError::raiseWarning ( 1, JText::_('YOUR_LICENSE_KEY_COULD_NOT_BE_VERIFIED_PLEASE_CHECK_YOUR_INTERNET_CONNECTION_AND_TRY_AGAIN_KINDLY_TRY_ANOTHER_METHOD_TO_CONTINUE_____JTEXT___OR_CONTACT_JOOMLART_FOR_FURTHER_ASSISTANCE'  ) );
				return;
			} else {				
				$result = JAVoiceHelpers::socket_getdata ( $this->host, $this->path, $req );
			}
		} else {					
			$result = JAVoiceHelpers::curl_getdata ( $URL, $req );			
		}		
		if (! $result) { //Not connected to server
			if ($action) {
				JError::raiseWarning ( 1, JText::_('UNABLE_TO_CONNECT_TO_THE_SERVER_BY_JOOMLART_PLEASE_CHECK_YOUR_INTERNET_CONNECTION' ) );
				return;
			} else {
				if (isset ( $_SESSION [md5 ( $_SERVER ['HTTP_HOST'] . 'JAVOICE_UNVALID_LICENSE' )] ))
					unset ( $_SESSION [md5 ( $_SERVER ['HTTP_HOST'] . 'JAVOICE_UNVALID_LICENSE' )] );
				if (isset ( $_SESSION [md5 ( $_SERVER ['HTTP_HOST'] . 'javcheking' )] )) {
					unset ( $_SESSION [md5 ( $_SERVER ['HTTP_HOST'] . 'javcheking' )] );
				}
				return true;
			}
		
		
		} else {
			$result 	= json_decode($result, true);			
			$statusMes 	= $result["status"];			
			switch ($statusMes) {
				case 'invalid_domain':
					{
						$this->updateFail ();
						JError::raiseWarning ( 1, JText::_('YOUR_DOMAIN_IS_NOT_ACCEPTED_THEREFORE_THE_SYSTEM_WILL_BE_DISABLED_PLEASE_CONTACT' ) . ' <a href="http://joomlart.com">JoomlArt</a> ' . JText::_('FOR_FURTHER_ASSISTANCE' ) );
						return;
					}
					break;
				
				case 'expired' :
					{
						$this->updateFail ();
						JError::raiseWarning ( 1, JText::_('YOUR_LICENSE_HAS_EXPIRED_THEREFORE_THE_SYSTEM_WILL_BE_DISABLED_PLEASE_CONTACT' ) . ' <a href="http://joomlart.com">JoomlArt</a> ' . JText::_('FOR_FURTHER_ASSISTANCE' ) );
						return;
					}
					break;
				
				case 'invalid_payment_id':
					{
						$this->updateFail ();
						JError::raiseWarning ( 1, JText::_('YOUR_PAYMENT_IS_NOT_CORRECTED_FOR_THIS_PRODUCT_PLEASE_CONTACT' ) . ' <a href="http://joomlart.com">JoomlArt</a> ' . JText::_('FOR_FURTHER_ASSISTANCE' ) );
						return;
					}
					break;
				
				case 'payment_not_completed':
					{
						$this->updateFail ();
						JError::raiseWarning ( 1, JText::_('YOUR_PAYMENT_IS_NOT_COMPLETED_PLEASE_CONTACT' ) . ' <a href="http://joomlart.com">JoomlArt</a> ' . JText::_('FOR_FURTHER_ASSISTANCE' ) );
						return;
					}
					break;
				
				case 'disabled_domain':
					{
						$this->updateFail ();
						JError::raiseWarning ( 1, JText::_('YOUR_DOMAIN_IS_DISABLED' ) );
						return;
					}
					break;
				
				case 'limited_domain':
					{
						$this->updateFail ();
						JError::raiseWarning ( 1, JText::_('LIMITED_DOMAIN' ) );
						return;
					}
					break;
				
				case 'invalid_member':
					{
						$this->updateFail ();
						JError::raiseWarning ( 1, JText::_('YOUR_PAYMENT_IS_NOT_CORRECTED_FOR_THIS_MEMBER_PLEASE_CONTACT' ) . ' <a href="http://joomlart.com">JoomlArt</a> ' . JText::_('FOR_FURTHER_ASSISTANCE' ) );
						return;
					}
					break;
				
				case 'successful':
					{
						$this->updateSuccess ( $payment_id, $email, $result["product_type"]);
						$mainframe->redirect ( 'index.php?option=com_javoice&view=voice&layout=supportandlicense' );
					}
					break;
				case 'error':
				default :
					{
						JError::raiseWarning ( 1, JText::_('HAVE_AN_ERROR_WHEN_PROCESSING_PLEASE_TRY_AGAIN' ) );
						return;
					}
					break;
			}			
		}			
		
		return;
	}
	
	function updateFail() {
		$db = JFactory::getDBO ();
		$query = "SELECT data FROM #__jav_configs WHERE `group`='license'";
		$db->setQuery ( $query );
		$data = $db->loadResult ();
		if (! $data) {
			$query = "INSERT INTO  #__jav_configs (`group`, data) VALUES ('license', 'verify_is_passed=0')";
		} else {
			$data = explode ( "\n", $data );
			$str = "";
			foreach ( $data as $item ) {
				if (strpos ( $item, "verify_is_passed" !== false )) {
					$item = "verify_is_passed=0";
				}
				$str = $item . "\n";
			}
			$db = JFactory::getDBO ();
			$query = "UPDATE  #__jav_configs SET data = '" . $str . "' WHERE  group = 'license'";
			$db->setQuery ( $query );
			$db->query ();
		}
		$_SESSION ['JAVOICE_VERIFY_PASSED'] = 0;
	}
	
	function updateSuccess($payment_id, $email, $product_type='') {
		$db  = JFactory::getDBO ();
		$str = "";
		$create_date = date ( 'Y-m-d H:i:s' );
		$last_verify = date ( 'Y-m-d H:i:s' );
		
		$query = "SELECT data FROM #__jav_configs WHERE `group`='license'";
		$db->setQuery ( $query );
		$data = $db->loadObjectList ();
		
		$proxy = JRequest::getInt('enable_proxy', 0);
		$str .= "enable_proxy=".$proxy;				
		if($proxy){
			$str .= "\nproxy_address=" . JRequest::getVar('proxy_address', '')
				    . "proxy_port=" . JRequest::getVar('proxy_port', '')
				    . "proxy_user=" . JRequest::getVar('proxy_user', '')
				    . "proxy_pass=" . JRequest::getVar('proxy_pass', '')
				    . "proxy_type=" . JRequest::getVar('proxy_type', 'CURLPROXY_HTTP');														
		}
		
		$str .=   "\npayment_id=" . $payment_id 
				. "\nemail=" . $email 
				. "\ncreate_date=" . $create_date 
				. "\nlast_verify=" . $last_verify 
				. "\nverify_is_passed=1";
		if($product_type){
			$str .=   "\ntype=" . $product_type;	
		}		
						
		if (! $data) {			
			$query = "INSERT INTO  #__jav_configs (`group`, data) VALUES ('license', '" . $str . "')";
			
			$db->setQuery ( $query );			
			$db->query ();
		} else {						
			$query = "UPDATE  #__jav_configs SET data = '" . $str . "' WHERE `group` = 'license'";
			$db->setQuery ( $query );
			$db->query ();
		}
		
		$_SESSION ['JATOOLBAR_VERIFY_PASSED'] = 1;
	}
			
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $tab
	 * @param unknown_type $key
	 */
	function update_key_config($tab, $key) {
		$db = JFactory::getDBO ();
		$sql = "select * from #__jav_configs where `group`='$tab'";
		$db->setQuery ( $sql );
		$result = $db->loadObject ();
		
		if ($result) {
			$sql = "Update #__jav_configs SET data='$key' where `group`='$tab'";
			$db->setQuery ( $sql );
			$db->query () or die ( $db->ErrorMsg () );
		} else {
			$sql = "Insert into #__jav_configs (data, `group`) Values('$key', '$tab')";
			$db->setQuery ( $sql );
			$db->query () or die ( $db->ErrorMsg () );
		}
	}
	
	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function show_license_detail( $show_full=true ) {
		$db = JFactory::getDBO ();
		$sql = "select * from #__jav_configs where `group`='license'";
		$db->setQuery ( $sql );
		$old_license = $db->loadObject ();
		if (! $old_license || $old_license->data == '') {
			JError::raiseWarning ( 1, JText::_('PLEASE_CHOOSE_METHOD' ) );
			return $this->display_form_activate_step1 ();
		}
		$params = class_exists('JRegistry')? new JRegistry($old_license->data) : new JParameter($old_license->data);
	
		?>
		<!--  -->
		<?php	echo JText::_('SUCCESSFUL_VERIFICATION' );?>
		
	
		<br />
		<a href="index.php?option=com_javoice"><?php
			echo JText::_('START_CHECKING_OUT_JA_VOICE_NOW' )?></a>
		<br />
		<br />
	
		<?php
			echo JText::_('LICENSE_INFORMATION' ) . ': <br/>'?>
			<?php
			echo JText::_('_LICENSE_FOR_DOMAIN' )?>: <?php
			echo $params->get ( 'domains', '' )?><br />
		<?php
			echo JText::_('_DURATION' )?>: <?php
			echo JText::_('LIFETIME' )?>
		<br />
			[
		<a href="http://joomlart.com/forums/forumdisplay.php?f=162"><?php
			echo JText::_('SUPPORT_FORUM' )?></a>
		] - [
		<a href="http://wiki.joomlart.com"><?php
			echo JText::_('USER_GUIDES' )?></a>
		]
		<?php
	}
	
	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function show_trial_license_detail( $registered=false, $create_date, $domains, $max_domains=2, $show_full=true ) {
		$db = JFactory::getDBO ();
		$sql = "select * from #__jav_configs where `group`='license'";
		$db->setQuery ( $sql );
		$old_license = $db->loadObject ();
		if (! $old_license || $old_license->data == '') {
			JError::raiseWarning ( 1, JText::_('PLEASE_ACTIVE_CODE' ) );
			return $this->display_form_activate_step1 ();
		}

		$params = class_exists('JRegistry')? new JRegistry($old_license->data) : new JParameter($old_license->data);
		$left_days = floor ( ($params->get ( 'expired_date', '' ) - time ()) / (60 * 60 * 24) ) + 1;
		$this_domain = $params->get ( 'domains', '' );
		
		if ($left_days<0) {
			$msg = sprintf('<b>Your testing license has expired.</b><br/>You have already requested  testing license of JA Voic Compoenent on %s.<br/><a href="http://www.joomlart.com/member/signup.php?group=javoice">Buy a new license</a> for this domain.<br/>Need help? <a href="http://support.joomlart.com">Submit support ticket</a> or send an email to <a href="mailto:javoice@joomlart.com">javoice@joomlart.com</a>', date ( "Y-m-d", $params->get ( 'create_date' ) ));
			JError::raiseWarning ( 1, $msg );
		} elseif ($registered) {		
			$msg = sprintf('You have already submitted a testing license of JA Voice Component for this domain on %s. Your license is now updated to the system.<br/><br/>If you need to extend your testing period for specific reason, please submit a <a href="http://support.joomlart.com">support ticket</a> or send an email to <a href="mailto:javoice@joomlart.com">javoice@joomlart.com</a>', date ( "Y-m-d", $params->get ( 'create_date' ) ));
			JError::raiseNotice ( 1, $msg );
		
		} else {
			echo sprintf('The domain <b>%s</b> is not matching with the domain <b>%s</b> under your license details.<br/>For testing / development purpose, you can have <b>%s days</b> to test on this domain.<br/>On <b>%s</b>, the license on this domain will be expired and you must move this site to the official domain. Each license will be granted up to <b>%s</b> testing domains.', $this_domain, $domains, $left_days, date('Y-m-d', $params->get ( 'expired_date', '' )), $max_domains) . '<br /><br />';
		}
		if(!$show_full) return ;
		?>


		<a href="index.php?option=com_javoice"><?php
			echo JText::_('START_CHECKING_OUT_JA_VOICE_NOW' )?></a>
		<br />
				
		<?php
			echo JText::_('LICENSE_INFORMATION' ) . ': <br/>'?>
			<?php
			echo JText::_('_LICENSE_FOR_DOMAIN' )?>: <?php
			echo $params->get ( 'domains', '' )?><br />
		<?php
			echo JText::_('_VALID_UNTIL' )?>: [<?php
			echo date ( "Y-m-d", $params->get ( 'expired_date' ) )?>] (<?php
			echo $left_days?> <?php
			echo JText::_('DAYS_LEFT' )?>)
		<br />
		[
		<a href="http://joomlart.com/forums/forumdisplay.php?f=162"><?php
			echo JText::_('SUPPORT_FORUM' )?></a>
		] - [
		<a href="http://wiki.joomlart.com"><?php
			echo JText::_('USER_GUIDES' )?></a>
		]
			<?php		
	}
	
	/**
	 * Enter description here...
	 *
	 */
	public static function show_powered_by(){
		global $javconfig;
		$html = '';
		if(!$javconfig['systems']->get('is_turn_off_copyright', 0) && (!isset($javconfig['license']) || $javconfig['license']->get('type')!='Professional')){
			if(JRequest::getCmd("view") != "feeds"){
				$html .= 'Powered by <a href="http://javoice.joomlart.com">JA Voice</a>.';
			}
		}		
		return $html;
	}
	
	function get_powered_by(){
		return JAPermissions::show_powered_by();
	}
	
	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function getRealIpAddr() {
		if (! empty ( $_SERVER ['HTTP_CLIENT_IP'] )) //check ip from share internet
	{
			$ip = $_SERVER ['HTTP_CLIENT_IP'];
		} elseif (! empty ( $_SERVER ['HTTP_X_FORWARDED_FOR'] )) //to check ip is pass from proxy
	{
			$ip = $_SERVER ['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER ['REMOTE_ADDR'];
		}
		return $ip;
	}
	
	/**
	 * Enter description here...
	 *
	 */
	function redirect_to_license_manager() {//echo 'redirect_to_license_manager';exit;
		$mainframe = JFactory::getApplication();
		if (isset ( $_SESSION [md5 ( $_SERVER ['HTTP_HOST'] . 'javchecking' )] ))
			unset ( $_SESSION [md5 ( $_SERVER ['HTTP_HOST'] . 'javchecking' )] );
		$_SESSION [md5 ( $_SERVER ['HTTP_HOST'] . 'JAVOICE_CHECKED' )] = 1;
		$_SESSION [md5 ( $_SERVER ['HTTP_HOST'] . 'JAVOICE_UNVALID_LICENSE' )] = 1;
		//$mainframe->redirect('index.php?option=com_javoice&view=JAVOICE&layout=license');	
	}
	
	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function check_license_time() {
		$mainframe = JFactory::getApplication();
		$plan_check = 3*60*60*24*1000;
		
		
		$db = JFactory::getDBO ();
		$sql = "select * from #__jav_configs where `group`='license'";
		$db->setQuery ( $sql );
		$old_licenses = $db->loadObject ();
		if ($old_licenses){
			$params = class_exists('JRegistry')? new JRegistry($old_licenses->data) : new JParameter($old_licenses->data);
		}
		else $params = class_exists('JRegistry')? new JRegistry('') : new JParameter('');
		
		$expired_date =  0;
		$lasted_check =  0;
		
		if (! $old_licenses || trim ( $old_licenses->data ) == '') {
			$query = "select * from #__users where usertype='Super Administrator' LIMIT 1";
			$db->setQuery ( $query );
			$user = $db->loadObject ();
			$params_user = class_exists('JRegistry')? new JRegistry($user->params) : new JParameter($user->params);
			if ($params_user->get ( '88093ac8bb60bc7ef2aa311aa31c2a91' )) {
				$this->redirect_to_license_manager ();
				JError::raiseWarning ( 1, JText::_('YOUR_LICENSE_KEY_IS_NOT_ACCEPTED_PLEASE_CONTACT' ) . ' <a href="http://joomlart.com">JoomlArt</a>' . JText::_('FOR_FURTHER_ASSISTANCE' ) );
				return;
			}
		} else {
			$expired_date =  ( int )trim ( $params->get ( 'expired_date' ) );
			$ips = trim ( $params->get ( 'ips' ) );
			$domains =  trim ( $params->get ( 'domains' ) );
			$lasted_check =  ( int )trim ( $params->get ( 'lasted_check' ) );
			$code = trim($params->get ( 'code' ));

			$rebuilt_code = md5 ( $domains . $ips . $expired_date . $lasted_check . trim ( $params->get ( 'email' ) ) . trim ( $params->get ( md5 ( 'paused' ) ) ) . 'JA_LICENSE_VOICE' );
			if ($rebuilt_code != $code) {
				$this->redirect_to_license_manager ();
				JError::raiseWarning ( 1, JText::_('YOUR_LICENSE_KEY_IS_NOT_ACCEPTED_PLEASE_CONTACT' ) . ' <a href="http://joomlart.com">JoomlArt</a>' . JText::_('FOR_FURTHER_ASSISTANCE' ) );
				return false;
			}
			$domain = $_SERVER ['HTTP_HOST'];
			if (strtolower ( substr ( $domain, 0, 3 ) ) == 'www') {
				$domain = substr ( $domain, strpos ( $domain, '.' ) + 1 );
			}
			$list_domains = str_replace ( ' ', '', $domains );
			$list_domains = explode ( ',', $list_domains );
			if (! in_array ( $domain, $list_domains )) {
				$plan_check = 0;
			}						
		}
		
		
		/*-------------*/	
		if ( $expired_date) {
			
			$duration_checked = time () - ( int ) $lasted_check;
			
			if ($duration_checked < $plan_check) {
				if (isset ( $_SESSION [md5 ( $_SERVER ['HTTP_HOST'] . 'javcheking' )] ))
					unset ( $_SESSION [md5 ( $_SERVER ['HTTP_HOST'] . 'javcheking' )] );
				if (isset ( $_SESSION [md5 ( $_SERVER ['HTTP_HOST'] . 'javerrors' )] ))
					unset ( $_SESSION [md5 ( $_SERVER ['HTTP_HOST'] . 'javerrors' )] );
				return true;
			} else { /* Auto checking*/
				$sql = "select * from #__jav_configs where `group` ='key'";
				$db->setQuery ( $sql );
				$row = $db->loadObject ();
				if ($row) {
					if ($row->data != '') {
						if (! defined ( 'AUTO_CHECKING' )){
							define ( 'AUTO_CHECKING', true );
						}
						
						if (! $this->verify_new_license_key ( $row->data, false )) {
							$this->redirect_to_license_manager ();
						} 
						else{
							return true;
						}
					}
						
				}
				
			}
			
		
		}
		
		$_SESSION [md5 ( $_SERVER ['HTTP_HOST'] . 'javcheking' )] = true;
		return false;
	
	}
	
	/**
	 * Enter description here...
	 *
	 */
	function show_status_msg() {
		global $javconfig;
		
		$db = JFactory::getDBO ();
		$sql = "select data from #__jav_configs where `group`='license'";
		$db->setQuery ( $sql );
		$data = $db->loadResult ();
		
		if (! $data)
			return;
		
		$params = class_exists('JRegistry')? new JRegistry($data) : new JParameter($data);
		
		$domain = $_SERVER ['HTTP_HOST'];
		if (strtolower ( substr ( $domain, 0, 3 ) ) == 'www') {
			$domain = substr ( $domain, strpos ( $domain, '.' ) + 1 );
		}	
		
		$javconfig ['license']->set ( 'd13c3f7baed576768b11a714ef4d90e2', $params->get ( 'd13c3f7baed576768b11a714ef4d90e2' ) );
		switch ($params->get ( 'd13c3f7baed576768b11a714ef4d90e2' )) {
			case $domain . md5 ( 'invalid_domain' ) :
				{
					JError::raiseWarning ( 1, JText::_('YOUR_LICENSE_KEY_IS_NOT_ACCEPTED_PLEASE_CONTACT_JOOMLART_FOR_FURTHER_ASSISTANCE') );
					return;
				}
				break;
			
			case $domain . md5 ( 'expired' ) :
				{
					JError::raiseWarning ( 1, JText::_('YOUR_LICENSE_HAS_EXPIRED_THEREFORE_THE_SYSTEM_WILL_BE_DISABLED_PLEASE_CONTACT_JOOMLART_FOR_FURTHER_ASSISTANCE' ) );
					return;
				}
				break;
			
			case $domain . md5 ( 'invalid_key' ) :
				{
					JError::raiseWarning ( 1, JText::_('YOUR_LICENSE_KEY_IS_NOT_ACCEPTED_PLEASE_CONTACT_JOOMLART_FOR_FURTHER_ASSISTANCE' ) );
					return;
				}
				break;
			
			case $domain . md5 ( 'disabled_domain' ) :
				{
					JError::raiseWarning ( 1, JText::_('YOUR_LICENSE_KEY_IS_DISABLED_PLEASE_CONTACT_JOOMLART_FOR_FURTHER_ASSISTANCE' ) );
					return;
				}
				break;
		}		
	}

	
}
if (! class_exists ( 'SmartTrim' )) {




class SmartTrim {
	/*
      $hiddenClasses: Class that have property display: none or invisible.
    */
	public static function mb_trim($strin, $pos = 0, $len = 10000, $hiddenClasses = '', $encoding = 'utf-8') {
		mb_internal_encoding ( $encoding );
		$strout = trim ( $strin );
		
		$pattern = '/(<[^>]*>)/';
		$arr = preg_split ( $pattern, $strout, - 1, PREG_SPLIT_DELIM_CAPTURE );
		$left = $pos;
		$length = $len;
		$strout = '';
		for($i = 0; $i < count ( $arr ); $i ++) {
			$arr [$i] = trim ( $arr [$i] );
			if ($arr [$i] == '')
				continue;
			if ($i % 2 == 0) {
				if ($left > 0) {
					$t = $arr [$i];
					$arr [$i] = mb_substr ( $t, $left );
					$left -= (mb_strlen ( $t ) - mb_strlen ( $arr [$i] ));
				}
				
				if ($left <= 0) {
					if ($length > 0) {
						$t = $arr [$i];
						$arr [$i] = mb_substr ( $t, 0, $length );
						$length -= mb_strlen ( $arr [$i] );
						if ($length <= 0) {
							$arr [$i] .= '...';
						}
					
					} else {
						$arr [$i] = '';
					}
				}
			} else {
				if (SmartTrim::isHiddenTag ( $arr [$i], $hiddenClasses )) {
					if ($endTag = SmartTrim::getCloseTag ( $arr, $i )) {
						while ( $i < $endTag )
							$strout .= $arr [$i ++] . "\n";
					}
				}
			}
			$strout .= $arr [$i] . "\n";
		}
		//echo $strout;  
		return SmartTrim::toString ( $arr, $len );
	}

	public static function trim($strin, $pos = 0, $len = 10000, $hiddenClasses = '') {
		$strout = trim ( $strin );
		
		$pattern = '/(<[^>]*>)/';
		$arr = preg_split ( $pattern, $strout, - 1, PREG_SPLIT_DELIM_CAPTURE );
		$left = $pos;
		$length = $len;
		$strout = '';
		for($i = 0; $i < count ( $arr ); $i ++) {
			$arr [$i] = trim ( $arr [$i] );
			if ($arr [$i] == '')
				continue;
			if ($i % 2 == 0) {
				if ($left > 0) {
					$t = $arr [$i];
					$arr [$i] = substr ( $t, $left );
					$left -= (strlen ( $t ) - strlen ( $arr [$i] ));
				}
				
				if ($left <= 0) {
					if ($length > 0) {
						$t = $arr [$i];
						$arr [$i] = substr ( $t, 0, $length );
						$length -= strlen ( $arr [$i] );
						if ($length <= 0) {
							$arr [$i] .= '...';
						}
					
					} else {
						$arr [$i] = '';
					}
				}
			} else {
				if (SmartTrim::isHiddenTag ( $arr [$i], $hiddenClasses )) {
					if ($endTag = SmartTrim::getCloseTag ( $arr, $i )) {
						while ( $i < $endTag )
							$strout .= $arr [$i ++] . "\n";
					}
				}
			}
			$strout .= $arr [$i] . "\n";
		}
		//echo $strout;  
		return SmartTrim::toString ( $arr, $len );
	}
	
	public static function isHiddenTag($tag, $hiddenClasses = '') {
		//By pass full tag like img
		if (substr ( $tag, - 2 ) == '/>')
			return false;
		if (in_array ( SmartTrim::getTag ( $tag ), array ('script', 'style' ) ))
			return true;
		if (preg_match ( '/display\s*:\s*none/', $tag ))
			return true;
		if ($hiddenClasses && preg_match ( '/class\s*=[\s"\']*(' . $hiddenClasses . ')[\s"\']*/', $tag ))
			return true;
	}
	
	public static function getCloseTag($arr, $openidx) {
		$tag = trim ( $arr [$openidx] );
		if (! $openTag = SmartTrim::getTag ( $tag ))
			return 0;
		
		$endTag = "</$openTag>";
		$endidx = $openidx + 1;
		$i = 1;
		while ( $endidx < count ( $arr ) ) {
			if (trim ( $arr [$endidx] ) == $endTag)
				$i --;
			if (SmartTrim::getTag ( $arr [$endidx] ) == $openTag)
				$i ++;
			if ($i == 0)
				return $endidx;
			$endidx ++;
		}
		return 0;
	}
	
	public static function getTag($tag) {
		if (preg_match ( '/\A<([^\/>]*)\/>\Z/', trim ( $tag ), $matches ))
			return ''; //full tag
		if (preg_match ( '/\A<([^ \/>]*)([^>]*)>\Z/', trim ( $tag ), $matches )) {
			//echo "[".strtolower($matches[1])."]";
			return strtolower ( $matches [1] );
		}
		//if (preg_match ('/<([^ \/>]*)([^\/>]*)>/', trim($tag), $matches)) return strtolower($matches[1]);
		return '';
	}
	
	public static function toString($arr, $len) {
		$i = 0;
		$stack = new JAStack ( );
		$length = 0;
		while ( $i < count ( $arr ) ) {
			$tag = trim ( $arr [$i ++] );
			if ($tag == '')
				continue;
			if (SmartTrim::isCloseTag ( $tag )) {
				if ($ltag = $stack->getLast ()) {
					if ('</' . SmartTrim::getTag ( $ltag ) . '>' == $tag)
						$stack->pop ();
					else
						$stack->push ( $tag );
				}
			} else if (SmartTrim::isOpenTag ( $tag )) {
				$stack->push ( $tag );
			} else if (SmartTrim::isFullTag ( $tag )) {
				//echo "[TAG: $tag, $length, $len]\n";
				if ($length < $len)
					$stack->push ( $tag );
			} else {
				$length += strlen ( $tag );
				$stack->push ( $tag );
			}
		}
		
		return $stack->toString ();
	}
	
	public static function isOpenTag($tag) {
		if (preg_match ( '/\A<([^\/>]+)\/>\Z/', trim ( $tag ), $matches ))
			return false; //full tag
		if (preg_match ( '/\A<([^ \/>]+)([^>]*)>\Z/', trim ( $tag ), $matches ))
			return true;
		return false;
	}
	
	public static function isFullTag($tag) {
		//echo "[Check full: $tag]\n";
		if (preg_match ( '/\A<([^\/>]*)\/>\Z/', trim ( $tag ), $matches ))
			return true; //full tag
		return false;
	}
	
	public static function isCloseTag($tag) {
		if (preg_match ( '/<\/(.*)>/', $tag ))
			return true;
		return false;
	}
}
}

if (! class_exists ( 'JAStack' )) {
class JAStack {
	var $_arr = null;
	public function JAStack() {
		$this->_arr = array ();
	}
	
	public function push($item) {
		$this->_arr [count ( $this->_arr )] = $item;
	}
	public function pop() {
		if (! $c = count ( $this->_arr ))
			return null;
		$ret = $this->_arr [$c - 1];
		unset ( $this->_arr [$c - 1] );
		return $ret;
	}
	public function getLast() {
		if (! $c = count ( $this->_arr ))
			return null;
		return $this->_arr [$c - 1];
	}
	public function toString() {
		$output = '';
		foreach ( $this->_arr as $item ) {
			$output .= $item . "\n";
		}
		return $output;
	}
}
}

/********************* [DCODE] parser ********************\
               Courtesy of http://oopstudios.com/
           Class for parsing [DCODE] markup into HTML
  \**********************************************************/
if (! class_exists ( 'DCODE' )) {  
  class DCODE {
    //
    // A list of the tags and their parsing regex's
    //  Note that extra work is needed for the lists, you can see them in the main function...
    //    
    var $tags = array (
      "LARGE" =>   array ('/\[LARGE\](.*)\[\/LARGE\]/iUs',
                    "<h3>\\1</h3>"),
      "MEDIUM" =>  array ('/\[MEDIUM\](.*)\[\/MEDIUM\]/iUs',
                    "<h4>\\1</h4>"),
      "HR" =>      array ('/\[HR\]/iUs',
                    "<div class=\"hr\"><hr /></div>"),
      "B" =>       array ('/\[B\](.*)\[\/B\]/iUs',
                    "<strong>\\1</strong>"),
      "I" =>       array ('/\[I\](.*)\[\/I\]/iUs',
                    "<em>\\1</em>"),
      "U" =>       array ('/\[U](.*)\[\/U\]/iUs',
                    "<u>\\1</u>"),
      "S" =>       array ('/\[S\](.*)\[\/S\]/iUs',
                    "<strike>\\1</strike>"),
      "UL" =>      array ('/\[\*\](.*)\[\/\*\]/iUs',
                    "<uli>\\1</uli>"),
      "OL" =>      array ('/\[\#\](.*)\[\/\#\]/iUs',
                    "<oli>\\1</oli>"),
      "SUB" =>     array ('/\[SUB\](.*)\[\/SUB\]/iUs',
                    "<sub>\\1</sub>"),
      "SUP" =>     array ('/\[SUP\](.*)\[\/SUP\]/iUs',
                    "<sup>\\1</sup>"),
      "QUOTE" =>   array ('/\[QUOTE(.*)\](.*)\[\/QUOTE\]/iUs',
                    "<blockquote class='comment-quotecontent'><span class='comment-quoteclose'>\\1\\2</span></blockquote>"),
      "LINK" =>    array ('/\[LINK=([\w-]+@([\w-]+\.)+[\w-]+)\]\[\/LINK\]/iUs',
                    "<a href=\"mailto:\\1\">\\1</a>",
                          '/\[LINK=([^\]]*)\]\[\/LINK\]/iUs',
                    "<a href=\"\\1\">\\1</a>",
                          '/\[LINK=([\w-]+@([\w-]+\.)+[\w-]+)\](.+)\[\/LINK\]/iUs',
                    "<a href=\"mailto:\\1\">\\3</a>",
                          '/\[LINK=([^\]]*)\](.+)\[\/LINK\]/iUs',
                    "<a href=\"\\1\">\\2</a>"),
      "IMG" =>     array ('/\[IMG\](.*)\[\/IMG\]/iUs',
                    "<img src=\"\\1\" alt=\"\" width=\"100%\"/>"),
      "YOUTUBE" => array ('/\[YOUTUBE\](.*)\[\/YOUTUBE\]/iUs',
                    "<youtube>\\1</youtube>")
    );
    //
    // A whitelist of the tags we allow
    //
    var $whiteList = array ("LARGE", "MEDIUM", "HR", "B", "I", "U", "S", "UL", "OL", "SUB", "SUP", "QUOTE", "LINK", "IMG", "YOUTUBE");
    //
    // Functions for modifying the whitelist...
    //
    function addTag ($tag) {
      // Add it if necessary
      $pos = array_search ($tag, $this->whiteList);
      if ($pos === false) {
        if (array_key_exists ($tag, $this->tags)) {
          $this->whiteList[] = $tag;
        }
      }
    }
            
    function removeTag ($tag) {
      // Remove it if it exists
      $pos = array_search ($tag, $this->whiteList);
      if ($pos !== false) {
        array_splice ($this->whiteList, $pos, 1);
      }
    }
    function setTags () {
      $tags = func_get_args ();
      // Check each tag is OK
      $okTags = array ();
      foreach ($tags as $tag) {
        if (array_key_exists ($tag, $this->tags)) {
          // Use keys in case of dupe tags ;-)
          $okTags[$tag] = true;
        }
      }
      $this->whiteList = array_keys ($okTags);
    }
    //
    // Which YouTube method are we using?
    //
    var $youTubeMethod = "default";
    function setYouTubeMethod ($method) {
      // Valid values are default, swfobject_1, swfobject_2, swfobject
      //  everything else is "defaulted"
      $this->youTubeMethod = $method;
    }
    var $youTubeNum = 0;
    function formatYouTube ($matches) {
      // Increment
      $this->youTubeNum ++;
      // id and vidId
      $vidId = $matches[1];
      $id = "dcode_youtube_{$this->youTubeNum}";
      // Output the requested format
      switch ($this->youTubeMethod) {
        case "swfobject_1":
          // SWFObject v1
          return <<<HEREHTML
<div id="{$id}">
  <p><em>You will need to <a href="http://www.adobe.com/products/flashplayer/">get Flash 8</a> or better to watch <a href="http://www.youtube.com/watch?v={$vidId}">this video</a>.</em></p>
</div>
<script type="text/javascript">
   var so = new SWFObject ("http://www.youtube.com/v/{$vidId}", "{$id}_o", "300", "184", "8");
   so.write("{$id}");
</script>
HEREHTML;
          break;
        case "swfobject":
        case "swfobject_2":
          // SWFObject v2
          return <<<HEREHTML
<div id="{$id}">
  <p><em>You will need to <a href="http://www.adobe.com/products/flashplayer/">get Flash 8</a> or better to watch <a href="http://www.youtube.com/watch?v={$vidId}">this video</a>.</em></p>
</div>
<script type="text/javascript">
  swfobject.embedSWF ("http://www.youtube.com/v/{$vidId}", "{$id}", "300", "184", "8", null, null, {allowScriptAccess: "always"}, {id: "{$id}_o"});
</script>
HEREHTML;
          break;
        case "default":
        default:
          // Stright HTML
          return <<<HEREHTML
<object width="300" height="184" id="{$id}_o">
  <param name="movie" value="http://www.youtube.com/v/{$vidId}&amp;hl=en&amp;fs=1"></param>
  <param name="allowFullScreen" value="true"></param>
  <param name="allowscriptaccess" value="always"></param>
  <embed src="http://www.youtube.com/v/{$vidId}&amp;hl=en&amp;fs=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="300" height="184"></embed>
</object>
HEREHTML;
          break;
      }
    }
    //
    // Parses a [DCODE] string into (X)HTML using the tags set
    //
    function parse ($string) {    
      ini_set('display_errors', '0');	    		
      // Convert chars and regulate the line breaks
        $string = htmlentities ($string);
        $string = preg_replace ('/(\r\n|\r)/', "\n", $string);
      // Run the regex's
        foreach ($this->whiteList as $tag) {
          for ($t=0; $t<count ($this->tags[$tag]); $t+=2) {
            $string = preg_replace ($this->tags[$tag][$t], $this->tags[$tag][($t+1)], $string);
          }
        }
        $string = html_entity_decode($string); 
        
      // Split up block elements
        $preg = "/(<(h3|h4|div|blockquote|uli|oli|youtube)[^>]*>)(.*)(<\/\\2>)/Us";
        $matches = preg_split ($preg, $string, -1, PREG_SPLIT_DELIM_CAPTURE);
        // Uncomment this line to see how the "shape" of the split worked and thus
        // how / why the next part is as it is!
        // echo "<pre>" . htmlentities (print_r ($matches, true)) . "</pre>";
      // Add <br /> and <p>...</p> where needed
        $string = "";
        //var_dump($matches);
       
        
    	for ($m=-4, $n=count ($matches); $m<$n; $m+=5) {
	    	//if(isset($matches[$m])){
	          // $m   = opening tag
	          // $m+1 = tagname (ignore)
	          // $m+2 = untrimmed tag contents
	          // $m+3 = closing tag
	          $string .= "\n" . $matches[$m] . str_replace ("\n", "<br />", trim ($matches[$m+2])) . $matches[$m+3];
	          // $m+4 = paragraph(s)
	          $tmp = trim ($matches[$m+4]);
	          if ($tmp) {
	            $tmp = preg_replace ("/\n\n+/", "</p><p>", $tmp);
	            $tmp = preg_replace ("/\n/", "<br />", $tmp);
	            $tmp = preg_replace ("/<\/p><p>/", "</p>\n<p>", $tmp);
	            $string .= "\n<p>" . $tmp . "</p>";
	          }
	    	//}
        }
      // Do them youtubers!
        $string = preg_replace_callback ("/<youtube>(.*)<\/youtube>/iUs", array ($this, 'formatYouTube'), $string);
      // Lists need wrapping up
        // This step always seems unnecesarily large, the truth is that I can't
        // get my head around any regex I could use with preg_replace!
        $preg = "/(<(u|o)li>.*<\/\\2li>)/Us";
        $matches = preg_split ($preg, $string, -1, PREG_SPLIT_DELIM_CAPTURE);
        // Loop and group
        $string = "";
        $curlist = "";
        for ($m=0, $n=count ($matches); $m<$n; $m+=3) {
          // $m = plain content (or empty)
          if (trim ($matches[$m])) {
            // Close the previous list?
            if ($curlist) {
              $string .= "</{$curlist}l>";
              $curlist = "";
            }
            // The string
            $string .= $matches[$m];
          }
          // $m+1 = list item
          // $m+2 = list item type
          if (isset($matches[$m+2])) {
            if (!$curlist) {
              $string .= "<" . $matches[$m+2] ."l>\n";
            } elseif ($matches[$m+2] != $curlist) {
              $string .= "</{$curlist}l>\n<" . $matches[$m+2] ."l>\n";
            }
            $curlist = $matches[$m+2];
            $string .= "  " . $matches[$m+1] . "\n";
          }
        }
        // If the last entry is empty, close the final list
        if(isset($matches[$n])) echo $matches[$n];
        if (isset($matches[$n])) {
          if ($curlist) {
            $string .= "</{$curlist}l>";
          }
        }
        // Change <uli> & <oli> to plain old <li>
        $string = preg_replace ('/<(\/?)(uli|oli)>/iUs', '<$1li>', $string);
        // Add line-breaks to <br /> (just a visual thing)
        $string = preg_replace ('/<br\s\/>/iUs', "<br />\n", $string);
      // Return
		
        return trim ($string);    
    }
  }
}