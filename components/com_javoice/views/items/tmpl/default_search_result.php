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
global $javconfig;
$Itemid = JRequest::getInt('Itemid');
$enable_after_the_deadline 		= $javconfig['plugin']->get('enable_after_the_deadline',1);
$db = JFactory::getDBO();
$key = rawurlencode(JRequest::getVar('key'));
$link = JURI::base().'index.php?option=com_javoice&view=items&type='.$this->type_id.'&layout=items&tmpl=component&amp;Itemid='.$Itemid;
$callAddNewButton = 0;
if(isset($this->callAddNewButton)){
	$callAddNewButton = 1;
}
?>

<ol>
	<li class="jav-back">
		<a onclick="jav_reset('key-<?php echo $this->type_id?>'); jav_findWord(event, jQuery('#key-<?php echo $this->type_id?>')[0], '<?php echo $link?>', '<?php echo $this->type_id?>'); return false;" href="javascript:void(0)">
			<?php echo JText::_('BACK_TO_TOP')?>
		</a>
	</li>
	<?php
	
	$count = isset($this->items)?count($this->items):0; if($count> 0 && $javconfig["systems"]->get("is_use_vote",1) && $callAddNewButton==0){?>
	<li class="jav-total-matches">
		<h3><?php echo JText::_('VOTE_FOR_ONE_OF_THESE')?></h3>
		<small><?php echo $count?> <?php echo JText::_('MATCHES')?></small>
	</li>
	
	<li class="jav-total-matches"><em><?php echo JText::_('OR')?></em></li>
	<?php }?>
	<li>
		<form>
			<input type="button" value="<?php echo JText::_('CREATE_NEW')?> <?php echo $this->type->title?>" name="commit" class="submit-post" onclick="jaCreatForm('&layout=add&type=<?php echo $this->type_id?>&key=<?php echo addslashes($key);?>','', 700, 350,'<?php echo JText::_('A').' '.$this->type->title?>');get_check_atd('<?php echo $enable_after_the_deadline ;?>');"/>
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php if($javconfig["systems"]->get("is_use_vote",1)):?>
		<small><?php echo JText::_('1_VOTE_REQUIRED')?></small>
		<?php endif;?>
		<?php if($callAddNewButton){?>
		<input type="hidden" value="1" name="isshowaddnewbutton" id="isshowaddnewbutton-<?php  echo $this->type->id;?>"/>
		<?php }?>
	</li>
</ol>