<?php
defined('_JEXEC') or die();

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
jimport('joomla.application.component.model');
jimport('joomla.filesystem.file');
class javoiceModelsendmail extends JAVBModel
{
	var $_filename='';
 function __construct()
	{
		parent::__construct();
		
	}	
	function setFilename($value){
		$this->_filename = $value;	
	}
	function writeLogFileChange($contents,$filename=''){
		
		if($filename=='')$filename = $this->_filename;
		JFile::write($filename,$contents);
	}
	function readFile($path){
		
		$files=JFolder::files($path);
		$parms='';
		if($files){
			$this->_filename=$path.'/'.$files[0];
			$content = JFile::read($this->_filename);
			$parms = class_exists('JRegistry')? new JRegistry($content) : new JParameter($content);		
		}else return FALSE;
		return $parms;
	}
	function deleteFile(){
		
		if(JFile::exists($this->_filename))
			JFile::delete($this->_filename);
	}
	function checkSendMail(){
		$db = JFactory::getDBO();
		$sql = " SELECT value FROM #__jav_temp_data WHERE id=1";
		$db->setQuery($sql);
		$value = $db->loadResult();
		$value = $value?$value:0;
		if(time() > $value + 10  )
				return TRUE;
				
		return FALSE;
	}
	function checkOut(){
		$db = JFactory::getDBO();	
		$sql = " DELETE FROM #__jav_temp_data WHERE id=1"	;
		$db->setQuery($sql);
		return $db->query();
	}
	function checkIn(){
		if($this->checkSendMail()){
			if($this->checkOut()){
				$db = JFactory::getDBO();
				$tem = new stdClass();
				$tem->id=1;
				$tem->name="Send mail";
				$tem->value =time() ;
				if($db->insertObject("#__jav_temp_data",$tem))
					return TRUE;			
			}
		}
		return FALSE;	

	}
}
?>