<?php // no direct access
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
defined('_JEXEC') or die('Restricted access');
global $permission,$permissionText;
$user = $this->user;
$params = $this->params;
?> 
 <script type="text/javascript">
 function checkData() {
		return true;
	}
	 
 </script>
 <form name="adminForm" id="adminForm" action="index.php" method="post"> 

 	<input type="hidden" name="option" value="com_javoice" /> 

 	<input type="hidden" name="view" value="permissions" /> 

 	<input type="hidden" name="task" value="saveIFrame" /> 
	
	<input type="hidden" name="tmpl" value="component" /> 

 	<input type="hidden" name='id' id='id' value="<?php echo $user->id?>"> 

	<input type="hidden" name='cid[]' id='cid[]' value="<?php echo $user->id?>">
	
	<ul>
		<li>
			<div><h2><?php echo JText::_("LIST_PERMISSION" );?></h2></div>
			<?php 
				$count = count($permission['admin']);
				if($count>0){
					foreach ($permission['admin'] as $key=>$value){
						$checked='';
						if(!$params->get('permissions',0)){
							if($this->isadmin==1)$checked='checked';
						}else {
							if((intval($params->get('permissions',0))& intval($value))>0)$checked='checked';
						}
						?>
						<div class='jav-permissions-list'>
							<input type="checkbox" <?php echo $checked;?> name='permissions[]' id='<?php echo $key;?>' value="<?php echo $value?>">	
							<?php echo $permissionText['admin'][$key]?>						
						</div>
						<?php			
					}
				}
			?>
		</li>
	</ul> 	
 </form>