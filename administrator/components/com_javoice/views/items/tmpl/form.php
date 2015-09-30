<?php // no direct access
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
$item=$this->item;
//$editor =& JFactory::getEditor();
$mess=$this->mess;
$user 		= JFactory::getUser();
$isAllowUpload = 0;
if(isset($this->item) && $this->item->id){
	$userAdd 	= JFactory::getUser($this->item->user_id);	
	if($this->is_attach_image){
		$isAllowUpload = 1;	
	}
}else{
	if($this->is_attach_image){
		$isAllowUpload = 1;	
	}
}
global $javconfig;

$helper   = new JAVoiceHelpers();
$listFile = $helper-> preloadfile($this->item->id, "admin");
?>
<script type="text/javascript">
<?php if($javconfig["systems"]->get("is_enable_tagging", 0)):?>
	var jav_tag_config = {
			tag_minimum_length:<?php echo $javconfig['systems']->get('tag_minimum_length',4);?>,
			tag_maximum_length:<?php echo $javconfig['systems']->get('tag_maximum_length',4);?>,
			tag_maximum_per_thread:<?php echo $javconfig['systems']->get('tag_maximum_per_thread',4);?>,
			characters_separating_tags:'<?php echo $javconfig['systems']->get('characters_separating_tags',',');?>'
	}
<?php endif;?>
</script>
<?php if($this->is_attach_image){?>    
 <form method="post" id="adminForm" name="adminForm" class="adminForm" enctype="multipart/form-data" action="index.php?tmpl=component&option=com_jacomment&view=comments&task=uploadFile"> 
<?php }else{?>
 <form name="adminForm" id="adminForm" action="index.php" method="post">
<?php }?>
 	<input type="hidden" name="option" value="com_javoice" /> 

 	<input type="hidden" name="view" value="items" /> 

 	<input type="hidden" name="task" value="saveIFrame" /> 
 	
 	<input type="hidden" name="tmpl" value="component" /> 

 	<input type="hidden" name='id' id='id' value="<?php echo $item->id?>"> 
 	
 	<input type="hidden" name='voicetype_default' id='voicetype_default' value="<?php echo $this->voicetype?>"> 
 	
 	<input type="hidden" name='cid[]' id='cid[]' value="<?php echo $item->id?>">
 	
	<input type="hidden" name="user_id" value="<?php echo $item->user_id;?>">
	
	<input type="hidden" name="number" value="<?php echo $this->number;?>">
	<ul class="ja-haftleft">
		<li>
			<label class="desc" for="title"><?php echo JText::_("TITLE" );?></label>
			<div><input onchange="checkdataString(this,'error')" type="text" id="title" name="title" size='50' value="<?php echo $item->title?>" class="text"></div>
			<div class="error" style="display: none;" id="jav-error-sort-title"><?php echo JText::_("YOU_MUST_INPUT_TITLE_AT_LEAST_3_CHARACTERS");?></div>
		</li>
		<li>
			<label class="desc" for="title"><?php echo JText::_("VOICE_TYPE" );?></label>
			<div id='jav-voicetype'><?php echo $this->displayVoicetypes;?></div>
		</li>		
		<li>
			<label class="desc" for="title"><?php echo JText::_("FORUMS" );?></label>
			<div id='jav-forum'><?php echo $this->displayForums;?></div>
			<div class="error" style="display: none;" id="jav-select-forum"><?php echo JText::_("YOU_MUST_SELECT_A_FORUM");?></div>
		</li>
		<li>
			<label class="desc" for="title"><?php echo JText::_("STATUS" );?></label>
			<div id='jav-status'><?php echo $this->displaystatus;?></div>
		</li>
		<li>
			<label class="desc" for="title"><?php echo JText::_("USERNAME" );?></label>
			<div><input class="text" type="text" disabled="disabled" name="username" id="username" value="<?php echo $this->user->username?>"></div>
		</li>
		<li>
			<label class="desc" for="title"><?php echo JText::_("CREATE_DATE" );?></label>
			<div><?php echo JHTML::_('calendar',date("Y-m-d H:i:s", $item->create_date), 'create_date', 'create_date', '%Y-%m-%d', array('class'=>'text', 'size'=>'25',  'maxlength'=>'19'));?></div>
		</li>
		<li>
			<label class="desc" for="title"><?php echo JText::_("PUBLISHED" );?></label>
			<div>
					<?php
					$published = ($item->published==1) ? $item->published : 0;
					$lists['published'] 		= JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $published );
				 echo $lists['published']; ?>
			</div>
		</li>
	</ul>
 	
	<ul class="ja-haftright">
		<li>
			<label class="desc" for="title"><?php echo JText::_("NUMBER_VOTE_UPS" );?></label>
			<div><input onchange="checkdataInt(this,'error')"  class="text" type="text" name="number_vote_up" id="number_vote_up" size="7" value="<?php echo $item->number_vote_up?>"></div>
		</li>	
		<li>
			<label class="desc" for="title"><?php echo JText::_("TOTAL_VOTE_UPS" );?></label>
			<div><input onchange="checkdataInt(this,'error')"  class="text" type="text" name="total_vote_up" id="total_vote_up"size="7" value="<?php echo $item->total_vote_up?>"></div>
		</li>
		<li>
			<label class="desc" for="title"><?php echo JText::_("NUMBER_VOTE_DOWNS" );?></label>
			<div><input onchange="checkdataInt(this,'error')" class="text" type="text" name="number_vote_down" id="number_vote_down" size="7" value="<?php echo $item->number_vote_down?>"></div>
		</li>
		<li>
			<label class="desc" for="title"><?php echo JText::_("TOTAL_VOTE_DOWNS" );?></label>
			<div><input onchange="checkdataInt(this,'error')"  class="text" type="text" name="total_vote_down" id="total_vote_down" size="7" value="<?php echo $item->total_vote_down?>"></div>
		</li>	
		<li>
			<label class="desc" for="title"><?php echo JText::_("NUMBER_USERS_REPORT_TO_SPAM" );?></label>
			<div><input onchange="checkdataInt(this,'error')" class="text" type="text" name="number_spam" id="number_spam" size="7" value="<?php echo $item->number_spam?>"></div>
		</li>		
	</ul>
	
	<?php if($javconfig["systems"]->get("is_enable_tagging", 0)):?>
    <?php  
		$textAddTag = JText::_("TAG");
		$txtTagDisable = '';
		$numTag = $javconfig["systems"]->get("tag_maximum_per_thread", 4);						
		if($numTag >1) $textAddTag = JText::_("TAGS");
		if(isset($this->listTags) && $this->listTags){			
			if($numTag <= count($this->listTags)){ $txtTagDisable = 'disabled="disabled"';}
		}		
	?>
	<ul>		
		<li style="display: block; clear: both;">			
			<div id="jav_tag_form">
				<label class="desc" for="jav_input_tags">
					<?php echo JText::_("ADD_TAG");?>:                    
					&nbsp;&nbsp;&nbsp;<input type="text" style="width: 400px;" <?php echo $txtTagDisable;?> style="<?php echo $txtTagDisable;?>;width: 400px;" size="25" class="text" value="" id="jav_input_tags" onkeyup="suggest(this.value);" class="" />
					<input id="jav_add_tag_button" type="button" value="Add" onclick="javAddTags()"/>					
				</label>
				<div style="margin-left: 62px;">                	
					<?php echo JText::_("SEPARATE_TAGS_USING_A_COMMA"). " (" . $javconfig['systems']->get('characters_separating_tags',',') . "). " . JText::_("YOU_MAY_ADD")." ".$numTag." ".$textAddTag." ".JText::_("TO_THIS_POST");?>.
				</div>
				<div class="suggestionsBox" id="suggestions" style="display: none;">									
        			<div class="suggestionList" id="suggestionsList"> &nbsp; </div>
      			</div>
			</div>
			<label class="desc" for="jav_input_tags">
				<?php echo JText::_("TAG_LIST");?>:
			</label>							
			<div id="jav_tags_list">
				<?php if(isset($this->listTags) && $this->listTags):?>
				<ul class="javtaglist"><?php foreach ($this->listTags as $tagItem):?>
					<li id="jav_tag_<?php echo $tagItem->id;?>" class="javtag" onclick="javRemoveTag(this)">
						<input class="javtagid" type="hidden" name="javtag[]" value="<?php echo $tagItem->id;?>"/>
						<span class="javtagtext"><?php echo $tagItem->name;?></span>
					</li>
				<?php endforeach;?></ul>
				<?php endif;?>
			</div>					
		</li>		
	</ul>
	<?php endif;?>
	
	<ul>
		<li style="display: block; clear: both;">		
		<div id="jav-dialog-content">
			<div id="jav-post-new-voice">
				<ul class="form-comment">
					<li class="clearfix form-comment" id="jac-editor-addnew">
						<?php 
							$this->setLayout("default");
							echo $this->loadTemplate('editor');
						?>										 					        
					</li>
					<!--BEGIN  Upload form-->															
					<li class="clearfix form-upload" <?php if(!$listFile){?>style="display: none;"<?php }?> id="jav-form-upload">						
						<div id="jav-upload" class="clearfix">
							<input type="hidden" name="jav-form-upload" value=""/>
							<?php if($isAllowUpload){?>
							<?php unset($_SESSION['javtemp']);unset($_SESSION['javnameFolder']);?>
								<p class="error" id="jav_err_myfile"></p>
								<div class="jav-upload-form">															
									<input name="myfile" id="myfile" type="file" size="30" <?php if(($this->total_attach_file <= 0) || (substr_count($listFile,"input") >= $this->total_attach_file)) echo 'disabled="disabled"';?> onchange="javStartAdminUpload(1);" class="field file" tabindex="5"/>
									<span id="jav_upload_process" class="jav-upload-loading" style="display: none;">
										<img src="components/com_javoice/asset/images/loading.gif" alt="<?php echo JText::_("LOADING"); ?>" />
									</span>
									<div class="small"><?php echo JText::_("ATTACHED_FILE");?> (&nbsp;<?php echo JText::_("TOTAL");?> <?php echo $this->total_attach_file; ?> <?php if($this->total_attach_file>1){ echo JText::_("FILES__MAX_SIZE").'&nbsp;<b>'.$helper->getSizeUploadFile().'</b>';}else{ echo JText::_("FILE__MAX_SIZE").'&nbsp;<b>'.$helper->getSizeUploadFile().'</b>';}?>&nbsp;)</div>														
								</div>						
							<?php }?>																																															
							<div id="jav_result_upload">
								<?php echo $listFile;?>																
							</div>					
						</div>	
					</li>					
					<!--END  Upload form-->		
				</ul>				
					<div id="err_exitchekspelling" class="error" style="display: none;color: red;"><?php echo JText::_("PLEASE_EXIT_SPELL_CHECK_BEFORE_SUBMITTING_VOICE");?></div>
					<!-- Expan form-->																							
			</div>
			<?php if($javconfig["systems"]->get("use_anonymous",0)):?>
			<ul class="ja-haftleft">
		        <li class="clearfix"> 
		            <label for="use_anonymous" class="description jav-title"><?php echo JText::_("DISPLAY_ANONYMOUS_TEXT");?>
		            	<input type="radio" id="use_anonymous_1" name="use_anonymous" value="1" <?php if($this->item->use_anonymous):?>checked="checked"<?php endif;?>/><label for="use_anonymous_1"><?php echo JTEXT::_("YES");?></label>
		                <input type="radio" id="use_anonymous_0" name="use_anonymous" value="0" <?php if(!$this->item->use_anonymous):?>checked="checked"<?php endif;?>/><label for="use_anonymous_0"><?php echo JTEXT::_("NO");?></label>
		            </label>            
		        </li>
	        </ul>	
	        <?php endif;?>
	        <?php if($javconfig["systems"]->get("is_private",0)):?>
	        <ul class="ja-haftright">
		        <li class="clearfix">
		            <label for="is_private" class="description jav-title"><?php echo JText::_("DISPLAY_AS_PRIVATE_VOICE");?></label>
		            <input type="radio" id="is_private_1" name="is_private" value="1" <?php if($this->item->is_private):?>checked="checked"<?php endif;?>/><label for="is_private_1"><?php echo JTEXT::_("YES");?></label>
		            <input type="radio" id="is_private_0" name="is_private" value="0" <?php if(!$this->item->is_private):?>checked="checked"<?php endif;?>/><label for="is_private_0"><?php echo JTEXT::_("NO");?></label>                        
		        </li>
	        </ul>	
	        <?php endif;?>		
			<?php if($this->item->user_id == 0 && $this->item->id != 0){ ?>
			<ul class="ja-haftleft">
				<li>
					<!-- BEGIN TEXT NAME-->
					<span class="jac-form-guest">
						<label for="javGuestName" class="desc"><?php echo JText::_("NAME");?> <span id="required_1" class="required">*</span></label>
						<input id="javGuestName" name="guest_name" value="<?php echo $this->item->guest_name;?>" maxlength="255" type="text" class="text" size="25" tabindex="2" title="<?php echo JText::_("DISPLAYED_NEXT_TO_YOUR_COMMENTS");?>"/>						
						<div id="err_javGuestName" class="error" style="display: none;"><?php echo JText::_("YOU_MUST_INPUT_NAME");?></div>											
					</span>
					<!-- END TEXT NAME-->
				</li>
			</ul>
			<ul class="ja-haftright">
				<!-- BEGIN TEXT EMAIL-->
				<span class="jac-form-guest">
					<label for="javGuestEmail" class="desc"><?php echo JText::_("EMAIL");?> <span id="required_2" class="required">*</span></label>
					<input id="javGuestEmail" name="guest_email" value="<?php echo $this->item->guest_email;?>" type="text" maxlength="100" class="text" value="" size="25" tabindex="3" title="<?php echo JText::_("NOT_DISPLAYED_PUBLICLY");?>"/>
					<div id="err_javGuestEmail" class="error" style="display: none;"><?php echo JText::_("YOU_MUST_INPUT_EMAIL");?></div>
					<div id="err_javGuestEmailInvalid" class="error" style="display: none;"><?php echo JText::_("YOUR_EMAIL_ADDRESS_IS_INVALID");?></div>											
				</span>
				<!-- END TEXT EMAIL-->		
			</ul>					
			<?php }?>			
		</div>
		</li>	
	</ul>
 </form>