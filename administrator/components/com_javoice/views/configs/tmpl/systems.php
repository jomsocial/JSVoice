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
defined('_JEXEC') or die('Retricted Access');
$mainframe = JFactory::getApplication();

JHTML::_('behavior.tooltip');
jimport('joomla.html.pane');

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
<form action="index.php" method="post" name="adminForm" id="adminForm">

<div class="col100">
	<fieldset class="adminform">
		<?php echo $this->getTabs();?>
	</fieldset>
	<table class="adminlist">
		<tr>
			<td valign="top">
				<fieldset>
					<legend><?php echo JText::_('GENERAL_SETTINGS' ); ?></legend>		
					<table class="admintable" width="100%">
					<tbody>	
						<tr>
							<td class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_('COMPONENT_OFFLINE' );?>::<?php echo JText::_('COMPONENT_OFFLINE_TOOLTIP' ); ?>">
								<?php echo JText::_("COMPONENT_OFFLINE");?>
								</span>
							</td>
							<td>
								<?php $isoff = $this->params->get('is_turn_off_javoice', 1);?>
								<input type="radio"<?php if(!$isoff)echo 'checked' ?> onclick="isturnon(0)" name="systems[is_turn_off_javoice]"  value='0'><?php echo JText::_('JNO')?>
								<input type="radio"<?php if($isoff) echo 'checked' ?> onclick="isturnon(1)" name="systems[is_turn_off_javoice]"  value='1'><?php echo JText::_('JYES')?>
							</td>
						</tr>	
						<tr id='div_display_message'<?php if(!$isoff){?> style='display:none'<?php }?>>
							<td class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_('OFFLINE_MESSAGE' );?>::<?php echo JText::_('OFFLINE_MESSAGE_TOOLTIP' ); ?>">
								<?php echo JText::_("OFFLINE_MESSAGE")?>
							</td>
							<td>
								<textarea name="systems[display_message]" rows="3" cols="50"><?php echo  $this->params->get('display_message');?></textarea>
							</td>
						</tr>
						<tr id='div_display_access' <?php if(!$isoff){?>style='display:none'<?php }?>>
							<td class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_('ACCESS_LEVEL' );?>::<?php echo JText::_('ACCESS_LEVEL_TOOLTIP' ); ?>">
								<?php echo JText::_("ACCESS_LEVEL")?>
							</td>
							<td>
								<?php 
									$item=new stdClass();
									
									$item->access=$this->params->get('access')?$this->params->get('access'):0;
									$access = JHTML::_('access.assetgrouplist', 'access', $item->access);
									echo $access;			
								?>	
							</td>
						</tr>		
						<tr>
							<td class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_('HIDE_THE_JA_VOICE_COPYRIGHT' );?>">
								<?php echo JText::_("HIDE_THE_JA_VOICE_COPYRIGHT");?>
								</span>
							</td>
							<td>
								<input type="radio"<?php if(!$this->params->get('is_turn_off_copyright', 0))echo 'checked' ?>  name="systems[is_turn_off_copyright]"  value='0'><?php echo JText::_('JNO')?>
								<input type="radio"<?php if($this->params->get('is_turn_off_copyright', 0)) echo 'checked' ?> name="systems[is_turn_off_copyright]"  value='1'><?php echo JText::_('JYES')?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_('SELECT_A_PAGING_TYPE' );?>">
								<?php echo JText::_("PAGING_TYPE");?>
								</span>
							</td>
							<td>
								<select name="systems[paging_type]">
									<option value="normal" <?php if($this->params->get('paging_type', 'normal')=="normal"):?>selected="selected""<?php endif;?>><?php echo JText::_("PAGING_NORMAL");?></option>
									<option value="autoscroll" <?php if($this->params->get('paging_type', 'normal')=="autoscroll"):?>selected="selected""<?php endif;?>><?php echo JText::_("PAGING_SCROLL");?></option>
								</select>
							</td>
						</tr>																																																					
						<tr>
							<td class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_('SELECT_A_PAGE_TO_REDIRECT' );?>">
								<?php echo JText::_("PAGE_TO_REDIRECT");?>
								</span>
							</td>
							<td>
								<select name="systems[page_redirect]">
									<option value="item" <?php if($this->params->get('page_redirect', 'item')=="item"):?>selected="selected""<?php endif;?>><?php echo JText::_("ITEM_PAGE");?></option>
									<option value="list" <?php if($this->params->get('page_redirect', 'item')=="list"):?>selected="selected""<?php endif;?>><?php echo JText::_("LIST_PAGE");?></option>
								</select>
							</td>
						</tr>
					</tbody>
					</table>
				</fieldset>		
				
				<fieldset>
					<legend><?php echo JText::_('EMAIL_SETTINGS' ); ?></legend>		
					<table class="admintable" width="100%">
					<tbody>		
						
						<tr>
							<td class="key">
								<label for="systems[enabled]"><span class="editlinktip hasTip" title="<?php echo JText::_('DISABLEENABLE_EMAIL_FUNCTION');?>::<?php echo JText::_('EMAIL_SENDING_TOOLTIP');?>"><?php echo JText::_('EMAIL_SENDING' ); ?></span></label>
							</td>
							<td>
								<input type="radio" name="systems[enabled]" id="enabled-1" value="1" <?php if($this->params->get('enabled')==1) echo 'checked';?>/>
								<label for="enabled-1">
									<span class="editlinktip hasTip" title="<?php echo JText::_('ENABLED' );?>::<?php echo JText::_('EMAIL_WILL_BE_SENT_TO_USER_NORMALLY'); ?>">
										<?php echo JText::_('ENABLED')?>
									</span>									
								</label> 
								
								<input type="radio" name="systems[enabled]" id="enabled-2" value="0" <?php if($this->params->get('enabled')==0) echo 'checked';?>/>
								<label for="enabled-2">
									<span class="editlinktip hasTip" title="<?php echo JText::_('DISABLE' );?>::<?php echo JText::_('NO_EMAIL_WILL_BE_SENT_EVEN_USER_CHOOSE_TO_RECEIVE_UPDATE_VIA_EMAIL'); ?>">
										<?php echo JText::_('DISABLE')?>
									</span>		
								</label>
								
								<input type="radio" name="systems[enabled]" id="enabled-3" value="2" <?php if($this->params->get('enabled')==2) echo 'checked';?>>
								<label for="enabled-3">
									<span class="editlinktip hasTip" title="<?php echo JText::_('PRINT_FOR_DEBUG' );?>::<?php echo JText::_('PRINT_FOR_DEBUG_TOOLTIP'); ?>">
										<?php echo JText::_('PRINT_FOR_DEBUG')?>
									</span>	
								</label>
							</td>
						</tr>			
						<tr id='tr_sendmode'>
							<td class="key">
								<label for="sendmode">								
									<span class="editlinktip hasTip" title="<?php echo JText::_('EMAIL_FORMAT');?>::<?php echo JText::_('EMAIL_FORMAT_TOOLTIP'); ?>">
        <?php echo JText::_('HTMLPLAIN_TEXT' ); ?></span></label>:
							</td>
							<td>
								<input type="radio" name="systems[sendmode]" id="sendmode-1" value="1" <?php if($this->params->get('sendmode')==1) echo 'checked';?>>
								<label for="sendmode-1">
									<?php echo JText::_('HTML')?>
								</label>
								<input type="radio" name="systems[sendmode]" id="sendmode-2" value="0" <?php if($this->params->get('sendmode')==0) echo 'checked';?>>
								<label for="sendmode-2">
									<?php echo JText::_('PLAIN_TEXT')?>
								</label>								
							</td>
						</tr>				
						<tr id='tr_fromname'>
							<td class="key">								
								<label for="fromname">
									<span class="editlinktip hasTip" title="<?php echo JText::_('NAME_OF_EMAIL_SENDER' );?>::<?php echo JText::_('FROM_NAME_TOOLTIP'); ?>">
										<?php echo JText::_('FROM_NAME')?>:
									</span>	
								</label>
							</td>
							<td>
								<input type="text" name="systems[fromname]" value="<?php echo $this->params->get('fromname');?>" id="fromname"  size="50">
							</td>
						</tr>
						<tr id='tr_fromemail'>
							<td class="key">
								<label for="fromemail">
									<span class="editlinktip hasTip" title="<?php echo JText::_('SENDERS_EMAIL_ADDRESS' );?>::<?php echo JText::_('FROM_EMAIL_TOOLTIP'); ?>">
										<?php echo JText::_('FROM_EMAIL')?>:
									</span>	
								</label>
							</td>
							<td>
								<input type="text" name="systems[fromemail]" value="<?php echo $this->params->get('fromemail');?>" id="fromemail"  size="50">
							</td>
						</tr>
						<tr id='tr_ccemail'>
							<td class="key">
								<label for="ccemail"><span class="editlinktip hasTip" title="<?php echo JText::_('ADD_CC_EMAIL');?>::<?php echo JText::_('CC_EMAIL_TOOLTIP');?>"><?php echo JText::_('CC_EMAIL' ); ?></span></label>:
							</td>
							<td>
								<input type="text" name="systems[ccemail]" value="<?php echo $this->params->get('ccemail');?>" id="ccemail"  size="50">
							</td>
						</tr>						
						</tbody>
					</table>
				</fieldset>
				<fieldset>
					<legend><?php echo Jtext::_("Rss Setting Manager") ?></legend>	
					<table class="admintable" width="100%">
						<tbody>		
							
							<tr>
								<td class="key">
									<label for="systems[rss_manager]"><span class="editlinktip hasTip" title="<?php echo JText::_('USERS_RSS_TITLE');?>::<?php echo JText::_('USERS_RSS_DETAILS');?>"><?php echo JText::_('USERS' ); ?></span></label>
								</td>
								<td>
									<?php 
										$gtree = JAVoiceHelpers::getGroupUser();											
										$arrayUserGroup = $this->params->get('user_group','1,6,7,2,3,4,5,10,12,8');										
										$arrayUserGroup = explode(",",$arrayUserGroup);										
										echo JHTML::_('select.genericlist',   $gtree, 'user_group[]', 'class="inputbox" size="7" multiple="multiple" style="width:226px; margin-left:47px;"', 'value', 'text', $arrayUserGroup);
									?>
								</td>
							</tr>
						</tbody>
					</table>								
				</fieldset>
				<fieldset>
					<legend><?php echo JText::_('TAG_SETTINGS' ); ?></legend>		
					<table class="admintable" width="100%">
					<tbody>	
						<tr>
							<td class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_('ENABLE_TAGGING' );?>::<?php echo JText::_('THIS_IS_A_GLOBAL_OPTION_TO_ENABLE_OR_DISABLE_THE_VOICE_TAGGING_SYSTEM' ); ?>">
								<?php echo JText::_("ENABLE_TAGGING");?>
								</span>
							</td>
							<td>
								<?php $isTagOn = $this->params->get('is_enable_tagging', 0);?>
								<input type="radio"<?php if(!$isTagOn)echo 'checked' ?> onclick="usetagging(0)" name="systems[is_enable_tagging]"  value='0'><?php echo JText::_('JNO')?>
								<input type="radio"<?php if($isTagOn) echo 'checked' ?> onclick="usetagging(1)" name="systems[is_enable_tagging]"  value='1'><?php echo JText::_('JYES')?>
							</td>
						</tr>	
						<tr class="tag-settings">
							<td class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_('MINIMUM_LENGTH' );?>::<?php echo JText::_('THE_MINIMUM_NUMBER_OF_CHARACTERS_IN_A_TAG_NAME' ); ?>">
								<?php echo JText::_("MINIMUM_LENGTH");?>
								</span>
							</td>
							<td>
								<input type="text" onkeypress="return isNumberKey(event)" name="systems[tag_minimum_length]" value="<?php echo $this->params->get('tag_minimum_length', 10);?>" id="tag_minimum_length"  size="5">
							</td>
						</tr>
						<tr class="tag-settings">
							<td class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_('MAXIMUM_LENGTH' );?>::<?php echo JText::_('THE_MAXIMUM_NUMBER_OF_CHARACTERS_IN_A_TAG_NAME' ); ?>">
								<?php echo JText::_("MAXIMUM_LENGTH");?>
								</span>
							</td>
							<td>
								<input type="text" onkeypress="return isNumberKey(event)" name="systems[tag_maximum_length]" value="<?php echo $this->params->get('tag_maximum_length',100);?>" id="tag_maximum_length"  size="5">
							</td>
						</tr>
						<tr class="tag-settings">
							<td class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_('MAXIMUM_TAGS_PER_VOICE_ITEM' );?>::<?php echo JText::_('THE_MAXIMUM_TOTAL_TAGS_PER_THREAD' ); ?>">
								<?php echo JText::_("MAXIMUM_TAGS_PER_VOICE_ITEM");?>
								</span>
							</td>
							<td>
								<input type="text" onkeypress="return isNumberKey(event)" name="systems[tag_maximum_per_thread]" value="<?php echo $this->params->get('tag_maximum_per_thread',10);?>" id="tag_maximum_per_thread"  size="5">
							</td>
						</tr>
						<tr class="tag-settings">
							<td class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_('CHARACTERS_SEPARATING_TAGS' );?>::<?php echo JText::_('IF_TYPE_IN_THIS_OPTION_A_CHARACTER_WILL_BE_DISPLAYED_SEPARATE_TAGS' ); ?>">
								<?php echo JText::_("CHARACTERS_SEPARATING_TAGS");?>
								</span>
							</td>
							<td>
								<input type="text" name="systems[characters_separating_tags]" value="<?php echo $this->params->get('characters_separating_tags',',');?>" id="characters_separating_tags"  size="5">
							</td>
						</tr>
						<tr class="tag-settings">
							<td class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_('FORCE_TAGS_TO_BE_LOWER_CASE' );?>::<?php echo JText::_('IF_YOU_ENABLE_THIS_OPTION_A_THROUGH_Z_WILL_BE_REPLACED_WITH_A_THROUGH_Z_IN_TAG_NAMES' ); ?>">
								<?php echo JText::_("FORCE_TAGS_TO_BE_LOWER_CASE");?>
								</span>
							</td>
							<td>
								<?php $isoff = $this->params->get('tag_to_be_lower_case', 1);?>
								<input type="radio"<?php if(!$isoff)echo 'checked' ?> name="systems[tag_to_be_lower_case]"  value='0'><?php echo JText::_('JNO')?>
								<input type="radio"<?php if($isoff) echo 'checked' ?> name="systems[tag_to_be_lower_case]"  value='1'><?php echo JText::_('JYES')?>
							</td>
						</tr>
						</tbody>
					</table>
				</fieldset>	
			</td>
			<td valign="top">
				<fieldset>
					<legend><?php echo JText::_('VOICE_SETTINGS' ); ?></legend>		
					<table class="admintable" width="100%">
						<tbody>	
							<?php //echo strtoupper('If reported as Spam, then the Voice status will be updated to tooltip');exit;?>														
							<tr>
								<td class="key" style="width: 160px;">
									<span class="editlinktip hasTip" title="<?php echo JText::_('EDIT_OR_DELELE_VOICE_AFTER_POSTING' );?>?::<?php echo JText::_('COULD_REGISTER_USER_CAN_EDIT_OR_DELETE_VOICE'); ?>">
										<?php echo JText::_('EDIT_OR_DELELE_VOICE_AFTER_POSTING')?>?
									</span>
								</td>
								<td>
									<?php echo JHTML::_('select.booleanlist',  'systems[is_edit_delete_voice]', 'onclick="setTimeNewVoice(this.value)"',$this->params->get('is_edit_delete_voice'));?>
								</td>							
							</tr>
							<?php $is_allow_delete = $this->params->get('is_edit_delete_voice');?>
							<tr id='div_display_time_for_delete_voice'<?php if(!$is_allow_delete){?> style='display:none'<?php }?>>
								<td class="key" style="width: 160px;">
									<span class="editlinktip hasTip" title="<?php echo JText::_('THE_TIME_INTERVAL_IS_SET_FOR_USERS_TO_BE_ABLE_TO_EDITDELETE_AFTER_POSTING' );?>?::<?php echo JText::_('ALWAY_IF_SET_TIME_IS_1'); ?>">
										<?php echo JText::_('THE_TIME_INTERVAL_IS_SET_FOR_USERS_TO_BE_ABLE_TO_EDITDELETE_AFTER_POSTING')?>
									</span>
								</td>
								<td>
									<input type="text" id="time_for_edit_voice" onkeypress="return isNumberKey(event)" value="<?php echo $this->params->get('time_for_edit_voice', 900);?>" name="systems[time_for_edit_voice]"/>
									<?php echo JText::_("SECONDS");?>
								</td>							
							</tr>
							
							<tr>
								<td class="key" style="width: 160px;">
									<span class="editlinktip hasTip" title="<?php echo JText::_('NEW_VOICE_NEED_TO_BE_ACTIVATED' );?>::<?php echo JText::_('NEW_VOICE_NEED_TO_BE_ACTIVATED_TOOLTIP'); ?>">
										<?php echo JText::_('NEW_VOICE_NEED_TO_BE_ACTIVATED')?>
									</span>
								</td>
								<td>
									<?php echo JHTML::_('select.booleanlist',  'systems[item_needs_approved]', '',$this->params->get('item_needs_approved'));?>
								</td>							
							</tr>
							
							<tr>
								<td class="key" style="width: 160px;">
									<span class="editlinktip hasTip" title="<?php echo JText::_('SEND_EMAIL_ADMIN_WHEN_THERE_IS_A_NEW_VOICE' );?>::<?php echo JText::_('SEND_EMAIL_ADMIN_WHEN_THERE_IS_A_NEW_VOICE_TOOLTIP'); ?>">
										<?php echo JText::_('SEND_EMAIL_ADMIN_WHEN_THERE_IS_A_NEW_VOICE')?>
									</span>
								</td>
								<td>
									<?php echo JHTML::_('select.booleanlist',  'systems[is_notify_admin]', '',$this->params->get('is_notify_admin'));?>
								</td>
							</tr>
                            
                            <tr>
								<td class="key" style="width: 160px;">
									<span class="editlinktip hasTip" title="<?php echo JText::_('ALLOW_USE_ANONYMOUSLY' );?>?::<?php echo JText::_('ALLOW_USE_ANONYMOUSLY_DESC'); ?>">
										<?php echo JText::_('ALLOW_USE_ANONYMOUSLY')?>?
									</span>
								</td>
								<td>
									<?php echo JHTML::_('select.booleanlist',  'systems[use_anonymous]', '',$this->params->get('use_anonymous',0));?>
								</td>							
							</tr>
							<tr>
								<td class="key" style="width: 160px;">
									<span class="editlinktip hasTip" title="<?php echo JText::_('ALLOW_USE_PRIVATE' );?>?::<?php echo JText::_('ALLOW_USE_PRIVATE_DESC'); ?>">
										<?php echo JText::_('ALLOW_USE_PRIVATE')?>?
									</span>
								</td>
								<td>
									<?php echo JHTML::_('select.booleanlist',  'systems[is_private]', '',$this->params->get('is_private',0));?>
								</td>							
							</tr>
							<tr>
								<td class="key">
									<span class="editlinktip hasTip" title="<?php echo JText::_('SET_TIME_FOR_NEW_VOICE' );?>::<?php echo JText::_('SET_TIME_FOR_NEW_VOICE_OR_LATEST_LOGIN' ); ?>">
									<?php echo JText::_("SET_A_VOICE_AS_NEW_VOICE");?>
									</span>
								</td>
								<td>
									<?php $isSetTime = $this->params->get('is_set_time_new_voice', 1);?>
									<select name="systems[is_set_time_new_voice]" onchange="isturnonnewvoice(this.value)">
										<option value="0" <?php if(!$isSetTime)echo 'selected' ?>><?php echo JText::_("USE_LATEST_LOGIN");?></option>
										<option value="1" <?php if($isSetTime)echo 'selected' ?>><?php echo JText::_("SET_DISPLAYING_TIME_FOR_NEW_VOICE");?></option>										
									</select>
									<!--<input type="radio"<?php if(!$isSetTime)echo 'checked' ?> onclick="isturnonnewvoice(0)" name="systems[is_set_time_new_voice]"  value='0'><?php echo JText::_('JNO')?>
									<input type="radio"<?php if($isSetTime) echo 'checked' ?> onclick="isturnonnewvoice(1)" name="systems[is_set_time_new_voice]"  value='1'><?php echo JText::_('JYES')?>
								--></td>
							</tr>
							
							<tr id='div_display_time_for_new_voice'<?php if(isset($isSetTime) && $isSetTime){?> style='display:none'<?php }?>>
								<td class="key">
									<span class="editlinktip hasTip" title="<?php echo JText::_('SET_TIME' );?>::<?php echo JText::_('SET_TIME_FOR_NEW_VOICE' ); ?>">
									<?php echo JText::_("TIME_SET_FOR_A_NEW_VOICE")?>
								</td>
								<td>
									<input type="text" id="time_for_new_voice" onkeypress="return isNumberKey(event)" value="<?php echo $this->params->get('time_for_new_voice', 7200);?>" name="systems[time_for_new_voice]"/>
									<span><?php echo JText::_("SECONDS");?></span>
								</td>
							</tr>
							
							<tr>
								<td class="key">
									<span class="editlinktip hasTip" title="<?php echo JText::_('NUMBER_OF_ITEMS_PER_PAGE' );?>::<?php echo JText::_('NUMBER_OF_ITEMS_PER_PAGE_TOOLTIP'); ?>">
										<?php echo JText::_('NUMBER_OF_ITEMS_PER_PAGE')?>:
									</span>
								</td>
								<td>
									<input type="text" name='systems[display_num]' id='display_num' onchange="checkdataIntAlert('display_num','<?php echo JText::_('INVALID_DATA_PLEASE_INSERT_INFORMATION_AGAIN')?>',10)" size="5" value="<?php echo  $this->params->get('display_num');?>"/>
								</td>							
							</tr>
							
							<tr>
								<td class="key">
									<span class="editlinktip hasTip" title="<?php echo JText::_('MINIMUM_LENGTH_OF_INPUT_VALUE_FOR_A_SEARCH' );?>::<?php echo JText::_('WHEN_DO_A_SEARCH_VOICE_ITEM_YOU_CAN_ADJUST_MINIMUM_LENGTH_FOR_STRING_INPUTTED_TO_SEARCH_TEXT_FIELD'); ?>">
										<?php echo JText::_('MINIMUM_LENGTH_OF_INPUT_VALUE_FOR_A_SEARCH')?>:
									</span>
								</td>
								<td>
									<input type="text" name='systems[minimum_search_num]' id='minimum_search_num' onchange="checkdataIntAlert('minimum_search_num','<?php echo JText::_('INVALID_DATA_PLEASE_INSERT_INFORMATION_AGAIN')?>',10)" size="5" value="<?php echo  $this->params->get('minimum_search_num');?>"/>
								</td>							
							</tr>
							
							<tr>
								<td class="key">
									<span class="editlinktip hasTip" title="<?php echo JText::_('MAX_CHARS_OF_DESCRIPTION' );?>::<?php echo JText::_('MAX_CHARS_OF_DESCRIPTION_TOOLTIP'); ?>">
										<?php echo JText::_('MAX_CHARS_OF_DESCRIPTION')?>:
									</span>
								</td>
								<td>
									<input type="text" name='systems[maxchars]' size="5" id ='maxchars' onchange="checkdataIntAlert('maxchars','<?php echo JText::_('INVALID_DATA_PLEASE_INSERT_INFORMATION_AGAIN')?>',100)" value="<?php echo  $this->params->get('maxchars', 100);?>"/>
								</td>							
							</tr>
							
							<tr>
								<td class="key">
									<span class="editlinktip hasTip" title="<?php echo JText::_("MAX_CHARS_OF_WIDGETS_DESCRIPTION" );?>::<?php echo JText::_('MAX_CHARS_OF_WIDGET_DESCRIPTION_TOOLTIP'); ?>">
										<?php echo JText::_("MAX_CHARS_OF_WIDGETS_DESCRIPTION" );?>:
									</span>
								</td>
								<td>
									<input type="text" name='systems[maxcharswidget]' size="5" id ='maxcharswidget' onchange="checkdataIntAlert('maxcharswidget','<?php echo JText::_('INVALID_DATA_PLEASE_INSERT_INFORMATION_AGAIN')?>',100)" value="<?php echo  $this->params->get('maxcharswidget', 100);?>"/>
								</td>							
							</tr>
							
							<tr>
								<td class="key">
									<span class="editlinktip hasTip" title="<?php echo JText::_('SHOW_NUMBER_OF_DAYS' );?>::<?php echo JText::_('SHOW_NUMBER_OF_DAYS_TOOLTIP'); ?>">
										<?php echo JText::_('SHOW_NUMBER_OF_DAYS')?>?
									</span>
								</td>
								<td>
									<?php echo JHTML::_('select.booleanlist',  'systems[show_date]', '',$this->params->get('show_date', 1));?>
								</td>							
							</tr>
							<tr>
								<td class="key">
									<span class="editlinktip hasTip" title="<?php echo JText::_('LAG' );?>::<?php echo JText::_('LAG_TOOLTIP'); ?>">
										
										<?php echo JText::_('LAG') ;?> <br/><small> (<?php echo JText::_('FOR_SPAMDUPLICATEINAPPROPRIATE');?>)</small>
									</span>
								</td>
								<td>
									<input type="text" name='systems[timelinespam]' size="5" id ='timelinespam' onchange="checkdataIntAlert('timelinespam','<?php echo JText::_('INVALID_DATA_PLEASE_INSERT_INFORMATION_AGAIN')?>',86400)" value="<?php echo  $this->params->get('timelinespam');?>"/> <?php echo JText::_('SECONDS')?>
								</td>							
							</tr>
							<tr>
								<td class="key" style="width: 160px;">
									<span class="editlinktip hasTip" title="<?php echo JText::_('USE_VOTE' );?>?::<?php echo JText::_('USER_CAN_TURN_ONOFF_VOTE_FUNCTION_FOR_VOICE_ITEM_ON_FRONTEND'); ?>">
										<?php echo JText::_('USE_VOTE')?>?
									</span>
								</td>
								<td>
									<?php echo JHTML::_('select.booleanlist',  'systems[is_use_vote]', '',$this->params->get('is_use_vote',1));?>
								</td>							
							</tr>
							<tr>
								<td class="key">
									<span class="editlinktip hasTip" title="<?php echo JText::_('TOTAL_VOTES_TO_REPORT_SPAM' );?>::<?php echo JText::_('TOTAL_VOTES_TO_REPORT_SPAM_TOOLTIP' ); ?>">
									<?php echo JText::_("TOTAL_VOTES_TO_REPORT_SPAM");?>
									</span>
								</td>
								<td>
									<input type="text" name='systems[total_vote_spam]' id='total_vote_spam'  onchange="checkdataIntAlert('total_vote_spam','<?php echo JText::_('INVALID_DATA_PLEASE_INSERT_INFORMATION_AGAIN')?>',30)" value="<?php echo $this->params->get('total_vote_spam')?>">
								</td>
							</tr>	
							<tr>
								<td class="key">
									<span class="editlinktip hasTip" title="<?php echo JText::_('IF_REPORTED_AS_SPAM_THEN_THE_VOICE_STATUS_WILL_BE_UPDATED_TO' );?>::<?php echo JText::_('IF_REPORTED_AS_SPAM_THEN_THE_VOICE_STATUS_WILL_BE_UPDATED_TO_TOOLTIP' ); ?>">
									<?php echo JText::_("IF_REPORTED_AS_SPAM_THEN_THE_VOICES_STATUS_WILL_BE_UPDATED_TO");?>
									</span>
								</td>
								<td>
									<?php 
										$voicetypes=$this->voicetypes;
										$count=count($voicetypes);
										if($count>0){
											?>
											<table class='adminlist'>
												<!--<thead>
													<tr>
														<th>
															<?php echo JText::_("VOICE_TYPE")?>
														</th>
														<th>
															<?php echo JText::_("STATUS")?>
														</th>
													</tr>
												</thead>
												--><tbody>
													<?php 
													for($i=0;$i<$count;$i++){
													?>
														<tr>
															<td>
																<?php echo $voicetypes[$i]->title;?>
															</td>
															<td>
																<input type="hidden" name="systems[status_spam_<?php echo $voicetypes[$i]->id;?>]" id="status_spam_<?php echo $voicetypes[$i]->id;?>" value="<?php echo $this->params->get("status_spam_{$voicetypes[$i]->id}")?>">
																 <span name='status_spam_title<?php echo $voicetypes[$i]->id;?>' id='status_spam_title_<?php echo $voicetypes[$i]->id;?>'>
																 	<?php echo $this->params->get("status_spam_title_{$voicetypes[$i]->id}")?>
																 </span>
																 [<a title="<?php echo JText::_('CLICK_TO_CHANGE_STATUS_FOR_REPORT_SPAM')?>" href="javascript:void(0)" onclick="jaCreatForm('editstatus&group=systems' ,'<?php echo $voicetypes[$i]->id?>',400,400,0,0,'<?php echo JText::_('CHOOSE_STATUS');?>',1);return false;"><?php echo JText::_('EDIT')?></a>]															
															</td>
															
														</tr>
														<?php 													
													}													
													?>
												</tbody>
											</table>
											<?php 
										}
									?>
											</td>
										</tr>								
									</tbody>
								</table>
							</fieldset>						
			</td>
			
		</tr>
				
	</table>
					

				
</div>
<div class="clr"></div>
<input type="hidden" name="option" value="com_javoice" />
<input type="hidden" name="view" value="configs" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="group" value="<?php echo $this->group; ?>" />
<input type="hidden" name="cid" value="<?php echo $this->cid; ?>" />
<?php echo JHTML::_( 'form.token' ); ?>	
 </form>
 <script>
 	<?php if(!$isTagOn):?>
    	window.addEvent('domready', function(){ $$('.tag-settings').setStyle('display','none'); });
    <?php endif;?>

 	function usetagging(type){
 		var display='table-row';
 	 	if(type==0)display='none';
 	 	
 	 	$$('.tag-settings').setStyle('display',display);
 	 	$$('.tag-settings').setStyle('display',display); 		
 	}
 	
 	function isturnonnewvoice(type){
	 	var display='table-row';
	 	if(type==0){display='none'};
	 	$$('#div_display_time_for_new_voice').setStyle('display',display);
	 	$$('#div_display_time_for_new_voice').setStyle('display',display);
	}
	
 	function isturnon(type){
 	 	var display='table-row';
 	 	if(type==0)display='none';
 	 	$$('#div_display_message').setStyle('display',display);
 	 	$$('#div_display_access').setStyle('display',display);
 	}

 	function setTimeNewVoice(type){
 		var display='table-row';
	 	if(type==0)display='none';
	 	
	 	$$('#div_display_time_for_delete_voice').setStyle('display',display);
	 	$$('#div_display_time_for_delete_voice').setStyle('display',display);
 	}
	
	function isNumberKey(evt){
	   var charCode = (evt.which) ? evt.which : evt.keyCode	   
	   if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 45)
	      return false;

	   return true;
	}
 	isturnonnewvoice('<?php echo $isSetTime?>'); 	
 </script>