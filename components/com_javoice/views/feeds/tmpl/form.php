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

defined('_JEXEC') or die('Restricted access'); 
$lists = $this->lists;
$feed = new stdClass();
$feed = $this->feed;
?>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">

	<div class='jav_field_table'>
		<h3><?php echo JText::_('SETTINGS');?></h3>
		<div class="content">
			<ul>	
				<li>
					<table width="98%">				
				        <tr >
				            <td><?php echo JText::_('TITLE');?>:</td>
				            <td><input type="text" size="75" maxlength="255" name="feed_name" value="<?php echo($feed->feed_name); ?>" /><font color="Red">*</font></td>
				            <td><?php echo JText::_('TYPE_OF_FEEDS'); ?>:</td><td><?php echo $lists['rssTypeList']; ?></td>
				        </tr>
				        <tr >
				            <td><?php echo JText::_('ALIAS');?>:</td>
				            <td><input type="text" size="75" maxlength="255" name="feed_alias" value="<?php echo($feed->feed_alias); ?>" /></td>
				            <td><?php echo JText::_('MAX_CHARS'); ?>:</td><td><?php echo $lists['numWordsList']; ?></td>				           
				        </tr>
						
				        <tr >
				            <td><?php echo JText::_('NUMBER_VOICES'); ?>: </td><td><input type="text" size="3" maxlength="3" name="msg_count" value="<?php echo $feed->msg_count; ?>" /></td>
				             <td><?php echo JText::_('CACHE_TIMES'); ?>:</td><td><input type="text" size="10" maxlength="10" name="feed_cache" value="<?php echo $feed->feed_cache; ?>" /></td> 
				        </tr> 				 
				        <tr >
				            <td><?php echo JText::_('DESCRIPTION'); ?>:</td>
				            <td colspan="3"><textarea name="feed_description" style="width: 100%" rows="7"><?php echo $feed->feed_description; ?></textarea></td>
				        </tr>
				        
				    </table>		
				</li>
			</ul>		    
	     	<h3><?php echo JText::_('FORUMS_AND_VOICE_TYPES_FILTER'); ?></h3>
	     	<ul>
	     		<li>
		    	<table width="98%">
		        	<tr >
		            	<td valign="top"><?php echo JText::_('FORUMSS');?>:</td>
	            		<td valign="top"><?php echo $lists['filter_forums_id'];?></td>
	            		<td valign="top"><?php echo JText::_('VOICE_TYPES');?>:</td>
	            		<td valign="top"><?php echo $lists['filter_voice_types_id'];?></td>
		            	<td valign="top">
		            		<div>
		            			<div>
		            				<div style="float: left;">
				            			<?php echo JText::_('CREATE_DATE_DAYS'); ?>:
				            		</div>
				            		<div>
				            		
				            			<input type="text" name="filter_date" value="<?php echo $feed->filter_date; ?>" /><?php echo JText::_('DAYS_AGO'); ?>
				            		</div>
			            		</div>
			            		<div>
			            			<small><?php echo JText::_('EG_IF_THE_VALUE_IS_5_ONLY_VOICES_IS_CREATED_IN_THE_LAST_5_DAYS_IS_LISTED');?></small>
			            		</div>
		            		</div>
		            		<div style="padding-top: 10px;">
		            			<div style="float: left;padding-right: 65px;">
		            				<?php echo Jtext::_("Status").": "; ?>
		            			</div>
		            			<div>
			            			<?php echo $lists['statuss'];?>
		            			</div>
		            		</div>
		            	</td> 
		        	</tr>

		    	</table>
	    	</li>
	    	</ul>
	    	<h3><?php echo JText::_('SPECIAL_FILTER'); ?></h3>
	    	<ul>
	    		<li>
			    	<table width="98%">
			    		<tr >
			            <td valign="top"><?php echo JText::_('INCLUDE_VOICEIDS'); ?>:</td>
			            <td>
			                <textarea name="filter_item_ids" style="width:90%;" rows="3" ><?php echo $feed->filter_item_ids; ?></textarea>
			                <br /><?php echo JText::_('ENTER_VOICEIDS_YOU_WANT_TO_INCLUDE'); ?>
			                <br /><?php echo JText::_('SEPERATE_ID_WITH_A_'); ?> <br /><?php echo JText::_('EXAMPLE_1_2_3_4_5_6_'); ?>
			            </td>
			            <td  valign="top"><?php echo JText::_('INCLUDED_CREATORIDS'); ?>:</td>
			            <td>
			                <textarea name="filter_creator_ids" style="width: 90%;" rows="3" ><?php echo $feed->filter_creator_ids; ?></textarea>
			                <br /><?php echo JText::_('ENTER_CREATORIDS_YOU_WANT_TO_INCLUDE'); ?>
			                <br /><?php echo JText::_('SEPERATE_ID_WITH_A_'); ?> <br /><?php echo JText::_('EXAMPLE_1_2_3_4_5_6_'); ?>
			            </td>
			        </tr>
			    	</table>
	    		</li>
	    	</ul>	    	
	    <div style="float: right;clear: both;">    
		    <input class="button" type="button" onclick="submitbutton('save');" value="<?php echo JText::_('SAVE'); ?>" />
		 	<input class="button" type="button" onclick="submitbutton('cancel');" value="<?php echo JText::_('CANCEL'); ?>" />	    	
	 	</div>
		</div>
	</div>

    <input type="hidden" name="cid[]" value="<?php echo $feed->id;?>" />
    <input type="hidden" name="option" value="com_javoice" />
    <input type="hidden" name="task" value="save" />
    <input type="hidden" name="view" value="feeds" />
    <input type="hidden" name="is_user" value="<?php echo (isset($feed->is_user))?$feed->is_user:1; ?>" ?>
    <?php echo JHTML::_( 'form.token' ); ?>
    
</form>
<script>
	function submitbutton(task){
		var form = document.adminForm;
		form.task.value =task;
		form.submit(); 
	}
</script>
