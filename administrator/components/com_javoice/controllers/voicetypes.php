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
defined('_JEXEC') or die('Restricted access');

class javoiceControllervoicetypes extends JAVoiceController
{
	
 function __construct( $default = array() )
    {
        parent::__construct( $default );
        // Register Extra tasks
        JRequest::setVar('view','voicetypes');
        $this->registerTask( 'add',        'edit' );
        $this->registerTask( 'apply',    'save' );
    }
    
 function display($cachable = false, $urlparams = false){	    	
    	$user = JFactory::getUser();
        if ($user->id==0)
        {
        	JError::raiseWarning(1001,JText::_("YOU_MUST_BE_SIGNED_IN"));
        	$this->setRedirect(JRoute::_("index.php?option=com_user&view=login"));
        	return ;
        }	        
		parent::display($cachable = false, $urlparams = false);
    } 
    
 function edit(){

    	JRequest::setVar('edit', true);    	
        JRequest::setVar('layout','form');        
        parent::display();    	
    }
    
 function cancel(){
	    $this->setRedirect('index.php?option=com_javoice&view=voicetypes');
	    return TRUE; 	  
 	}    
	
 function save(&$errors=''){
 	
 		$task = $this->getTask ();
		$model= $this->getModel('voicetypes');
		$item = $model->getItem();
        $post    = JRequest::get('request');

		$paramsField_value = JRequest::getVar( 'votes_value', null, 'request', 'array' );
		
		$paramsField_text = JRequest::getVar( 'votes_text', null, 'request', 'array' );
		
		$paramsField_description = JRequest::getVar( 'votes_description', null, 'request', 'array' );
		
		$params = array();
				
		if ($paramsField_value)
		{	
			$error=FALSE;			
			foreach ($paramsField_value as $value){
				if(!is_numeric($value))
				{
					$error=TRUE;
					break;						
	
				}								
			}
			if($error) $errors[]=JText::_("VALUE_MUST_NOT_BE_INTEGER");
			$votes_value=implode("###",$paramsField_value);
			$params[]="votes_value=".$votes_value;
							
		}
 		
 		if ($paramsField_text)
		{	
			$error=FALSE;
			foreach ($paramsField_value as $value){
				if($value == '')
				{					
					$error=TRUE;
					break;				   
				
				}								
				
			}
			if($error) $errors[]=JText::_("TITLE_MUST_NOT_BE_NULL");
							
			$votes_text=implode("###",$paramsField_text);
			$params[]="votes_text=".$votes_text;
							
		}
		
		if($errors){
			$array=array(JText::_('VALUE_OF_VOTE_OPTIONS_IS_INCORRECT').": ");
			$errors=array_merge($array,$errors);
		}
		
 		if ($paramsField_description)
		{	
			
			$votes_description=implode("###",$paramsField_description);
			$params[]="votes_description=". $votes_description;
							
		}		

		$post['vote_option']='';

		if($params)
			$post['vote_option'] = implode("\n", $params);

        if (!$item->bind( $post )) 
       	{
		    $errors[]=JText::_("DO_NOT_BIND_DATA");

	    }  
	    
		if($errors)
		{
			return FALSE;
		}
		$item->title = trim($item->title);
	    $errors=$item->check();
	    
		if (count($errors)>0)
	    {	    	   	
		    return FALSE;
	    }	 
	    $where = " AND t.title = '$item->title' AND t.id!=$item->id";
		$count = $model->getTotal($where);
 		if($count>0){
			$errors[] = JText::_("ERROR_OCCURRED_DUPLICATE_FOR_VOICE_TYPE_TITLE" );
			return FALSE;
		}		
	    if($item->alias=='')$item->alias= JFilterOutput::stringURLSafe($item->title);	    
		if (!$item->store())
	    {	    	
		    $errors[]=JText::_("ERROR_OCCURRED_DATA_NOT_SAVED");
		    return FALSE;
	    }
	    else $item->reorder(1);	
	    if ($task != 'saveIFrame') {
	    	
		    $link='index.php?option=com_javoice&view=voicetypes';
		    if($this->getTask()=='apply')$link.="&task=edit&cid[]=".$item->id;
		    $msg=JText::_('SAVE_DATA_SUCCESSFULLY');
		    $this->setRedirect($link,$msg);
	    
	    }
	    return $item->id;
	}
	
	function saveIFrame() {
		
		$post = JRequest::get ( 'request' );
		$number=$post['number'];
		$errors=array();
		$id = $this->save ($errors);
		
		$helper = new JAVoiceHelpers ( );
		$objects = array ();
		
		if (count($errors)==0) {
									
			$model= $this->getModel('voicetypes');

			$item = $model->getItem ($id );	
			
			if($post['id']=='0')
				$objects [] = $helper->parseProperty ( "reload", "#reload" . $item->id, 1 );
			else 
				$objects [] = $helper->parseProperty ( "html", "#system-message", $helper->message ( 0, JText::_("SAVE_DATA_SUCCESSFULLY" ) ) );
			$objects [] = $helper->parseProperty ( "html", "#title" . $item->id, $item->title );

			$objects [] = $helper->parsePropertyPublish ( "html", "#publish" . $item->id, $item->published,$number);
 			if($item->total_votes<0)$item->total_votes = JText::_("UNLIMITED");			
			$objects [] = $helper->parseProperty ( "html", "#total_votes" . $item->id, $item->total_votes);
			
			$objects [] = $helper->parseProperty ( "value", "#order" . $item->id, $item->ordering);

		} else {
			$objects [] = $helper->parseProperty ( "html", "#system-message", $helper->message ( 1, $errors ) );
		
		}
		$helper = new JAVoiceHelpers ( );		
		echo $helper->parse_JSON_new ( $objects );
		exit ();
	}
		
 function saveorder()
	{
		$model = $this->getModel('voicetypes');
		$msg='';
		if(!$model->saveOrder())
		{
			JError::raiseWarning(1001, JText::_('ERROR_OCCURRED_DATA_NOT_SAVED'));
		}
		else 
		{
			$msg=JText::_('NEW_ORDER_SAVED');
		}
		$this->setRedirect( 'index.php?option=com_javoice&view=voicetypes',$msg);
	}
	
 function publish()
	{
		$model = $this->getModel('voicetypes');
		if(!$model ->published(1))
		{
			JError::raiseWarning(1001, JText::_('ERROR_OCCURRED_DATA_NOT_SAVED'));
		} else {
			$msg = JText::_('SAVE_DATA_SUCCESSFULLY');
		}
		$this->setRedirect('index.php?option=com_javoice&view=voicetypes',$msg);
	}
	
 function unpublish()
	{
		$model = $this->getModel('voicetypes');
		if(!$model->published(0))
		{
			JError::raiseWarning(1001, JText::_('ERROR_OCCURRED_DATA_NOT_SAVED'));
		} 
		else 
		{
			$msg = JText::_('SAVE_DATA_SUCCESSFULLY');
		}
		$this->setRedirect('index.php?option=com_javoice&view=voicetypes',$msg);
	}
 function select()
	{
		$model = $this->getModel('voicetypes');
		if(!$model ->selected(1))
		{
			JError::raiseWarning(1001, JText::_('ERROR_OCCURRED_DATA_NOT_SAVED'));
		} else {
			$msg = JText::_('SAVE_DATA_SUCCESSFULLY');
		}
		$this->setRedirect('index.php?option=com_javoice&view=voicetypes',$msg);
	}
	
 function unselect()
	{
		$model = $this->getModel('voicetypes');
		if(!$model->selected(0))
		{
			JError::raiseWarning(1001, JText::_('ERROR_OCCURRED_DATA_NOT_SAVED'));
		} 
		else 
		{
			$msg = JText::_('SAVE_DATA_SUCCESSFULLY');
		}
		$this->setRedirect('index.php?option=com_javoice&view=voicetypes',$msg);
	}	
 function remove(){
		$model = $this->getModel('voicetypes');
		$errors=$model->remove();
		if($errors){
			foreach ($errors as $error)
				JError::raiseWarning(1001,$error);	
		}else 
			$msg=JText::_("DELETE_DATA_SUCCESSFULLY");
		$this->setRedirect('index.php?option=com_javoice&view=voicetypes',$msg);
	}
	function changevoicetypebystatusid(){
		
		$model = JAVBModel::getInstance ( 'voicetypesstatus', 'javoiceModel' );
		$status_id=JRequest::getInt('status_id',0);
		$where='';
		if($status_id!=0)
			$where=" AND s.id=$status_id  ";
		
		$fiel=" DISTINCT t.id,t.title ";
		$orderby=" t.title DESC ";
		$voicetypes = $model->getItemsDyamic ($fiel,$where,$orderby );
		
		if ( $voicetypes){			
			$displayVoicetypes = JHTML::_ ( 'select.genericlist', $voicetypes, 'voice_types_id', 'class="inputbox" ', 'id', 'title', 0 );
			echo $displayVoicetypes;
		}		
	}	
}
?>