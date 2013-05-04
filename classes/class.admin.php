<?php
//class to handle every admin section


class Athlatics_Board_Admin{
	
	
	static $ajax_requested = 0;
	static $message = array();
	

	//hooks
	static function init(){
		add_action( 'add_meta_boxes', array(get_class(), 'custom_metaboxes'));
		add_action( 'admin_enqueue_scripts', array(get_class(), 'include_scripts'));
		
		add_action('save_post', array(get_class(), 'save_whiteboard'), 20, 2);
		
		add_filter('the_content', array(get_class(), 'attach_white_board'), 10, 1);
		
		add_action('wp_enqueue_scripts', array(get_class(), 'print_scripts'));
		
		//activation hook
		register_activation_hook(ATHLATESWHITEBOARD_FILE, array(get_class(), 'activate_the_plugin'));		
		
		//athletes page designing
		add_action('admin_menu', array(get_class(), 'admin_menu'));	
		
		//tiny mc
		add_filter("mce_external_plugins", array(get_class(), "register_tinymce_plugin"));
		add_filter('mce_buttons', array(get_class(), 'add_tinymce_button'));
			
		add_action('init', array(get_class(), 'register_new_athlete'));
	}
	
	
	//tinymce button with functionality
	static function register_tinymce_plugin($plugin_array){
		if(current_user_can('edit_posts')){
			$plugin_array['whiteboard_button'] = ATHLATESWHITEBOARD_URL . 'js/tinymce.js';
		}
    	return $plugin_array;
	}
	
		
	static function add_tinymce_button($buttons){
		if(current_user_can('edit_posts')){
			$buttons[] = 'whiteboard_button';
		}
		
		return $buttons;
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
		
		
		wp_register_script('athlates_board_form_field_extender_jquery', ATHLATESWHITEBOARD_URL . 'js/admin-form-extender.js', array('jquery'));
		wp_enqueue_script('athlates_board_form_field_extender_jquery');
		
		wp_localize_script('athlates_board_form_field_extender_jquery', 'AthlatesAjaxAdmin', array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' )
		));
		
		wp_register_style('athlates-board-white-board-metabaox', ATHLATESWHITEBOARD_URL . 'css/metabox.css');
		wp_enqueue_style('athlates-board-white-board-metabaox');
		
	}
	
	
	//front end css including
	static function print_scripts(){
		wp_register_style('athlates-board-white-board', ATHLATESWHITEBOARD_URL . 'css/white-board.css');
		wp_enqueue_style('athlates-board-white-board');
		
		//js
		wp_enqueue_script('jquery');
		wp_register_script('athlates_white_board_jquery', ATHLATESWHITEBOARD_URL . 'js/Cf-front-end.js', array('jquery'));
		wp_enqueue_script('athlates_white_board_jquery');
		
		wp_localize_script('athlates_white_board_jquery', 'AthlatesAjax', array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' )
		));
	}
	
	
	//saving the whiteboard post data
	static function save_whiteboard($post_ID, $post){
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
		
		if($post->post_type == 'post') :
			$board = array();
			if(count($_POST['class-names']) > 0):
			
				$sanitized_key = 0;
			
				foreach($_POST['class-names'] as $classkey => $classname){
					if(empty($classname)) continue;
					
					
					$board[$sanitized_key]['class'] = $classname;
					
					foreach($_POST['component-names'][$classkey] as $comkey => $component){
						if(empty($component)) continue;
						$description = $_POST['component-descriptions'][$classkey][$comkey];
						$board[$sanitized_key]['component'][] = array('name'=>$component, 'des'=>$description);
					}
					
					$sanitized_key++;					
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
		
		return $board_data . $content;
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
		$records = $wpdb->get_results("SELECT $user_meta.user_id, $user_meta.log, $user.name FROM $user_meta INNER JOIN $user ON $user_meta.user_id = $user.id WHERE $user_meta.post_id = '$post_id' ORDER BY $user.name ASC");
		
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
	
	
	
	//admin menu
	static function admin_menu(){
		add_menu_page('athletes page', 'Athletes', 'manage_options', 'athlete-integration', array(get_class(), 'athletes_integration'));
		add_submenu_page('athlete-integration', 'athlete register', 'Add New', 'manage_options', 'athletes-register-add', array(get_class(), 'athletes_register'));
		add_submenu_page('athlete-integration', 'athlates directory', 'A. Directory', 'manage_options', 'athletes-directory', array(get_class(), 'athletes_page'));
	}
	
	
	/*
	 * admin page to manipulate athletes
	 * */
	static function athletes_integration(){
				
		$athletes_table = self::get_new_list_table();
		
		
		if($athletes_table->current_action() == 'delete'){
			$athletes = $_REQUEST['athlete'];
			if(is_array($athletes)){
				$message = count($athletes) . ' deleted';				
			}
			
			self::handle_actions($athletes);
		}
	
		$athletes_table->prepare_items();
		include ATHLATESWHITEBOARD_DIR . '/includes/athletes-list-table.php';
	}
	
	
	/*
	 * handle actions
	 * */
	static function handle_actions($athletes){
		global $wpdb;
		$tables = self::get_tables();
		extract($tables);
		
		$sql[] = "DELETE FROM $user WHERE id = '%s'";
		$sql[] = "DELETE FROM $user_meta WHERE user_id = '%s'";
		
		if(!function_exists('wp_redirect')){
			include ABSPATH . '/wp-includes/pluggable.php';
		}
			
		
		if(is_array($athletes)){
			foreach($athletes as $athlete){
				foreach($sql as $s){
					$wpdb->query($wpdb->prepare($s, $athlete));
				}
			}
											
		}
		else{
			foreach($sql as $s){
				$wpdb->query($wpdb->prepare($s, $athletes));
			}
		}
			
	}
	
	
	/*
	 * return a new list table for athlates
	 * */
	static function get_new_list_table(){
		if(!class_exists('Athletes_List_Table')){
			include ATHLATESWHITEBOARD_DIR . '/classes/list-table.php';
		}
		
		$list_table = new Athletes_List_Table();				
		return $list_table;
	}	
	
	
	/*
	 * submenu page to register or add new athletes or edit the exisitng one
	 * */
	static function athletes_register(){
				
		if($_REQUEST['type'] == 'editlog'){
			if($_POST['fetch-log'] == 'Y' && $_REQUEST['phase'] == 2){
							
				if($_POST['athlete-log-by-admin-saved'] == 'Y'){						
					self::update_athlete_log_by_admin();					
				}
				
				
				include ATHLATESWHITEBOARD_DIR . '/includes/edit-workouts-phase2.php';
			}
			
			else{
				include ATHLATESWHITEBOARD_DIR . '/includes/edit-workouts.php';
			}
		}
		else{
			include ATHLATESWHITEBOARD_DIR . '/includes/edit-athlete.php';
		}
	}
	
	
	//athlete logs updated by admin
	static function update_athlete_log_by_admin(){
		
		global $wpdb;
		$tables = Athlatics_Board_Admin::get_tables();
		extract($tables);
		$time = current_time('timestamp');
		
		$athlete_id = $_POST['athlete'];
		$post_id = $_POST['wod-post-number'];
		
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
					'log_time' => $time,
					'components' => $components
				);
			}
		}
		
		if($sanitized){
			$is_update = $wpdb->get_row("SELECT * FROM $user_meta WHERE user_id = '$athlete_id' AND post_id = '$post_id'");
			if($is_update){
				$wpdb->update($user_meta, array('time'=>current_time('mysql'), 'log'=>serialize($sanitized)), array('user_id'=>$athlete_id, 'post_id'=>$post_id), array('%s', '%s'), array('%d', '%d'));
			}
			else{
				$wpdb->insert($user_meta, array('user_id'=>$athlete_id, 'post_id'=>$post_id, 'time'=>current_time('mysql'), 'log'=>serialize($sanitized)), array('%d', '%d', '%s', '%s'));
			}
		}
	}
	
	
	/*
	 * register new athlete
	 * */
	static function register_new_athlete(){
		if($_POST['action'] == 'edit-athlete'){
			
			$is_error = false;
			
			if(empty($_POST['athlete_name'])){
				self::$message['registration']['error'][] = 'Name: name field should not be empty';
				$is_error = true;
			}
			
			if(!is_email($_POST['athlete_email'])){
				self::$message['registration']['error'][] = "Email: invalid Email";
				$is_error = true;
			}
			
			if($is_error){
				return;
			}
			else{
				$athlete = self::register_new_athlate($_POST['athlete_name'], $_POST['athlete_email']);
				$url = admin_url('admin.php?page=athletes-register-add');
				if($athlete['is_exist']){
					$url = add_query_arg(array('is_exist'=>1), $url);
				}
				
				$url = add_query_arg(array('athlete'=>$athlete['id'], 'message'=>1), $url);
				
				if(!function_exists('wp_redirect')){
					include ABSPATH . '/wp-includes/pluggable.php';
				}
				
				wp_redirect($url);
				exit;
			}		
			
		}
	}
	
	
	
	/*get whiteboard containing posts*/
	static function get_whiteboard_posts(){
		global $wpdb;
		$sql = "SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' AND ID in (
			SELECT post_id FROM $wpdb->postmeta WHERE meta_key LIKE 'Athlates_White_Board'
		) ORDER BY post_title ASC";
		
		return $wpdb->get_results($sql);
	}
	
	
	
	/*
	 * get an athlete and it's  inforamtion
	 * */
	static function get_an_athlete($id){
		global $wpdb;
		$tables = Athlatics_Board_Admin::get_tables();
		extract($tables);
		
		$athlete = $wpdb->get_row("SELECT * FROM $user WHERE id = '$id'");
		$logged_posts = $wpdb->get_col("SELECT post_id FROM $user_meta WHERE user_id = '$id'");
		
		return array(
			'athlete' => $athlete,
			'posts' => $logged_posts
		);
			
	}
	
	
	//register new athlates
	static function register_new_athlate($name, $email){
		global $wpdb;
		$tables = Athlatics_Board_Admin::get_tables();
		extract($tables);
		$email = trim($email);		
		
		$user_id = $wpdb->get_var("SELECT id FROM $user WHERE email = '$email'");
		if($user_id) return array('is_exist'=>true, 'id'=>$user_id);		
		
		$wpdb->insert($user, array('name'=>$name, 'email'=>$email), array('%s', '%s'));		
		return array('is_exist'=>false, 'id'=>$wpdb->insert_id);
		
	}
	
	
	//athletes page designing
	static function athletes_page(){
		
		/*saving the form submitted data*/
		if($_POST['athletes-page-selection-table-submit'] == 'Y'){
			if(update_option('whiteboard_athlates_page', $_POST['athlete-page']));
		}
		
		$athlates_page = get_option('whiteboard_athlates_page');
		
		$pages = self::get_pages();		
		include ATHLATESWHITEBOARD_DIR . '/includes/options-page.php';
	}
	
	//return all the pages
	static function get_pages(){
		global $wpdb;
		$sql = "SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'page' AND post_status = 'publish'";
		
		return $wpdb->get_results($sql);
	}
	
}