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
$helper   = new JAVoiceHelpers();
$listFile = $helper-> preloadfile($this->item->id, "adminresponse");
?> 
<?php if($this->is_attach_image){?>
 <form name="adminForm" id="adminForm" action="index.php" method="post" enctype="multipart/form-data" action="index.php?tmpl=component&option=com_javoice&view=items&task=uploadReplyFile"> 
<?php }else{?>
 <form name="adminForm" id="adminForm" action="index.php" method="post">
<?php }?>
 	<div id="jav-reply-voice">
	 	<input type="hidden" name="option" value="com_javoice" /> 
	 	<input type="hidden" name="view" value="items" /> 
	 	<input type="hidden" name="task" value="saveResponse" /> 
	 	<input type="hidden" name="tmpl" value="component" /> 
	 	<input type="hidden" name='id' id='id' value="<?php echo $item->id?>"/> 
	 	<input type="hidden" name='cid[]' id='cid[]' value="<?php echo $item->id?>"/>
	 	<input type="hidden" name="user_id" value="<?php echo $item->user_id;?>"/>
		<input type="hidden" id='editor' name="editor" value="#response"/>
		
		<ul>
			<li class="ja-haftleft" style="width:70%">
				<label class="desc" for="title"><?php echo JText::_("VOICE_TITLE" );?>:</label>
				<div><?php echo $item->item_title;?></div>
			</li>
			<li class="ja-haftright" style="width:25%">
				<label class="desc" for="title"><?php echo JText::_("RESPONSE_BY" );?>:</label>
				<div><?php echo $item->responsename;?></div>
			</li>							
			<li>
				<div id="jav-reply-voice">
				<label class="desc" for="title"><?php echo JText::_("RESPONSE" );?>:</label>
				<div id="jav-reply-new-comment">
					<ul class="form-comment">
						<li class="clearfix form-comment" id="jav-reply-editor">
							<?php
								$this->setLayout("default"); 
								echo $this->loadTemplate('editor');
							?>										 					        
						</li>
						<!--BEGIN  Upload form-->					
						<?php if($this->is_attach_image){?>
						<li class="clearfix form-upload" style="display: none;" id="jav-form-upload-reply">
							<input type="hidden" name="jav-form-upload-reply" value=""/>	
							<?php unset($_SESSION['javReplyTemp']);unset($_SESSION['javReplyNameFolder']);?>
								<div id="jac-reply-upload" class="clearfix">
									<p class="error" id="jav_err_myfilereply"></p>
									<div>						
										<input name="myfile" id="myfilereply" type="file" size="30" <?php if($this->total_attach_file <= 0 || (substr_count($listFile, "input") >=$this->total_attach_file)) echo 'disabled="disabled"';?> onchange="javStartAdminReplyUpload();" class="field file" tabindex="5"/>
										<span id="jav_reply_upload_process" class="jav-upload-loading" style="display: none;">
											<img src="components/com_javoice/asset/images/loading.gif" alt="<?php echo JText::_("LOADING"); ?>" />
										</span>
										<div class="small"><?php echo JText::_("ATTACHED_FILE");?> (&nbsp;<?php echo JText::_("TOTAL");?> <?php echo $this->total_attach_file; ?> <?php if($this->total_attach_file>1){ echo JText::_("FILES__MAX_SIZE").'&nbsp;<b>'.$helper->getSizeUploadFile().'</b>';}else{ echo JText::_("FILE__MAX_SIZE").'&nbsp;<b>'.$helper->getSizeUploadFile().'</b>';}?>&nbsp;)</div>														
									</div>																																								
									<div id="jav_result_reply_upload">
									<?php echo $listFile;?>
									</div>					
								</div>	
						</li>
						<?php }?>
						<!--END  Upload form-->		
					</ul>
					<div id="err_emptyreplyvoice" class="error" style="display: none;"><?php echo JText::_("YOU_MUST_INPUT_CONTENT");?></div>				
					<div id="err_exitchekspellingReply" class="error" style="display: none;"><?php echo JText::_("PLEASE_EXIT_SPELL_CHECK_BEFORE_SUBMITTING_VOICE");?></div>																										
				</div>
				</div>
			</li>	
		</ul>
	</div>	
 </form>