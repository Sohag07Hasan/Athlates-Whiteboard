/**
 * Javascript for frontend
 */

jQuery(document).ready(function($){
	
	//WhiteBoard Button's functionality
	$('.athlates-white-board h1').bind('click', function(){
		var popup_id = $(this).attr('id');
		popup_id = '#w' + popup_id;
		$(popup_id).toggleClass('whiteboard-window-shown');
	});
	
	//Class functionality to display
	$('.whiteboard-class').bind('click', function(){
		var selected_id = $(this).attr('id');
		var attributes = selected_id.split('-');
		var mclass = attributes[0];
		var post_id = attributes[1];
		var key = attributes[2];
		
		if($('#'+selected_id).hasClass('whiteboard-class-selected')){
			
		}
		else{
			for(i=0; i<3; i++){
				var new_id = '#' + attributes[0] + '-' + attributes[1] + '-' + i;
				var new_entry_id = '#whiteboard-newentry-' + attributes[1] + '-' + i;
				if(key == i){					
					//class name
					$(new_id).toggleClass('whiteboard-class-selected');
					
					//logically athlates profiles are shown
					if($(new_entry_id).hasClass('whiteboard-entries')){
						$(new_entry_id).toggleClass('whiteboard-entries');
					}
				}
				else{
					
					//class name
					if($(new_id).hasClass('whiteboard-class-selected')){
						$(new_id).toggleClass('whiteboard-class-selected');
					}
					
					//logically athlates profiles are shown
					if(!$(new_entry_id).hasClass('whiteboard-entries')){
						$(new_entry_id).toggleClass('whiteboard-entries');
					}
				}
			}
			
		}
	});
});