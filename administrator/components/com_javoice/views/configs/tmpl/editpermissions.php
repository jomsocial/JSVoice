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
defined('_JEXEC') or die('Retricted Access');
 $mainframe = JFactory::getApplication();
 JHTML::_('behavior.tooltip');
 jimport('joomla.html.pane');
 $items=$this->items;

 $lists=$this->lists;

 $ordering = (@$lists['filter_order'] == 'f.ordering');
?>
 <script type="text/javascript">
	function submitbutton(pressbutton){
		var form = document.adminForm;
	    form.task.value = pressbutton;
	    form.submit();			
	}
</script>
<div class="col100">

<div class="clr"></div>
<dl id="system-message">
</dl> 
 <form action="index.php" method="post" name="adminForm" id="adminForm"> 

 	<input type="hidden" name="option" value="com_javoice" /> 

 	<input type="hidden" name="boxchecked" value="0" /> 

 	<input type="hidden" name="view" value="configs" /> 
 	
 	<input type="hidden" name="group" value="permissions" /> 
 	
 	<input type="hidden" name="type" value="<?php echo @$lists['type']?>" /> 
 	
 	<input type="hidden" name="task" value="saveAddUser" /> 
 	
 	<input type="hidden" name="viewmenu" value="0" /> 

	<input type="hidden" name="tmpl" value="component" />
	
 	<input type="hidden" name="filter_order" value="<?php echo @$lists['filter_order']; ?>" /> 

 	<input type="hidden" name="filter_order_Dir" value="<?php echo @$lists['filter_order_Dir']; ?>" />
 	<table>
 		<tr>
 			<td> 		
 				<?php echo JText::_('PLEASE_ENTER_USERNAME_OR_USER_ID');?>:<br/>
 				<input type="text" name="search" value="<?php echo @$lists['search'];?>" size="30"/>
 				<?php  					
 					echo $this->groupUser;
 				?>
 				<input type="button" value="Search" onclick="submitbutton('editpermissions')">
 			</td>
 		</tr>
 	</table> 
	<?php 
	
	//if($this->postback){?>
 	<table class="adminlist">
		<thead>
			<tr> 

 				<th width="10" align="left">
					#
				</th> 

 				<th width="2%">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
				</th> 

 				<th class="">
 					<?php echo JText::_('USERNAME' ); ?>
				</th> 

 				<th class="">
 					<?php echo JText::_('EMAIL' ); ?>					
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
		for ($i=0;$i<$count; $i++) {
			$item	= $items[$i];
			$item->checked_out=1;
			$checked 	= JHTML::_('grid.id',$i,$item->id );
			JFilterOutput::objectHtmlSafe($item);
			$title=JText::_('EDIT_PERMISSION')." ID: ".$item->id;					
			?> 

 		<tr class="<?php echo "row$k"; ?>"> 

 				<td align="center">
					<?php echo  $i+1; ?>
				</td>
				<td>
					<?php echo $checked; ?>
				</td> 
 				<td>
					<?php echo $item->username;?>
				</td>
 				<td align="left">
					<?php echo $item->email;?>
				</td> 				
 				<td align="center">
					<?php echo $item->id;?>
				</td> 

 			</tr> 

 		<?php }?> 

 	<?php }else{
 		?>
 			<tr>
 				<td colspan="5">
 					<?php echo JText::_("HAVE_NO_RESULT");?>
 				</td>
 			</tr>
 		<?php 
 	}
 	?> 

 	</table> 
	<?php //}?>
 <?php echo JHTML::_( 'form.token' ); ?> 

 </form>
</div>