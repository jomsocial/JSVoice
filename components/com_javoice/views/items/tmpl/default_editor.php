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
$contentComment = "";
if(isset($this->item)){
	$contentComment = $this->item->content;	
}
$prefix	= "";
if(isset($this->textAreaID)){
	$prefix = "Reply";
}
?>	          	    
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
		    		<a href="javascript:javOpenAttachFile('<?php echo $prefix;?>');" class="plugin"><span><?php echo JText::_("ATTACH_FILE");?></span></a>							    		
		    	</li>
		    <?php }?>
					    			    				    				    							    
			<!--  BEGIN - BBCODE -->				
		    <?php if($this->enable_bbcode){		    				    	
		    	echo $this->loadTemplate("bbcode");		    					    	
		   	}?>
		    <!--  END - BBCODE -->		    		    
		</ul>	 
	</div>
	<?php }?> 
<!-- END - Add smileys - Add youTube - check spelling -->
<!-- BEGIN - Text area  -->    
	<div id="jac-container-textarea<?php echo $prefix; ?>" class="form-comment clearfix">													  
		<textarea class="field textarea" rows="12" cols="80" tabindex="1" id="newVoiceContent<?php echo $prefix;?>" name="newVoiceContent<?php echo $prefix;?>"><?php echo $contentComment;?></textarea>				            	            		
        <input type="hidden" name="javNameOfTextarea" value="newVoiceContent<?php echo $prefix;?>" />
	</div>		           	         					
<!-- END - Text area  -->									        