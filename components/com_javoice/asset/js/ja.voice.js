// JavaScript Document
jQuery.noConflict();

var jav_activePopIn = 0;
var jav_idActive = '';
var jav_vote_selected = new Array();
var timeout = '';
var jav_ajax = '';
var jav_header = 'ja-header';

window.addEvent('domready', function (){
	
	if($('jav-msg-succesfull') != undefined){		
		// obj = $('jav-msg-succesfull');
		// $('jav-msg-succesfull').destroy();		
		// obj.injectInside($(window.document.body));		
		$('jav-msg-succesfull').inject($(window.document.body));		
	}	
});

function jav_init() {
	jQuery(document).ready(
			function($) {
				$(this).click( function() {
					if (jav_idActive != '' && jav_activePopIn == 1) {
						$(jav_idActive).removeClass('jav-active');
						jav_activePopIn = 0;
					}
					jav_activePopIn = 1;
				});				
			});
}

function jav_ajax_load(url, type_id) {
	jav_displayLoadingSpan();
	if(type_id){
		jav_option_type = type_id;
	}
	jQuery(document).ready( function($) {	
		jav_ajax = $.getJSON(url, function(res) {
			jav_parseData(res);
		});
	});

}
function jav_ajax_update(url) {
	jQuery(document).ready( function($) {
		jav_ajax = $.getJSON(url, function(res) {
		});
	});
}
function jav_ajax_load_vote(url) {
	jav_displayLoadingSpan();	
	jQuery(document).ready(
			function($) {
				jav_ajax = $.getJSON(url, function(res) {
					jav_parseData(res);
					jav_vote_total = parseInt($('#votes-left-' + jav_option_type).attr('value').trim());
					if(jav_vote_total==-1) jav_vote_total = 1000;
					if (jav_vote_total == 0) {
						checkTypeOfTooltip('#jav-dialog', jav_option_type, 400, 'auto', 3000);
					}
				});
			});
}

function checkTypeOfTooltip(divId, type, width, height, time_delay) {
	jQuery(document).ready( function($) {
		$(divId).css( {
			'width' :width,
			'height' :height
		});
		switch (type) {
		case 'none':
			$(divId).hide('fast');
			break;
		case 'auto_hide':
			$(divId).show('slow');
			timeout = ( function() {
				$(divId).hide('slow');
			}).delay(time_delay);

			$(divId).hover( function() {
				clearTimeout (timeout);
			}, function() {
				timeout = ( function() {
					$(divId).hide('slow');
				}).delay(time_delay);
			});
			break;
		case 'normal':
		default:
			$(divId).show('slow');
		}
	});
}

function jav_parseData(response) {	
	jQuery(document).ready( function($) {
		if($('#loader')) {
			id='#'+jav_header;
			$(id).css('z-index','10');			
			$('#loader').hide();
		}	
		var result = true;
		var myResponse = null;
		if(response.data){			
			myResponse = response.data;			
		}else{			
			myResponse = response;		
		}
		$.each(myResponse, function(i, item) {
			var divId = item.id;
			if(divId == "#err_invalidjavTextCaptcha"){
				result = false;
			}
			var type = item.attr;
			var content = item.content;
			//alert("ID: " + divId + " \nTYPE: " + type+ " \ncontent: " + content);
			if(divId == "callFuntion" || divId == "assignValue"){				
				if(type=="jav_process_ajax"){										
					jav_process_ajax = 0;
					//queueListenerScroll();
				}
			}else{
				if ($(divId) != "undefined") {
					if(divId == 'jav_error_checkphp'){
						if(content ==1){
							return false;
						}
					}			
					if (type == 'html') {
						$(divId).html(content);
						if(divId == "#err_invalidjavTextCaptcha"){
							$(divId).show();
							$("#javTextCaptcha").val("");
							loadNewCaptcha(0);
							$("#javTextCaptcha").focus();
						}
					} else if (type == 'class') {
						$(divId).attr('class', '');
						$(divId).addClass(content);
					}
					else if (type == 'css') {
						var arr = content.split(',');
						$(divId).css(arr[0], arr[1]);
					}
					else if(type=='reload'){
						content = content.replace('&amp;', '&');
						location.href = content;
					}
					else if(type=='timeout'){
						var arrtimeout = content.split(',');
						window.setTimeout(function() {
							if(arrtimeout[1] == 'hide'){
								$(divId).css('display','none');
							}else{
								$(divId).css('display','block');
							}
						}, arrtimeout[0]);
					}
					else if(type=='append'){
						$(divId).append(content);
					}else{	
						$(divId).attr(type, content);
					}				
				}
			}
				
		});
		if(result)jaFormHide();
	});
}

function jav_showDiv(divId) {
	jQuery(document).ready( function($) {
		var objDiv = $(divId);
		var clsDiv = objDiv.attr('class');

		jav_idActive = divId;

		if (clsDiv != "undefined") {
			var mainClass = clsDiv.split(' ');
			$('.' + mainClass[0]).removeClass('jav-active');
		}
		var $chk = function(obj){
		    return !!(obj || obj === 0);
		};
		if ($chk(objDiv)) {
			if (clsDiv != "undefined" && clsDiv.indexOf('jav-active') != -1) {
				objDiv.removeClass('jav-active');
			} else {
				objDiv.addClass('jav-active');
			}
		}

		jav_activePopIn = 0;
	});
}

function jav_hideDiv(divId) {
	jQuery(document).ready( function($) {
		var objDiv = $(divId);
		
		var $chk = function(obj){
		    return !!(obj || obj === 0);
		};
		if ($chk(objDiv)) {
			objDiv.removeClass('jav-active');
		}

		jav_idActive = '';
		jav_activePopIn = 0;
	});
}

function jav_createTabs(tabId, href_url) {	
	//restore value of from reply
	cancel_frm_response();
	jQuery(document)
			.ready( function($) {
				// When page loads...					
					$(".javtabs-panel").hide(); // Hide all content
					$("ul.javtabs-title li:eq("+ jav_tab_active +")").addClass("active").show(); // Activate
					
					// first
					// tab
					$("ul.javtabs-title li:eq("+ jav_tab_active +")").addClass('loaded');
					$(".javtabs-panel:eq("+ jav_tab_active +")").show(); // Show first tab
														// content
					// On Click Event
					$("ul.javtabs-title li")
							.click( function(item) {						
									var activeTab = '#' + $(this).find("a").attr("class"); // Find the href									
										// attribute value
									// to identify the active tab +
									// content

									//$(activeTab).show(); // Fade in the
															// active ID
									// content

									var clicked = $(this).attr('class');
									var obj = $(this);

									clstype_id = $(this).attr('id');
									
									if (clstype_id != "undefined"  && clstype_id!='') {
										clstype = clstype_id.split('_');
										type_id = parseInt(clstype[1]);	
									}
									var href = href_url + type_id;
									if(typeof jav_current_tag != "undefined" ){
										href = href + "&tagid=" + jav_current_tag;
									}									
									if (clicked != "undefined" && clicked.indexOf('loaded') == -1) {
										jav_displayLoadingSpan();										
										jav_ajax = $.getJSON(href, 
													function(res) {
														jav_parseData(res);														
														
														if (clstype_id != "undefined"  && clstype_id!='') {
															clstype = clstype_id.split('_');
															type_id = parseInt(clstype[1]);	
															jav_option_type = type_id;
															var jav_pathway = $('#jav-pathway-' + type_id);							
															if (jav_pathway) {
																$('.jav-pathway-main').hide();
																jav_pathway.show();	
																
															}
														}

														$("ul.javtabs-title li").removeClass("active"); // Remove any "active" class														
														// class to selected tab
														$(".javtabs-panel").hide(); // Hide all tab
																					// content
														
														$(activeTab).show(); 
														obj.addClass("active"); // Add "active"

													});
													$(this).addClass('loaded');
									} else {
										
										if (clstype_id != "undefined"  && clstype_id!='') {
											clstype = clstype_id.split('_');
											type_id = parseInt(clstype[1]);	
											jav_option_type = type_id;
											var jav_pathway = $('#jav-pathway-' + type_id);							
											if (jav_pathway) {
												$('.jav-pathway-main').hide();
												jav_pathway.show();	
											}
										}
										
										$("ul.javtabs-title li").removeClass("active"); // Remove any "active" class
										// class to selected tab
										$(".javtabs-panel").hide(); // Hide all tab content
										$(activeTab).show(); 
										$(this).addClass("active"); // Add "active"
									}
									return false;
								});
				});
}


function jav_createTabs_users(tabId) {
	//restore value of from reply
	cancel_frm_response();
	jQuery(document)
			.ready( function($) {
				// When page loads...					
					$(".javtabs-panel").hide(); // Hide all content
					$("ul.javtabs-title li:eq("+ jav_tab_active +")").addClass("active").show(); // Activate
					
					// first
					// tab
					$("ul.javtabs-title li:eq("+ jav_tab_active +")").addClass('loaded');
					$(".javtabs-panel:eq("+ jav_tab_active +")").show(); // Show first tab
						
					// On Click Event
					$("ul.javtabs-title li")
							.click( function(item) {						
									var activeTab = '#' + $(this).find("a").attr("class"); // Find the href
																		
									var clicked = $(this).attr('class');
									var obj = $(this);

									clstype_id = $(this).attr('id');
									
									if (clstype_id != "undefined"  && clstype_id!='') {
										clstype = clstype_id.split('_');
										type_id = trim(clstype[1]);	
									}
										
									if (clicked != "undefined" && clicked.indexOf('loaded') == -1) {
										jav_displayLoadingSpan();
										var href = 'index.php?option=com_javoice&view=users&&limitstart=0&tmpl=component&layout=' + type_id + '&uid='+uid + '&Itemid=' + Itemid;
										jav_ajax = $.getJSON(href, 
													function(res) {
														jav_parseData(res);														
														
														$("ul.javtabs-title li").removeClass("active"); // Remove any "active" class														
														// class to selected tab
														$(".javtabs-panel").hide(); // Hide all tab
																					// content
														
														$(activeTab).show(); 
														obj.addClass("active"); // Add "active"

													});
													$(this).addClass('loaded');
									} else {										
										$("ul.javtabs-title li").removeClass("active"); // Remove any "active" class
										// class to selected tab
										$(".javtabs-panel").hide(); // Hide all tab content
										$(activeTab).show(); 
										$(this).addClass("active"); // Add "active"
									}
									return false;
								});
				});
}

function jav_showVoteOption(divId, vote_value, vote_text, vote_description, vote_msg, cid, type_id, mode, forums_id, Itemid) {
	
	jQuery(document)
			.ready(
					function($) {
						jav_vote_total = $('#votes-left-' + type_id).attr('value');
						if(jav_vote_total==-1) jav_vote_total = 1000;
						
						var objVote = $(divId);

						var vote_select = $('#jav-item-votes-'+cid).html().trim();

						if (vote_select!=null && isNaN(vote_select)) {
							vote_select = 0;
						} else {
							vote_select = parseInt(vote_select);
						}

						/* Empty html before assign other vote */
						objVote.empty();

						/* Check value */
						if (vote_value == '' || vote_text == '') {
							/* Display div */
							objVote.html(vote_msg);
							jav_showDiv(divId);
							return;
						}
						
						var arrVoteValue = vote_value.split(',');
						var arrVoteText = vote_text.split(',');						
						var arrVoteDesc = new Array(arrVoteText.length);
						if(vote_description!='') arrVoteDesc = vote_description.split(',');

						/* render html for this div */
						var ptag = $('<p>').html(jav_many_vote).appendTo(
								objVote);
						var olTag = $('<ol>').appendTo(objVote);
						
						for (var i = 0 ; i < arrVoteValue.length; i++) {
							var liTag = $('<li>').appendTo(olTag);
							var vote_select_temp = vote_select;
							if(vote_select_temp=='') vote_select_temp = 0;
							//alert(   eval(vote_select_temp ) );
							
							if ( jav_vote_total	- ( Math.abs( eval(arrVoteValue[i]) ) - Math.abs( eval(vote_select_temp ) ) ) >= 0 ) {
									
								var aTag = $('<a>').addClass('jav-spend-' + arrVoteValue[i]).attr({id:arrVoteValue[i], title:arrVoteDesc[i]})
											.click(
												function() {
													/* Check if it selected */
													if (vote_select != ''
															&& vote_select == arrVoteValue[i]) {														
														return false;
													} else {
														var link = jav_base_url
																	+ '?option=com_javoice&view=items&layout=item&task=vote&cid='
																	+ cid
																	+ '&votes='
																	+ $(this).attr(
																			'id')
																	+ '&tmpl=component&type='
																	+ type_id
																	+ '&forums=' + forums_id
																	+ '&Itemid='+ Itemid;
														
														if(mode){
															var link = link + '&mode=true'; 
														}
														
														jav_ajax_load_vote(link);
													}

												}).html(arrVoteText[i])
										.hover(
											   function() {	
											   	if ($(this).attr('title') != '' ) {
												   $(objVote.find('p')).html($(this).attr('title'));
												   $(this).attr('title', '');
												}
											   },
											   function() {
												   	$(this).attr('title', $(objVote.find('p')).html());
												    $(objVote.find('p')).html(jav_many_vote);
											   }
											   ).appendTo(liTag);
								
								if (vote_select != ''	&& vote_select == arrVoteValue[i]) {
									$(aTag).addClass('jav-selected');
								}
								

							} else {
								var aTag = $('<a>').addClass(
										'jav-spend-' + arrVoteValue[i]).attr(
										'id', arrVoteValue[i]).click(
										function() {
											return false;
										})
										.hover(
											   function() {										   		
												   $(objVote.find('p')).html(jav_run_out);												   
											   },
											   function() {												   	
												    //$(objVote.find('p')).html(jav_many_vote);
											   }
										)
										.html(arrVoteText[i]).appendTo(liTag);

								$(aTag).addClass('jav-disable');

								if (vote_select != ''
										&& vote_select == arrVoteValue[i]) {
									$(aTag).addClass('jav-selected');
								}
							}
						}

						/* Display div */
						jav_showDiv(divId);
					});
}

function encode(string) {
	string = string.replace(/\r\n/g,"\n");
	var utftext = "";

	for (var n = 0; n < string.length; n++) {

		var c = string.charCodeAt(n);

		if (c < 128) {
			utftext += String.fromCharCode(c);
		}
		else if((c > 127) && (c < 2048)) {
			utftext += String.fromCharCode((c >> 6) | 192);
			utftext += String.fromCharCode((c & 63) | 128);
		}
		else {
			utftext += String.fromCharCode((c >> 12) | 224);
			utftext += String.fromCharCode(((c >> 6) & 63) | 128);
			utftext += String.fromCharCode((c & 63) | 128);
		}

	}

	return utftext;
}

function jav_findWord(e, obj, url, type_id, time) {

	jQuery(document)
			.ready(
					function($) {
						$('#key-' + type_id).removeClass('input_error');
						var strurl = url;
						keysearch =  encodeURIComponent(obj.value);
						var len = obj.value.length;
						
						if(typeof jav_minimum_search  == 'undefined'){
							jav_minimum_search= 4;	
						}
						if (keysearch && len < jav_minimum_search	&& len > 0)
							return;
						if (keysearch && keysearch.length > 2) {
							var strurl = strurl + '&key=' +keysearch;
						}
						if(typeof jav_current_tag!='undefined' && jav_current_tag!=0) strurl = strurl + '&tagid=' +keysearch;
						
						clearTimeout(timeout);
						
						var intKey = (window.Event) ? e.which : e.keyCode;
						if (intKey == 13) { //enter key
							
							jav_ajax = $.getJSON(
											strurl,
											function(res) {												
												jav_parseData(res);
												$('#jav-mainbox-' + type_id + ' .search-loading').hide();
												if (keysearch && keysearch.length > 2) {
													$('#jav-mainbox-' + type_id + ' .jav-search-result').show();
													$('#jav-mainbox-' + type_id + ' .jav-list-options').hide();
												} else {
													if(keysearch.length > 0){
														$('#jav-mainbox-' + type_id + ' .jav-search-result').hide();
														$('#jav-mainbox-' + type_id + ' .jav-list-options').show();
													}else{
														$('#jav-mainbox-'+type_id+' .jav-search-result .jav-total-matches').hide();
													}
												}
											});
							if (keysearch && keysearch.length > 2 || keysearch==0) {
								$('#jav-mainbox-' + type_id + ' .search-loading').show();
							}
							
							return false;
						}
						
						if(time=='undefined' || time==null) time = 700;
												
						timeout = setTimeout( function() {											
							jav_ajax = $.getJSON(
											strurl,
											function(res) {												
												jav_parseData(res);
												$('#jav-mainbox-' + type_id + ' .search-loading').hide();
												if (keysearch && keysearch.length > 2) {
													$('#jav-mainbox-' + type_id + ' .jav-search-result').show();
													$('#jav-mainbox-' + type_id + ' .jav-list-options').hide();
												} else {
													if(keysearch.length > 0){
														$('#jav-mainbox-' + type_id + ' .jav-search-result').hide();
														$('#jav-mainbox-' + type_id + ' .jav-list-options').show();
													}else{
														$('#jav-mainbox-'+type_id+' .jav-search-result .jav-total-matches').hide();
													}
													
												}
											});
							if (keysearch && keysearch.length > 2) {
								$('#jav-mainbox-' + type_id + ' .search-loading').show();
							}
							
						}, time);
								
						return false;
					});
}

function jav_isEmail(string) {
	return (string.search(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,5}|[0-9]{1,3})(\]?)$/) != -1);
}

function jav_reset(id) {
	$(id).value = '';
}

function validation(){
	var check = true;
	jQuery(document).ready( function($) {
		isFocus = 0;
		
		if($('#title')=='undefine' || trim($('#title').attr('value'))==''){
			$('#title-msg').show();
			$('#title').focus();
			isFocus = 1;
			check = false;
		}else{
			$('#title-msg').hide();
		}
		if($('#forums_id')=='undefine' || $('#forums_id').attr('value')=='' || $('#forums_id').attr('value')==0){
			$('#forums_id-msg').show();
			if(isFocus == 0){
				$('#forums_id').focus();
				isFocus = 1;
			}
			check = false;
		}else{
			$('#forums_id-msg').hide();
		}
		if(typeof jav_minimum_search  == 'undefined'){
			jav_minimum_search= 4;	
		}
		if($('#title').length >0 && trim($('#title').attr('value')) != "" && trim($('#title').attr('value')).length < jav_minimum_search ){
			$('#error_javTitle').show();		
			if(isFocus == 0){
				$('#title').focus();
				isFocus = 1;
			}
			check = false;
		}else{
			$('#error_javTitle').hide();
		}
		
		if($('#javGuestName').length >0 && $('#javGuestName').attr('value')==''){
			$('#err_javGuestName').show();		
			if(isFocus == 0){
				$('#javGuestName').focus();
				isFocus = 1;
			}
			check = false;
		}else{
			$('#err_javGuestName').hide();
		}
		
		if($('#newVoiceContent').length >0){
			if($("#newVoiceContent")[0].tagName != "TEXTAREA"){
				$("#err_exitchekspelling").show();
				alert($("#err_exitchekspelling").html());
				isFocus == 1;
				check = false;				
			}else{
				$("#err_exitchekspelling").hide();
			}
		}
		
		if($('#javGuestEmail').length >0){			
			if($('#javGuestEmail').attr('value')==''){								
				$('#err_javGuestEmail').show();
				if(isFocus == 0){
					$('#javGuestEmail').focus();
					isFocus = 1;
				}
				check = false;
			}else{								
				$('#err_javGuestEmail').hide();
				var filter = /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;
				
				if (!filter.test($('#javGuestEmail').attr('value'))) {					
					$('#err_javGuestEmailInvalid').show();
					if(isFocus == 0){
						$('#javGuestEmail').focus();
						isFocus = 1;
					}									
					check = false;					
				}else{
					$('#err_javGuestEmailInvalid').hide();
				}
			}			
		}		
		
		if($("#javTextCaptcha").length >0){			
			$("#err_invalidjavTextCaptcha").hide();
			if($("#javTextCaptcha").attr('value')==""){
				if(isFocus == 0){
					$("#javTextCaptcha").focus();
				}				
				$("#err_javTextCaptcha").show();
				check = false;
			}else{
				$("#err_javTextCaptcha").hide();
			}
		}
	});	
	
	return check;
}

function jav_submitbutton() {
	if(!validation()) return false;	
		
	jQuery(document).ready( function($) {
		var vars = $("#new_item").serialize();
		$.get("index.php", vars, function(response) {
			jav_parseData(response);			
		}, 'json');
	});
}

function jav_addClass(divId, obj , className) {
	jQuery(document).ready( function($) {
		
		$(divId).find('.' + className).removeClass(className);
		$(obj).parent().addClass(className);
	});
}

//ajax pagination
function jav_ajaxPagination(url,divid) {
	//restore value of from reply
	cancel_frm_response();
	url = url + '&type=' + jav_option_type;	
	
	var vars = url.split('&');
	for (var i=0; i<vars.length; i++){
		if( vars[i].indexOf('task')>-1 || vars[i].indexOf('cid')>-1 || vars[i].indexOf('votes')>-1 || vars[i].indexOf('layout')>-1){
			vars[i] = '';
		}
	}
	var new_url = '';
	for (var i=0; i<vars.length; i++){
		if( vars[i]!='' ) new_url += vars[i] + '&';
	}
	
	new_url += 'layout=paging';
	if($("jav-forum-select").value !=0) url += "&forums=" + $("jav-forum-select").value;
	
	if($("key-"+jav_option_type).value !='') url +="&key=" + $("key-"+jav_option_type).value;
	url = jav_base_url+"?index.php"+url;
	
	jav_ajax_load(url, jav_option_type);
	//pr_ajax = new Ajax(url,{method:'get', update:divid, onComplete:update}).request(); 
}

/*
	@desc: modify comment
	@params: add_button: 0 or 1, 
*/
function jav_changeComment(type, add_button) {	
	jQuery(document).ready( function($) {
		$('#jav-list-comment').hide();		
		switch(type) {
			case 'intensedebate':
				jav_intensedebate(add_button);					
				break;
			case 'disqus':		
					if (dsq > 0) {					
						jav_updateCommentDQ();
					}
					jav_disqus(add_button);						
					break;
			case 'jcomments':				
				jav_jcomments(add_button);
				$('#jav-list-comment').show();
				break;
			case 'jacomment':				
				jav_jacomment(add_button);
				$('#jav-list-comment').show();
				break;
			default:
				jav_intensedebate(add_button);	
		}
	});
}

function jav_jacomment(add_button) {
	jQuery(document).ready( function($) {	
		
		var header_comment = '';
		var arrDivs = new Array('#jac_sub_expand_form > li', '#jac-number-total-comment' , '#btlAddNewComment');
		
		for(j =0; j < arrDivs.length ; j++) {			
			header_comment = $(arrDivs[j]);				
			
			if (header_comment) {
				
				if (header_comment.length > 2)
				{
					for ( k = 0; k <  header_comment.length ; k++)
					{
						var header_html = header_comment[k].innerHTML;
						
						if(header_html) {				
							if(pre_langs !== undefined && pre_langs.length > 0) {
								for(i = 0; i < pre_langs.length; i++) {
									header_html = header_html.replace(pre_langs[i] , next_langs[i]);						
								}
							}
							header_comment[k].innerHTML = header_html;	
						}
					}
				} else {
					var header_html = header_comment.html();	
					
					if(header_html) {				
						if(pre_langs !== undefined && pre_langs.length > 0) {
							for(i = 0; i < pre_langs.length; i++) {
								header_html = header_html.replace(pre_langs[i] , next_langs[i]);						
							}
						}
						header_comment.html(header_html);	
					}
				}
			}
		}
			
		
		/*Add button*/		
		jav_checkAddActionForJAComment(add_button);
		
		/*Update comment*/
		//id_add_action('comment_post', jav_updateComment);
		//jav_hideComment(jav_max_comment);
	});
}


var jaCommentAddButton = false;

function jav_checkAddActionForJAComment(add_button){
	jQuery(document).ready( function($) {
		if (!jaCommentAddButton){
			
			timeout = ( function() {
				
				if(add_button){
					/*Add button*/
					if($('#jac-container-comment').html()!=null){
						var list_items = $('#jac-container-comment li.jac-row-comment');
										
						if (list_items && list_items.length > 0) {					
							$.each(list_items, function(i, item) {
								var objIdc = $($(item).find('.comment-heading'));
								var content = $($(item).find('.comment-text')).html();				
								var url = siteurl + '&task=mark_best_answer&item_id=' + jav_cid + '&type=' + jav_option_type +'&content=' + content;

								var mark_best = $('<a>').addClass('jav-best-answer jacomment')
												.attr('href', 'javascript:void(0)')
												.attr('title', 'Mark the ' + jav_best_answer)
												.html(jav_best_answer)
												.click(function () {
													jav_ajax_load(url);																		  
												})
												.appendTo($('<span>').css('border-left', 'none').css('border-right', '1px solid #CCCCCC').appendTo($('<div>').css('float', 'right').appendTo(objIdc)));
																
							});			
						}
						jaCommentAddButton = true;
					}
					else{
						jav_checkAddActionForJAComment(add_button);
					}
					
				}				
				
			}).delay(100);
			
		} else {		
			clearTimeout(timeout);
			$('#jav-list-comment').show();
		}
	});	
}

function jav_jomcomment(add_button) {
	jQuery(document).ready( function($) {	
		//var body_comment = $('#jc_commentFormDiv');
		//var header_comment = $('#jc_numComment');
		//var footer_comment = $('#write_comment_title');
		//var submit_comment = $('#jc_submit');
		var header_comment = '';
		var arrDivs = new Array('#written_comments_title', '#write_comment_title' , '#jc_submit' , '#jc_commentForm div label:eq(4)');
		
		for(j =0; j < arrDivs.length ; j++) {			
			header_comment = $(arrDivs[j]);	
			if (header_comment) {
				var header_html = header_comment.html();				
				if(header_html) {				
					if(pre_langs !== undefined && pre_langs.length > 0) {
						for(i = 0; i < pre_langs.length; i++) {
							header_html = header_html.replace(pre_langs[i] , next_langs[i]);						
						}
					}
					header_comment.html(header_html);	
				}
			}
		}
		
		
		
		/*Add button*/		
		if (add_button) {
			var list_items = $('#jc_commentsDiv > div');			
			
			if (list_items && list_items.length > 0) {					
				$.each(list_items, function(i, item) {
					var objIdc = $(item).find('.jcAdminPanel');					
					
					if (objIdc.length == 0) {
						var objIdc = $('<div>').addClass('jcAdminPanel').insertBefore($(item).find('.avatarImg'));
					}
					
					var content = $($(item).find('.jc_comment_title')).html();
					
					content = '<b>' + content + '</b> <br/>' + $($(item).find('.comment-text')).html();
					
					var url = siteurl + '&task=mark_best_answer&item_id=' + jav_cid + '&type=' + jav_option_type +'&content=' + content;
					
					//var li_mark_best = $('<li>').addClass('jav-li-best_answer').appendTo(objIdc);
					var mark_best = $('<a>').addClass('jav-best-answer jomcomment')
									.attr('href', 'javascript:void(0)')
									.attr('title', 'Mark the best answer')
									.html(jav_best_answer)
									.click(function () {
										jav_ajax_load(url,jav_option_type);															  
									})
									.appendTo($(objIdc));				
				});					
			}
		}
		
		
		/*Update comment*/
		//id_add_action('comment_post', jav_updateComment);
		//jav_hideComment(jav_max_comment);
	});
}

function jav_jcomments(add_button) {
	jQuery(document).ready( function($) {	
		//var body_comment = $('#jc_commentFormDiv');
		//var header_comment = $('#jc_numComment');
		//var footer_comment = $('#write_comment_title');
		//var submit_comment = $('#jc_submit');
		var header_comment = '';
		var arrDivs = new Array('#comments-form label', '#jc > h4' , '#comments-list-footer a');
		
		for(j =0; j < arrDivs.length ; j++) {			
			header_comment = $(arrDivs[j]);	
			
			
			if (header_comment) {
				if (header_comment.length > 2)
				{
					for ( k = 0; k <  header_comment.length ; k++)
					{
						var header_html = header_comment[k].innerHTML;
						if(header_html) {				
							if(pre_langs !== undefined && pre_langs.length > 0) {
								for(i = 0; i < pre_langs.length; i++) {
									header_html = header_html.replace(pre_langs[i] , next_langs[i]);						
								}
							}
							header_comment[k].innerHTML = header_html;	
						}
					}
				} else {
					var header_html = header_comment.html();	
					
					if(header_html) {				
						if(pre_langs !== undefined && pre_langs.length > 0) {
							for(i = 0; i < pre_langs.length; i++) {
								header_html = header_html.replace(pre_langs[i] , next_langs[i]);						
							}
						}
						header_comment.html(header_html);	
					}
				}
			}
		}
		
		
		/*Add button*/		
		if (true) {
			var list_items = $('.comment-box');			
			
			if (list_items && list_items.length > 0) {					
				$.each(list_items, function(i, item) {
					var objIdc = $(item).find('.comments-buttons');		
					
										
					var content = $($(item).find('.comment-body')).html();
					
					var url = siteurl + '&task=mark_best_answer&item_id=' + jav_cid + '&type=' + jav_option_type +'&content=' + content;
					
					//var li_mark_best = $('<li>').addClass('jav-li-best_answer').appendTo(objIdc);
					var mark_best = $('<a>').addClass('jav-best-answer jomcomment')
									.attr('href', 'javascript:void(0)')
									.attr('title', 'Mark the best answer')
									.html(jav_best_answer)
									.click(function () {
										jav_ajax_load(url,jav_option_type);															  
									})
									.appendTo($(objIdc));				
				});					
			}
		}
		
		
		/*Update comment*/
		//id_add_action('comment_post', jav_updateComment);
		//jav_hideComment(jav_max_comment);
	});
}


function jav_disqus(add_button) {
	jQuery(document).ready( function($) {	
		var header_comment = $('#dsq-comments-count');
		var footer_comment = $('#dsq-add-new-comment');
		var submit_comment = $('#comment-form');
		if ( header_comment ) {
			var header_html = header_comment.html();
			if(header_html) {				
				if(pre_langs != "undefined" && pre_langs.length > 0) {
					for(i = 0; i < pre_langs.length; i++) {
						header_html = header_html.replace(pre_langs[i] , next_langs[i]);						
					}
				}
				header_comment.html(header_html);	
			}
		}			
		
		if ( footer_comment ) {			
			var footer_html = footer_comment.html();
			if(footer_html) {
				if(pre_langs != "undefined" && pre_langs.length > 0) {
					for(i = 0; i < pre_langs.length; i++) {
						footer_html = footer_html.replace(pre_langs[i] , next_langs[i]);
					}
				}
				footer_comment.html(footer_html);
			}
		}	
		
		
		if ( submit_comment ) {			
			var submit_html = submit_comment.html();
			if(submit_html) {
				if(pre_langs != "undefined" && pre_langs.length > 0) {
					for(i = 0; i < pre_langs.length; i++) {
						submit_html = submit_html.replace(pre_langs[i], next_langs[i]);
						//alert(pre_langs[i] +  ' .... ' + next_langs[i] + '- -- ' + submit_html);
					}
				}
				submit_comment.html(submit_html);
			}
		}	
		
		/*Add button*/		
		jav_checkAddActionForDisqus(add_button);
		/*Update comment*/
		//id_add_action('comment_post', jav_updateComment);
		//jav_hideComment(jav_max_comment);
	});
}

var disqusAddButton = false;

function jav_checkAddActionForDisqus(add_button){
	jQuery(document).ready( function($) {
		if (!disqusAddButton){
			
			timeout = ( function() {
				jav_checkAddActionForDisqus(add_button);
				if(add_button){
					/*Add button*/
					var list_items = $('#dsq-comments li.dsq-comment');				
					if (list_items && list_items.length > 0) {					
						$.each(list_items, function(i, item) {
							var objIdc = $($(item).find('.dsq-comment-header'));
							var content = $($(item).find('.dsq-comment-message')).html();				
							var url = siteurl + '&task=mark_best_answer&item_id=' + jav_cid + '&type=' + jav_option_type +'&content=' + content;

							var mark_best = $('<a>').addClass('jav-best-answer disqus')
											.attr('href', 'javascript:void(0)')
											.attr('title', 'Mark the ' + jav_best_answer)
											.html(jav_best_answer)
											.click(function () {
												jav_ajax_load(url);																		  
											})
											.appendTo(objIdc);				
						});					
					}
				}
				
				disqusAddButton = true;
				
			}).delay(700);
			
		} else {		
			clearTimeout(timeout);
			$('#jav-list-comment').show();
		}
	});	
}

var jskitAddButton = false;

function jav_jskit(add_button) {
	jQuery(document).ready( function($) {	
		if (!jskitAddButton){
		/*Add button*/		
			if (add_button) {
				timeout = ( function() {
					
					jav_jskit(add_button);
					
					var list_items = $('#jav-list-comment .js-singleComment');				
					if (list_items && list_items.length > 0) {					
						$.each(list_items, function(i, item) {
							var objIdc = $($(item).find('.js-singleCommentCtls'));
							var content = $($(item).find('.jsk-ItemBodyText')).html();	
							//var comment_id = $(item).attr('id').replace('jsid-', '');
							var url = siteurl + '&task=mark_best_answer&item_id=' + jav_cid + '&type=' + jav_option_type +'&content=' + content;
							var mark_best = $('<a>').addClass('jav-best-answer')
											.attr('href', 'javascript:void(0)')
											.attr('title', 'Mark the ' + jav_best_answer)
											.html(' &ndash; ' + jav_best_answer)
											.click(function () {
												jav_ajax_load(url);																		  
											})
											.appendTo(objIdc);				
						});					
					}					
					
				}).delay(700);
				
				jskitAddButton = true;
			}
		}
		else{
			clearTimeout (timeout);
			$('#jav-list-comment').show();
		}
				
	});
}

function jav_intensedebate(add_button) {	
	jQuery(document).ready( function($) {	
		var header_comment = $('#IDCommentsHead .idc-head h3');
		var footer_comment = $('#IDCommentsNewThreadCover h3');				
		var submit_comment = $('#IDCommentsNewThreadCover span.idc-r strong');
		if ( header_comment ) {
			var header_html = header_comment.html();
			if(header_html) {
				if(pre_langs.length > 0) {
					for(i = 0; i < pre_langs.length; i++) {
						header_html = header_html.replace(pre_langs[i] , next_langs[i]);						
					}
				}
				header_comment.html(header_html);	
			}
		}			
		
		if ( footer_comment ) {			
			var footer_html = footer_comment.html();
			if(footer_html) {
				if(pre_langs != "undefined" && pre_langs.length > 0) {
					for(i = 0; i < pre_langs.length; i++) {
						footer_html = footer_html.replace(pre_langs[i] , next_langs[i]);
					}
				}
				footer_comment.html(footer_html);
			}
		}	
		
		
		if ( submit_comment ) {			
			var submit_html = submit_comment.html();
			if(submit_html) {
				if(pre_langs != "undefined" && pre_langs.length > 0) {
					for(i = 0; i < pre_langs.length; i++) {
						submit_html = submit_html.replace(pre_langs[i], next_langs[i]);
						//alert(pre_langs[i] +  ' .... ' + next_langs[i] + '- -- ' + submit_html);
					}
				}
				submit_comment.html(submit_html);
			}
		}							
		
			
		/*Update comment*/
		jav_checkAddActionForIntensedebate(add_button);		
		
		
		//jav_checkLoadedComment(700);
		//jav_hideComment(jav_max_comment);
	});
	
	
}

function jav_checkAddActionForIntensedebate(add_button){
	jQuery(document).ready( function($) {
		if (typeof(id_add_action) != 'function' ){
			
			timeout = ( function() {
				jav_checkAddActionForIntensedebate(add_button);
				if(add_button){
					/*Add button*/
					var list_items = $('#idc-cover > div.idc-thread');				
					if (list_items && list_items.length > 0) {					
						$.each(list_items, function(i, item) {
							var objIdc = $($(item).find('.idc-v'));
							//var comment_id = $(item).attr('id').replace('IDThread', '');
							var content = $($(item).find('.idc-c-t-inner')).html();					
							//comment_id = parseInt(comment_id);
							var url = siteurl + '&task=mark_best_answer&item_id=' + jav_cid + '&type=' + jav_option_type +'&content=' + content;
							var mark_best = $('<a>').addClass('jav-best-answer intensdebate')
											.attr('href', 'javascript:void(0)')
											.attr('title', 'Mark the ' + jav_best_answer)
											.html(jav_best_answer)
											.click(function () {										
												jav_ajax_load(url);																		  
											})
											.appendTo(objIdc);				
						});					
					}
				}
				
			}).delay(700);
			
		} else {		
			clearTimeout (timeout);
			$('#jav-list-comment').show();
			id_add_action('comment_post', jav_updateCommentIB);
		}
	});	
}


function jav_doPaging( limitstart, limit, order, key, url ){
	limitstart = 0;
	//restore value of from reply
	cancel_frm_response();
	// Display loading icon			
	var mainUrl = jav_base_url + "index.php?tmpl=component&option=com_javoice&view=items&layout=paging&limitstart=" + eval(limitstart) + '&limit=' + eval(limit) + '&type='+ jav_option_type;
	if(order){
		mainUrl += "&order=" + escape(order);
	}
	if(key){
		mainUrl += "&key=" + escape(key);
	}
	if($("jav-forum-select").value !=0) mainUrl += "&forums_id=" + $("jav-forum-select").value;
	
	if(url){
		mainUrl = url+"&limitstart=" + eval(limitstart) + '&limit=' + eval(limit);
	}
	
	jav_ajax_load(mainUrl, jav_option_type);	
}

function jav_displayLoadingSpan() {
	jQuery(document).ready( function($) {
		id='#'+jav_header;
		$(id).css('z-index','1');		
		$('#loader').show();
	});	
}

function jav_change_status( itemid, statusid, type_id ){
	var mainUrl = jav_base_url + "index.php?tmpl=component&option=com_javoice&view=items&task=change_status&cid="+itemid+'&statusid='+statusid+'&type='+type_id;	
	jav_ajax_load(mainUrl, jav_option_type);
}

function jav_show_all_status( itemid ) {
	jQuery(document).ready( function($) {
		jav_showDiv('#jav-box-item-' + itemid + ' .statuses');
		$('#jav-box-item-' + itemid + ' .statuses').css('top', '-65px');
	});	
}


function jav_updateCommentIB() {
	jQuery(document).ready( function($) {		
		//jav_hideComment(jav_max_comment);
		/*var cmt_header = $('#IDCommentsHead .idc-head h3');
		if ( cmt_header ) {
			var header_html = cmt_header.html();		
			var posFirst = header_html.indexOf('(');
			var posLast = header_html.indexOf(')');
			var reply_total = parseInt(header_html.substr(posFirst + 1, posLast - 1));
		}*/	
		
		var reply_total = 1;
		var cmt_list = $('#idc-cover .idc-i');
		
		if ( cmt_list ) {			
			reply_total = cmt_list.length;
		}
		
		var url = jav_base_url + "index.php?tmpl=component&option=com_javoice&view=items&task=updateTotalComments&cid=" + jav_cid + "&type=" + jav_option_type + "&total=" + reply_total;
		var divUpdate  = '';
		jav_ajax_update(url);
	});	
}


function jav_updateCommentDQ() {
	jQuery(document).ready( function($) {		
		
		//jav_hideComment(jav_max_comment);
		var reply_total = 1;
		var cmt_list = $('#dsq-comments .dsq-comment');
		if ( cmt_list ) {
			reply_total = cmt_list.length;
		}				
		var url = jav_base_url + "index.php?tmpl=component&option=com_javoice&view=items&task=updateTotalComments&cid=" + jav_cid + "&type=" + jav_option_type + "&total=" + reply_total;
		var divUpdate  = '';
		jav_ajax_update(url);
	});	
}


function jav_hideComment(count) {
	jQuery(document).ready( function($) {
		var list_items = $('#idc-cover .idc-thread');
		if (list_items.length >= count) {
			$('#IDCommentsNewThreadCover').hide();
			/*Hide reply*/
			$.each(list_items, function(i, item) {
					$(item).find('.idc-c-b').hide();
			});
			
		} else {
			$('#IDCommentsNewThreadCover').show();
			$.each(list_items, function(i, item) {
					$(item).find('.idc-c-b').show();
			});
		}
	});	
}

function change_options(obj, url, type_id ){
	//jQuery(document).ready( function($) {
		/*Cancel Ajax when other action is called*/	
		
		if (jav_ajax) {
			jav_ajax.abort();
		}
	
		jav_ajax_load(url, type_id);
		jav_addClass('#jav-list-options-' + type_id, obj, 'current');
	
/*		var smalls = $('jav-list-options-' + type_id).getElementsByTagName('small');
		
		for( var i=0; i<smalls.length; i++){
			smalls[i].setStyle('display', 'none');
		}	
		obj.getElementsByTagName('small')[0].setStyle('display', '');*/
	
}		

function delete_reply_voice(node, id, responeid, mes){
	jQuery(document).ready( function($) {		
		var action  = confirm(mes);
		if(action){
			$(node).parent().css('display','none');			
			var mainUrl = jav_base_url + "index.php?tmpl=component&option=com_javoice&view=items&task=deletereply&item_id="+id+ '&responeid='+responeid;	
			jav_ajax_load(mainUrl);			
		}
	});
}
	
function show_frm_response(node, id, responeid){		
	jQuery(document).ready( function($) {
		
		$(node).parent().css('display','none');
		
		$("#jav-smileys-Reply").hide();
		
		//get reply form from add new form
		if(jav_current_active_voice == 0){
			//set text or get text						
			form = $("#jav-form-reply").html();		
			$("#jav-form-reply").html("");
			$("#jav-form-reply").hide();											
		}
		
		//get reply from another reply form
		else{	
			form = $("#jav-container-response-" + jav_current_active_voice).html();
			if(form==''){
				form = $("#jav-container-bestanswer-" + jav_current_active_voice).html();
			}
			
			$("#jav-container-response-" + jav_current_active_voice).html("");			
			$('#jav-form-response-' + jav_current_active_voice).hide();									
			
			/* Best Answer */
			$("#jav-container-bestanswer-" + jav_current_active_voice).html("");
			$('#jav-form-bestanswer-' + jav_current_active_voice).hide();
			if($("#jav-content-bestanswer-" + jav_current_active_voice).val() != ""){	
				$('#jav-box-item-' + jav_current_active_voice + ' .jav-bestanswer-text').show();
				$('#jav-box-item-' + jav_current_active_voice + ' .jav-bestanswer-text .editable').show();
			}else{
				$('#jav-box-item-' + jav_current_active_voice + ' .jav-bestanswer-text').hide();
			}
			$("#jav-container-bestanswer-" + jav_current_active_voice).hide();
			
			/* Response */
			$("#jav-container-response-" + jav_current_active_voice).html("");
			$('#jav-form-response-' + jav_current_active_voice).hide();
			if($("#jav-content-respone-" + jav_current_active_voice).val() != ""){	
				$('#jav-box-item-' + jav_current_active_voice + ' .jav-response-text').show();
				$('#jav-box-item-' + jav_current_active_voice + ' .jav-response-text .editable').show();
			}else{
				$('#jav-box-item-' + jav_current_active_voice + ' .jav-response-text').hide();
			}
			$("#jav-container-response-" + jav_current_active_voice).hide();
			
			//show abutton add respone
			$('#jav-add-respone-'+ jav_current_active_voice).show();				
		}			
		
		//assign text of current form to next
		$("#jav-container-response-" + id).html(form);		
		$("#jav-container-response-" + id).show();
		
		//show form
		$('#jav-form-response-' + id).show();		
		$('#jav-box-item-' + id + ' .jav-response-text').hide();		
		
		$('#new_reply_item' + id).focus();
		var name ='response-'+id;			
		$("#div-"+name).show('fast');
		$("#newVoiceContentReply").val($("#jav-content-respone-" + id).val());		
		if(typeOf(total_attach_file)){			
			$("#jav_result_reply_upload").html("");
			
			numberUpload = $('#jav-box-item-' + id + ' .jav-upload-form').find('input').length;
			if(numberUpload>=total_attach_file){
				$("#myfilereply").attr("disabled", true);
			}else{
				$("#jav_err_myfilereply").hide();
				$("#myfilereply").removeAttr("disabled"); 
			}
			$("#jav_result_reply_upload").html($('#jav-box-item-' + id + ' .jav-upload-form').html());
		}
		$("#hd_parent_respone").val(id);
		$("#hd_respone_id").val(responeid);
		jav_current_active_voice = id;							
	});
	jav_delete_session_upload = 1;
	if(typeof(DCODE) !== 'undefined' && jQuery("#newVoiceContentReply").length > 0){
		DCODE.setTags (["LARGE", "MEDIUM", "HR", "B", "I", "U", "S", "UL", "OL", "SUB", "SUP", "QUOTE", "LINK", "IMG", "YOUTUBE", "HELP"]);
		DCODE.activate ("newVoiceContentReply", false);	
	}
	return false;
}

function cancel_frm_response(){	
	if(typeOf(jav_current_active_voice)){
		if(jav_current_active_voice){
			jQuery(document).ready( function($) {
				form = $("#jav-container-response-" + jav_current_active_voice).html();		
				$("#jav-container-response-" + jav_current_active_voice).html("");		
				$("#jav-form-reply").html(form);	
				$("#jav-container-response-" + jav_current_active_voice).hide();
				jav_current_active_voice = 0;
				$("#hd_parent_respone").val("0");
				$("#hd_respone_id").val("0");
			});
		}
		jav_delete_session_upload = 1;
	}
}

function hide_frm_response(node, id, show){
	jQuery(document).ready( function($) {
		//restore form add new
		$("#jav-container-response-" + jav_current_active_voice).hide();
		jav_current_active_voice = 0;
		$("#hd_parent_respone").val("0");
		$("#hd_respone_id").val("0");
		
		form = $("#jav-container-response-" + id).html();		
		$("#jav-container-response-" + id).html("");		
		$("#jav-form-reply").html(form);
		
		$(node).parent().css('display','block');
		$('#jav-form-response-' + id).hide();
		if(show){
			$('#jav-box-item-' + id + ' .jav-response-text').show();
		}
		else{
			$('#jav-box-item-' + id + ' .jav-response-text').hide();
		}
		jav_delete_session_upload = 1;											
		return false;
	});
	
}

function jav_submit_admin_response(item_id, id){
	//get content
	jQuery(document).ready( function($) {						
		if($("#newVoiceContentReply")[0].tagName != "TEXTAREA"){		
			$("#err_exitchekspellingReply").show();			
			return false;
		}else{
			var content = $("#newVoiceContentReply").val();
			if(content==''){
				if($("#err_emptyreplyvoice").length >0)
					$("#err_emptyreplyvoice").show();
				return false;
			}else{				
				$("#jav-form-response-" + item_id + " input").attr('disabled', 'disabled');
				if($("#err_emptyreplyvoice").length >0)
					$("#err_emptyreplyvoice").hide();
				var mainUrl = jav_base_url + "index.php?tmpl=component&option=com_javoice&view=items&task=admin_response&item_id="+item_id+ '&cid[]='+id +"&";
				
				var data = $("#new_reply_item").serialize();
				
				jav_displayLoadingSpan();
				
				jQuery(document).ready( function($) {	
					$.post(mainUrl, data, function(response) {			
						jav_parseData(response);			
					}, 'json');					
				});
								
				cancel_frm_response();
			}
		}				
	});		
}

function jav_checkLoadedComment (time_delay) {
	jQuery(document).ready( function($) {
		$('#jav-list-comment').hide();
		timeout = ( function() {
			if ($('#idc-container-parent'))
			{
				
				$('#jav-list-comment').show();
				clearTimeout (timeout);
			} else {				
				clearTimeout (timeout);
				jav_checkLoadedComment(jav_checkLoadedComment);
			}
		}).delay(time_delay);
	});
}

function show_frm_bestanswer(node, id, bestanswerid){
	jQuery(document).ready( function($) {
		
		$(node).parent().css('display','none');
		$("#jav-smileys-Reply").hide();
		
		//get reply form from add new form
		if(jav_current_active_voice == 0){
			//set text or get text						
			form = $("#jav-form-reply").html();		
			$("#jav-form-reply").html("");
			$("#jav-form-reply").hide();											
		}
		
		//get reply from another reply form
		else{			
			
			form = $("#jav-container-bestanswer-" + jav_current_active_voice).html();
			if(form==''){
				form = $("#jav-container-response-" + jav_current_active_voice).html();
			}
			
			$("#jav-container-bestanswer-" + jav_current_active_voice).html("");			
			$('#jav-form-bestanswer-' + jav_current_active_voice).hide();
			
			
			
			/* Response */
			$("#jav-container-response-" + jav_current_active_voice).html("");
			$('#jav-form-response-' + jav_current_active_voice).hide();
			if($("#jav-content-respone-" + jav_current_active_voice).val() != ""){	
				$('#jav-box-item-' + jav_current_active_voice + ' .jav-response-text').show();
				$('#jav-box-item-' + jav_current_active_voice + ' .jav-response-text .editable').show();
			}else{
				$('#jav-box-item-' + jav_current_active_voice + ' .jav-response-text').hide();
			}
			$("#jav-container-response-" + jav_current_active_voice).hide();
			
			/* Best Answer */
			$("#jav-container-bestanswer-" + jav_current_active_voice).html("");
			$('#jav-form-bestanswer-' + jav_current_active_voice).hide();
			if($("#jav-content-bestanswer-" + jav_current_active_voice).val() != ""){	
				$('#jav-box-item-' + jav_current_active_voice + ' .jav-bestanswer-text').show();
				$('#jav-box-item-' + jav_current_active_voice + ' .jav-bestanswer-text .editable').show();
			}else{
				$('#jav-box-item-' + jav_current_active_voice + ' .jav-bestanswer-text').hide();
			}
			$("#jav-container-bestanswer-" + jav_current_active_voice).hide();
			
			//show abutton add bestanswer
			$('#jav-add-bestanswer-'+ jav_current_active_voice).show();			
		}			
	
		//assign text of current form to next
		$("#jav-container-bestanswer-" + id).html(form);		
		$("#jav-container-bestanswer-" + id).show();
		
		//show form
		$('#jav-form-bestanswer-' + id).show();		
		$('#jav-box-item-' + id + ' .jav-bestanswer-text').hide();		
		
		$('#new_reply_item' + id).focus();
		var name ='bestanswer-'+id;			
		$("#div-"+name).show('fast');
		
		var jav_content_bestanswer = $("#jav-content-bestanswer-" + id).val();
		if(typeof(DCODR) !== 'undefined') {
			jav_content_bestanswer = DCODR.formatHTML(jav_content_bestanswer, true);
			jav_content_bestanswer = jav_content_bestanswer.replace(/\\\[/gi, '\[');
		}
		$("#newVoiceContentReply").val(jav_content_bestanswer);
		
		if(typeOf(total_attach_file)){			
			$("#jav_result_reply_upload").html("");
			
			numberUpload = $('#jav-box-item-' + id + ' .jav-upload-form').find('input').length;
			if(numberUpload>=total_attach_file){
				$("#myfilereply").attr("disabled", true);
			}else{
				$("#jav_err_myfilereply").hide();
				$("#myfilereply").removeAttr("disabled"); 
			}
			$("#jav_result_reply_upload").html($('#jav-box-item-' + id + ' .jav-upload-form').html());
		}
		$("#hd_parent_bestanswer").val(id);
		$("#hd_bestanswer_id").val(bestanswerid);
		
		jav_current_active_voice = id;
		
	});
	
	jav_delete_session_upload = 1;
	if(typeof(DCODE) !== 'undefined' && jQuery("#newVoiceContentReply").length > 0){
		DCODE.setTags (["LARGE", "MEDIUM", "HR", "B", "I", "U", "S", "UL", "OL", "SUB", "SUP", "QUOTE", "LINK", "IMG", "YOUTUBE", "HELP"]);
		DCODE.activate ("newVoiceContentReply", false);	
	}
	return false;				
}

function hide_frm_bestanswer(node, id, show){
	
	jQuery(document).ready( function($) {
		//restore form add new
		$("#jav-container-bestanswer-" + jav_current_active_voice).hide();
		jav_current_active_voice = 0;
		
		form = $("#jav-container-bestanswer-" + id).html();		
		$("#jav-container-bestanswer-" + id).html("");		
		$("#jav-form-reply").html(form);
		
		$(node).parent().css('display','block');
		$('#jav-form-bestanswer-' + id).hide();
		
		if(show){
			$('#jav-box-item-' + id + ' .jav-bestanswer-text').show();
		}
		else{
			$('#jav-box-item-' + id + ' .jav-bestanswer-text').hide();
		}
		
		jav_delete_session_upload = 1;
		
		return false;
	});
	
}

function jav_submit_bestanswer(item_id, id){
	
	//get content
	jQuery(document).ready( function($) {						
		if($("#newVoiceContentReply")[0].tagName != "TEXTAREA"){		
			$("#err_exitchekspellingReply").show();			
			return false;
		}else{
			var content = $("#newVoiceContentReply").val();
			if(content==''){
				if($("#err_emptyreplyvoice").length >0)
					$("#err_emptyreplyvoice").show();
				return false;
			}else{				
				$("#jav-form-bestanswer-" + item_id + " input").attr('disabled', 'disabled');
				if($("#err_emptyreplyvoice").length >0)
					$("#err_emptyreplyvoice").hide();
				var mainUrl = jav_base_url + "index.php?tmpl=component&option=com_javoice&view=items&task=mark_best_answer&item_id="+item_id+ '&cid[]='+id +"&";
				
				var data = $("#new_reply_item").serialize();
				
				jav_displayLoadingSpan();
				
				jQuery(document).ready( function($) {	
					$.post(mainUrl, data, function(response) {			
						jav_parseData(response);			
					}, 'json');					
				});
								
				cancel_frm_bestanswer();
				$('#jav-box-item-' + id + ' .jav-bestanswer-text').show();
			}
		}				
	});		
	
}

function cancel_frm_bestanswer(){
	if(typeOf(jav_current_active_voice)){
		if(jav_current_active_voice){
			jQuery(document).ready( function($) {
				form = $("#jav-container-bestanswer-" + jav_current_active_voice).html();		
				$("#jav-container-bestanswer-" + jav_current_active_voice).html("");		
				$("#jav-form-reply").html(form);	
				$("#jav-container-bestanswer-" + jav_current_active_voice).hide();
				$('#jav-form-bestanswer-' + jav_current_active_voice).hide();
				$("#jav-form-bestanswer-" + jav_current_active_voice + " input").attr('disabled', '');
				
				jav_current_active_voice = 0;
			});
		}
		jav_delete_session_upload = 1;
	}
}

function jav_clean_search(type_id,forum_id){
	jQuery(document).ready(function($) {
		if(!$("#isshowaddnewbutton-"+type_id)[0]){			
			$('#jav-mainbox-' + type_id +' .jav-search-result').hide();
		}
		$('#key-' + type_id ).attr('value', '');
		$('#jav-forum-select').attr('value',forum_id);
	});
	
}

function jav_checkMaxLength(obj, maxlength) {
	jQuery(document).ready(function($) {
		var strTitle = trim(obj.value);
		var remain_number = maxlength - strTitle.length;
		$('#jav-charactersleft').html(remain_number);
		
	});
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
function jav_showNoticeToCenter(xwidth,yheight,divid) {
	// First, determine how much the visitor has scrolled
	jQuery(document).ready(function($) {
		var scrolledX, scrolledY;
		if( self.pageYoffset || self.pageXoffset) {
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
		var topoffset = scrolledY + (centerY - yheight) / 2;
		
		// the initial width and height of the div can be set in the
		// style sheet with display:none; divid is passed as an argument to // the function
		var obj = $('#' + divid);
		obj.css('position', 'absolute');
		
		if (obj.css('top') && topoffset) {			
			obj.css('top', parseInt(topoffset) + 'px');
		}
		
		if (obj.css('left') && leftoffset) {
			obj.css('left', parseInt(leftoffset) + 'px');
		}
		
		obj.css('display', 'block');	
	});
} 

function jav_change_vars(type_id){
	jav_vote_total = $('votes-left-'+type_id).value;
	jav_option_type = type_id;
	var url = siteurl + '&task=load_forums&type='+parseInt(type_id);
	jav_ajax_load(url);
}
function closemessage(){
	jQuery(document).ready(function($) {
		id='#'+jav_header;
		$(id).css('z-index','10');
		$('#jav-msg-succesfull').css('display','none');
	});	
}
function displaymessage(){
	jQuery(document).ready(function($) {
		id='#'+jav_header;
		$(id).css('z-index','1');
		$('#jav-msg-succesfull').css('display','');
	});	
}
function jaCreatLogin(location,title) {

	var obj = jQuery("#ja-wrap-popup");
	if (obj)
		obj.remove();
	var content = jQuery('<div>').attr( {
		'id' :'ja-wrap-popup'
	}).appendTo(location);
	
	new Ajax(jav_ajax_url_login, {
		method :'get',
		onComplete : function(text) {
			//jQuery('#ja-wrap-popup').html(text);
			jQuery('#jaFormContentTop').html(title);
			jQuery('#javoice_as').css('display','none');
			jQuery('#javoice_ac').css('display','none');
			loadLoginComplete();
		}
	}).request();
}
function jaLoadLogin(url,title,action) {	
	if(action){
		jaCreatForm(url+"&layout=login",title,700,350,title);
	}else{	
		new Request({
			url : url, 
			method :'get',
			onComplete : function(text) {
				jQuery('#jav-dialog-content').html(text);	
				jQuery('#jaFormContentBottom').html('&nbsp;');
				var oldel = jQuery('#ja-wrap-popup');
				if(oldel.html()){
					//var newel = oldel.clone();
					oldel.remove();
					//newel.appendTo(jQuery('#jav-login-joomla-form'));
				}else{
					//jaCreatLogin('#jav-login-joomla-form',title);
				}
				loadLoginComplete();
			}
		}).send();
	}
}
function removefile(url,type) {
	new Ajax(url, {
		method :'get',
		onComplete : function(text) {
			if(type){
				var par = window.parent.document;
				jQuery('#jav_result_upload',par).html(text);
				jQuery('#myfile',par).removeAttr('disabled');
			}else{
				jQuery('#jav_result_upload').html(text);
				jQuery('#myfile').removeAttr('disabled');
			}
			
		}
	}).request();
}
function get_check_atd(isload){
	if(isload && jav_enable_after_the_deadline){
		jav_includeJs_complete('components/com_javoice/asset/js/atd/jquery.atd.js');
		jav_includeJs_complete('components/com_javoice/asset/js/atd/csshttprequest.js');
		jav_includeJs_complete('components/com_javoice/asset/js/atd/atd.js');	
		jav_includeCss('components/com_javoice/asset/js/atd/atd.css');
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
function jav_includeJs_complete(file,arrObj) {
	var js;
    var html_doc = document.getElementsByTagName('head')[0];
    js = document.createElement('script');
    js.setAttribute('type', 'text/javascript');
    js.setAttribute('src', file);
    html_doc.appendChild(js);
}
function loadLoginComplete(){
	if(!jav_base_url_login)jav_base_url_login="";
	jQuery("input[name=return]").attr('value',jav_base_url_login) ;
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
function display_plugin(type){
	jQuery(document).ready(function($) {
		var id = "#jav_"+type+"_content";
		var display = $(id).css('display');
		if(display=='block'){
			$(id).css('display','none');
			return ;
		}
		else{				
			$(id).css('display','block');
			if(type=='embed')
				$("#jav_smiley_content").css('display','none');
			else if (type=='smiley') 
				$("#jav_embed_content").css('display','none');
			return ;
		}
	});
}
function submitembed(){
	
	
	jQuery(document).ready( function($) {
		
		$('#task').attr('value','submitemded');
		
		var vars = $("#new_item").serialize();
		
		$.get("index.php", vars, function(response) {
			if(jav_parseData(response))
				display_plugin('embed');
		}, 'json');
	});	
}
function insertsmiley(content){
	var newVoiceContent = jQuery('#newVoiceContent').attr('value');
	jQuery('#newVoiceContent').attr('value',newVoiceContent+content);
}

function loadNewCaptcha(){	            				
	$("jav_image_captcha").src =  "index.php?option=com_javoice&view=items&task=displaycaptchaaddnew&tmpl=component&ran=" + Math.random();	
}
function actionLoadNewCaptcha(action){
	//show image load new
	if(action){
		jQuery('#jac-refresh-image').show();		
	}
	//dis able image load new
	else{
		jQuery('#jac-refresh-image').hide();
	}
}

function textCounter(textAreID, textCountField){
	maxlimit = JACommentConfig.maxLengthComment;
	if($(textAreID).value.length > maxlimit){
		$(textAreID).value = $(textAreID).value.substring(0, maxlimit);
	}else{
		$(textCountField).innerHTML = maxlimit - $(textAreID).value.length;  
	}
}

function changeDisplay(id, action, isSmiley){
	
	if(jQuery(id).length > 0){
		jQuery(id).style.display = action;
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
				jav_textarea_cursor = jQuery("#newVoiceContent")[0].selectionStart;
				if(jQuery("#newVoiceContent")[0].selectionStart == undefined){				
					jQuery("#newVoiceContent")[0].focus ();
					var range = document.selection.createRange();
					var stored_range = range.duplicate ();
					stored_range.moveToElementText (el);
					stored_range.setEndPoint ('EndToEnd', range);
					jav_textarea_cursor = stored_range.text.length - range.text.length;
				
				}else{
					jav_textarea_cursor = jQuery("#newVoiceContent")[0].selectionStart;
				}
			}else{
				jav_textarea_cursor = jQuery("#newVoiceContentReply")[0].selectionStart;
				if(jQuery("#newVoiceContentReply")[0].selectionStart == undefined){				
					jQuery("#newVoiceContentReply")[0].focus ();
					var range = document.selection.createRange();
					var stored_range = range.duplicate ();
					stored_range.moveToElementText (el);
					stored_range.setEndPoint ('EndToEnd', range);
					jav_textarea_cursor = stored_range.text.length - range.text.length;
				
				}else{
					jav_textarea_cursor = jQuery("#newVoiceContentReply")[0].selectionStart;
				}
			}					
		}
	});
}

function insertSmiley(which) {	
	text = document.getElementById("newVoiceContent").value;
	document.getElementById("newVoiceContent").value = text.substring(0, jav_textarea_cursor) + which + text.substring(jav_textarea_cursor, text.length);
	jav_textarea_cursor = jav_textarea_cursor + which.length;  
}
function insertSmileyReply(which) {
	text = document.getElementById("newVoiceContentReply").value;
	document.getElementById("newVoiceContentReply").value = text.substring(0, jav_textarea_cursor) + which + text.substring(jav_textarea_cursor, text.length);
	jav_textarea_cursor = jav_textarea_cursor + which.length;   
}

function javOpenAttachFile(id){
	if(id !=0 || id != ""){
		//reply a new voice
		if($("jav-form-upload-reply").style.display == "none"){
			$("jav-form-upload-reply").style.display = "block";
		}else{
			$("jav-form-upload-reply").style.display = "none";
		}
	}
	//add new or edit voice
	else{
		if($("jav-form-upload").style.display == "none"){
			$("jav-form-upload").style.display = "block";
			window.location.href += "#jav-form-upload";
		}else{
			$("jav-form-upload").style.display = "none";
		}
	}
}
function jav_addNewVoice(){	
	if(!validation()) return false;		
	jQuery(document).ready( function($) {		
		$("#task").val("save");
		if($("#javoice_as")){
			$("#javoice_as").attr("disabled", true);
		}
		
		var vars = $("#new_item").serialize();	
		$.post(siteurl, vars, function(response) {
			jav_parseData(response);
			if($("#javoice_as")){
				$("#javoice_as").removeAttr("disabled");
			}
		}, 'json');
	});
}

function jav_delete_item(id, type_id){
	var url = siteurl + '&task=remove&cid=' + id;
	return jav_ajax_load(url);
}

function jav_handleEnter (field, event) {
	var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
	if (keyCode == 13) {
		var i;
		for (i = 0; i < field.form.elements.length; i++)
			if (field == field.form.elements[i])
				break;
		i = (i + 1) % field.form.elements.length;
		field.form.elements[i].focus();
		return false;
	} 
	else
	return true;
}     
function jav_show_dialog(box, subbox){
	$(box).setStyles({'display':'block'});
	var size = $(window.document.body).getSize();
	var left = (size.size.x - $(subbox).offsetWidth)/2;
	var top = 100;
	
	$(box).setStyles({'top':top, 'left':left});
}
function suggest(inputString){
	jQuery(document).ready( function($) {		
		if(inputString.length == 0) {
			$('#jav_tag_form #suggestions').fadeOut();			
		} else {
		clearTimeout(timeout);
		
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
			
			queryString = "queryString="+searchText+"&currenttext="+escape(currenttext);			
			tagURL = siteurl.replace("&view=items", "&view=tags&layout=showlist");			
			timeout = setTimeout( function() {																																		
				jav_ajax = $.ajax({
				  type: 'POST',
				  //url: "index.php?option=com_javoice&view=tags&layout=showlist&tmpl=component",
				  url: tagURL,
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

function javAddTags(numberTag){
	jQuery(function($) {	
		inputString = $('#jav_tag_form #jav_input_tags').val();
		if(!inputString) return;		
		arraycheck = inputString.split(jav_tag_config.characters_separating_tags);
		resultString = "";
		var error =0;
		for(i = 0; i < arraycheck.length; i++){
			if(arraycheck[i].length >= jav_tag_config.tag_minimum_length){
				if(arraycheck[i].length <= jav_tag_config.tag_maximum_length){
					if(resultString == "")
						resultString = arraycheck[i];
					else	
						resultString += jav_tag_config.characters_separating_tags	+ arraycheck[i];
				}else{
					error++;
					alert("Your tag: '"+ arraycheck[i] +"' is very long");
				}
			}else{
				error++;
				alert("Your tag: '"+ arraycheck[i] +"' is very short");
			}
		}
		
		if(error > 0) {
			return;
		}
		else if((jQuery(".javtaglist .javtagid").length*1+arraycheck.length*1) > numberTag){
			alert("Total of your added tag must be smaller than "+numberTag+"!");
			return;
		}
		else{
			$("#jav_add_tag_button").attr('disabled', 'disabled');
			
			addText = "taglist="+escape(resultString);
			$.post(jav_base_url+"index.php?option=com_javoice&view=tags&task=addnew&tmpl=component", addText, function(response) {
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
				jQuery(document).ready( function($) {				
				    if($(".javtaglist").length <=0){			    	
				    	$("#jav_tags_list").html('<ul class="javtaglist"></ul>');
				    }
				});		
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
		}
					
	});
}
function javqueueListenerScroll(){
	jQuery(function($) {
		$(window).scroll(function() {
			jaVoiceProcessScroll("scroll");
		});
	});
	
//	window.addEvent ('scroll', function (e) {				
//		jaVoiceProcessScroll("scroll");
//	});
}
function isScrollInBottom(){	
	var pageHeight = getDocHeight();	
	var scrollHeight = window.pageYOffset || document.body.scrollTop || document.documentElement.scrollTop;
    scrollHeight =  scrollHeight ? scrollHeight : 0;	
	var innerHeight = self.innerHeight || document.body.clientWidth;
    innerHeight =  innerHeight ? innerHeight : 0;
    
	return (pageHeight - (scrollHeight + innerHeight)) < 400;	
}
function getDocHeight() {
    var D = document;
    return Math.max(
        Math.max(D.body.scrollHeight, D.documentElement.scrollHeight),
        Math.max(D.body.offsetHeight, D.documentElement.offsetHeight),
        Math.max(D.body.clientHeight, D.documentElement.clientHeight)
    );
}
function jaVoiceProcessScroll(jascaction){
	
	if(isScrollInBottom()){			
		jQuery(function($) {
			//process ajax			
			if(jav_process_ajax != 0) return;			
			current_tab = 0;
			//find current tab
		    $(".javtabs-title").find("li").each(function(index) {
		        if($(this).hasClass("active")){
		        	current_tab = $(this).attr("id");		        	
		        	current_tab = current_tab.replace("jav-typeid_",""); 		        			            
		        }        
		    });
		    
			if(current_tab){				
				//if have page
				if($("#jav_nexpage_"+current_tab) && $("#jav_nexpage_"+current_tab).val()){
					nextPage = $("#jav_nexpage_"+current_tab).val();					
					nextPage = nextPage.split("_");
					page 	  = parseInt(nextPage[0],10);
					totalPage = parseInt(nextPage[1],10);
					
					if(page < totalPage){						
						jav_displayLoadingSpan();
						jav_process_ajax = 1;
						url = jav_base_url+"?index.php&layout=paging&pagingtype=autoscroll&option=com_javoice&tmpl=component&type="+current_tab+"&javpage="+page;
						
						if($("#jav-forum-select").val() !=0) url += "&forums_id=" + $("#jav-forum-select").val();						
						jav_ajax_load(url, jav_option_type);
					}										
				}
			}			
		});	
	}	
}