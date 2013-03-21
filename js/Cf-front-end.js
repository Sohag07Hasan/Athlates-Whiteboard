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
			
			//new entries table are made hidden
			$('.althlates-new-entry').addClass('whiteboard-entries');
		}
		else{
			
			//new entries table are amde hidden
			$('.althlates-new-entry').addClass('whiteboard-entries');
			
			for(i=0; i<3; i++){
				var new_id = '#' + attributes[0] + '-' + attributes[1] + '-' + i;
				var new_entry_id = '#whiteboard-entries-' + attributes[1] + '-' + i;
				if(key == i){					
					//class name
					$(new_id).toggleClass('whiteboard-class-selected');
					
					//logically athlates profiles are shown
					if($(new_entry_id).hasClass('whiteboard-entries')){
						$(new_entry_id).toggleClass('whiteboard-entries');
						
						//setup selected valudes
						var selected_class_id = "#selected-class-name_" + post_id;
						var selected_class_name = $('#Classname-'+post_id+'-'+key).val();
						$(selected_class_id).val(selected_class_name);
						//alert(selected_class_name);
						
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
	
	
	//if + button is pressed
	$('.white-board-entry-add').bind('click', function(){
		var selected_id = $(this).attr('id');
		var attributes = selected_id.split('_');
		var post_id = attributes[1];
		var selected_class_name = $('#selected-class-name_'+post_id).val();
		selected_class_name = selected_class_name.replace(" ", '-');
		var new_entry_table_id = '#whiteboard-new-entries-' + selected_class_name + '_' + post_id;
		//$('.althlates-new-entry').addClass('whiteboard-entries');
		$(new_entry_table_id).removeClass('whiteboard-entries');
	});
	
});