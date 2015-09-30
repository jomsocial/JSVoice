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
defined ( '_JEXEC' ) or die ( 'Restricted access' ); 

jimport( 'joomla.application.component.model' );

/**
 * @package Joomla
 * @subpackage javoice
 */
class JAVoiceModelVoice extends JAVBModel
{
	
	function getKey(){
		global $javconfig;		
		$sql = "select data from #__jav_configs where `group`='key'";
		$db = JFactory::getDBO();
		$db->setQuery($sql);
		$key = $db->loadResult();
		return $key;
	}
	function getLatestVersion(){
		global $JVVERSION;
		if(isset($_SESSION['latest_version'])) 
			$latest_version =  $_SESSION['latest_version'];
		else 
		{
			global $JVPRODUCTKEY;	
			
			$req = 'type=product_name';
			$req .= '&key=com_javoice';
			$req .= '&jversion=1.6';
			//$req .= '&current_version='.$JVVERSION;			
			$host = 'www.joomlart.com';
		    $path = '/forums/getlatestversion.php';
		    $URL = "http://$host$path";
		    $latest_version = '';
		    if(!function_exists('curl_version')) {
		    	if (stristr(ini_get('disable_functions'), "fsockopen")) {
		            return ;
		        }
		        else{	        	
		        	$latest_version = @JAVoiceHelpers::socket_getdata($host, $path, $req);
		        }
		    }
		    else{
			   $latest_version =@JAVoiceHelpers::curl_getdata($URL, $req);				      
		   }
		}
		$_SESSION['latest_version'] = $latest_version;	
		  
	   return $latest_version;
	}	

}
?>
