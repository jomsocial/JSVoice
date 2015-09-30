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

 global $javconfig;
 
 $items=$this->items; 
 $lists = $this->lists;
 $page=$this->pageNav; 
 ?>
 <script type="text/javascript">
 	var jav_minimum_search = <?php echo $javconfig['systems']->get('minimum_search_num',4);?>; 	
	function submitbutton(pressbutton){
		var form = document.adminForm;
	    if(pressbutton == 'add' || pressbutton == 'edit'){		    
	    	jaCreatForm("edit&voicetypes=<?php echo $lists['voicetypes'];?>",0,700,500,0,0,'<?php echo JText::_("NEW_ITEM")?>');
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

 	<input type="hidden" name="view" value="items" /> 

 	<input type="hidden" name="filter_order" value="<?php echo $lists['filter_order']; ?>" /> 
	
	<input type="hidden" name='voicetypes'  id ='voicetypes' value='<?php echo $lists ['voicetypes'];?>'>
	
	<input type="hidden" name='createdate'  id ='createdate' value='<?php echo $lists['create_date'];?>'>
	
 	<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['filter_order_Dir']; ?>" /> 
<?php echo $this->tabs;?>
 	<table width="100%">
		<tr>
			<td align="left" >
				<?php echo JText::_("FILTER" ); ?>:
				<input type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area" />
				<button class="btn" onclick="this.form.submit();"><?php echo JText::_("GO" ); ?></button>
				<button class="btn" onclick="document.getElementById('search').value='';this.form.getElementById('sectionid').value='-1';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_('RESET' ); ?></button>
			</td>
			<td align="right">
				<?php echo $this->displayForums; ?>		
				<?php echo $this->displayStatus;?>	
			</td>
		</tr>
	</table> 

 	<table class="adminlist">
		<thead>
			<tr> 

 				<th width="2%" align="left">
					<?php echo JText::_('NUM' ); ?>
				</th> 

 				<th width="2%">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
				</th> 

 				<th class="" width="20%">
					<?php echo JHTML::_('grid.sort',   JText::_("TITLE"), 'i.title', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
				</th> 

 				<th class="" width="10%">
 					<?php echo JHTML::_('grid.sort',   JText::_("FORUMS"), 'f.title', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
				</th> 
				
 				<th class="">
 					<?php echo JText::_('USERNAME' ); ?>					
				</th> 

 				<th class="">
					<?php echo JHTML::_('grid.sort',   JText::_("CREATE_DATE"), 'i.create_date', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
				</th> 

 				<th class="">
					
					<?php echo JHTML::_('grid.sort',   JText::_("SPAM"), 'i.number_spam', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
				</th> 
				
 				<th class="">
 					<?php echo JText::_('STATUS' ); ?>					
				</th> 

 				<th class="">
					<?php echo JHTML::_('grid.sort',   JText::_("PUBLISHED"), 'i.published', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
				</th> 
				
				<th>
					<?php echo JText::_('RESPONSE' ); ?>	
				</th>
				<?php if($this->has_answer){?>
				<th>
					<?php echo JText::_('BEST_ANSWER' ); ?>	
				</th>				
				<?php }?>
 				<th class="">
					<?php echo JHTML::_('grid.sort',   JText::_("ID"), 'i.id', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
				</th> 

 			</tr>
		</thead> 

 		<tfoot>
		<tr>
			<td colspan="19">
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
			
			$link = 'index.php?tmpl=component&option=com_javoice&view=items&task=edit&cid[]='. $item->id."&viewmenu=0";
			$item->checked_out=1;
			$checked 	= JHTML::_('grid.id',$i,$item->id );
			
			$published=($item->published ==1)?'<a title="'.JText::_('UNPUBLISH' ).'" onclick="return listItemTask(\'cb'.$i.'\',\'unpublish\')" '
			.'href="javascript:void(0);"><img  border="0" alt="'.JText::_('UNPUBLISH' ).'" src="components/com_javoice/asset/images/tick.png"/></a>':'<a title="'.JText::_('PUBLISH' ).'"'
			.' onclick="return listItemTask(\'cb'.$i.'\',\'publish\')" href="javascript:void(0);"><img border="0" alt="'.JText::_('PUBLISH' ).'" src="components/com_javoice/asset/images/publish_x.png"/>';
			$published="<span id='publish$item->id'>$published</span>";	
			$title=JText::_('EDIT_ITEM')." ID: ".$item->id;						
			?> 

 		<tr class="<?php echo "row$k"; ?>"> 

 				<td>
					<?php echo $page->getRowOffset( $i ); ?>
				</td>
				<td>
					<?php echo $checked; ?>
				</td> 

 				<td>
					 <a  href="<?php echo $link;?>" onclick="jaCreatForm('edit&voicetypes=<?php echo $lists['voicetypes'];?>' ,'<?php echo $item->id?>',700,350,0,'<?php echo $i?>','<?php echo $title?>');return false;">
					 	<span id="title<?php echo $item->id?>"><?php echo $item->title;?></span>					 	
					 </a>

				</td> 

 				<td >
					<span id="forums_title<?php echo $item->id?>"> <?php echo $item->forums_title;?></span>
				</td> 

 				<td >
					<span id="user_name<?php echo $item->id?>"><?php echo $item->user_name;?></span>
				</td> 

 				<td align="center">
					<span id="create_date<?php echo $item->id?>"> <?php echo date("Y-m-d", $item->create_date);?></span>
				</td> 

 				<td align="center">
 					<span id="number_spam<?php echo $item->id?>">
						<?php echo $item->number_spam;?>
					</span>
				</td> 
				
				<?php if($item->voice_type_status_title){?>
 				<td>
 					<span id="voice_type_status_title<?php echo $item->id?>">
						<?php echo $item->voice_type_status_title;?>
					</span>
				</td> 
				<?php }else{?>
				<td align="center">
 					<span id="voice_type_status_title<?php echo $item->id?>">
						<?php echo '-';?>
					</span>
				</td> 
				<?php }?>

 				<td align="center">
					<?php echo $published;?>
				</td> 

 				<td align="center">
 					<?php if(isset($item->rid)){?>
 					<img src="components/com_javoice/asset/images/tick.png">
 					 <a  href="<?php echo $link;?>" onclick="jaCreatForm('response&type=admin_response' ,'<?php echo $item->rid?>',700,350,0,0,'<?php echo JText::_('ADMIN_RESPONSE')?>');return false;">
						<?php echo JText::_("VIEW");?>
					</a>
					<?php }else{
						?><img src="components/com_javoice/asset/images/publish_x.png"><?php 
					}
					?>
				</td> 
				<?php if($this->has_answer){?>
					<td align="center">
						<?php if(isset($item->bid)){?>
		 					<img src="components/com_javoice/asset/images/tick.png">
		 					 <a  href="<?php echo $link;?>" onclick="jaCreatForm('response&type=best_answer' ,'<?php echo $item->bid?>',450,300,0,0,'<?php echo JText::_('BESST_ANSWER')?>');return false;">
								<?php echo JText::_("VIEW");?>
							</a>						
						<?php }else{?>
							<img src="components/com_javoice/asset/images/publish_x.png">
						<?php }?>
					</td>
				<?php }?>
 				<td>
					<?php echo $item->id;?>
				</td> 

 			</tr> 

 		<?php }?> 

 	<?php }else{
 		?>
 		<tr><td colspan="13"><?php echo JText::_("HAVE_NO_RESULT")?></td></tr>
 		<?php 
 	}
 	?> 

 	</table> 

 <?php echo JHTML::_( 'form.token' ); ?> 

 </form>
