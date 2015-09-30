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
 $ordering = ($lists['filter_order'] == 's.ordering');
 ?> 
<script type="text/javascript">
	function submitbutton(pressbutton){
		var form = document.adminForm;
		var isstart = form.isstart.value;
		if(isstart == 1){
			alert("<?php echo JText::_("PLEASE_CREATE_BEFORE_VOICEE_TYPE")?>");
			return false;
		}		
	    if(pressbutton == 'add' || pressbutton == 'edit'){		    
	    	jaCreatForm("edit&voice_types_id=<?php echo $lists['voice_types_id'];?>",0,500,320,0,0,'<?php echo JText::_("NEW_STATUS");?>');
	    }else{
		    if(pressbutton == 'remove'){
			    var group_parent = 0;
			    var urlrequestgroup = 'index.php?tmpl=component&option=com_javoice&view=voicetypesstatus&task=checkgroup';
			    var status_id = '';
		    	jQuery("input[name=\"cid\[\]\"]:checked").each(function(i){
		    		status_id += "&status_id[]="+jQuery(this).val();
		    	});
		    	urlrequestgroup += status_id+"&viewmenu=0";
	    		var req = new Request({
	    		      method: 'get',
	    		      url: urlrequestgroup,
	    		      onSuccess : function(responseText){
	    		    	 if(responseText == 1){
    				    	var answer = confirm ("<?php echo JText::_('WARNING_BEFOR_DELETED_GROUP_NOT_NULL');?>")
    				    	if (answer){
    				    		form.task.value = pressbutton;
    						    form.submit();
    				    	}
    			    	}else{
    						alert("<?php echo JText::_('WARNING_BEFOR_DELETED');?>");
    						form.task.value = pressbutton;
    					    form.submit();
    					}
	    		      }
	    		 }).send();
			}
		    else if(pressbutton == 'addgroup' || pressbutton == 'editgroup'){		    
		    	jaCreatForm("editgroup&isgroup=1&voice_types_id=<?php echo $lists['voice_types_id'];?>",0,500,260,0,0,"<?php echo JText::_("NEW_GROUP");?>");
		    }else{		    
			    form.task.value = pressbutton;
			    form.submit();
		    }
	    }			
	}
	
</script>
 <form action="index.php" method="post" name="adminForm" id="adminForm"> 

 	<input type="hidden" name="option" value="com_javoice" /> 

 	<input type="hidden" name="task" value="" /> 

 	<input type="hidden" name="boxchecked" value="0" /> 

 	<input type="hidden" name="view" value="voicetypesstatus" /> 
	
 	<input type="hidden" name="filter_order" value="<?php echo $lists['filter_order']; ?>" /> 

 	<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['filter_order_Dir']; ?>" /> 
	
	<input type="hidden" name='isstart' id='isstart'value="<?php echo $this->isstart; ?>">
	
 	<table width="100%">
		<tr>
			<td align="left" >
				<?php echo JText::_("FILTER" ); ?>:
				<input type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area"  />				
				<button onclick="this.form.submit();"><?php echo JText::_("GO" ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.getElementById('sectionid').value='-1';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_('RESET' ); ?></button>
			</td>
			<td align="right">
				<?php echo JText::_('SELECT_VOICE_TYPE').": " ?><?php echo $this->displayVoicetypes;?>
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
					<?php echo JHTML::_('grid.sort',   "TITLE", 's.title', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
				</th> 
				
 				<th class="">
					<?php echo JHTML::_('grid.sort',   "SHOW_ON_TAB", 's.show_on_tab', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
				</th> 

 				<th class="" width="90px;">
					<?php echo JHTML::_('grid.sort',   "BACKGROUND", 's.class_css', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
				</th> 
				
 				<th class="order" >
					<?php echo JHTML::_('grid.sort',   "ORDERING", 's.ordering', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
					<?php echo JHTML::_('grid.order',  $items ); ?>
				</th> 
				
 				<th class="">
					<?php echo JHTML::_('grid.sort',   "PUBLISHED", 's.published', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
				</th> 

 				<th class="">
					<?php echo JHTML::_('grid.sort',   "ID", 's.id', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
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
			$inum = 0;
			foreach ($items as $item){
				
				
				if($inum>=$lists['limitstart']+$lists['limit'])break;
				if($inum>=$lists['limitstart']){					

				JFilterOutput::objectHtmlSafe($item);
				$link = 'index.php?option=com_javoice&view=voicetypesstatus&task=edit&cid[]='. $item->id."&viewmenu=0";
				$item->checked_out=1;
				$checked 	= JHTML::_('grid.id',$inum,$item->id );	
				$published 	= JHTML::_('grid.published', $item, $inum );
				
				$published="<span id='publish$item->id'>$published</span>";
				$show_on_tab=($item->show_on_tab ==1)?'<a title="'.JText::_('CURRENTLY_ENABLED_AS_FILTER_UNDER_THE_VOICETYPE_TAB_CLICK_TO_DISABLE').'" onclick="return listItemTask(\'cb'.$inum.'\',\'unshow\')" '
				.'href="javascript:void(0);"><img border="0" alt="'.JText::_('CURRENTLY_ENABLED_AS_FILTER_UNDER_THE_VOICETYPE_TAB_CLICK_TO_DISABLE' ).'" src="components/com_javoice/asset/images/tick.png"/></a>':'<a title="'.JText::_('CURRENTLY_NOT_ENABLED_AS_FILTER_UNDER_THE_VOICETYPE_TAB_CLICK_TO_ENABLE' ).'"'
				.' onclick="return listItemTask(\'cb'.$inum.'\',\'show\')" href="javascript:void(0);"><img border="0" alt="'.JText::_('CURRENTLY_NOT_ENABLED_AS_FILTER_UNDER_THE_VOICETYPE_TAB_CLICK_TO_ENABLE' ).'" src="components/com_javoice/asset/images/publish_x.png"/>';
				$show_on_tab="<span id='show_on_tab$item->id'>$show_on_tab</span>";

				$style='';
				$retask='edit';
				$height = 320;
				$title=JText::_("EDIT_STATUS_ID").": ".$item->id;
				if($item->parent_id==0){
					$style = " colspan='2' style='background-color: rgb(238, 238, 238);font-weight:bold;'";	
					$retask = "editgroup";
					$height=260;
					$title=JText::_("EDIT_GROUP_ID").": ".$item->id;			
				}				
				$ordering = TRUE;
			?> 						
	
	 		<tr class="<?php echo "row"; ?>" > 
	
	 				<td >
						<?php echo $inum+1; ?>
					</td>
					<td>
						<?php echo $checked; ?>
					</td> 
	
	 				<td <?php echo $style;?>>
	 					<?php if(!$item->parent_id)echo JText::_("GROUP");?>
	 					<a  href="<?php echo $link;?>" onclick='jaCreatForm("<?php echo $retask;?>" ,"<?php echo $item->id;?>",500,<?php echo $height;?>,0,<?php echo $inum;?>,"<?php echo $title;?>");return false;'>
	 						<span id='title<?php echo $item->id?>'>
	 							<?php echo $item->title;?>
	 						</span>
	 					</a>
					</td> 
					
					<?php if($item->parent_id){?>	 				

		 				<td align="center">
							<?php echo $show_on_tab;?>
						</td> 		
		
					<?php }?>
					<td>
						<a  href="index.php" onclick='jaCreatFormColor("color","#class_css<?php echo $item->id?>",289,95,"<?php echo $item->id;?>","<?php echo JText::_("SAVE"); ?>","<?php echo JText::_("CANCEL"); ?>",1);return false;'>
							<div id='class_css<?php echo $item->id?>'style="border:1px solid #CCCCCC;float:left;width:70px; height:15px;background-color:<?php echo $item->class_css;?>"></div>		 							 											
							<div style="float:left;">				
								<img src="<?php echo JURI::base()?>components/com_javoice/asset/images/paintbrush.png"> 								
							</div>		 							 												
						</a>							
					</td> 											
	 				<td class="order">
						<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" id="order<?php echo  $item->id;?>"  size="5" value="<?php echo $item->ordering; ?>" <?php echo $disabled; ?> class="text_area" style="text-align: center" />	 										
					</td> 
					
	 				<td align="center">
						<?php echo $published;?>
					</td> 
	
	 				<td>
						<?php echo $item->id;?>
					</td> 
	
	 			</tr> 
	
	 		<?php }
	 		$inum ++;
			
			}?> 

 <?php }else{ ?> 
 	<tr>
 		<td colspan="10"><?php echo JText::_("HAVE_NO_RESULT")?></td>
 	</tr>
 <?php }?>

 	</table> 

 <?php echo JHTML::_( 'form.token' ); ?> 

 </form>