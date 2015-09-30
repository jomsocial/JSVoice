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
$item=$this->item;
JHTML::_('behavior.tooltip');
?> 
<style type="text/css">
form .ja-haftleft{
	width: 30%;
}
form .ja-haftright{
	width: 62%;
}
</style>
<script>
	var siteurl = '<?php echo JURI::base()."index.php?tmpl=component&option=com_javoice&view=voicetypesstatus";?>';
</script>
 <form name="adminForm" id="adminForm"  action="index.php" method="post"> 

 	<input type="hidden" name="option" value="com_javoice" /> 

 	<input type="hidden" name="view" value="voicetypesstatus" /> 
 	
 	<input type="hidden" name="tmpl" value="component" />
 	
 	<input type="hidden" name="number" value="<?php echo $this->number;?>"/>

 	<input type="hidden" name="task" value="saveIFrame" /> 

 	<input type="hidden" name='id' id='id' value="<?php echo $item->id?>"/> 

	<input type="hidden" name='cid[]' id='cid' value="<?php echo $item->id?>"/>
	 
	<ul>
		<li class="ja-haftleft">
			<label class="desc" for="title"><?php echo JText::_("TITLE" );?></label>
			<div><input onchange="checkdataString(this,'error')" type="text" name="title" id="title" value="<?php echo $item->title?>" class="text required" size='20'/></div>
		</li>
		<li class="ja-haftright">
			<label class="desc" for="voice_types_id"><?php echo JText::_("VOICE_TYPES" );?></label>
			<div><?php echo $this->displayVoicetypes;?></div>			
		</li>
		
		<li class="ja-haftleft">
			<label class="desc" for="published0"><?php echo JText::_("PUBLISHED" );?></label>
			<div>
				<?php
				$published = ($item->published==1) ? $item->published : 0;
				$lists['published'] 		= JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $published );
			 	echo $lists['published']; ?>
			</div>		
		</li>
		
		<li class="ja-haftright">
			<label class="desc editlinktip hasTip" for="allow_show0" title="<?php echo JText::_("RETURN_VOTE" );?>::<?php echo JText::_("RETURN_VOTE_DESC" );?>">
				<?php echo JText::_("RETURN_VOTE" );?>
			</label>
			<div>
				<?php
					$return_vote = ($item->return_vote==1) ? $item->return_vote : 0;
					$lists['return_vote'] 		= JHTML::_('select.booleanlist',  'return_vote', 'class="inputbox"', $return_vote );
				echo $lists['return_vote']; ?>
			</div>
		</li>
				
		<li class="ja-haftright">
			<label class="desc editlinktip hasTip" for="allow_show0" title="<?php echo JText::_("VOICE_DISPLAY_AT_FRONEND_WHEN_STATUS_IS_ASSIGNED" );?>::<?php echo JText::_("VOICE_DISPLAY_AT_FRONEND_WHEN_STATUS_IS_ASSIGNED_TOOLTIP" );?>">
				<?php echo JText::_("VOICE_DISPLAY_AT_FRONEND_WHEN_STATUS_IS_ASSIGNED" );?>
			</label>
			<div>
				<?php
					$allow_show = ($item->allow_show==1) ? $item->allow_show : 0;
					$lists['allow_show'] 	= JHTML::_('select.booleanlist',  'allow_show', ' onchange="if(this.value==0){$(\'allow-voting-box\').setStyle(\'display\',\'none\');} else{$(\'allow-voting-box\').setStyle(\'display\',\'\');}"', $allow_show );
					echo $lists['allow_show']; ?>
			</div>
		</li>
		<li class="ja-haftleft">			
			<label class="desc" for="class_css"><?php echo JText::_("CSS_CLASS" );?></label>
			<div style="float:left;" id='class_css<?php echo $item->id?>'><input type="text" name="class_css" id="class_css" value="<?php echo $item->class_css?>" class="text" size='10'/></div>
			<div style="float:left;">
				<a  href="index.php" onclick='jaCreatFormColor("color","#class_css<?php echo $item->id?>",289,95,"<?php echo $item->id;?>","<?php echo JText::_("SELECT"); ?>","<?php echo JText::_("CANCEL"); ?>",0);return false;'>
					<img src="<?php echo JURI::base()?>components/com_javoice/asset/images/paintbrush.png"> 
				</a>								
			</div>				
		</li>
		<li class="ja-haftright" id="allow-voting-box" <?php if(!$item->allow_show){?> style="display: none" <?php }?>>
			<label class="desc editlinktip hasTip" for="allow_voting0" title="<?php echo JText::_("VOICE_CAN_BE_VOTED_WHEN_STATUS_IS_ASSIGNED" );?>::<?php echo JText::_("VOICE_CAN_BE_VOTED_WHEN_STATUS_IS_ASSIGNED_TOOLTIP" ); ?>">
				<?php echo JText::_("VOICE_CAN_BE_VOTED_WHEN_STATUS_IS_ASSIGNED" );?>
			</label>
			<div><?php
				$allow_voting = ($item->allow_voting==1) ? $item->allow_voting : 0;
				$lists['allow_voting'] 		= JHTML::_('select.booleanlist',  'allow_voting', 'class="inputbox"', $allow_voting );
				echo $lists['allow_voting']; ?>
			</div>
		</li>
		
	</ul>
 </form>