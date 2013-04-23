/**
 *  form extender actions
 */
jQuery(document).ready(function($){
	
	//component removal
	$('span.remove-component').unbind('click');
	$('span.remove-component').live('click', function(){
		if($(this).siblings().size() == 0){
			$(this).parent().parent().remove();
		}
	});
	
	
	//component add
	$('span.add-component').unbind('click');
	$('span.add-component').live('click', function(){		
		
		var key = $(this).parent().attr('key');
		
		var empty_component = '<tr><td>Component </td><td><input type="text" recname="componentname1" name="component-names[key][]" value="" /></td>	<td><textarea rows="2" cols="50" recname="componentdes1" name="component-descriptions[key][]"></textarea></td><td class="add-remove-action"> <span class="remove-component">Remove</span></td></tr>';
		var new_empty_component = empty_component.replace(/key/g, key);		
		
		$(this).parent().parent().before(new_empty_component);
		
	});
	
	
	
	//Class Removal
	$('span.remove-class').unbind('click');
	$('span.remove-class').live('click', function(){
		if($(this).siblings().size() == 0){
			$(this).parent().parent().remove();
		}
	});
	
	
	//new class add
	$('span.add-class').unbind('click');
	$('span.add-class').live('click', function(){
		
		var key = $(this).parent().attr('key');
				
		var new_key = key/1 + 1;
		
		var empty_class = '<div class="athlates-white-board-parent-class">	<p class="board-class-name"> Class Name <input size="50" type="text" recname="cllassname" name="class-names[key]" value="" ></p> <table><tr><td>&nbsp</td>	<td>Component Name</td>	<td>Component Description</td> 	</tr><tr><td>Component</td>	<td><input type="text" recname="componentname1" name="component-names[key][]" value="" /></td><td><textarea rows="2" cols="50" recname="componentdes1" name="component-descriptions[key][]"></textarea></td><td class="add-remove-action"> <span class="remove-component">Remove</span> &nbsp; &nbsp;<span class="add-component">Add New</span> </td>	</tr></table><p class="add-remove-classes"> <span class="remove-class">Remove Class</span> </p></div>';
		
		empty_class = empty_class.replace(/key/g, new_key);
		
		$(this).parent().attr('key', new_key);
		
		$(this).parent().parent().before(empty_class);			
		
	});
	
	
	
	//metabox to posts
	$('#metabox-to-post').unbind('click');
	$('#metabox-to-post').bind('click', function(){
		
		alert('under construction');
		return false;
		
		var parent_wrap = $(this).parents('div#metabox-to-post-holder');
		//var inputs = parent_wrap.find('input[type="text"]');
		var inputs = parent_wrap.find('input[type="text"]').serializeArray();
		var textareas = parent_wrap.find('textarea').serializeArray();
		
		
		
		$.ajax({
			type: 'post',
			url: AthlatesAjaxAdmin.ajaxurl,
			cache: false,
			timeout: 10000,
			
			data :{
				action: 'metabox_to_post',
				inputs: inputs,
				textareas: textareas
			},
			
			success: function(result){
				
			//	var result = jQuery.parseJSON(result);
				$('#wpfooter').html(result);
												
														
			},
			
			error: function(jqXHR, textStatus, errorThrown){
				jQuery('#site-generator').html(textStatus);
				alert(textStatus);
				return false;
			}
			
		});	
		
		/*
		for(i=0; i<inputs.length; i++){
			var name = inputs.eq(i).attr('name');
			var value = inputs.eq(i).attr('value');
			
			check[] = [name, value];
		}
		
		$('#wpfooter').html(jsoncheck);
		*/
	});
	
});