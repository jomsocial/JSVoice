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
var timeout = '';
jQuery.noConflict();
jQuery(document).ready( function($) {
	var message = $("#system-message").children().size();
	if(message>0){
		setTimeout("hiddenMessage()", 15000);
	}
});
function hiddenNote(type, display, hidden) {
	jQuery(document).ready( function($) {
		var value = 0;
		if ($('#jav-system-message').css('display') == 'block') {
			$('#jav-system-message').attr('style', 'display:none');
			value = 1;
			$('#jav_help').html(display);
		} else {
			$('#jav-system-message').attr('style', 'display:block');
			value = 0;
			$('#jav_help').html(hidden);
		}
		setCookie('hidden_message_' + type, value, 365);
	});
}
function setCookie(name, value, expires, path, domain, secure) {
	var today = new Date();
	today.setTime(today.getTime());
	if (expires) {
		expires = expires * 1000 * 60 * 60 * 24;
	}
	var expires_date = new Date(today.getTime() + (expires));
	document.cookie = name + "=" + escape(value)
			+ ((expires) ? ";expires=" + expires_date.toGMTString() : "")
			+ ((path) ? ";path=" + path : "")
			+ ((domain) ? ";domain=" + domain : "")
			+ ((secure) ? ";secure" : "");
}

function getCookie(name) {
	var start = document.cookie.indexOf(name + "=");
	var len = start + name.length + 1;
	if ((!start) && (name != document.cookie.substring(0, name.length))) {
		return null;
	}
	if (start == -1)
		return null;
	var end = document.cookie.indexOf(";", len);
	if (end == -1)
		end = document.cookie.length;
	return unescape(document.cookie.substring(len, end));
}

function deleteCookie(name, path, domain) {
	if (getCookie(name))
		document.cookie = name + "=" + ((path) ? ";path=" + path : "")
				+ ((domain) ? ";domain=" + domain : "")
				+ ";expires=Thu, 01-Jan-1970 00:00:01 GMT";
}

function submitbutton(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'add' || pressbutton == 'edit') {

		jaCreatForm('edit');
	} else {
		form.task.value = pressbutton;
		form.submit();
	}
}

Joomla.submitform = function(task, form) {
	submitbutton(task);
};

function selectOption(type) {
	if ($(type).checked) {
		$('text_option_' + type).setStyle('display', '');
	} else {
		$('text_option_' + type).setStyle('display', 'none');
	}
	jaFormResize(500);
}
function displaySelectStatus(obj, id) {
	if (obj.checked) {
		$('jav_div_status_' + id).setStyle('display', '');
	} else {
		$('jav_div_status_' + id).setStyle('display', 'none');
	}
}
function displayCache() {
	var isdisplay = '';
	if ($('cache[caching]0').checked) {
		isdisplay = 'none';
	}
	$('tr_cache_time').setStyle('display', isdisplay);
	$('tr_cache_base').setStyle('display', isdisplay);
}
function displayEmail() {
	var isdisplay = '';
	if ($('emails[enabled]0').checked) {
		isdisplay = 'none';
	}
	$('tr_sitname').setStyle('display', isdisplay);
	$('tr_fromname').setStyle('display', isdisplay);
	$('tr_fromemail').setStyle('display', isdisplay);
	$('tr_ccemail').setStyle('display', isdisplay);
	$('tr_sendmode').setStyle('display', isdisplay);
}
function OptionSelected(value, name, isselect) {
	var elSel = document.getElementById(name);
	for (i = elSel.length - 1; i >= 0; i--) {
		if (elSel.options[i].value == value) {
			elSel.options[i].selected = isselect;
			break;
		}
	}
}

function changePost() {
	var elSel = document.getElementById('gids_post');
	for (i = elSel.length - 1; i >= 0; i--) {
		if (elSel.options[i].selected) {
			OptionSelected(elSel.options[i].value, 'gids_view', true);
		}
	}
}
function changeView() {
	var elSel = document.getElementById('gids_view');
	for (i = elSel.length - 1; i >= 0; i--) {
		if (!elSel.options[i].selected) {
			OptionSelected(elSel.options[i].value, 'gids_post', false);
		}
	}
}

var pr_ajax = null;

function CompleteLoadAjax() {
	if ($('loadingspan')) {
		$('loadingspan').setStyle('display', 'none');
	}
}
function displayLoadingSpan() {
	if ($('loadingspan')) {
		$('loadingspan').setStyle('display', '');
	}
}
function loadContentAjax(urlrequest, div_id) {	 
	urlrequest += "&ajax=1";
	displayLoadingSpan();
	if (pr_ajax != null) {
		pr_ajax.cancel();
	}
	var req = new Request.HTML({
      method: 'get',
      url: urlrequest,	 
	  update :div_id,
      onComplete :CompleteLoadAjax      
    }).send();	
}
function changeStatus(urlrequest,voicetype_id) {
	urlrequest += "&voice_types_id=" + voicetype_id;
	loadContentAjax(urlrequest, 'jav-status');
}
function changeVoiceType(urlrequest, div_id, arg, obj) {
	urlrequest += "&" + arg + "=" + obj.value;
	loadContentAjax(urlrequest, div_id);
}
function changeForumByVoiceType(urlrequestforum,voicetype_id,urlrequeststatus) {
	urlrequestforum += "&voice_types_id=" + voicetype_id;
	if (pr_ajax != null) {
		pr_ajax.cancel();
	}
	var req = new Request.HTML({
      method: 'get',
      url: urlrequestforum,	 
	  update :'jav-forum',
      onComplete: function() { changeStatus(urlrequeststatus,voicetype_id); }
    }).send();
}
function changeStatusByForum(urlrequeststatus) {
	urlrequeststatus += "&voice_types_id=" + $('voice_types_id').value
			+ "&ajax=1";
	displayLoadingSpan();
	if (pr_ajax != null) {
		pr_ajax.cancel();
	}
	var req = new Request.HTML({
      method: 'get',
      url: urlrequeststatus,	 
	  update :'jav-status',
      onComplete :CompleteLoadAjax
    }).send();
}
function checkError() {
	var flag = true;
	var requireds = jQuery('#iContent').contents().find('input.required');
	jQuery.each(requireds, function(i, item) {
		if (jQuery(item).attr('value') == '') {
			var li_parent = jQuery(item.parentNode.parentNode);
			li_parent.addClass('error');
		}
	});
	var isDuplicate = false;
	var duplicates = jQuery('#iContent').contents().find('input.duplicate');
	jQuery.each(duplicates,function (i,el1) {
	        var current_val = jQuery(el1).val();
	        if (current_val != "") {
	            jQuery.each(duplicates,function (j,el2) {
	                if (jQuery(el2).val() == current_val && i != j) {
	                	isDuplicate = true;
	                    var li_parent_el1 = jQuery(el1.parentNode.parentNode);
	                    li_parent_el1.addClass('error');
	                    return;
	                }
	            });
	        }
	});
	if(!isDuplicate){
		jQuery.each(duplicates,function (i,items) {
			var li_parent = jQuery(items.parentNode.parentNode);
			li_parent.removeClass('error');
		});
	}
	var errors = jQuery('#iContent').contents().find('li.error');
	errors.each( function() {
		flag = false;
		return;
	});
							
	return flag;
}
function submitbuttonAdmin() {
	var flag = checkError();
	if (flag) {
		check = true;
		jQuery(document).ready(function($) {
			numDiv = jQuery('#iContent').contents().find('div#newVoiceContent');		
			if(numDiv.length > 0){
				jQuery('#iContent').contents().find("div#err_exitchekspelling").show();
				alert(jQuery('#iContent').contents().find("div#err_exitchekspelling").html());
				check = false;
				return false;
			}else{
				jQuery('#iContent').contents().find("div#err_exitchekspelling").hide();
			}
			
			numDiv = jQuery('#iContent').contents().find('div#newVoiceContentReply');		
			if(numDiv.length > 0){
				jQuery('#iContent').contents().find("div#err_exitchekspellingReply").show();
				alert(jQuery('#iContent').contents().find("div#err_exitchekspellingReply").html());
				check = false;
				return false;
			}else{
				jQuery('#iContent').contents().find("div#err_exitchekspellingReply").hide();
			}
			
			numDiv = jQuery('#iContent').contents().find('select#forums_id');
			if(numDiv.length > 0){
				if(numDiv[0].value == 0){
					jQuery('#iContent').contents().find("div#jav-select-forum").show();
					alert(jQuery('#iContent').contents().find("div#jav-select-forum").html());
					check = false;
					return false;
				}else{
					jQuery('#iContent').contents().find("div#jav-select-forum").hide();
				}
			}
			isFocus= 0;
			if(typeof jav_minimum_search  == 'undefined'){
				jav_minimum_search= 4;	
			}
			if(jQuery('#iContent').contents().find('#title').length >0 && jQuery('#iContent').contents().find('#title').attr('value').length < jav_minimum_search){				
				jQuery('#iContent').contents().find('#title').parent().parent().addClass("error");
				jQuery('#iContent').contents().find('#jav-error-sort-title').show();
				alert(jQuery('#iContent').contents().find('#jav-error-sort-title').html());
				if(isFocus == 0){				
					jQuery('#iContent').contents().find('#title').focus();
					isFocus = 1;
				}
				check = false;
				return false;
			}else{
				jQuery('#iContent').contents().find('#title').parent().parent().removeClass("error");
				jQuery('#iContent').contents().find('#jav-error-sort-title').hide();
			}

			
			if(jQuery('#iContent').contents().find('#javGuestName').length >0 && jQuery('#iContent').contents().find('#javGuestName').attr('value')==''){
				jQuery('#iContent').contents().find('#err_javGuestName').show();
				alert(jQuery('#iContent').contents().find('#err_javGuestName').html());
				//jQuery('#iContent').contents().find('#javGuestName').parent().parent().addClass("error");
				if(isFocus == 0){
					jQuery('#iContent').contents().find('#javGuestName').focus();
					isFocus = 1;
				}
				check = false;
				return false;
			}else{
				//jQuery('#iContent').contents().find('#javGuestName').parent().parent().removeClass("error");
				jQuery('#iContent').contents().find('#err_javGuestName').hide();
			}
			
			if(jQuery('#iContent').contents().find('#javGuestEmail').length >0){			
				if(jQuery('#iContent').contents().find('#javGuestEmail').attr('value')==''){								
					jQuery('#iContent').contents().find('#err_javGuestEmail').show();
					//jQuery('#iContent').contents().find('#javGuestEmail').parent().parent().addClass("error");
					alert(jQuery('#iContent').contents().find('#err_javGuestEmail').html());
					if(isFocus == 0){
						jQuery('#iContent').contents().find('#javGuestEmail').focus();
						isFocus = 1;
					}
					check = false;
					return false;
				}else{								
					jQuery('#iContent').contents().find('#err_javGuestEmail').hide();
					var filter = /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;
					
					if (!filter.test(jQuery('#iContent').contents().find('#javGuestEmail').attr('value'))) {					
						jQuery('#iContent').contents().find('#err_javGuestEmailInvalid').show();
						jQuery('#iContent').contents().find('#javGuestEmail').parent().parent().addClass("error");
						alert(jQuery('#iContent').contents().find('#err_javGuestEmailInvalid').html());
						if(isFocus == 0){
							jQuery('#iContent').contents().find('#javGuestEmail').focus();
							isFocus = 1;
						}									
						check = false;
						return false;
					}else{
						//jQuery('#iContent').contents().find('#javGuestEmail').parent().parent().removeClass("error");
						jQuery('#iContent').contents().find('#err_javGuestEmailInvalid').hide();
					}
				}			
			}
			
			if(check == false)
				return false;
			
			textRespose = jQuery('#iContent').contents().find('textarea#newVoiceContentReply');									
			if(textRespose.length >0 && textRespose.val() == ""){
				jQuery('#iContent').contents().find("div#err_emptyreplyvoice").show();
				alert(jQuery('#iContent').contents().find("div#err_emptyreplyvoice").html());
				check = false;
				return false;			
			}else{
				jQuery('#iContent').contents().find("div#err_emptyreplyvoice").hide();
			}						
		});		
		
		if(check == false){
			return;
		}
		jQuery(document).ready(
				function($) {
					$('#javoice-wait').css( {
						'display' :''
					});

					$.get("index.php", $("#iContent").contents().find(
							"#adminForm").serialize(), function(res) {
						jaFormHideIFrame();
						parseData_admin(res);
					}, 'json');
				});
	}else
		alert("Invalid data! Please insert information again!");
}
function parseData_admin(response) {

	jQuery(document, window.parent.document).ready( function($) {
		var reload = 0;
		if(response.data){			
			myResponse = response.data; 
		}else{						
			myResponse = response;
		}
		$.each(myResponse, function(i, item) {
			var divId = item.id;
			var type = item.type;
			var value = item.value;
			if ($(divId, window.parent.document) != undefined) {
				if (type == 'html') {
					if ($(divId, window.parent.document))
						$(divId, window.parent.document).html(value);
					else
						alert('not fount element');
				} else {
					if (type == 'reload') {
						if (value == 1)
							reload = 1;
					} else if(type=='-javmsg-'){
						alert(value);
					}else{
						if(type=='val'){
							$(divId, window.parent.document).val(value);
						}else{
							$(divId, window.parent.document).attr(type, value);
						}
					}
				}
			}
		});		
		if (reload == 1)
			parent.window.document.adminForm.submit();
		else
			setTimeout("hiddenMessage()", 5000);
	});

}
function hiddenMessage() {
	jQuery('#system-message', window.parent.document).html('');
}
function checkDataElementType(el, type, message, count) {
	if (type == 'int') {
		if ($(el) && !isNaN($(el).value) && $(el).value) {
			return true;
		}
	}
	if (type == 'string') {
		if ($(el) && $(el).value != '') {
			return true;
		}
	}
	if (type == 'array') {
		var c = 0;
		if (count > 0) {
			for ( var i = 1; i <= count; i++) {
				var id = el + "_" + i;
				if ($(id) && $(id).checked)
					c++;
			}
		}
		if (c > 0) {
			return true;
		}
	}
	alert(message);
	return false;
}
function checkdataInt(el, class_css,positive) {
	var li_parent = $(el.parentNode.parentNode);
	var result=true;
	var value = $(el).value;
	if(!positive)positive = 1;
	if (!value) {
		result=false;
	}else{
		if(isNaN(value))
			result=false;
		else{
			if(value<0 & positive==1)result=false;
		}
	}	
	if (result) {
		li_parent.removeClass(class_css);
	} else
		li_parent.addClass(class_css);
}
function checkdataIntAlert(el,mess,def){
	var value = $(el).value;
	var result=true;
	if (!value) {
		result=false;
	}else{
		if(isNaN(value))
			result=false;
		else{
			if(value<0)result=false;
		}
	}
	if(!result){
		alert(mess);
		$(el).value = def;
	}
}
function checkdataString(el, class_css) {
	var li_parent = $(el.parentNode.parentNode);
	if (trim(el.value) != '')
		li_parent.removeClass(class_css);
	else
		li_parent.addClass(class_css);
}
function trim(str, chars) {
	return ltrim(rtrim(str, chars), chars);
}
 
function ltrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
}
 
function rtrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
}
function ckStartingStatus(mv, mt) {
	var v = 0;
	var voice_types = $$('#jav_td_status input');
	for ( var i = 0; i < voice_types.length; i++) {
		if (voice_types[i].checked) {
			v++;
			var id = 'voice_type_status_' + voice_types[i].value;
			if (!$(id).value) {
				alert(mt);
				return false;
			}
		}
	}
	if (!v) {
		alert(mv);
		return false;
	}
	return true;
}
function removeoption(obj) {
	jQuery(document).ready( function($) {
		var el = $(obj).parent().parent();
		if (el)
			el.remove();
	});
	hiddenFirst();
}
function addvoteoption(obj) {
	var ul = $('jav-ul-vote-option');
	var last = ul.getLast('li');
	var li_parent = $(obj.parentNode.parentNode);
	if (last == li_parent) {
		var li = li_parent.clone();
		li.inject(li_parent, 'after');
		li_parent.getElement('input.jav-input-value').set( {
			'name' :'votes_value[]'
		});
		li_parent.getElement('input.jav-input-title').set( {
			'name' :'votes_text[]'
		});

		li_parent.getElement('input.jav-input-description').set( {
			'name' :'votes_description[]'
		});

		li_parent.setStyle('opacity', '1');
		li_parent.getLast('span').setStyle('display', '');
		hiddenFirst();
	}

}
function hiddenFirst() {
	var length = $('jav-ul-vote-option').getChildren().length;
	if (length > 2) {
		$('jav-ul-vote-option').getFirst('li').getLast('span').setStyle(
				'display', '');
	} else {
		$('jav-ul-vote-option').getFirst('li').getLast('span').setStyle(
				'display', 'none');
	}
}
function checkDataVoiceType() {
	var values = $$('#jav-ul-vote-option input.jav-input-value');
	var result = true;
	for ( var i = 0; i < values.length - 1; i++) {
		if (!checkDataInt(values[i])) {
			values[i].set( {
				'styles' : {
					'border' :'1px solid red'
				}
			});
			result = false;
		}
	}
	var titles = $$('#jav-ul-vote-option input.jav-input-title');
	for ( var i = 0; i < titles.length - 1; i++) {
		if (!checkDataText(titles[i])) {
			titles[i].set( {
				'styles' : {
					'border' :'1px solid red'
				}
			});
			result = false;
		}
	}
	return result;
}
function checkDataInt(els, mess) {
	if (els.value == '' || isNaN(els.value)) {
		if (mess)
			alert(mess);
		return false;
	}
	return true;
}
function checkDataText(els, mess) {
	if (els.value == '') {
		if (mess)
			alert(mess);
		return false;
	}
	return true;
}
function displayTextArea(id, system) {
	var active = $$('#submenu-tmpl li');
	for ( var i = 0; i < active.length; i++) {
		active[i].removeClass('active');
	}
	$('submenu-li-' + id).addClass('active');

	var language = $$('#jav-language textarea');
	for ( var i = 0; i < language.length; i++) {
		language[i].setStyle('display', 'none');
		;
	}
	$('language_' + system + '_' + id).setStyle('display', '');
}

jQuery(document).ready( function($) {
	$('li').click( function() {
		var active = $$('li');
		for ( var i = 0; i < active.length; i++) {
			active[i].removeClass('focused');
		}
		this.addClass('focused');
	});
});

function catchTab(item, e) {
	if (navigator.userAgent.match("Gecko")) {
		c = e.which;
	} else {
		c = e.keyCode;
	}
	if (c == 9) {
		var offset = jQuery('#editFile').scrollTop();
		replaceSelection(item, String.fromCharCode(9));
		setTimeout("document.getElementById('" + item.id + "').focus();", 0);

		jQuery('#editFile').scrollTop(offset);
		offset = offset * -1;
		offset = '0 ' + offset + 'px';
		jQuery(e).css('background-position', offset);

		return false;
	}

}
function scrollEditor(e) {
	var offset = jQuery(e).scrollTop();
	offset = offset * -1;
	offset = '0 ' + offset + 'px';
	jQuery(e).css('background-position', offset);

}
function showColor(val) {
	jQuery('input[name=div-tranparent-box]').attr('checked', false);
	jQuery('#ja-class-css').attr( {
		'value' :val
	});
	jQuery('#div-class-css').css( {
		'background-color' :val
	});

}
function jaCreatFormColor(jatask, location, jaWidth, jaHeight, cid,textadd,textcancel,issave,class_css) {
	if(issave==1){
		class_css = jQuery('#class_css'+cid).css('background-color');
		if(class_css!='transparent'){
			if(class_css)class_css = rgb2hex(class_css);
		}else class_css =''; 
	}else{
		class_css = jQuery('#class_css').attr('value');
	}
	var url = siteurl + "&task=" + jatask + "&cid[]=" + cid;
	
	url += '&viewmenu=0';
	var obj = jQuery("#ja-wrap-color");
	if (obj)
		obj.remove();
	var content = jQuery('<div>').attr( {
		'id' :'ja-wrap-color'
	}).appendTo(document.body);
	var jacolor = jQuery('<div>').attr( {
		'id' :'ja-form-color',
		'style' :'top: 0px;'
	});

	jacolor.appendTo(content);

	jQuery('<button>').attr( {
		'id':'ja-color-save'
	}).html(textadd).appendTo(jQuery('#ja-wrap-color'));	
	
	jQuery("#ja-color-save").click( function() { submitColor(siteurl,cid,issave); } );
	
	jQuery('<button>').attr( {
		'id':'ja-color-cancel'
	}).html(textcancel).appendTo(jQuery('#ja-wrap-color'));
	
	jQuery("#ja-color-cancel").click( function() { cancelColor(); } );
	
	var pos = jQuery(location).offset();
	var topPos = pos.top  - jQuery(window).scrollTop()-50;
	var height = jQuery(window).height();
	var absTop = eval(eval(height) - eval(topPos) - eval(jaHeight))-60;

	if (absTop < 0) {
		topPos = pos.top - jaHeight - 60 - jQuery(window).scrollTop();
	}
	var leftPos = pos.left + 80 - jQuery(window).scrollLeft();

	jQuery("#ja-form-color").css( {
		'top' :topPos,
		'left' :leftPos + 15
	});
		
	jQuery("#ja-color-save").css( {
		'top' :topPos+jaHeight+10,
		'left' :leftPos+jaWidth -95,
		'height':0,
		'cursor': 'pointer'
	});	
	
	jQuery("#ja-color-cancel").css( {
		'top' :topPos+jaHeight+10,
		'left' :leftPos+jaWidth -45,
		'height':0,
		'cursor': 'pointer'
	});				
	var req = new Request.HTML({
      method :'get',
      url: url,	
      onComplete : function(text) {
			jQuery('#ja-form-color').html(text);
			loadComplete(topPos,leftPos,jaHeight,jaWidth,class_css);
	  }
    }).send();
	
	if (jQuery('#ja-form-color').get().length > 0)
		jQuery('#ja-form-color').animate( {
			top :topPos + "px",
			left :leftPos + "px",
			width :jaWidth + "px",
			height :jaHeight + "px"
		}, 200);	
	jQuery('#ja-form-color').fadeIn();

}
function loadComplete(topPos,leftPos,jaHeight,jaWidth,class_css){
	jQuery('#div-class-css').css('background-color',class_css);
	jQuery('#ja-class-css').attr('value',class_css);
	if (jQuery('#ja-color-save').get().length > 0)
		jQuery('#ja-color-save').animate( {
			top :topPos+jaHeight-10+ "px",
			left :leftPos +jaWidth -95+ "px",
			'height':20
		}, 200);
	jQuery('#ja-color-cancel').fadeIn();
	
	if (jQuery('#ja-color-cancel').get().length > 0)
		jQuery('#ja-color-cancel').animate( {
			top :topPos+jaHeight-10+ "px",
			left :leftPos +jaWidth -45+ "px",
			'height':20
		}, 200);
	jQuery('#ja-color-cancel').fadeIn();	
}
function cancelColor(){
	jQuery('#ja-wrap-color').fadeOut('fast', function() {
		jQuery(this).remove();
	});	
}
function submitColor(siteurl,cid,issave){
	var color = jQuery('#ja-class-css').attr('value');
	if(issave){
		color=color.replace("#","");
		url = siteurl+"&task=saveColor&cid[]="+cid+"&class_css="+color;
		
		var req = new Request.HTML({
		  method :'get',
		  url: url,	
		  onComplete : function(text) {
				cancelColor();
				if(color)color="#"+color;
				jQuery('#class_css'+cid).css('background-color',color);
		  }
		}).send();		
	}else{
		cancelColor();
		jQuery('#class_css').attr('value',color);
	}
}
function rgb2hex(color) {  
	rgb = color.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);  
	if(rgb){
		function hex(x) {  
		   hexDigits = new Array("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f");  
		   return isNaN(x) ? "00" : hexDigits[(x - x % 16) / 16] + hexDigits[x % 16];  
		}  
		return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
	}
	return color;
}  

function checkColor(id){
	var color = jQuery(id).attr('value');
	jQuery('#div-class-css').css('background-color',color);
}
function tranparentColor(id){
	if(jQuery("#div-tranparent-box").attr('checked'))
	jQuery(id).attr('value','');
}
function  moveContent(){
	var content =  tinyMCE.activeEditor.getContent();
	var id = jQuery('#editor').attr('value');
   jQuery(id).attr('value',content);
}

function changeDisplay(id, action, isSmiley){	
	if($(id) != undefined){
		$(id).style.display = action;
	}
	jQuery(document).ready( function($) {		
		if(action =="none"){
			$("#"+id).hide();
		}else{
			$("#"+id).show();
		}		
		//if click on smiley - save cursor in texarea
		if(isSmiley){
			if(id == "jav-smileys-"){
				jac_textarea_cursor = jQuery("#newVoiceContent")[0].selectionStart;
			}else{
				jac_textarea_cursor = jQuery("#newVoiceContentReply")[0].selectionStart;
			}					
		}
	});
}

function insertSmiley(which) {	
	text = document.getElementById("newVoiceContent").value;
	document.getElementById("newVoiceContent").value = text.substring(0, jac_textarea_cursor) + which + text.substring(jac_textarea_cursor, text.length);
	jac_textarea_cursor = jac_textarea_cursor + which.length;  
}

function insertSmileyReply(which) {
	text = document.getElementById("newVoiceContentReply").value;
	document.getElementById("newVoiceContentReply").value = text.substring(0, jac_textarea_cursor) + which + text.substring(jac_textarea_cursor, text.length);
	jac_textarea_cursor = jac_textarea_cursor + which.length;   
}

function openAttachFile(id){
	if(id !=0 || id != ""){
		//reply a new voice
		if($("jav-form-upload-reply").style.display == "none"){
			$("jav-form-upload-reply").style.display = "block";
			window.location.href="#jav-form-upload-reply";			
		}else
			$("jav-form-upload-reply").style.display = "none";
	}
	//add new or edit voice
	else{
		if($("jav-form-upload").style.display == "none"){
			$("jav-form-upload").style.display = "block";
			window.location.href="#jav-form-upload";			
		}else
			$("jav-form-upload").style.display = "none";
	}
}

function suggest(inputString){
	jQuery(document).ready( function($) {		
		if(inputString.length == 0) {
			$('#jav_tag_form #suggestions').fadeOut();			
		} else {
		$('#jav_tag_form #jav_input_tags').addClass('load');
			listSearchs = new Array();
			searchText = inputString;
			if(inputString.indexOf(jav_tag_config.characters_separating_tags) > 0){				
				listSearchs = inputString.split(jav_tag_config.characters_separating_tags);				
				searchText = listSearchs[listSearchs.length-1];				
			}else{				
				listSearchs[0] = inputString;			
			}			
			currenttext = "";
			if($("#jav_tags_list").html()){				
				$("#jav_tags_list .javtaglist").children().find(".javtagtext").each(function() {					
					for(i=0; i< listSearchs.length; i++){						
						tmphtml = $(this).html();						
						tmphtml = tmphtml.replace("&amp;", "&");
						if(listSearchs[i] == unescape($(this).html()) || listSearchs[i] == tmphtml) break;
					}					
					if(i == listSearchs.length){
						if(currenttext == ""){
							currenttext = $(this).html();
						}else{
							currenttext += jav_tag_config.characters_separating_tags+$(this).html();
						}
					};					
				});
			}
			clearTimeout(timeout);
			queryString = "queryString="+searchText+"&currenttext="+escape(currenttext);			
			timeout = setTimeout( function() {																																		
				jav_ajax = $.ajax({
				  type: 'POST',
				  url: "index.php?option=com_javoice&view=tags&layout=showlist&tmpl=component",
				  data: queryString,
				  success: function(data){				
						if(data.length >0) {
							$('#jav_tag_form #suggestions').fadeIn();					
							$('#jav_tag_form #suggestionsList').html(data);					
							$('#jav_tag_form #jav_input_tags').removeClass('load');
						}else{
							$('#jav_tag_form #suggestions').fadeOut();					
							$('#jav_tag_form #suggestionsList').html("");					
							$('#jav_tag_form #jav_input_tags').removeClass('load');
						}
				  }				  
				});
			}, 300);			
		}
	});
}

function fill(thisValue) {
	jQuery(document).ready( function($) {		
		if($('#jav_tag_form #jav_input_tags').val()){
			inputString = $('#jav_tag_form #jav_input_tags').val();
			if(inputString.indexOf(jav_tag_config.characters_separating_tags) > 0){
				listSearchs = inputString.split(jav_tag_config.characters_separating_tags);
				
				if(listSearchs[listSearchs.length-1]){					
					tmpText = parseInt(listSearchs[listSearchs.length-1].length,10);					
					inputString = inputString.substr(0,inputString.length-tmpText);					
				}	
				
				if(thisValue === undefined) thisValue = '';
				thisValue = inputString + thisValue;				
			}												
		}
		$('#jav_tag_form #jav_input_tags').val(thisValue);
		
		$('#jav_tag_form #suggestions').fadeOut();		
		//setTimeout("$('#jav_tag_form #suggestions').fadeOut();", 600);
	});
}

function javRemoveTag(obj){	
	jQuery(obj).remove();
}

function isExistTag(tagName){
	jQuery(function($) {
		if($(".javtaglist").length <=0){
			return false;
		}
		$("#jav_tags_list .javtaglist").children().children(".javtagtext").each(function() {
											
		});
	});	
}

function javAddTags(){	
	jQuery(function($) {		
		inputString = $('#jav_tag_form #jav_input_tags').val();
		//remove text already exits
		// listStr = inputString.split(",");
		// if(listStr.length > 0){
			
		// }else{
			// if($(".javtaglist").length <=0){
				
			// }
		// }
		if(!inputString) return;
		$("#jav_add_tag_button").attr('disabled', 'disabled');
		addText = "taglist="+escape(inputString);		
		$.post("index.php?option=com_javoice&view=tags&task=addnew&tmpl=component", addText, function(response) {
			$("#jav_add_tag_button").removeAttr('disabled');

			var itemLists = new Array();
			var itemlist = null;
			$.each(response, function(i, item) {						
				var type = item.type;
				var value = item.id;
				itemlist = {
					id:value,
					name:type
				};				
				itemLists[i] = itemlist;
			});			
			
		    if($(".javtaglist").length <=0){			    	
		    	$("#jav_tags_list").html('<ul class="javtaglist"></ul>');
		    }
					
			for(i = 0; i< itemLists.length; i++){
				check = 1;
				if(itemLists[i].name=="-javmsg-"){
					alert(itemLists[i].id);
					continue;
				}
				$("#jav_tags_list .javtaglist").children().children(".javtagid").each(function() {
					if($(this).val() == itemLists[i].id) {
						alert("The tag name "+itemLists[i].name+" already exists.");
						check = 0;
					}								
				});
				if(check == 1){					
					text = '<li id="jav_tag_'+itemLists[i].id+'" class="javtag" onclick="javRemoveTag(this)"><input class="javtagid" type="hidden" name="javtag[]" value="'+itemLists[i].id+'"/><span class="javtagtext">'+ itemLists[i].name +'</span></li>';			
					$("#jav_tags_list .javtaglist").append(text);
				}
				if($("#jav_tags_list .javtaglist").children().children(".javtagid").length>=jav_tag_config.tag_maximum_per_thread){
					$("#jav_input_tags").attr('disabled', 'disabled');
					break;					
				}
			}
			$('#jav_tag_form #jav_input_tags').val("");						
		}, 'json');				
	});
}
function submitTags(){	
	jQuery(function($) {
		text = jQuery('#iContent').contents().find('input#name');
		if(text.val() == ""){
			alert("You must input tag's name");
			return;
		}		
		$('#javoice-wait').css( {
			'display' :''
		});
		$("#iContent").contents().find("#adminForm").find("#task").val("save");		
		$.get("index.php?tmpl=component", $("#iContent").contents().find(
				"#adminForm").serialize(), function(res) {
			//jaFormHideIFrame();
			parseData_admin(res);
		}, 'json');
	});
}