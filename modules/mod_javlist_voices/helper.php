<?php
/**
 *$JA#COPYRIGHT$
 */
 

// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
if(!defined('DS')){
	define('DS', DIRECTORY_SEPARATOR);
}
require_once JPATH_ADMINISTRATOR . '/components/com_javoice/models/model.php';
require_once (JPATH_SITE . '/components/com_javoice/helpers/route.php');
require_once (JPATH_SITE . '/components/com_javoice/models/items.php');
require_once (JPATH_SITE . '/components/com_javoice/models/voicetypes.php');
require_once (JPATH_SITE . '/components/com_javoice/models/voicetypesstatus.php');
require_once (JPATH_SITE . '/components/com_javoice/helpers/jahelper.php');

class modJAVLatestItemsHelper {
	
	/*
	 * 
	 */
	function getList(&$params) {
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO ();
		$user = JFactory::getUser ();
		
		$limit = intval ( $params->get ( 'numberitems', 5 ) );
		$displaymode = intval ( $params->get ( 'displaymode', 0 ) );
		
		$where_more = '';
		$order = '';
		if( !$displaymode )	$order = 'i.create_date desc';
		else $order = 'i.total_vote_up desc';
		
		/*$forums_id = 0;
		if($params->get ( 'forums', 0 )){
			$forums_id = $params->get ( 'forums', 0 );
			if(isset($forums_id) && is_array($forums_id))
				$forums_id = implode(',', $forums_id);
		}
		if($forums_id)*/
		$forums_id = $params->get('forums', '');
		$str_forums_id = array();
		if($forums_id  && is_array($forums_id)){
			foreach ($forums_id as $fid){
				if($fid==1){
					$str_forums_id = array();
					break;
				}
				$fid = explode('_', $fid);
				$str_forums_id[$fid[0]][] = $fid[1];
			}
		}
		
		if($str_forums_id){
			foreach ($str_forums_id as $k=>$row){
				if($row){
					$forums = implode(',', $row);
					$subwhere[] = " ( i.voice_types_id = '$k' and i.forums_id in (".$forums.") )";
				}
			}
			$where_more .= ' and '. implode(' or ', $subwhere);
		}
		
		
		/* BEGIN: Show items are activing Only */  				
		$model_status = new javoiceModelvoicetypesstatus();
        $list_status = $model_status->getListTreeStatus($params->get ( 'voicetypes', 5 ));   
		$status_ids = array(0);
		if($list_status){
	        foreach ($list_status as $k=>$status){
	        	if( ($status->parent_id!=0 && ( $status->allow_voting==1 ||  ( $status->allow_voting==-1 && $list_status[$status->parent_id]->allow_voting==1 ))) || JRequest::getWord('layout')=='item' ){
	        		$status_ids[] = $status->id;
	        	}
	        }        
		}       
		$where_more .= " and ( i.voice_type_status_id in (".implode(',', $status_ids)."))";
	
		$where_more .= ' and i.published=1';
		
		$join = " LEFT JOIN #__jav_voice_type_status as s ON s.id=i.voice_type_status_id";
		$fields_join = ' s.title as status_title, s.class_css as status_class_css, s.allow_voting as status_allow_voting, s.parent_id as status_parent_id';
		
		$model = new JAVoiceModelItems ();
		
		$items = $model->getItems($where_more, $order, 0, $limit, $fields_join, $join);		
		
		
		
		return $items;
	}
	
	/*
	 * 
	 */
	function parseItems(&$items, $types){
		global $javconfig;
		
		$db = JFactory::getDBO();				
			
        $array_votes = array();      
			
		$user = JFactory::getUser();
		require_once (JPATH_SITE . DS . 'components' . DS . 'com_javoice' . DS . 'models' . DS . 'forums.php');
		$model_forums = new JAVoiceModelforums();
		
		$array_votes = array();
		if($types){
			foreach ($types as $type){				
				$params_type = class_exists('JRegistry')? new JRegistry($type->vote_option) : new JParameter($type->vote_option);
				
				$array_votes[$type->id]['value'] = $params_type->get('votes_value')? str_replace("###", ',', $params_type->get('votes_value')):'';
			}
		}
		
		if($items){
			require_once (JPATH_SITE . DS . 'components' . DS . 'com_javoice' . DS . 'models' . DS . 'voicetypesstatus.php');
			
			$model_status = new javoiceModelvoicetypesstatus();
			foreach ($items as $k=>$item){				
				$items[$k]->has_down = 0;
				
				$check = true;
				/* Check status has closed */
				if($item->voice_type_status_id && $item->status_allow_voting==0){
					$check = false;
				}
				elseif($item->voice_type_status_id && $item->status_allow_voting==-1){					
					$parent_status = $model_status->getItems(" and s.id='{$item->status_parent_id}' and s.published=1", '', 0, 1);
					if($parent_status) $parent_status = $parent_status[0];
					if($parent_status && $parent_status->allow_voting==0){
						$check = false;
					}
				}
				
				if($check){
					$user_can_post = $user_can_view = 0;
					
					$layout = JRequest::getVar ( 'layout', '' );
					$forum = $model_forums->getItems(' and f.id='.$item->forums_id);
					
					if($layout == "add" || $layout == "form"){
						$forum = $forum[1];
					}
					else {
						$forum = $forum[0];
					}
					$forum->gids_post = str_replace("\n\n", "\n", $forum->gids_post);
					$forum->gids_vote = str_replace("\n\n", "\n", $forum->gids_post);
					$lits_user_can_posts = explode("\n", $forum->gids_post);
					$lits_user_can_views = explode("\n", $forum->gids_view);
					
					if (!$user->id)
						$user->groups = array_merge(array('Guest'=>'0'), $user->groups);
				    foreach(array_values($user->groups) as $ugid){
					   if(in_array($ugid, $lits_user_can_posts)){ $user_can_post = 1;}
					   if(in_array($ugid, $lits_user_can_views)){ $user_can_view = 1;}
					} 
					
					if(!$user_can_view){
						unset($items[$k]);
					}
					else{
						if($user_can_post && isset($array_votes[$item->voice_types_id]['value'])){							
							$values = explode(',', $array_votes[$item->voice_types_id]['value']);
							
							foreach ($values as $value){
								if(intval($value)<0){
									$items[$k]->has_down = 1;
									break;
								}								
							}
							
						}						
					}
				}
											
			}
		}
	}
}
