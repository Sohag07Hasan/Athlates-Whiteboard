<?php 
	/*
	if($_POST['athlete-log-by-admin-saved']){
				
		$sanitized = array();		
		if(is_array($_POST['class'])){
			foreach($_POST['class'] as $class_name => $class){
				
				$components = array();
				
				foreach($class['components'] as $com => $details){
					if(empty($details['result']) && empty($details['Rx']) && empty($details['RxScale'])) continue;
					$components[$com] = $details;
				}
				
				if(empty($components)) continue;
				
				$sanitized[$class_name] = array(
					'log_time' => current_time('timestamp'),
					'components' => $components
				);
			}
		}
		
		if($sanitized){
			
		}
	}
	*/

	$athlete = self::get_an_athlete($_POST['athlete']);
	if(isset($athlete['athlete'])){
		$name = $athlete['athlete']->name;
		$athlete_id = $athlete['athlete']->id;
		$email = $athlete['athlete']->email;
	}

	$post_id = $_POST['wod-post-number'];
	$post = get_post($post_id);
	
	if(empty($post)){
		echo "invalid post selected"; return;
	}
		
	//$action = admin_url('admin.php?page=athletes-register-add&type=editlog&phase=2&athlete='.$_POST['athlete']);
	
	$data = Athlates_whiteboard_ajax_handling::get_single_athlate_from_single_post($post->ID, $athlete_id);
	$board_data = Athlatics_Board_Admin::get_white_board($post_id);
		
?>


<style>

	div.single-class{
		padding: 10px;
	}
	
	div.component-holder{
		padding: 5px;
	}
	
	h3.post-title{
		font-size: 18px;
	}
	
	p.class-title{
		font-size: 16px;
	}
	
	span.component-title{
		font-size: 14px;
	}
	
</style>

<div class="wrap">
	<div id="icon-users" class="icon32"></div>
	<h2>Edit Athlete Workouts</h2>
	
	<?php if($_POST['athlete-log-by-admin-saved']):	?>
		<div class="updated"> <p>saved</p> </div>
	<?php endif; ?>
	
	
	<table class="form-table">
		<tbody>		
			<tr>
				<th scope="row">Athlete's Name</th>
				<td><?php echo $name; ?></td>
			</tr>
			
			<tr>
				<th scope="row">Athlete's Email</th>
				<td><?php echo $email; ?></td>
			</tr>
							
		</tbody>
	</table>
	
	<hr />
	<div> &nbsp </div>
	
	<h3 class="post-title"><?php echo $post->post_title; ?></h3>
	
	<form action="" method="post">
		<input type="hidden" name="athlete" value="<?php echo $_POST['athlete']; ?>" />	
		<input type="hidden" name="wod-post-number" value="<?php echo $post->ID ?>" />	
		<input type="hidden" name="fetch-log" value="Y" />
		
		<input name="athlete-log-by-admin-saved" value="Y" type="hidden" />
				
		<?php 
			if($board_data){
				foreach($board_data as $key => $b_data){
					$class_data = $data['records'][$b_data['class']];
					
			//		echo '<p class="class-title">' . $b_data['class'] . '</p>';
					
					if($b_data['component']){
						
						?>
						
						<div class="single-class">
							
							<p class="class-title">Class: <?php echo $b_data['class']; ?></p>
													
							<?php 
								foreach($b_data['component'] as $key => $component){
									if(isset($class_data['components'][$component['name']])){
										$result = $class_data['components'][$component['name']]['result'];
										$Rx = isset($class_data['components'][$component['name']]['Rx']) ? true : false;
										$RxScale = $class_data['components'][$component['name']]['RxScale'];											
									} 
									else{
										$result = '';
										$Rx = false;
										$RxScale = '';
									}
									
									?>
									
									<div class="component-holder">
									
										<span class="component-title">Component: <?php echo $component['name']; ?></span> 									
										
										<table>
																				
											<tr>
												<th scope="row">Result</th>
												<td>
													<input name="class[<?php echo $b_data['class']; ?>][components][<?php echo $component['name']; ?>][result]" type="text" placeholder="what is athlete's result?" value="<?php echo $result; ?>" >
												</td>
											</tr>
											
											<tr>
												<th scope="row">Rx</th>
												<td>
													<input <?php checked($Rx); ?>  name="class[<?php echo $b_data['class']; ?>][components][<?php echo $component['name']; ?>][Rx]" type="checkbox" value="on" />
												</td>
											</tr>
											
											<tr>
												<th scope="row">Rx Scale</th>
												<td>
													<input name="class[<?php echo $b_data['class']; ?>][components][<?php echo $component['name']; ?>][RxScale]" type="text" placeholder="How do you scale?" value="<?php echo $RxScale; ?>" />
												</td>
											</tr>
											
										</table>
									</div>
									
									<?php 
								}
							?>
							
							</div>
						
						<?php 
						
					}
				}
			}		
		?>
		
		<p><input class="button button-primary" type="submit" value="Save" /></p>
									
	</form>
	
	
</div>