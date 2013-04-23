<?php 
	$current_time = current_time('timestamp');
?>

<div class="wrap">
	<h2> <?php echo $athlate_info->name; ?> </h2>
	<?php 
		foreach ($sanitized_data as $time => $data){
			foreach($data as $d){
				?>
				
				<div class="athlete-profile">
				
					<time class="date-time">
						<div>
							<?php echo date('d F Y') ?>
						</div>
						<div>
							<?php echo self::get_interval($current_time, $time); ?>
						</div>
					</time>
					
					<div class="class-description">
						<span class="class-name"> <?php echo $d['class']; ?> </span> <span class="blog-post-link"> <a href="<?php echo get_permalink($d['post_id']); ?>">view blog post</a> </span>
						<ul class="components-with-results">
							<?php 
								foreach ($d['components'] as $component => $result){
									?>
									<li><span><?php echo $component ?></span> | <?php echo $result['result']; ?>  <?php echo ($result['Rx']) ? 'Rx' : ''; ?>  <?php echo $result['RxScale']; ?></li>
									<?php 
								}
							?>
						</ul>
					</div>
					<div style="clear: both"></div> 
				</div>
				
				<?php 
			}
					
		}
	?>
	
</div>