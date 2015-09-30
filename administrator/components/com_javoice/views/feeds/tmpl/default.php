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
  
 $ordering = ($lists['filter_order'] == 'f.ordering');
 
 ?> 
<script type="text/javascript">
function submitbutton(pressbutton) {
	var form = document.adminForm;
	form.task.value = pressbutton;
	form.submit();
	return true;
}
</script>
 <form action="index.php" method="post" name="adminForm" id="adminForm"> 

 	<input type="hidden" name="option" value="com_javoice" /> 

 	<input type="hidden" name="task" value="" /> 

 	<input type="hidden" name="boxchecked" value="0" /> 

 	<input type="hidden" name="view" value="feeds" /> 

 	<input type="hidden" name="filter_order" value="<?php echo $lists['filter_order']; ?>" /> 

 	<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['filter_order_Dir']; ?>" /> 
	
 	<table>
		<tr>
	
			<td align="left" width="100%">
				<?php echo JText::_("FILTER" ); ?>:
				<input type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area" />
				<button onclick="this.form.submit();"><?php echo JText::_("GO" ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.getElementById('sectionid').value='-1';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_('RESET' ); ?></button>
			</td>
			<td style="width: 50px;">
					<a id='jav_help' href="index.php"onclick="hiddenNote('forums','<?php echo JText::_('HELP')?>','<?php echo JText::_('CLOSE')?>');return false;"><?php echo JText::_('HELP')?></a>		
			</td>				
		</tr>
		<tr>
			<td colspan="2">
			
				<?php 
					$note = JText::_("THE_RSS_FEED_DELIVERS_VOICE_SEARCH_RESULTS_AS_AN_RSS_FEED_YOU_CAN_THEN_SAVE_THOSE_RESULTS_IN_ANY_FAVOURITE_FEED_AGGREGATOR_OR_INCORPORATE_THAT_DATA_INTO_YOUR_OWN_WEBSITE_OR_CLIENT_APPLICATION_THE_RSS_FEED_IS_A_DYNAMICALLYGENERATED_FEED_START_WITH_THE_BASE_URL_ADD_PARAMETERS_FOR_YOUR_FORUMSVOICE_TYPES_CREATOR_DATE_CREATE_YOULL_GET_BACK_JUST_THE_INFORMATION_YOUVE_REQUESTED" );
					JAVoiceHelpers::displayNote($note,'forums');
				?>				
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

 				<th width="20%">
					<?php echo JHTML::_('grid.sort',   "TITLE", 'f.title', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
				</th> 
				<th>
					<?php echo JText::_("DESCRIPTION")?>
				</th>
				<th>
					<?php echo JText::_("PREVIEW")?>
				</th>
 				<th class="">
					<?php echo JHTML::_('grid.sort',   "PUBLISHED", 'f.published', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
				</th> 

 				<th class="">
					<?php echo JHTML::_('grid.sort',   "ID", 'f.id', @$lists['filter_order_Dir'], @$lists['filter_order'] ); ?>
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

			JFilterOutput::objectHtmlSafe($item);

			$link = 'index.php?option=com_javoice&view=feeds&task=edit&cid[]='. $item->id;
			$item->checked_out=1;
			$checked 	= JHTML::_('grid.id',$i,$item->id );
			$published 	= JHTML::_('grid.published', $item, $i );	
			$published="<span id='publish$item->id'>$published</span>";	
			$title=JText::_('EDIT_FORUM')." ID: ".$item->id;					
			?> 

 		<tr class="<?php echo "row$k"; ?>"> 

 				<td align="center">
					<?php echo $page->getRowOffset( $i ); ?>
				</td>
				<td align="center">
					<?php echo $checked; ?>
				</td> 

 				<td>
 					<a href="<?php echo $link;?>">
 						<span id='title<?php echo $item->id?>'>
							<?php echo $item->feed_name;?>
						</span>
					</td>
				</td> 
				
				<td>
					<span id='voice-types-<?php echo $item->id?>'>
						<?php echo $item->feed_description; ?>
					</span>
				</td>
 				<td>
 					<a href="<?php echo JURI::root()."index.php?option=com_javoice&view=feeds&layout=rss&alias={$item->feed_alias}"?>" target="_blank"><?php echo JText::_("VIEW");?></a>
 				</td>
 				<td align="center">
						<?php echo $published;?>
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
  <script>
jQuery(document).ready( function($) {	
	var coo = getCookie('hidden_message_forums');
	if(coo==1)
		$('#jav-message').attr('style','display:none');
});	
</script>