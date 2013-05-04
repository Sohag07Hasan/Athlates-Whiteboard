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
		
		var empty_component = '<tr><td>Component </td><td><input type="text" recname="componentname1" name="component-names[key][]" value="" /></td>	<td><textarea rows="2" cols="50" recname="componentdes1" name="component-descriptions[]"></textarea></td><td class="add-remove-action"> <span class="remove-component">Remove</span></td></tr>';
		$(this).parent().parent().before(empty_component);
		
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
		var empty_class = '<div class="athlates-white-board-parent-class">	<p class="board-class-name"> Class Name <input size="50" type="text" recname="cllassname" name="class-names[]" value="" ></p> <table><tr><td>&nbsp</td>	<td>Component Name</td>	<td>Component Description</td> 	</tr><tr><td>Component</td>	<td><input type="text" recname="componentname1" name="component-names[]" value="" /></td><td><textarea rows="2" cols="50" recname="componentdes1" name="component-descriptions[]"></textarea></td><td class="add-remove-action"> <span class="remove-component">Remove</span> &nbsp; &nbsp;<span class="add-component">Add New</span> </td>	</tr></table><p class="add-remove-classes"> <span class="remove-class">Remove Class</span> </p></div>';
		$(this).parent().parent().before(empty_class);		
	});
	
});