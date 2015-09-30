<?php // no direct access
/*------------------------------------------------------------------------
# $JA#PRODUCT_NAME$ - Version $JA#VERSION$ - Licence Owner $JA#OWNER$
# ------------------------------------------------------------------------
# Copyright (C) 2004-2008 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
# @license - Copyrighted Commercial Software
# Author: J.O.O.M Solutions Co., Ltd
# Websites:  http://www.joomlart.com -  http://www.joomlancers.com
# This file may not be redistributed in whole or significant part.
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
$item=$this->item;
//$editor =& JFactory::getEditor();
$mess=$this->mess;
?> 
 <form name="adminForm" id="adminForm" action="index.php" method="post"> 

 	<input type="hidden" name="option" value="com_javoice" /> 

 	<input type="hidden" name="view" value="items" /> 

 	<input type="hidden" name="task" value="saveResponese" /> 
 	
 	<input type="hidden" name="tmpl" value="component" /> 

 	<input type="hidden" name='id' id='id' value="<?php echo $item->id?>"> 
 	
 	<input type="hidden" name='cid[]' id='cid[]' value="<?php echo $item->id?>">
 	
	<input type="hidden" name="user_id" value="<?php echo $item->user_id;?>">
	
	<input type="hidden" name="number" value="<?php echo $this->number;?>">
	<ul>
		<li>
			<label class="desc" for="title"><?php echo JText::_("VOICE_TITLE" );?></label>
			<div><?php echo $item->item_title;?></div>
		</li>
		<li>
			<label class="desc" for="title"><?php echo JText::_("RESPONESE_BY" );?></label>
			<div><?php echo $item->responsename;?></div>
		</li>							
		<li>
			<label class="desc" for="title"><?php echo JText::_("RESPONESE" );?></label>
			<div><textarea rows="5" cols="50" onchange="checkdataString(this,'error')"  class="text" name="responese" id="responese"><?php echo $item->content?></textarea></div>
		</li>	
	</ul>	
 </form>