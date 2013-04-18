
<?php 
	$board_data = get_post_meta($post->ID, 'Athlates_White_Board', true);
		
?>


<div class='wrap'>
	
	<h4>Manage Athlates WhiteBoard</h4>
	<p>Classes Scheduled for this date</p>
	
	<?php 
		if($board_data): 
				
		foreach($board_data as $key => $data){
	?>
			 
			 <div class="athlates-white-board-parent-class">
			 	<p class="board-class-name"> Class Name <input size="50" type="text" recname="cllassname" name="class-names[]" value="<?php echo $data['class']; ?>" ></p>
				
				<table>
					
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
									<td><input type="text" recname="componentname1" name="component-names[]" value="<?php echo $component['name'] ?>" /></td>
									<td><textarea rows="2" cols="50" recname="componentdes1" name="component-descriptions[]"><?php echo $component['des']; ?></textarea></td>
									<td class="add-remove-action"> <span class="remove-component">Remove</span></td>
								</tr>
								
								<?php 
							}
						}
					?>
						
								<tr>
									<td>Component </td>
									<td><input type="text" recname="componentname1" name="component-names[]" value="" /></td>
									<td><textarea rows="2" cols="50" recname="componentdes1" name="component-descriptions[]"></textarea></td>
									<td class="add-remove-action"> <span class="remove-component">Remove</span> &nbsp; &nbsp;<span class="add-component">Add New</span> </td>
								</tr>
										
				</table>	
				
				<p class="add-remove-classes">  <span class="remove-class">Remove Class</span> </p>
										
			 </div>	 

	<?php
		} 
		endif;
	 ?>
		
		<div class="athlates-white-board-parent-class">
				 	<p class="board-class-name"> Class Name <input size="50" type="text" recname="cllassname" name="class-names[]" value="" ></p>
					
					<table>
						
						<tr>
							<td>&nbsp</td>
							<td>Component Name</td>
							<td>Component Description</td>
						</tr>
						<tr>
							<td>Component</td>
							<td><input type="text" recname="componentname1" name="component-names[]" value="" /></td>
							<td><textarea rows="2" cols="50" recname="componentdes1" name="component-descriptions[]"></textarea></td>
							<td class="add-remove-action"> <span class="remove-component">Remove</span> &nbsp; &nbsp;<span class="add-component">Add New</span> </td>
						</tr>
											
					</table>	
				<p class="add-remove-classes"> <span class="remove-class">Remove Class</span> &nbsp; &nbsp; &nbsp; <span class="add-class">Add Class</span> </p>
											
		</div>	 
	
	
</div>

<!-- 

<script type="text/javascript">
	jQuery('#athlates-white-board-parent-id').EnableMultiField({
		 data:[
				 <?php if($board_data){
				 		foreach($board_data as $key => $data){
				 			$com_name_1 = ($data['component'][0]['name']) ? $data['component'][0]['name'] : '';
				 			$com_des_1 = ($data['component'][0]['des']) ? $data['component'][0]['des'] : '';
				 			$com_name_2 = ($data['component'][1]['name']) ? $data['component'][1]['name'] : '';
				 			$com_des_2 = ($data['component'][1]['des']) ? $data['component'][1]['des'] : '';
				 			$com_name_3 = ($data['component'][2]['name']) ? $data['component'][2]['name'] : '';
				 			$com_des_3 = ($data['component'][2]['des']) ? $data['component'][2]['des'] : '';
						 	?>
						 	{
						       cllassname : "<?php //echo $data['class'];?>",
						       componentname1 : "<?php // echo $com_name_1;?>",
						       componentdes1 : "<?php //echo $com_des_1;?>",
						       componentname2 : "<?php //echo $com_name_2;?>",
						       componentdes2 : "<?php //echo $com_des_2;?>",
						       componentname3 : "<?php //echo $com_name_3; ?>",
						       componentdes3 : "<?php //echo $com_des_3; ?>"
						    },
						 	<?php 
					 	}
				 }
				 ?>
		       
		]
	});
			
			
</script>

 -->
