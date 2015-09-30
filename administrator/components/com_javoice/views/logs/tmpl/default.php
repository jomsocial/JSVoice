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

 <form action="index.php" method="post" name="adminForm" id="adminForm"> 

 	<input type="hidden" name="option" value="com_javoice" /> 

 	<input type="hidden" name="task" value="" /> 

 	<input type="hidden" name="boxchecked" value="0" /> 

 	<input type="hidden" name="view" value="logs" /> 

 	<input type="hidden" name="filter_order" value="<?php echo $lists['filter_order']; ?>" /> 

 	<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['filter_order_Dir']; ?>" /> 

 	<table>
		<tr>
			<td align="left" width="100%">
				<?php echo JText::_("FILTER" ); ?>:
				<input type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
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

 				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
				</th> 

 				<th class="">
					<?php echo JHTML::_('grid.sort',   "ID", 'l.id', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
				</th> 

 				<th class="">
					<?php echo JHTML::_('grid.sort',   "USER ID", 'l.user_id', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
				</th> 

 				<th class="">
					<?php echo JHTML::_('grid.sort',   "ITEM ID", 'l.item_id', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
				</th> 

 				<th class="">
					<?php echo JHTML::_('grid.sort',   "TIME EXPIRED", 'l.time_expired', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
				</th> 

 				<th class="">
					<?php echo JHTML::_('grid.sort',   "REMOTE ADDR", 'l.remote_addr', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
				</th> 

 			</tr>
		</thead> 

 		<tfoot>
		<tr>
			<td colspan="7">
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

			$link = 'index.php?option=com_javoice&controller=logs&task=edit&cid[]='. $item->id;
			$item->checked_out=1;
			$checked 	= JHTML::_('grid.id',$i,$item->id );			
			?> 

 		<tr class="<?php echo "row$k"; ?>"> 

 				<td>
					<?php echo $page->getRowOffset( $i ); ?>
				</td>
				<td>
					<?php echo $checked; ?>
				</td> 

 				<td>
					<?php echo $item->id;?>
				</td> 

 				<td>
					<?php echo $item->user_id;?>
				</td> 

 				<td>
					<?php echo $item->item_id;?>
				</td> 

 				<td>
					<?php echo $item->time_expired;?>
				</td> 

 				<td>
					<?php echo $item->remote_addr;?>
				</td> 

 			</tr> 

 		<?php }?> 

 	<?php }?> 

 	</table> 

 <?php echo JHTML::_( 'form.token' ); ?> 

 </form>