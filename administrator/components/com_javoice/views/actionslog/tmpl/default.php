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
?> 

<dl id="system-message">
</dl> 
 <form action="index.php" method="post" name="adminForm" id="adminForm"> 

 	<input type="hidden" name="option" value="com_javoice" /> 

 	<input type="hidden" name="task" value="" /> 

 	<input type="hidden" name="boxchecked" value="0" /> 

 	<input type="hidden" name="view" value="actionslog" /> 

 	<input type="hidden" name="filter_order" value="<?php echo $lists['filter_order']; ?>" /> 

 	<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['filter_order_Dir']; ?>" /> 

 	<table width="100%">
		<tr>
			<td align="left">
				<?php echo JText::_("FILTER"); ?>:
				<input type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />				
				<button onclick="this.form.submit();"><?php echo JText::_("GO" ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.getElementById('sectionid').value='-1';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_('RESET'); ?></button>
			</td>
			<td align="right">
				<select name='runby' id='runby' onchange="this.form.submit()">
					<option value="0" <?php if($lists['runby']==0)echo "selected";?> ><?php echo JText::_("USERCRON")?></option>
					<option value="1" <?php if($lists['runby']==1)echo "selected";?> ><?php echo JText::_("USER")?></option>
					<option value="2" <?php if($lists['runby']==2)echo "selected";?> ><?php echo JText::_("CRON")?></option>
				</select>
				<?php echo $this->displayTypes?>
			</td>
		</tr>
	</table> 

 	<table class="adminlist">
		<thead>
			<tr> 

 				<th width="10" align="left">
					#
				</th> 

 				<th class="">
					<?php echo JHTML::_('grid.sort',   "TYPE", 'l.type', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
				</th> 
	
 				<th class="">
					<?php echo JHTML::_('grid.sort',   "DETAILS", 'l.details', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
				</th>
				 
 				<th class="">
					<?php echo JHTML::_('grid.sort',   "Date", 'l.time', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
				</th> 
								
 				<th class="">
					<?php echo JHTML::_('grid.sort',   "IP Address", 'l.remote_addr', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
				</th> 
				
 				<th class="">
					<?php echo JHTML::_('grid.sort',   "ID", 'l.id', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
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
				
			?> 

 		<tr class="<?php echo "row$k"; ?>"> 

 				<td align="center">
					<?php echo $page->getRowOffset( $i ); ?>
				</td>
 				<td align="center">
						<?php echo $item->type;?>
				</td> 

 				<td align="center">
					<?php echo $item->details;?>
				</td>
				 
 				<td align="center">
					<?php echo date('d/M/Y',$item->time);?>
				</td> 	
								
 				<td align="center">
					<?php echo $item->remote_addr;?>
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