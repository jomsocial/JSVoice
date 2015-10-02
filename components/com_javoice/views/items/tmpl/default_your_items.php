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
<?php defined( '_JEXEC' ) or die( 'Restricted access' );?>

<?php $items = $this->your_items;?>
<?php $user = JFactory::getUser();?>
<?php $Itemid = JRequest::getInt('Itemid')?>
<?php if($items){?>

<ul class="jav-menu">	
<h4><span><?php echo JText::_('MY_VOTES')?></span></h4>		
	<?php foreach ($items as $item){?>
		<?php 
		if( !$user->id && isset( $_COOKIE[md5( 'jav-view-item' ) . $item->id] ) ){
			$item->votes =  $_COOKIE[md5( 'jav-view-item' ) . $item->id];
		}
		?>
		<li class="jav-your-box-item"> 
        	<?php if($javconfig ['systems']->get ( 'is_use_vote', 1 )):?>
			<div class="jav-badge">
				<div class="jav-moderation">
					<?php $onclick = "jav_showVoteOption('#jav-your-item-vote-{$this->type_id}-{$item->id}', '{$item->list_vote_value}', {$item->list_vote_text}, {$item->list_vote_description}, {$item->list_vote_msg}, '{$item->id}', '{$item->voice_types_id}', '','', '$Itemid')";?>
					<a class="votes value-<?php echo $item->votes?>" href="javascript:void(0)" onclick="<?php echo $onclick;?>">
						<?php echo $item->votes?>
					</a>					
					<div class="pop-in has-layout" id="jav-your-item-vote-<?php echo $this->type_id?>-<?php echo $item->id?>" style="display: none;"></div>					
				</div>
			</div>
			<?php endif;?>	
				<div class="jav-item-details <?php if(!$javconfig ['systems']->get ( 'is_use_vote', 1 )):?> jav-detail-no-vote<?php endif;?>">
					
						<?php $link = JRoute::_('index.php?option=com_javoice&view=items&layout=item&cid='.$item->id.'&type='.$item->voice_types_id.'&amp;Itemid='.$Itemid);?>
						<a href="<?php echo $link;?>" class="ja-title"><?php echo $item->title?></a>
				</div>
		</li>										
	<?php }?>
</ul>	
<?php }?>