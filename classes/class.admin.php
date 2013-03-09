<?php
//class to handle every admin section


class Athlatics_Board_Admin{

	//hooks
	static function init(){
		add_action( 'add_meta_boxes', array(get_class(), 'custom_metaboxes'));
		add_action( 'admin_enqueue_scripts', array(get_class(), 'include_scripts'));
		
		add_action('save_post', array(get_class(), 'save_whiteboard'), 20, 2);
	}
	
	
	//metabox creation
	static function custom_metaboxes(){
		add_meta_box('athlates_whiteboard', 'Athlates White Board', array(get_class(), 'custom_metabox_content'), 'post', 'high', 'core');
	}
	
	
	//metabox content
	static function custom_metabox_content(){
		global $post;
		include ATHLATESWHITEBOARD_DIR . '/metaboxes/post-metabox.php';
	}
	
	
	/*
	 * include the scripts
	 * */
	static function include_scripts(){
		//self::include_css();
		self::include_js();
	}
	
	
	static function include_css(){
		wp_register_style('emaildrip_autoresponder_css', EMAILDRIPCAMPAIGN_URL . 'css/emaildrip_autoresponder.css');
		wp_enqueue_style('emaildrip_autoresponder_css');
	}
		
	
	static function include_js(){	
		wp_enqueue_script('jquery');
		wp_register_script('athlates_board_form_field_extender_jquery', ATHLATESWHITEBOARD_URL . 'asset/jquery.multiFieldExtender-2.0.js', array('jquery'));
		wp_enqueue_script('athlates_board_form_field_extender_jquery');
		
	}
	
	
	//saving the whiteboard post data
	static function save_whiteboard($post_ID, $post){
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
		
		if($post->post_type == 'post') :
			$board = array();
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
			
			//var_dump($board);
			// die();
			
			update_post_meta($post_ID, 'Athlates_White_Board', $board);
			
		endif;
	}
	
}