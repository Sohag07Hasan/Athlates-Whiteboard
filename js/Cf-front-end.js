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
			
			//hide the all blank form and unbind the cancel click
			$('.new-entry-form').addClass('hidden-entries');
			
			//unbinding events
			$('input.cancel').unbind('click');
			//$('input.entry-from-submit-button').unbind('click');
			
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
			
			//unbinding the events
			$('input.entry-from-submit-button').unbind('click');
			
			//toggle the style
			toggleTableForm(table_id, form_id, 'hidden-entries');
			
			
			$('#' + form_id).children().find('input.cancel').bind('click', function(){
				toggleTableForm(form_id, table_id, 'hidden-entries');
			});
						
			
			//again binding the submit button
			$('input.entry-from-submit-button').bind('click', function(){
				ajax_handling($('#' + form_id), $('#' + table_id));
				toggleTableForm(form_id, table_id, 'hidden-entries');
			});
			
												
		});
		
		
		/*
		 *  when someone clicks more to show the athlates contribution
		 * */
		$('td.whiteboard-more').bind('click', function(){
						
			var parent_table = $(this).parents('table');
			var class_name = parent_table.attr('class_name');
			var post_id = parent_table.attr('post_id');
			var current_table_id = parent_table.attr('id');
			var user_id = $(this).attr('user_id');
			
			//ajax showing div
			var specific_div = $('div[post_id="'+post_id+'"]').filter('.ajax-showing-div');
			var specific_actions_div = $('div[post_id="'+post_id+'"]').filter('.ajax-showing-div-actions');
			
			
			//ajax info editing form
			var specific_form = $('form[post_id="'+post_id+'"]').filter('.ajax-profile-info-editing-form');
			
			
			//unbinding some events
			$('.athlates-profile-back').unbind('click');
			$('.unlock-profile-info').unbind('click');
						
			
			//ajax requesting
			$.ajax({
				type: 'post',
				url: AthlatesAjax.ajaxurl,
				cache: false,
				timeout: 10000,
				
				data :{
					action: 'athlates_contribution',
					class_name: class_name,
					post_id: post_id,
					user_id: user_id
				},
				
				success: function(result){
					
					var result = jQuery.parseJSON(result);
					
					$('#' + current_table_id).addClass('hidden-entries');					
					 specific_div.html(result.profile);
					 specific_form.children('table.ajax-profile-info-editing-form-container').children('tbody').html(result.form);
					 specific_div.show();
					 specific_actions_div.show();
					
					//$('#colophon').html(result);
													
															
				},
				
				error: function(jqXHR, textStatus, errorThrown){
					jQuery('#site-generator').html(textStatus);
					alert(textStatus);
					return false;
				}
				
			});
			
			
			//now if the back link is clicked
			$('.athlates-profile-back').bind('click', function(){
				
				specific_div.hide();
				specific_actions_div.hide();
				specific_div.html(null);
				specific_form.children('table.ajax-profile-info-editing-form-container').children('tbody').html(null);
				$('#' + current_table_id).removeClass('hidden-entries');
				
			});
			
			
			//if edit button is clicked
			$('.unlock-profile-info').bind('click', function(){
				specific_actions_div.hide();
				specific_div.hide();
				
				//another little form pops up
				var email_form = $('form[post_id="'+post_id+'"]').filter('.ajax-email-verify-form');
				email_form.show();
				
				
				var cancel_button = email_form.find('input[value="cancel"]');
				var verify_button = email_form.find('input[value="verify"]');
				
				//cancel the verificaton process
				cancel_button.unbind('click');
				cancel_button.bind('click', function(){
					email_form.hide();
					specific_actions_div.show();
					specific_div.show();
				});
				
				
				//if someone wants to verify the email
				verify_button.unbind('click');
				verify_button.bind('click', function(){
					var input_email = email_form.find('input[name="ajax-email"]').val();
					
					var email_pattern = /^(?!\.)[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/g;
					if(email_pattern.test(input_email)){
						var presaved_email = specific_form.children('table.ajax-profile-info-editing-form-container').children('tbody').children('tr:first').find('input:first').attr('value');
						if(presaved_email == input_email){
							email_form.hide();
							specific_form.show();
						}
						else{
							var message = "Ooops! Email address did not match!";
							email_form.find('p[class="email-veirfication-message"]').html(message);
						}
					}
					else{						
						var message = "Ooops! Invalid Email address";
						email_form.find('p[class="email-veirfication-message"]').html(message);
					}
				});
												
				
				//now if cancle is clicked
				$('input.edit-form-cancel').unbind('click');
				$('input.edit-form-cancel').bind('click', function(){
					specific_form.hide();
					specific_actions_div.show();
					specific_div.show();
				});
			});
			
			
		});
		
		
		
		
		
		
		var toggleTableForm = function(first, second, class_name){
		//	alert(first);
		//	alert(second);
			$('#' + first).addClass(class_name);
			$('#' + second).removeClass(class_name);
		};
		
		
		//ajax handling
		var ajax_handling = function(form, table){	
			
			$.ajax({
				type: 'post',
				url: AthlatesAjax.ajaxurl,
				cache: false,
				timeout: 10000,
				
				data :{
					action : 'athlates_records_submitted',
					form_data: form.serializeArray()
				},
				
				success: function(result){
									
					var result = jQuery.parseJSON(result);
					
					//jQuery('#site-generator').html(result.data);		
				
					var new_tr = result.data;
					table.find('tbody').append(new_tr);	
					
					
															
				},					
				
				error: function(jqXHR, textStatus, errorThrown){
					jQuery('#site-generator').html(textStatus);
					alert(textStatus);
					return false;
				}
			});
		};
		
	});
	
});

