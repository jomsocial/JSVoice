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
			<td width="100%">
				<center>				
				<fieldset>			
					<legend><?php echo JText::_('LICENSE_INFORMATION');?></legend>
					<table align="center" class="admintable" width="50%">
						<tr>
							<td class="key" align="left"><?php echo JText::_('LICENSE_TYPE')?>:</td>
							<td align="left"><?php echo (isset($javconfig['license']) && $javconfig['license']->get('type'))?JAVoiceHelpers::get_license_type():JText::_('NA');?></td>
						</tr>
						<tr>
							<td class="key" align="left"><?php echo JText::_('LICENSE_FOR_DOMAINS')?>:</td>
							<td align="left"><?php echo (isset($javconfig['license']) && $javconfig['license']->get('domains'))?$javconfig['license']->get('domains'):JText::_('NA');?></td>
						</tr>
						<tr>
							<td class="key" align="left">
								<?php if(isset($javconfig['license']) && ($javconfig['license']->get('expired_date')<time() || $javconfig['license']->get('d13c3f7baed576768b11a714ef4d90e2')=='Waiting verify')){?>
									<?php echo JText::_('STATUS');?>
								<?php }else{?>
									<?php echo JText::_('VALID')?>:
								<?php }?>
							</td>
							<td align="left">
								<?php 								
								if(!isset($javconfig['license']) || $javconfig['license']->get('expired_date')==''){
									echo JText::_('NA');
								}								
								elseif($javconfig['license']->get('d13c3f7baed576768b11a714ef4d90e2')=='Waiting verify'){
									echo JText::_('VERIFICATION_PENDING');						
								}
								elseif(date('Y', $javconfig['license']->get('expired_date')) - date('Y')>0){
									if(  !$javconfig['license']->get('expired_date') || ( date('Y', $javconfig['license']->get('expired_date')) - date('Y')  > 10)  )	echo JText::_('LIFETIME');
									else echo date('Y-m-d', $javconfig['license']->get('expired_date'));
								}
								else{?>
									<?php if($javconfig['license']->get('expired_date')>time()){?>
										<?php echo date('Y-m-d', $javconfig['license']->get('expired_date'));?>
										(<?php echo (floor(($javconfig['license']->get('expired_date')-time())/(60*60*24))+1). ' ' . JText::_('DAYS');?>)
									<?php }else{?>
										<?php JError::raiseWarning(1, JText::_('YOUR_LICENSE_HAS_EXPIRED_THEREFORE_THE_SYSTEM_WILL_BE_DISABLED_PLEASE_CONTACT_A_HREFHTTPSUPPORTJOOMLARTCOMJOOMLARTA_FOR_FURTHER_ASSISTANCE'));?>
										<span class="" style="color: red; font-weight: bold;"><?php echo JText::_('EXPIRED');?>  </span>
									<?php }?>
										
								<?php }?>
								
							</td>
							
						</tr>
						
					</table>
					<br/>
					<table align="center">
						<tr>
							<td class="key" align="left">
								<?php if(isset($javconfig['license']) && strtolower($javconfig['license']->get('type'))=='trial'){?>
									<strong><?php echo JText::_('TRIAL_LICENSE_KEY')?>:</strong>
								<?php }else{?>
									<strong><?php echo JText::_('LICENSE_KEY')?>:</strong>
								<?php }?>
								 [<a href="#" onclick="show_box_licensekey();"><?php echo JText::_('UPDATE_LICENSE_KEY')?></a>]
							</td>
						</tr>
						<tr>
							<td align="left">								
								<textarea rows="5" id="license_key" name="license_key" cols="80" <?php if($this->key!=''){?>disabled="disabled"<?php }?>><?php echo $this->key;?></textarea>
							</td>
						</tr>
						<?php if (function_exists ( 'curl_version' )) {?>
						<tr>
							<td align="left">								
								<input type="checkbox" <?php if($this->key!=''){?>disabled="disabled"<?php }?> onchange="if(this.checked){ $('proxy-box').setStyle('display', 'block')} else{ $('proxy-box').setStyle('display', 'none')}" value="1" name="enable_proxy" id="enable_proxy" <?php if(isset($javconfig['license']) && $javconfig['license']->get('enable_proxy', 0)) echo 'checked'?>/>
								<label for="enable_proxy"><?php echo JText::_('USE_A_PROXY_SERVER_FOR_YOUR_LAN')?></label>
								<br/>
								
								<table id="proxy-box" style="padding-left: 25px; display: <?php if(isset($javconfig['license']) && $javconfig['license']->get('enable_proxy', 0)) echo 'block'; else echo 'none';?>">
									<tr>
										<td><label for="proxy_type"><?php echo JText::_('PROXY_TYPE')?>:</label></td>
										<td>
											<?php $arrayProxy = array("CURLPROXY_HTTP" => "http", "CURLPROXY_SOCKS4" => "socks4", "CURLPROXY_SOCKS5" => "socks5");?>
											<select id="proxy_type" name="proxy_type">
												<?php foreach ($arrayProxy as $key=>$val):?>
												<option value="<?php echo $key;?>"><?php echo $val;?></option>	
												<?php endforeach;?>																								
											</select>
										</td>
									</tr>
									<tr>
										<td><label for="proxy_address"><?php echo JText::_('ADDRESS')?>:</label></td>
										<td><input type="text" <?php if($this->key!=''){?>disabled="disabled"<?php }?> value="<?php if(isset($javconfig['license'])) echo $javconfig['license']->get('proxy_address', '')?>" size="30" name="proxy_address" id="proxy_address"/> </td>
									</tr>
									<tr>
										<td><label for="proxy_port"><?php echo JText::_('PORT')?>:</label></td>
										<td><input type="text" <?php if($this->key!=''){?>disabled="disabled"<?php }?> value="<?php if(isset($javconfig['license'])) echo $javconfig['license']->get('proxy_port', '')?>" size="5" name="proxy_port" id="proxy_port"/></td>
									</tr>
									<tr>
										<td><label for="proxy_user"><?php echo JText::_('USER')?>:</label></td>
										<td><input type="text" <?php if($this->key!=''){?>disabled="disabled"<?php }?> value="<?php if(isset($javconfig['license'])) echo $javconfig['license']->get('proxy_user', '')?>" size="30" name="proxy_user" id="proxy_user"/></td>
									</tr>
									<tr>
										<td><label for="proxy_pass"><?php echo JText::_('PASSWORD')?>:</label></td>
										<td><input type="text" <?php if($this->key!=''){?>disabled="disabled"<?php }?> value="<?php if(isset($javconfig['license'])) echo $javconfig['license']->get('proxy_pass', '')?>" size="30" name="proxy_pass" id="proxy_pass"/></td>
									</tr>
								</table>								 							
							</td>
						</tr>
						<?php }?>
						<tr id="submit_key" <?php if($this->key!=''){?>style="display:none" <?php }?>>
							<td class="key" align="right">
								<strong><input type="submit"  value="<?php echo JText::_('SAVE')?>" onclick="return submit_license_key();"/> </strong>
								<strong><input type="reset"  value="<?php echo JText::_('CANCEL')?>" onclick="return hide_box_licensekey();"/> </strong>
							</td>
						</tr>
					</table>
					 
				</fieldset> 
				</center>
			</td>
		</tr>
	</table>
<input type="hidden" name="option" value="<?php echo $option; ?>" />	
<input type="hidden" name="view" value="voice" />	
<input type="hidden" name="task" value="reissue" />	

</form>