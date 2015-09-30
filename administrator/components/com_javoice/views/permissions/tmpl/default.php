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
 	Joomla.submitform = function(task, form) { 		
 		submitbutton(pressbutton);
 	}
	function submitbutton(pressbutton){
		var form = document.adminForm;
	    if(pressbutton == 'add' || pressbutton == 'edit'){		    
	    	jaCreatForm("edit",0,700,350,0,0,'<?php echo JText::_("NEW_FORUM")?>');
	    }else{
    
		    form.task.value = pressbutton;
		    form.submit();
	    }			
	}
</script>
<dl id="system-message">
</dl> 
 <form action="index.php" method="post" name="adminForm" id="adminForm"> 

 	<input type="hidden" name="option" value="com_javoice" /> 

 	<input type="hidden" name="task" value="" /> 

 	<input type="hidden" name="boxchecked" value="0" /> 

 	<input type="hidden" name="view" value="permissions" /> 

 	<input type="hidden" name="filter_order" value="<?php echo $lists['filter_order']; ?>" /> 

 	<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['filter_order_Dir']; ?>" /> 

 	<table>
		<tr>
			<td align="left" width="100%">
				<?php echo JText::_("FILTER" ); ?>:
				<input type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area" />
				<button onclick="this.form.submit();"><?php echo JText::_("GO" ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.getElementById('sectionid').value='-1';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_('RESET' ); ?></button>
			</td>
		</tr>
	</table> 

 	<table class="adminlist">
		<thead>
			<tr> 

 				<th width="10" align="left">
					<?php echo JText::_('NUM' ); ?>
				</th> 

 				<th class="">
					<?php echo JHTML::_('grid.sort',   "User Name", 'u.username', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
				</th> 

 				<th class="">
					<?php echo JHTML::_('grid.sort',   "Email", 'u.email', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
				</th> 
		
 				<th class="">
					<?php echo JHTML::_('grid.sort',   "ID", 'u.id', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
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
			$title=JText::_('EDIT_PERMISSION')." ID: ".$item->id;					
			?> 

 		<tr class="<?php echo "row$k"; ?>"> 

 				<td align="center">
					<?php echo $page->getRowOffset( $i ); ?>
				</td>

 				<td>
 					<a href="<?php echo $link;?>" onclick="jaCreatForm('edit' ,'<?php echo $item->id;?>', 300 , 200,0,'<?php echo $item->id;?>','<?php echo $title;?>');return false;">
 						<span id='title<?php echo $item->id?>'>
							<?php echo $item->username;?>
						</span>
				</td>
 				<td align="left">
					<?php echo $item->email;?>
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