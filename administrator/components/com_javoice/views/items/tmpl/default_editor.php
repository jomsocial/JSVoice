<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );
$contentComment = "";
global $javconfig;
if(isset($this->item)){
	$contentComment = $this->item->content;	
}
$prefix	= "";
if(isset($this->textAreaID)){
	$prefix = "Reply";
}
?>
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
		var v_array_type 	  		= [ <?php echo $strListFile;?> ];	
		var error_type_file   		= "<?php echo $strTypeFile;?>";
		var total_attach_file 		=	"<?php echo $totalAttachFile;?>";
		var error_name_file   		= "<?php echo JText::_("FILE_NAME_IS_TOO_LONG");?>";
		var max_size_attach_file 	= "<?php echo $max_size_attach_file;?>";  
	</script>		
<?php }?>	          	    
<?php if($javconfig['plugin']->get('is_attach_image',1)){?>	
	<?php JHTML::_('script', 'ja.upload.js',JURI::root().'components/com_javoice/asset/js/');?>
<?php }?>
<?php if($javconfig['plugin']->get('enable_bbcode',1)){?>
	<script type="text/javascript" src="../components/com_javoice/asset/js/dcode/dcodr.js"></script>
	<script type="text/javascript" src="../components/com_javoice/asset/js/dcode/dcode.js"></script>
<?php }?>
<?php if($javconfig['plugin']->get('enable_after_the_deadline',1)){?>
	<script language="javascript" type="text/javascript">
		var jav_text_checkspelling_no_error = '<?php echo JText::_("NO_WRITING_ERRORS_WERE_FOUND");?>';
	</script>
	<script type="text/javascript" src="../components/com_javoice/asset/js/atd/atd.js"></script>
	<script type="text/javascript" src="../components/com_javoice/asset/js/atd/csshttprequest.js"></script>
	<script type="text/javascript" src="../components/com_javoice/asset/js/atd/jquery.atd.js"></script>
	<?php JHTML::stylesheet('atd.css', JURI::root().'components/com_javoice/asset/js/atd/'); ?>		
<?php }?>
<!-- BEGIN - Add smileys - Add youTube - check spelling -->
	<?php if($this->enable_smileys || $this->enable_after_the_deadline || $this->enable_bbcode || $this->is_show_embed_video || $this->is_attach_image){?>
       <div class="plugin_embed form-actions clearfix">
       	<ul>			        								    
    	    <?php if($this->enable_smileys){
    	    		echo "<li class='jav-form-act-smiley'>";    	    			
    	    		echo  $this->loadTemplate('smiley');
    	    		echo "</li>";
    	    	}
    	    ?>			    	    
    	    <?php if($this->enable_after_the_deadline){?>
		    	<li class='form-act-spell'><a href="javascript:check_atd('<?php echo $prefix;?>')"><span id="checkLink<?php echo $prefix;?>"><?php echo JText::_("CHECK_SPELLING"); ?></span></a></li>			         
		    <?php } ?>
			    	    	            		    					    
		    <?php if($this->is_attach_image){?>
		    	<li class="form-act-attach">						    				    		
		    		<a href="javascript:openAttachFile('<?php echo $prefix;?>');" class="plugin"><span><?php echo JText::_("ATTACH_FILE");?></span></a>							    		
		    	</li>
		    <?php }?>
					    			    				    				    							    
			<!--  BEGIN - BBCODE -->				
		    <?php if($this->enable_bbcode){		    		
		    		//echo "<li class='form-act-bbcode'>";
		    	echo $this->loadTemplate("bbcode");		    			
		    		//echo "</li>";
		   	}?>
		    <!--  END - BBCODE -->		    		    
		</ul>	 
	</div>
	<?php }?> 
<!-- END - Add smileys - Add youTube - check spelling -->
<!-- BEGIN - Text area  -->    
	<div id="jac-container-textarea<?php echo $prefix; ?>" class="form-comment clearfix">													  
		<textarea class="field textarea" rows="12" cols="80" tabindex="1" id="newVoiceContent<?php echo $prefix;?>" name="newVoiceContent"><?php echo $contentComment;?></textarea>
		<script language="javascript" type="text/javascript">
			DCODE.setTags (["LARGE", "MEDIUM", "HR", "B", "I", "U", "S", "UL", "OL", "SUB", "SUP", "QUOTE", "LINK", "IMG", "YOUTUBE", "HELP"]);
			<?php if($prefix == ""){?>
				DCODE.activate ("newVoiceContent", false);
			<?php }else{?>
				DCODE.activate ("newVoiceContentReply", false);
			<?php }?>	
		</script>				            	            		
	</div>		           	         					
<!-- END - Text area  -->									     
<iframe id="upload_target" name="upload_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>   