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
defined( '_JEXEC' ) or die( 'Restricted access' );
global $javconfig;
$Itemid = JRequest::getInt('Itemid');
if(!$Itemid) $Itemid = JAVoiceHelpers::get_Itemid(array('option'=>'com_javoice', 'view'=>'items'));

$items = $this->items;
$user = JFactory::getUser();
$model_status = JAVBModel::getInstance ( 'voicetypesstatus', 'javoiceModel' );
$list_status = $model_status->getListTreeStatus();
$sytem_comment = isset($javconfig['integrate'])?$javconfig['integrate']->get('run_system', 'intensedebate'):'intensedebate';
$helper = new JAVoiceHelpers ( );
$isSepecialUse = JAVoiceHelpers::checkPermissionAdmin();
if ($sytem_comment == 'jcomments') {
	$jcomment = JAVoiceHelpers::checkComponent ( 'com_jcomments' );
	$this->jcomments = $jcomment;
}
elseif ($sytem_comment == 'jacomment') {
	$jacomment = JAVoiceHelpers::checkComponent ( 'com_jacomment' );
	$this->jacomment = $jacomment;
}
if($items){?>
<?php if(isset($this->show_suggest) && $this->show_suggest){?>
<?php if(JRequest::getVar("pagingtype","") != "autoscroll"):?>
<h1><?php echo JText::_('ALREADY_EXISTS_SOME_IDEAS_SIMILAR_TO_YOUR_IDEAS')?></h1>
<?php endif;?>
<?php }?>
<?php if($javconfig["systems"]->get("paging_type","normal") == "autoscroll" && JRequest::getVar("pagingtype","") != "autoscroll"){?>
	<input type="hidden" id="jav_nexpage_<?php echo $this->type_id;?>" value="1_<?php echo $this->total_page;?>"/>	
<?php }?>
<?php if(JRequest::getVar("pagingtype","") != "autoscroll"):?>					
<ol>	    		
<?php endif;?>
	<?php $number_z_index = count($items)+1;?>
	<?php
	foreach ($items as $item){
	
		$itemCanShow = ($javconfig["systems"]->get("is_private",0) == 0) || ($item->is_private == 0) || ($user->id == $item->user_id) || $isSepecialUse;
		
		$listFiles = "";
		if( !$user->id && isset( $_COOKIE[md5( 'jav-view-item' ) . $item->id] ) ){
			$item->votes =  $_COOKIE[md5( 'jav-view-item' ) . $item->id];
		}
		$item->data = class_exists('JRegistry')? new JRegistry($item->data) : new JParameter($item->data);
		$best_answer = $admin_response = array();
		$admin_responses = $this->getModel()->getAdmin_responses(" and r.item_id='{$item->id}'", 0, 2);
		$admin_avatar = '';
		if($admin_responses){
			foreach ($admin_responses as $row) {
				if($row->type=='admin_response') {
					$admin_response = $row;
					$admin_avatar = $helper->getAvatar($row->user_id);
					break;
				}
				
			}
			
			
		}
		
		if($this->type->has_answer){
			foreach ($admin_responses as $row){
				if($row->type=='best_answer'){
					$best_answer = $row;
					break;
				}
				
			}	
		}
		
		if ($itemCanShow) {
		?>
		<li class="jav-box-item <?php if(isset($item->votes)){?>selected<?php }?>" style="z-index:<?php echo $number_z_index--;?>" id="jav-box-item-<?php echo $item->id;?>">
			<?php if($javconfig ['systems']->get ( 'is_use_vote', 1 )):?>
			<!-- BADGE -->            
			<div class="jav-badge">				
				<div class="jav-item-points jav-big-number">
					<strong id="jav-total-votes-of-user-<?php echo $item->id?>" class="up">
						<?php echo $item->total_vote_up?>
					</strong>
					<?php if($item->has_down){?>
					<strong id="jav-total-votes-of-user-down-<?php echo $item->id?>" class="down">
						<?php if($item->total_vote_down>0){?>-<?php echo $item->total_vote_down?><?php }?>
					</strong>
					<?php }?>  					
				</div>
			
				<div class="jav-moderation">
					<?php if (!$item->list_vote_text){ $item->list_vote_text = "''";};?>
					<?php if(!$item->list_vote_description) $item->list_vote_description = "''";?>
					<?php $onclick = "jav_showVoteOption('#jav-item-vote-{$item->id}', '{$item->list_vote_value}', {$item->list_vote_text}, {$item->list_vote_description}, {$item->list_vote_msg}, '{$item->id}', '{$item->voice_types_id}', '','', '$Itemid')";?>					
					<?php if(isset($item->votes)){?>
						<a class="votes value-<?php echo $item->votes?>" id="jav-item-votes-<?php echo $item->id?>" href="javascript:void(0)" onclick="<?php echo $onclick;?>">
							<?php echo $item->votes?>
						</a>
					<?php } else {?>
						<a class="teaser" href="javascript:void(0)" id="jav-item-votes-<?php echo $item->id?>" onclick="<?php echo $onclick;?>">
							<?php echo JText::_('VOTE')?>
						</a>
					<?php } ?>
					
					<div class="pop-in has-layout" id="jav-item-vote-<?php echo $item->id?>" style="display: none;"></div>
				</div>
			</div>
			<!-- //BADGE -->
			<?php endif; ?>	
			<!-- ITEM DETAIL -->
			<div class="jav-item-details clearfix<?php if(!$javconfig ['systems']->get ( 'is_use_vote', 1 )):?> jav-detail-no-vote<?php endif;?>">
				<?php if($item->avatar[0]){?>
				<img class="jav-avatar" src="<?php echo $item->avatar[0];?>" style="<?php echo $item->avatar[1]?>"/>
				<?php }?>
				<div class="jav-item-details-content">
					
						<?php
						if(JRequest::getInt('view_detail',0)) $link = '#'; 
						else $link = JRoute::_('index.php?option=com_javoice&view=items&layout=item&cid='.$item->id.'&type='.$item->voice_types_id.'&amp;Itemid='.$Itemid);						
						?>		
						 
						<a href="<?php echo $link?>" class="jav-item-title"><?php echo $item->title?></a>												
						
						
						<?php if( $isSepecialUse){?>
						
						<span class="jav-item-status">
							<span class="jav-status-title">
								<?php if(!$item->voice_type_status_id){ ?>
									<a onclick="jav_show_all_status(<?php echo $item->id?>, <?php echo $item->voice_types_id?>); return false;" href="#" class="jav-tag inline-edit">
										<?php echo JText::_('SET_STATUS__CLOSE')?>
									</a>
								<?php } else { ?>
									<a style="background: <?php echo $item->status_class_css?>" onclick="jav_show_all_status(<?php echo $item->id?>); return false;" href="#" class="jav-tag inline-edit">
										<?php echo $item->status_title?>
									</a>
								<?php } ?>
							</span>																				
							<div style="display: none;" class="statuses layer">
								<dl>								  
									<?php echo $model_status->builtTreeStatus( $list_status, $item->voice_type_status_id, $item->id );?>						  
								</dl>
							</div>
						</span>
						
						<?php								
						} elseif ( $item->voice_type_status_id) { ?>
						
						<span class="jav-item-status">
							<span class="jav-tag" style="background: <?php echo $item->status_class_css?>"> <?php echo $item->status_title?> </span>
						</span>
	
						<?php }?>
						
					
					<br/>
					<div class="jav-item-content clearfix">
						<?php 																				
							if(!$javconfig['plugin']->get('enable_bbcode',1))
								$item->content = str_replace("\n", '<br/>', $item->content);
						?>
						<?php if(JRequest::getVar('layout')=='item'){
								echo $item->content;
						?>														
						<?php }else{ 
							$maxchars = $javconfig['systems']->get('maxchars', 100);
							if($maxchars==-1){
								echo $item->content;
							}
							elseif($maxchars>0){
								if (function_exists ( 'mb_substr' )) {
									$doc = JDocument::getInstance ();
									echo SmartTrim::mb_trim($item->content, 0, $javconfig['systems']->get('maxchars', 100), $doc->_charset);
								}else{
									echo SmartTrim::trim($item->content, 0, $javconfig['systems']->get('maxchars', 100));
								}
							}
							
						}
						?>						
					</div>
					<?php if(JRequest::getVar('layout')=='item'){?>
						<div id ='jav_file_upload_<?php echo $item->id;?>'>						
							<?php
							if($item->attachs){
								foreach ($item->attachs as $attach){
									echo $attach;
								}				
							}	
							?>
						</div>	
					<?php }?>
				</div>
				<?php					
					$isAllowRegisterEdit = 0;
					//print_r($javconfig["systems"]->get("is_edit_delete_voice",1));die();
					if($javconfig["systems"]->get("is_edit_delete_voice",0)){						
						$userEId = $user->get( 'id' );
						$timeE = $javconfig["systems"]->get("time_for_edit_voice", 900);
						
						if($userEId && ($userEId == $item->user_id)){																								
							if($timeE != -1 || time() <= ($item->create_date_store+$timeE)){
								$isAllowRegisterEdit = 1;
							}
						}	
					}
				?>
				<div class="jav-item-response">
                	<?php if($itemCanShow){?>                    	                    
					<div class="jav-response-text" <?php if(!isset($admin_response->content) || !$admin_response->content){?> style="display: none;" <?php }?>>
						<?php if(isset($admin_response->content) && $admin_response->content!=''){
							//if($this->is_attach_image){
								$file_path   =  JPATH_ROOT.DS."images".DS."stories".DS."ja_voice".DS."admin_response".DS.$admin_response->id;
								$listFiles 	 =  $helper-> preloadfile($admin_response->id, "response");
								$attachFiles =  $this->getModel()->formatFilesInDir($file_path,'download',$admin_response->user_id,$admin_response->id);
							//}
						?>	
							<div class="jav-author">
								<?php if($admin_avatar){?>
								<img class="jav-avatar" src="<?php echo $admin_avatar[0];?>" style="<?php echo $admin_avatar[1]?>">
								<?php }?>
								<?php echo JText::_('ADMIN_RESPONSE')?>
							</div>
							<?php echo html_entity_decode($helper->showItem($admin_response->content));?>
							<?php if($attachFiles){?>
								<div class="jav-response-upload"><?php if($attachFiles){echo $attachFiles;}?></div>
							<?php }?>
							<span class="editable">
								<a class="user" href="<?php echo JRoute::_('index.php?option=com_javoice&view=users&uid='.$admin_response->user_id.'&amp;Itemid='.$Itemid)?>">
									<?php echo JFactory::getUser($admin_response->user_id)->username?>
								</a>								
								<?php if( $isSepecialUse || $isAllowRegisterEdit){?>
								 | 
								<a id="link-response-<?php echo $item->id?>" onclick="return show_frm_response('#link-response-<?php echo $item->id?>', <?php echo $item->id?>, <?php echo $admin_response->id?>)" href="javascript:void(0)" class="edit-link">
									<?php echo JText::_('EDIT')?>
								</a>
								 | 
								<a id="link-delete-response-<?php echo $item->id?>" onclick="return delete_reply_voice('#link-delete-response-<?php echo $item->id?>', <?php echo $item->id?>, <?php echo $admin_response->id?>, '<?php echo JText::_("DO_YOU_WANT_TO_DELETE_THIS_REPLY");?>')" href="javascript:void(0)" class="edit-link" title="<?php echo JText::_("DELETE_A_REPLY");?>">
									<?php echo JText::_('DELETE')?>
								</a>
								<?php }?>
							</span>
						<?php }?>							
					 </div>
					 <?php }?>
					 <?php if(isset($this->is_attach_image)){?>
					 <div class="jav-upload-form" style="display: none;">
					 	<?php echo $listFiles;?>
					 </div>
					 <?php }?>							
					<?php if( $isSepecialUse){?>
						  <?php if(!isset($admin_response->content) || !$admin_response->content){?>
						      <span class="add-response" id ='jav-add-respone-<?php echo $item->id?>'>
						      		<a id="link-response-<?php echo $item->id?>" onclick="return show_frm_response('#link-response-<?php echo $item->id?>', <?php echo $item->id?>)" href="javascript:void(0)" class="inline-edit-prompt">
						      			<?php echo JText::_('ADD_RESPONSE')?>
						      		</a>
						      </span>					    
					      <?php }?>
					      <input type="hidden" id="jav-content-respone-<?php echo $item->id;?>" value="<?php echo isset($admin_response->content)?$admin_response->content:'';?>"/>
					      							      						      				
						  <div id="jav-container-response-<?php echo $item->id?>" style="display:none;"></div>																	
						  
						 <div id="jav-form-response-<?php echo $item->id;?>" style="display: none;">	  
							  <input type="button" onclick="jav_submit_admin_response(<?php echo $item->id?>, <?php if( !@$admin_response->content) echo 0; else echo $admin_response->id;?>);" value="<?php echo JText::_('SAVE')?>" name="commit" id="adminresponse-commit-<?php echo $item->id?>"/>
							  <input type="button" value="<?php echo JText::_('CANCEL')?>" name="cancel" id="jav-button-adminresponse-cancel-<?php echo $item->id;?>" onclick="hide_frm_response('#link-response-<?php echo $item->id?>', <?php echo $item->id?>, <?php if( !@$admin_response->content) echo 0; else echo 1;?>)"/> 
					      </div>
					<?php } ?>
				</div>
					
				<div class="jav-item-bestanswer">
					<div class="jav-bestanswer-text comment-text" <?php if(!isset($best_answer->content) || !$best_answer->content){?> style="display: none;" <?php }?>>
						<?php if(isset($best_answer->content) && $best_answer->content!=''){?>
							<label><em><?php echo JText::_('BEST_ANSWER')?></em></label>
							<span><?php echo html_entity_decode($helper->showItem($best_answer->content));?></span>
							
							<?php if( $isSepecialUse){?>
								<span class="editable">
								<a id="link-bestanswer-<?php echo $item->id?>" onclick="return show_frm_bestanswer('#link-bestanswer-<?php echo $item->id?>', <?php echo $item->id?>, <?php echo $best_answer->id?>)" href="javascript:void(0)" class="edit-link">
									<?php echo JText::_('EDIT')?>
								</a>
								</span>
							<?php }?>
							
						<?php }?>
						<input type="hidden" id="jav-content-bestanswer-<?php echo $item->id;?>" value="<?php echo htmlentities(nl2br(@$best_answer->content));?>" />
					</div>
					
					<div id="jav-container-bestanswer-<?php echo $item->id?>" style="display:none;"></div>																	
						  
                    <div id="jav-form-bestanswer-<?php echo $item->id;?>" style="display: none;">	  
                      <input type="button" onclick="jav_submit_bestanswer(<?php echo $item->id?>, <?php if( !@$best_answer->content) echo 0; else echo $best_answer->id;?>);" value="<?php echo JText::_('SAVE')?>" name="commit" id="bestanswer-commit-<?php echo $item->id?>"/>
                      <input type="button" value="<?php echo JText::_('CANCEL')?>" name="cancel" id="jav-button-bestanswer-cancel-<?php echo $item->id;?>" onclick="hide_frm_bestanswer('#link-bestanswer-<?php echo $item->id?>', <?php echo $item->id?>, <?php if( !@$best_answer->content) echo 0; else echo 1;?>)"/> 
                    </div>									
				</div>
					
					<?php 
					if($javconfig["systems"]->get("is_enable_tagging", 0) && JRequest::getVar('layout')=='item'){
						$modelTags 	= JAVBModel::getInstance ( 'tags', 'javoiceModel' );							
						$listTags 	= $modelTags->getTagByVoice($item->id);						
						$spaterTags = $javconfig["systems"]->get("characters_separating_tags", ",");
					?>
                    <div id="jav_item_tags" class="clearfix">
					<?php if($listTags):?>
						<b><?php echo JText::_("TAG_LIST");?>:</b>                        
						<?php foreach ($listTags as $itags => $tagItem):?>                 
							<a href="<?php echo JRoute::_('index.php?option=com_javoice&amp;view=items&amp;layout=item&amp;tagid='.$tagItem->id.'&amp;Itemid='.$Itemid);?>"><?php echo $tagItem->name;?></a><?php if($itags != (count($listTags)-1)){echo $spaterTags;}?>
						<?php endforeach;?>							
					<?php endif;?>	
                    </div>
					<?php }?>
					
					<div class="jav-article-meta">
						<?php
						if(JRequest::getInt('view_detail',0)) $link = ''; 
						else $link = JRoute::_('index.php?option=com_javoice&amp;view=items&amp;layout=item&amp;cid='.$item->id.'&amp;type='.$item->voice_types_id.'&amp;Itemid='.$Itemid);
						?>
						<span class="jav-createdby"><small><?php echo JText::_('BY')?></small> 
							<?php if(!($javconfig['systems']->get('use_anonymous', 0) && $item->use_anonymous)):?>
                            	<?php if($item->user_id>0 && $javconfig['plugin']->get('displayname', 'username') != "anonymous"){?>
								<?php if($javconfig['systems']->get('use_anonymous', 0) && $item->use_anonymous):?>
                                <?php echo JText::_("ANONYMOUS");?>                                                
                                <?php else:?>	
                            	<a	href="<?php echo JRoute::_('index.php?option=com_javoice&view=users&uid='.$item->user_id.'&amp;Itemid='.$Itemid)?>" class="user"><?php echo $item->create_by?></a>                                
                                <?php endif;?>                                
								<?php }else{?>
                                    <?php echo $item->create_by?>
                                <?php }?>
                            <?php else:?>
                            	<?php echo JText::_("ANONYMOUS");?>
                            <?php endif;?>                            							
						</span>	 
								
						<?php if(@$javconfig['systems']->get('show_date', 1)){?>
							 <span class="created-at">
								<?php if($item->list_vote_msg!='' && $item->list_vote_value==''){?>
									<?php echo JText::_('CLOSED_ON')?> <?php echo $item->update_date?>
								<?php }else{?>
									<?php echo JText::_('ON')?>
									<?php if($item->update_date!=$item->create_date){?>
										 <?php echo $item->update_date;?>
									<?php }else{?>
										 <?php echo $item->create_date;?>
									<?php }?>
								<?php }?>
							</span>
						<?php }?>
						<?php
						if($javconfig["systems"]->get("is_private",0) == 0 || $item->is_private == 0 || $user->id == $item->user_id || $isSepecialUse){ 
						//only show button count comment in list comment
						if(JRequest::getVar("layout") != "item" && !JRequest::getVar("cid")){?>
						|
						 <?php $sytem_comment = $javconfig['integrate']->get('run_system', 'intensedebate');
						 	//if current system comment is jacomment
							if($sytem_comment == 'jacomment' && $this->jacomment){								
								$baseurl = (!empty($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'];
								if ($_SERVER['SERVER_PORT']!="80") 
									$baseurl .= ":".$_SERVER['SERVER_PORT'];
								$link	= $baseurl . $link;																							
						?>	
							<span class="jav-comment">
								<a href="<?php echo $link.'#jav-comment'?>" <?php if(isset($totalComment) && $totalComment>0){?> class="jav-has-comments" <?php }?>>								
								<?php echo JText::_($this->type->language_response)?>								
								<?php
									$text =  '{jacomment_count contentid='. $item->id .' option=com_javoice contenttitle='.$item->title.'}';
									if(JRequest::getVar("tmpl") == "component"){
										$dispatcher = JDispatcher::getInstance();										
										JPluginHelper::importPlugin( 'plgSystemJAComment' );
										$string = $dispatcher->trigger( 'onAfterRoute');
										$string = $dispatcher->trigger( 'replaceCommentCode', array( $text) );
										if(isset($string[0])){
											echo $string[0];
										}else{
											echo "0";
										}	
									}else{
										echo $text;
									}																		
								?>
								</a>								
							</span>			
						<?php }else{?>
						 <span class="jav-comment">
							<?php 
								$totalComment = 0;	
				
								if($sytem_comment == 'jcomments' && $this->jcomments){		
									$comments = JPATH_SITE . '/components/com_jcomments/jcomments.php';									
									if (file_exists($comments)) {
									  require_once($comments);									  
									  $totalComment = JComments::getCommentsCount($item->id, 'com_javoice');
									}
									
								}else if($sytem_comment == 'jomcomment' && $this->jomcomment){									
									require_once ( JPATH_BASE .DS.'components'.DS.'com_jomcomment'.DS.'helper'.DS.'minimal.helper.php' );
									$totalComment = jcCountComment($item->id, 'com_javoice');
								}else if($sytem_comment == 'jacomment' && $this->jacomment){
								
									$totalComment = $item->data->get($sytem_comment.'_total');	
								}								
							?>
							<?php if($sytem_comment == 'intensedebate'):?>
								<a href="<?php echo $link.'#jav-comment'?>">								
									<?php echo JText::_($this->type->language_response)?>
								</a>
							<?php else:?>
								<a href="<?php echo $link.'#jav-comment'?>" <?php if($totalComment>0){?> class="jav-has-comments" <?php }?>>								
									<?php echo JText::_($this->type->language_response)?><?php echo ' ('.$totalComment.')';?>
								</a>
							<?php endif;?>

						</span>
						<?php }?>
						<?php }?>
						<?php }//end of check private?>
						<?php if( $isSepecialUse || $isAllowRegisterEdit){?>													
							<?php	$link = JRoute::_('index.php?option=com_javoice&view=items&layout=item&cid='.$item->id.'&amp;Itemid='.$Itemid);?>
							  
							 | <span class="jav-edit" ><a href="javascript:void(0)" onclick="jaCreatForm('&amp;layout=edit&amp;type=<?php echo $this->type_id?>','<?php echo $item->id?>', 700, 350,'<?php echo JText::_('EDIT')." ".$this->type->title;?>');get_check_atd('1');"><?php echo JText::_('EDIT')?></a></span>
							 | <span class="jav-delete" ><a href="javascript:void(0)" onclick="if(confirm('<?php echo JText::_('ARE_YOU_SURE_TO_DELETE_THIS_ITEM')?>')) jav_delete_item(<?php echo $item->id?>, <?php echo $item->voice_types_id?>)"><?php echo JText::_('DELETE')?></a></span>						
						<?php } elseif($item->list_vote_msg=="''" || !$item->list_vote_msg){?>
							<span class="jav-flags" id="flags-<?php echo $item->id?>" style="float: right;">
								<?php $link = JURI::base().'index.php?option=com_javoice&amp;view=items&amp;task=spam&amp;cid='.$item->id.'&amp;tmpl=component&amp;type='.$item->voice_types_id.'&amp;Itemid='.$Itemid;?>
								 <a href="javascript:void(0)" onclick="jav_ajax_load('<?php echo $link?>', '<?php echo $item->voice_types_id?>'); "><?php echo JText::_('REPORT_SPAM')?></a>
								
							</span>
						<?php }?>
					</div>
					
			</div>
			<!-- //ITEM DETAIL -->
		
		</li>
	<?php
		}
	}
	?>
<?php if(JRequest::getVar("pagingtype","") != "autoscroll"):?>
</ol>	
<?php endif;?>
<?php }elseif(!isset($this->show_suggest) || !$this->show_suggest){?>
<li id="lasted_load">
	<?php echo JText::_('NO_ITEM')?>
	<?php 
		if(!$user->id) echo JText::_("PLEASE_LOGIN_TO_VIEW_MORE");
	?>
</li>	
<?php }?>