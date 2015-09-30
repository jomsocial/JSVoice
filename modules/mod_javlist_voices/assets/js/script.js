/**
 * $JA#COPYRIGHT$
 */
(function($){
	javm_slide = function(modules_id,display_items){
		$("#javm_slide"+modules_id).bxSlider({
		    slideWidth : $("#javm_slide"+modules_id).width()/display_items,
		    minSlides  : display_items,
		    maxSlides  : display_items,
		    moveSlides : 1,
		    pager : false,
		    controls:true,
		    onSliderLoad : function(){
		    	$("#javm_slide"+modules_id+" .slide").equalHeight();
		    },
		    hideControlOnEnd : true,
		    infiniteLoop:false,
		    autoStart : false
		});	
	}
})(jQuery);