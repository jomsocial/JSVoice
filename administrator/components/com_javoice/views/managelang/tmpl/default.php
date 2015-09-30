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
	JHTML::_('behavior.tooltip');
	$option = JRequest::getCmd('option');
	?>

<fieldset class="adminform">
	<div class="submenu-box">
		<div class="submenu-pad">
			<ul class="configuration" id="submenu">
				<li><a <?php if(!$this->client->id) echo "class=\"active\""?> href="index.php?option=com_javoice&view=managelang&client=0"><?php echo JText::_('SITE')?></a></li>
				<li><a <?php if($this->client->id) echo "class=\"active\""?> href="index.php?option=com_javoice&view=managelang&client=1"><?php echo JText::_('ADMINISTRATOR')?></a></li>
			</ul>
			<div class="clr"></div>
		</div>
	</div>
</fieldset>	
	<form name="adminForm" id="adminForm" action="index.php" method="post">		
		<table class="adminlist">
			<thead>
			
				<tr>
					<th width="20">
						#
					</th>
					<th width="30">&nbsp;
						
					</th>
					<th width="" class="title">
						<?php echo JText::_('LANGUAGE' ); ?>
					</th>
					<th width="">
						<?php echo JText::_('DEFAULT' ); ?>
					</th>
					<th width="">
						<?php echo JText::_('VERSION' ); ?>
					</th>
					
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="8">
						<?php echo $this->page->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			
			<tbody>
			<?php
				$k = 0;
				for ($i=0, $n=count( $this->rows ); $i < $n; $i++) {
					$row = &$this->rows[$i];
					?>
					<tr class="<?php echo "row$k"; ?>">
						<td width="">
							<?php echo $this->page->getRowOffset( $i ); ?>
						</td>
						<td width="">
							<input type="radio" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->language; ?>" onclick="Joomla.isChecked(this.checked);" />
						</td>
						<td width="">
							<a href="index.php?option=<?php echo $option;?>&view=managelang&task=edit&layout=form&lang=<?php echo $row->language;?>&client=<?php echo $this->client->id;?>"><?php echo $row->name;?></a>
						</td>
						<td width="" align="center">
							<?php
							if ($row->published == 1) {	 ?>
								<img src="components/com_javoice/asset/images/icon-16-default.png" alt="<?php echo JText::_('DEFAULT' ); ?>" />
								<?php
							} else {
								?>
								&nbsp;
							<?php
							}
						?>
						</td>
						<td align="center">
							<?php echo $row->version; ?>
						</td>
						
					</tr>
				<?php
				}
				?>
			</tbody>
			
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="client" value="<?php echo $this->client->id;?>" />
		<input type="hidden" name="view" value="managelang" />
		<input type="hidden" value="0" name="boxchecked"/>
		<input type="hidden" name="task" value="edit" />
		<input type="hidden" name="layout" value="form" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
