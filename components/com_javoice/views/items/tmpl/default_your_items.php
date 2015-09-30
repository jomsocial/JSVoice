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
<h3><span><?php echo JText::_('YOUR_IDEAS')?></span></h3>

<ul class="jav-menu">	    		
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
					<h2 class="jav-contentheading clearfix">
						<?php $link = JRoute::_('index.php?option=com_javoice&view=items&layout=item&cid='.$item->id.'&type='.$item->voice_types_id.'&amp;Itemid='.$Itemid);?>
						<a href="<?php echo $link;?>" class="title"><?php echo $item->title?></a>
						
						<span class="jav-item-status">
							<?php if($item->voice_type_status_id){?>
								<span class="jav-tag" style="background: <?php echo $item->status_class_css?>"> <?php echo $item->status_title?> </span>								
							<?php }?> 
						</span>
					</h2>		
				</div>
				
		</li>										
	<?php }?>
</ul>	
<?php }?>