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
		
		var empty_component = '<tr><td>Component </td><td><input class="class-component" type="text" name="component-names[key][]" value="" /></td>	<td><textarea class="component-description" rows="2" cols="50" recname="componentdes1" name="component-descriptions[key][]"></textarea></td><td class="add-remove-action"> <span class="remove-component">Remove</span></td></tr>';
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
		
		var empty_class = '<div class="athlates-white-board-parent-class">	<p class="board-class-name"> Class Name <input class="ahlete-class" size="50" type="text" name="class-names[key]" value="" ></p> <table class="component-holder-table"><tr><td>&nbsp</td>	<td>Component Name</td>	<td>Component Description</td> 	</tr><tr><td>Component</td>	<td><input type="text" recname="componentname1" name="component-names[key][]" value="" /></td><td><textarea rows="2" cols="50" recname="componentdes1" name="component-descriptions[key][]"></textarea></td><td class="add-remove-action"> <span class="remove-component">Remove</span> &nbsp; &nbsp;<span class="add-component">Add New</span> </td>	</tr></table><p class="add-remove-classes"> <span class="remove-class">Remove Class</span> </p></div>';
		
		empty_class = empty_class.replace(/key/g, new_key);
		
		$(this).parent().attr('key', new_key);
		
		$(this).parent().parent().before(empty_class);			
		
	});
	
	
	$('#metabox-to-post').unbind('click');
	$('#metabox-to-post').live('click', function(){
		var parent_wrap = $(this).parents('div#metabox-to-post-holder');
		var class_inputs = parent_wrap.find('input.ahlete-class');
		
		var str = '<p>';
		
		//class inputs 
		for(i=0; i<class_inputs.length; i++){
			
			if(class_inputs.eq(i).val().length > 0){
				
				str += '<strong>' + class_inputs.eq(i).val() + '</strong><br/>';
				
				var holder_table = class_inputs.eq(i).parent().siblings('table.component-holder-table');
				var components = holder_table.find('input.class-component');
				var descriptions = holder_table.find('textarea.component-description');
				
				for(j=0; j<components.length; j++){
					//alert(components.eq(j).val());
					if(components.eq(j).val().length > 0){
						str += components.eq(j).val() + '<br/>';
						str += descriptions.eq(j).val() + '<br/>';
					}
				}
			}
					
		}
		
		str += '</p>';
		
		var t = $('body#tinymce').html();
		alert(t);
	//	$('#wpfooter').html(str);
		
	});
		
});