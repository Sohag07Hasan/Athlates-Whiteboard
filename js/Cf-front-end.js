/**
 * Javascript for frontend
 */

jQuery(document).ready(function($){
	$('.athlates-white-board h1').bind('click', function(){
		var popup_id = $(this).attr('id');
		popup_id = '#w' + popup_id;
		$(popup_id).toggleClass('whiteboard-window-shown');
	});	
});