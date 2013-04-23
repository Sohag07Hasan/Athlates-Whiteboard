/**
 * tiny mce script
 */

jQuery(document).ready(function($) {

    tinymce.create('tinymce.plugins.AtheleteWhieBoard', {
        init : function(ed, url) {
                // Register command for when button is clicked
                ed.addCommand('insert_metabox', function() {
                   
                	content = metabox_string(),
                	
                    tinymce.execCommand('mceInsertContent', false, content);
                });

            // Register buttons - trigger above command when clicked
            ed.addButton('whiteboard_button', {title : 'Insert Whiteboard', cmd : 'insert_metabox', image: 'http://localhost/wordpress/wp-content/plugins/athlatics-board' + '/images/cfwhiteboard.png' });
        }  
    });

    // Register our TinyMCE plugin
    // first parameter is the button ID1
    // second parameter must match the first parameter of the tinymce.create() function above
    tinymce.PluginManager.add('whiteboard_button', tinymce.plugins.AtheleteWhieBoard);
    
    
    //return the metabox data as string
    var metabox_string = function(){
    	
    	var parent_wrap = $('div#metabox-to-post-holder');
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
		return str;
    };
    
});