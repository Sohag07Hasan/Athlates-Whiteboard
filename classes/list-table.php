<?php 

/*
 * This class will create a list table
 * */


if( ! class_exists( 'WP_List_Table' ) ) {
	if(!class_exists('WP_Internal_Pointers')){
		require_once( ABSPATH . '/wp-admin/includes/template.php' );
	}
	require_once( ABSPATH . '/wp-admin/includes/class-wp-list-table.php' );
}

class Athletes_List_Table extends  WP_List_Table{
	
	
	private $per_page = 0;
	private $total_items = 0;
	private $current_page = 0;
	
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
		
		//paginations
		$this->_set_pagination_parameters();
				
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
	 * total items
	 * */
	private function _set_pagination_parameters(){
		$this->per_page = 30;
		$this->current_page = $this->get_pagenum();
		
		global $wpdb;
		$tables = Athlatics_Board_Admin::get_tables();
		extract($tables);
		
		$sql = "SELECT COUNT(id) FROM $user";
		if(isset($_REQUEST['s']) && !empty($_REQUEST['s'])){
			$s = trim($_REQUEST['s']);
			$sql .= " WHERE name LIKE '%$s%' OR email LIKE '%$s%'";
		}
		$this->total_items = $wpdb->get_var($sql);
		
		$this->set_pagination_args( array(
            'total_items' => $this->total_items,                  //WE have to calculate the total number of items
            'per_page'    => $this->per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($this->total_items/$this->per_page)   //WE have to calculate the total number of pages
        ) );
	}
	
	
	
	/*
	 * Table population
	 * */
	function populate_table_data(){
		global $wpdb;
		$tables = Athlatics_Board_Admin::get_tables();
		extract($tables);
		
		$sql = "SELECT * FROM $user";
		
		if(isset($_REQUEST['s']) && !empty($_REQUEST['s'])){
			$s = trim($_REQUEST['s']);
			$sql .= " WHERE name LIKE '%$s%' OR email LIKE '%$s%'";
		}	
		
		//sorting elements
		$order_by = (isset($_GET['orderby'])) ? $_GET['orderby'] : 'name';
		$order = (isset($_GET['order'])) ? strtoupper($_GET['order']) : 'ASC';
		
		$sql .= " ORDER BY $order_by $order";
		
		
		//pagination
		$current_page = ($this->current_page > 0) ? $this->current_page - 1 : 0;
		$offset = (int) $current_page * (int) $this->per_page;
		
		$sql .= " LIMIT $this->per_page OFFSET $offset";
			
		
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
		
		$delete_href = sprintf('?page=%s&action=%s&athlete=%s', $_REQUEST['page'],'delete',$item['ID']);
		
		if(isset($_REQUEST['s']) && !empty($_REQUEST['s'])){
			$delete_href = add_query_arg(array('s'=>$_REQUEST['s']), $delete_href);
		}
		
		if($this->get_pagenum()){
			$delete_href = add_query_arg(array('paged'=>$this->get_pagenum()), $delete_href);
		}
		
		$actions = array(
			'wod' => sprintf('<a href="?page=%s&type=%s&athlete=%s">Wod</a>','athletes-register-add','editlog',$item['ID']),
			'edit' => sprintf('<a href="?page=%s&action=%s&athlete=%s">Edit</a>','athletes-register-add','edit',$item['ID']),
			'delete' => "<a href='$delete_href'>Delete</a>"
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
	  
    
	/**
	 * Display the pagination.
	 *
	 * @since 3.1.0
	 * @access protected
	 * using the main function to handle this 
	 */
	function pagination( $which ) {
		
		if ( empty( $this->_pagination_args ) )
			return;

		extract( $this->_pagination_args, EXTR_SKIP );

		$output = '<span class="displaying-num">' . sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

		$current = $this->get_pagenum();

		$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		$current_url = remove_query_arg( array( 'hotkeys_highlight_last', 'hotkeys_highlight_first' ), $current_url );
			
		if(isset($_REQUEST['s']) && !empty($_REQUEST['s'])){
			$s = trim($_REQUEST['s']);
			$current_url = add_query_arg(array('s'=>$s), $current_url);
		}	
		
		$page_links = array();

		$disable_first = $disable_last = '';
		if ( $current == 1 )
			$disable_first = ' disabled';
		if ( $current == $total_pages )
			$disable_last = ' disabled';

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'first-page' . $disable_first,
			esc_attr__( 'Go to the first page' ),
			esc_url( remove_query_arg( 'paged', $current_url ) ),
			'&laquo;'
		);

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'prev-page' . $disable_first,
			esc_attr__( 'Go to the previous page' ),
			esc_url( add_query_arg( 'paged', max( 1, $current-1 ), $current_url ) ),
			'&lsaquo;'
		);

		if ( 'bottom' == $which )
			$html_current_page = $current;
		else
			$html_current_page = sprintf( "<input class='current-page' title='%s' type='text' name='paged' value='%s' size='%d' />",
				esc_attr__( 'Current page' ),
				$current,
				strlen( $total_pages )
			);

		$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
		$page_links[] = '<span class="paging-input">' . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . '</span>';

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'next-page' . $disable_last,
			esc_attr__( 'Go to the next page' ),
			esc_url( add_query_arg( 'paged', min( $total_pages, $current+1 ), $current_url ) ),
			'&rsaquo;'
		);

		$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
			'last-page' . $disable_last,
			esc_attr__( 'Go to the last page' ),
			esc_url( add_query_arg( 'paged', $total_pages, $current_url ) ),
			'&raquo;'
		);

		$pagination_links_class = 'pagination-links';
		if ( ! empty( $infinite_scroll ) )
			$pagination_links_class = ' hide-if-js';
		$output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

		if ( $total_pages )
			$page_class = $total_pages < 2 ? ' one-page' : '';
		else
			$page_class = ' no-pages';

		$this->_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

		echo $this->_pagination;
	}
	
}