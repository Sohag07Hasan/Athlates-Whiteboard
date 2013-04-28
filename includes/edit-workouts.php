<?php 

	$athlete = self::get_an_athlete($_REQUEST['athlete']);
	if(isset($athlete['athlete'])){
		$name = $athlete['athlete']->name;
		$athlete_id = $athlete['athlete']->id;
		$email = $athlete['athlete']->email;
	}
	
	
	$posts = self::get_whiteboard_posts();
	$logged_posts = array();

	if(count($athlete['posts']) > 0){
		$logged_posts = $athlete['posts'];
	}
	

?>


<style>
	p.instructions{
		color: green;
		font-size: 15px;
		font-style: italic;
	}
	p.athlates-inforation{
		font-size: 15px;
	}
	
</style>

<div class="wrap">
	<div id="icon-users" class="icon32"></div>
	<h2>Edit Athlete Workouts</h2>
	
	<p class="instructions">Green colored posts alreay have workouts form this athlete</p>
	
	<form action="" method="post">
		<table class="form-table">
			<tbody>
			
				<tr>
					<th scope="row">Athlete's Name</th>
					<td><?php echo $name; ?></td>
				</tr>
				
				<tr>
					<th scope="row">Athlete's Email</th>
					<td><?php echo $email; ?></td>
				</tr>
				
				<tr>
					<th scope="row">Select a post </th>
					<td>
						
						<select name="wod-post-number">
			 			<option value="">Choose</option>
			 			<?php 
					 		foreach ($posts as $post){
					 			$option_class = (in_array($post->ID, $logged_posts)) ? 'available' : '';
					 		?>
					 		<option class="<?php echo $option_class; ?>" value="<?php echo $post->ID; ?>"><?php echo $post->post_title; ?></option>
					 		<?php
					 		} 
					 	?>
						
					</td>
				</tr>
				
			</tbody>
		</table>
		
		<p> <input type="submit" class="button button-primary" value="Fetch" /> </p>
						
	</form>
</div>