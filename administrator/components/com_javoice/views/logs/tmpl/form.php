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
$item=$this->item;
?> 

 <form name="adminForm" id="adminForm" action="index.php" method="post"> 

 	<input type="hidden" name="option" value="com_javoice" /> 

 	<input type="hidden" name="view" value="logs" /> 

 	<input type="hidden" name="task" value="" /> 

 	<input type="hidden" name='id' id='id' value="<?php echo $item->id?>"> 

 	<table class="admintable"> 

 		<tr> 

 			<td class="key">
				<?php echo JText::_("ID" );?>
			</td> 

 			<td>
				<input type="text" name="id" value="<?php echo $item->id?>">
			</td> 

 		</tr> 

 		<tr> 

 			<td class="key">
				<?php echo JText::_("USER_ID" );?>
			</td> 

 			<td>
				<input type="text" name="user_id" value="<?php echo $item->user_id?>">
			</td> 

 		</tr> 

 		<tr> 

 			<td class="key">
				<?php echo JText::_("ITEM_ID" );?>
			</td> 

 			<td>
				<input type="text" name="item_id" value="<?php echo $item->item_id?>">
			</td> 

 		</tr> 

 		<tr> 

 			<td class="key">
				<?php echo JText::_("TIME_EXPIRED" );?>
			</td> 

 			<td>
				<input type="text" name="time_expired" value="<?php echo $item->time_expired?>">
			</td> 

 		</tr> 

 		<tr> 

 			<td class="key">
				<?php echo JText::_("REMOTE_ADDR" );?>
			</td> 

 			<td>
				<input type="text" name="remote_addr" value="<?php echo $item->remote_addr?>">
			</td> 

 		</tr> 

 	</table> 

 </form>