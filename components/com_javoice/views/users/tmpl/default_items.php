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

$uid = JRequest::getInt('uid');
$items = $this->items;
$user = JFactory::getUser($uid);
$Itemid = JRequest::getInt('Itemid');

?>

<?php if($items){?>
<ol>	    		
	<?php foreach ($items as $item){?>
		<?php 
		if( !$user->id && isset( $_COOKIE[md5( 'jav-view-item' ) . $item->id] ) ){
			$item->votes =  $_COOKIE[md5( 'jav-view-item' ) . $item->id];
		}
		?>
		<li class="jav-box-item-user <?php if(isset($item->votes)){?>selected<?php }?>" id="jav-box-item-<?php echo $item->id;?>">
			<div  class="jav-item">
					<?php if($javconfig ['systems']->get ( 'is_use_vote', 1 )):?>
					<div class="jav-badge">
						<div class="jav-item-points jav-big-number">
							<strong id="jav-total-votes-of-user-<?php echo $item->id?>"><?php echo $item->total_vote_up?></strong> 
							<!--<span><?php echo JText::_('VOTES')?></span>-->
						</div>
						
						<div class="jav-moderation">
							<?php if(isset($item->votes) && $item->votes){?>
							<span class="votes value-<?php echo $item->votes?>"><?php echo $item->votes?></span>
							<?php }?>													
						</div>
					</div>
					<?php endif; ?>
				<div class="jav-item-details<?php if(!$javconfig ['systems']->get ( 'is_use_vote', 1 )):?> jav-detail-no-vote<?php endif;?>">
					<h2 class="jav-contentheading clearfix">
						<?php
						$link = JRoute::_('index.php?option=com_javoice&amp;view=items&amp;layout=item&amp;cid='.$item->id.'&amp;type='.$item->voice_types_id.'&amp;Itemid='.$Itemid);						
						?>
						<a href="<?php echo $link?>" class="jav-item-title"><?php echo $item->title?></a>												
						<?php if($item->voice_type_status_id){?>
						<span class="jav-item-status">							
							<span class="jav-tag" style="background: <?php echo $item->status_class_css?>"> <?php echo $item->status_title?> </span>															
						</span>
						<?php }?>
					</h2>		
					<p>
						<?php
						if(!$javconfig['plugin']->get('enable_bbcode',1))
							$item->content = str_replace("\n", '<br/>', $item->content);
							
						$maxchars = $javconfig['systems']->get('maxchars', 100);
						if($maxchars==-1){
							echo $item->content;
						}
						elseif($maxchars>0){
							if (function_exists ( 'mb_substr' )) {
								$doc = JDocument::getInstance ();
								echo SmartTrim::mb_trim($item->content, 0, $javconfig['systems']->get('maxchars', 100), $doc->_charset);
							}else{
								echo SmartTrim::trim($item->content, 0, $javconfig['systems']->get('maxchars', 100));
							}
						}
						// echo $item->content;
						?>
					</p>																		
				</div>
				
			</div>
		</li>										
	<?php }?>
</ol>	
<?php }else{?>
	<?php echo JText::_('NO_ITEM')?>
<?php }?>