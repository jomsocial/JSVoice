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
$user 		= JFactory::getUser();
$userAdd 	= JFactory::getUser($this->item->user_id);
$isAllowUpload = 0;
if($this->is_attach_image){
	$isAllowUpload = 1;	
}

$helper = new JAVoiceHelpers();
$listFile = $helper-> preloadfile($this->item->id);

if($javconfig["systems"]->get("is_enable_tagging", 0)){
?>
<script type="text/javascript" charset="utf-8">
//<![CDATA[
if (typeof(jav_tag_config) == "undefined") {
	var jav_tag_config = {
		tag_minimum_length:<?php echo $javconfig['systems']->get('tag_minimum_length',4);?>,
		tag_maximum_length:<?php echo $javconfig['systems']->get('tag_maximum_length',4);?>,
		tag_maximum_per_thread:<?php echo $javconfig['systems']->get('tag_maximum_per_thread',4);?>,
		characters_separating_tags:'<?php echo $javconfig['systems']->get('characters_separating_tags',',');?>'
	};
}
//]]>
</script>
<?php
}
?>
<div id="jav-dialog-content">
  <?php if($isAllowUpload){?>	
  	<form method="post" id="new_item" name="new_item" class="new_item" enctype="multipart/form-data" action="index.php?tmpl=component&option=com_jacomment&view=comments&task=uploadFile">
  <?php }else{?>
  	<form method="get" id="new_item" class="new_item" action="index.php">
  <?php }?>
    <fieldset class='jav-fieldset'>      
      <ul >
        <li class="ja-allrow title" style="width: 50%; float: left">
          <label class="desc" for="title">
            <?php echo JText::_('TITLE')?> <span class="counter" id="title-counter">(<em  id="jav-charactersleft"><?php echo (100-strlen($this->item->title))?></em> <?php echo JText::_('CHARACTERS_LEFT')?>)</span>
          </label>
          <div>
          	<input type="text" onkeypress="jav_checkMaxLength(this, 100); return jav_handleEnter(this, event)" value="<?php echo htmlspecialchars($this->item->title);?>" size="54" name="title" maxlength="100" id="title" class="text"/>
          	
          </div>
          <div id="title-msg" class="error" style="display: none;">
          	<?php echo JText::_('PLEASE_ENTER_TITLE_VALUE')?>
          </div>		  
          <div class="error" id="error_javTitle" style="display: none;"><?php echo JText::sprintf("YOU_MUST_INPUT_TITLE_AT_LEAST_3_CHARACTERS",$javconfig['systems']->get('minimum_search_num',4));?></div>
	        <div id="forums_id-msg" class="error" style="display:none">
				 <?php echo JText::_('PLEASE_CHOOSE_FORUMS')?>              
	        </div>          
        </li>
        <li style="width: 45%; float: right;">
          <label class="desc"><?php echo JText::_('FORUM')?></label>
          <?php echo $this->displayForums;?>
        </li>
        <li id="forums_id-msg" class="error" style="display:none">
			 <?php echo JText::_('PLEASE_CHOOSE_FORUMS')?>              
        </li> 
        <?php 
			if($javconfig["systems"]->get("is_enable_tagging", 0)){
				$modelTags 	= JAVBModel::getInstance ( 'tags', 'javoiceModel' );							
				$listTags 	= $modelTags->getTagByVoice($this->item->id);
		?>
        <?php  
		$textAddTag = JText::_("TAG");
		$txtTagDisable = '';
		$numTag = $javconfig["systems"]->get("tag_maximum_per_thread", 4);						
		if($numTag >1) $textAddTag = JText::_("TAGS");
		if(isset($this->listTags) && $this->listTags){			
			if($numTag <= count($this->listTags)){ $txtTagDisable = 'disabled="disabled"';}
		}		
		?>
		<li class="ja-allrow">
			<ul>		
				<li style="display: block; clear: both;">			
					<div id="jav_tag_form">
						<label class="desc" for="jav_input_tags">
							<?php echo JText::_("ADD_TAG");?>:
							&nbsp;&nbsp;&nbsp;<input type="text" style="width: 400px;" <?php echo $txtTagDisable;?> style="<?php echo $txtTagDisable;?>;width: 400px;" size="25" class="text" value="" id="jav_input_tags" onkeyup="suggest(this.value);" class="" />
							<input id="jav_add_tag_button" type="button" value="Add" onclick="javAddTags(<?php echo $javconfig["systems"]->get("tag_maximum_per_thread", 4);?>)"/>					
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
					<?php 
						$tag_to_be_lower_case = $javconfig['systems']->get('tag_to_be_lower_case',1);
						$style = '';
						if($tag_to_be_lower_case == 1){
							$style = 'style = "text-transform:lowercase;"';
						}
					?>						
					<div id="jav_tags_list" <?php echo $style;?>>
						<?php if($listTags):?>
						<ul class="javtaglist"><?php foreach ($listTags as $tagItem):?>
							<li id="jav_tag_<?php echo $tagItem->id;?>" class="javtag" onclick="javRemoveTag(this)">
								<input class="javtagid" type="hidden" name="javtag[]" value="<?php echo $tagItem->id;?>"/>
								<span class="javtagtext"><?php echo $tagItem->name;?></span>
							</li>
						<?php endforeach;?></ul>
						<?php endif;?>
					</div>					
				</li>		
			</ul>		
		</li>	
		<?php }?>             
        <li class="ja-allrow"> 
      		<div id="jav-post-new-voice">
				<ul class="form-comment">
					<li class="clearfix form-comment" id="jac-editor-addnew">
						<?php echo $this->loadTemplate('editor');?>										 					        
					</li>
					<!--BEGIN  Upload form-->															
					<li class="clearfix form-upload" name="jav-form-upload" <?php if(!$listFile){?>style="display: none;"<?php }?> id="jav-form-upload">						
						<div id="jav-upload" class="clearfix">
							<?php if($isAllowUpload){?>
							<?php unset($_SESSION['javtemp']);unset($_SESSION['javnameFolder']);?>
								<p class="error" id="jav_err_myfile"></p>
								<div class="jav-upload-form" style="float: left;">						
									<input name="myfile" id="myfile" type="file" size="30" <?php if($this->total_attach_file <= 0) echo 'disabled="disabled"';?> onchange="javStartUpload(1);" class="field file" tabindex="5"/>
									<span id="jav_upload_process" class="jav-upload-loading" style="display: none;">
										<img src="<?php echo Juri::root();?>/components/com_javoice/asset/images/loading.gif" alt="<?php echo JText::_("LOADING"); ?>" />
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
				<div id="err_exitchekspelling" class="error" style="display: none;"><?php echo JText::_("PLEASE_EXIT_SPELL_CHECK_BEFORE_SUBMITTING_VOICE");?></div>
				<!-- Expan form-->																							
			</div>  	
        </li>                
        <li style="padding-top: 4px;">
        	<img src="<?php echo JURI::base()?>components/com_javoice/asset/images/loading.gif" alt="" style="display:none; padding-left: 10px; z-index:1000" id="jav-form-wait" />
        </li>
        <li id="votes-msg" style="display:none">
			          
        </li>
        <?php //allow edit information of guest?>
        <?php if($this->item->user_id == 0){ ?>
        <li class="jav-guest-info">
        	<div id="other_field" class="clearfix">
				<div class="form-userdata clearfix">
					<!-- BEGIN TEXT NAME-->
					<span class="jac-form-guest">
						<label for="javGuestName"><?php echo JText::_("NAME");?> <span id="required_1" class="required">*</span></label>
						<input id="javGuestName" name="guest_name" value="<?php echo $this->item->guest_name;?>" maxlength="255" type="text" class="text" size="25" tabindex="2" title="<?php echo JText::_("DISPLAYED_NEXT_TO_YOUR_COMMENTS");?>"/>						
						<div id="err_javGuestName" class="error" style="display: none;"><?php echo JText::_("YOU_MUST_INPUT_NAME");?></div>											
					</span>
					<!-- END TEXT NAME-->
					<!-- BEGIN TEXT EMAIL-->
					<span class="jac-form-guest">
						<label for="javGuestEmail"><?php echo JText::_("EMAIL");?> <span id="required_2" class="required">*</span></label>
						<input id="javGuestEmail" name="guest_email" value="<?php echo $this->item->guest_email;?>" type="text" maxlength="100" class="text" value="" size="25" tabindex="3" title="<?php echo JText::_("NOT_DISPLAYED_PUBLICLY");?>"/>
						<div id="err_javGuestEmail" class="error" style="display: none;"><?php echo JText::_("YOU_MUST_INPUT_EMAIL");?></div>
						<div id="err_javGuestEmailInvalid" class="error" style="display: none;"><?php echo JText::_("YOUR_EMAIL_ADDRESS_IS_INVALID");?></div>											
					</span>
					<!-- END TEXT EMAIL-->							
				</div>
			</div>
        </li>
        <?php }?>
        <?php if($javconfig["systems"]->get("use_anonymous",0)):?>
        <li class="clearfix"> 
            <label for="use_anonymous" class="description jav-title"><?php echo JText::_("DISPLAY_ANONYMOUS_TEXT");?>
            	<input type="radio" id="use_anonymous_1" name="use_anonymous" value="1" <?php if($this->item->use_anonymous):?>checked="checked"<?php endif;?>/><label for="use_anonymous_1"><?php echo JTEXT::_("JYES");?></label>
                <input type="radio" id="use_anonymous_0" name="use_anonymous" value="0" <?php if(!$this->item->use_anonymous):?>checked="checked"<?php endif;?>/><label for="use_anonymous_0"><?php echo JTEXT::_("JNO");?></label>
            </label>            
        </li>	
        <?php endif;?>
        <?php if($javconfig["systems"]->get("is_private",0)):?>
        <li class="clearfix">
            <label for="is_private" class="description jav-title"><?php echo JText::_("DISPLAY_AS_PRIVATE_VOICE");?></label>
            <input type="radio" id="is_private_1" name="is_private" value="1" <?php if($this->item->is_private):?>checked="checked"<?php endif;?>/><label for="is_private_1"><?php echo JTEXT::_("JYES");?></label>
            <input type="radio" id="is_private_0" name="is_private" value="0" <?php if(!$this->item->is_private):?>checked="checked"<?php endif;?>/><label for="is_private_0"><?php echo JTEXT::_("JNO");?></label>                        
        </li>	
        <?php endif;?>
      </ul>
    </fieldset>
    <input type="hidden" value="<?php echo $this->item->id?>" name="cid[]"/>
    <input type="hidden" value="com_javoice" name="option"/>
    <input type="hidden" value="items" name="view"/>    
    <input type="hidden" value="save" name="task" id="task"/>
    <input type="hidden" value="<?php echo $this->type->id?>" name="type"/>
    <?php echo JHTML::_( 'form.token' ); ?>
  </form>    
</div>