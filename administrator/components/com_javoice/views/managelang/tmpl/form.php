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
		defined('_JEXEC') or die('Restricted access'); 
		$option = JRequest::getCmd('option');
	  	JRequest::setVar( 'hidemainmenu', 1 );		
				        
		if (!is_writable($this->root)) {
			echo '<span style="color:red; font-size:14px"><b>'. JText::_('FILE_IS_UNWRITABLE').'</b></span><br/><br/>';
		}
		
		?>
		<div style="position:relative; width:100%; float:left ;">		
			
			<form action="index.php" method="POST" id="adminForm" name="adminForm" style=" width:100%;">

				<div><b><?php echo JText::_('EDIT_LANGUAGE_FILE'), ' "', $this->filename;?>"</b></div>
				<textarea wrap="off" onscroll="scrollEditor(this);" spellcheck="false" onkeydown="return catchTab(this,event)" class="inputbox jav-editor-code" id="datalang" name="datalang" rows="25" cols="110">
					<?php echo $this->data; ?>
				</textarea>			
				
				<input type="hidden" name="path_lang"  value="<?php echo $this->path_lang;?>" />
				<input type="hidden" name="task"  value="" />
				<input type="hidden" name="filename"  value="<?php echo $this->lang;?>" />
				<input type="hidden" name="client"  value="<?php echo $this->client->id;?>" />
				<input type="hidden" name="option" value="<?php echo $option;?>" />
				<input type="hidden" name="view" value="managelang" />
			</form>				
		</div>		