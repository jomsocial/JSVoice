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

 defined('_JEXEC') or die( 'Restricted access' ); 
$mainframe = JFactory::getApplication();
$this->getTabs();
?>
<script type="text/javascript">
	function highlight(){
		document.getElementById('script').select();
	}	
</script>
<form action="index.php" method="post" >
	<br/>
	<div style="width: 90%; border: 1px solid rgb(153, 153, 153); padding: 4px; text-align: left; background: none repeat scroll 0% 0% rgb(255, 255, 221);">
 		<?php echo JText::_("WIDGET_SETUP_TOOLTIP");?>
 		<br/>
 		<?php echo JText::_("COPY_AND_PASTE_THE_FOLLOWING_CODE_INTO_YOUR_HTML_JUST_BEFORE_THE_CLOSING_BODY_TAG")?>
 	</div>
	<br/>	
	
	<input type="button" class="button" style="text-align: left;" onclick="javascript: highlight();return false" value="<?php echo JText::_("SELECT_ALL");?>">
	<br>
	<textarea wrap="off" onscroll="scrollEditor(this);" style="margin-bottom: 10px;width:100%;" id="script" name="script" rows="15" readonly="readonly"><?php echo $this->script;?></textarea>
	<table class="adminlist">
		<thead>
			<tr>
				<th>
					<?php echo JText::_("FIELD")?>
				</th>
				<th>
					<?php echo JText::_("DATA_TYPE")?>
				</th>
				<th>
					<?php echo JText::_("REQUIRED")?>
				</th>
				<th>
					<?php echo JText::_("DEFAULT")?>
				</th>
				<th>
					<?php echo JText::_("OPTIONS")?>
				</th>					
				<th>
					<?php echo JText::_("NOTES")?>
				</th>											
			</tr>
		</thead>
		  <tbody>
		    
		      <tr class="">
		        <th><code>url</code></th>
		        <td align="center"><?php echo JText::_('STRING')?></td>
		        <td align="center"><img border="0" alt="Published" src="components/com_javoice/asset/images/publish_x.png"/></td>
		        <td><?php echo JURI::root(); ?></td>
		        <td></td>
		        <td>
		          <?php echo JText::_("URL_NOTE")?>
		          
		        </td>
		      </tr>
		    
		      <tr class="">
		        <th><code>forums</code></th>
		        <td align="center"><?php echo JText::_('STRING')?></td>
		         <td align="center"><img border="0" alt="Published" src="components/com_javoice/asset/images/publish_x.png"/></td>
		        <td><b><?php echo JText::_('ALL_FORUMS'). '</b> [<code>'.$this->strforum.'</code>]';?></td>
		        <td/>
		        <td>
		        	<?php echo JText::_("FORUMS_NOTE")?>
		        </td>
		      </tr>
		    
		      <tr class="">
		        <th><code>voicetypes</code></th>
		       <td align="center"><?php echo JText::_('STRING')?></td>
		        <td align="center"><img border="0" alt="Published" src="components/com_javoice/asset/images/publish_x.png"/></td>
		        <td><b><?php echo JText::_('ALL_VOICE_TYPES'). '</b> [<code>'.$this->strvoicetypes.'</code>]';?></td>
		        <td/>
		        <td>
		         <?php echo JText::_("VOICES_TYPE_NOTE")?>		          
		        </td>
		      </tr>
		      
		      <tr class="">
		        <th><code>number_voices</code></th>
		        <td align="center"><?php echo JText::_('INTEGER')?></td>
		         <td align="center"><img border="0" alt="Published" src="components/com_javoice/asset/images/publish_x.png"/></td>
		        <td><?php echo 5?></td>
		        <td></td>
		        <td>
		         <?php echo JText::_("NUMBER_VOICES_NOTE")?>		          
		        </td>
		      </tr>
		      
		      <tr class="">
		        <th><code>alignment</code></th>
		        <td align="center"><?php echo JText::_('STRING')?></td>
		         <td align="center"><img border="0" alt="Published" src="components/com_javoice/asset/images/publish_x.png"/></td>
		        <td>left</td>
		        <td>left, right</td>
		        <td>
		          
		          
		        </td>
		      </tr>
		    
		      <tr class="">
		        <th><code>mode_display</code></th>
		        <td align="center"><?php echo JText::_('STRING')?></td>
		         <td align="center"><img border="0" alt="Published" src="components/com_javoice/asset/images/publish_x.png"/></td>
		        <td>image</td>
		        <td>image, text</td>
		        <td>
		          
		          
		        </td>
		      </tr>
		    
		      <tr class="">
		        <th><code>width</code></th>
		        <td align="center"><?php echo JText::_('INTEGER')?></td>
		        <td align="center"><img border="0" alt="Published" src="components/com_javoice/asset/images/tick.png"/></td>
		        <td>
		        	520
		        <td/>
		        <td>
		        </td>
		      </tr>
		      
		      <tr class="">
		        <th><code>height</code></th>
		        <td align="center"><?php echo JText::_('INTEGER')?></td>
		          <td align="center"><img border="0" alt="Published" src="components/com_javoice/asset/images/tick.png"/></td>
		        <td>
		        	600
		        <td/>
		        <td>
		        </td>
		      </tr>
		      <tr class="">
		        <th><code>feedback_title</code></th>
		        <td align="center"><?php echo JText::_('STRING')?></td>
		         <td align="center"><img border="0" alt="Published" src="components/com_javoice/asset/images/publish_x.png"/></td>
		        <td>
		        	<?php echo JText::_('FEEDBACK')?>
		        <td/>
		        <td>
		        </td>
		      </tr>		    
		  </tbody>
	</table> 
</form>
 <?php echo JHTML::_( 'form.token' ); ?> 