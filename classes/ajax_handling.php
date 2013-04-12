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
		
		$data = self::get_single_athlate_from_single_post($post_id, $user_id);
		
		$class_data = $data['records'][$class_name];
		$athlate_name = $data['athlate'];
		
		
		?>
		
		<dl>
			<dt> <?php echo $athlate_name; ?></dt>
				
				
				<?php foreach($class_data['components'] as $key => $com) : ?>
					<dt> <?php echo $key; ?> </dt>
						<dd>
							<span><?php echo $com['result']; ?></span>
							<span><?php echo ($com['Rx']) ? 'RX' : ''; ?></span>
							<span><?php echo $com['RxScale']; ?></span>
						</dd>
				<?php endforeach; ?>							
		</dl>
		
		<?php 
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