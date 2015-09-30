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
;(function($){
	jaCreatForm = function(jatask, cid, jaWidth, jaHeight, title,dsave,titlesave,location) {	
		jav_displayLoadingSpan();
		if (!cid)
			cid = 0;
		if (!jaWidth)
			jaWidth = 700;
		if (!jaHeight)
			jaHeight = 500;
		if(!titlesave)titlesave="Save";
		var Obj = document.getElementById('jaForm');
		if (!Obj) {
			var content = $('<div>').attr( {
				'id' :'ja-wrap-content'
			}).appendTo(document.body);
			
			var jaForm = $('<div>').attr( {
				'id' :'jaForm',
				'style' :'top: 0px;display:none;'
			});
			
			jaForm.appendTo(content);
			$('<div>').attr( {
				'id' :'javoice_tl'
			}).appendTo(jaForm);
			$('<div>').attr( {
				'id' :'javoice_tm'
			}).appendTo(jaForm);
			$('<div>').attr( {
				'id' :'javoice_tr'
			}).appendTo(jaForm);
			$('<a>').attr( {
				'id' :'javoice_ar',
				'href' :'javascript:void(0);',
				'onclick' :'jaFormHide();'
			}).appendTo($('#javoice_tr'));
			$("#javoice_ar").click( function() { jaFormHide(); } );
			$('<div>').attr( {
				'style' :'clear:both'
			}).appendTo(jaForm);
			$('<div>').attr( {
				'id' :'javoice_ml'
			}).appendTo(jaForm);
			$('<div>').attr( {
				'id' :'jaFormContentOuter'
			}).appendTo(jaForm);
			if (title) {
				$('<div>').attr( {
					'id' :'jaFormContentTop',
					'style' :'font-weight: bold;font-size:10pt;'
				}).appendTo($('#jaFormContentOuter'));
	
				$('#jaFormContentTop').html(title);
			}
			$('<div>').attr( {
				'id' :'jaFormContent',
				'style' :'position:relative;jaFormContent;overflow:scroll',
				'class' :''
			}).appendTo($('#jaFormContentOuter'));
			$('<div>').attr( {
				'id' :'jaFormContentBottom',
				'style':'bottom:0px;',
				'style' :'font-weight: bold;font-size:10pt;'
			}).appendTo($('#jaFormContentOuter'));		
			if (!dsave) {
				$('<button>').attr( {
					'id' :'javoice_as',
					'style':'width:60px;'
				}).html(titlesave).appendTo($('#jaFormContentBottom'));
				if(jatask.indexOf("&layout=add&type=") != -1 || jatask.indexOf("&layout=edit&type=") != -1){
					$("#javoice_as").click( function() { jav_addNewVoice(); } );
				}else{
					$("#javoice_as").click( function() { jav_submitbutton(); } );
				}
			}	
			$('<button>').attr( {
				'id' :'javoice_ac',
				'style':'width:60px;'
			}).html('Cancel').appendTo($('#jaFormContentBottom'));	
			$("#javoice_ac").click( function() { jaFormHide(); } );
			
			$('<div>').attr( {
				'id' :'javoice_bl'
			}).appendTo(jaForm);
			$('<div>').attr( {
				'id' :'javoice_bm'
			}).appendTo(jaForm);
			$('<div>').attr( {
				'id' :'javoice_br'
			}).appendTo(jaForm);			
			$('<div>').attr( {
				'style' :'clear: both;'
			}).appendTo(jaForm);
		}
		// Set jaFormWidth + 40
		// jQuery('#jaForm').width(jaWidth);
		$("#jaForm").css('width', jaWidth+'px');
	
		// jQuery('#jaFormContentOuter').width(jaWidth);
		$('#jaFormContentOuter').css('width', jaWidth+'px');
		// jQuery('#jaFormContentBottom').width(jaWidth);
		$('#jaFormContentBottom').css('width', jaWidth+'px');
		// jQuery('#jaFormContent').width(jaWidth);
		$('#jaFormContent').css('width', jaWidth+'px');
	
		// jQuery('#javoice_bm').width(jaWidth);
		$('#javoice_bm').css('width', jaWidth+'px');
		// jQuery('#javoice_tm').width(jaWidth);
		$('#javoice_tm').css('width', jaWidth+'px');
		var myWidth = 0, myHeight = 0;
	
		myWidth = $(window).width();
		myHeight = $(window).height();
	
		var yPos;
	
		if ($.browser.opera && $.browser.version > "9.5" && $.fn.jquery <= "1.2.6") {
			yPos = document.documentElement['clientHeight'] - 20;
		} else {
			yPos = $(window).height() - 20;
		}
	
		var leftPos = (myWidth - jaWidth) / 2;
	
		$('#jaForm').css('zIndex', cGetZIndexMax() + 1);
	
		/*
		 * jQuery.ajax({ url: jatask, cache: false, success: function(html){
		 * jQuery("#jaFormContent").append(html); } });
		 */
		//jataskNomal = jatask;
		//jatask = encodeURIComponent(jatask);
		if(jatask.indexOf("/") != -1){
			var url = jatask + "&cid[]=" + cid;		
		}else{
			var url = siteurl + "&task=" + jatask + "&cid[]=" + cid;
		}
		var forum_id = $('#jav-forum-select').attr('value');
		if(forum_id>0)url +="&forum_id="+forum_id;
		
		var req = new jQuery.ajax({
			method: 'get',
			url: url,		
			complete: function(data) { 
				$('#jaFormContent').html(data.responseText);
				if(jatask.indexOf("&layout=add&type=") != -1 || jatask.indexOf("&layout=edit&type=") != -1){			
					if(typeof(DCODE) !== 'undefined' && $("#newVoiceContent").length > 0){				
						DCODE.setTags (["LARGE", "MEDIUM", "HR", "B", "I", "U", "S", "UL", "OL", "SUB", "SUP", "QUOTE", "LINK", "IMG", "YOUTUBE", "HELP"]);
						DCODE.activate ("newVoiceContent", false);
					}
				}
				$(document).ready( function($) {
					if($('#loader')) {
						id='#'+jav_header;
						$(id).css('z-index','10');			
						$('#loader').hide();
					}
					$('li').click(function() {
						$('li').addClass('focused')
							.siblings().removeClass('focused');
					});
				}); 
			}
		});
	
		ajax_loaded = true;
	
		$("#jaForm").css('marginTop', '5px');
	
		/*
		 * Set height and width for transparent window
		 */
		if (title)
			$('#jaFormContentTop').width(jaWidth-20);	
		$('#jaForm').css('height', jaHeight);
		$('#jaForm').css('left', leftPos);
		$('#jaFormContent').css('height', (jaHeight-52)); // - 30px title and 20px
															// border
		$('#jaFormContent').css('width', (jaWidth)); // -20px border
		$('#jaFormContentOuter').css('height', jaHeight);
		$('#javoice_ml').css('height', jaHeight);
		$('#javoice_mr').css('height', jaHeight);
		$('#javoice_as').css('display','block');
		$('#javoice_ac').css('display','block');	
		$('#jaFormContentBottom').animate( {
			bottom :"0px",
			left:"0px",
			height :"30px"
		}, 200);
		$('#jaForm').fadeIn();
	
	}

	jaFormHide = function() {
		if ($('#jaFormAction').get().length > 0)
			$('#jaFormAction').animate( {
				bottom :"-40px"
			}, 200, '', function() {
				$('#jaForm').fadeOut('fast', function() {
					$(this).remove();
					$('#ja-wrap-content').remove();
				});
			});
		else {
	
			$('#jaForm').fadeOut('fast', function() {
				$(this).remove();
				$('#ja-wrap-content').remove();
			});
		}
	}

	jaFormActions = function(action) {
	
		$('#jav-form-wait').show();
		$('#jaFormContent input').attr('disabled', true);
		$('#jaFormContent textarea').attr('disabled', true);
		$('#jaFormContent button').attr('disabled', true);
	
	}
	
	jaFormResize = function(newheight) {
		$("#jaFormContentOuter").animate( {
			"left" :"+=50px"
		}, "slow");
	
		$("#jaFormContent").animate( {
			"left" :"+=50px"
		}, "slow");
		$("#iContent").animate( {
			"left" :"+=50px"
		}, "slow");
	
	}

	cGetZIndexMax = function () {
		var allElems = document.getElementsByTagName ? document
				.getElementsByTagName("*") : document.all; // or test for that too
		var maxZIndex = 0;
	
		for ( var i = 0; i < allElems.length; i++) {
			var elem = allElems[i];
			var cStyle = null;
			if (elem.currentStyle) {
				cStyle = elem.currentStyle;
			} else if (document.defaultView && document.defaultView.getComputedStyle) {
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
})(jQuery);