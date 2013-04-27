
<?php 
	$board_data = get_post_meta($post->ID, 'Athlates_White_Board', true);
		
?>


<div class='wrap' id="metabox-to-post-holder" />
	
	<input type="hidden" id="whiteboard-tinymce-button" value="<?php echo ATHLATESWHITEBOARD_URL . 'images/cfw-mce-button-130113.png'; ?>" />
		
	<h4>Manage Athlates WhiteBoard</h4>
		
	<?php 
		if($board_data): 
				
		//return var_dump($board_data);
		
		foreach($board_data as $key => $data){
	?>
			 
			 <div class="athlates-white-board-parent-class">
			 	<p class="board-class-name"> Class Name <input class="ahlete-class" size="50" type="text" name="class-names[<?php echo $key; ?>]" value="<?php echo $data['class']; ?>" ></p>
				
				<table class="component-holder-table">
					
					<tr>
						<td>&nbsp</td>
						<td>Component Name</td>
						<td>Component Description</td>
					</tr>
					
					<?php 
						if($data['component']){
							foreach($data['component'] as $k => $component){
								
								?>
								
								<tr>
									<td>Component </td>
									<td><input class="class-component" type="text" name="component-names[<?php echo $key; ?>][]" value="<?php echo $component['name'] ?>" /></td>
									<td><textarea class="component-description" rows="2" cols="50" name="component-descriptions[<?php echo $key; ?>][]"><?php echo $component['des']; ?></textarea></td>
									<td class="add-remove-action"> <span class="remove-component">Remove</span></td>
								</tr>
								
								<?php 
							}
						}
					?>
						
								<tr>
									<td>Component </td>
									<td><input class="class-component" type="text" name="component-names[<?php echo $key; ?>][]" value="" /></td>
									<td><textarea class="component-description" rows="2" cols="50" name="component-descriptions[<?php echo $key; ?>][]"></textarea></td>
									<td class="add-remove-action" key="<?php echo $key; ?>" > <span class="remove-component">Remove</span> &nbsp; &nbsp;<span class="add-component">Add New</span> </td>
								</tr>
										
				</table>	
				
				<p class="add-remove-classes" key="<?php echo $key; ?>" >  <span class="remove-class">Remove Class</span> </p>
										
			 </div>	 

	<?php
		} 
		endif;
	 ?>
		
		<div class="athlates-white-board-parent-class">
				 	<p class="board-class-name"> Class Name <input class="ahlete-class" size="50" type="text" name="class-names[<?php echo $key+1; ?>]" value="" ></p>
					
					<table class="component-holder-table">
						
						<tr>
							<td>&nbsp</td>
							<td>Component Name</td>
							<td>Component Description</td>
						</tr>
						<tr>
							<td>Component</td>
							<td><input class="class-component" type="text" name="component-names[<?php echo $key+1; ?>][]" value="" /></td>
							<td><textarea class="component-description" rows="2" cols="50" name="component-descriptions[<?php echo $key+1; ?>][]"></textarea></td>
							<td class="add-remove-action" key="<?php echo $key+1; ?>" > <span class="remove-component">Remove</span> &nbsp; &nbsp;<span class="add-component">Add New</span> </td>
						</tr>
											
					</table>	
				<p class="add-remove-classes" key="<?php echo $key + 1; ?>" > <span class="remove-class">Remove Class</span> &nbsp; &nbsp; &nbsp; <span class="add-class">Add Class</span> </p>
											
		</div>	 
	
	
</div>

