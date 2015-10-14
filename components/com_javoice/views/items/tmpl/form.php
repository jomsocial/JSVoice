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
$Itemid = JRequest::getInt('Itemid');
if(!$this->types) return;
$currentUserInfo		= JFactory::getUser();
JHTML::stylesheet( 'components/com_javoice/asset/js/atd/ja.voice.css');
$isShowCaptcha = 0; 
if((($currentUserInfo->guest && $this->is_enable_captcha) || (!$currentUserInfo->guest && $this->is_enable_captcha_user))&& ($currentUserInfo->get ( 'aid' )<2)){
	$isShowCaptcha = 1;		
}
$helper = new JAVoiceHelpers();
$currentLayout = JRequest::getVar("layout", "form");
global $javconfig;
?>
<?php 
if($currentLayout == "form"){	
?>
<script type="text/javascript" charset="utf-8">
//<![CDATA[	
	jQuery(document).ready(function($){	
		jav_init();	
	});
	var jav_base_url = '<?php echo JURI::base()?>';	
	var jav_vote_total = <?php echo $this->votes_left?>;
	var jav_dont_permission = "<?php echo JText::_('YOU_DONT_PERMISSION')?>";
	var jav_many_vote = '<?php echo JText::_('HOW_MANY_VOTES')?>';
	var jav_run_out = "<?php echo JText::_("YOUVE_RUN_OUT_OF_VOTE")?>" +  "[<a onclick='jav_showNoticeToCenter(400, 120, \"jav-dialog\"); return false;' href='javascript:void(0)'>?</a>]";
	var jav_option_type = <?php echo $this->type_id?>;
	var siteurl = '<?php echo JURI::base();?>index.php?option=com_javoice&view=items&tmpl=component&Itemid=<?php echo $Itemid?>';
	var jav_text_checkspelling_no_error = '<?php echo JText::_("NO_WRITING_ERRORS_WERE_FOUND");?>';
//]]>
</script>
<?php if($javconfig['plugin']->get('enable_bbcode',1) && $currentLayout== "form"){?>	
	<script type="text/javascript" src="components/com_javoice/asset/js/dcode/dcodr.js"></script>
	<script type="text/javascript" src="components/com_javoice/asset/js/dcode/dcode.js"></script>	
<?php }?>
<?php if($javconfig['plugin']->get('enable_after_the_deadline',1)){?>
	<script type="text/javascript" src="components/com_javoice/asset/js/atd/atd.js"></script>
	<script type="text/javascript" src="components/com_javoice/asset/js/atd/csshttprequest.js"></script>
	<script type="text/javascript" src="components/com_javoice/asset/js/atd/jquery.atd.js"></script>
	<?php JHTML::stylesheet('components/com_javoice/asset/js/atd/atd.css'); ?>	
<?php }?>

<script type="text/javascript">	
	var v_array_type 	  		= new Array();	
	var error_type_file   		= "";
	var total_attach_file 		= 0;
	var error_name_file   		= "";
	var max_size_attach_file 	= 0;		
</script>

<?php if($javconfig['plugin']->get('is_attach_image',1)){	
	$attachFileType	 = $javconfig['plugin']->get('attach_file_type', "doc,docx,pdf,txt,zip,rar,jpg,bmp,gif,png");
	$totalAttachFile = $javconfig['plugin']->get('total_attach_file',1);
	$max_size_attach_file	= $javconfig['plugin']->get('max_size_attach_file',1);
	$strTypeFile = JText::_("SUPPORT_FILE_TYPE").$attachFileType." ".JText::_("ONLY");		
	$arrTypeFile = explode(",", $attachFileType);			
	$strListFile = "";
	if ($arrTypeFile) {
		foreach ($arrTypeFile as $type){
			$strListFile .= "'$type',";
		}
		$strListFile .= '0000000';
	}	
	?>
	<script type="text/javascript">	
		v_array_type 	  		= [ <?php echo $strListFile;?> ];	
		error_type_file   		= "<?php echo $strTypeFile;?>";
		total_attach_file 		=	"<?php echo $totalAttachFile;?>";
		error_name_file   		= "<?php echo JText::_("FILE_NAME_IS_TOO_LONG");?>";
		max_size_attach_file 	= "<?php echo $max_size_attach_file;?>";  
	</script>
	<script type="text/javascript" src="components/com_javoice/asset/js/ja.upload.js"></script>	
<?php }?>
<?php
	//use for smiley - add style and javascript	 
	if($javconfig['plugin']->get('enable_smileys',1)){		
		$smiley = $javconfig['plugin']->get('smiley',"default");  
		require_once JPATH_SITE.'/components/com_javoice/views/items/tmpl/head.php';
	}
?>
<?php }//end layout form?>


<?php if(JRequest::getVar('search')==1){ $this->setLayout('default'); echo $this->loadTemplate('msg_votes');}?>
<?php
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
	<div id="jav-msg-error" style="display:none">
		<div class="jav-msg-error jav-notpermission">
			<?php echo JText::_("SORRY_YOU_CANT_POST_FOR_THIS_VOICE_TYPE" );?> 
		</div>
	</div>
	<?php if($this->votes_left==0) return ;?>
  <form method="post" id="new_item" name="new_item" enctype="multipart/form-data" class="new_item" action="index.php">
    <fieldset class='jav-fieldset'>
      <ol>
      	<li>
          <label for="voice_types_id">
            <?php echo JText::_('VOICE_TYPE')?>
          </label>
          <?php echo $this->types_option?>
        </li>
        
        <li class="ja-allrow">
          <label class="desc" for="title">
            <?php echo JText::_('TITLE')?> <span class="counter" id="title-counter"></span>
	          <span class='small'>
	          	(<em id="jav-charactersleft"><?php echo (100-strlen($this->key))?></em> <?php echo JText::_('CHARACTERS_LEFT')?>)
	          </span>            
          </label>
          <div>
          	<input type="text" onkeypress="jav_checkMaxLength(this, 100); return jav_handleEnter(this, event)"  value="<?php echo htmlspecialchars($this->key);?>" size="70" name="title" maxlength="100" id="title" class="text"/>
          </div>
          <div id="title-msg" class="error" style="display:none">
          	<?php echo JText::_('PLEASE_ENTER_TITLE_VALUE')?>
          </div>
        </li>
		<li class="ja-haftleft">
			<label class="desc" ><?php echo JText::_('FORUM')?></label>
			<div id="jav-forums">
				<?php echo $this->displayForums;?>
			</div>
	        <div id="forums_id-msg" class="error" style="display:none">
				 <?php echo JText::_('PLEASE_CHOOSE_FORUMS')?>              
	        </div>	
       				
		</li>
        <?php if($javconfig ['systems']->get ( 'is_use_vote', 1 )):?>
		<li class="ja-haftright">
			<label class="desc" for="votes"><?php echo JText::_('SPEND_HOW_MANY_VOTES')?></label>
			<div style='margin-left:5px;margin-top:2px;'>
				<?php echo $this->displayVotes;?>
			</div>
	        <div id="votes-msg" class="error" style="display:none">
				 <?php echo JText::_('PLEASE_CHOOSE_VOTES_FOR')?> <?php echo $this->type->title?>              
	        </div>	 							
		</li>
		<?php endif;?>
		
		<li class="ja-allrow">
			<div id="jav-post-new-voice">
				<ul class="form-comment">
					<li class="clearfix form-comment" id="jac-editor-addnew">
						<?php $this->setLayout('default')?>
						<?php echo $this->loadTemplate('editor');?>	
						<?php if($javconfig['plugin']->get('enable_bbcode',1) && $currentLayout== "form"){?>
							<script type="text/javascript">
								if(DCODE != undefined && jQuery("#newVoiceContent").length > 0){
									DCODE.setTags (["LARGE", "MEDIUM", "HR", "B", "I", "U", "S", "UL", "OL", "SUB", "SUP", "QUOTE", "LINK", "IMG", "YOUTUBE", "HELP"]);
									DCODE.activate ("newVoiceContent", false);
								}	
							</script>
						<?php }?>									 					        
					</li>
					<!--BEGIN  Upload form-->					
					<?php if($this->is_attach_image){?>					
					<li class="clearfix form-upload" style="display: none;" id="jav-form-upload">						
						<input type="hidden" name="jav-form-upload" value=""/>	
							<?php unset($_SESSION['javtemp']);unset($_SESSION['javnameFolder']);?>
							<div id="jav-upload" class="clearfix">
								<p class="error" id="jav_err_myfile"></p>
								<div class="jav-upload-form" style="float: left;">						
									<input name="myfile" id="myfile" type="file" size="30" <?php if($this->total_attach_file <= 0) echo 'disabled="disabled"';?> onchange="javStartUpload(1);" class="text" tabindex="5"/>
									<span id="jav_upload_process" class="jav-upload-loading" style="display: none;">
										<img src="<?php JURI::base()?>components/com_javoice/asset/images/loading.gif" alt="<?php echo JText::_("LOADING"); ?>" />
									</span>
									<div class="small"><?php echo JText::_("ATTACHED_FILE");?> (&nbsp;<?php echo JText::_("TOTAL");?> <?php echo $this->total_attach_file; ?> <?php if($this->total_attach_file>1){ echo JText::_("FILES__MAX_SIZE").'&nbsp;<b>'.$helper->getSizeUploadFile().'</b>';}else{ echo JText::_("FILE__MAX_SIZE").'&nbsp;<b>'.$helper->getSizeUploadFile().'</b>';}?>&nbsp;)</div>														
								</div>																																								
								<div id="jav_result_upload"></div>					
							</div>	
					</li>
					<?php }?>
					<!--END  Upload form-->		
				</ul>				
				<div id="err_exitchekspelling" class="error" style="display: none;"><?php echo JText::_("PLEASE_EXIT_SPELL_CHECK_BEFORE_SUBMITTING_VOICE");?></div>
				<div id="err_enter_description" class="error" style="display: none;"><?php echo JText::_("PLEASE_ENTER_DESCRIPTION");?></div>
			</div>											
		</li>		
		<?php 
			if($javconfig["systems"]->get("is_enable_tagging", 0)){
				$modelTags 	= JAVBModel::getInstance ( 'tags', 'javoiceModel' );							
				$listTags 	= $modelTags->getTagByVoice($this->Itemid);
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
					<div id="jav_tags_list">
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
			<!-- Expan form-->					
				<ul>								
					<?php if ($currentUserInfo->guest) {?>						
					<li class="clearfix">				
					<?php if($currentLayout == "form"):?>				
						<label class="description jav-title"><?php echo JText::_("POST_AS_A_GUEST_OR");?>&nbsp;<strong><a href='index.php' onclick="javascript:jaLoadLogin('index.php?option=com_javoice&view=users&layout=login&tmpl=component','<?php echo JText::_('LOGIN')?>','formlogin');return false;"><?php echo JText::_("LOGIN");?></a></strong></label>																				
					<?php else: ?>
						<label class="description jav-title"><?php echo JText::_("POST_AS_A_GUEST_OR");?>&nbsp;<strong><a href='index.php' onclick="javascript:jaLoadLogin('index.php?option=com_javoice&view=users&layout=login&tmpl=component','<?php echo JText::_('LOGIN')?>');return false;"><?php echo JText::_("LOGIN");?></a></strong></label>																					
					<?php endif; ?>
					<!--END TEXT LOGIN OR LOOUT-->								
					<!--BEGIN  Name, email, website-->
						
					<div id="other_field" class="clearfix">
						<div class="form-userdata clearfix">
							<!-- BEGIN TEXT NAME-->
							<span class="jac-form-guest">
								<label for="javGuestName"><?php echo JText::_("NAME");?> <span id="required_1" class="required">*</span></label>
								<input id="javGuestName" name="guest_name" maxlength="255" type="text" class="text" size="25" tabindex="2" title="<?php echo JText::_("DISPLAYED_NEXT_TO_YOUR_COMMENTS");?>"/>						
								<div id="err_javGuestName" class="error" style="display: none;"><?php echo JText::_("YOU_MUST_INPUT_NAME");?></div>											
							</span>
							<!-- END TEXT NAME-->
							<!-- BEGIN TEXT EMAIL-->
							<span class="jac-form-guest">
								<label for="javGuestEmail"><?php echo JText::_("EMAIL");?> <span id="required_2" class="required">*</span></label>
								<input id="javGuestEmail" name="guest_email" type="text" maxlength="100" class="text" value="" size="25" tabindex="3" title="<?php echo JText::_("NOT_DISPLAYED_PUBLICLY");?>"/>
								<div id="err_javGuestEmail" class="error" style="display: none;"><?php echo JText::_("YOU_MUST_INPUT_EMAIL");?></div>
								<div id="err_javGuestEmailInvalid" class="error" style="display: none;"><?php echo JText::_("YOUR_EMAIL_ADDRESS_IS_INVALID");?></div>											
							</span>
							<!-- END TEXT EMAIL-->							
						</div>
					</div>
					</li>											
					<?php }?>		
					<!--END  Name, email, website-->
					
					<!--END  Name, email, website-->
                    <?php if($javconfig["systems"]->get("use_anonymous",0)):?>
                    <li class="clearfix">
                    	<label for="use_anonymous" class="description jav-title"><?php echo JText::_("DISPLAY_ANONYMOUS_TEXT");?></label>
                        <input type="radio" id="use_anonymous_1" name="use_anonymous" value="1" /><label for="use_anonymous_1"><?php echo JTEXT::_("JYES");?></label>
                        <input type="radio" id="use_anonymous_0" name="use_anonymous" value="0" checked="checked"/><label for="use_anonymous_0"><?php echo JTEXT::_("JNO");?></label>    
                    </li>	
                    <?php endif;?>
                    <?php if($javconfig["systems"]->get("is_private",0)):?>
                    <li class="clearfix">
                    	<label for="is_private" class="description jav-title"><?php echo JText::_("DISPLAY_AS_PRIVATE_VOICE");?></label>
                        <input type="radio" id="is_private_1" name="is_private" value="1" /><label for="is_private_1"><?php echo JTEXT::_("JYES");?></label>
                        <input type="radio" id="is_private_0" name="is_private" value="0" checked="checked"/><label for="is_private_0"><?php echo JTEXT::_("JNO");?></label>    
                    </li>	
                    <?php endif;?>
					
					<?php if($isShowCaptcha){?>																			
					<li class="clearfix">
						<!-- BEGIN -  CAPTCHA -->								
						<div id="jav-new-captcha">
							<img alt="Captcha Image" onmousemove="actionLoadNewCaptcha('show')" onmouseout="actionLoadNewCaptcha()" onclick="loadNewCaptcha(0)" id="jav_image_captcha"  src="index.php?option=com_javoice&amp;task=displaycaptchaaddnew"/>
							<div id="jac-refresh-image" style="display: none;"><img alt="" src="components/com_javoice/asset/images/loading.gif" /></div>
							<div class="type_captcha" style="float: left;"><span><label for="javTextCaptcha"><?php echo JText::_("INPUT_CAPTCHA_TEXT_HERE");?><span id="required_4" class="required">*</span></label><input type="text" name="captcha" class="text" id="javTextCaptcha" tabindex="5" value=""/><div id="err_javTextCaptcha" class="error" style="display: none;"><?php echo JText::_("YOU_MUST_INPUT_TEXT_OF_CAPTCHA");?></div><div id="err_invalidjavTextCaptcha" class="error" style="display: none;"><?php echo JText::_("YOUR_CAPTCHA_IS_INVALID");?></div></span></div>						
						</div>					
						<!-- END -  CAPTCHA -->
					</li>
					<?php }?>														
			</ul>
			<ul></ul>
		</li>							
        
        <li style="padding-top: 4px;">        	        
        	<input type="button" onclick="jav_submitbutton()" value="<?php echo JText::_('POST')?>" name="commit" id="new_suggestion_submit" class="submit"/>
        	<img src="<?php echo JURI::base()?>components/com_javoice/asset/images/loading.gif" alt="" style="display:none; padding-left: 10px; z-index:1000" id="jav-form-wait" />
        </li>
        <li id="votes-msg" style="display:none">
			          
        </li>
      </ol>
    </fieldset>
    
    <input type="hidden" value="1" name="create_full"/>
    <input type="hidden" value="com_javoice" name="option"/>
    <input type="hidden" value="items" name="view"/>
    <input type="hidden" value="save" name="task" id="task"/>
    <input type="hidden" value="<?php echo $this->Itemid?>" name="Itemid"/>
    <?php echo JHTML::_( 'form.token' ); ?>
  </form>  
</div>

<?php if(JRequest::getVar('search')=='yes'){?>	
	
	<div id="jav-suggestion">
		
	</div>
	<?php foreach ($this->types as $type){?>
		<input type="hidden" name="votes-left-<?php echo $type->id?>" id="votes-left-<?php echo $type->id?>" value="<?php echo $this->model->getVotes_left();?>" />
	<?php }?>
<?php }?>
<iframe id="upload_target" name="upload_target" style="width:0;height:0;border:0px solid #fff;"></iframe>