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

defined ( '_JEXEC' ) or die ( 'Restricted access' );

$fucSmiley = "insertSmiley";
$prefix = '';
if(isset($this->textAreaID)){
	$prefix = "Reply";
	$fucSmiley = "insertSmileyReply";
}
?>
<ul onmouseover='document.getElementById("jav-smileys-<?php echo $prefix;?>").style.display="block";' onmouseout='document.getElementById("jav-smileys-<?php echo $prefix;?>").style.display="none";' style="display: none;" id="jav-smileys-<?php echo $prefix;?>" class="smileys">
	<li><a href='javascript:<?php echo $fucSmiley;?>(":)");' class="smiley"><span style="background-position: 0px 0px;" onmouseover='document.getElementById("jav-smileys-<?php echo $prefix;?>").style.display="block";'><span>:)</span></span></a></li>
	<li><a href='javascript:<?php echo $fucSmiley;?>(":D");' class="smiley"><span style="background-position: -12px 0px;" onmouseover='document.getElementById("jav-smileys-<?php echo $prefix;?>").style.display="block";'><span>:D</span></span></a></li>
	<li><a href='javascript:<?php echo $fucSmiley;?>("xD");' class="smiley"><span style="background-position: -24px 0px;" onmouseover='document.getElementById("jav-smileys-<?php echo $prefix;?>").style.display="block";'><span>xD</span></span></a></li>
	<li><a href='javascript:<?php echo $fucSmiley;?>(";)");' class="smiley"><span style="background-position: -36px 0px;" onmouseover='document.getElementById("jav-smileys-<?php echo $prefix;?>").style.display="block";'><span>;)</span></span></a></li>
	<li><a href='javascript:<?php echo $fucSmiley;?>(":p");' class="smiley"><span style="background-position: -48px 0px;" onmouseover='document.getElementById("jav-smileys-<?php echo $prefix;?>").style.display="block";'><span>:p</span></span></a></li>
	<li><a href='javascript:<?php echo $fucSmiley;?>("^_^");' class="smiley"><span style="background-position: 0px -12px;" onmouseover='document.getElementById("jav-smileys-<?php echo $prefix;?>").style.display="block";'><span>^_^</span></span></a></li>
	<li><a href='javascript:<?php echo $fucSmiley;?>(":$");' class="smiley"><span style="background-position: -12px -12px;" onmouseover='document.getElementById("jav-smileys-<?php echo $prefix;?>").style.display="block";'><span>:$</span></span></a></li>
	<li><a href='javascript:<?php echo $fucSmiley;?>("B)");' class="smiley"><span style="background-position: -24px -12px;" onmouseover='document.getElementById("jav-smileys-<?php echo $prefix;?>").style.display="block";'><span>B)</span></span></a></li>
	<li><a href='javascript:<?php echo $fucSmiley;?>(":*");' class="smiley"><span style="background-position: -36px -12px;" onmouseover='document.getElementById("jav-smileys-<?php echo $prefix;?>").style.display="block";'><span>:*</span></span></a></li>
	<li><a href='javascript:<?php echo $fucSmiley;?>("(3");' class="smiley"><span style="background-position: -48px -12px;" onmouseover='document.getElementById("jav-smileys-<?php echo $prefix;?>").style.display="block";'><span>(3</span></span></a></li>
	<li><a href='javascript:<?php echo $fucSmiley;?>(":S");' class="smiley"><span style="background-position: 0px -24px;" onmouseover='document.getElementById("jav-smileys-<?php echo $prefix;?>").style.display="block";'><span>:S</span></span></a></li>
	<li><a href='javascript:<?php echo $fucSmiley;?>(":|");' class="smiley"><span style="background-position: -12px -24px;" onmouseover='document.getElementById("jav-smileys-<?php echo $prefix;?>").style.display="block";'><span>:|</span></span></a></li>
	<li><a href='javascript:<?php echo $fucSmiley;?>("=/");' class="smiley"><span style="background-position: -24px -24px;" onmouseover='document.getElementById("jav-smileys-<?php echo $prefix;?>").style.display="block";'><span>=/</span></span></a></li>
	<li><a href='javascript:<?php echo $fucSmiley;?>(":x");' class="smiley"><span style="background-position: -36px -24px;" onmouseover='document.getElementById("jav-smileys-<?php echo $prefix;?>").style.display="block";'><span>:x</span></span></a></li>
	<li><a href='javascript:<?php echo $fucSmiley;?>("o.0");' class="smiley"><span style="background-position: -48px -24px;" onmouseover='document.getElementById("jav-smileys-<?php echo $prefix;?>").style.display="block";'><span>o.0</span></span></a></li>
	<li><a href='javascript:<?php echo $fucSmiley;?>(":o");' class="smiley"><span style="background-position: 0px -36px;" onmouseover='document.getElementById("jav-smileys-<?php echo $prefix;?>").style.display="block";'><span>:o</span></span></a></li>
	<li><a href='javascript:<?php echo $fucSmiley;?>(":(");' class="smiley"><span style="background-position: -12px -36px;" onmouseover='document.getElementById("jav-smileys-<?php echo $prefix;?>").style.display="block";'><span>:(</span></span></a></li>
	<li><a href='javascript:<?php echo $fucSmiley;?>(":@");' class="smiley"><span style="background-position: -24px -36px;" onmouseover='document.getElementById("jav-smileys-<?php echo $prefix;?>").style.display="block";'><span>:@</span></span></a></li>
	<li><a href='javascript:<?php echo $fucSmiley;?>(":&#39;(");' class="smiley"><span style="background-position: -36px -36px;" onmouseover='document.getElementById("jav-smileys-<?php echo $prefix;?>").style.display="block";'><span>:&#39;(</span></span></a></li>
</ul>
<a href="javascript:void(0);" class="act-smiley" onclick='javascript:changeDisplay("jav-smileys-<?php echo $prefix;?>", "block", "smiley")' title="<?php echo JText::_("ADD_A_SMILEY");?>"><span><?php echo JText::_("ADD_A_SMILEY");?></span></a>