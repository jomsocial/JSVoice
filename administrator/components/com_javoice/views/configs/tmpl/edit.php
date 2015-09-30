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
	
	$application=$this->application;	
	$count= count( $application );
	$task=$this->task;
	$number=$this->number;
	$system = $this->system;
	$tabs=$this->tabs;
	$voicetypes=$this->voicetypes;

?>

<form action="index.php" method="post" name="adminForm"  id="adminForm">

	 <script type="text/javascript">
	function checkData() {
		var language=$$('#jav-language textarea');
		for ( var i = 0; i < language.length; i++) {
			if(language[i].style.display=='none')
				language[i].setStyles({left:'2000px', height:'0px', top:'0px', position:'absolute', display:''});
			
			
		}		
		 return true;
	}
			 
	 </script>	
	<input type="hidden" name="number" value="<?php echo $number;?>">
	<input type="hidden" name="tmpl" value="component">
	<?php if(!in_array($system, array('jomcomment', 'jcomments', 'jacomment'))){?>
	<fieldset class='border-radius'>
		<legend><?php echo JText::_('CONFIG_SYSTEM')?></legend>
		<table class="adminform">
			<tbody>	
				
				<tr> 
		
		 			<td class="key">
		 				<?php if($system=='intensedebate')$title = JText::_('ACCOUNT');
		 						elseif($system=='disqus') $title = JText::_('SUBDOMAIN');
		 				?>
						<span class="editlinktip hasTip" title="<?php echo JText::_("APPLICATIONSYSTEM_TITLE");?>::<?php echo JText::_("APPLICATIONSYSTEM_TITLE_DETAIL"); ?>">
							<?php echo JText::_("APPLICATIONSYSTEM_TITLE");?>
						</span>						
					</td> 
		
		 			<td>
		 					<input type="text" size="60" name="integrate[account_<?php echo $system;?>]" value="<?php echo  $this->params->get("account_$system") ?>">				
					</td> 
						 			
		 		</tr> 
 				
		 		
		 		<?php if($application[$system]=='Disqus'){?>
						<tr> 				
				 			<td class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_("APPLICATIONSYSTEM_MODE");?>::<?php echo JText::_("APPLICATIONSYSTEM_MODE_DETAIL"); ?>">
									<?php echo JText::_("APPLICATIONSYSTEM_MODE");?>
								</span>						
							</td> 
				
				 			<td>
								<?php
								$mode = ($this->params->get("system_mode_$system")==1) ? $this->params->get("system_mode_$system") : 0;
								$lists['mode'] 		= JHTML::_('select.booleanlist',  "integrate[system_mode_$system]", 'class="inputbox"', $mode );
								echo $lists['mode']; ?>				 							 									
							</td> 
								 			
				 		</tr> 		 		
		 		<?php }?>		 		
		 		
						 			
		 				 		
		 	</tbody>		
		</table>	
	</fieldset>	
	<?php }?>	
	
	<fieldset class='border-radius'>
		<legend><?php echo JText::_("LANGUAGE_OVERRIDE")?></legend>
			<?php echo $tabs;?>
			<div id='jav-language'>
		<?php
			if(count($voicetypes>0)){
				foreach ($voicetypes as $voicetype){
		?>
					<textarea id="language_<?php echo $system?>_<?php echo $voicetype->id?>" <?php if($this->active!=$voicetype->id)echo "style='display:none;'"?> rows="3" style="width:98%;" name="integrate[language_<?php echo $system?>_<?php echo $voicetype->id?>]"><?php echo $this->params->get("language_{$system}_{$voicetype->id}") ?></textarea>
		<?php
				} 
			}
		?>
		</div>
	</fieldset>
<input type="hidden" name="option" value="com_javoice" />
<input type="hidden" name="view" value="configs" />
<input type="hidden" name="task" value="saveIFrame" />
<input type="hidden" name="system" value="<?php echo $system?>" />
<input type="hidden" name="group" value="<?php echo $this->group; ?>" />
<input type="hidden" name="cid" value="<?php echo $this->cid; ?>" />
<input type="hidden" name="id" value="<?php echo $this->cid; ?>" />
<?php echo JHTML::_( 'form.token' ); ?>	
 </form>	