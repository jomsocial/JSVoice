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
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$option = JRequest::getCmd('option');
?>

<script type="text/javascript">
	//<![CDATA[
	function submitbutton(pressbutton) {
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
		if (trim( document.adminForm.name.value ) == "") {
			alert( '<?php echo JText::_('TAG_CANNOT_BE_EMPTY', true);?>' );
		} else {
			submitform( pressbutton );
		}
	}
	//]]>
</script>

<form action="index.php" enctype="multipart/form-data" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
	
		<legend><?php echo JText::_('DETAILS');?></legend>
		<table class="admintable">
			<tr>
				<td class="key"><?php echo JText::_('NAME'); ?></td>
				<td><input class="text_area" type="text" name="name" id="name" value="<?php echo $this->row->name; ?>" size="50" maxlength="250" /></td>
			</tr>
			<tr>
				<td class="key"><?php	echo JText::_('PUBLISHED');	?></td>
				<td><div class="ja_tags"><?php echo $this->lists['published']; ?></div></td>
			</tr>
		</table>
		
		<input type="hidden" name="id" value="<?php echo $this->row->id;?>" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="view" value="tags" />
		<input type="hidden" id="task" name="task" value="<?php echo JRequest::getVar('task'); ?>" />
		<?php echo JHTML::_('form.token'); ?>
	</fieldset>
</form>
