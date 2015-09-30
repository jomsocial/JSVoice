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
$mainframe = JFactory::getApplication();
JHTML::_('behavior.tooltip');
jimport('joomla.html.pane');
$application= $this->application;
$count= count( $application );
$task=JRequest::getVar('task',NULL);

?>
<style type="text/css">
	table.admintable td {
		font-size:10px !important;
	}
	table.admintable td.key{
		font-weight:normal !important;
		text-align:left !important;
		font-size:1.091em !important;
	}
	table.admintable td.master{
		font-weight:bold !important;
	}
</style>
<form action="index.php" method="post" name="adminForm"  id="adminForm">
<div class="col100">
	<fieldset class="adminform">
		<?php echo $this->getTabs();?>
	</fieldset>	
	<br/>
	<div style="text-align: right;">
		<a id='jav_help' href="index.php"onclick="hiddenNote('integrate','<?php echo JText::_('HELP')?>','<?php echo JText::_('CLOSE')?>');return false;"><?php echo JText::_('HELP')?></a>
	</div>
	<?php 
		$note = JText::_("COMMENT_SETTINGS_TOOLTIP" );
		JAVoiceHelpers::displayNote($note,'integrate');
	?>		
		<table class="adminlist" width="100%">
			<thead>		
				<tr>
	 				<th width="5%">
						#
					</th> 
	
	 				<th width="5%">
						
					</th> 
					
	 				<th class="" width="20%">
						<?php echo JText::_('NAME' ); ?>
					</th> 	
	 				<th class="" width="20%">
						<?php echo JText::_('ACTION' ); ?>
					</th> 	
 					<th class="" width="40%">
						<?php echo JText::_('DESCRIPTIONS' ); ?>
					</th> 											
	 				<th class="" width="10%">
						<?php echo JText::_('DEFAULT' ); ?>
					</th>
				</tr> 														
			</thead>	
			<tbody>
				<?php
					$i=0;
				foreach ($application as $key=>$value){
					if($key=='jacomment'){?>
						<tr>
							<td align="center">
								<?php echo $i+1;?>
							</td>
							<td align="center">
								<input <?php if(!$this->com_jacomment) echo "disabled= 'disabled'; " ?>type="radio" <?php if($this->params->get('run_system')==$key)echo "checked";?> id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $key;?>" onclick="Joomla.isChecked(this.checked);" />
							</td>
							<td>
								
								<?php if(!$this->com_jacomment){?>	
									<a target="_blank"  href="http://jacomment.joomlart.com/">
										<b><?php echo $value;?></b>
									</a>
									<span class="editlinktip hasTip" title="<?php echo JText::_('WARNING' );?>::<?php echo JText::_('JA_COMMENT_COMPONENT_NOT_INSTALLED_TO_USE_THE_JA_COMMENT_COMPONENT_YOU_MUST_INSTALL_IT_FIRST'); ?>">
										<img border="0" alt="" src="components/com_javoice/asset/images/warning.png"/>
									</span>												
								<?php }else{?>
									<a target="_blank"  href="index.php?option=com_jacomment">
										<?php echo $value;?>
									</a>
								<?php }?>
							</td>
							<td align="center">
								<a  href="index.php" onclick="jaCreatForm('edit&group=integrate' ,'<?php echo $key?>',600,450,0,'<?php echo $i?>','<?php echo $value;?>');return false;">
									<?php echo  JText::_('SETTING')?>
								</a>
							</td>	
							<td>
								<span>
									<?php echo JText::_('BJA_COMMENTB_IS_A_JOOMLA_COMPONENT_FOR_JOOMLA_CONTENTS_VISIT_A_HREFHTTPJACOMMENTJOOMLARTCOM_TARGET_BLANKHTTPJACOMMENTJOOMLARTCOMA_AND_A_HREFHTTPDEMOJACOMMENTJOOMLARTCOM_TARGET_BLANKHTTPDEMOJACOMMENTJOOMLARTCOMA_FOR_MORE_INFORMATION')?>									
								</span>						
							</td>									
							<td align="center">
								<?php if($this->params->get('run_system')==$key) {?>
								<img src="components/com_javoice/asset/images/icon-16-default.png" alt="<?php echo JText::_('DEFAULT' ); ?>" />
								<?php }?>
							</td>
						</tr>
					<?php }
					elseif($key=='jcomments'){?>
						<tr>
							<td align="center">
								<?php echo $i+1;?>
							</td>
							<td align="center">
								<input <?php if(!$this->com_jcomments) echo "disabled= 'disabled'; " ?>type="radio" <?php if($this->params->get('run_system')==$key)echo "checked";?> id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $key;?>" onclick="Joomla.isChecked(this.checked);" />
							</td>
							<td>
								
								<?php if(!$this->com_jcomments){?>	
									<a target="_blank"  href="http://www.joomlatune.com/jcomments.html">
										<?php echo $value;?>
									</a>
									<span class="editlinktip hasTip" title="<?php echo JText::_('WARNING' );?>::<?php echo JText::_('JCOMMENT_COMPONENT_NOT_INSTALLED_TO_USE_THE_JCOMMENT_COMPONENT_YOU_MUST_INSTALL_IT_FIRST'); ?>">
										<img border="0" alt="" src="components/com_javoice/asset/images/warning.png"/>
									</span>												
								<?php }else{?>
									<a target="_blank"  href="index.php?option=com_jcomments">
										<?php echo $value;?>
									</a>
								<?php }?>
							</td>
							<td align="center">
								<a  href="index.php" onclick="jaCreatForm('edit&group=integrate' ,'<?php echo $key?>',600,450,0,'<?php echo $i?>','<?php echo $value;?>');return false;">
									<?php echo  JText::_('SETTING')?>
								</a>
							</td>	
							<td>
								<span>
									<?php echo JText::_('JCOMMENTS_IS_A_JOOMLA_COMPONENT_FOR_JOOMLA_CONTENTS_VISIT_HTTPWWWJOOMLATUNECOMJCOMMENTSHTML_FOR_MORE_INFORMATION')?>
								</span>						
							</td>									
							<td align="center">
								<?php if($this->params->get('run_system')==$key) {?>
								<img src="components/com_javoice/asset/images/icon-16-default.png" alt="<?php echo JText::_('DEFAULT' ); ?>" />
								<?php }?>
							</td>
						</tr>
					<?php }elseif($key=='intensedebate'){?>
					
						<tr>
							<td align="center">
								<?php echo $i+1;?>
							</td>
							<td align="center">
								<input type="radio" <?php if($this->params->get('run_system')==$key)echo "checked";?> id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $key;?>" onclick="Joomla.isChecked(this.checked);" />
							</td>
							<td>																
								<a target="_blank"  href="http://intensedebate.com">
									<?php echo $value;?>
								</a>
							</td>
							<td align="center">
								<a  href="index.php" onclick="jaCreatForm('edit&group=integrate' ,'<?php echo $key?>',600,450,0,'<?php echo $i?>','<?php echo $value;?>');return false;">
									<?php echo  JText::_('SETTING')?>
								</a>
							</td>	
							<td>
								<span>
									<?php echo JText::_('INTENSEDEBATE_INTRO')?>
								</span>						
							</td>									
							<td align="center">
								<?php if($this->params->get('run_system')==$key) {?>
								<img src="components/com_javoice/asset/images/icon-16-default.png" alt="<?php echo JText::_('DEFAULT' ); ?>" />
								<?php }?>
							</td>
						</tr>
					<?php }elseif($key=='disqus'){?>
					
						<tr>
							<td align="center">
								<?php echo $i+1;?>
							</td>
							<td align="center">
								<input type="radio" <?php if($this->params->get('run_system')==$key)echo "checked";?> id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $key;?>" onclick="Joomla.isChecked(this.checked);" />
							</td>
							<td>																
								<a target="_blank"  href="http://disqus.com/">
									<?php echo $value;?>
								</a>
							</td>
							<td align="center">
								<a  href="index.php" onclick="jaCreatForm('edit&group=integrate' ,'<?php echo $key?>',600,450,0,'<?php echo $i?>','<?php echo $value;?>');return false;">
									<?php echo  JText::_('SETTING')?>
								</a>
							</td>	
							<td>
								<span>
									<?php echo JText::_('DISQUS_INTRO')?>
								</span>						
							</td>									
							<td align="center">
								<?php if($this->params->get('run_system')==$key) {?>
								<img src="components/com_javoice/asset/images/icon-16-default.png" alt="<?php echo JText::_('DEFAULT' ); ?>" />
								<?php }?>
							</td>
						</tr>
					<?php }?>
				<?php 
				$i++;
				}?>
			</tbody>
		</table>
</div>
<div class="clr"></div>
<input type="hidden" name="option" value="com_javoice" />
<input type="hidden" name="view" value="configs" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="group" value="<?php echo $this->group; ?>" />
<input type="hidden" name="boxchecked" value="1" />
<?php echo JHTML::_( 'form.token' ); ?>	
 </form>