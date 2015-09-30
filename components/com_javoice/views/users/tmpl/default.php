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
<?php 
	$user = JFactory::getUser();
	$uid = JRequest::getInt('uid');
	$Itemid = JRequest::getInt('Itemid');
	JHTML::script('components/com_javoice/asset/js/ja.users.js');
?>
<script type="text/javascript" charset="utf-8">
//<![CDATA[
	var jav_base_url = '<?php echo JURI::base()?>';	
	var jav_tab_active = 0;
	var jav_current_active_voice = 0;
	var Itemid = '<?php echo $Itemid?>';
	var uid = 0;
	<?php if($user->id>0 && $uid==$user->id){?>
	uid = '<?php echo $uid?>';
	<?php }?>
	jQuery(document).ready(function($){	
		jav_createTabs_users('#jav-user', 'index.php?option=com_javoice&view=items&layout=items&limitstart=0&tmpl=component&type=');
	});
	
//]]>
</script>
	
<!-- Loading -->
<div id="loader">
	<?php echo JText::_('LOADING')?>
</div>

<div id="jav-msg-loading" style="display:none">	
</div>

<div id="jav-pathway">
	<?php echo $this->getPatway();?>
</div>

<div id="jav-user" class="javtabs-mainwrap">

	<ul class="javtabs-title clearfix">
		<li class="jav-mainbox" id="jav-user_default">
			<a href="javascript:void(0)" class="jav-mainbox-user">
				<?php echo JText::_('STATISTIC');?>
			</a>
		</li>
		<?php if($user->id>0 && $uid==$user->id){?>
			<li class="jav-mainbox"  id="jav-user_emailref">
				<a href="javascript:void(0)" class="jav-mainbox-emailref">
					<?php echo JText::_('EMAIL_PREFERENCE');?>
				</a>
			</li>
			<!--<li class="jav-mainbox" >
				<a href="<?php echo JURI::base(); ?>index.php?option=com_javoice&amp;view=users&amp;uid=<?php echo $uid?>&amp;layout=invitefriends&amp;tmpl=component&amp;Itemid=<?php echo $Itemid?>" class="jav-mainbox-invitefriends">
					<?php echo JText::_('INVITE_FRIENDS');?>
				</a>
			</li>
		--><?php }?>
	</ul>
	
	<div class="javtabs_container users">
		<div class="javtabs-panel" id="jav-mainbox-user">		
			<!-- COL1 -->
			<div id="jav-col-left" class="jav-col1-fr">
	    	<!-- LOAD LIST OF ITEMS -->
	    	<?php if($this->types){?>
	    		<?php foreach ($this->types as $type){?>
	    			<div class="jav-voice-type">
		    			<h3><a href="<?php echo JRoute::_('index.php?option=com_javoice&amp;view=items&amp;type='. $type->id.'&Itemid='.$Itemid)?>"><?php echo $type->title?></a></h3>
		    			
		    			<?php JRequest::setVar('type', $type->id);?>
		    			<?php JRequest::setVar('limit', 50);?>
		    			
				    	<div id="jav-list-items-<?php echo $type->id?>" class="jav-list-items"> 
			    			<?php echo $this->getItems()?>
				    	</div>
			    	</div>
	    		<?php }?>
	    	<?php }?>
				<!-- //LOAD LIST OF ITEMS -->
			</div>
			<!-- //COL1 -->		
		</div>
		
		<?php if($user->id>0 && $uid==$user->id){?>
	  	<div class="javtabs-panel" id="jav-mainbox-emailref"></div>
	  	<div class="javtabs-panel" id="jav-mainbox-invitefriends"></div>
	  <?php } ?>
	</div>
	
</div>