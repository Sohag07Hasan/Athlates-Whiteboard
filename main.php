<?php 

/*
 * plugin name: Athletes Whitebaord
 * author: Mahibul Hasan
 * description: This plugin will add whiteboard button in posts and pages and athletes can log their info 
 * */

define("ATHLATESWHITEBOARD_DIR", dirname(__FILE__));
define("ATHLATESWHITEBOARD_FILE", __FILE__);
define("ATHLATESWHITEBOARD_URL", plugins_url('/', __FILE__));


include ATHLATESWHITEBOARD_DIR . '/classes/class.admin.php';
Athlatics_Board_Admin::init();

//ajax handling class
include ATHLATESWHITEBOARD_DIR . '/classes/ajax_handling.php';
Athlates_whiteboard_ajax_handling::init();

?>