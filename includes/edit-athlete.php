<div class="wrap">
	<div id="icon-users" class="icon32"></div>
	<h2>Edit Athlete</h2>
	
	<?php 
		if(is_array(self::$message['registration']['error'])){
			?>
				<div class="error">
					<?php 
						foreach(self::$message['registration']['error'] as $m){
							echo "<p>$m</p>";
						}
					?>
				</div>
			<?php 	
		}
		
		if(!empty($_REQUEST['athlete'])){
			if($_REQUEST['message'] == 1){
				$message = ($_REQUEST['is_exist']) ? 'Existing User is here for edit' : 'New athlete is added';
				?>
					<div class="updated"><p><?php echo $message; ?></p></div>
				<?php 
			}

			$athlete = self::get_an_athlete($_REQUEST['athlete']);
			if(isset($athlete['athlete'])){
				$name = $athlete['athlete']->name;
				$athlete_id = $athlete['athlete']->id;
				$email = $athlete['athlete']->email;
				
				//edit url
				$edit_url = admin_url('admin.php?page=athletes-register-add&type=editlog&athlete=' . $athlete['athlete']->id);
			}
			
			/*
			$posts = self::get_whiteboard_posts();
			$logged_posts = array();

			if(count($athlete['posts']) > 0){
				$logged_posts = $athlete['posts'];
			}
			*/
		}
		
		
	?>
	
	<form action="" method="post">
		<input type="hidden" name="action" value="edit-athlete" />
		
		<?php 
			if(isset($athlete_id)){
				?>
				<input name="athlete_id" type="hidden" value="<?php echo $athlete_id ?>" />
				<?php 
			}
		?>
		
		<table class="form-table">
			<tbody>
				<tr class="">
					<th scope="row">
						<label for="athlete_name"> Name <span class="description">(required)</span> </label>
					</th>
					<td>
						<input size="35" id="athlete_name" type="text" value="<?php echo $name; ?>" name="athlete_name" />
					</td>
				</tr>
				
				<tr class="">
					<th scope="row">
						<label for="athlete_email"> Email <span class="description">(required)</span> </label>
					</th>
					<td>
						<input size="35" id="athlete_email" type="text" value="<?php echo $email; ?>" name="athlete_email" />
					</td>
				</tr>
											
			</tbody>
		</table>
		
		<p><input type="submit" class="button button-primary" value="Add New Athlate" /></p>
		
	</form>	
	
	<?php 
		if($edit_url){
			echo '<a href="'.$edit_url.'">Edit Workouts</a>';
		}
	?>
	
</div>