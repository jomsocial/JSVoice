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
?>

<form name="adminForm" id="adminForm" action="index.php" method="post">
    <div style="width:100%;">
    	<fieldset class="adminform">
			<div class="submenu-box">
				<div class="submenu-pad">
					<ul class="configuration" id="submenu">
						<li><a href="index.php?option=com_javoice&view=customtmpl"><?php echo JText::_('LAYOUT_TEMPLATES')?></a></li>
						<li><a class="active"  href="index.php?option=com_javoice&view=customcss"><?php echo JText::_('CSS')?></a></li>
					</ul>
					<div class="clr"/>
				</div>
			</div>			
		</fieldset>
		
		<table class="adminlist">
        	<thead>
            	<tr>
            		<th width="20%" class="title"><?php echo JText::_('FOLDER' ); ?></th>
            		<th >
            			<table width="100%">
            				<tr>
            					<td  width="5%" style="background-color:#F0F0F0;border: 0px;">
            						<?php echo JText::_('')?>
            					</td>
            					
            					<td  style="background-color:#F0F0F0;border: 0px;">
            						<?php echo JText::_('FILE_NAME'); ?>
            					</td>            					
            					<td width="15%" style="background-color:#F0F0F0;border: 0px;" align="center">
            						<?php echo JText::_('ACTION'); ?>
            					</td>
            				</tr>
            			</table>
            		</th>       
            	</tr>
			</thead>
			<tbody>
			<tr class="row0">
           		<td align="center">
           			<span class="editlinktip" title="CSS">
						<strong>CSS</strong>
					</span>            			
           		</td> 
				<td align="left">
           			<table class="adminlist">
				
			        	<?php
			        	$files = $this->files;
			        	
			        	for($i = 0; $i < count($files); $i++){
			        		$folder = $files[$i];		
			        		?>
			        		<tr class="row<?php echo $i%2; ?>">
			            		<td align="center" width="5%"><?php echo $i+1;  ?></td>    
			            		<td align="left">
			    					<?php echo $files[$i]; ?>
			            		</td>      
			            		<td width="15%" align="center">
		            				<a href="<?php echo JRoute::_( 'index.php?option='. $this->lists['option'] .'&view=customcss&task=edit&file='.$files[$i] ); ?>">
        								<?php echo JText::_('EDIT')?>
        							</a>
		            			</td>      		          		            	
			            	</tr>
			        	<?php
			        	
						}
			        	?>
			        </table>
				 </td>
			</tr>    
        	<tbody>        	
		</table>
	</div>
	
	
	<input type="hidden" name="option" value="com_javoice" />
	<input type="hidden" name="view" value="customcss" id="view"/>
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>	
 </form>
