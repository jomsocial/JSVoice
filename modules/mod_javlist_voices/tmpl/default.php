<?php
/**
 *$JA#COPYRIGHT$
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<ol class="jav-voices">
<?php if($list):?>
  <?php foreach ($list as $item) : ?>
	  <li class="jav-has-layout">
	  	<p class="votes">			
			<strong id="jav-total-votes-of-user-<?php echo $item->id?>" class="up">
				<?php echo $item->total_vote_up?>
			</strong>
			<?php if($item->total_vote_down>0 && $item->has_down){?>
			<strong id="jav-total-votes-of-user-down-<?php echo $item->id?>" class="down">
				-<?php echo $item->total_vote_down?>
			</strong>
			<?php }?>  		
		</p>
		<h4>
	   		<?php
				$link = JRoute::_('index.php?option=com_javoice&view=items&layout=item&cid='.$item->id.'&type='.$item->voice_types_id.'&Itemid='.$Itemid);
			?>
			<a href="<?php echo $link?>" class="jav-item-title"><?php echo $item->title?></a>	    		    
	    
	    	<?php if($params->get('showstatus') == 1 && trim($item->status_title) != '') { ?>
	    		<span class="jav-tag" style="background: <?php echo $item->status_class_css?>"> <?php echo $item->status_title?> </span>
	   		<?php } ?>
	  	</h4>  	    	   	
	    
	  </li>
  <?php endforeach; ?>	
<?php endif;?>
</ul>
<div style="float: right">
	<a href="<?php echo JRoute::_('index.php?option=com_javoice&Itemid='.$Itemid)?>"><?php echo JText::_('Search')?></a> 
	<?php echo JText::_('or')?> 
	<a href="<?php echo JRoute::_('index.php?option=com_javoice&view=items&layout=form&type='.$types[0]->id.'&search=yes&Itemid='.$Itemid)?>"><?php echo JText::_('Post your')?> <?php echo $types[0]->title?></a>
</div>