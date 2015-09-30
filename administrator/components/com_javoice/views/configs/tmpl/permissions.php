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
 $items=$this->items; 

 $page=$this->pageNav; 

 $lists=$this->lists;

 $ordering = ($lists['filter_order'] == 'f.ordering');
?>
<script type="text/javascript">
	function submitbutton(pressbutton){
		var form = document.adminForm;	
		if(pressbutton=='add')    
	    	jaCreatForm("editpermissions&group=permissions",0,500,350,0,0,'<?php echo JText::_("ADD_USER");?>',0,'<?php echo JText::_('ADD');?>');
		else{
			form.task.value=pressbutton;
			form.submit();
		}			
	}
</script>

<div class="col100">

<div class="clr"></div>
 <form action="index.php" method="post" name="adminForm" id="adminForm"> 

 	<input type="hidden" name="option" value="com_javoice" /> 

 	<input type="hidden" name="task" value="" /> 

 	<input type="hidden" name="boxchecked" value="0" /> 

 	<input type="hidden" name="view" value="configs" /> 
 	
 	<input type="hidden" name="group" value="permissions" /> 
 	
 	<input type="hidden" name="type" value="<?php echo @$lists['type']?>" /> 

 	<input type="hidden" name="filter_order" value="<?php echo @$lists['filter_order']; ?>" /> 

 	<input type="hidden" name="filter_order_Dir" value="<?php echo @$lists['filter_order_Dir']; ?>" /> 

	<fieldset class="adminform">
		<?php echo $this->getTabs();?>
	</fieldset>	
	<br/>

		<div style="text-align: right;">
			<a id='jav_help' href="index.php"onclick="hiddenNote('permission','<?php echo JText::_('HELP')?>','<?php echo JText::_('CLOSE')?>');return false;"><?php echo JText::_('HELP')?></a>
		</div>		
		<?php 
			$note = JText::_("ADMIN__MODERATOR_SETTINGS_TOOLTIP" )." ".JText::_("ADMIN__MODERATOR_SETTINGS_NOTE" );
			JAVoiceHelpers::displayNote($note,'permission');
		?>
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
						<?php echo JHTML::_('grid.sort',   "Username", 'u.username', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
					</th> 
					<th class="">
						<?php echo JHTML::_('grid.sort',   "User Group", 'u.usertype', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
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
				$item->checked_out=1;
				$checked 	= JHTML::_('grid.id',$i,$item->id );
				JFilterOutput::objectHtmlSafe($item);
				$title=JText::_('EDIT_PERMISSION')." ID: ".$item->id;					
				?> 
	
	 		<tr class="<?php echo "row$k"; ?>"> 
	
	 				<td align="center">
						<?php echo $page->getRowOffset( $i ); ?>
					</td>
					<td>
						<?php echo $checked; ?>
					</td> 
	 				<td>
						<?php echo $item->username;?>
					</td>
					<td>
						<?php if(isset($item->usertype))echo $item->usertype; else echo JText::_("REGISTERED");?>
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
</div>
