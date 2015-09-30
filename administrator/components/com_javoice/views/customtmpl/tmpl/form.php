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
  $filename = JPATH_SITE.'/components/com_javoice/tmpl/'.$this->file;
  $filename = JPath::clean($filename);
?>
<form name="adminForm" id="adminForm" action="index.php" method="post">
    <table width="100%">
		<tr>
	
		<td width="100%" valign="top">
			<div style="width:100%;">
				<fieldset>
				<legend><?php echo JText::_('EDIT_FILE'). ': '. $filename?> </legend>
    
				<table class="admintable" width="100%;">		        			        	
		        	<tr>		        		
						<td align="center" style="padding: 10px; width: 100%;">
							<textarea wrap="off" onscroll="scrollEditor(this);" spellcheck="false" onkeydown="return catchTab(this,event)" class="inputbox jav-editor-code" id="content" name="content" rows="25" cols="110"><?php echo $this->content; ?></textarea>
						</td>
		        	</tr> 
		        	
		    	</table>
		     </fieldset>    
			</div>
		</td>
	</tr>
	</table>
	
	<input type="hidden" name="file" value="<?php echo $this->file; ?>" />
	<input type="hidden" name="folder" value="<?php echo $this->folder; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="view" value="customtmpl" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>	
 </form>