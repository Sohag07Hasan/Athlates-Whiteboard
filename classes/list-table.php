<?php 

/*
 * This class will create a list table
 * */

if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . '/wp-admin/includes/class-wp-list-table.php' );
}

class Athletes_List_Table extends  WP_List_Table{
	
	/*columns of the talbe*/
	function get_columns(){
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'name' => __('Athlete Name'),
			'email' => __('Email'),
			'wo_count' => __('Work Outs'),
			'last_seen' => __('Last Seen')
		);
		
		return $columns;
	}
	
	
	/*preparing items*/
	function prepare_items(){
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
				
		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->items = $this->populate_table_data();
	}
	
	
	//make some column sortable
	function get_sortable_columns(){
		$sortable_columns = array(
			'name' => array('name', false),
			'email' => array('email', false)
		);
		
		return $sortable_columns;
	}
	
	
	/*
	 * Table population
	 * */
	function populate_table_data(){
		global $wpdb;
		$tables = Athlatics_Board_Admin::get_tables();
		extract($tables);
		
		$sql = "SELECT * FROM $user";
		
		//sorting elements
		$order_by = (isset($_GET['orderby'])) ? $_GET['orderby'] : 'name';
		$order = (isset($_GET['order'])) ? strtoupper($_GET['order']) : 'ASC';
		
		$sql .= " ORDER BY $order_by $order";
		
		$athlates = $wpdb->get_results($sql);
		
		$data = array();
		if($athlates){
			foreach($athlates as $athlate){
				$info = $this->get_athlete_logs($athlate->id);
				$data[] = array(
					'ID' => $athlate->id,
					'name' => $athlate->name,
					'email' => $athlate->email,
					'wo_count' => $info['count'],
					'last_seen' => $info['last_seen']
				);
			}
		}

		return $data;
	}
	
	
	/*
	 * return logs of a certain athlete
	 * */
	function get_athlete_logs($id){
		global $wpdb;
		$tables = Athlatics_Board_Admin::get_tables();
		extract($tables);
		
		$results = $wpdb->get_results("SELECT time, log FROM $user_meta WHERE user_id = '$id' ORDER BY time DESC");
		
		if($results){
			$count = 0;
			$last_seen = array();
			foreach($results as $key => $result){
				$log = unserialize($result->log);
				if($key == 0){
					$last_seen = Athlates_whiteboard_ajax_handling::get_interval(current_time('timestamp'), strtotime($result->time));
				}
				if(is_array($log)){
					$count += count($log);
				}
			}
			
			return array(
				'last_seen' => $last_seen,
				'count' => $count
			);
		}
		
		return array(
			'last_seen' => 'N/A',
			'count' => 0
		);
	}
	
	
	
	/* default column checking */
	function column_default($item, $column_name){
		switch($column_name){
			case "name":				
			case "email" :
			case 'wo_count' :
			case 'last_seen' :
				return $item[$column_name];
				break;
			default: 
				var_dump($item);
			
		}
	}
	
	
	/*adding some extra actions links after the first column*/
	function column_name($item){
		$actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&athlete=%s">Edit</a>',$_REQUEST['page'],'edit',$item['ID']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&athlete=%s">Delete</a>',$_REQUEST['page'],'delete',$item['ID']),
        );

  		return sprintf('%1$s %2$s', $item['name'], $this->row_actions($actions) );
	}
	
	
	//bulk actions initialization
	function get_bulk_actions() {
		$actions = array(
	    	'delete'    => 'Delete'
	  	);
	  	return $actions;
	}
	
	
	/* checkbox for bulk action*/
	function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="athlete[]" value="%s" />', $item['ID']
        );    
    }
	   
	
}