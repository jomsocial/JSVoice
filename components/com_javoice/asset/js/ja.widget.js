// JavaScript Document
var JAV_Widget = {};
var jav_time_jquery = '';
JAV_Widget.show = function (arrObj) {
	//jav_createFeedbackButton(arrObj);
	/*include CSS file*/	
	window.onload = function() {
		if(typeof arrObj.type=='undefined'){
			jav_includeCss(arrObj.url + 'components/com_javoice/asset/css/ja.widget.css');	
			if (typeof jQuery == 'undefined') {		
				// jQuery is not loaded  
				jav_includeJs(arrObj.url + 'components/com_javoice/asset/js/jquery-1.4.2.js');	
				
				jav_jQueryCheck(arrObj);	
				//jav_loadWidget(arrObj);
				
			} else {
				// jQuery is loaded
				jav_loadWidget(arrObj);
			}
		}
		else{
			jav_includeJs_complete(arrObj.url + 'components/com_javoice/asset/js/jquery-1.4.2.js',arrObj);
			
		}		
	}
}

function jav_includeJs_complete(file,arrObj) {
	var js;
    var html_doc = document.getElementsByTagName('head')[0];
    js = document.createElement('script');
    js.setAttribute('type', 'text/javascript');
    js.setAttribute('src', file);
    html_doc.appendChild(js);

    js.onload = function () {
    	jav_loadWidgetIframe(arrObj);
    }
    return false;
}
function jav_loadWidgetIframe(arrObj){
	if(typeof arrObj=='undefined' || typeof arrObj.voicetypes=='undefined' || typeof arrObj.forums=='undefined')return false;
	var textsrc =arrObj.url + 'index.php?option=com_javoice&view=items&layout=widget&voicetypes='+ arrObj.voicetypes +'&forums='+ arrObj.forums ;
	if(typeof arrObj.number_voices=='undefined')arrObj.number_voices=10;
	textsrc = textsrc + '&number_voices='+ arrObj.number_voices +'&tmpl=component';
	if(typeof arrObj.status!='undefined')textsrc = textsrc + '&status='+arrObj.status;
	if(typeof arrObj.creator!='undefined')textsrc = textsrc + '&creator='+arrObj.creator;
	if(typeof arrObj.created_before!='undefined')textsrc = textsrc + '&created_before='+arrObj.created_before;
	if(typeof arrObj.created_after!='undefined')textsrc = textsrc + '&created_after='+arrObj.created_after;
	if(typeof arrObj.link_target!='undefined')textsrc = textsrc + '&link_target='+arrObj.link_target;
	if(typeof arrObj.view_all_button!='undefined')textsrc = textsrc + '&view_all_button='+arrObj.view_all_button;
	var height = '400px';
	if(typeof arrObj.height!='undefined')height = arrObj.height+'px';
	var width = '100%';
	if(typeof arrObj.width!='undefined')width = arrObj.width+'px';	
	jQuery('<iframe>').attr( {
		'id' :'iContent',
		'src' :textsrc,
		'width':width,
		'height':height,
		'style':'border:none;overflow-x:hidden;'
	}).appendTo(jQuery('#jav_widget_content'));
	//jQuery("#iContent").load( function() { loadIFrameComplete(); } );	
}

function loadIFrameComplete(){
}
function jav_jQueryCheck(arrObj) {	
	if(typeof jQuery == 'undefined') {		
		jav_time_jquery = setTimeout(function() { jav_jQueryCheck(arrObj) }, 1000);
	} else {		
		clearTimeout(jav_time_jquery);
		jav_loadWidget(arrObj);		
	}
}

function jav_includeCss(cssFile)
{
	//  document.writeln('<link rel="stylesheet"  type="text/css" href="' +  cssFile +'"/>');
	var head = document.getElementsByTagName('head')[0];
	var css = document.createElement('link');
	css.type = 'text/css';
	css.rel = 'stylesheet';
	css.href = cssFile;
	head.appendChild (css);
}

function jav_includeJs(jsFile)
{
	//  document.write('<script type="text/javascript" src="'
	//    + jsFile + '"></scr' + 'ipt>'); 
	var head = document.getElementsByTagName('head')[0];
	var js = document.createElement('script');
	js.type = 'text/javascript';
	js.src = jsFile;
	head.appendChild (js);
}

function jav_loadWidget(arrObj) {
	jQuery.noConflict();
	(function($) {
		
			/*var defaults = $.extend({
					key: 'javoice',
					host: '10.0.0.246',
					forum: 'general',
					alignment: 'left',
					background_color: '#cccccc',
					hover_color: '#ff0000',
					lang: 'en',
					feedback_url : 'url',
					width : '300',
					height : '300'
			}, options || {});*/
			
			var options = $.extend(arrObj, options);
			var height_div = '';
			
			var isLoaded = 0;
			var isClicked = 0;
			/*Check data*/
			if (options.height == 0 || options.height == 'undefined') {
				options.height = 600;
			} 			
				
			var jav_feedback_ajax = '';				
			var obj_body = $('body');		
			var overlay_feedback = $('<div>')
									.css({'display' : 'none',  'background-color' : '#000000', 'opacity' : '0.7', 'z-index' : 1000})
									.attr({'class' : 'jav-feedback_overlay', 'id' : 'jav-feedback_overlay'})								
									.click(function () {
										jav_closeDialog();
									})
									.appendTo(obj_body);
									
			var div_feedback = $('<div>')
									.css({'display' : 'none', 'overflow' : 'hidden', 'width' : (options.width) + 'px' ,'padding-left':'10px', 'z-index' : 1001})
									.attr({'class' : 'jav-feedback_dialog', 'id' : 'jav-feedback_dialog'})					
									.click(function () {
										//jav_closeDialog();
									})
									.appendTo(obj_body);
			var div_close_feedback = $('<div>')
									.css({'display' : 'block','right':'-5px ', 'width' : (options.width) + 'px' ,'height':'10px', 'z-index' : 1001})
									.attr({'class' : 'jav-close-feedback', 'id' : 'jav_close_feedback'})					
									.click(function () {
										//jav_closeDialog();
									})
									.appendTo(div_feedback);	
			var close_feedback = $('<a>')
									.css({'position' : 'absolute', 'top' : '0', 'right' : '0'})
									.attr({'href' : 'javascript: void(0)' ,  'class' : 'jav-feedback_close', 'id' : 'jav-feedback_close'})									
									.html('Close')									
									.click(function() {
										jav_closeDialog();
									}).appendTo(div_close_feedback);
			var height = options.height;
			if(height>400)height=400;
			
			var iframe_feedback = $('<iframe>')
									.css({'width' : options.width - 10 + 'px', 'height' : height+'px', 'overflow-x': 'hidden', 'display' : 'block', 'background-color' : '#DCD8D7'})
									.attr({'class' : 'jav-feedback_iframe', 'id' : 'jav-feedback_iframe' , 'allowTransparency' : 'true' , 'frameBorder' : '0'})										
									.appendTo(div_feedback);

				iframe_feedback.load(function() {
										if (isLoaded == 1 ) {
											jav_showDivCenter(options.width, 'jav-feedback_dialog');
											$('#jav-feedback_overlay').show(); 
											//$('#jav-feedback_dialog').show(); 										
											isLoaded = 0;
											
											/*Auto resize image*/
											//$(this).css('height' , '600px');
											
											//var iFrame_dialog = $("#jav-feedback_iframe");
											//var innerDoc = (iFrame_dialog.get(0).contentDocument) ? iFrame_dialog.get(0).contentDocument : iFrame_dialog.get(0).contentWindow.document;
											//alert(innerDoc);
											//iFrame_dialog.height(innerDoc.body.scrollHeight + 35);

											/*var iframe_dialog = document.getElementById('jav-feedback_iframe');
											if(iframe_dialog.contentDocument){
												iframe_dialog.style.height = iframe_dialog.contentDocument.body.offsetHeight + 35 + 'px';
											} else {
												iframe_dialog.style.height = iframe_dialog.contentWindow.document.body.scrollHeight + 'px';
											}*/


										}
									});	
				windowWidth  = GetWidth();
				
				javCopyrightWidth  = (windowWidth - options.width)/2 + (options.width-130);

				var footer_feedback = $('<a>')
				.css({'position' : 'absolute', 'left' : javCopyrightWidth+'px','top':options.height-150+'px'})
				.attr({'href' : 'http://javoice.joomlart.com','target':'_blank' , 'class' : 'jav-copyright', 'id' : 'jav_copyright'})
				.appendTo(overlay_feedback);				

			if (options.mode_display == '' || options.mode_display == 'undefined') {
				cls_feedback = 'jav-feedback-image1';
			} else {
				cls_feedback = 'jav-feedback-' + options.mode_display;
			}
			
			
			/*Check the mode display*/
			var jav_feedback_title = '&nbsp;';
			
			if (options.mode_display == 'text') {
				jav_feedback_title = options.feedback_title;
			}
			
			var button_feedback = $('<a>')
									.css({'z-index' : 1002})
									.attr({'href' : 'javascript: void(0)' ,  'class' : cls_feedback , 'id' : 'jav-btfeedback'})									
									.html(jav_feedback_title)
									/*.hover(function() {
										$(this).css('background-color' , options.hover_color);
									}, function() {
										$(this).css('background-color' , options.background_color);
									})*/
									.click(function() {		
										if (isClicked == 0) {											
											$('#jav-feedback_iframe').attr('src', options.url + 'index.php?option=com_javoice&view=items&layout=widget&voicetypes='+ options.voicetypes +'&forums='+ options.forums + '&number_voices='+ options.number_voices +'&tmpl=component');										
											//jav_isLoaded();
											isLoaded = 1;
											isClicked = 1;
										} else {
											$('#jav-feedback_overlay').show(); 
											//$('#jav-feedback_dialog').show(); 	
											jav_showDivCenter(options.width, 'jav-feedback_dialog');
										}
										
									}).appendTo(obj_body);
									
				
				
				
				if (typeof(options.alignment) == 'undefined' ) {
					options.alignment = 'left';
				}
				
				button_feedback.css(options.alignment, 0);
										
			var jav_closeDialog = function() {		
				if (jav_feedback_ajax) {
					jav_feedback_ajax.abort();
				}
				//$('#jav-feedback_iframe').attr('src', '');
				$('#jav-feedback_dialog').hide();
				$('#jav-feedback_overlay').hide();		
			};	
			
						
			var jav_showDivCenter = function (xwidth ,divid) {			
				var scrolledX, scrolledY;
				if( self.pageYoffset ) {
					scrolledX = self.pageXoffset;
					scrolledY = self.pageYoffset;
				} else if( document.documentElement && document.documentElement.scrollTop ) {
					scrolledX = document.documentElement.scrollLeft;
					scrolledY = document.documentElement.scrollTop;
				} else if( document.body ) {
					scrolledX = document.body.scrollLeft;
					scrolledY = document.body.scrollTop;
				}
				
				// Next, determine the coordinates of the center of browser's window
				
				var centerX, centerY;
				  if( typeof( window.innerWidth ) == 'number' ) {
					//Non-IE
					centerX = window.innerWidth;
					centerY = window.innerHeight;
				  } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
					//IE 6+ in 'standards compliant mode'
					centerX = document.documentElement.clientWidth;
					centerY = document.documentElement.clientHeight;
				  } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
					//IE 4 compatible
					centerX = document.body.clientWidth;
					centerY = document.body.clientHeight;
				  }
				
				
				// Xwidth is the width of the div, Yheight is the height of the
				// div passed as arguments to the function:
				var leftoffset = scrolledX + (centerX - xwidth) / 2;
				//var topoffset = scrolledY + (centerY - yheight) / 2;
				
				
				// the initial width and height of the div can be set in the
				// style sheet with display:none; divid is passed as an argument to // the function
				var obj = $('#' + divid);
				//obj.css('position', 'absolute');
				
				//if (obj.css('top') && topoffset) {			
					//obj.css('top', parseInt(topoffset) + 'px');
				//}
				
				if (obj.css('left') && leftoffset) {
					obj.css('left', parseInt(leftoffset) + 'px');
				}
				
				obj.css('display', 'block');		
			}
	})(jQuery);

}

function GetWidth(){
        var x = 0;
        if (self.innerHeight){
                x = self.innerWidth;
        }
        else if (document.documentElement && document.documentElement.clientHeight){
                x = document.documentElement.clientWidth;
        }
        else if (document.body){
                x = document.body.clientWidth;
        }
        return x;
}


function GetHeight(){
        var y = 0;
        if (self.innerHeight){
			y = self.innerHeight;
        }
        else if (document.documentElement && document.documentElement.clientHeight){
        	y = document.documentElement.clientHeight;
        }
        else if (document.body){
         	y = document.body.clientHeight;
        }
        return y;
}

