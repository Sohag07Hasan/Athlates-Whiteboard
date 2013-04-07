/*
 * written by Mahibul Hasan SOhag
 * */

jQuery(document).ready(function($){
	
	//if the whiteboard is clicked in a post
	$('.athlates-white-board h1').bind('click', function(){
				
		//Doing hidden and shown the whole process
		var whiteboard = $(this).parent().siblings('.athlates-white-board-popup');
		var popup_window = whiteboard.children('.whiteboard-window');
		popup_window.toggleClass('whiteboard-window-hidden');
		
		
		//now class toggling
		$('td.whiteboard-class').bind('click', function(){
			var post_id = $(this).attr('post_id');
			var class_name = $(this).attr('whiteboard_class');
			
			//setting the current class name
			$('input[post_id|="'+post_id+'"]').attr('class_name', class_name);
			
			//hide the all blank form
			$('.new-entry-form').addClass('hidden-entries');
			
			//finding the exact matched table and toggle them
			var active_table_id = class_name.replace('/[ ]/', '-') + '_' + post_id;			
			$('table[post_id|="'+post_id+'"]').addClass('hidden-entries');					
			$('#' + active_table_id).removeClass('hidden-entries');
			
			//doing the same thing for the td of the main class
			$(this).siblings().removeClass('selected-class');
			$(this).addClass('selected-class');
			
		});
		
		
		//now if the + button is pressed
		$('span.add-new-entry').bind('click', function(){
			var post_id = $(this).parent().attr('post_id');
			var class_name = $('input[post_id|="'+post_id+'"]').attr('class_name');
			var table_id = class_name.replace('/[ ]/', '-') + '_' + post_id;
			var form_id = 'form_' + table_id;
			
			//toggle the style
			toggleTableForm(table_id, form_id, 'hidden-entries');
			
			
			//now if cancel is cliked
			$('input.cancel').bind('click', function(){
				toggleTableForm(form_id, table_id, 'hidden-entries');
			});
			
			
						
		});
		
		
		var toggleTableForm = function(first, second, class_name){
			$('#' + first).addClass(class_name);
			$('#' + second).removeClass(class_name);
		}
		
		
	});
});