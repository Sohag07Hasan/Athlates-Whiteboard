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
		
		//ajax actions
		add_action('wp_ajax_athlates_records_submitted', array(get_class(), 'ajax_reuqest_parsing'));
		add_action('wp_ajax_nopriv_athlates_records_submitted', array(get_class(), 'ajax_reuqest_parsing'));
	}
	
	
	
	
	//ajax request
	static function ajax_reuqest_parsing(){
		$data = $_POST['form_data'];
		var_dump($data);
		die();
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
	
	
	//the content
	static function attach_white_board($content){
		global $post;
		$board_data = self::get_white_board($post->ID);
		if($board_data){
			//$board_div = '<div class="athlates-white-board"> <h1>Whiteboard</h1>';
			ob_start();
			include ATHLATESWHITEBOARD_DIR . '/includes/white-board-table.php';
			$board_data = ob_get_clean();
			//$board_div .= '</div>';
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
}