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
  JHTML::_('behavior.tooltip');
?>

<form name="adminForm" id="adminForm" action="index.php" method="post">

    <div style="width:100%;">
    	<fieldset class="adminform">
			<div class="submenu-box">
				<div class="submenu-pad">
					<ul class="configuration" id="submenu">
						<li><a class="active" href="index.php?option=com_javoice&view=customtmpl"><?php echo JText::_('LAYOUT_TEMPLATES')?></a></li>
						<li><a href="index.php?option=com_javoice&view=customcss"><?php echo JText::_('CSS')?></a></li>
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
            					
            					<td width="10%"   style="background-color:#F0F0F0;border: 0px;">
            						<?php echo JText::_('FILE_NAME'); ?>
            					</td>
            					<td style="background-color:#F0F0F0;border: 0px;" align="center">
            						<?php echo JText::_('DESCRIPTION'); ?>
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
        	<?php
        	$layouts = $this->layouts;        	
        	$i = 0;
			if (count($layouts)) {
				foreach ($layouts as $view){
        		?>
					<tr class="row<?php echo $i%2; $i++; ?>">
	            		<td align="center">
	            			<span class="editlinktip hasTip" title="<?php echo JText::_("DESCRIPTION" );?>::<?php echo JText::_($view['folder'][1]); ?>">
								<strong><?php echo $view['folder'][0]; ?></strong>
							</span>            			
	            		</td>    
	            		<td align="left">
	            			<table class="adminlist">
								<?php for($k = 0; $k < count($view['nodes']); $k++){ ?>
										<tr>
				            				<td width="5%">
				            					<?php echo $k+1?>
				            				</td> 
				            				<td width="25%" >
			            						<?php echo $view['nodes'][$k]['filename']; ?>
			            					</td>
					            			<td align="left">
					            				<?php echo $view['nodes'][$k]['description']; ?>			            				
					            			</td>
					            			<td width="15%" align="center">
					            				<a onclick="return confirm('<?php echo JText::_('IMPROPER_EDITING_OF_SOURCE_FILES_CAN_RESULT_IN_ERROR_FILE_BACKUP_BEFORE_EDIT_IS_ADVISED')?>');" href="<?php echo JRoute::_( 'index.php?option=com_javoice&view=customtmpl&task=edit&folder='.$view['folder'][2].'&file='.$view['nodes'][$k]['filename'] ); ?>">
	            									<?php echo JText::_('EDIT')?>
	            								</a>
					            			</td>
					            		</tr>
								<?php }?>
							</table>
	            		</td>        		          		            	
	            	</tr>
        	<?php
				}
			}
        	?>
        	<tbody>        	
		</table>
	</div>

	
	<input type="hidden" name="option" value="com_javoice" />
	<input type="hidden" name="view" value="customtmpl" id="view"/>
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>	
 </form>
