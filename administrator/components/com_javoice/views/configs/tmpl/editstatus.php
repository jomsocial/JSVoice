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
 // Check to ensure this file is included in Joomla! 

 defined('_JEXEC') or die( 'Restricted access' ); 

 $items=$this->items; 

 ?> 
<script>
	function changeStatusDefault(status_id,voice_type_id,title,ptitle){
		jQuery(document).ready( function($) {
			$('#status_spam_'+voice_type_id, window.parent.document).attr('value',status_id);
			$('#jav_title_'+voice_type_id, window.parent.document).html(title);
			$('#jav_parent_title_'+voice_type_id, window.parent.document).html(ptitle);			
		});
		jaFormHideIFrame();
		return true;
	}
</script>
 <form name="adminForm" id="adminForm" action="index.php" method="post"> 

	<input type="hidden" name="number" value="1">


 	<input type="hidden" name="option" value="com_javoice" /> 

 	<input type="hidden" name="task" value="saveStatus" /> 

 	<input type="hidden" name="boxchecked" value="0" /> 

 	<input type="hidden" name="view" value="configs" /> 
	<input type="hidden" name="tmpl" value="component" /> 
	
	<input type="hidden"  id="voicetype_id" name="voicetype_id" value="<?php echo $this->voice_types_id?>" />
	 
	<input type="hidden"  id="status_id" name="status_id" value="" />
	
	<input type="hidden"  id="id" name="id" value="1" />
	
 	<table class="adminlist">
		<thead>
			<tr> 

 				<th width="10" align="left">
					#
				</th> 

 				<th class="">
 					<?php echo JText::_('TITLE' ); ?>
				</th> 

 				<th class="" width="70px">
 					<?php echo JText::_('BACKGROUND' ); ?>					
				</th> 
 				<th class="">
 					<?php echo JText::_('ID' ); ?>	
				</th> 
				
 			</tr>
		</thead> 

 		<?php
		$k = 0;
		$count=count($items);
		if( $count>0 ) {
			$inum = 0;
			$grouptitle = "";
			foreach ($items as $item){
			$style='';			
			if($item->parent_id==0){
				$style = "style='background-color: rgb(238, 238, 238);font-weight:bold;'";
				$grouptitle = $item->title;
			}
			?> 						
	
	 		<tr class="<?php echo "row"; ?>" > 
	
	 				<td >
						<?php echo $inum+1; ?>
					</td>
	
	 				<td <?php echo $style;?>>
	 					<?php if($item->parent_id==0){
	 						echo JText::_('GROUP');
	 						echo ": ".$item->title;
	 					}else{?>
	 						<a href="index.php" onclick="changeStatusDefault('<?php echo $item->id?>','<?php echo $this->voice_types_id;?>','<?php echo $item->title?>','<?php echo $grouptitle; ?>');return false;">
	 							<?php echo $item->title;?>
	 						</a>
	 					<?php }?>
					</td> 
	 				<td>
						<div style="width:70px; height:15px;background:<?php echo $item->class_css;?>"></div> 
					</td> 					
	 				<td>
						<?php echo $item->id;?>
					</td> 
	
	 			</tr> 
	
	 		<?php 
			$inum ++;
			}
	 		
		 }else{ ?> 
 	<tr>
 		<td colspan="10"><?php echo JText::_("HAVE_NO_RESULT")?></td>
 	</tr>
 <?php }?>

 	</table> 

 <?php echo JHTML::_( 'form.token' ); ?> 

 </form>