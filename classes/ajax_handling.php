<?php
/*
 *This class is to handle ajax request
 * **/

class Athlates_whiteboard_ajax_handling{
	
	static function init(){
		//ajax actions to add a record
		add_action('wp_ajax_athlates_contribution', array(get_class(), 'show_athlates_contribution'));
		add_action('wp_ajax_nopriv_athlates_contribution', array(get_class(), 'show_athlates_contribution'));
	}
	
	
	//ajax requested athlates profile returing
	static function show_athlates_contribution(){
		
		$post_id = (int) $_POST['post_id'];		
		
		$user_id = (int) $_POST['user_id'];
		
		$class_name = $_POST['class_name'];
		
		//get the athlate specific class and post data
		$data = self::get_single_athlate_from_single_post($post_id, $user_id);
		
		//get the default post data
		$board_data = Athlatics_Board_Admin::get_white_board($post_id);
		
		
		
		$class_data = $data['records'][$class_name];
		$athlate_name = $data['athlate'];
		
		$url = get_option('siteurl');
		
		$return = array();
			
			ob_start();
		?>
				
		<dl class="athlates-profile-viewing">
			<dt> <?php echo $athlate_name; ?> <a class="athlates-profile-link" href="<?php echo $url . '/athlates/?id=' . $user_id; ?>"> view profile </a> </dt>
				<hr />
				
				<?php foreach($class_data['components'] as $key => $com) : ?>
					<dt> <?php echo $key; ?> </dt>
						<dd>
							<span><?php echo $com['result']; ?></span>
							<span><?php echo ($com['Rx']) ? 'RX' : ''; ?></span>
							<span><?php echo $com['RxScale']; ?></span>
						</dd>
						<hr />
				<?php endforeach; ?>							
		</dl>
				
		<?php 
			$return['profile'] = ob_get_contents();
			ob_end_clean();
			
			ob_start();
		
			foreach($board_data as $key => $b_data){
				
				if($b_data['class'] == $class_name){
					$cell_spacing = count($data['component']) + 1;
					
					?>					
						<tr> 
							<td colspan="<?php echo $cell_spacing; ?>">
								<h4>Email Adress</h4>
								<input type="text" name="email" value="*****************" readonly />
							</td>
						</tr>
						
						<tr>
							<td colspan="<?php echo $cell_spacing; ?>">
								<h4>Name</h4>
								<input name="name" type="text" placeholder="Guest" value="<?php echo $athlate_name; ?>" />
							</td>
						</tr>
						
						<?php foreach($b_data['component'] as $key => $component) : ?>
							<?php
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
						
							<tr>
								<td colspan="<?php echo $cell_spacing; ?>">
									<h4><?php echo $component['name'];?></h4>
									<p><input name="result[<?php echo $component['name']; ?>]" type="text" placeholder="what is your result?" value="<?php echo $result; ?>" > </p>
									<p>
																					
										<span>
											<input <?php checked($Rx); ?>  name="Rx[<?php echo $component['name']; ?>]" type="checkbox" value="" /> Rx
										</span> 
										<span style="margin: 0 20px 0 20px">or</span> 
										<span>
											<input name="RxScale[<?php echo $component['name']; ?>]" type="text" placeholder="How do you scale?" value="<?php echo $RxScale; ?>" />
										</span> 												 
										
									</p>
								</td>
							</tr>									
						<?php endforeach;?>					
					
					<?php 
				}
			}
			
			$return['form'] = ob_get_contents();
			ob_end_clean();
		
			echo json_encode($return);
		
		exit;
	}
	
	
	//return the single athlate data for a specific posts
	static function get_single_athlate_from_single_post($post_id, $user_id){
		global $wpdb;
		$tables = Athlatics_Board_Admin::get_tables();
		extract($tables);
		
		//$records = $wpdb->get_results("SELECT user_id, log FROM $user_meta WHERE post_id = '$post_id' ORDER BY time ASC");
		$sql = "SELECT $user_meta.log, $user.name FROM $user_meta INNER JOIN $user ON $user_meta.user_id = $user.id WHERE $user_meta.post_id = '$post_id' AND $user.id = '$user_id'";
		
	//	$records = $wpdb->get_results("SELECT $user_meta.user_id, $user_meta.log, $user.name FROM $user_meta INNER JOIN $user ON $user_meta.user_id = $user.id WHERE $user_meta.post_id = '$post_id' ORDER BY $user_meta.time ASC");
		
		$record = $wpdb->get_row($sql);
		$structured_record = array();
		
		
		
		
		if($record){
			if(is_array(unserialize($record->log))){
				$structured_record['athlate'] = $record->name;
				$structured_record['records'] = unserialize($record->log);
			}
		}
		
		
		return $structured_record;
	}
	
	
}