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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class javoiceControllerTags extends JAVoiceController
{

	function display($cachable = false, $urlparams = false) {
		JRequest::setVar('view', 'tags');
		$task =$this->getTask();		
		
		if($task=="edit") JRequest::setVar ( 'layout', 'form' );		
		parent::display($cachable = false, $urlparams = false);
	}				
	
	function addnew(){
		global $javconfig;
		$tags = JRequest::getVar("taglist", "");									
		if($tags){			
			$list = explode($javconfig['systems']->get('characters_separating_tags',','), $tags);
			$model = $this->getModel('tags');
			$post = array();
			$tags = "";
			$objects = array ();
			$helper = new JAVoiceHelpers ( ); 			
			foreach ($list as $i=>$item){
				$id = $model->checkExistTag($item);
				//if don't exist tags				
				if(!$id){			
					if($javconfig["systems"]->get("tag_to_be_lower_case",0)){
						$item = strtolower($item);	
					}						
					$post["name"] = $item;
					$post["published"] = 1;
					$post["id"] = 0;					
					if(strlen($item)<$javconfig["systems"]->get("tag_minimum_length",10)){									
						$id = JText::_("LENG_OF_TAG")." '".$item."' ".JText::_("IS_VERY_SHORT");
						$item = "-javmsg-";
					}else if(strlen($item)>$javconfig["systems"]->get("tag_maximum_length",100)){
						$id = JText::_("LENG_OF_TAG")." '".$item."' ".JText::_("IS_VERY_LONG");
						$item = "-javmsg-";
					}else{
						$id = $model->save($post,1);
					}
					$objects [] = $helper->parseProperty ( $item,$id );
				}else{
					$objects [] = $helper->parseProperty ( $item,$id );
				}
			}
			$helper = new JAVoiceHelpers ( );		
			echo $helper->parse_JSON_new ( $objects );
			exit ();					
		}else{
			exit();
		}
	}
	
	function save() {
		global $javconfig;
		$model = $this->getModel('tags');
		$post["name"] = JRequest::getVar("name", "");
		$post["published"] = JRequest::getInt("published", 1);
		$post["id"] = JRequest::getInt("id", 0);
		
		if(strlen($post["name"])<$javconfig["systems"]->get("tag_minimum_length",10)){																		
			$helper = new JAVoiceHelpers ( );
			$errors = JText::_("LENG_OF_TAG")." '".$post["name"]."' ".JText::_("IS_VERY_SHORT");
			$objects [] = $helper->parseProperty ( "-javmsg-", "alert", $errors );
			
			$helper = new JAVoiceHelpers ( );		
			echo $helper->parse_JSON_new ( $objects );
			exit ();	
		}else if(strlen($post["name"])>$javconfig["systems"]->get("tag_maximum_length",100)){
			$helper = new JAVoiceHelpers ( );
			$errors = JText::_("LENG_OF_TAG")." '".$post["name"]."' ".JText::_("IS_VERY_LONG");
			$objects [] = $helper->parseProperty ( "-javmsg-", "alert", $errors );
			
			$helper = new JAVoiceHelpers ( );		
			echo $helper->parse_JSON_new ( $objects );
			exit ();			
		}else{
			$model = $this->getModel('tags');
			if($model->checkExistTag($post["name"],$post["id"])){
				$helper = new JAVoiceHelpers ( );
				$errors = JText::_("TAG_NAME_ALREADY_EXIST");
				$objects [] = $helper->parseProperty ( "-javmsg-", "alert", $errors );
				
				$helper = new JAVoiceHelpers ( );		
				echo $helper->parse_JSON_new ( $objects );
				exit ();
			}
			
			$model->save($post);			
		}					
	}
	
	function remove(){
		JRequest::checkToken() or jexit('Invalid Token');
		$model = $this->getModel('tags');
		$model->remove();
	}

	function apply() {
		$this->save();
	}

	function cancel() {
		$mainframe = JFactory::getApplication();
		$mainframe->redirect('index.php?option=com_javoice&view=tags');
	}
	
	function publish() {
		$model = $this->getModel ( 'tags' );		
		if (! $model->published ( 1 )) {
			JError::raiseWarning ( 1001, JText::_('ERROR_OCCURRED_DATA_NOT_SAVED' ) );
		} else {
			$msg = JText::_('SAVE_DATA_SUCCESSFULLY' );
		}
		$link = 'index.php?option=com_javoice&view=tags';		
		$this->setRedirect ( $link, $msg );
	}
	
	function unpublish() {
		$model = $this->getModel ( 'tags' );		
		if (! $model->published ( 0 )) {
			JError::raiseWarning ( 1001, JText::_('ERROR_OCCURRED_DATA_NOT_SAVED' ) );
		} else {
			$msg = JText::_('SAVE_DATA_SUCCESSFULLY' );
		}
		$link = 'index.php?option=com_javoice&view=tags';			
		$this->setRedirect ( $link, $msg );
	}

}
