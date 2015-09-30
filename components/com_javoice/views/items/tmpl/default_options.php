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
$forums = '';
if (JRequest::getInt('forums')) $forums = '&amp;forums='.JRequest::getInt('forums');
?>
<?php $Itemid = JRequest::getInt('Itemid');?>
<?php $default = false;?>
<ul>
<li class="first <?php if(JRequest::getString('order')=='total_vote_up desc' || (!JRequest::getInt('status') && !JRequest::getString('order'))){ $default = true;?> current <?php }?>">
	<?php $link = JURI::base().'index.php?option=com_javoice&amp;view=items&amp;layout=search&amp;tmpl=component&amp;order=total_vote_up desc&amp;type='.$this->type->id.'&amp;Itemid='.$Itemid.$forums;?>
	<a onclick="change_options(this, '<?php echo $link?>', <?php echo $this->type->id?>);" href="javascript:void(0)">
		<?php echo JText::_('TOP')?>
		<small <?php if(!$default){?> style="display: none;" <?php }?>> <?php echo $this->type->alias?></small>
		
	</a>
</li>
<li <?php if(JRequest::getString('order')=='create_date desc'){?> class="current" <?php }?>>
	<?php $link = JURI::base().'index.php?option=com_javoice&amp;view=items&amp;layout=search&amp;tmpl=component&amp;order=create_date desc&amp;type='.$this->type->id.'&amp;Itemid='.$Itemid.$forums;?>
	<a onclick="change_options(this, '<?php echo $link?>', <?php echo $this->type->id?>);" href="javascript:void(0)">
		<?php echo JText::_('NEW')?>
		<small <?php if(JRequest::getString('order')!='create_date desc'){?>style="display: none;"<?php }?>> <?php echo $this->type->alias?></small>
		
	</a>
</li>

<?php		
if($this->list_options){
	$tem = array();
	foreach ($this->list_options as $k=>$status){
		$link = JURI::base().'index.php?option=com_javoice&amp;view=items&amp;layout=search&amp;tmpl=component&amp;status='.$status->id.'&amp;type='.$this->type->id.'&amp;Itemid='.$Itemid.$forums;
		?>
		 <li class="<?php if($k==count($this->list_options)-1) echo 'last '; ?><?php if(JRequest::getInt('status')==$status->id){?> current <?php }?>">
			<a onclick="change_options(this, '<?php echo $link?>', <?php echo $this->type->id?>);" href="javascript:void(0)">
				<?php echo $status->title?>
				<small <?php if(JRequest::getString('status')!=$status->id){?>style="display: none;"<?php }?>> <?php echo $this->type->alias?></small>
				
			</a>
		</li>
	<?php }
}	
?>
</ul>