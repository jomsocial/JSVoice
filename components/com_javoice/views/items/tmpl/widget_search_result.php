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
?>
<?php $Itemid = JRequest::getInt('Itemid');?>
<ol>
	<!--<li class="back">
		<a onclick="jav_reset('key-<?php echo $this->type_id?>'); jav_findWord(jQuery('#key-<?php echo $this->type_id?>')[0], '<?php echo JURI::base(); ?>index.php?option=com_javoice&view=items&type=<?php echo $this->type_id?>&layout=items&tmpl=component&amp;Itemid=<?php echo $Itemid?>', '<?php echo $this->type_id?>'); return false;" href="javascript:void(0)">
			<?php echo JText::_('BACK_TO_TOP_IDEAS')?>
		</a>
	</li>
	--><?php $count = count($this->items); if($count>0 && $javconfig["systems"]->get("is_use_vote",1)){?>
	<li class="jav-total-matches">
		<h3><?php echo JText::_('VOTE_FOR_ONE_OF_THESE')?></h3>
		<small><?php echo $count?> <?php echo JText::_('MATCHES')?></small>
	</li>
	
	<li class="jav-total-matches"><em><?php echo JText::_('OR')?></em></li>
	<?php }?>
	<li>
		<a class="button" target="_blank" href="<?php echo JRoute::_('index.php?option=com_javoice&view=items&layout=form&type='.$this->type_id.'&key='.JRequest::getString('key'))?>" title="<?php echo JText::_('CLICK_HERE_TO_CREATE_NEW')?> <?php echo $this->type->title?>"><?php echo JText::_('CREATE_NEW')?> <?php echo $this->type->title?></a>		
		<?php if($javconfig["systems"]->get("is_use_vote",1)):?>
		<small><?php echo JText::_('1_VOTE_REQUIRED')?></small>
		<?php endif;?>
	</li>
</ol>