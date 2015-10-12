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

defined( '_JEXEC' ) or die( 'Restricted access' );?>
<script type="text/javascript" charset="utf-8">
//<![CDATA[
function submit_email_preference(){
	var mainUrl = jav_base_url + "index.php?tmpl=component&option=com_javoice&view=users&task=setEmailNotificationReferences&votedVoiceUpdateNotification="+jQuery('#votedVoiceUpdateNotification').val()+ '&receive='+jQuery('#receive').val()+ '&often='+jQuery('#often').val();
	jav_ajax_load(mainUrl);
}
//]]>
</script>
<?php if ($this->user->id) { ?>
<div id="jav-email-preference">
  <h3>
   <span><?php echo JText::_('EMAIL_PREFERENCE_CENTER')?></span>
  </h3>
  <div class="jav-msg-successful"></div>
  <form method="post">
    <p>
      <?php echo JText::_('SELECT_WHICH_TYPES_OF_EMAIL_NOTIFICATIONS_YOU_WISH_TO_RECEIVE')?>
    </p>
    
    <p>
    	<input type="checkbox" value="1" id="votedVoiceUpdateNotification" <?php echo $this->user->getParam('votedVoiceUpdateNotification')? 'checked="checked"' : '' ?> />
      	<label for="votedVoiceUpdateNotification"><?php echo JText::_('SEND_ME_EMAILS_WHEN_THE_VOICES_I_VOTED_FOR_ARE_UPDATED').'.'; ?></label>
    </p>
    <p>
    	<input type="checkbox" value="1" id="receive" <?php echo $this->user->getParam('receive')? 'checked="checked"' : '' ?> />
      	<label>
      		<label for="receive"><?php echo JText::_('RECEIVE')?> </label>
      		<select id="often">
      			<option value="7" <?php if($this->user->getParam('often')==7) echo "'selected'";?>><?php echo JText::_('WEEKLY')?></option>
      			<option value="1" <?php if($this->user->getParam('often')==1) echo "'selected'";?>><?php echo JText::_('DAILY')?></option>
      		</select>
      		<?php echo JText::_('SUMMARY'); ?>
      	</label>
    </p>    
    <p>
      <input type="button" value="<?php echo JText::_('SAVE'); ?>" onclick="submit_email_preference()"/>
    </p>
  </form>
</div>
<?php } ?>
