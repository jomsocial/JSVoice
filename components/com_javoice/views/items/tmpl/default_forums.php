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
<?php if(!$javconfig["systems"]->get("is_enable_tagging", 0) || !JRequest::getVar("tagid",0)):?> 
<input type="hidden" id="forums-<?php echo $this->type_id?>" value="<?php echo JRequest::getInt('forums')?>"/>
<h3><span><?php echo JText::_('FORUMS')?></span></h3>
<ul class="jav-menu">
<?php
$Itemid = JRequest::getInt('Itemid');
$list_forums = $this->list_forums;
$status = '';
if(JRequest::getInt('status')) $status = '&amp;status='.JRequest::getInt('status');
if($list_forums){
	foreach ($list_forums as $forum){
		$link = JURI::base().'index.php?option=com_javoice&amp;view=items&amp;layout=items&amp;tmpl=component&amp;forums='.$forum->id.'&amp;type='.$this->type_id.'&amp;Itemid='.$Itemid.$status;
		?>
		 <li>         	            
			<a <?php if(JRequest::getInt('forums')==$forum->id){?>class="current"<?php }?> onclick="jav_clean_search(<?php echo $this->type_id?>,<?php echo $forum->id?>); jav_ajax_load('<?php echo $link?>', <?php echo $this->type_id?>); " href="javascript:void(0)">
				<?php echo $forum->title?>
				<small>(<?php echo $forum->total_items?>)</small>
			</a>
		</li>
	<?php }
}	
?>
</ul>
<?php endif;?>
