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
defined('_JEXEC') or die('Retricted Access');
global $javconfig;
$option = JRequest::getCmd('option');
?>
<script type="text/javascript">
	function show_box_licensekey(){
		$('license_key').disabled = false;
		$('submit_key').setStyle('display', '');
		if($("enable_proxy") != undefined){
			$('enable_proxy').disabled = false;
			$('proxy_address').disabled = false;
			$('proxy_port').disabled = false;
			$('proxy_user').disabled = false;
			$('proxy_pass').disabled = false;
		}
		//$('update_key').setStyle('display', 'none');
		return false;
	}
	function hide_box_licensekey(){
		$('license_key').disabled = true;
		$('submit_key').setStyle('display', 'none');
		//$('update_key').setStyle('display', 'none');
	}
	function submit_license_key(){		
		if($('license_key').value.trim()=='') {
			alert('<?php echo JText::_('PLEASE_ENTER_YOUR_LICENSE_KEY');?>');
			return false;
		}
		return confirm('<?php echo JText::_('ARE_YOU_SURE_YOU_WANT_TO_UPDATE_TO_THIS_NEW_LICENSE_KEY');?>');
	}
</script>

<form name="adminForm" id="adminForm" action="index.php" method="post" enctype="multipart/form-data">	
    <table width="100%">
		<tr>
			<td width="80%" valign="top">
				<div style="width:100%;">
					<?php echo $this->menu();?>
					<br/>
					
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div style="text-align: right;">
					<a onclick="hiddenNote('permission','[Help]','[Close]');return false;" href="index.php" id="jav_help"><?php echo JText::_("CLOSE");?></a>
				</div>
				<div id="jav-system-message">
	    			In order to be download updates and receive support, you need to be active JA Voice subscriber.
	    			<br/>
					Please login to your <a target="_blank" href="http://www.joomlart.com/member/member.php" title="member">Member Area</a> to view your subscription.<br/>						
					<br/>									    				
			    </div>	
			</td>
		</tr>
		<tr>
			<td width="100%">
				<center>
				<div style="width:100%;">
					<fieldset>
			<legend><?php echo JText::_('DOCUMENTATION_AMP_USERGUIDES');?></legend>	    	
				<table width="100%">
					<tr>
						<td width="80%" valign="top">
							Detailed documentation and userguides are available on our wiki site.
							<ul>
								<li>Wiki &amp; Documentation (<a target="_blank" href="http://wiki.joomlart.com/wiki/JA_Voice/Overview" title="Click here to go to Wiki &amp; Documentation">JA Voice Wiki Section</a>)</li>					
						</td>
					</tr>
				</table>					    																	
		</fieldset>    
	</div>	
	<div style="width:100%;">
		<fieldset>
			<legend><?php echo JText::_('SUPPORT');?></legend>	    	
				<table width="100%">
					<tr>
						<td width="80%" valign="top">
							Customer support is our top priority, with a valid active subscription, you can always get help via one of follow options:
							<p></p>
							<ul>
								<li>JA Voice <a target="_blank" href="http://www.joomlart.com/forums/forumdisplay.php?162-JA-Voice" title="Click here to go to JA Voice Forum">Forum</a></li>
								<li>Premium <a target="_blank" href="http://support.joomlart.com/index.php" title="Click here to go to Ticket Support">ticket Support</a></li>		
				
							</ul><p></p>						
							We will try our best to get back to you within 24 hours (9:00AM - 5:00PM, Monday - Friday GMT +8)						
						</td>
					</tr>
				</table>					    																	
		</fieldset> 			
				</div>					
				<!--<fieldset>			
					<legend><?php echo JText::_('LICENSE_INFORMATION');?></legend>
					<table align="center" class="admintable" width="50%">						
						<tr>
							<td class="key" align="left"><?php echo JText::_('LICENSE_FOR_DOMAINS')?>:</td>
							<td align="left"><?php echo $_SERVER ['HTTP_HOST']; ?></td>
						</tr>
						<?php if(isset($javconfig['license']) && $javconfig['license']->get('type', '')){?>
						<tr>
							<td class="key" align="left"><?php echo JText::_('LICENSE_TYPE')?>:</td>
							<td align="left"><?php echo (isset($javconfig['license']) && $javconfig['license']->get('type'))?JAVoiceHelpers::get_license_type():JText::_('NA');?></td>
						</tr>
						<?php }?>
						<tr>
        					<td class="key hasTip" align="left" title="<?php echo JText::_("EMAIL_OR_USERNAME" );?>::<?php echo JText::_("EMAIL_OR_USERNAME_DESC" );?>">
							<?php echo JText::_("EMAIL" );?>:</td>
					        <td align="left">					        	
					        	<?php if(isset($javconfig['license'])){
					        		echo $javconfig['license']->get('email');					        	 
					        	}?>					        						        
					        </td>
					    </tr>
						<tr>
					        <td class="key hasTip" align="left" title="<?php echo JText::_("PAYMENT_ID" );?>::<?php echo JText::_("PAYMENT_ID_DESC" );?>">
							<?php echo JText::_("PAYMENT_ID" );?>:</td>
					        <td align="left">
					        	<?php if(isset($javconfig['license'])){
					        		echo $javconfig['license']->get('payment_id'); 
					        	}?>					        						        
					        </td>
					    </tr>
					    <tr>
					    	<td class="key hasTip" align="left" title="<?php echo JText::_("PAYMENT_ID" );?>::<?php echo JText::_("PAYMENT_ID_DESC" );?>">
					        <td align="left">
					        	<input type="button" value="<?php echo JText::_('CHANGE')?>" onclick="window.location.href='index.php?option=com_javoice&amp;view=voice&amp;layout=verify'; return false;" title="">
					        </td>
					    </tr>												
					</table>					 
				</fieldset> 
				--></center>
			</td>
		</tr>
	</table>
<input type="hidden" name="option" value="<?php echo $option; ?>" />	
<input type="hidden" name="view" value="voice" />	
<input type="hidden" name="task" value="reissue" />	
</form>