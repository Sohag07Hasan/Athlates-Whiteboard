<style>
	p.instruction{
		font-size: 15px;
		color: #000000;
		background-color: #E5E5E5;
		padding: 15px;
	}
	
	table.athletes-page-selection-table td{
		padding: 15px;
		font-size: 15px;
	}
	
</style>


<div class="wrap">

	<?php 
		if($_POST['athletes-page-selection-table-submit'] == 'Y'){
			?>
				<div class="updated"> <p>Saved..</p> </div>
			<?php 
		}
	?>
	
	
	<h2> Athletes Directory </h2>
	
	<p class="instruction"> Use the shortcode [athletes_directory] for the Athlete's directory </p>

	<form action="" method="post">
		
		<input type="hidden" name="athletes-page-selection-table-submit" value="Y" />
		
		<table class="athletes-page-selection-table">
			
			<tr>
				<td> Choose Athlete Directory </td>
				<td>
					<select name="athlete-page">
						<option value="0">Choose</option>
					<?php 
						if($pages) :
						
							foreach($pages as $page){
								?>
								
									<option <?php selected($page->ID, $athlates_page) ?> value="<?php echo $page->ID ?>"><?php echo $page->post_title; ?></option>
								
								<?php 
							}	
						
						endif;
					?>
					</select>
				</td>
			</tr>
			
			<tr>
				<td> <input type="submit" value="Save" class="button-primary" /> </td>
			</tr>
			
		</table>
		
	</form>
</div>