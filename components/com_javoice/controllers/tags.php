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

class javoiceControllerTags extends JAVFController
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
			$k=0;
			foreach ($list as $i=>$item){
				$id = $model->checkExistTag($item);				
				if(!$id){									
					$post["name"] = $item;
					$post["published"] = 1;
					$post["id"] = 0;					
					if($i==0){
						$tags .= $post["name"];
					}else{
						$tags .= $javconfig['systems']->get('characters_separating_tags',',').$post["name"];
					}										
					$id = $model->save($post,1);				
					$object [$k] = new stdClass ( );
					$object [$k]->id = $id;
					$object [$k]->type = $item;
					$k++;
				}else{					
					$object [$k] = new stdClass ( );
					$object [$k]->id = $id;
					$object [$k]->type = $item;
					$k++;
				}
			}
			$helper = new JAVoiceHelpers ( );		
			echo $helper->parse_JSON_new ( $object );
			exit ();										
		}else{
			exit();
		}
	}
	
	function save() {		
		//JRequest::checkToken() or jexit('Invalid Token');
		$model = $this->getModel('tags');
		$model->save();
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
