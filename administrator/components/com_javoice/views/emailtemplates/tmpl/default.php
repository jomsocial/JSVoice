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
?>
<script type="text/javascript">
	function gotoaction(){
		if($('action0').checked){
			$('task').value = 'duplicate';
		}
		else if($('action1').checked){
			$('task').value = 'import';
		}
		document.adminForm.submit();
	}
	function submitbutton(pressbutton){
		var form = document.adminForm;
   
	    form.task.value = pressbutton;
	    form.submit();		
	}
</script>

<form name="adminForm" id="adminForm" action="index.php" method="post">
<table width="100%">		
	<tr>
		<td align="left" width="100%">
			<?php echo JText::_('FILTER_NAME' ); ?>:
			<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" />
			<input type="button" onclick="this.form.submit();" value="<?php echo JText::_('GO' ); ?>">
			<input type="button" onclick="document.getElementById('search').value='';this.form.getElementById('filter_state').value='';this.form.submit();" value="<?php echo JText::_('FILTER_RESET' ); ?>">
		</td>
		<td  align="right" nowrap="nowrap">
			<?php echo $this->languages; ?>
			<?php echo $this->lists['state']; ?>
		</td>
	</tr>
</table>

<table width="100%" class="adminlist">	
        	<thead>
            	<tr>
            		
            		<th width="30%">
            			<?php echo JText::_('EMAIL_GROUP');?>  
            		</th>
            		<th >
            			<table width="100%">
            				<tr>
            					<td style="background-color:#F0F0F0;border: 0px;" width="5%">
            						<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
            					</td>
            					
            					<td  style="background-color:#F0F0F0;border: 0px;">
            						<?php echo JText::_('EMAIL_TITLE');?>            						
            					</td>
            					<td width="15%" style="background-color:#F0F0F0;border: 0px;" align="center">
            						<?php echo JText::_('PUBLISHED')?>            						
            					</td>
            				</tr>
            			</table>
            		</th>            		          		            		
  
            	</tr>
			</thead>
			<tbody>
        	<?php
			
        	$items = $this->items;
        	$en_items = $this->en_items;
        	$k = 0;
        	for($i = 0; $i < count($this->arr_group); $i++){
        		$group = $this->arr_group[$i];
				
        		?>
        		<tr class="row<?php echo $i%2; ?>">
					<td style="font-weight:bold;">					
						<?php echo $group; ?>
					</td>
            		<td>
            			<table class="adminlist">
							<?php if(!isset($items[$i]) && isset($en_items[$i])){?>
								<?php foreach ($en_items[$i] as $item){?>
									<tr>
			            				<td width="5%">
			            					<input type='checkbox' name=cid[] disabled=true/>
			            				</td> 
			            				<td>
		            						<?php echo $item['title']; ?> 
		            						<a href="<?php echo JRoute::_( 'index.php?option='. $this->lists['option'] .'&view=emailtemplates&task=duplicate&filter_lang='.$this->filter_lang.'&cid='.$item['id'] ); ?>" onclick="return confirm('<?php echo JText::_('ARE_YOU_SURE_YOU_WANT_TO_COPY_THE_TEMPLATE_FROM_THIS_DEFAULT_TEMPLATE')?>')" title="<?php echo JText::_('EMAIL_TEMPLATE_DOES_NOT_EXIST_CLICK_HERE_TO_COPY_IT_FROM_THE_DEFAULT_FILE'); ?>"><img src="<?php echo JURI::base()?>components/com_javoice/css/copy-32x32.png" alt="Copy" /></a>
		            					</td>
				            			<td width="15%" align="center">
				            				
				            				
				            			</td>
				            		</tr>
				            	<?php }?>
							<?php }
							
							elseif(isset($items[$i]) && isset($en_items[$i])){?>
							
								<?php $diff = $this->getModel('emailtemplates')->diff_multi_array($en_items[$i], $items[$i], 'name');?>
								
								<?php if($diff){?> 
									<?php foreach ($diff as $item){?>
										<tr>
				            				<td width="5%">
				            					<input type='checkbox' name=cid[] disabled=true/>
				            				</td> 
				            				<td>
			            						<?php echo $item['title']; ?>
			            						<a href="<?php echo JRoute::_( 'index.php?option='. $this->lists['option'] .'&view=emailtemplates&task=duplicate&filter_lang='.$this->filter_lang.'&cid='.$item['id'] ); ?>" onclick="return confirm('<?php echo JText::_('ARE_YOU_SURE_YOU_WANT_TO_COPY_THE_TEMPLATE_FROM_THIS_DEFAULT_TEMPLATE')?>')" title="<?php echo JText::_('EMAIL_TEMPLATE_DOES_NOT_EXIST_CLICK_HERE_TO_COPY_IT_FROM_THE_DEFAULT_FILE'); ?>"><img src="<?php echo JURI::base()?>components/com_javoice/css/copy-32x32.png" alt="Copy" /></a>
			            					</td>
					            			<td width="15%" align="center">					            				
					            				
					            			</td>
					            		</tr>
					            	<?php }?>	
								<?php }?>
								
								<?php foreach ($items[$i] as $item){									
									$temp = new stdClass();
									$temp->id = $item['id'];
									$temp->published = $item['published'];
									$published 	= JHTML::_('grid.published', $temp, $k );
									$temp->checked_out = 0;
									$checked 	= JHTML::_('grid.id',$k,$temp->id );
									?>
									<tr>
			            				<td width="5%">
			            					<?php 
			            						if ($item['system']!=1)
			            							echo $checked; 
			            						else 					
			            							echo "<input type='checkbox' name=cid[] disabled=true/>"
			            					?> 
			            				</td> 
			            				<td>
		            						<a href="<?php echo JRoute::_( 'index.php?option='. $this->lists['option'] .'&view=emailtemplates&task=edit&cid[]='.$item['id'] ); ?>">
	            								<?php echo $item['title']; ?>
	            							</a>
		            					</td>
				            			<td width="15%" align="center">
				            				<?php echo $published ?>
				            			</td>
				            		</tr>
				            		<?php $k++;?>
				            	<?php }?>	
							
							<?php }
							
							elseif(isset($items[$i])){?>
								
								<?php foreach ($items[$i] as $j=>$item){
									
									$temp = new stdClass();
									$temp->id = $item['id'];
									$temp->published = $item['published'];
									$published 	= JHTML::_('grid.published', $temp, $k );
									$temp->checked_out = 0;
									$checked 	= JHTML::_('grid.id',$k,$temp->id );
									?>
									<tr>
			            				<td width="5%">
			            					<?php 
			            						echo $checked; 
			            					?> 
			            				</td> 
			            				<td>
		            						<a href="<?php echo JRoute::_( 'index.php?option='. $this->lists['option'] .'&view=emailtemplates&task=edit&cid[]='.$item['id'] ); ?>">
	            								<?php echo $item['title']; ?>
	            							</a>
		            					</td>
				            			<td width="15%" align="center">
				            				<?php echo $published ?>
				            			</td>
				            		</tr>
				            		<?php $k++;?>
				            	<?php }?>	
							
							<?php }?>								            				
            					
            			</table>
            		</td>                		        		            		
            	</tr>
        	<?php }?>
	<tbody>
	<tfoot>
		<tr>
			<td colspan="6">
				<?php //echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
</table>
	
	
	<input type="hidden" name="option" value="<?php echo $this->lists['option']; ?>" />
	<input type="hidden" name="view" value="emailtemplates" />
	<input type="hidden" name="task" id="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>	
 </form>
