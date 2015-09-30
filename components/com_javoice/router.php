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


require_once dirname(__FILE__).'/helpers/route.php';

function JavoiceBuildRoute(&$query)
{
	$app = JFactory::getApplication();
	$helper = new JavoiceHelperRoute();
	
	$segments = array();
   // print_r($query);
	// get a menu item based on Itemid or currently active
	$menu = $app->getMenu();
	if (empty($query['Itemid'])) {
		$menuItem = $menu->getActive();
	} else {
		$menuItem = $menu->getItem($query['Itemid']);
	}
		
	$mView	= (empty($menuItem->query['view'])) ? 'items' : $menuItem->query['view'];	
	$view = isset($query['view'])?$query['view']:$mView;
	if($view!=$mView){
		$segments[] = $view;
	}
	
	if(isset($query['view'])){		
		unset($query['view']);
	}

	if($view=='items'){		
		if(isset($query['type']) && !empty($query['type'])){
			$segments[] = $helper->getAlias($query['type']);
			unset($query['type']);
		}
		
		if(isset($query['forums']) && !empty($query['forums'])){
			$segments[] = $helper->getAlias($query['forums'], 'forums');
			unset($query['forums']);
		}
			
		if(isset($query['status']) && !empty($query['status'])){
			$segments[] = $helper->getAlias($query['status'], 'voice_type_status');
			unset($query['status']);
		}
		
		if(isset($query['order']) && $query['order']=='create_date desc'){
			$segments[] = 'latest';
			unset($query['order']);
		}
		
		if(isset($query['cid']) && intval($query['cid'])){
			$segments[] = (int)$query['cid'].':'.$helper->getItemAlias($query['cid']);
			unset($query['cid']);
		}			
	}
	elseif($view=='users'){
		if(isset($query['uid']) && intval($query['uid'])){
			$segments[] = JFactory::getUser((int)$query['uid'])->username;
			unset($query['uid']);
		}
	}
	elseif ($view=='feeds'){
		if(isset($query['layout'])){
			$segments[] = $query['layout'];
			unset($query['layout']);	
		}	
		if(isset($query['alias'])){
			$segments[]=$query['alias'];
			unset($query['alias']);
		}
	}
	if(isset($query['layout']) && in_array($query['layout'], array('default','item'))){
		unset($query['layout']);
	}
	elseif(isset($query['layout'])){
		$segments[] = $query['layout'];
		unset($query['layout']);
	}
	return $segments;
}

function JavoiceParseRoute($segments){ 
	$vars = array();   		
	$helper = new JavoiceHelperRoute();
	$app = JFactory::getApplication();
	//Get the active menu item
	$menu = $app->getMenu();
	$item = $menu->getActive();
    
	// Count route segments
	$count = count($segments);
	//print_r($segments);exit;
	if(!isset($item)){
		$vars['view']  = $segments[0];
		if(!is_dir(JPATH_SITE.'/components/com_javoice/views/'.$vars['view']))
		{	
			$vars['view'] = 'items';
			$type_id = $helper->getTypeId($segments[0]);
			$vars['type'] = $type_id;
				
			if($count==2){			
				$temp = explode(':', $segments[$count-1]);
				if(intval(@$temp[0])){ //Item detail
					$vars['cid'] = (int)$temp[0];
					$vars['layout'] = 'item';
				}
				else{// Forums or Status
					if($forums_id=$helper->getForumsId($temp[0], $type_id)){
						$vars['forums'] = $forums_id;
					}
					elseif($status_id=$helper->getStatusId($temp[0], $type_id)){
						$vars['status'] = $status_id;
					}
					elseif($segments[1]=='latest'){
						$vars['order'] = 'create_date desc';
					}
					else{
						$vars['layout'] = $segments[$count-1];
					}
				}
			}
			elseif($count==3){				
				if($forums_id=$helper->getForumsId($segments[$count-2], $type_id)){
					$vars['forums'] = $forums_id;
				}
				if($status_id=$helper->getStatusId($segments[$count-1], $type_id)){
					$vars['status'] = $status_id;
				}
				elseif($segments[$count-1]=='latest'){
					$vars['order'] = 'create_date desc';
				}
				else{
					$temp = explode(':', $segments[$count-1]);
					if(intval(@$temp[0])){ //Item detail
						$vars['cid'] = (int)$temp[0];
						$vars['layout'] = 'item';
					}
				}
			}
			elseif($count==4){
				$vars['view'] = $segments[$count-4];
				$type_id = $helper->getTypeId($segments[$count-3]);
				$vars['type'] = $type_id;
				if($forums_id=$helper->getForumsId($segments[$count-2], $type_id)){
					$vars['forums'] = $forums_id;
				}
				if($status_id=$helper->getStatusId($segments[$count-1], $type_id)){
					$vars['status'] = $status_id;
				}
				elseif($segments[$count-1]=='latest'){
					$vars['order'] = 'create_date desc';
				}
				else{
					$temp = explode(':', $segments[$count-1]);
					if(intval(@$temp[0])){ //Item detail
						$vars['cid'] = (int)$temp[0];
						$vars['layout'] = 'item';
					}
				}
			}
			return $vars;
		}
		
		if($vars['view']=='items'){
			
			$type_id = $helper->getTypeId($segments[1]);
			$vars['type'] = $type_id;
			
			if($count==3){
				$temp = explode(':', $segments[$count-1]);
				if(intval(@$temp[0])){ //Item detail
					$vars['cid'] = (int)$temp[0];
					$vars['layout'] = 'item';
				}
				else{// Forums or Status
					if($forums_id=$helper->getForumsId($temp[0], $type_id)){
						$vars['forums'] = $forums_id;
					}
					elseif($status_id=$helper->getStatusId($temp[0], $type_id)){
						$vars['status'] = $status_id;
					}
					elseif($segments[2]=='latest'){
						$vars['order'] = 'create_date desc';
					}
					else{
						$vars['layout'] = $segments[$count-1];
					}
				}
			}
			elseif($count==4){
				if($forums_id=$helper->getForumsId($segments[2], $type_id)){
					$vars['forums'] = $forums_id;
				}
				if($status_id=$helper->getStatusId($segments[3], $type_id)){
					$vars['status'] = $status_id;
				}
				elseif($segments[3]=='latest'){
					$vars['order'] = 'create_date desc';
				}
				else{
					$temp = explode(':', $segments[$count-1]);
					if(intval(@$temp[0])){ //Item detail
						$vars['cid'] = (int)$temp[0];
						$vars['layout'] = 'item';
					}
				}
			}
			
		}
		elseif($vars['view']=='users'){
			$vars['view'] = 'users';
			$temp = explode(':', $segments[1]);
			if(intval(@$temp[0])){ //Item detail
				$vars['uid'] = (int)$temp[0];
			}
			else{
				$vars['uid'] = $helper->getUserId($segments[1]);
			}
			
		}elseif ($vars['view']=='feeds'){
			$vars['view'] = 'feeds';
			if(isset($segments[1]))
				$vars['layout'] = $segments[1];
			if(isset($segments[2]))
				$vars['alias'] =str_replace(":","-",$segments[2]);				
		}
		//print_r($vars);exit;		
		return $vars;
	}	
	if($item->query['view']=='items' && $segments[0]!='users'&& $segments[0]!='feeds'){				
		$type_id = $helper->getTypeId($segments[0]);
		$vars['type'] = $type_id;
		
		if($count==2){
			//$temp = split(':', $segments[1]);
			$temp = explode(':', $segments[1]);
			if(intval(@$temp[0])){ //Item detail
				$vars['cid'] = (int)$temp[0];
				$vars['layout'] = 'item';
			}
			else{// Forums or Status
				if($forums_id=$helper->getForumsId($segments[$count-1], $type_id)){
					$vars['forums'] = $forums_id;
				}
				elseif($status_id=$helper->getStatusId($segments[$count-1], $type_id)){
					$vars['status'] = $status_id;
				}
				elseif($segments[1]=='latest'){
					$vars['order'] = 'create_date desc';
				}
				else{
					$vars['layout'] = $segments[$count-1];
				}
			}
		}
		elseif($count==3){
			$temp = explode(':', $segments[$count-1]);
			if(intval(@$temp[0])){ //Item detail
				$vars['cid'] = (int)$temp[0];
				$vars['layout'] = 'item';
			}
			if($forums_id=$helper->getForumsId($segments[1], $type_id)){
				$vars['forums'] = $forums_id;
			}
			if($status_id=$helper->getStatusId($segments[2], $type_id)){
				$vars['status'] = $status_id;
			}
			elseif($segments[2]=='latest'){
				$vars['order'] = 'create_date desc';
			}
			elseif(!intval(@$temp[0])){
				$vars['layout'] = $segments[$count-1];
			}
		}		
	}
	elseif(isset($item->query['view']) && $item->query['view']=='users' || $segments[0]=='users'){
		$vars['view'] = 'users';
		if(isset($segments[1])){
			$temp = explode(':', $segments[1]);
			if(intval(@$temp[0])){ //Item detail
				$vars['uid'] = (int)$temp[0];
			}
			else{
				$vars['uid'] = (int)$helper->getUserId($segments[1]);
			}
		}
		if(!isset($vars['uid'])) $vars['uid'] = 999999999999;
	}elseif(isset($item->query['view']) && $item->query['view']=='feeds' || $segments[0]=='feeds'){
		$vars['view'] = 'feeds';
		if(isset($segments[1]))$vars['layout'] = $segments[1];
		if(isset($segments[2])) {
			$segments[2] =str_replace(":","-",$segments[2]);
			$vars['alias'] = $segments[2];
		}
	}
	return $vars;	
}