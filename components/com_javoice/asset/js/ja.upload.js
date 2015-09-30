function in_array(needle, haystack, strict) {
	for(var i = 0; i < haystack.length; i++) {
		if(strict) {
			if(haystack[i] === needle) {
				return true;
			}
		} else {
			if(haystack[i] == needle) {
				return true;
			}
		}
	}

	return false;
}
function javCheckTypeFile(value, action, id){		
	var pos = value.lastIndexOf('.');
	var type = value.substr(pos+1, value.length).toLowerCase();
	if(!in_array(type, v_array_type, false)){			
		if(action == "admin"){			
			document.getElementById('jav_err_myfile_reply').style.display = "block";			
		}else if(action == "reply"){				
			document.getElementById('jav_err_myfilereply').style.display = "block";
			document.getElementById('jav_err_myfilereply').innerHTML = "<span class='err' style='color:red;'>"+error_type_file+"<\/span>" +"<br />";
		}else{
			document.getElementById('jav_err_myfile').style.display = "block";
			document.getElementById('jav_err_myfile').innerHTML = "<span class='err' style='color:red;'>"+error_type_file+"<\/span>" +"<br />";		
		}
		return false;
	}
	
	var fileName = value.substr(0, pos+1).toLowerCase();
	if(fileName.length > 100){
		if(action == "admin"){			
			document.getElementById('jav_err_myfile_reply').style.display = "block";			
		}else if(action == "reply"){
			document.getElementById('jav_err_myfilereply').style.display = "block";
			document.getElementById('jav_err_myfilereply').innerHTML = "<span class='err' style='color:red;'>"+error_name_file+"<\/span>" +"<br />";
		}else{
			document.getElementById('jav_err_myfile').style.display = "block";
			document.getElementById('jav_err_myfile').innerHTML = "<span class='err' style='color:red;'>"+error_name_file+"<\/span>" +"<br />";		
		}
		return false;
	}
	return true;
}	

function javCheckTotalFileReply(){
	javCheckTotalFile("reply");
}

function javCheckTotalFile(action){		
	myfile = "myfile";
	jav_result_upload = "jav_result_upload";
	err_myfile = "jav_err_myfile";
	if(action == "reply"){
		myfile = "myfilereply"
		jav_result_upload = "jav_result_reply_upload";
		err_myfile = "jav_err_myfilereply";
	}
	
	if(document.getElementById(myfile) == undefined) return;
	
	var listFiles =  $(jav_result_upload).getElements('input[name^=listfile]');	
	var currentTotal = 0;
	for(i = 0 ; i< listFiles.length; i++){		
		if(listFiles[i].checked == true){
			currentTotal+=1;
		}
	}	
	if(currentTotal < total_attach_file){		
		document.getElementById(myfile).disabled = false;
		for(i = 0 ; i< listFiles.length; i++){
			if(listFiles[i].checked == false){
				listFiles[i].disabled = false;
				document.getElementById(err_myfile).style.display = "none";
			}
		}
	}else{		
		document.getElementById(myfile).disabled = true;
		for(i = 0 ; i< listFiles.length; i++){
			if(listFiles[i].checked == false){
				listFiles[i].disabled = true;
				document.getElementById(err_myfile).style.display = "block";
			}
		}	
	}
}

function javStartUpload(action){	
	if(!javCheckTypeFile(document.new_item.myfile.value)) return false;
	document.new_item.setAttribute( "autocomplete","off" );
	document.new_item.target = "upload_target";
	document.getElementById('jav_upload_process').style.display='block';
	document.new_item.task.value = "uploadFile";		
	document.new_item.submit();
}

function javStartAdminUpload(action){
	if(!javCheckTypeFile(document.adminForm.myfile.value)) return false;
	document.adminForm.setAttribute( "autocomplete","off" );
	document.adminForm.target = "upload_target";
	document.getElementById('jav_upload_process').style.display='block';
	document.adminForm.task.value = "uploadFile";		
	document.adminForm.submit();
}

function javStartAdminReplyUpload(){
	if(!javCheckTypeFile(document.adminForm.myfile.value, "reply")) return false;
	document.adminForm.setAttribute( "autocomplete","off" );
	document.adminForm.target = "upload_target";
	document.getElementById('jav_reply_upload_process').style.display='block';
	document.adminForm.task.value = "uploadReplyFile";		
	document.adminForm.submit();
}



function javStartReplyUpload(){						
	if(!javCheckTypeFile(document.new_reply_item.myfile.value, "reply")) return false;	
	document.new_reply_item.setAttribute( "autocomplete","off" );			
	document.new_reply_item.target = "upload_target";
	
	if(jav_delete_session_upload == 1){
		document.getElementById('javhd_deleteSession').value =1;		
		jav_delete_session_upload = 0;
	}else{
		document.getElementById('javhd_deleteSession').value =0;
	}			
	
	document.getElementById('jav_reply_upload_process').style.display='block';
	document.new_reply_item.submit();	
}			