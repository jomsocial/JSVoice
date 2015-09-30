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
?>
<?php 
global $javconfig;
$types = $this->types;
$is_show_rpx = $javconfig['systems']->get('is_show_rpx',0);
$Itemid = JRequest::getInt('Itemid');
if(!$Itemid) $Itemid = JAVoiceHelpers::get_Itemid(array('option'=>'com_javoice', 'view'=>'items'));

$url_login = JRoute::_("index.php?option=com_javoice&view=items&task=ja_login&tmpl=component");
$helper = new JAVoiceHelpers();
if($types){?>
	<?php
		$type_default = $this->type;
		$jav_tab_active = 0;
		$k = 0;
		foreach ($types as $_type){
			if($_type->id==JRequest::getInt('type')){
				$type_default = $_type;
				$jav_tab_active = $k;				
			}
			$k++;
		}		
	?>
	<script type="text/javascript" charset="utf-8">
	//<![CDATA[
		jQuery(document).ready(function($){	
			jav_init();	
			jav_createTabs('#javtabs-main', '<?php echo JURI::base();?>index.php?option=com_javoice&view=items&layout=items&limitstart=0&tmpl=component&Itemid='+<?php echo $Itemid?>+'&type=');
			$("jav-forum-select").value = 0;
<?php 	if($javconfig["systems"]->get("paging_type","normal") == "autoscroll"):?>			
			javqueueListenerScroll();
<?php endif;?>						
		});			
	
		var jav_base_url = '<?php echo JURI::base()?>';
		var jav_vote_total = <?php echo $this->total_votes_left==-1?1000:$this->total_votes_left;?>;
		var jav_dont_permission = "<?php echo JText::_('YOU_DONT_PERMISSION')?>";
		var jav_many_vote = '<?php echo JText::_('HOW_MANY_VOTES')?>';
		var jav_run_out = "<?php echo JText::_("YOUVE_RUN_OUT_OF_VOTE")?>" +  "[<a onclick='jav_showNoticeToCenter(400, 120, \"jav-dialog\"); return false;' href='javascript:void(0)'>?</a>]";
		var jav_tab_active = <?php echo $jav_tab_active?>;
		var jav_option_type = <?php echo $type_default->id?>;
		var siteurl = '<?php echo JURI::base();?>index.php?option=com_javoice&view=items&tmpl=component&Itemid=<?php echo $Itemid?>';
		var jav_base_url_login = '<?php echo $this->base_url;?>';
		var jav_ajax_url_login = '<?php echo $url_login;?>';
		var jav_current_active_voice = 0;
		var jav_delete_session_upload = 1;
		var jav_text_checkspelling_no_error = '<?php echo JText::_("NO_WRITING_ERRORS_WERE_FOUND");?>';
		var jav_minimum_search = <?php echo $javconfig['systems']->get('minimum_search_num',4);?>;
		var jav_textarea_cursor = -1;
		var jav_process_ajax = 0;
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
	<?php }?>	
<?php if($javconfig['plugin']->get('enable_bbcode',1)){?>
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

<?php if($javconfig['plugin']->get('is_attach_image',0)){	
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
	if($javconfig['plugin']->get('enable_smileys',0)){		
		$smiley = $javconfig['plugin']->get('smiley',"default");  
		require_once JPATH_SITE.DS.'components'.DS.'com_javoice'.DS.'views'.DS.'items'.DS.'tmpl'.DS.'head.php';
	}
?>	  	
<!-- Path way -->
<?php if($types){?>

<div class="jav-wrapper-pathway">
	<div class="jav-pathway-top">
		<?php foreach ($types as $k=>$type){ ?>
		<div id="jav-pathway-<?php echo $type->id?>" class="jav-pathway-main" <?php echo $k != 0 ? 'style="display:none"' : '' ?>> 
			<?php if($type_default->id==$type->id) echo $this->getPatway();?>
		</div>
		<?php }?>
	</div>
	<div style="right:0px;">
		<a href="<?php echo Jroute::_("index.php?option=com_javoice&view=feeds&layout=list&Itemid=".$Itemid);?>">
			<?php echo Jtext::_("Voices via RSS");?>
			<img src="<?php echo Juri::root()?>components/com_javoice/asset/images/rss.gif" alt="RSS help"/>
		</a>
	</div>
</div>
<?php }else{
	echo JText::_("PLEASE_CREATE_NEW_FORUM_AND_VOICE_TYPE");
}?>


<!--< New Loading Image and Text >-->
<!-- Loading -->
<div id="loader">
	<img src="<?php echo Juri::root()?>components/com_javoice/asset/images/loading.gif" alt="<?php echo JText::_("LOADER_TITLE");?>"/>
	<?php echo '<br>'; ?>
	<?php echo JText::_('LOADING')?>
</div>
<!--< End New Loading Image and Text >-->

<div id="jav-msg-loading" style="display:none"></div>
<input type="hidden" name='jav-forum-select' id='jav-forum-select' value='0'/>

<?php $this->setLayout('default');echo $this->loadTemplate('msg_votes');?>
<div id="jav-msg-succesfull" style="display:none">
	<?php if(isset($_REQUEST['aa48e4026862bea433858a250d86ba8c'])){?>
		<script type="text/javascript">
			displaymessage();
		</script>
	<?php }?>
	<div id = 'jav-msg-title'>
		<?php 
		$user = JFactory::getUser();
		if($user->id) $msg = JText::_("VOTED_WELL_TEXT"). ' <a href="'.JRoute::_('index.php?option=com_javoice&view=users&uid'.$user->id).'" title="'.JText::_('CLICK_HERE_TO_GO_TO_EMAIL_PREFERENCE').'">'.JText::_("EMAIL_YOU").'</a> ' .JText::_('WHEN_YOUR_VOICE_IS_HEARD_STATUS_CHANGE');
		else $msg = JText::_('VOTED'). ' <a href="'.JRoute::_('index.php?option=com_users&view=login').'" title="'.JText::_('CLICK_HERE_TO_LOGIN').'">'.JText::_("LOGIN").'</a> '. 
			JText::_('OR') . ' <a href="'.JRoute::_('index.php?option=com_users&view=registration').'" title="'.JText::_('CLICK_HERE_TO_SIGN_UP').'">'.JText::_("SIGN_UP").'</a> '.JText::_('SO_THAT_WE_CAN_LET_YOU_KNOW_WHEN_YOUR_VOICE_IS_HEARD_STATUS_CHANGED');
		echo $msg;
		?>
	</div>
	<div id='jav-msg-close'><a href="javascript:closemessage()"><?php echo Jtext::_("Close"); ?></a></div>
</div>


<!-- Popup create voice -->
<div id="jav-create-item" style="display: none">	
</div>
<?php //echo JRoute::_("index.php?option=com_javoice&view=items&type={$type->id}&layout=items&limitstart=0&tmpl=component"); ?>
<?php if($javconfig["systems"]->get("is_enable_tagging", 0) && JRequest::getVar("tagid",0)):?>	
<h3 class="jav-text-tag"><?php echo JText::_("DISPLAY_VOICE_BY_TAG")." <span>".$this->getTagName(JRequest::getVar("tagid",0))."</span>";?></h3>
<?php endif;?>
<!-- Main box -->
<div id="javtabs-main" class="javtabs-mainwrap clearfix">	
	<ul class="javtabs-title">
		<?php if($types){?>
			<?php foreach ($types as $k=>$type){?>				
				<li title="<?php echo $type->title;?>" class="jav-mainbox-<?php echo $type->id?> <?php if($k==0) echo 'first'; elseif($k==count($types)-1) echo 'last';?>" id="jav-typeid_<?php echo $type->id;?>">
					<a href="javascript:void(0)" class="jav-mainbox-<?php echo $type->id?>"><?php echo $type->title;?></a>					
				</li>
			<?php }?>		
		<?php }?>
	</ul>
	<?php if($types){?>
	<div class="javtabs_container">
		<?php foreach ($types as $k=>$type){ ?>
		<div class="javtabs-panel" id="jav-mainbox-<?php echo $type->id?>">
		
			<!-- BEGIN: Left Column -->
						
			<div id="jav-col-left-<?php echo $type->id?>" class="jav-col1">		
				<div class="jav-welcome">
				  <p class="message"><?php echo  str_replace("\n", "<br />", trim($type->description));?></p>
				</div>
				
				<div class="jav-search">
					<?php $link  = JURI::base() . 'index.php?option=com_javoice&view=items&type='.$type->id.'&layout=search&tmpl=component&Itemid='.$Itemid.'&forums='?>
                   	<?php if($javconfig["systems"]->get("is_enable_tagging",0) && JRequest::getVar("tagid",0)): ?>
                    	<form name="jav-search-form-<?php echo $type->id?>" action="index.php" method="get" onsubmit="if(	$('key-<?php echo $type->id?>').value!='<?php echo addslashes($type->search_description);?>' && 	$('key-<?php echo $type->id?>').value.length>0) {jav_findWord(event,$('key-<?php echo $type->id?>'), '<?php echo $link?>', '<?php echo $type->id?>', 0	); } else { $('key-<?php echo $type->id?>').addClass('input_error'); } return false;">
					
					  	<span class="jav-search-title"><?php echo $type->search_title?></span>
						<div class="jav-search-field">				
				  			<input type="text" size="50" id="key-<?php echo $type->id?>" name="key-<?php echo $type->id?>" maxlength="100" class="inputbox"  onkeyup="jav_findWord(event, this, '<?php echo $link?>', '<?php echo $type->id?>'); return false;"  onfocus="if(this.value=='<?php echo trim(addslashes($type->search_description));?>') this.value='';" onblur="if(this.value=='') this.value='<?php echo trim(addslashes($type->search_description))?>';" value="<?php echo trim(addslashes($type->search_description))?>"/>
				  			<?php $button = $type->search_button?$type->search_button:Jtext::_('Search');?>
							<input type="submit" value="<?php echo $button?>" name="submit-<?php echo $type->id?>" class="button submit-search" />
							<img class="search-loading" style="display: none" src="<?php echo JURI::base();?>components/com_javoice/asset/images/loading-small.gif" alt="<?php echo JText::_('LOADING')?>"/>
				      	</div>
							    
		  				</form>
					<?php else: ?>
                    	<form name="jav-search-form-<?php echo $type->id?>" action="index.php" method="get" onsubmit="if(	$('key-<?php echo $type->id?>').value!='<?php echo addslashes($type->search_description);?>' && 	$('key-<?php echo $type->id?>').value.length>0) {jav_findWord(event,$('key-<?php echo $type->id?>'), '<?php echo $link?>'+ $('forums-<?php echo $type->id?>').value, '<?php echo $type->id?>', 0	); } else { $('key-<?php echo $type->id?>').addClass('input_error'); } return false;">
					
					  	<span class="jav-search-title"><?php echo $type->search_title?></span>
						<div class="jav-search-field">				
				  			<input type="text" size="50" id="key-<?php echo $type->id?>" name="key-<?php echo $type->id?>" maxlength="100" class="inputbox"  onkeyup="jav_findWord(event, this, '<?php echo $link?>'+ $('forums-<?php echo $type->id?>').value, '<?php echo $type->id?>'); return false;"  onfocus="if(this.value=='<?php echo trim(addslashes($type->search_description));?>') this.value='';" onblur="if(this.value=='') this.value='<?php echo trim(addslashes($type->search_description))?>';" value="<?php echo trim(addslashes($type->search_description))?>"/>
				  			<?php $button = $type->search_button?$type->search_button:JText::_('SEARCH');?>
							<input type="submit" value="<?php echo $button?>" name="submit-<?php echo $type->id?>" class="button submit-search" />
							<img class="search-loading" style="display: none" src="<?php echo JURI::base();?>components/com_javoice/asset/images/loading-small.gif" alt="<?php echo JText::_('LOADING')?>"/>
				      	</div>
							    
		  				</form>
                    <?php endif;?>	  					  				
				</div>
    			
	    		<div class="jav-search-result clearfix" <?php if(!isset($this->formcreatenew)){?>style="display:none;"<?php }?>><?php if(isset($this->formcreatenew)){echo $this->formcreatenew;}?></div>
	    		
	    		<!-- LOAD LIST OF OPTIONS -->
		    	<div class="jav-list-options" id="jav-list-options-<?php echo $type->id?>"> 
		    		<?php if($type_default->id==$type->id){?>
	    				<?php echo $this->getOptions()?>
	    			<?php }?>
	    			
		    	</div>
		    	<!-- //LOAD LIST OF OPTIONS -->
	    	
		    	<!-- LOAD LIST OF ITEMS -->
		    	<div id="jav-list-items-<?php echo $type->id?>" class="jav-list-items"> 
	    			<?php if($type_default->id==$type->id) { ?>
	    				<?php echo $this->getItems()?>
	    			<?php }?>
		    	</div>
					<!-- //LOAD LIST OF ITEMS -->
				<?php if($javconfig["systems"]->get("paging_type","normal") == "normal"):?>
					<!-- LOAD PAGING -->
		    	<div id="jav-pagination-<?php echo $type->id?>" class="jav-pagination"> 		    		
	    			<?php if($type_default->id==$type->id){?>
	    				<?php echo $this->getPaging($type->id)?>
	    			<?php }?>
		    	</div>
					<!-- //LOAD PAGING -->
				<?php endif;?>
			</div>
			<!-- //COL1 -->
			
			<!-- COL2 -->
			<div id="jav-col-right-<?php echo $type->id?>" class="jav-col2">
				<div class="jav-innerpad">
					
					<!-- Username and Logout button -->
					
					<?php 
					if($this->enale_form_login && isset($this->user->id) && $this->user->id>0){?>
					<div class="moduletable_menu">									
						<div style="right: 0;">
							<?php echo JText::_('HI')?> <a href="<?php echo JRoute::_('index.php?option=com_javoice&view=users&uid='.$this->user->id.'&Itemid='.$Itemid)?>"><?php echo $this->user->username?></a> | 
							<a href="<?php echo JRoute::_("index.php?option=com_users&task=user.logout&". JSession::getFormToken() . "=1&return={$this->base_url}");?>" title="<?php echo JText::_('LOGOUT'); ?>"><?php echo JText::_('LOGOUT'); ?></a>
						</div>
					</div>
					<?php }?>
					<!-- End-->
					<?php if($javconfig["systems"]->get("is_use_vote",1)):?>
					<div class="jav-has-votes">
						<h4>
							<strong id="jav-points-remaining-<?php echo $type->id?>">
								<?php if($type_default->id==$type->id){?><?php echo $this->show_votes_left?><?php }?>
							</strong>
							<?php if(is_int($this->show_votes_left) && $this->show_votes_left<2){?>
								<?php echo JText::_('VOTE_LEFT')?>
							<?php }else{?>
								<?php echo JText::_('VOTES_LEFT')?>
							<?php }?>
						</h4>
						<ul class="jav-help">
							<li>
								<a onclick="jav_showNoticeToCenter(400, 120, 'jav-dialog'); return false" href="javascript:void(0)"><?php echo JText::_('WHAT_HAPPENS_IF_I_RUN_OUT')."?";?></a>
							</li>
						</ul>
					</div>
					<?php endif; ?>
                    <?php if($javconfig["plugin"]->get("enable_your_items",0)):?>
					<div class="moduletable_menu jav-list-your-ideas">
						<?php if($type_default->id==$type->id) { ?>
							<?php echo $this->getYourItems()?>
						<?php }?>
					</div>
					<?php endif;?>
					<div class="moduletable_menu jav-list-forums" id="jav-list-forums-<?php echo $type->id?>">
						<?php if($type_default->id==$type->id) { ?>
							<?php echo $this->getForums()?>
						<?php }?>
					</div>
					
					<?php 
					if($this->enale_form_login && !isset($this->user->id)){?>
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
						
								
					<input type="hidden" name="votes-left-<?php echo $type->id?>" id="votes-left-<?php echo $type->id?>" value="<?php if($type_default->id==$type->id){ echo $this->total_votes_left;}?>" />
				
				</div>
			</div>
			<!-- //COL2 -->
			
		</div>
		<?php }?>
  	</div>
  	<?php }?>
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
					<?php echo $this->loadTemplate('editor');?>										 					        
				</li>
				<!--BEGIN  Upload form-->					
				<?php if($this->is_attach_image){?>
				<li class="clearfix form-upload" style="display: none;" id="jav-form-upload-reply">
						<?php unset($_SESSION['javReplyTemp']);unset($_SESSION['javReplyNameFolder']);?>
						<div id="jac-reply-upload" class="clearfix">
							<p class="error" id="jav_err_myfilereply"></p>
							<div style="float: left;">						
								<input name="myfile" id="myfilereply" type="file" size="30" <?php if($this->total_attach_file <= 0) echo 'disabled="disabled"';?> onchange="javStartReplyUpload();" class="field file" tabindex="5"/>
								<span id="jav_reply_upload_process" class="jav-upload-loading" style="display: none;">
									<img src="components/com_javoice/asset/images/loading.gif" alt="<?php echo JText::_("LOADING"); ?>" />
								</span>
								<div class="small"><?php echo JText::_("ATTACHED_FILE");?> (<?php echo JText::_("TOTAL");?> <?php echo $this->total_attach_file; ?> <?php if($this->total_attach_file>1){ echo JText::_("FILES__MAX_SIZE").'�<b>'.$helper->getSizeUploadFile().'</b>';}else{ echo JText::_("FILE__MAX_SIZE").'�<b>'.$helper->getSizeUploadFile().'</b>';}?>)</div>														
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
<iframe id="upload_target" name="upload_target" style="width:0;height:0;border:0px solid #fff;"></iframe>