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
	global $javconfig;
	$document = JFactory::getDocument();
	$sytem_comment = $javconfig['integrate']->get('run_system', 'intensedebate');
	$is_show_bookmark_share = $javconfig['plugin']->get('is_show_bookmark_share',1);
	$type = isset($this->type)?$this->type :null;
	if( !JAVoiceHelpers::checkPermissionAdmin()) $has_answer = 0;
	else $has_answer = $type->has_answer;	
	$helper = new JAVoiceHelpers();
	
?>

<script type="text/javascript" charset="utf-8">
//<![CDATA[
	var jav_base_url = '<?php echo JURI::base()?>';
	var jav_vote_total = <?php echo $this->total_votes_left==-1?1000:$this->total_votes_left;?>;
	var jav_dont_permission = "<?php echo JText::_("YOU_DONT_PERMISSION")?>";
	var jav_many_vote = '<?php echo JText::_('HOW_MANY_VOTES')?>';
	var jav_best_answer = '<?php echo JText::_('BEST_ANSWER')?>';
	var jav_time_delay = 5000;
	var jav_option_type = <?php echo $type->id?>;
	var jav_cid = "<?php echo isset($_GET['cid']) ? intval($_GET['cid']) : 0;?>";
	var siteurl = '<?php echo JURI::base();?>index.php?option=com_javoice&view=items&tmpl=component&Itemid=<?php echo $Itemid?>';
	var jav_max_comment = 5;
	var dsq = "<?php echo isset($_GET['dsq']) ? intval($_GET['dsq']) : 0;?>";
	/*Call change*/
	jav_init();	
	jav_changeComment('<?php echo $sytem_comment;?>', <?php echo $has_answer;?>);
	var jav_current_active_voice = 0;
	var jav_delete_session_upload = 1;
	var jav_text_checkspelling_no_error = '<?php echo JText::_("NO_WRITING_ERRORS_WERE_FOUND");?>';
	var jav_minimum_search = <?php echo $javconfig['systems']->get('minimum_search_num',4);?>;
	var jav_textarea_cursor = -1;
	var jav_enable_after_the_deadline = <?php echo $javconfig['plugin']->get('enable_after_the_deadline',1);?>;
	<?php if($javconfig["systems"]->get("is_enable_tagging", 0)):?>		
	var jav_tag_config = {
			tag_minimum_length:<?php echo $javconfig['systems']->get('tag_minimum_length',4);?>,
			tag_maximum_length:<?php echo $javconfig['systems']->get('tag_maximum_length',4);?>,
			tag_maximum_per_thread:<?php echo $javconfig['systems']->get('tag_maximum_per_thread',4);?>,
			characters_separating_tags:'<?php echo $javconfig['systems']->get('characters_separating_tags',',');?>'
	};
	<?php if(JRequest::getVar("tagid",0)): ?>
		jav_current_tag = <?php echo JRequest::getVar("tagid",0);?>;
	<?php endif; ?>
	<?php endif;?>
//]]>
</script>
<?php if($javconfig['plugin']->get('enable_bbcode',1)){
	$document->addScript('components/com_javoice/asset/js/dcode/dcodr.js');
	$document->addScript('components/com_javoice/asset/js/dcode/dcode.js');
 }?>
<?php if($javconfig['plugin']->get('enable_after_the_deadline',1)){
	$document->addScript('components/com_javoice/asset/js/atd/atd.js');
	$document->addScript('components/com_javoice/asset/js/atd/csshttprequest.js');
	$document->addScript('components/com_javoice/asset/js/atd/jquery.atd.js');
	JHTML::stylesheet('components/com_javoice/asset/js/atd/atd.css'); ?>	
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
		foreach ($arrTypeFile as $typeFile){
			$strListFile .= "'$typeFile',";
		}
		$strListFile .= '0000000';
	}	
	?>
	<script type="text/javascript">	
		var v_array_type 	  		= [ <?php echo $strListFile;?> ];	
		var error_type_file   		= "<?php echo $strTypeFile;?>";
		var total_attach_file 		=	"<?php echo $totalAttachFile;?>";
		var error_name_file   		= "<?php echo JText::_("FILE_NAME_IS_TOO_LONG");?>";
		var max_size_attach_file 	= "<?php echo $max_size_attach_file;?>";  
	</script>
	<script type="text/javascript" src="components/com_javoice/asset/js/ja.upload.js"></script>	
<?php }?>
<?php
	//use for smiley - add style and javascript	 
	if($javconfig['plugin']->get('enable_smileys',1)){		
		$smiley = $javconfig['plugin']->get('smiley',"default");  
		require "components/com_javoice/views/items/tmpl/head.php";
	}
	
?>	  
<div id="loader">
	<?php echo JText::_('LOADING')?>
</div>

<?php $this->setLayout('default'); echo $this->loadTemplate('msg_votes');?>
<div id="jav-msg-succesfull" style="display:none">
	<?php if(isset($_REQUEST['aa48e4026862bea433858a250d86ba8c'])){?>
		<script type="text/javascript">
			displaymessage();
		</script>
	<?php }?>
	<div id = 'jav-msg-title'>
		<?php 
		$user = JFactory::getUser();
		if($user->id) $msg = JText::_("VOTED_WELL_TEXT"). ' <a href="'.JRoute::_('index.php?option=com_javoice&amp;view=users&uid='.$user->id).'" title="'.JText::_('CLICK_HERE_TO_GO_TO_EMAIL_PREFERENCE').'">'.JText::_("EMAIL_YOU").'</a> ' .JText::_('WHEN_YOUR_VOICE_IS_HEARD_STATUS_CHANGE');
		else $msg = JText::_('VOTED'). ' <a href="'.JRoute::_('index.php?option=com_users&amp;view=login').'" title="'.JText::_('CLICK_HERE_TO_LOGIN').'">'.JText::_("LOGIN").'</a> '. 
			JText::_('OR') . ' <a href="'.JRoute::_('index.php?option=com_users&amp;view=registration').'" title="'.JText::_('CLICK_HERE_TO_SIGN_UP').'">'.JText::_("SIGN_UP").'</a> '.JText::_('SO_THAT_WE_CAN_LET_YOU_KNOW_WHEN_YOUR_VOICE_IS_HEARD_STATUS_CHANGE');
		echo $msg;
		?>
	</div>
	<div id='jav-msg-close'><a href="javascript:closemessage()"><?php echo Jtext::_("Close"); ?></a></div>	
</div>
<div id="jav-pathway">
	<?php echo $this->getPatway();?>
</div>

<div id="javtabs-main" class="javtabs-mainwrap">
	
	<!-- JAV COL1 -->
	<div class="jav-col1">
		<div style="float: right">
			<?php  if($this->items && ($javconfig ['plugin']->get ( 'enable_addthis' ) || $javconfig ['plugin']->get ( 'enable_tweetmeme', 1 ) || $javconfig ['plugin']->get ( 'enable_addtoany', 1 ))){
			?>				
					<?php if($javconfig ['plugin']->get ( 'enable_addthis' )){?>
						<div style="float: left;"><?php echo $this->getAddThis(); ?></div>
					<?php }?>					
					<?php if($javconfig ['plugin']->get ( 'enable_addtoany' )){?>
						<div style="float: left;"><?php echo $this->getAddToAny(); ?></div>
					<?php }?>
			<?php												
			}?>
		</div>
		
		<!-- BEGIN: ITEM DETAILS -->	
		<div id="jav-list-items-<?php echo $type->id?>" class="jav-list-items">
			<?php $this->setLayout('default')?> 		
			<?php echo JAVBView::loadTemplate('items')?>
		</div>
		<!-- END: LIST OF ITEM -->		
		<?php if(!JRequest::getVar("tagid") && ($javconfig["systems"]->get("is_private",0) == 0 || $this->items[0]->is_private == 0 || $user->id == $this->items[0]->user_id || JAVoiceHelpers::checkPermissionAdmin())):?>
		<!-- BEGIN: LIST OF COMMENT -->
		<a name="jav-comment"></a>
		<div id="jav-list-comment">
			<?php if($this->items) echo JAVBView::loadTemplate('comment')?>
		</div>
		<!-- END: LIST OF COMMENT -->
		<?php endif; ?>
	</div>
	<!-- //JAV COL1 -->
			
	<!-- JAV COL2 -->
	<div class="jav-col2">
		<div class="jav-innerpad">
			<!-- Username and Logout button -->
			<?php if($this->enale_form_login && $this->user->id>0){?>
			<div class="moduletable_menu">									
				<div style="right: 0;">
					<?php echo JText::_('HI')?> <a href="<?php echo JRoute::_('index.php?option=com_javoice&view=users&uid='.$this->user->id.'&amp;Itemid='.$Itemid)?>"><?php echo $this->user->username?></a> | 
					<a href="<?php echo JRoute::_("index.php?option=com_users&task=user.logout&". JSession::getFormToken() . "=1&return={$this->base_url}");?>" title="<?php echo JText::_('LOGOUT'); ?>"><?php echo JText::_('LOGOUT'); ?></a>
				</div>
			</div>
			<?php }?>
			<!-- End-->
			<?php if($javconfig["systems"]->get("is_use_vote",1)):?>				
			<div id="jav-vote-left" class="jav-has-votes">
				<h4>						
					<strong id="jav-points-remaining-<?php echo $type->id?>"><?php echo $this->show_votes_left?></strong>
					<?php echo JText::_('VOTES_LEFT')."!";?>
				</h4>
					<ul class="jav-help">
					<li>
						<a onclick="$('jav-dialog').setStyle('display', 'block'); return false" href="javascript:void(0)"><?php echo JText::_('WHAT_HAPPENS_IF_I_RUN_OUT')."?";?></a>
					</li>
				</ul>
			</div>
			<?php endif; ?>
			<?php 
			if($this->enale_form_login && !$this->user->id){?>
			<div class="loginform moduletable_menu">
				<h3><span><?php echo JText::_('MEMBERS_LOGIN')?></span></h3>
				<?php if($this->enable_login_form_type==2){
					$plugin = JPluginHelper::getPlugin ( 'system', 'janrain' );
					if ( $plugin) {
						echo '{janrain}';
					}
					else{
						echo JText::_('JA_RPXNOW_PLUGIN_NOT_BE_INSTALLED');
					}
				}
				else{
					echo $this->loadTemplate('login');
				}?>
			</div>
			<?php }?>
		</div>
	</div>
	<!-- //JAV COL2 -->
	
	<input type="hidden" name="votes-left-<?php echo $type->id?>" id="votes-left-<?php echo $type->id?>" value="<?php echo $this->total_votes_left;?>" />
</div>
<div id="jav-form-reply" style="display: none;">
	<ol>
	<li>
	<div id="jav-reply-voice">	
  	<?php if($this->is_attach_image){?>
	<form method="post" id="new_reply_item" name="new_reply_item" class="new_item" enctype="multipart/form-data" action="index.php?tmpl=component&option=com_javoice&view=items&task=uploadReplyFile">
	<?php unset($_SESSION['javReplyId']);unset($_SESSION['javReplyTemp']);unset($_SESSION['javReplyNameFolder']);?>
	<?php }else{?>
	<form method="get" id="new_reply_item" name="new_reply_item" class="new_item">	
	<?php }?>		
		<div id="jav-reply-new-comment">
			<ul class="form-comment">
				<li class="clearfix form-comment" id="jav-reply-editor">
					<?php 
						$this->setLayout ( 'default' );
						echo $this->loadTemplate('editor');
						$this->setLayout ( 'item' );					
					?>										 					        
				</li>
				<!--BEGIN  Upload form-->					
				<?php if($this->is_attach_image){?>				
				<li class="clearfix form-upload" style="display: none;" id="jav-form-upload-reply">
						<?php unset($_SESSION['javtemp']);unset($_SESSION['javnameFolder']);?>
						<div id="jav-reply-upload" class="clearfix">
							<p class="error" id="jav_err_myfilereply"></p>
							<div style="float: left;">						
								<input name="myfile" id="myfilereply" type="file" size="30" <?php if($this->total_attach_file <= 0) echo 'disabled="disabled"';?> onchange="javStartReplyUpload();" class="field file" tabindex="5"/>
								<span id="jav_reply_upload_process" class="jav-upload-loading" style="display: none;">
									<img src="components/com_javoice/asset/images/loading.gif" alt="<?php echo JText::_("LOADING"); ?>" />
								</span>
								<div class="small"><?php echo JText::_("ATTACHED_FILE");?> (&nbsp;<?php echo JText::_("TOTAL");?> <?php echo $this->total_attach_file; ?> <?php if($this->total_attach_file>1){ echo JText::_("FILES__MAX_SIZE").'&nbsp;<b>'.$helper->getSizeUploadFile().'</b>';}else{ echo JText::_("FILE__MAX_SIZE").'&nbsp;<b>'.$helper->getSizeUploadFile().'</b>';}?>&nbsp;)</div>														
							</div>																																								
							<div id="jav_result_reply_upload"></div>					
						</div>	
				</li>
				<?php }?>
				<!--END  Upload form-->		
			</ul>
			<div id="err_emptyreplyvoice" class="error" style="display: none;"><?php echo JText::_("YOU_MUST_INPUT_CONTENT");?></div>				
			<div id="err_exitchekspellingReply" class="error" style="display: none;"><?php echo JText::_("PLEASE_EXIT_SPELL_CHECK_BEFORE_SUBMITTING_VOICE");?></div>
			<!-- Expan form-->
			<input type="hidden" name="deleteSession" id="javhd_deleteSession" value="1"/>
			<input type="hidden" name="responeid" id="hd_respone_id" value="0"/>																		
		</div>					    	    			
	</form>
	</div>
	</li>
	</ol>
</div>
<!-- END: Left Column -->
<iframe id="upload_target" name="upload_target" style="width:0;height:0;border:0px solid #fff;"></iframe>