
/* convienence method to restore the text area from the preview div */
function restoreTextArea(id, isAdmin)
{   		
	if(id) id = 'Reply';
	if(id){
		if(jQuery("#err_exitchekspellingReply").length >0)
			jQuery("#err_exitchekspellingReply").hide();		
	}else{
		if(jQuery("#err_exitchekspelling").length >0)
			jQuery("#err_exitchekspelling").hide();	
	}
    
    /* clear the error HTML out of the preview div */
    AtD.remove('newVoiceContent'+id); 

    /* swap the preview div for the textarea, notice how I have to restore the appropriate class/id/style attributes */
    jQuery('#newVoiceContent'+id).replaceWith('<textarea class="field textarea" id="newVoiceContent'+id+'" name="newVoiceContent" rows="12" cols="80">' + jQuery('#newVoiceContent'+id).html() + '</textarea>');        

    /* change the link text back to its original label */
    jQuery('#checkLink'+id).text('Check Spelling');
    	if(typeof(DCODE) !== 'undefined' && jQuery("#newVoiceContent" + id).length > 0){
    DCODE.setTags (["LARGE", "MEDIUM", "HR", "B", "I", "U", "S", "UL", "OL", "SUB", "SUP", "QUOTE", "LINK", "IMG", "YOUTUBE", "HELP"]);
		DCODE.activate ("newVoiceContent" + id, false);	
	}
};

/* where the magic happens, checks the spelling or restores the form */
function check_atd(id, isAdmin)
{	
    if(id) id = 'Reply';
	if($('newVoiceContent'+id) != undefined && $('newVoiceContent'+id).value == undefined){
		//restore text area if it is second click
		restoreTextArea(id, isAdmin);
		return;
	}
	
 jQuery(function()
 {
     /* If the text of the link says edit comment, then restore the textarea so the user can edit the text */
     if (jQuery('#checkLink'+id).text() == 'Edit Text'){    	 
    	 check_atd('', isAdmin); 
     } 
     else 
     {      	 
         /* set the spell check link to a link that lets the user edit the text */
         jQuery('#checkLink'+id).text('Edit Text');

         /* disable the spell check link while an asynchronous call is in progress. if a user tries to make a request while one is in progress
            they will lose their text. Not cool! */
         var disableClick = function() { return false; };
         jQuery('#checkLink'+id).click(disableClick);
         
         /* replace the textarea with a preview div, notice how the div has to have the same id/class/style attributes as the textarea */
         jQuery('#newVoiceContent'+id).replaceWith('<div style="height:186px;" class="field textarea clearfix" id="newVoiceContent'+id+'">' + jQuery('#newVoiceContent'+id).val() + '</div>');

         /* check the writing in the textarea */
         AtD.checkCrossAJAX('newVoiceContent'+id,  
         {
             ready: function(errorCount)
             {
                /* this function is called when the AtD async service request has finished. 
                   this is a good time to allow the user to click the spell check/edit text link again. */
                jQuery('#checkLink'+id).unbind('click', disableClick);
             },

             success: function(errorCount) 
             {
                if (errorCount == 0)
                {
                   alert(jav_text_checkspelling_no_error);				   			   
                }                
                /* once all errors are resolved, this function is called, it's an opportune time
                   to restore the textarea */
                restoreTextArea(id, isAdmin);
             },

             error: function(reason)
             {
                jQuery('#checkLink'+id).unbind('click', disableClick);

                alert("There was an error communicating with the spell checking service.\n\n" + reason);

                /* restore the text area since there won't be any highlighted spelling errors */
                restoreTextArea(id, isAdmin);
             },

             editSelection : function(element)
             {
                var text = prompt( "Replace selection with:", element.text() );
                if (text != null)
                   element.replaceWith( text );                   
             }
         });
     }
 });
}