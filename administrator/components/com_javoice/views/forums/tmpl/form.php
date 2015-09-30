<?php // no direct access
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
defined('_JEXEC') or die('Restricted access');
$item = $this->item;
//$editor =& JFactory::getEditor();
$voicetypes = $this->voicetypes;
$count=count($voicetypes);
$lists = $this->lists;
JHTML::_('behavior.tooltip');
?> 

 <form name="adminForm" id="adminForm" action="index.php" method="post"> 

 	<input type="hidden" name="option" value="com_javoice" /> 

 	<input type="hidden" name="view" value="forums" /> 

 	<input type="hidden" name="task" value="saveIFrame" /> 
	
	<input type="hidden" name="tmpl" value="component" /> 
	
	<input type="hidden" name="number" value="<?php echo $this->number;?>">
	
 	<input type="hidden" name='id' id='id' value="<?php echo $item->id?>"> 
	<input type="hidden" name='cid[]' id='cid[]' value="<?php echo $item->id?>">
	<div class="ja-haftleft">
		<h2><?php echo JText::_("GENERAL_SETTINGS" ); ?></h2>
		<ul class="ja-forums">
			<li>
				<label class="desc" for="title"><?php echo JText::_("TITLE" );?> <font color="red">*</font></label>
				<div><input onchange="checkdataString(this,'error')" type="text" name="title" id="title" class="text required" size='50' value="<?php echo $item->title?>"></div>
			</li>
					
			<li style="height: 40px">
				<div  class="ja-haftleft">
					<label class="desc" for="title"><?php echo JText::_("ALIAS" );?></label>
					<div><input type="text" name="alias" id="alias" class="text" size='20' value="<?php echo $item->alias?>"></div>
				
				</div>
				<div  class="ja-haftright">
					<label class="desc" for="title"><?php echo JText::_("PUBLISHED" );?></label>
					<div style="float: left;clear:both;">
							<?php
							$published = ($item->published==1) ? $item->published : 0;
							$lists['published'] 		= JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $published );
							echo $lists['published']; ?>
					</div>
				</div>
			</li>
			<li>
				<fieldset class="adminform">
				<legend class="editlinktip hasTip" title="<?php echo JText::_('APPLIES_VOICE_TYPES_TOOLTIP' ); ?>"><?php echo JText::_("APPLIES_VOICE_TYPES" );?> <font color="red">*</font></legend>
				<div id='jav_td_status'>
						<?php 
					 				$j=0;
					 				foreach ($voicetypes as $voicetype){
					 					$j++;
					 					$display=$voicetype->voice_type_selected>0?'':"style='display:none;'";
					 					?>
					 						<p>
							 				<input type="checkbox" <?php if($voicetype->voice_type_selected)echo "checked";?> id="voice_type_<?php echo $j; ?>" name="voice_type[]" onclick="displaySelectStatus(this,'<?php echo $voicetype->id; ?>')" value="<?php echo $voicetype->id; ?>"><?php echo $voicetype->title;?>
							 				</p>
							 				<div class='jav-starting-status' id='jav_div_status_<?php echo $voicetype->id;?>' <?php echo $display ;?>>
							 					<span class="desc editlinktip hasTip" title="<?php echo JText::_("STARTING_STATUS_DEFAULT" );?>::<?php echo JText::_("STARTING_STATUS_DEFAULT_TOOLTIP" ); ?>">
							 						<?php echo JText::_("STARTING_STATUS_DEFAULT")?>: 
							 					</span>
							 					<?php echo $lists[$voicetype->id];?>
							 				</div>
							 				
					 					<?php 
					 				}
					 				?>
				</div>
				</fieldset>
			</li>
			<li>
				<label class="desc" for="title"><?php echo JText::_("DESCRIPTION");?></label>
				<div>
						<textarea name='description' id='description' class='textarea' rows="1" cols="50"><?php echo  htmlspecialchars($item->description, ENT_QUOTES);?></textarea>
				</div>
			</li>
		</ul> 		
	</div>
	
	<div class="ja-haftright">
		<h2><?php echo JText::_("PERMISSION_SETTINGS" ); ?></h2>
		<ul>
			<li>				
				<label class="desc editlinktip hasTip" for="gids_post" title="<?php echo JText::_("POST_PERMISSION" );?>::<?php echo JText::_("POST_PERMISSION_TOOLTIP" ); ?>">
					<?php echo JText::_("POST_PERMISSION")?> <font color="red">*</font>
				</label>
				<div><?php echo JHTML::_('select.genericlist',   $this->gtree, 'gids_post[]', 'class="inputbox" size="7" multiple="multiple" onchange="changePost()" style="width: 100%;"', 'value', 'text', $item->gids_post_selected);?></div>
			</li>
			<li>
				<label class="desc editlinktip hasTip" for="gids_view" title="<?php echo JText::_("VIEW_PERMISSION" );?>::<?php echo JText::_("VIEW_PERMISSION_TOOLTIP" ); ?>">
					<?php echo JText::_("VIEW_PERMISSION")?> <font color="red">*</font>
				</label>
				<div><?php echo JHTML::_('select.genericlist',   $this->gtree, 'gids_view[]', 'class="inputbox" size="7" multiple="multiple" onchange="changeView(this)" style="width: 100%;"', 'value', 'text', $item->gids_view_selected);?></div>
			</li>
		</ul>
		
	</div>
	
 </form>