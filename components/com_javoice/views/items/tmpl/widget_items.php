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
?>
<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );
global $javconfig;
$link_target		= JRequest::getVar('link_target','_blank');
$view_all_button	= JRequest::getVar('view_all_button','yes');
$model_status =  JAVBModel::getInstance ( 'voicetypesstatus', 'javoiceModel' );
$Itemid = JAVoiceHelpers::get_Itemid(array('option'=>'com_javoice', 'view'=>'items'));
$list_status = $model_status->getListTreeStatus();
$sytem_comment = isset($javconfig['integrate'])?$javconfig['integrate']->get('run_system', 'intensedebate'):'intensedebate';
$helper = new JAVoiceHelpers ( );
$items = $this->items;
if($items){?>
<ol>	    		
	<?php foreach ($items as $item){?>
		<?php
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
		
		?>	
		<li class="jav-box-item <?php if(isset($item->votes)){?>selected<?php }?>" id="jav-box-item-<?php echo $item->id;?>">
			<?php if($javconfig ['systems']->get ( 'is_use_vote', 1 )):?>
			<!-- BADGE -->
			<div class="jav-badge">
				<div class="jav-item-points jav-big-number">
					<strong id="jav-total-votes-of-user-<?php echo $item->id?>" class="up">
						<?php echo $item->total_vote_up?>
					</strong>
					<?php if($item->total_vote_down>0 && $item->has_down){?>
					<strong id="jav-total-votes-of-user-down-<?php echo $item->id?>" class="down">
						-<?php echo $item->total_vote_down?>
					</strong>
					<?php }?> 
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
					<h2 class="jav-contentheading">
						<?php
						if(JRequest::getInt('view_detail',0)) $link = '#'; 
						else $link = JRoute::_('index.php?option=com_javoice&view=items&layout=item&cid='.$item->id.'&type='.$item->voice_types_id.'&amp;Itemid='.$Itemid);						
						?>														
						<a href="<?php echo $link?>" class="jav-item-title" target="<?php echo $link_target?>"><?php echo $item->title?></a>
						<?php								
						if ( $item->voice_type_status_id) { ?>
						
						<span class="jav-item-status">
							<span class="jav-tag" style="background: <?php echo $item->status_class_css?>"> <?php echo $item->status_title?> </span>
						</span>
						<?php }?>											 																																									
					</h2>
					<br/>
					<div class="jav-item-content clearfix">
						<?php 																				
							if(!$this->enable_bbcode)
								$item->content = str_replace("\n", '<br/>', $item->content);
						?>
						<?php if(JRequest::getVar('layout')=='item'){
								echo $item->content;
						?>														
						<?php }else{ 
							$maxcharswidget = $javconfig['systems']->get('maxcharswidget', 100);
							if($maxcharswidget==-1){
								echo $item->content;
							}
							elseif($maxcharswidget>0){
								if (function_exists ( 'mb_substr' )) {
									$doc = JDocument::getInstance ();
									echo SmartTrim::mb_trim($item->content, 0, $javconfig['systems']->get('maxcharswidget', 100), $doc->_charset);
								}else{
									echo SmartTrim::trim($item->content, 0, $javconfig['systems']->get('maxcharswidget', 100));
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
				
				<div class="jav-item-response">
					<?php if($javconfig["systems"]->get("is_private",0) == 0 || $item->is_private == 0 || $user->id == $item->user_id || JAVoiceHelpers::checkPermissionAdmin()){?>				
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
								<a class="user" href="<?php echo JRoute::_('index.php?option=com_javoice&view=users&uid='.$admin_response->user_id.'&amp;Itemid='.$Itemid)?>" target="<?php echo $link_target?>">
									<?php echo JFactory::getUser($admin_response->user_id)->username?>
								</a>								
							</span>
						<?php }?>							
					 </div>
					 <?php }?>
					 <?php if($this->is_attach_image){?>
					 <div class="jav-upload-form" style="display: none;">
					 	<?php echo $listFiles;?>
					 </div>
					 <?php }?>												
				</div>
					
				<div class="jav-item-bestanswer">
					<div class="jav-bestanswer-text" <?php if(!isset($best_answer->content) || !$best_answer->content){?> style="display: none;" <?php }?>>
						<?php if(isset($best_answer->content) && $best_answer->content!=''){?>
							<label><em><?php echo JText::_('BEST_ANSWER')?></em></label>
							<span><?php echo $best_answer->content?></span>							
						<?php }?>
					</div>																
				</div>
					
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
						//only show button count comment in list comment
						if($javconfig["systems"]->get("is_private",0) == 0 || $item->is_private == 0 || $user->id == $item->user_id || JAVoiceHelpers::checkPermissionAdmin()){
						if(JRequest::getVar("layout") != "item" && !JRequest::getVar("cid")){?>
						|
						 <?php $sytem_comment = $javconfig['integrate']->get('run_system', 'intensedebate');
						 	//if current system comment is jacomment
							if($sytem_comment == 'jacomment'){								
								$baseurl = (!empty($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'];
								if ($_SERVER['SERVER_PORT']!="80") 
									$baseurl .= ":".$_SERVER['SERVER_PORT'];
								$link	= $baseurl . $link;																												
						?>	
							<span class="jav-comment">
								<a href="<?php echo $link.'#jav-comment'?>" target="_parent" <?php if($totalComment>0){?> class="jav-has-comments" <?php }?>>								
								<?php echo JText::_($this->type->language_response)?>								
								<?php
									$text =  '{jacomment_count contentid='. $item->id .' option=com_javoice contenttitle='.$item->title.'}';
									if(JRequest::getVar("tmpl") == "component"){
										$dispatcher =& JDispatcher::getInstance();										
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
									
								}else if($sytem_comment == 'jacomment' && $this->jacomment){
									$totalComment = $item->data->get($sytem_comment.'_total');	
								}								
							?>
							<a href="<?php echo $link.'#jav-comment'?>" target="_parent" <?php if($totalComment>0){?> class="jav-has-comments" <?php }?> target="<?php echo $link_target?>">								
								<?php echo JText::_($this->type->language_response)?><?php echo ' ('.$totalComment.')';?>
							</a>
						</span>
						<?php }?>
						<?php }?>
						<?php }//end of check private?>											
					</div>
					
			</div>
			<!-- //ITEM DETAIL -->
		
		</li>
	<?php }?>
</ol>	
	<?php if($view_all_button=='yes' ||$view_all_button==''){?>
	<div style="padding-left:350px;">
		<a target="<?php echo $link_target?>" href="<?php echo JRoute::_("index.php?option=com_javoice&view=items&type={$this->type->id}&Itemid=$Itemid")?>"><?php echo JText::_("VIEW_ALL");?></a>
	</div>	
	<?php }?>
<?php }else{?>
	<?php echo JText::_('NO_ITEM')?>
<?php }?>