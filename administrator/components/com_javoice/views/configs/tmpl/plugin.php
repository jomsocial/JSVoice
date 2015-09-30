<?php  
/*
# ------------------------------------------------------------------------
 * ------------------------------------------------------------------------
 * JA Voice Package for Joomla 2.5 & 3.x
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
*/
defined('_JEXEC') or die('Retricted Access');
 $mainframe = JFactory::getApplication();
 JHTML::_('behavior.tooltip');
 jimport('joomla.html.pane');
 $helper = new JAVoiceHelpers ( );
 $maxSizeUpload = (int)$helper->checkUploadSize();	
?>
<style type="text/css">
	table.admintable td {
		font-size:10px !important;
	}
	table.admintable td.key{
		font-weight:normal !important;
		text-align:left !important;
		font-size:1.091em !important;
	}
	table.admintable td.master{
		font-weight:bold !important;
	}
</style>
<script type="text/javascript">
maxSizeUpload = '<?php echo $maxSizeUpload; ?>';
jQuery(document).ready(function(){
    jQuery.each( ["avatar","addthis","addtoany","polldaddy","smileys","tweetmeme","comment_form","sorting_options","bbcode"], function(i, n){        
        jQuery("#enable_" + n).click(function () {            
            if(jQuery("#enable_" + n).is(':checked')){
                jQuery("#ja-block-" + n).show("");    
            }else{
                jQuery("#ja-block-" + n).hide("");    
            }
        });
    });   
});
</script>
<?php 
 $is_show_addthis = $this->params->get('is_show_addthis',0);
 $is_show_addtoany = $this->params->get('is_show_addtoany',0);
 $is_show_tweetmeme = $this->params->get('is_show_tweetmeme',0);
 $is_show_bookmark_share = $this->params->get('is_show_bookmark_share',0);
 $root = JURI::root();
 $selected = 'selected="selected"';
$checked = 'checked="checked"';

jimport('joomla.filesystem.folder');  
$smileyFolders = JPATH_SITE.'\components\com_javoice\asset\images\smileys\\';
$smileys = JFolder::folders($smileyFolders);
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">

<div class="col100">
	<fieldset class="adminform TopFieldset">
		<?php echo $this->getTabs();?>
	</fieldset>
	
	<div class="box" style="margin-top: 10px;">
        <h2><?php echo JText::_('LAYOUT_SETTINGS' ); ?></h2>	
        <ul class="ja-list-checkboxs">
        	<li class="row-1">
	      		<label for="displaypagination">
	      			<?php echo JText::_("NUMBER_DISPLAY_PAGINATION");?>
	      			<input type="text" style="width: 180px;" name="plugin[displaynumberpagination]" value="<?php echo $this->params->get('displaynumberpagination', 10);?>" />
	      		</label>		
	      	</li>	        	       
	      	<li class="row-0">
	      		<label for="displayname">
	      			<?php echo JText::_("DISPLAY_NAME");?>
	      			<?php $typeDisplayName = $this->params->get('displayname', "username");?>
	      			<select name="plugin[displayname]">
	      				<option value="name" <?php if($typeDisplayName == "name"){?>selected="selected"<?php }?>><?php echo JText::_("REAL_NAME");?></option>
	      				<option value="username" <?php if($typeDisplayName == "username"){?>selected="selected"<?php }?>><?php echo JText::_("USERNAME");?></option>
	      				<option value="anonymous" <?php if($typeDisplayName == "anonymous"){?>selected="selected"<?php }?>><?php echo JText::_("ANONYMOUS");?></option>
	      			</select>
	      		</label>		
	      	</li>	       
	        <li class="row-1">
		        <label for="enable_avatar">
			        <?php $EnableAvatar = $this->params->get('enable_avatar', 1);?>
			        <input type="checkbox" <?php if($EnableAvatar==1){ echo $checked; }?> value="1" name="plugin[enable_avatar]" id="enable_avatar"/> 
			        <?php echo JText::_("ENABLE_AVATAR");?>
		        </label>
		        <br />
		        <div id="ja-block-avatar" style="<?php if(!$EnableAvatar){?>display:none;<?php } ?> width:100%; position:relative">
		        	<?php				        		
		        		 $type_avatar = $this->params->get('type_avatar', 0);				        		 
		        	?>					       
			        <ul>					        	
			        	<select name="plugin[type_avatar]" id="type_avatar" multiple="multiple" size="4">						        		
			        		<option <?php if($type_avatar==0)echo $selected; ?> value="0"><?php echo JText::_("DEFAULT");?></option>
							<option <?php if($type_avatar==1)echo $selected; ?> value="1"><?php echo JText::_("COMMUNITY_BUILDER");?></option>
							<option <?php if($type_avatar==2)echo $selected; ?> value="2"><?php echo JText::_("FIREBOARD_KUNENA");?></option>
							<option  <?php if($type_avatar==4)echo $selected; ?> value="4"><?php echo JText::_("JOMSOCIAL");?></option>
							<option  <?php if($type_avatar==3)echo $selected; ?> value="3"><?php echo JText::_("GRAVATAR");?></option>
			        	</select>
			        	<br/>				        								        							        							        								       							      
			        </ul>
			        <!--<br>					       				      
			        <ul>		
			        	<label>
			        		<?php echo JText::_("PATH_TO_SMF_FORUM_IF_REQUIRED");?>
			        	</label>			        						        	
			        	<input type="text" style="width: 180px;" name="plugin[path_to_smf_forum]" value="<?php echo $this->params->get('path_to_smf_forum', '');?>">					        	
		        		<small><?php echo JText::_('FULL_PATH_TO_YOUR_SMF_FORUM_FOLDER')?></small>						        							        							        								       							        
			        </ul>-->
			        <br />						       		       
			        <?php $avatar_size = $this->params->get('avatar_size', 1);?>					        	
			        <ul class="ja-list-avatars clearfix" style="width: 100%; float: left;">
				        <li <?php if($avatar_size==1){?>class="active"<?php }?> id="ja-li-avatar-1">							        	
					        <label for="avatar_size_1" class="normal">
						        <img width="16" height="16" src="components/com_javoice/asset/images/settings/layout/avatar-large.png"/>
						        <span>
							        <input onclick="update_avatar_size_selection(1)" <?php if($avatar_size==1) echo $checked?> type="radio" value="1" id="avatar_size_1" name="plugin[avatar_size]"/> 
							        <?php echo JText::_('COMPACT')?>
						        </span>
					        </label>
					        
				        </li>
				        <li <?php if($avatar_size==2){?>class="active"<?php }?> id="ja-li-avatar-2">
					        <label for="avatar_size_2" class="normal">
						        <img width="24" height="24" src="components/com_javoice/asset/images/settings/layout/avatar-large.png" style="margin-top: 14px;"/>
						        <span>
							        <input onclick="update_avatar_size_selection(2)" <?php if($avatar_size==2) echo $checked?> type="radio" value="2" id="avatar_size_2" name="plugin[avatar_size]"/> 
							        <?php echo JText::_('NORMAL')?>
						        </span>
					        </label>
					        
				        </li>
				        <li <?php if($avatar_size==3){?>class="active"<?php }?> id="ja-li-avatar-3">
					        <label for="avatar_size_3" class="normal">
						        <img src="components/com_javoice/asset/images/settings/layout/avatar-large.png" style="margin-top: 6px;"/>
						        <span>
							        <input onclick="update_avatar_size_selection(3)" <?php if($avatar_size==3) echo $checked?> type="radio" value="3" id="avatar_size_3" name="plugin[avatar_size]"/> 
							        <?php echo JText::_('LARGE')?>
						        </span>
					        </label>								
				        </li>							        							      
			        </ul>
			        <br/>
			        <small><?php echo JText::_('SELECT_WHICH_AVATAR_TO_DISPLAY')?></small>
		        </div>				
	        </li>		
	        	
        	<li class="row-0">
        		<?php $enable_pathway = $this->params->get('enable_pathway', 0);?>
				<label for="enable_pathway">
					<input type="checkbox" <?php if($enable_pathway) echo 'checked="checked"';?> value="1" name="plugin[enable_pathway]" id="enable_pathway" /> <?php echo JText::_('ENABLE_PATHWAY')?>
				</label>
				<p class="info"><?php echo JText::_('SHOWHIDE_THE_PATHWAY_OF_THE_JA_VOICE_COMPONENT')?></p>
        	</li>	
        	<li class="row-1">
        		<?php $enable_your_items = $this->params->get('enable_your_items', 0);?>
				<label for="enable_your_items">
					<input type="checkbox" onchange="showOrHideElement(this.checked,'div_enable_your_items')" <?php if($enable_your_items) echo 'checked="checked"';?> value="1" name="plugin[enable_your_items]" id="enable_your_items" /> <?php echo JText::_('ENABLE_YOUR_ITEMS')?>
				</label>
				<p class="info"><?php echo JText::_('SHOWHIDE_THE_MODULE_YOUR_ITEMS_OF_THE_JA_VOICE_COMPONENT')?></p>
				<div <?php if(!$enable_your_items) echo 'style="display:none"';?> id="div_enable_your_items">
					<label for="number_of_your_items">
						<?php echo JText::_("MAXIMUM_OF_ITEMS");?>:
					</label>
					<input type="text" value="<?php echo $this->params->get('number_of_your_items', 5);?>" name="plugin[number_of_your_items]" id="number_of_your_items"/>
					<p class="info"><?php echo JText::_('MAXIMUM_NUMBER_OF_ITEMS_ARE_SHOWN_IN_THE_FRONTEND')?></p>					
				</div>
        	</li>        	
        	<li class="row-0">
        		<?php $enable_login_form = $this->params->get('enable_login_form', 1);?>
				<label for="enable_login_form">
					<input type="checkbox" <?php if($enable_login_form) echo $checked;?> value="1" name="plugin[enable_login_form]" id="enable_login_form" /> 
					<?php echo JText::_('ENABLE_LOGIN_FORM')?>
				</label>
				<div>
					<?php $type_form = $this->params->get('enable_login_form_type', 1);?>
					<ul class="clearfix">
				        <li style="display: inline;">	
				        <?php if (version_compare(JVERSION, '3.0', 'ge')){?>
					        <label for="enable_login_form_type_1">
							<input type="radio" value="1" id="enable_login_form_type_1" name="plugin[enable_login_form_type]" <?php if($type_form==1) echo $checked?> /><?php echo JText::_('SHOW_NORMAL_LOGIN_FORM_OF_JOOMLA')?>
					        </label>
				        <?php }else{?>	
				        	<input type="radio" value="1" id="enable_login_form_type_1" name="plugin[enable_login_form_type]" <?php if($type_form==1) echo $checked?>/>					        	
					        <label for="enable_login_form_type_1">
							     <?php echo JText::_('SHOW_NORMAL_LOGIN_FORM_OF_JOOMLA')?>
					        </label>
					     <?php }?>   
				        </li>
				        <li style="display: inline;">
				        <?php if (version_compare(JVERSION, '3.0', 'ge')){?>
					        <label for="enable_login_form_type_2" class="editlinktip hasTip" title="<?php echo JText::_('INTEGRATE_JA_RPXNOW_PLUGIN' );?>::<?php echo JText::_('INTEGRATE_JA_RPXNOW_PLUGIN_TOOLTIP' ); ?>">
							   <input type="radio" value="2" id="enable_login_form_type_2" name="plugin[enable_login_form_type]" <?php if($type_form==2) echo $checked?> /><?php echo JText::_('INTEGRATE_JA_RPXNOW_PLUGIN')?>
					        </label>	
				        <?php }else{?>	
				        	<input type="radio" value="2" id="enable_login_form_type_2" name="plugin[enable_login_form_type]" <?php if($type_form==2) echo $checked?>/>					        	
					        <label for="enable_login_form_type_2" class="editlinktip hasTip" title="<?php echo JText::_('INTEGRATE_JA_RPXNOW_PLUGIN' );?>::<?php echo JText::_('INTEGRATE_JA_RPXNOW_PLUGIN_TOOLTIP' ); ?>">
							     <?php echo JText::_('INTEGRATE_JA_RPXNOW_PLUGIN')?>
					        </label>
					      <?php }?>   
				        </li>
					</ul>
				</div>
				<p class="info"><?php echo JText::_('SHOWHIDE_THE_LOGIN_FORM_AT_THE_JA_VOICE_COMPONENT')?></p>
        	</li>
        	<li class="row-1">
        		<?php $enable_button_create_new = $this->params->get('enable_button_create_new', 0);?>
				<label for="enable_button_create_new">
					<input type="checkbox" <?php if($enable_button_create_new) echo 'checked="checked"';?> value="1" name="plugin[enable_button_create_new]" id="enable_button_create_new" /> <?php echo JText::_('ENABLE_CREATE_NEW_BUTTON')?>
				</label>
				<p class="info"><?php echo JText::_('SHOWHIDE_THE_BUTTON_CREATE_NEW_OF_THE_JA_VOICE_COMPONENT')?></p>
        	</li>	        			        			        			        			        			      
       	</ul>		
	</div>
	        
	
	<div class="box" style="margin-top: 10px;">
	        <h2><?php echo JText::_('PLUGIN_SETTINGS' ); ?></h2>	
			<div class="box_content">
				<ul class="ja-list-checkboxs">
					<li class="row-1">
						<table width="100%" cellpadding="0" cellspacing="0" class="tbl tbl_appearance">
							<col width="7%" /><col width="93%" />
							<tr>
								<td><img class="screenshot" alt="Screenshot" src="components/com_javoice/asset/images/settings/comments/addthis.gif"/></td>
								<td>
									<?php 
									$str = "<!-- AddThis Button BEGIN -->
										<a href=\"http://www.addthis.com/bookmark.php?v=250&amp;pub=xa-4a764a015f702d7f\" onmouseover=\"return addthis_open(this, '', '[URL]', '[TITLE]')\" onmouseout=\"addthis_close()\" onclick=\"return addthis_sendto()\">
											<img src=\"http://s7.addthis.com/static/btn/lg-share-en.gif\" width=\"125\" height=\"16\" alt=\"" . JText::_('BOOKMARK_AND_SHARE' ) . "\" style=\"border:0\"/>
										</a>
										<script type=\"text/javascript\" src=\"http://s7.addthis.com/js/250/addthis_widget.js?pub=xa-4a764a015f702d7f\"></script>
										<!-- AddThis Button END -->	";				
									?>
									<?php $enable_addthis = $this->params->get('enable_addthis', 1);?>
									<label for="enable_addthis">
										<input type="checkbox" <?php if($enable_addthis) echo $checked;?> value="1" name="plugin[enable_addthis]" id="enable_addthis" /> <?php echo JText::_('ADDTHIS')?>
									</label>
									<p class="info"><?php echo JText::_('THE_1_BOOKMARKING__SHARING_SERVICE')?></p>            
									<div class="ja-block-inline child" id="ja-block-addthis"<?php if(!$enable_addthis){?>style="display:none"<?php } ?>>
										<textarea id="custom_addthis" class="text" name="plugin[custom_addthis]" cols="80" rows="5"><?php echo $this->params->get('custom_addthis', $str);?></textarea>
									</div>
								</td>
							</tr>
						</table>                   
					</li>
					<li class="row-0">
						<table width="100%" cellpadding="0" cellspacing="0" class="tbl tbl_appearance">
							<col width="7%" /><col width="93%" />
							<tr>
								<td><img class="screenshot" alt="Screenshot" src="components/com_javoice/asset/images/settings/comments/AddToAny.jpeg"/></td>
								<td>
									<?php 
									$str = "<a class=\"a2a_dd\" href=\"http://www.addtoany.com/share_save\"><img src=\"http://static.addtoany.com/buttons/share_save_171_16.png\" width=\"171\" height=\"16\" border=\"0\" alt=\"Share/Bookmark\"/></a><script type=\"text/javascript\">a2a_linkname=document.title;a2a_linkurl=location.href;</script><script type=\"text/javascript\" src=\"http://static.addtoany.com/menu/page.js\"></script>";
									?>
									<?php $enable_addtoany = $this->params->get('enable_addtoany', 0);?>
									<label for="enable_addtoany">
										<input type="checkbox" <?php if($enable_addtoany) echo 'checked="checked"';?>value="1" name="plugin[enable_addtoany]" id="enable_addtoany" /> <?php echo JText::_('ADDTOANY_SHARE_BUTTON')?> 
									</label>
									<p class="info"><?php echo JText::_('HELPS_READERS_SHARE_SAVE_BOOKMARK_AND_EMAIL_POSTS_USING_ANY_SERVICE_SUCH_AS_DELICIOUS_DIGG_FACEBOOK_TWITTER_AND_OVER_100_MORE_SOCIAL_BOOKMARKING_AND_SHARING_SITES')?></p>
									<div class="ja-block-inline child" id="ja-block-addtoany"<?php if(!$enable_addtoany){?>style="display:none"<?php } ?>>
										<textarea id="custom_addtoany" class="text" name="plugin[custom_addtoany]" cols="80" rows="5"><?php echo $this->params->get('custom_addtoany', $str);?></textarea>
									</div>
								</td>
							</tr>
						</table>                   
					</li>            
					<li class="row-1">
						<table width="100%" cellpadding="0" cellspacing="0" class="tbl tbl_appearance">
							<col width="7%" /><col width="93%" />
							<tr>
								<td><img class="screenshot" alt="Screenshot" src="components/com_javoice/asset/images/settings/comments/atdbuttontr.gif"/></td>
								<td>									
									<?php $enable_after_the_deadline = $this->params->get('enable_after_the_deadline', 0);?>
									<label for="enable_after_the_deadline">
										<input type="checkbox" <?php if($enable_after_the_deadline) echo 'checked="checked"';?>value="1" name="plugin[enable_after_the_deadline]" id="enable_after_the_deadline" /> <?php echo JText::_('AFTER_THE_DEADLINE__SPELL_CHECK_FOR_COMMENTS')?> 
									</label>
									<p class="info"><?php echo JText::_('LET_USERS_CHECK_SPELLING_AND_GRAMMAR_BEFORE_SUBMITTING_THEIR_COMMENTS')?></p>
								</td>
							</tr>
						</table>                   
					</li>						
					<!--<li class="row-0">
						<?php $enable_polldaddy = $this->params->get('enable_polldaddy', 0);?>
						<label for="enable_polldaddy">
							<img class="screenshot" alt="Screenshot" src="components/com_javoice/asset/images/settings/comments/polldaddy.png"/>
							<input type="checkbox" <?php if($enable_polldaddy) echo 'checked="checked"';?>value="1" name="plugin[enable_polldaddy]" id="enable_polldaddy"/> <?php echo JText::_('POLLDADDY_EMBEDDABLE_POLLS')?>
						</label>
						<p class="info"><?php echo JText::_('CREATE_AND_ADD_POLLDADDY_POLLS_TO_YOUR_COMMENT_STREAM_AND_LET_YOUR_READERS_TAKE_THE_DEBATE_TO_A_NEW_DIMENSION_FIND_OUT_WHAT_YOUR_VISITORS_ARE_THINKING_TODAY_CREATE_YOUR_SURVEYS_AND_LET_YOUR_READERS_CREATE_POLLS_TOO')?></p>
						<div class="ja-block-inline child" id="ja-block-polldaddy"<?php if(!$enable_polldaddy){?>style="display:none"<?php } ?>>
							<textarea id="custom_polldaddy" class="text" name="plugin[custom_polldaddy]" cols="80" rows="5"><?php echo $this->params->get('custom_polldaddy');?></textarea>
						</div>
					</li>
					<li class="row-1">
						<?php $enable_seesmic = $this->params->get('enable_seesmic', 0);?>
						<label for="enable_seesmic">
							<img class="screenshot" alt="Screenshot" src="components/com_javoice/asset/images/settings/comments/seesmic.png"/>
							<input type="checkbox" <?php if($enable_seesmic) echo 'checked="checked"';?>value="1" name="plugin[enable_seesmic]" id="enable_seesmic"/> <?php echo JText::_('SEESMIC_VIDEO_COMMENTS')?>
						</label>
						<p class="info"><?php echo JText::_('ACTIVATE_SEESMIC_VIDEO_COMMENTS_IN_YOUR_INTENSEDEBATE_COMMENTS_SECTION_LET_YOUR_COMMENTERS_VOICES_BE_HEARD_IN_A_WHOLE_NEW_WAY_ABOUT_SEESMIC_SEESMIC_PROVIDES_ANYONE_WITH_AN_INNOVATIVE_WAY_TO_COMMUNICATE_AND_CONNECT_ONLINE_THROUGH_VIDEO_CONVERSATION')?></p>
					</li>-->
					<li class="row-0">
						<table width="100%" cellpadding="0" cellspacing="0" class="tbl tbl_appearance">
							<col width="7%" /><col width="93%" />
							<tr>
								<td><img class="screenshot" alt="Screenshot" src="components/com_javoice/asset/images/settings/comments/simplysmileys.png"/></td>
								<td>
									<?php $enable_smileys = $this->params->get('enable_smileys', 1);?>
									<label for="enable_smileys">
										<input type="checkbox" <?php if($enable_smileys) echo 'checked="checked"';?>value="1" name="plugin[enable_smileys]" id="enable_smileys" /> <?php echo JText::_('SMILEYS')?>
									</label>
									<p class="info"><?php echo JText::_('ADD_SMILEYS_TO_YOUR_COMMENT_SECTION_TO_LET_YOUR_COMMENTERS_EXPRESS_THEIR_DIGITAL_FACIAL_EXPRESSIONS_SARCASM_ISNT_SARCASM_WITHOUT_A_WINK_')?></p>
									<div class="ja-block-inline child" id="ja-block-smileys"<?php if(!$enable_smileys){?>style="display:none"<?php } ?>>
										<ul>
											<li>                                
											<?php                         
											echo JText::_('SELECT_A_STYLE');												
											foreach($smileys as $smiley){
											?>
											<?php if (version_compare(JVERSION, '3.0', 'ge')){?>
											
											<label for="smileys<?php echo $smiley;?>" class="normal" style="font-weight: normal;display:inline-block;"><input type="radio" value="<?php echo $smiley;?>" name="plugin[smiley]" <?php if($this->params->get('smiley', "default")==$smiley) echo $checked;?> id="smileys<?php echo $smiley;?>"/><?php echo ucfirst(JText::_($smiley))?><img src="../components/com_javoice/asset/images/smileys/<?php echo $smiley;?>/smileys_icon.png" /></label>
											<?php }else{?>
											<input type="radio" value="<?php echo $smiley;?>" name="plugin[smiley]" <?php if($this->params->get('smiley', "default")==$smiley) echo $checked;?> id="smileys<?php echo $smiley;?>"/>
											<label for="smileys<?php echo $smiley;?>" class="normal" style="font-weight: normal;"><?php echo ucfirst(JText::_($smiley))?></label><img src="../components/com_javoice/asset/images/smileys/<?php echo $smiley;?>/smileys_icon.png" />
											<?php }?>
											<?php } ?>
											</li>
										</ul>
									</div>
								</td>
							</tr>
						</table>                   
					</li>
					<li class="row-1">
						<table width="100%" cellpadding="0" cellspacing="0" class="tbl tbl_appearance">
							<col width="7%" /><col width="93%" />
							<tr>
								<td><img class="screenshot" alt="Screenshot" src="components/com_javoice/asset/images/settings/comments/youtube.png"/></td>
								<td>
									<?php $enable_youtube = $this->params->get('enable_youtube', 0);?>
									<label for="enable_youtube">
										<input type="checkbox" <?php if($enable_youtube) echo 'checked="checked"';?>value="1" name="plugin[enable_youtube]" id="enable_youtube" /> <?php echo JText::_('YOUTUBE_EMBEDDABLE_VIDEO')?>
									</label>
									<p class="info"><?php echo JText::_('ACTIVATE_YOUTUBE_EMBEDS_AND_YOUR_READERS_WILL_BE_ABLE_TO_SHARE_THEIR_FAVORITE_YOUTUBE_VIDEOS_AND_BEEF_UP_THEIR_RESPONSES_RIGHT_IN_THE_COMMENT_SECTION')?></p>
								</td>
							</tr>
						</table>                   
					</li>
					<li class="row-0">
						<table width="100%" cellpadding="0" cellspacing="0" class="tbl tbl_appearance">
							<col width="7%" /><col width="93%" />
							<tr>
								<td><img class="screenshot" alt="Screenshot" src="components/com_javoice/asset/images/settings/comments/bbcode.png"/></td>
								<td>
									<?php $enable_bbcode = $this->params->get('enable_bbcode', 1);?>
									<label for="enable_bbcode">
										<input type="checkbox" <?php if($enable_bbcode) echo 'checked="checked"';?>value="1" name="plugin[enable_bbcode]" id="enable_bbcode" /> <?php echo JText::_('ENABLE_BBCODE')?>
									</label>																
								</td>
							</tr>
						</table>                   
					</li> 
					<li class="row-1">
						<table width="100%" cellpadding="0" cellspacing="0" class="tbl tbl_appearance">
							<col width="7%" /><col width="93%" />
							<tr>
								<td></td>
								<td>
									<?php $enable_activity_stream = $this->params->get('enable_activity_stream', 1);?>
									<label for="enable_activity_stream">
										<input type="checkbox" <?php if($enable_activity_stream) echo $checked;?>value="1" name="plugin[enable_activity_stream]" id="enable_activity_stream" /> <?php echo JText::_('JOMSOCIAL__ACTIVITY_STREAM')?>
									</label>
									<p class="info"><?php echo JText::_('NEW_VOICE_ITEM_WILL_SHOW_UP_IN_ACTIVITY_STREAM_TO_USE_YOU_NEED_TO_INSTALL_JOMSOCIAL_COMPONENT_AND_ACTIVITY_STREAM_MODULE')?></p>
									
								</td>
							</tr>
						</table>                   
					</li> 
					<li class="row-0">
					<?php $is_attach_image = $this->params->get('is_attach_image', 0);?>				
					<table width="100%" cellpadding="0" cellspacing="0" class="tbl tbl_appearance">
						<col width="7%" /><col width="93%" />
						<tr>
							<td></td>
							<td>
								<label for="is_attach_image">
									<input type="checkbox" <?php if($is_attach_image) echo 'checked="checked"';?>value="1" name="plugin[is_attach_image]" id="is_attach_image" onclick="changeStatusAttachImg(this)" /> <?php echo JText::_('ENABLE_ATTACH_FILES_IN_POSTED_ITEM')?>
								</label>
								<p class="info"><?php echo JText::_('ENABLE_USERS_TO_POST_FILES_HOSTED_ELSEWHERE_INTO_ITEM')?></p>
								<div id="div_is_attach_image" <?php if(!$is_attach_image) echo "style='display:none;'";?>>								
									<?php  $total_attach_file = $this->params->get('total_attach_file', '5');?>
									<label>
										<?php echo JText::_("MAX_NUMBER_OF_ALLOWED_ATTACHMENTS");?>																					
									</label>
									<br />
									<input type="text" onkeyup="checkValidKey(this.value,'total_attach_file')" onkeypress="return isNumberKey(event)" size="3" maxlength="4" value="<?php echo $total_attach_file;?>" name="plugin[total_attach_file]" id="total_attach_file" onblur="checkTotalAttach(this)">
									<p class="info" style="color: red;" id="error_total_attach_file"></p>
									<p class="info"><?php echo JText::_('TOTAL_OF_ATTACHED_FILE')?></p>
									<input type="hidden" value="<?php echo $total_attach_file;?>" id="hidden_total_attach_file" />									
									<?php $max_size_attach_file = $this->params->get('max_size_attach_file', $maxSizeUpload);?>
									<label>
										<?php echo JText::_("MAX_SIZE_FOR_AN_ATTACHED_FILE");?>																					
									</label>
									<br />
									<input type="text" onkeypress="return isNumberKey(event)" onkeyup="checkValidKey(this.value,'max_size_attach_file')" onblur="checkSizeUpload(this, this.value)" size="3" maxlength="4" value="<?php echo $max_size_attach_file;?>" name="plugin[max_size_attach_file]" id="max_size_attach_file"><?php echo JText::_("M");?><?php echo JText::_("<=").$helper->checkUploadSize();?>
									<p class="info" style="color: red;" id="error_max_size_attach_file"></p>
									<p class="info"><?php echo JText::_('SIZE_OF_ATTACH_FILE')?></p>
									<input type="hidden" value="<?php echo $max_size_attach_file;?>" id="hidden_max_size_attach_file" />		
									<?php 
										$attach_file_type = $this->params->get('attach_file_type', 'doc,docx,pdf,txt,zip,rar,jpg,bmp,gif,png');																			
									?>	
									<label>
										<?php echo JText::_("ALLOWED_FILE_TYPES");?>																				
									</label>																		
									<br />
									<?php 
										$listAllowUploads = array('doc', 'docx', 'pdf', 'txt','zip','rar','jpg','bmp','gif','png','ppt');
										$listUploads 	  = explode(",", $attach_file_type);
										foreach ($listAllowUploads as $listAllowUpload){
									?>	
											<label for="fileType<?php echo $listAllowUpload; ?>" style="float: left;">	
												<input type="checkbox" name="plugin[attach_file_type][]" <?php if(in_array($listAllowUpload, $listUploads)) echo("checked='checked'");?>  id="fileType<?php echo $listAllowUpload; ?>" value="<?php echo $listAllowUpload; ?>">&nbsp;<?php echo $listAllowUpload;?>&nbsp;
											</label>											
									<?php
										}
									?>
									<br /><br />
									<p class="info"><?php echo JText::_('SELECT_FILE_TYPE_WHICH_CAN_BE_UPLOADED_AS_ATTACHMENTS_SUPPORT_DOC_DOCX_PDF_TXT_ZIP_RAR_JPG_BMP_GIF_PNG_ONLY')?></p>
								</div>
							</td>											
						</tr>
					</table>										  							
				</li> 
<!--				<li class="row-1">-->
<!--					<table width="100%" cellpadding="0" cellspacing="0" class="tbl tbl_appearance">-->
<!--						<col width="7%" /><col width="93%" />-->
<!--						<tr>-->
<!--							<td></td>-->
<!--							<td><h4>--><?php //echo JText::_("CAPTCHA_SETTINGS");?><!--</h4></td>-->
<!--						</tr>-->
<!--						<tr>-->
<!--							<td></td>-->
<!--							<td>										-->
<!--								<label for="is_enable_captcha">-->
<!--									--><?php //$isEnableCaptcha = $this->params->get('is_enable_captcha', 0);?>
<!--									<input type="checkbox" onclick="checkValidCaptcha(this);" --><?php //if($isEnableCaptcha){?><!--checked="checked"--><?php //}?><!-- value="1" name="plugin[is_enable_captcha]" id="is_enable_captcha"/> -->
<!--									--><?php //echo JText::_('ENABLE_CAPTCHA_IMAGE_SECURITY')?>
<!--								</label>-->
<!--								<p class="info">--><?php //echo JText::_('ENABLE_CAPTCHAIMAGE_FOR_GUEST_POSTER_NEEDS_TO_TYPE_IN_THE_DISPLAYED_CHARACTER_IN_ORDER_TO_POST_A_NEW_ITEM')?><!--</p>-->
<!--							</td>-->
<!--						</tr>-->
<!--						<tr>-->
<!--							<td></td>-->
<!--							<td>										-->
<!--								<label for="is_enable_captcha_user">-->
<!--								--><?php //$isEnableCaptchaUser = $this->params->get('is_enable_captcha_user', 0);?>
<!--								<input type="checkbox" --><?php //if($isEnableCaptchaUser){?><!--checked="checked"--><?php //}?><!-- onclick="checkValidCaptcha(this);" value="1" name="plugin[is_enable_captcha_user]" id="is_enable_captcha_user"/> -->
<!--								--><?php //echo JText::_("ENABLE_CAPTCHA_FOR_REGISTERED_USER");?>
<!--								</label>-->
<!--								<p class="info">--><?php //echo JText::_('ENABLE_CAPTCHAIMAGE_FOR_REGISTERED_USER_POSTER_NEEDS_TO_TYPE_IN_THE_DISPLAYED_CHARACTER_IN_ORDER_TO_POST_A_NEW_ITEM')?><!--</p>-->
<!--							</td>-->
<!--						</tr>-->
<!--					</table>-->
<!--				</li>				            -->
				</ul>                
			</div>
		</div>				
	</div>
<div class="clr"></div>
<input type="hidden" id="hdInvalidTotalAttach" value="<?php echo JText::_("TOTAL_OF_ATTACHED_FILE_MUST_BE_INTEGER_NUMBERS_NOT_NULL_AND_GREATER_THAN_0");?>" />
<input type="hidden" id="hdInvalidSizeAttach" value="<?php echo JText::_("Max size for an attached file must be integer numbers, not null, greater than 0 and less than ".$maxSizeUpload);?>" />
<input type="hidden" name="option" value="com_javoice" />
<input type="hidden" name="view" value="configs" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="group" value="<?php echo $this->group; ?>" />
<input type="hidden" name="cid" value="<?php echo $this->cid; ?>" />
<?php echo JHTML::_( 'form.token' ); ?>
 </form>
 <script>
	function showOrHideElement(display, divID){		
		if(display==1){
 	 	 	jQuery('#'+divID).css('display','');
 	 	}
 	 	else{
			jQuery('#'+divID).css('display','none'); 	 	 	 
 	 	}
	}
 	function isturnon(id,display){
 	 	if(display==1){
 	 	 	jQuery('#'+id+'_content').css('display','block');
 	 	}
 	 	else{
 	 	 	 jQuery('#'+id+'_content').css('display','none');
 	 	}
 	}
 </script>
<script type="text/javascript">
function update_avatar_size_selection(size){
	if($('ja-li-avatar-' + size)!='undifined'){
		for(var i=1; i<=3; i++){
			$('ja-li-avatar-' + i).removeClass('active');
		}
		$('ja-li-avatar-' + size).addClass('active');
	}
}
function changeStatusAttachImg(obj){
	if(obj.checked == true){
		$('div_is_attach_image').style.display = "block";
	}else{
		$('div_is_attach_image').style.display = "none";
	}			
}

function checkSizeUpload(obj, value){	
	if(value == ""){
		$("error_max_size_attach_file").innerHTML = $("hdInvalidSizeAttach").value;		
		obj.value = maxSizeUpload;
		return;
	}		
	if(value > maxSizeUpload || value <=0){		
		if($('hidden_max_size_attach_file').value	<= maxSizeUpload)
			obj.value = $('hidden_max_size_attach_file').value;
		else
			obj.value = maxSizeUpload;
		$("error_max_size_attach_file").innerHTML = $("hdInvalidSizeAttach").value;		
		return;
	}
	$("error_max_size_attach_file").innerHTML = "";		
}

function checkValidKey(value,obj){		
	if(value == 0){
		$(obj).value = "";
	}
}

function isNumberKey(evt){
	   var charCode = (evt.which) ? evt.which : evt.keyCode
	   if (charCode > 31 && (charCode < 48 || charCode > 57))
	      return false;

	   return true;
}

function checkTotalAttach(obj){
	var checkInteger  = /(^\d\d*$)/;
	if(!checkInteger.test($("total_attach_file").value)){
		$("error_total_attach_file").innerHTML = $("hdInvalidTotalAttach").value;	
		
		obj.value = $('hidden_total_attach_file').value;
				
		jQuery('#ja-box-action').animate( {
			bottom :"-45px"
		}, 300);
		return false;
	}else{
		$("error_total_attach_file").innerHTML = "";
		if($("maximum_comment_in_item").value >= 0 || checkInteger.test($("maximum_comment_in_item").value)){
			show_bar_preview('<?php echo JText::_('PREVIEW');?>', '<?php echo JText::_('CANCEL');?>');
		}
		return true;
	}
}

function checkValidCaptcha(obj){
	if(obj.id == "is_enable_captcha_user"){
		if(obj.checked == true){
			$("is_enable_captcha").checked = true;
		}
	}else{
		if(obj.checked == false){
			$("is_enable_captcha_user").checked = false;
		}
	}	
}
</script>