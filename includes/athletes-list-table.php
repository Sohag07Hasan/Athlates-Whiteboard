<div class="wrap">
	<div id="icon-users" class="icon32"></div>
	<h2>Athletes</h2>
	
	<?php 
		if($message){
			?>
			
			<div class="updated"><p><?php echo $message; ?></p></div>
			
			<?php 
		}
	?>
	
	<form method="post" action="<?php echo admin_url('?page=athlete-integration'); ?>">
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		<?php			
			$athletes_table->display();
		?>
	
	</form>
	
</div>