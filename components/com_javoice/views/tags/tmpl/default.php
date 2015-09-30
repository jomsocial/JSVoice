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

?>
<script type="text/javascript"> 	 
	function submitbutton(pressbutton){
		var form = document.adminForm;
	    if(pressbutton == 'add' || pressbutton == 'edit'){		    
	    	jaCreatForm("edit&action=tags",0,700,500,0,0,'<?php echo JText::_("NEW_TAG")?>');
	    }else{    
		    form.task.value = pressbutton;
		    form.submit();
	    }			
	}
</script>

<form action="index.php" method="post" name="adminForm">
  <table width="100%">  	
    <tr>
      <td align="left" width="50%"><?php echo JText::_('FILTER'); ?>
        <input type="text" name="search" id="search" value="<?php echo $this->lists['search'] ?>" class="text_area" onchange="document.adminForm.submit();" title="<?php echo JText::_('FILTER_BY_NAME'); ?>"/>
        <button onclick="this.form.submit();"><?php echo JText::_('GO'); ?></button>
        <button onclick="document.getElementById('search').value='';this.form.getElementById('filter_state').value='-1';this.form.submit();"><?php echo JText::_('RESET'); ?></button></td>
      <td align="right" width="50%"><?php echo $this->lists['state']; ?></td>
    </tr>
  </table>
  <table class="adminlist">
    <thead>
      <tr>
        <th width="20"> # </th>
        <th width="20"> <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows ); ?>);" /></th>
        <th class="title"> <?php echo JHTML::_('grid.sort',   JText::_('NAME'), 'name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?> </th>
        <th width="5%" nowrap="nowrap"> <?php echo JHTML::_('grid.sort', JText::_('PUBLISHED'), 'published', @$this->lists['order_Dir'], @$this->lists['order'] ); ?> </th>
        <th width="5%" nowrap="nowrap"> <?php echo JHTML::_('grid.sort', JText::_('ID'), 'id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?> </th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="5"><?php echo $this->page->getListFooter(); ?></td>
      </tr>
    </tfoot>
    <tbody>
      <?php
      $k = 0; $i = 0;
			foreach ($this->rows as $row) :
				$row->checked_out=0;
				$checked 	= JHTML::_('grid.checkedout', $row, $i );
				$published = JHTML::_('grid.published', $row, $i );
				$link = JRoute::_('index.php?option=com_javoice&view=tags&layout=form&task=edit&cid='.$row->id);
			?>
      <tr class="<?php echo "row$k"; ?>">
        <td width="20" align="center"><?php echo $i+1; ?></td>
        <td width="20" align="center"><?php echo $checked; ?></td>
        <!--<td><a onclick="jaCreatForm('&amp;layout=edit','<?php echo $row->id?>', 700, 350,'<?php echo JText::_('EDIT_TAGS')." ".$row->name;?>');return false;" href="<?php echo $link; ?>"><?php echo $row->name;?></a></td>
        -->
        <td><a href="<?php echo $link; ?>"><?php echo $row->name;?></a></td>
        <td align="center"><?php echo $published;?></td>
        <td align="center"><?php echo $row->id; ?></td>
      </tr>
      <?php $k = 1 - $k; $i++; endforeach; ?>
    </tbody>
  </table>
  <input type="hidden" name="option" value="<?php echo $option;?>" />
  <input type="hidden" name="view" value="<?php echo JRequest::getVar('view'); ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
  <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
  <input type="hidden" name="boxchecked" value="0" />
  <?php echo JHTML::_( 'form.token' );?>
</form>
