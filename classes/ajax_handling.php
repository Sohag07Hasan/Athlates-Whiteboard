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
		
		
		
		//ajax actions to add a record
		add_action('wp_ajax_athlates_records_submitted', array(get_class(), 'ajax_reuqest_parsing'));
		add_action('wp_ajax_nopriv_athlates_records_submitted', array(get_class(), 'ajax_reuqest_parsing'));
		
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
		
		$athlete_directory = self::get_athlete_directory_page_id();
		
		$url = get_permalink($athlete_directory);
		
		$athlete_url = add_query_arg('id', $user_id, $url);
		
		$return = array();
			
			ob_start();
		?>
				
		<dl class="athlates-profile-viewing">
			<dt> <?php echo $athlate_name; ?> <a class="athlates-profile-link" href="<?php echo $athlete_url; ?>"> view profile </a> </dt>
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
		
		if(isset($_GET['id'])){
			$athlate_id = (int) $_GET['id'];			
			if($athlate_id > 0) return self::show_an_athlate($athlate_id);
		}

		//doing some pagination
		$total_athlete = (int) $wpdb->get_var("SELECT COUNT(id) FROM $user");
		$per_page = 20;		
		$total_page = ceil($total_athlete/$per_page);	

		$cur_page = 1;
		
		if(isset($_GET['page'])){
			$cur_page = $_GET['page'];
		}
		else{
			
			$url = trim($_SERVER['REQUEST_URI'], '/');
			
			if(preg_match('/page/', $url, $match)){
				$url = explode('/', $url);			
				$cur_page = end($url);
			}			
		}
		
		//var_dump($cur_page);
		
		//$cur_page = ($_GET['ap'] > 0) ? $_GET['ap'] : 0;
		
		$offset = (int) $per_page * ($cur_page-1);
		
		//$offset = 0;
		
		$permalinks = get_option('permalink_structure');
        $format = empty( $permalinks ) ? '&page=%#%' : 'page/%#%/';
        		
		$args = array(
			'base' => get_pagenum_link(1) . '%_%',
			'format' => $format,
			'total' => (int) $total_page,
			'current' => (int)$cur_page,
			
		);
		
		$athletes = $wpdb->get_results("SELECT * FROM $user ORDER BY name LIMIT $per_page OFFSET $offset");
		//var_dump($athletes);
		
		$athlates = array();
		foreach($athletes as $a){
			$raw_datas = $wpdb->get_results("SELECT post_id, log FROM $user_meta WHERE user_id = '$a->id'");
			if($raw_datas){
				foreach($raw_datas as $raw_data){
					$log = unserialize($raw_data->log);
					$athlates[$a->id]['name'] = $a->name;

					if($log){
						foreach($log as $cn => $value){
							$athlates[$a->id]['time'][] = $value['log_time'];
							$athlates[$a->id]['classes'][$cn] = $value;					
						}
					}
										
				}
			}
			else{
				$athlates[$a->id]['name'] = $a->name;
				$athlates[$a->id]['time'] = array();
				$athlates[$a->id]['classes'] = array();
			}
		}
		
		//var_dump($athlates);
			
		
		ob_start();
		include ATHLATESWHITEBOARD_DIR . '/includes/athletes-directory.php';
		$content = ob_get_contents();	
		ob_end_clean();
		
		
		return $content;
	}
	
	
	//return the athletic directory page id
	static function get_athlete_directory_page_id(){
		return get_option('whiteboard_athlates_page');
	}
	
	
	/*
	 * Showing an individual athlate
	 * */
	static function show_an_athlate($athlate_id){
		global $wpdb;
		$tables = Athlatics_Board_Admin::get_tables();
		extract($tables);
		
		$athlate_info = $wpdb->get_row("SELECT name FROM $user WHERE id = '$athlate_id'");
		
		$sql = "SELECT post_id, log FROM $user_meta WHERE user_id = '$athlate_id' ORDER BY post_id DESC";
		
		$records = $wpdb->get_results($sql);
		
		//var_dump($records);
		
		$sanitized_data = array();
		
		foreach ($records as $r){
			$rd = unserialize($r->log);
			foreach($rd as $key => $rz){
				$sanitized_data[$rz['log_time']][] = array(
					'class' => $key,
					'components' => $rz['components'],
					'post_id' => $r->post_id
				);
			}
		}

		krsort($sanitized_data);
		
		ob_start();
		include ATHLATESWHITEBOARD_DIR . '/includes/single-athlate-profile.php';
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
	
	
	
	//ajax request handling
	static function ajax_reuqest_parsing(){
		
		
		$data = $_POST['form_data'];
			
		if(is_array($data)){
			$new_data = array();
			foreach ($data as $key => $value){
				$new_data[$value['name']] = $value['value'];
			}
		}
		
		
		$post_id = $new_data['post_id'];
		$class_name = $new_data['class_name'];
		$name = $new_data['name'];
		$email = $new_data['email'];
		
		if(empty($email) || empty($name)){
			$return_array = array(
				'is_error' => true,
				'message' => 'Email and Name fields are mendatory'
			);
			
			echo json_encode($return_array);
			exit;
		}
		
		if(!is_email($email)){
			$return_array = array(
				'is_error' => true,
				'message' => 'Invalid Email address!'
			);
			
			echo json_encode($return_array);
			exit;	
		}
			
		//get the user id
		$user_info = self::register_new_athlate($name, $email, $class_name, $post_id);
		
		if($user_info['is_exist']){
			$return_array = array(
				'is_error' => true,
				'message' => 'This email is already in use'
			);
			
			echo json_encode($return_array);
			exit;
		}
		
		//assigning the user id
		$user_id = $user_info['id']; 
		
		if($user_id){
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
			
			
			global $wpdb;
			$tables = Athlatics_Board_Admin::get_tables();
			extract($tables);
	
			$log_row = $wpdb->get_row("SELECT * FROM $user_meta WHERE post_id = '$post_id' AND user_id = '$user_id'");
			
		//	var_dump($log_row);
			
			$is_update = (empty($log_row)) ? false : true;
			
			$log = (empty($log_row->log)) ? array() : unserialize($log_row->log);
			
			//var_dump($log);
						
			$log[$class_name] = array(
					'log_time' => current_time('timestamp'),
					'components' => $sanitized_data
				);
				
			//var_dump($log); die();
				
			if($is_update){
				$success = $wpdb->update($user_meta, array('log'=>serialize($log), 'time'=>current_time('mysql')), array('user_id'=>$user_id, 'post_id'=>$post_id), array('%s', '%s'), array('%d', '%d'));
			}
			else{
				$wpdb->insert($user_meta, array('user_id'=>$user_id, 'post_id'=>$post_id, 'time'=>current_time('mysql'), 'log'=>serialize($log)), array('%d', '%d', '%s', '%s'));
				$success = $wpdb->insert_id;
			}
						
			//now fetching the updated informations

			$updated_data = $wpdb->get_row("SELECT * FROM $user_meta WHERE post_id = '$post_id' AND user_id = '$user_id'");
			
			$records = $wpdb->get_row("SELECT $user_meta.log, $user.name FROM $user_meta INNER JOIN $user ON $user_meta.user_id = $user.id WHERE $user_meta.post_id = '$post_id' AND $user.id = '$user_id'");
			
			if($records){
				
				$log_data = unserialize($records->log);
				if(is_array($log_data)){
					$class_data = $log_data[$class_name];
					
					foreach($board_data as $key => $data){
						if($data['class'] == $class_name){
							$sanitized_data = array();
							$string = '<tr><td>'.$records->name.'</td>';
							foreach ($data['component'] as $d){
								
								$Rx = (isset($class_data['components'][$d['name']]['Rx'])) ? 'Rx' : '';
								
								$string .= '<td>'.$class_data['components'][$d['name']]['result']. ' ' .$Rx.'</td>';
								/*
								$sanitized_data[$d['name']]['result'] = $new_data['result['.$d['name'].']'];
								$sanitized_data[$d['name']]['Rx'] = $new_data['Rx['.$d['name'].']'];
								$sanitized_data[$d['name']]['RxScale'] = $new_data['RxScale['.$d['name'].']'];
								*/
							}
							$string .= '<td user_id=" ' . $user_id . ' " class="whiteboard-more"> > </td>';
							$string .= '</tr>';			
						}
					}
				}
			}
			
		}
		
		$success_array = array(
			'status' => $success,
			'data' => $string
		);
		
		echo json_encode($success_array);
		
		
		exit;		
			
	}
	
	

	//register new athlates
	static function register_new_athlate($name, $email, $class_name, $post_id){
		global $wpdb;
		$tables = Athlatics_Board_Admin::get_tables();
		extract($tables);
		$email = trim($email);		
		
		$user_id = $wpdb->get_var("SELECT id FROM $user WHERE email = '$email'");
		
		if($user_id){
			$log = $wpdb->get_var("SELECT log FROM $user_meta WHERE user_id = '$user_id' AND post_id = '$post_id'");
			
			if($log){
				$log = unserialize($log);
				if(isset($log[$class_name])){
					return array('is_exist'=>true, 'id'=>$user_id);
				}
			}
		}

		if($user_id){
			return array('is_exist'=>false, 'id'=>$user_id);	
		}
		
		$wpdb->insert($user, array('name'=>$name, 'email'=>$email), array('%s', '%s'));		
		return array('is_exist'=>false, 'id'=>$wpdb->insert_id);
		
	}
	
	
}