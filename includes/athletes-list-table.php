<div class="wrap">
	<div id="icon-users" class="icon32"></div>
	<h2>Athletes</h2>
	
	<?php 
		if($message){
			?>
			
			<div class="updated"><p><?php echo $message; ?></p></div>
			
			<?php 
		}
		
		$action = admin_url('?page=athlete-integration');
		if($athletes_table->get_pagenum()){
			$action = add_query_arg(array('paged'=>$athletes_table->get_pagenum()), $action);
		}		
	?>
	
	
	
	<form method="post" action="<?php echo $action; ?>">
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		<?php
			echo $athletes_table->search_box('search', 'athlete');		
			$athletes_table->display();
		?>
	
	</form>
	
</div>