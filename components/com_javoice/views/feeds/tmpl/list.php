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
			<td  width="20%">
				<?php 
					echo JText::_("TITLE");
				?>
			</td>
			<td  width="55%">
				<?php 
					echo JText::_("SIMPLE_URL");
				?>
			</td>			
			<td width="20%">
				<?php 
					echo JText::_("DESCRIPTION");
				?>
			</td>
		</tr>
	</thead>
 		<tfoot>
		<tr>
			<td colspan="4">		
				<?php echo $this->pagination->getPagesLinks();?>
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
                    	<?php echo $item->feed_name;?>
                </td>
                <td>
                    <p><a href="<?php echo JRoute::_("index.php?option=com_javoice&view=feeds&layout=rss&alias={$item->feed_alias}")?>" target="_blank">
                    	<?php echo JURI::base().substr(JRoute::_("index.php?option=com_javoice&view=feeds&layout=rss&alias={$item->feed_alias}"),  strlen(JURI::base(true)) + 1); ?>
                    </a></p>
                </td>
                
                <td >                   
                    <?php echo $item->feed_description; ?></a>
                </td>
            </tr>
            <?php	
            $k = $k==2?1:2;
            }
            if($this->permission){
            	?>
            		<tr>
		               <td colspan="4"> 
		                    <a class="button" href="<?php echo JRoute::_("index.php?option=com_javoice&view=feeds&layout=guide");?>" target="_blank">
		                    	<?php echo Jtext::_("Rss Manager");?>
		                    </a>	                    
		                </td>            		
            		</tr>
            	<?php 
            }
        ?>
        </tbody>
</table>