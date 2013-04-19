<?php
/*
 *This class is to handle ajax request
 * **/

class Athlates_whiteboard_ajax_handling{
	
	static $post_id = 0;
	static $user_id = 0;
	static $class_name = '';
	
	static function init(){
		//ajax actions to show an athlates contibutions for a specific class and posts
		add_action('wp_ajax_athlates_contribution', array(get_class(), 'show_athlates_contribution'));
		add_action('wp_ajax_nopriv_athlates_contribution', array(get_class(), 'show_athlates_contribution'));
		
		
		//ajax actions to email verification
		add_action('wp_ajax_athlates_email_verify', array(get_class(), 'athlates_email_verify'));
		add_action('wp_ajax_nopriv_athlates_email_verify', array(get_class(), 'athlates_email_verify'));
		
		
		//visitors profiles are updated
		add_action('wp_ajax_athlates_records_updated', array(get_class(), 'athlates_records_updated'));
		add_action('wp_ajax_nopriv_athlates_records_updated', array(get_class(), 'athlates_records_updated'));
		
		
		//athlete's directory
		add_shortcode('athletes_directory', array(get_class(), 'athletes_directory'));
		
	}
	
	
	
	/*
	 * Athlates profile should be updated
	 * */
	static function athlates_records_updated(){
		$data = $_POST['form_data'];
			
		if(is_array($data)){
			$new_data = array();
			foreach ($data as $key => $value){
				$new_data[$value['name']] = $value['value'];
			}
			
			//var_dump($new_data); exit();
			
			$post_id = $new_data['post_id'];
			$class_name = $new_data['class_name'];
			$email = $new_data['ajax-processed-email'];
			$user_id = $new_data['user_id'];
			
			self::$post_id = $post_id;
			self::$class_name = $class_name;
			self::$user_id = $user_id;
			
			//now board data and updated data are to be compaired
			$board_data = get_post_meta($post_id, 'Athlates_White_Board', true);			
			foreach($board_data as $key => $data){
				if($data['class'] == $class_name){
					$sanitized_data = array();
					foreach ($data['component'] as $d){
						$sanitized_data[$d['name']]['result'] = $new_data['result['.$d['name'].']'];
						$sanitized_data[$d['name']]['Rx'] = $new_data['Rx['.$d['name'].']'];
						$sanitized_data[$d['name']]['RxScale'] = $new_data['RxScale['.$d['name'].']'];
					}			
				}
			}
			
			
			//now database operation
			global $wpdb;
			$tables = Athlatics_Board_Admin::get_tables();
			extract($tables);
			
			$log_row = $wpdb->get_row("SELECT * FROM $user_meta WHERE post_id = '$post_id' AND user_id = '$user_id'");
			$log[$class_name] = array(
					'log_time' => current_time('timestamp'),
					'components' => $sanitized_data
				);
			$wpdb->update($user_meta, array('log'=>serialize($log), 'time'=>current_time('mysql')), array('user_id'=>$user_id, 'post_id'=>$post_id), array('%s', '%s'), array('%d', '%d'));
		}	

		$updated_data = self::show_athlates_contribution(true);
		
		echo json_encode($updated_data);
		exit;
	}
	
	
	
	//ajax requested athlates profile returing
	static function show_athlates_contribution($r = false){
		
		$post_id = (int) $_POST['post_id'];		
		
		$user_id = (int) $_POST['user_id'];
		
		$class_name = $_POST['class_name'];
		
		if($r){
			$post_id = self::$post_id;
			$user_id = self::$user_id;
			$class_name = self::$class_name;
		}
		
		//get the athlate specific class and post data
		$data = self::get_single_athlate_from_single_post($post_id, $user_id);
		
		//get the default post data
		$board_data = Athlatics_Board_Admin::get_white_board($post_id);
		
		
		
		$class_data = $data['records'][$class_name];
		$athlate_name = $data['athlate']['name'];
		$athlate_email = $data['athlate']['email'];
		
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
								<input value="<?php echo $athlate_email; ?>" type="hidden" name="ajax-processed-email" post_id="<?php echo $post_id ?>" user_id="<?php echo $user_id; ?>" />
								<input type="text" name="email" value="*****************" readonly />
								<input type="hidden" value=<?php echo $post_id; ?> name="post_id" />
								<input type="hidden" value=<?php echo $class_name; ?> name="class_name" />
								<input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
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
											<input <?php checked($Rx); ?>  name="Rx[<?php echo $component['name']; ?>]" type="checkbox" value="on" /> Rx
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
			
			if($r){
				return $return;
			}
			
			echo json_encode($return);
			exit;
			
							
	}
	
	
	//return the single athlate data for a specific posts
	static function get_single_athlate_from_single_post($post_id, $user_id){
		global $wpdb;
		$tables = Athlatics_Board_Admin::get_tables();
		extract($tables);
		
		//$records = $wpdb->get_results("SELECT user_id, log FROM $user_meta WHERE post_id = '$post_id' ORDER BY time ASC");
		$sql = "SELECT $user_meta.log, $user.name, $user.email FROM $user_meta INNER JOIN $user ON $user_meta.user_id = $user.id WHERE $user_meta.post_id = '$post_id' AND $user.id = '$user_id'";
		
	//	$records = $wpdb->get_results("SELECT $user_meta.user_id, $user_meta.log, $user.name FROM $user_meta INNER JOIN $user ON $user_meta.user_id = $user.id WHERE $user_meta.post_id = '$post_id' ORDER BY $user_meta.time ASC");
		
		$record = $wpdb->get_row($sql);
		$structured_record = array();
		
		
		
		
		if($record){
			if(is_array(unserialize($record->log))){
				$structured_record['athlate'] = array('name'=>$record->name, 'email'=>$record->email);
				$structured_record['records'] = unserialize($record->log);
			}
		}
		
		
		return $structured_record;
	}
	
	
	
	/* Email verificatio */
	static function athlates_email_verify(){
		$eamil = $_POST['email'];
		$post_id = $_POST['post_id'];
		$user_id = $_POST['user_id'];
		
		echo $user_id;
		exit;
	}
	

	/*Athletes directory*/
	static function athletes_directory(){
		
		global $wpdb, $post;
		
		$tables = Athlatics_Board_Admin::get_tables();
		extract($tables);
		
		
		
		$sql = "SELECT $user_meta.post_id, $user_meta.user_id, $user_meta.log, $user.name FROM $user_meta INNER JOIN $user ON $user_meta.user_id = $user.id ORDER BY $user.name";
		
		//var_dump($sql);
		$raw_athlates = $wpdb->get_results($sql);
		$athlates = array();
		
		if($raw_athlates){
			foreach($raw_athlates as $rath){
				$log = unserialize($rath->log);
				$athlates[$rath->user_id]['name'] = $rath->name;				
				foreach($log as $cn => $value){
					$athlates[$rath->user_id]['time'][] = $value['log_time'];
					$athlates[$rath->user_id]['classes'][$cn] = $value;					
				}
								
			}
		}
		
		
		ob_start();
		include ATHLATESWHITEBOARD_DIR . '/includes/athletes-directory.php';
		$content = ob_get_contents();	
		ob_end_clean();
		
		return $content;
	}
	
	
	//get time interval
	static function get_interval($now, $post_time){
		$datetime1 = new DateTime();
		$datetime1->setTimestamp($now);
		
		$datetime2 = new DateTime();
		$datetime2->setTimestamp($post_time);
		
		$interval = $datetime1->diff($datetime2);
			
		$years = ($interval->y > 0) ? $interval->y . ' years ' : '';
		$months = ($interval->m > 0) ? $interval->m . ' months ' : '';
		$days = ($interval->d > 0) ? $interval->d . ' days ' : '';
		$hours = ($interval->h > 0) ? $interval->h . ' hours ' : '';
		$minutes = ($interval->i > 0) ? $interval->i . ' minutes ' : '';
		
		$string = '';
		
		if($interval->y > 0){
			$string .= $interval->y . ' years ago';
		}
		elseif($interval->m > 0){
			$string .= $interval->m . ' months ago';
		}
		elseif($interval->d > 0){
			$string .= $interval->d . ' days ago';
		}
		elseif($interval->h > 0){
			$string .= $interval->h . ' hours ago';
		}
		elseif($interval->i > 0){
			$string .= $interval->i . ' minutes ago';
		}
		else{
			$string .= 'now';
		}
		
		return $string;
		
		
	}
		
	
}