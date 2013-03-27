<?php
//class to handle every admin section


class Athlatics_Board_Admin{

	//hooks
	static function init(){
		add_action( 'add_meta_boxes', array(get_class(), 'custom_metaboxes'));
		add_action( 'admin_enqueue_scripts', array(get_class(), 'include_scripts'));
		
		add_action('save_post', array(get_class(), 'save_whiteboard'), 20, 2);
		
		add_filter('the_content', array(get_class(), 'attach_white_board'), 10, 1);
		
		add_action('wp_enqueue_scripts', array(get_class(), 'print_scripts'));
		
		//activation hook
		register_activation_hook(ATHLATESWHITEBOARD_FILE, array(get_class(), 'activate_the_plugin'));
		
		//ajax actions
		add_action('wp_ajax_athlates_records_submitted', array(get_class(), 'ajax_reuqest_parsing'));
		add_action('wp_ajax_nopriv_athlates_records_submitted', array(get_class(), 'ajax_reuqest_parsing'));
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
			
		//get the user id
		$user_id = self::register_new_athlate($name, $email);
		
		
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
			$tables = self::get_tables();
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
					
		}
		
		echo $success;
	
		
		exit;		
			
	}
	
	
	
	//register new athlates
	static function register_new_athlate($name, $email){
		global $wpdb;
		$tables = self::get_tables();
		extract($tables);
		$email = trim($email);		
		
		$user_id = $wpdb->get_var("SELECT id FROM $user WHERE email = '$email'");
		if($user_id) return $user_id;		
		
		$wpdb->insert($user, array('name'=>$name, 'email'=>$email), array('%s', '%s'));		
		return $wpdb->insert_id;
		
	}
	
	
	
	//metabox creation
	static function custom_metaboxes(){
		add_meta_box('athlates_whiteboard', 'Athlates White Board', array(get_class(), 'custom_metabox_content'), 'post');
	}
	
	
	//metabox content
	static function custom_metabox_content(){
		global $post;
		include ATHLATESWHITEBOARD_DIR . '/metaboxes/post-metabox.php';
	}
	
	
	/*
	 * include the scripts at admin pages
	 * */
	static function include_scripts(){
		//self::include_css();
		self::include_js();
	}
		
	//field extender
	static function include_js(){	
		wp_enqueue_script('jquery');
		wp_register_script('athlates_board_form_field_extender_jquery', ATHLATESWHITEBOARD_URL . 'asset/jquery.multiFieldExtender-2.0.js', array('jquery'));
		wp_enqueue_script('athlates_board_form_field_extender_jquery');
		
	}
	
	
	//front end css including
	static function print_scripts(){
		wp_register_style('athlates-board-white-board', ATHLATESWHITEBOARD_URL . 'css/white-board.css');
		wp_enqueue_style('athlates-board-white-board');
		
		//js
		wp_enqueue_script('jquery');
		wp_register_script('athlates_white_board_jquery', ATHLATESWHITEBOARD_URL . 'js/Cf-front-end.js', array('jquery'));
		wp_enqueue_script('athlates_white_board_jquery');
	}
	
	
	//saving the whiteboard post data
	static function save_whiteboard($post_ID, $post){
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
		
		if($post->post_type == 'post') :
			$board = array();
			if(count($_POST['ahtlates-white-board-class-names']) > 0):
				foreach($_POST['ahtlates-white-board-class-names'] as $classkey => $classname){
					if(empty($classname)) continue;
					$board[$classkey]['class'] = $classname;
					
					//component 1
					if(!empty($_POST['ahtlates-white-board-component-names-1'][$classkey])){
						$comname = $_POST['ahtlates-white-board-component-names-1'][$classkey];
						$comdes = $_POST['ahtlates-white-board-component-descriptions-1'][$classkey];
						$board[$classkey]['component'][] = array('name'=>$comname, 'des'=>$comdes);
					}
					
					//component 2
					if(!empty($_POST['ahtlates-white-board-component-names-2'][$classkey])){
							$comname = $_POST['ahtlates-white-board-component-names-2'][$classkey];
							$comdes = $_POST['ahtlates-white-board-component-descriptions-2'][$classkey];
							$board[$classkey]['component'][] = array('name'=>$comname, 'des'=>$comdes);
					}
	
					//component 3
					if(!empty($_POST['ahtlates-white-board-component-names-3'][$classkey])){
							$comname = $_POST['ahtlates-white-board-component-names-3'][$classkey];
							$comdes = $_POST['ahtlates-white-board-component-descriptions-3'][$classkey];
							$board[$classkey]['component'][] = array('name'=>$comname, 'des'=>$comdes);
					}
					
					//var_dump($board[$classkey]);
				}
			endif;	
			
			update_post_meta($post_ID, 'Athlates_White_Board', $board);
			
		endif;
	}
	
	
	//the content hook to show the add cf white board
	static function attach_white_board($content){
		global $post;
		$board_data = self::get_white_board($post->ID);
		
		$records = self::get_athlates_records($post->ID);
		
		if($board_data){
			
			ob_start();
			include ATHLATESWHITEBOARD_DIR . '/includes/white-board-table.php';
			$board_data = ob_get_clean();
			ob_end_flush();
		}
		else{
			$board_data = '';
		}
		
		return $content . $board_data;
	}
	
	
	//return the whiteboard
	static function get_white_board($post_id = null){
		return get_post_meta($post_id, 'Athlates_White_Board', true);
	}
	
	
	//get the athlates records
	static function get_athlates_records($post_id){
		global $wpdb;
		$tables = self::get_tables();
		extract($tables);
		
		//$records = $wpdb->get_results("SELECT user_id, log FROM $user_meta WHERE post_id = '$post_id' ORDER BY time ASC");
		$records = $wpdb->get_results("SELECT $user_meta.user_id, $user_meta.log, $user.name FROM $user_meta INNER JOIN $user ON $user_meta.user_id = $user.id WHERE $user_meta.post_id = '$post_id' ORDER BY $user_meta.time ASC");
		
		$structured_record = array();
		
		if($records){
			foreach($records as $record){
				if(isset($record->log)){
					$log = unserialize($record->log);
					if(is_array($log)){
						foreach ($log as $key => $l){
							$structured_record[$key][] = array(
								'athlate' => array('name'=>$record->name, 'id'=>$record->user_id),
								'records' => $l
							);
						}
					}			
				}
			}
		}
		
		return $structured_record;
	}
	
	
	//activate the plugin
	static function activate_the_plugin(){
		global $wpdb;
		$tables = self::get_tables();
		extract($tables);

		$sql = array();
		$sql[] = "CREATE TABLE IF NOT EXISTS $user(
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			name VARCHAR(50) NOT NULL,
			email VARCHAR(50) NOT NULL UNIQUE 
		)";
		
		$sql[] = "CREATE TABLE IF NOT EXISTS $user_meta(
			user_id BIGINT UNSIGNED NOT NULL,
			post_id BIGINT UNSIGNED NOT NULL,
			time DATETIME NOT NULL,
			log LONGTEXT NOT NULL
		)";
		
		if(!function_exists('dbDelta')){
			include ABSPATH . 'wp-admin/includes/upgrade.php';
		}
		
		foreach($sql as $s){
			dbDelta($s);
		}
		
		return false;
	}
	
	
	//tables names
	static function get_tables(){
		global $wpdb;
		return array(
			'user' => $wpdb->prefix . 'athlate',
			'user_meta' => $wpdb->prefix . 'athlate_meta'
		);
	}
}