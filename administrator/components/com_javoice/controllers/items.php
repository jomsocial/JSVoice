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

class JAVoiceControllerItems extends JAVoiceController {
	
	function __construct($default = array()) {
		
		parent::__construct ( $default );
		// Register Extra tasks
		JRequest::setVar ( 'view', 'items' );
		$this->registerTask ( 'add', 'edit' );
		$this->registerTask ( 'apply', 'save' );
	
	}
	
	function display($cachable = false, $urlparams = false) {
		
		$user = JFactory::getUser ();
		$task = $this->getTask();
		switch ($task){
			case 'edit':
				JRequest::setVar ( 'layout', 'form' );
				break;
			case 'response':
				JRequest::setVar ( 'layout', 'response' );
				break;				
		}
		if ($user->id == 0) {
			
			JError::raiseWarning ( 1001, JText::_("YOU_MUST_BE_SIGNED_IN" ) );
			
			$this->setRedirect ( JRoute::_ ( "index.php?option=com_user&view=login" ) );
			
			return;
		}
		
		parent::display($cachable = false, $urlparams = false);
	}
	
	function cancel() {		
		$this->setRedirect ( 'index.php?option=com_javoice&view=items' );
		
		return TRUE;
	}
	
	function save(&$errors = '') {
		global $javconfig;	
		$task = $this->getTask ();		
		$model	= $this->getModel('items');
		$post	= JRequest::get('request');
		$helper = new JAVoiceHelpers ( );
		$post["title"]	= $helper->addSpaceInLongTitle(trim($post["title"]));
		
		// allow name only to contain html
		$post['content'] = JRequest::getString( 'newVoiceContent', '');
		$post['voice_types_id'] = JRequest::getInt('voice_types_id');
		$post['forums_id'] = JRequest::getInt('forums_id');		
		
		$lists 		= $model->getItem ();
		$old_forums_id 		= $lists->forums_id;
		$old_voice_types_id = $lists->voice_types_id;
		
		$model->setState( 'request', $post );
		$row = $model->store();
		
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
			
			$model->update_total_items ( $row->voice_types_id, $row->forums_id,$post["id"],$old_forums_id,$old_voice_types_id);
			
			//update tags
			if($javconfig['systems']->get("is_attach_image", 0)){
					
			}
		}else{
			$errors[] = $row;
			return FALSE;	
		}
			
		return $row->id;
	}
	
			
	function saveIFrame() {
		global $javconfig;
		$helper = new JAVoiceHelpers ( );		
		$post = JRequest::get ( 'request', JREQUEST_ALLOWHTML );		
		
		//check length of title
		if(strlen($post["title"]) <3){
			$helper->showMessenger(JText::_("YOU_MUST_INPUT_TITLE_AT_LEAST_3_CHARACTERS"));
			return;	
		}					
		
		$post ['content'] = $post['newVoiceContent'];	
		$voicetype_default = $post['voicetype_default'];
		$number = $post ['number'];
		$errors = array ();
		$id = $this->save ( $errors );		
		$objects = array ();
		//save successfull
		if ($id) {
			
		//save file upload	
		if($javconfig['plugin']->get("is_attach_image", 0)){
			//delete file in store image if remove file
			jimport( 'joomla.filesystem.folder' );
			jimport('joomla.filesystem.file');
				
			$listFile = JRequest::getVar('listfile', 0);
			
			$file_path 			 =  JPATH_ROOT.DS."images".DS."stories".DS."ja_voice".DS.$id;
			
			$listFileOfComments  =  JFolder::files($file_path);																			
			
			if($listFileOfComments){
				foreach ($listFileOfComments as $listFileOfComment){
					if($listFile){
						if (!is_array($listFile)) {
							$listFile = explode(',', $listFile);
						}
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
					$target_path =  JPATH_ROOT.DS."images".DS."stories".DS."ja_voice".DS.$id;																
					
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
				$javtags = JRequest::getVar("javtag", "");
				$modelTag = $this->getModel('tags'); 
				$modelTag->addVoiceToTag($id,$javtags);
			}
			
			
			$model = $this->getModel ( 'items' );
			
			$fiel = " f.title as forums_title,t.title as voice_types_title ";
			$join = " 	INNER JOIN #__jav_forums as f ON f.id=i.forums_id 
	 					INNER JOIN #__jav_voice_types as t ON t.id=i.voice_types_id";
			
			$items = $model->getItems ( " AND i.id=$id ", '', 0, 1, $fiel, $join );
			if ($items)
				
				$model->parseItems ( $items );
			
			$item = $items [0];
			$reload = 0;
			if($post['id']=='0'){
				$reload = 1; 				
				if($item->voice_types_id !=$voicetype_default){					
					$objects [] = $helper->parseProperty ( "value", "#voicetypes",$item->voice_types_id );
				}
			}else{
					if($item->voice_types_id !=$voicetype_default)$reload = 1; 				
			}
			$objects [] = $helper->parseProperty ( "reload", "#reload" . $item->id, $reload );
			if(!$reload){
				$objects [] = $helper->parseProperty ( "html", "#system-message", $helper->message ( 0, JText::_("SAVE_DATA_SUCCESSFULLY" ) ) );					
				$objects [] = $helper->parseProperty ( "html", "#title" . $item->id, $item->title );
				$objects [] = $helper->parseProperty ( "html", "#forums_title" . $item->id, $item->forums_title );
				$objects [] = $helper->parseProperty ( "html", "#voice_types_title" . $item->id, $item->voice_types_title );
				$objects [] = $helper->parseProperty ( "html", "#create_date" . $item->id, date ( 'Y:m:d', $item->create_date ) );
				$objects [] = $helper->parseProperty ( "html", "#number_vote_up" . $item->id, $item->number_vote_up );
				$objects [] = $helper->parseProperty ( "html", "#number_vote_up" . $item->id, $item->number_vote_up );
				$objects [] = $helper->parseProperty ( "html", "#number_vote_down" . $item->id, $item->number_vote_down );
				$objects [] = $helper->parseProperty ( "html", "#total_vote_down" . $item->id, $item->total_vote_down );
				$objects [] = $helper->parseProperty ( "html", "#number_vote_neutral" . $item->id, $item->number_vote_neutral );
				$objects [] = $helper->parseProperty ( "html", "#number_spam" . $item->id, $item->number_spam );
				//$objects [] = $helper->parseProperty ( "html", "#number_duplicate" . $item->id, $item->number_duplicate );
				//$objects [] = $helper->parseProperty ( "html", "#number_inapproprivate" . $item->id, $item->number_inapproprivate );
				$objects [] = $helper->parseProperty ( "html", "#voice_type_status_title" . $item->id, $item->voice_type_status_title );
				
				$objects [] = $helper->parsePropertyPublish ( "html", "#publish" . $item->id, $item->published, $number );
				//$objects [] = $helper->parsePropertyPublisha ( $item->id, $item->published );
			}		
		
		} else {
			$objects [] = $helper->parseProperty ( "html", "#system-message", $helper->message ( 1, $errors ) );
		}
		
		$helper = new JAVoiceHelpers ( );		
		echo $helper->parse_JSON_new ( $objects );
		exit ();
	}
	function saveResponse(){		
		global $javconfig;
		$model	= $this->getModel('items');		
		
		$post = JRequest::get ( 'request', JREQUEST_ALLOWRAW);		
		$post['content'] = $post['newVoiceContent'];
		$model->setState( 'request', $post );
		$row = $model->store_admin_response();
		
		$helper = new JAVoiceHelpers ( );
		$objects = array ();
		$errors = array();
		$objects = array();
		$cid = JRequest::getVar ( 'cid', array (0 ), '', 'array' );
		JArrayHelper::toInteger ( $cid, array (0 ) );
		
		if(!$cid){
			$errors[]=  JText::_("ERROR_OCCURRED_DATA_NOT_SAVED" );
			$objects [] = $helper->parseProperty ( "html", "#system-message", $helper->message ( 1, $errors ) );
		}
		else{
			if($row->id){
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
				
				
				
				$message [] = JText::_("SAVE_DATA_SUCCESSFULLY" );
				$objects [] = $helper->parseProperty ( "html", "#system-message", $helper->message ( 0, $message ) );
			}
			else{
				$errors[]=  JText::_("ERROR_OCCURRED_DATA_NOT_SAVED" );
				$objects [] = $helper->parseProperty ( "html", "#system-message", $helper->message ( 1, $errors ) );	
			}
		}
				
		$helper = new JAVoiceHelpers ( );		
		echo $helper->parse_JSON_new ( $objects );
		exit ();		
	}
	
	function saveorder() {
		$model = $this->getModel ( 'items' );
		$msg = '';
		if (! $model->saveOrder ()) {
			JError::raiseWarning ( 1001, JText::_('ERROR_OCCURRED_DATA_NOT_SAVED' ) );
		} else {
			$msg = JText::_('NEW_ORDER_SAVED' );
		}
		$this->setRedirect ( 'index.php?option=com_javoice&view=items', $msg );
	}
	
	function publish() {
		$model = $this->getModel ( 'items' );
		$createdate = JRequest::getInt('createdate',0);
		if (! $model->published ( 1 )) {
			JError::raiseWarning ( 1001, JText::_('ERROR_OCCURRED_DATA_NOT_SAVED' ) );
		} else {
			$msg = JText::_('SAVE_DATA_SUCCESSFULLY' );
		}
		$link = 'index.php?option=com_javoice&view=items';
		if($createdate) $link.="&createdate=".$createdate;
		$this->setRedirect ( $link, $msg );
	}
	
	function unpublish() {
		$model = $this->getModel ( 'items' );
		$createdate = JRequest::getInt('createdate',0);
		if (! $model->published ( 0 )) {
			JError::raiseWarning ( 1001, JText::_('ERROR_OCCURRED_DATA_NOT_SAVED' ) );
		} else {
			$msg = JText::_('SAVE_DATA_SUCCESSFULLY' );
		}
		$link = 'index.php?option=com_javoice&view=items';
		if($createdate) $link.="&createdate=".$createdate;		
		$this->setRedirect ( $link, $msg );
	}
	
	function uploadFile(){
		global $javconfig;
		$helper = new JAVoiceHelpers();
		$maxSize = (int)$helper->getSizeUploadFile("byte");
		echo '<script language="javascript" type="text/javascript">
	   			var par = window.parent.document;
	   			par.adminForm.task.value = "saveIFrame";
	   		  </script>';							
				
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
					$img .= "<input type='checkbox' onclick='javCheckTotalFile()' name='listfile[]' value='$listFile' checked></span>&nbsp;&nbsp;<img src='../components/com_javoice/asset/images/icons/". $type .".gif' alt='". $type ."' /> " .$listFile . "</br>";
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
						$img .= "<input type='checkbox' onclick='javCheckTotalFile()' name='listfile[]' value='$listFilesInFolder' disabled='disabled'></span>&nbsp;&nbsp;<img src='../components/com_javoice/asset/images/icons/". $type .".gif' alt='". $type ."' /> " .$listFilesInFolder . "</br>";
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
						$img .= "<input type='checkbox' onclick='javCheckTotalFile()' name='listfile[]' value='$listFilesInFolder' disabled='disabled'></span>&nbsp;&nbsp;<img src='../components/com_javoice/asset/images/icons/". $type .".gif' alt='". $type ."' /> " .$listFilesInFolder . "</br>";
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
					 $img .= "<input type='checkbox' name='listfile[]' onclick='javCheckTotalFile()' value='$fname' checked>&nbsp;&nbsp;<img src='../components/com_javoice/asset/images/icons/". $type .".gif' /> " .$fname . "<br />";					 
			   }
			}
			
			echo '<script language="javascript" type="text/javascript">
	   		var par = window.parent.document;			   		
			function stopUpload(par, listfile, count, totalUpload){									  		  
				par.getElementById(\'jav_err_myfile\').innerHTML = "";   			  					  
				par.adminForm.target = "_self";
				
				par.getElementById(\'jav_upload_process\').style.display=\'none\';
				par.getElementById(\'jav_result_upload\').innerHTML = listfile;
				par.adminForm.myfile.value = "";
				if(eval(count)>=totalUpload){
						if(1<=totalUpload){
							par.adminForm.myfile.disabled = true;
							par.getElementById(\'jav_err_myfile\').style.display = "block";
					par.getElementById(\'jav_err_myfile\').innerHTML = "'. JText::_("YOU_ADDED").'" + totalUpload + " '. JText::_("FILES").'!";
						}else{						  		
							par.getElementById(\'jav_err_myfile\').style.display = "block";
							par.adminForm.myfile.disabled = true;
					par.getElementById(\'jav_err_myfile\').innerHTML = "'. JText::_("YOU_ADDED").'" + totalUpload + " '. JText::_("FILE").'!";
				}  																
				}					  
				return true;   
			}</script>';
								   			 
			if($fileexist){				
				echo '<script language="javascript" type="text/javascript">								
						var par = window.parent.document;
						par.getElementById(\'jav_err_myfile\').style.display = "block";													
						par.getElementById(\'jav_err_myfile\').innerHTML = "<span class=\'err\' style=\'color:red\'>'. JText::_("THIS_FILE_EXISTED") .'</span>";									
						par.getElementById("jav_upload_process").style.display="none";
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
					par.adminForm.myfile.value = "";
					par.getElementById(\'jav_err_myfile\').style.display = "block";
					par.getElementById(\'jav_err_myfile\').innerHTML = "'. JText::_("LIMITATION_OF_UPLOAD_IS").$helper->getSizeUploadFile().'.";  		
					par.adminForm.myfile.focus();					
				</script>';
		}
	}
	
	
	function uploadReplyFile(){
		global $javconfig;
		$helper = new JAVoiceHelpers();
		$maxSize = (int)$helper->getSizeUploadFile("byte");
		echo '<script language="javascript" type="text/javascript">
	   			var par = window.parent.document
	   			par.adminForm.task.value = "saveResponse";
	   		  </script>';										
		if (isset($_FILES['myfile']['name']) && $_FILES['myfile']['size']>0 && $_FILES['myfile']['size']<= $maxSize && $_FILES['myfile']['tmp_name']!=''){																													
			jimport( 'joomla.filesystem.folder' );
			jimport('joomla.filesystem.file');												
			
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
					$img .= "<input type='checkbox' onclick='javCheckTotalFileReply()' name='listfile[]' value='$listFile' checked></span>&nbsp;&nbsp;<img src='../components/com_javoice/asset/images/icons/". $type .".gif' alt='". $type ."' /> " .$listFile . "</br>";
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
						$img .= "<input type='checkbox' onclick='javCheckTotalFileReply()' name='listfile[]' value='$listFilesInFolder' disabled='disabled'></span>&nbsp;&nbsp;<img src='../components/com_javoice/asset/images/icons/". $type .".gif' alt='". $type ."' /> " .$listFilesInFolder . "</br>";
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
						$img .= "<input type='checkbox' onclick='javCheckTotalFileReply()' name='listfile[]' value='$listFilesInFolder' disabled='disabled'></span>&nbsp;&nbsp;<img src='../components/com_javoice/asset/images/icons/". $type .".gif' alt='". $type ."' /> " .$listFilesInFolder . "</br>";
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
					 $img .= "<input type='checkbox' name='listfile[]' onclick='javCheckTotalFileReply()' value='$fname' checked>&nbsp;&nbsp;<img src='../components/com_javoice/asset/images/icons/". $type .".gif' /> " .$fname . "<br />";					 
			   }
			}
			
			echo '<script language="javascript" type="text/javascript">
	   		var par = window.parent.document;			   		
			function stopUpload(par, listfile, count, totalUpload){					  		  
				par.getElementById(\'jav_err_myfilereply\').innerHTML = "";   			  					  
				par.adminForm.target = "_self";
				
				par.getElementById(\'jav_reply_upload_process\').style.display=\'none\';
				par.getElementById(\'jav_result_reply_upload\').innerHTML = listfile;
				par.adminForm.myfile.value = "";
				if(eval(count)>=totalUpload){
						if(1<=totalUpload){
							par.adminForm.myfile.disabled = true;
							par.getElementById(\'jav_err_myfilereply\').style.display = "block";
							par.getElementById(\'jav_err_myfilereply\').innerHTML = "'. JText::_("YOU_ADDED").'" + totalUpload + " '. JText::_("FILES").'!";
						}else{						  		
							par.adminForm.myfile.disabled = true;
							par.getElementById(\'jav_err_myfilereply\').style.display = "block";
							par.getElementById(\'jav_err_myfilereply\').innerHTML = "'. JText::_("YOU_ADDED").'" + totalUpload + " '. JText::_("FILE").'!";
				}  																
				}					  
				return true;   
			}</script>';
								   			 
			if($fileexist){				
				echo '<script language="javascript" type="text/javascript">								
						var par = window.parent.document;
						par.getElementById(\'jav_err_myfilereply\').style.display = "block";													
						par.getElementById(\'jav_err_myfilereply\').innerHTML = "<span class=\'err\' style=\'color:red\'>'. JText::_("THIS_FILE_EXISTED") .'</span>";									
						par.getElementById("jav_reply_upload_process").style.display="none";
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
					par.adminForm.myfile.value = "";
					par.getElementById(\'jav_err_myfilereply\').style.display = "block";
					par.getElementById(\'jav_err_myfilereply\').innerHTML = "'. JText::_("LIMITATION_OF_UPLOAD_IS").$helper->getSizeUploadFile().'.";  		
					par.adminForm.myfile.focus();					
				</script>';
		}
	}
	
	
	
	
	function remove() {
		$model = $this->getModel ( 'items' );
		$cids = JRequest::getVar ( 'cid', null, 'post', 'array' );
		$error = array ();
		$err = '';
		$msg = '';
		
		foreach ( $cids as $cid ) {
			$error = $model->delete ( $cid );
			if($error){
				$err .= implode ( ",", $error );
			}
		}
		
		if ($err) {
			JError::raiseWarning ( 1001, $err );
		} else
			$msg = JText::_("DELETE_DATA_SUCCESSFULLY" );
		$this->setRedirect ( 'index.php?option=com_javoice&view=items', $msg );
	}
}
?>