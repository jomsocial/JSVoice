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
$prefix = "";
$fucSmiley = "insertSmiley";
if(isset($this->textAreaID)){	
	$fucSmiley = "insertSmileyReply";
	$prefix		= "reply";	
}


	global $javconfig;
?>
<?php
	//use for smiley - add style and javascript	 
	if($javconfig['plugin']->get('enable_smileys',1)){		
		$smiley = $javconfig['plugin']->get('smiley',"default");		  			
?>
	<?php	
		$style = '
		        .plugin_embed .smileys{
		            top: -81px;
		        	background:#ffea00;
		            clear:both;
		            height:84px;
		            width:105px;	            
		            padding:2px 1px 1px 2px !important;
		            position:absolute;
		            z-index:51;
		            -webkit-box-shadow:0 1px 3px #999;box-shadow:1px 2px 3px #666;-moz-border-radius:2px;-khtml-border-radius:2px;-webkit-border-radius:2px;border-radius:2px;
		        }        
		        .plugin_embed .smileys li{
		            display: inline;
		            float: left;
		            height:20px;
		            width:20px;
		            padding:2px 1px 1px 2px !important;	            
		            border:none;
		            padding:0
		        }
		        .plugin_embed .smileys .smiley{
		            background: url('.JURI::base().'../components/com_javoice/asset/images/smileys/'.$smiley.'/smileys_bg.png) no-repeat;
		            display:block;
		            height:20px;
		            width:20px;
		        }
		        .plugin_embed .smileys .smiley:hover{
		            background:#fff;
		        }
		        .plugin_embed .smileys .smiley span{
		            background: url('.JURI::base().'../components/com_javoice/asset/images/smileys/'.$smiley.'/smileys.png) no-repeat;
		            display: inline;
		            float: left;
		            height:12px;
		            width:12px;
		            margin:4px !important;
		        }
		        .plugin_embed .smileys .smiley span span{
		            display: none;
		        } 
		        div.jav-item-content .jav-smiley {
		            font-family:inherit;
					font-size:100%;
					font-style:inherit;
					font-weight:inherit;
					text-align:justify;
		        }
		        div.jav-item-content .jav-smiley span{
		            background: url('.JURI::base().'../components/com_javoice/asset/images/smileys/'.$smiley.'/smileys.png) no-repeat scroll 0 0 transparent;
					display:inline-block;				
					height:12px;
					margin:4px !important;
					width:12px;
		        }
		        div.jav-item-content .jav-smiley span span{
		            display:none;
		        }
		';		
		$doc = JFactory::getDocument();
		$doc->addStyleDeclaration($style);
	?>	
<?php }?>
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