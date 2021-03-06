<?php 
	if($athlates){
		$now = current_time('timestamp');
		$link = get_permalink($post->ID);		
		
		?>
		<table class="athletes-directory">
			<thead>
				<tr>
					<th>Name</th> <th>Records</th> <th>Last seen</th>
				</tr>
			</thead>
			
			<tbody>
				<?php 
					$counter = 1;
					foreach ($athlates as $athlate_id => $athlate){
						$directory = add_query_arg('id', $athlate_id, $link);
						
						$tr_class = (fmod($counter, 2) > 0) ? 'odd' : 'even';
						$counter ++;						
						
						?>
							
							<tr class="<?php echo $tr_class; ?>">
								<td><a title="<?php echo $athlate['name']; ?>" href="<?php echo $directory ?>"><?php echo $athlate['name']; ?></a></td>
								<td><?php echo count($athlate['classes']); ?> Workouts Logged</td>
								<td>
									<?php 
										if(count($athlate['time']) > 0){									
											echo self::get_interval($now, max($athlate['time'])); 
										}
										else{
											echo 'N/A';
										}
									?>
								</td>
							</tr>
							
						<?php 
					}
				?>
			</tbody>
			
		</table>
				
		<?php 
			
		
		echo paginate_links($args);
		
	}
?>