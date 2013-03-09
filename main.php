<?php 

/*
 * plugin name: Athlates Whitebaord
 * author: Mahibul Hasan
 * */

define("ATHLATESWHITEBOARD_DIR", dirname(__FILE__));
define("ATHLATESWHITEBOARD_FILE", __FILE__);
define("ATHLATESWHITEBOARD_URL", plugins_url('/', __FILE__));


include ATHLATESWHITEBOARD_DIR . '/classes/class.admin.php';
Athlatics_Board_Admin::init();


?>