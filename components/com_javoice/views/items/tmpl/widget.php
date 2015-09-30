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
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' ); 

?>
<?php JHTML::stylesheet('components/com_javoice/asset/css/ja.widget.css')?>
<?php if($this->types){?>
<?php
	$type_default = $this->types[0];
	$jav_tab_active = 0;
	$Itemid = JAVoiceHelpers::get_Itemid(array('option'=>'com_javoice', 'view'=>'items'));
	$url_login = JRoute::_("index.php?option=com_javoice&view=items&task=ja_login&tmpl=component");
	$base_url = JRoute::_("index.php?option=com_javoice&view=items&type=$jav_tab_active");
	$base_url = base64_encode($base_url);  	
?>
<script type="text/javascript" charset="utf-8">
//<![CDATA[
	var jav_tab_active = <?php echo $jav_tab_active?>;
	var jav_base_url = '<?php echo JURI::base()?>';
	var jav_base_url_login = '<?php echo $base_url;?>';
	var jav_ajax_url_login = '<?php echo $url_login;?>';	
	var jav_current_active_voice = 0;
	var link = 'index.php?option=com_javoice&view=items&layout=widget_change&limitstart=0&tmpl=component&forums=';
	link += '<?php echo $this->forums_text; ?>'
	link += '&type=';	
	jQuery(document).ready(function($){			
		jav_createTabs('#javtabs-main',link);
	});
//]]>
</script>
<?php }?>
<!-- Loading -->
			
<div id="jav-feedback">
	<?php if(!$this->types){?>
		<?php echo JText::_('NO_TYPE_AVAILBLE_PLEASE_CHECK_YOUR_WIDGET_SETTING_FIND_OUT_MORE_PULISHED_VOICE_TYPES')?>: <?php echo $this->types_title?>
	<?php }else{?>
					
		<div id="javtabs-main" class="javtabs-mainwrap clearfix">
			<ul class="javtabs-title" id='javtabs_ul'>
				<?php if($this->types){?>
					<?php foreach ($this->types as $k=>$type){?>				
						<li title="<?php echo $type->title;?>" class="jav-mainbox-<?php echo $type->id?> <?php if($k==0) echo 'first'; elseif($k==count($this->types)-1) echo 'last';?>" id="jav-typeid_<?php echo $type->id;?>">
							<a href="javascript:void(0)" class="jav-mainbox-<?php echo $type->id?>"><?php echo $type->title;?></a>
						</li>
					<?php }?>
				<?php }?>
			</ul>
			
			<?php if($this->types){?>
			<div class="javtabs_container">
				<?php foreach ($this->types as $k=>$type){ ?>
				<div class="javtabs-panel" id="jav-mainbox-<?php echo $type->id?>">					
					<div>												
						<div class="jav-search">
							<?php $link  = JURI::base() . 'index.php?option=com_javoice&amp;view=items&amp;type='.$type->id.'&amp;layout=widget_search&amp;tmpl=component&amp;Itemid='.$Itemid.'&amp;forums='.JRequest::getVar('forums_id')?>
			  				<form name="jav-search-form-<?php echo $type->id?>" action="index.php" method="get" onsubmit="if(	$('key-<?php echo $type->id?>').value!='<?php echo $type->search_description?>' && 	$('key-<?php echo $type->id?>').value.length>0){ jav_findWord(event,$('key-<?php echo $type->id?>'), '<?php echo $link?>', '<?php echo $type->id?>', 0); } else { $('key-<?php echo $type->id?>').addClass('input_error'); } return false;">
							
							  	<span class="jav-search-title"><?php echo $type->search_title?></span>
								<div class="jav-search-field" >
						  			
						  			<input type="text" style="width: 70%"  size="50" id="key-<?php echo $type->id?>" name="key-<?php echo $type->id?>" maxlength="100" class="inputbox"  onkeyup="jav_findWord(event, this, '<?php echo $link?>', '<?php echo $type->id?>'); return false;"  onfocus="if(this.value=='<?php echo addslashes($type->search_description)?>') this.value='';" onblur="if(this.value=='') this.value='<?php echo addslashes($type->search_description);?>';" value="<?php echo $type->search_description?>"/>
									<input type="submit" value="<?php echo $type->search_button?>" name="submit-<?php echo $type->id?>" class="button submit-search" />
									<img class="search-loading" style="display: none" src="<?php echo JURI::base();?>components/com_javoice/asset/images/loading-small.gif" alt="<?php echo JText::_('LOADING')?>"/>
						      	</div>
									    
			  				</form>
			  				
						</div>
		    		
			    		<div class="jav-search-result clearfix" style="display: none"></div>
	    				
	    				<div class="jav-list-items"  id="jav-list-items-<?php echo $type->id?>">
							<?php if($type_default->id==$type->id){?>
								<?php $this->setLayout('default')?>
			    				<?php echo $this->get_top_popular();?>
			    			<?php }?>
		    			</div>	    			
					</div>
				</div>
				<?php }?>
		  	</div>
		  	<?php }?>
		</div>
	
	<?php }?>
</div>