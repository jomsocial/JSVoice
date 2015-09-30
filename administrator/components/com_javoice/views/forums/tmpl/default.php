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

 $page=$this->pageNav; 

 $lists=$this->lists;
  
 $ordering = ($lists['filter_order'] == 'f.ordering');
 
 ?> 
 <script type="text/javascript">
	function submitbutton(pressbutton){
		var form = document.adminForm;
		var isstart = form.isstart.value;
		if(isstart == 1){
			alert("<?php echo JText::_("PLEASE_CREATE_BEFORE_VOICEE_TYPE_AND_STATUS")?>");
			return false;
		}
	    if(pressbutton == 'add' || pressbutton == 'edit'){		    
	    	jaCreatForm("edit",0,700,450,0,0,'<?php echo JText::_("NEW_FORUM")?>');
	    }else{
    
		    form.task.value = pressbutton;
		    form.submit();
	    }			
	}
</script>
 <form action="index.php" method="post" name="adminForm" id="adminForm"> 

 	<input type="hidden" name="option" value="com_javoice" /> 

 	<input type="hidden" name="task" value="" /> 

 	<input type="hidden" name="boxchecked" value="0" /> 

 	<input type="hidden" name="view" value="forums" /> 

 	<input type="hidden" name="filter_order" value="<?php echo $lists['filter_order']; ?>" /> 

 	<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['filter_order_Dir']; ?>" /> 
 	<input type="hidden" name='isstart' id='isstart'value="<?php echo $this->isstart; ?>">
	
 	<table>
		<tr>
	
			<td align="left" width="100%">
				<?php echo JText::_("FILTER" ); ?>:
				<input type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area" />
				<button onclick="this.form.submit();"><?php echo JText::_("GO" ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.getElementById('sectionid').value='-1';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_('RESET' ); ?></button>
			</td>
			<td style="width: 50px;">
					<a id='jav_help' href="index.php"onclick="hiddenNote('forums','<?php echo JText::_('HELP')?>','<?php echo JText::_('CLOSE')?>');return false;"><?php echo JText::_('HELP')?></a>		
			</td>				
		</tr>
		<tr>
			<td colspan="2">
			
				<?php 
					$note = JText::_("FOURMS_CAN_BE_UNDERSTOOD_AS_CATEGORIES_YOU_CAN_SET_UNLIMITED_FORUMS__SET_ACCESS_PERMISSION_FOR_EACH_FORUM" );
					JAVoiceHelpers::displayNote($note,'forums');
				?>				
			</td>
			
		</tr>
	</table> 

 	<table class="adminlist">
		<thead>
			<tr> 

 				<th width="10" align="left">
					#
				</th> 

 				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
				</th> 

 				<th class="">
					<?php echo JHTML::_('grid.sort',   "TITLE", 'f.title', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
				</th> 
				<th>
					<?php echo JText::_("APPLIES_VOICE_TYPES")?>
				</th>
 				<th class="">
					<?php echo JHTML::_('grid.sort',   "ORDERING", 'f.ordering', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
					<?php echo JHTML::_('grid.order',  $items ); ?>
				</th> 

 				<th class="">
					<?php echo JHTML::_('grid.sort',   "PUBLISHED", 'f.published', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
				</th> 

 				<th class="">
					<?php echo JHTML::_('grid.sort',   "ID", 'f.id', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
				</th> 

 			</tr>
		</thead> 

 		<tfoot>
		<tr>
			<td colspan="10">
				<?php echo $page->getListFooter(); ?>
			</td>
		</tr>
		</tfoot> 

 		<?php
		$k = 0;
		$count=count($items);
		if( $count>0 ) {
		for ($i=0;$i<$count; $i++) {
			$item	= $items[$i];

			JFilterOutput::objectHtmlSafe($item);

			$link = 'index.php?tmpl=component&option=com_javoice&view=forums&task=edit&cid[]='. $item->id."&viewmenu=0";
			$item->checked_out=1;
			$checked 	= JHTML::_('grid.id',$i,$item->id );
			$published 	= JHTML::_('grid.published', $item, $i );	
			$published="<span id='publish$item->id'>$published</span>";	
			$title=JText::_('EDIT_FORUM')." ID: ".$item->id;					
			?> 

 		<tr class="<?php echo "row$k"; ?>"> 

 				<td align="center">
					<?php echo $page->getRowOffset( $i ); ?>
				</td>
				<td align="center">
					<?php echo $checked; ?>
				</td> 

 				<td>
 					<a href="<?php echo $link;?>" onclick="jaCreatForm('edit' ,'<?php echo $item->id;?>', 700 , 450,0,<?php echo $i;?> ,'<?php echo $title;?>');return false;">
 						<span id='title<?php echo $item->id?>'>
							<?php echo $item->title;?>
						</span>
					</td>
				</td> 
				
				<td>
					<span id='voice-types-<?php echo $item->id?>'>
						<?php echo $item->strvoice; ?>
					</span>
				</td>
 				<td class='order'> 				
					<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
					<input type="text" name="order[]" id='order<?php echo $item->id?>' size="5" value="<?php echo $item->ordering; ?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
					
				</td> 

 				<td align="center">
						<?php echo $published;?>
				</td> 

 				<td align="center">
					<?php echo $item->id;?>
				</td> 

 			</tr> 

 		<?php }?> 

 	<?php }?> 

 	</table> 

 <?php echo JHTML::_( 'form.token' ); ?> 

 </form>
  <script>
jQuery(document).ready( function($) {	
	var coo = getCookie('hidden_message_forums');
	if(coo==1)
		$('#jav-message').attr('style','display:none');
});	
</script>