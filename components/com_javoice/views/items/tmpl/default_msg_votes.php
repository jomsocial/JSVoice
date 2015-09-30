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
defined( '_JEXEC' ) or die( 'Restricted access' );

?>
<div id="jav-dialog" style="display:none" >
	
	<div style="display: block;" onclick="UserVoice.Dialog.close(); return false;" class="uservoice-component" id="uservoice-overlay"></div>
	
	<div class="uservoice-component" id="uservoice-dialog">
		<a id="jav-dialog-close" onclick="jQuery('#jav-dialog').css('display', 'none'); return false;" href="#"></a>
		<div id="uservoice-dialog-content">
			<div id="what-now">
				  <h1><?php echo JText::_("YOURE_OUT_OF_VOTES")?> <small><?php echo JText::_('WHAT_NOW')?></small></h1>
				  <ol class="what-now" id="pane">
					    <li>
					      <div style="display:none; background: transparent url(/images/screenshots/out_of_votes/en/returned-votes-inbox.png) no-repeat scroll right -2px; -moz-background-clip: -moz-initial; -moz-background-origin: -moz-initial; -moz-background-inline-policy: -moz-initial;" class="screenshot votes-returned"></div>
					      <em><strong><?php echo JText::_('YOUR_VOTES_WILL_BE_RETURNED_WHEN_YOUR_IDEAS_ARE_COMPLETED_OR_DELETED')?></strong></em> (<?php echo JText::_("WELL_EMAIL_YOU")?>)
					      
					    </li>
					    <li>
					      <div style="display:none; background: transparent url(/images/screenshots/out_of_votes/en/change-your-vote.png) no-repeat scroll 0pt 0pt; -moz-background-clip: -moz-initial; -moz-background-origin: -moz-initial; -moz-background-inline-policy: -moz-initial;" class="screenshot change-votes"></div>
					      <?php echo JText::_('YOU_CAN')?> <em><?php echo JText::_('CHANGE_YOUR_VOTES_BY_CLICKING_ON_THEM')?></em>
					    </li>
					    <li>
					      <div style="display:none; background: transparent url(/images/screenshots/out_of_votes/en/activity-snippet.png) no-repeat scroll -12px 0pt; -moz-background-clip: -moz-initial; -moz-background-origin: -moz-initial; -moz-background-inline-policy: -moz-initial;" class="screenshot activity"></div>
					      <?php echo JText::_("TEXT_JAVOICE_ANY_ACTIVE")?>
					    </li>
				  </ol>
			</div>
		</div>
	</div>
</div>

<div id="jav-firsttime-voting" style="display: none;">
	
	<div style="display: block;" onclick="UserVoice.Dialog.close(); return false;" class="uservoice-component" ></div>
	
	<div class="uservoice-component" id="jav-uservoice-dialog">
		<a id="jav-dialog-close-1" onclick="jQuery('#jav-firsttime-voting').css('display', 'none'); return false;" href="#"></a>
		<div id="jav-uservoice-dialog-content">
			<div id="jav-what-now">
				  <?php echo JText::_("USE_YOUR_VOTES_WISELY_BECAUSE_YOU_HAVE_LIMITED_VOTES_YOUR_VOTE_WILL_BE_RETURNED_WHEN_YOUR_VOICE_IS_MARKED_COMPLETEDCLOSED");?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	var oldel = $('#jav-dialog');
	oldel.appendTo( document.body );
	oldel = $('#jav-firsttime-voting');
	oldel.appendTo( document.body );
	jav_showNoticeToCenter(400, 40, 'jav-firsttime-voting');
	$('#jav-firsttime-voting').css('display', 'none');
});
</script>