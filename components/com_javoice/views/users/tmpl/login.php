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
?>
<div id="jav-frm_login" class="clearfix">
	<ul>
		<li class='ja-haftleft'>
			<h1 style="background:none !important; font-size:1.5em;font-weight:normal;margin:7px 0 0 5px; color: #333333 !important; font-family: 'lucida grande',Verdana,sans-serif; text-transform: none">
				<?php echo JText::_('LOGIN_AS_ACCOUNT_JOOMLA')?>
			</h1>
		    <div id="jav-login-joomla-form" style="overflow:hidden;height: 300px; margin-left: 7px" >
		    	<?php echo $this->loadTemplate('form')?>
		    </div>		
		</li>
		<li class='ja-haftright'>
			<?php if(JPluginHelper::isEnabled('system', 'plg_jarpxnow') ){ ?>
			<div id="jav-login-rpx">
			    <iframe src="https://<?php echo $this->application;?>/openid/embed?token_url=<?php echo $this->token_url;?>" scrolling="no" frameBorder="no" style="width:350px;height:240px;"></iframe>
			</div>   
			<?php } ?>		
		</li>		
	</ul>
</div>
<?php exit();?>

