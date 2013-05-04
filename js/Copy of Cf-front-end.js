/**
 * Javascript for frontend
 */

/*
jQuery.fn.reset = function () {
  $(this).each (function() { this.reset(); });
};
*/

jQuery(document).ready(function($){
	
	//WhiteBoard Button's functionality
	$('.athlates-white-board h1').bind('click', function(){
		var popup_id = $(this).attr('id');
		popup_id = '#w' + popup_id;
		$(popup_id).toggleClass('whiteboard-window-shown');
	});
	
	var selected_table_id = null;
	
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
					
					selected_table_id = new_entry_id;
					
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
	
	
	//if + button is pressed and the other actions
	$('.white-board-entry-add').bind('click', function(){
		
		
		
		var selected_id = $(this).attr('id');
		var attributes = selected_id.split('_');
		var post_id = attributes[1];
		var selected_class_name = $('#selected-class-name_'+post_id).val();
		selected_class_name = selected_class_name.replace(" ", '-');
		var new_entry_table_id = '#whiteboard-new-entries-' + selected_class_name + '_' + post_id;		
		//$(new_entry_table_id).removeClass('whiteboard-entries');
		
		$(selected_table_id).fadeOut('slow', function(){
			$(new_entry_table_id).removeClass('whiteboard-entries');
		});		
		
		$('.cancel').bind('click', function(){			
			$(selected_table_id).fadeIn('slow', function(){
				$(new_entry_table_id).addClass('whiteboard-entries');
			});
		});
		
		
		var ajax_request = 0;
		
		//for submittion class
		$('.whiteboardNewEntriesSubmitted').on('submit', function(){
			
			//check only to send ajax reqest once
			if(ajax_request > 0) return false;			
			ajax_request = ajax_request + 1;
			
			html_form_id = '#' + $(this).attr('id');
						
			$.ajax({
				type: 'post',
				url: AthlatesAjax.ajaxurl,
				cache: false,
				timeout: 10000,
				
				data :{
					action : 'athlates_records_submitted',
					form_data: $(this).serializeArray()
				},
				
				success: function(result){
					
					var result = jQuery.parseJSON(result);
					
					if(result.status > 0){
						var new_tr = result.data;
						$(selected_table_id).find('tbody').append(new_tr);	
					}
					else{
						alert('problem occurs');
					}
					
				},					
				
				error: function(jqXHR, textStatus, errorThrown){
					jQuery('#footer').html(textStatus);
					alert(textStatus);
					return false;
				}
			});
					
			return false;
		});	
		
		$(this).off('submit');
	});
	
	
	
});