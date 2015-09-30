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
	global $javconfig;
	$items = $this->items;
	
?>
<div class="componentheading"><?php echo $javconfig['emails']->get('sitename')." RSS Feed"; ?></div>
<span>
	<?php 
		$msg = JText::_("THE").$javconfig['emails']->get('sitename').JText::_("RSS_FEED_DELIVERS_VOICE_SEARCH_RESULTS_AS_AN_RSS_FEED");
		$msg .= JText::_("YOU_CAN_THEN_SAVE_THOSE_RESULTS_IN_ANY_FAVOURITE_FEED_AGGREGATOR_OR_INCORPORATE_THAT_DATA_INTO_YOUR_OWN_WEBSITE_OR_CLIENT_APPLICATION");
		$msg .= JText::_("THE_RSS_FEED_IS_A_DYNAMICALLYGENERATED_FEED_START_WITH_THE_BASE_URL_ADD_PARAMETERS_FOR_YOUR_FORUMSVOICE_TYPES_CREATOR_DATE_CREATE");
		$msg .= JText::_("YOULL_GET_BACK_JUST_THE_INFORMATION_YOUVE_REQUESTED");
		echo $msg;
	?>
</span>
<br /><br />
<span>

<h3>
		<?php 
			echo JText::_("STANDARD_RSS");
		?>
</h3>

<table class="tablelist" width="100%">
	<thead>
		<tr class="sectiontableheader">
			<td width="3%">#</td>
			<td  width="30%">
				<?php 
					echo JText::_("TITLE");
				?>
			</td>
			<td  width="25%">
				<?php 
					echo JText::_("SIMPLE_URL");
				?>
			</td>			
			<td  width="40%">
				<?php 
					echo JText::_("DESCRIPTION");
				?>
			</td>
		</tr>
	</thead>
 		<tfoot>
		<tr>
			<td colspan="4">				
				<?php echo $this->pagination->getPagesLinks();
			?>
			</td>			
		</tr>
		</tfoot> 	
		<tbody>
    		<?php
            $k = 0;
            for ($i=0, $n=count( $items ); $i < $n; $i++) {
            	$item = $items[$i];
    		?>
    	
            <tr class="sectiontableentry<?php echo $k?>">
                <td><?php echo $this->pagination->getRowOffset( $i ); ?></td>
                <td >
                    <a href="<?php echo JRoute::_("index.php?option=com_javoice&view=feeds&layout=form&cid[]=$item->id"); ?>" >
                    	<?php echo $item->feed_name;?>
                    </a>
                </td>
                <td>
                    <a href="<?php echo JRoute::_("index.php?option=com_javoice&view=feeds&layout=rss&alias={$item->feed_alias}")?>" target="_blank">
                    	<?php echo JURI::root().JRoute::_("index.php?option=com_javoice&view=feeds&alias={$item->feed_alias}")?>
                    </a>
                </td>
                
                <td >                   
                    <?php echo $item->feed_description; ?></a>
                </td>
            </tr>
            <?php	
            $k = $k==2?1:2;
            }
        ?>
        </tbody>
</table>
<a class="button" href="<?php echo JRoute::_("index.php?option=com_javoice&view=feeds&layout=form"); ?>" target="_blank"><?php echo JText::_("CREATE_ADVANCE_RSS"); ?></a>
</span>
<br><br>
<table class="tablelist" width="100%">
	<tr>
		<td class="sectiontableheader" width="10%">
			<?php 
				echo JText::_("PARAMETER");
			?>
		</td>
		<td class="sectiontableheader" width="90%">
			<?php 
				echo JText::_("DESCRIPTION");
			?>
		</td>
	</tr>
	<tr class="first sectiontableentry1">	
		<td>
			<b>type</b>
		</td>
		<td>
			<?php 
				echo JText::_("WE_SUPPORT_FOLLOW_TYPE_RSS_RSS_091_RSS_10_RSS_20_MBOX_OPML_ATOM_ATOM_03_HTML_JS_EG_TYPE");
			?>
		</td>
	</tr>
	<tr class="sectiontableentry2">	
		<td>
			<b>feed_name</b>
		</td>
		<td>
			<?php 
				echo JText::_("TITLE_OF_THE_FEED");
			?>
		</td>
	</tr>
	<tr class="sectiontableentry1">	
		<td>
			<b>feed_description</b>
		</td>
		<td>
			<?php 
				echo JText::_("SHORT_DESCRIPTION_OF_THE_FEED");
			?>
		</td>
	</tr>
	<tr class="sectiontableentry1">	
		<td>
			<b>filter_forums_title</b>
		</td>
		<td>
			<?php 
				echo JText::_("THE_FROUMS_OF_VOICE_SEPERATED_BY_COMMA_EG_FILTER_FORUMS_TITLETHISFORUMSTR");
			?>
		</td>
	</tr>
	<tr class="sectiontableentry2">	
		<td>
			<b>filter_voicetypes_title</b>
		</td>
		<td>
			<?php 
				echo JText::_("THE_VOICES_TYPE_OF_VOICE_SEPERATED_BY_COMMA_EG_FILTER_VOICETYPES_TITLETHISVOICE_TYPESSTR");
			?>
		</td>
	</tr>
	<tr class="sectiontableentry1">	
		<td>
			<b>filter_date</b>
		</td>
		<td>
			<?php 
				echo JText::_("THE_VOICE_CREATE_XX_DAYS_A_GO_EG_FILTER_DATE30_WILL_LIST_ALL_THE_VOICE_CREATE_30_DAYS_AGO");
			?>
		</td>
	</tr>
	
	<tr class="sectiontableentry1">	
		<td>
			<b>msg_count</b>
		</td>
		<td>
			<?php 
				echo JText::_("THE_LIMIT_NUMBER_OF_VOICE_GENERATED_BY_RSS");
			?>
		</td>
	</tr>
	<tr class="sectiontableentry1">	
		<td>
			<b>filter_item_ids</b>
		</td>
		<td>
			<?php 
				echo JText::_("THE_INCLUDED_VOICE_OF_WHICH_YOU_WANT_TO_GET_VOICES_FROM_EG_FILTER_ITEM_IDS4554_DISPLAY_OPEN_VOICE_FROM_VOICE_ID_45_AND_54");
			?>
		</td>
	</tr>
	<tr class="sectiontableentry2">	
		<td>
			<b>filter_creator_ids</b>
		</td>
		<td>
			<?php 
				echo JText::_("THE_VOICE_OF_WHICH_YOU_WANT_TO_GET_VOICES_FROM_EG_FILTER_CREATOR_IDS6263_WILL_DISPLAY_ALL_VOICE_FROM_CREATOR_HAS_USER_ID_6263");
			?>
		</td>
	</tr>
</table>
<a class="button" href="<?php echo JRoute::_("index.php?option=com_javoice&view=feeds&layout=form"); ?>" target="_blank"><?php echo JText::_("CREATE_ADVANCE_RSS"); ?></a>