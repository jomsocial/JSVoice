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
// JavaScript Document

// Must re-initialize window position

function jaCreatForm(jatask, cid, jaWidth, jaHeight, vmenu, number, title,
		dsave,titlesave,location) {	
	if(jaHeight > 350) jaHeight = 350;
	if (!vmenu)
		vmenu = 0;	
	if (!cid)
		cid = 0;
	if (!jaWidth)
		jaWidth = 700;
	if (!jaHeight)
		jaHeight = 500;
	if (!number)
		number = 0;
	if (!location)
		location = '';	
	if(!titlesave)titlesave='Save';
	var Obj = document.getElementById('jaForm');
	if (!Obj) {
		var content = jQuery('<div>').attr( {
			'id' :'ja-wrap-content'
		}).appendTo(document.body);
		var jaForm = jQuery('<div>').attr( {
			'id' :'jaForm',
			'style' :'top: 0px;display:none;'
		});
		jaForm.appendTo(content);
		jQuery('<div>').attr( {
			'id' :'javoice_tl'
		}).appendTo(jaForm);
		jQuery('<div>').attr( {
			'id' :'javoice_tm'
		}).appendTo(jaForm);
		jQuery('<div>').attr( {
			'id' :'javoice_tr'
		}).appendTo(jaForm);

		jQuery('<a>').attr( {
			'id' :'javoice_ar',
			'style' :'top:5px',
			'href' :'javascript:void(0);'
		}).appendTo(jQuery('#javoice_tr'));
		jQuery("#javoice_ar").click( function() { jaFormHide(); } );

		jQuery('<div>').attr( {
			'style' :'clear:both'
		}).appendTo(jaForm);
		jQuery('<div>').attr( {
			'id' :'javoice_ml'
		}).appendTo(jaForm);
		jQuery('<div>').attr( {
			'id' :'jaFormContentOuter'
		}).appendTo(jaForm);

		if (title) {
			jQuery('<div>').attr( {
				'id' :'jaFormContentTop',
				'style' :'font-weight: bold;font-size:10pt;'
			}).appendTo(jQuery('#jaFormContentOuter'));

			jQuery('#jaFormContentTop').html(title);
		}
		jQuery('<div>').attr( {
			'id' :'jaFormContent',
			'style' :'position:relative',
			'class' :''
		}).appendTo(jQuery('#jaFormContentOuter'));
		jQuery('<div>').attr( {
			'id' :'javoice_mr'
		}).appendTo(jaForm);
		jQuery('<div>').attr( {
			'style' :'clear: both;'
		}).appendTo(jaForm);
		
		jQuery('<div>').attr( {
			'id' :'jaFormContentBottom',
			'style':'bottom:0px;',
			'style' :'font-weight: bold;font-size:10pt;display:block;'
		}).appendTo(jQuery('#jaFormContentOuter'));		
		if (!dsave) {
			jQuery('<button>').attr( {
				'id' :'javoice_as',
				'style':'width:60px;'
			}).html(titlesave).appendTo(jQuery('#jaFormContentBottom'));
			if(jatask != "edit&action=tags"){
				jQuery("#javoice_as").click( function() { submitbuttonAdmin(); } );
			}else{				
				jQuery("#javoice_as").click( function() { submitTags(); } );
			}
		}	
		jQuery('<button>').attr( {
			'id' :'javoice_ac',
			'style':'width:60px;'
		}).html('Cancel').appendTo(jQuery('#jaFormContentBottom'));	
		jQuery("#javoice_ac").click( function() { jaFormHide(); } );
		
		jQuery('<div>').attr( {
			'id' :'javoice_bl'
		}).appendTo(jaForm);
		jQuery('<div>').attr( {
			'id' :'javoice_bm'
		}).appendTo(jaForm);
		jQuery('<div>').attr( {
			'id' :'javoice_br'
		}).appendTo(jaForm);
	
		jQuery('<div>').attr( {
			'style' :'clear: both;'
		}).appendTo(jaForm);

	}

	// Set jaFormWidth + 40
	jQuery('#jaForm').width(jaWidth);
	if (title)
		jQuery('#jaFormContentTop').width(jaWidth-20);
	jQuery('#jaFormContentBottom').width(jaWidth);
	jQuery('#jaFormContentOuter').width(jaWidth);

	jQuery('#jaFormContent').width(jaWidth);
	jQuery('#javoice_bm').width(jaWidth);
	jQuery('#javoice_tm').width(jaWidth);

	var myWidth = 0, myHeight = 0;

	myWidth = jQuery(window).width();
	myHeight = jQuery(window).height();

	var yPos;

	if (jQuery.browser.opera && jQuery.browser.version > "9.5"
			&& jQuery.fn.jquery <= "1.2.6") {
		yPos = document.documentElement['clientHeight'] - 20;
	} else {
		yPos = jQuery(window).height() - 20;
	}

	var leftPos = (myWidth - jaWidth) / 2;

	jQuery('#jaForm').css('zIndex', cGetZIndexMax() + 1);

	/*
	 * jQuery.ajax({ url: jatask, cache: false, success: function(html){
	 * jQuery("#jaFormContent").append(html); } });
	 */
	var url = siteurl + "&task=" + jatask + "&cid[]=" + cid;
	url += '&viewmenu=' + vmenu;
	url += '&number=' + number;	
	if (jQuery('#iContent').length >0){
		jQuery('#iContent').attr('src',url);
		jQuery('#jaFormContentTop').html(title);
	}
	else{
		jQuery('<iframe>').attr( {
			'id' :'iContent',
			'src' :url,
			'width' :jaWidth,
			'height' :jaHeight-80
		}).appendTo(jQuery('#jaFormContent'));
		jQuery("#iContent").load( function() { loadIFrameComplete(); } );
	}
	/*
	 * Set editor position, center it in screen regardless of the scroll
	 * position
	 */
	if (location){
		  var pos = jQuery(location).offset();
		  var topPos = pos.top -30 - jQuery(window).scrollTop();
		  var height = jQuery(window).height();
		  var absTop =eval( eval(height) - eval(topPos) - eval(jaHeight));
		  if(absTop < 0){
			  topPos = pos.top - jaHeight - 55 - jQuery(window).scrollTop();
		  }
		  var leftPos = pos.left - (jaWidth/2) +10;
		  jQuery("#jaForm").css({'top': topPos,'left':leftPos});
		
	}else{
		jQuery("#jaForm").css('marginTop', '5px');
		jQuery('#jaForm').css('left', leftPos);
	}
	/*
	 * Set height and width for transparent window
	 */
	jQuery('#jaForm').css('height', jaHeight);
	

	jQuery('#javoice-wait').css( {
		'top' :jaHeight / 2 - 10,
		'left' :jaWidth / 2 - 10
	})
	jQuery('#iContent').css('border', '0px');
	jQuery('#jaFormContentOuter').css('height', jaHeight-20);
	jQuery('#jaFormContent').css('height', jaHeight-60);
	jQuery('#javoice_ml').css('height', jaHeight);
	jQuery('#javoice_mr').css('height', jaHeight);

	jQuery('#jaForm').fadeIn('slow');

}

function jaFormHide() {
	
	if (jQuery('#javoice_ar').get().length > 0)
		jQuery('#javoice_ar').animate( {
			top :"-20px"
		}, 200, '');
	if (jQuery('#jaFormContentBottom').get().length > 0)
		jQuery('#jaFormContentBottom').animate( {
			bottom :"0px",
			height :"0px"
		}, 200);
	jQuery('#ja-wrap-content').fadeOut('fast', function() {
		jQuery(this).remove();
	});
	
}
function jaFormHideIFrame() {
	var jaForm = jQuery("#ja-wrap-content", window.parent.document);
	if (jQuery('#javoice_ar').get().length > 0)
		jQuery('#javoice_ar').animate( {
			top :"-20px"
		}, 200, '');

	jaForm.fadeOut('slow', function() {
		jaForm.remove();
	});

}
function loadIFrameComplete(){
	jQuery('#javoice-wait',window.parent.document).css('display','none');
	jQuery('#javoice_as',window.parent.document).css('display','block');
	jQuery('#javoice_ac',window.parent.document).css('display','block');
	jaFormActions();	
}
function jaFormActions() {
	if (jQuery('#jaFormContentBottom').get().length > 0)
		jQuery('#jaFormContentBottom').animate( {
			bottom :"0px",
			right:"0px",
			height :"30px"
		}, 200);

	jQuery('#ja-wrap-content').fadeIn('fast');
}

function jaFormResize(newheight) {
	jQuery("#jaFormContentOuter").animate( {
		"left" :"+=50px"
	}, "slow");

	jQuery("#jaFormContent").animate( {
		"left" :"+=50px"
	}, "slow");
	jQuery("#iContent").animate( {
		"left" :"+=50px"
	}, "slow");
	/*
	 * jQuery('#iContent', window.parent.document).animate( { height:
	 * jQuery(this).height()+30 });
	 */
}

function cGetZIndexMax() {
	var allElems = document.getElementsByTagName ? document
			.getElementsByTagName("*") : document.all; // or test for that too
	var maxZIndex = 0;

	for ( var i = 0; i < allElems.length; i++) {
		var elem = allElems[i];
		var cStyle = null;
		if (elem.currentStyle) {
			cStyle = elem.currentStyle;
		} else if (document.defaultView
				&& document.defaultView.getComputedStyle) {
			cStyle = document.defaultView.getComputedStyle(elem, "");
		}

		var sNum;
		if (cStyle) {
			sNum = Number(cStyle.zIndex);
		} else {
			sNum = Number(elem.style.zIndex);
		}
		if (!isNaN(sNum)) {
			maxZIndex = Math.max(maxZIndex, sNum);
		}
	}
	return maxZIndex;
}
