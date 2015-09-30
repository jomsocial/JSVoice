<?php
defined ( '_JEXEC' ) or die ( 'Restricted access' );
$textAreaID = "newVoiceContent";
global $javconfig;
if(isset($this->textAreaID)){
	$textAreaID = "newVoiceContentReply";
}     
?>
<li class="jac-bbcode-large">
	<a href="#" title="<?php echo JText::_("LARGE_TITLE"); ?>" onclick="return DCODE.doClick ('<?php echo $textAreaID;?>', 'LARGE');"><?php echo JText::_("LARGE");?></a>	
</li>
<li class="jac-bbcode-medium">
	<a href="#" title="<?php echo JText::_("MEDIUM_TITLE"); ?>" onclick="return DCODE.doClick ('<?php echo $textAreaID;?>', 'MEDIUM');"><?php echo JText::_("MEDIUM");?></a>	
</li>
<li class="jac-bbcode-horizontal-rule">
	<a href="#" title="<?php echo JText::_("HORIZONTAL_RULE"); ?>" onclick="return DCODE.doClick ('<?php echo $textAreaID;?>', 'HR');"><?php echo JText::_("HR");?></a>	
</li>
<li class="jac-bbcode-bold-text">
	<a href="#" title="<?php echo JText::_("BOLD_TEXT"); ?>" onclick="return DCODE.doClick ('<?php echo $textAreaID;?>', 'B');"><?php echo JText::_("B");?></a>	
</li>
<li class="jac-bbcode-italic-text">
	<a href="#" title="<?php echo JText::_("ITALIC_TEXT"); ?>" onclick="return DCODE.doClick ('<?php echo $textAreaID;?>', 'I');"><?php echo JText::_("I");?></a>	
</li>
<li class="jac-bbcode-underline-text">
	<a href="#" title="<?php echo JText::_("UNDERLINE_TEXT"); ?>" onclick="return DCODE.doClick ('<?php echo $textAreaID;?>', 'U');"><?php echo JText::_("U");?></a>	
</li>
<li class="jac-bbcode-line-through-text">
	<a href="#" title="<?php echo JText::_("LINE_THROUGH_TEXT"); ?>" onclick="return DCODE.doClick ('<?php echo $textAreaID;?>', 'S');"><?php echo JText::_("S");?></a>	
</li>
<li class="jac-bbcode-bullet-list-text">
	<a href="#" title="<?php echo JText::_("UNORDERED_BULLET_LIST"); ?>" onclick="return DCODE.doClick ('<?php echo $textAreaID;?>', 'UL');"><?php echo JText::_("UL");?></a>	
</li>	
<li class="jac-bbcode-subscript">
	<a href="#" title="<?php echo JText::_("SUBSCRIPT_TEXT"); ?>" onclick="return DCODE.doClick ('<?php echo $textAreaID;?>', 'SUB');"><?php echo JText::_("SUB");?></a>	
</li>
<li class="jac-bbcode-superscript">
	<a href="#" title="<?php echo JText::_("SUPERSCRIPT_TEXT"); ?>" onclick="return DCODE.doClick ('<?php echo $textAreaID;?>', 'SUP');"><?php echo JText::_("SUP");?></a>	
</li>
<li class="jac-bbcode-quotation">
	<a href="#" title="<?php echo JText::_("QUOTATION"); ?>" onclick="return DCODE.doClick ('<?php echo $textAreaID;?>', 'QUOTE');"><?php echo JText::_("QUOTE");?></a>	
</li>
<li class="jac-bbcode-link">
	<a href="#" title="<?php echo JText::_("LINK__EMAIL"); ?>" onclick="return DCODE.doClick ('<?php echo $textAreaID;?>', 'LINK');"><?php echo JText::_("LINK");?></a>	
</li>
<?php if($javconfig["plugin"]->get("enable_youtube",0)){ ?>
<li class="jac-bbcode-youtube">
	<a href="#" title="<?php echo JText::_("YOUTUBE"); ?>" onclick="return DCODE.doClick ('<?php echo $textAreaID;?>', 'YOUTUBE');"><?php echo JText::_("YOUTUBE");?></a>	
</li>
<?php }?>
<li class="jac-bbcode-image">
	<a href="#" title="<?php echo JText::_("IMAGE"); ?>" onclick="return DCODE.doClick ('<?php echo $textAreaID;?>', 'IMG');"><?php echo JText::_("IMG");?></a>	
</li>