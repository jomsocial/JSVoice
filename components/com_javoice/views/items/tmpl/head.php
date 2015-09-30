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

	$style = '
	        .plugin_embed .smileys{
	            top: 5px;
				left: 20px;
	        	background:#ffea00;
	            clear:both;
	            height:84px;
	            width:105px;	    
				margin:0;        
	            padding:2px 1px 1px 2px !important;
	            position:absolute;
	            z-index:51;
	            -webkit-box-shadow:0 1px 3px #999;
				box-shadow:1px 2px 3px #666;
				-moz-border-radius:2px;
				-khtml-border-radius:2px;
				-webkit-border-radius:2px;
				border-radius:2px;
	        }        
	        .plugin_embed ul.smileys li{
	            display: inline;
	            float: left;
	            height:20px;
	            width:20px;
	            padding:2px 1px 1px 2px !important;	            
	            border:none;
	            padding:0
	        }
	        	        	        
	        .plugin_embed .smileys .smiley{
	            background: url('.JURI::base().'components/com_javoice/asset/images/smileys/'.$smiley.'/smileys_bg.png) no-repeat;
	            display:block;
	            height:20px;
	            width:20px;
	        }
	        .plugin_embed .smileys .smiley:hover{
	            background:#fff;
	        }
	        .plugin_embed .smileys .smiley span{
	            background: url('.JURI::base().'components/com_javoice/asset/images/smileys/'.$smiley.'/smileys.png) no-repeat;
	            display: inline;
	            float: left;
	            height:12px;
	            width:12px;
	            margin:4px !important;
	        }
	        .plugin_embed .smileys .smiley span span{
	            display: none;
	        } 
	        div.jav-item-content .jav-smiley, 
			div.jav-item-response .jav-smiley, 
			div.jav-item-bestanswer .jav-smiley{
	            font-family:inherit;
				font-size:100%;
				font-style:inherit;
				font-weight:inherit;
				text-align:justify;
	        }
	        div.jav-item-content .jav-smiley span, 
			div.jav-item-response .jav-smiley span,
			div.jav-item-bestanswer .jav-smiley span{
	            background: url('.JURI::base().'components/com_javoice/asset/images/smileys/'.$smiley.'/smileys.png) no-repeat scroll 0 0 transparent;
				display:inline-block;				
				height:12px;
				margin:4px !important;
				width:12px;
	        }
	        div.jav-item-content .jav-smiley span span, 
			div.jav-item-response .jav-smiley span span,
			div.jav-item-bestanswer .jav-smiley span span{
	            display:none;
	        }
	';
	$doc = JFactory::getDocument();
	$doc->addStyleDeclaration($style);
?>