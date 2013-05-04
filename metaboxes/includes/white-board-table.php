<div class="athlates-white-board">
	<h1 id="whiteboard-window-<?php echo $post->ID;?>">Whiteboard</h1>
</div>
<div style="clear:both"></div>

<div class="athlates-white-board-popup">

	<div class="whiteboard-window whiteboard-window-shown" id="wwhiteboard-window-<?php echo $post->ID; ?>">
		
		<table class="whiteboard-entry-tabel">
			<tr class="whiteboard-entry-row">
				<td> <h2>Entries</h2> </td>
				<td> <a class="white-board-entry-add">+</a> </td>
			</tr>
		</table>
		
		<?php 
			if($board_data){
				?>
				<table class="whiteboard-entry-tabel">
					<tr class="whiteboard-class-row">
						<?php 
						foreach($board_data as $key => $data) : 
							if($key == 0){
								$class = "whiteboard-class whiteboard-class-selected";
							}
							else{
								$class = "whiteboard-class";
							}
						?> 						
						<td class="<?php echo $class;?>" id="<?php echo 'boardclass' . '-' . $post->ID . '-' . $key; ?>" ><a> <?php echo $data['class']; ?> </a></td> 
						<?php endforeach; ?>
					</tr>
				</table>
				<?php 
				//athlates scroe showing
					foreach ($board_data as $key => $data){
						if($key == 0){
							$class = "whiteboard-entry-table";
						}
						else{
							$class= "whiteboard-entry-table whiteboard-entries";
						}
						?>
						
						<table class="<?php echo $class; ?>" id="whiteboard-newentry-<?php echo $post->ID . '-' . $key;?>">
							<tr>
								<td>Name</td>
								<?php foreach($data['component'] as $k => $component) : ?>
									<td> <?php echo $component['name'];?> </td>
								<?php endforeach;?>
							</tr>
						</table>
						
						<?php 
							$cell_spacing = count($data['component']) + 1;
						?>
						
						<table class="althlates-new-entry">
							<tr> 
								<td colspan="<?php echo $cell_spacing; ?>">
									<h4>Email Adress</h4>
									<input type="text" name="new-athlates-name" placeholder="name@example.com" />
								</td>
							</tr>
							
							<tr>
								<td colspan="<?php echo $cell_spacing; ?>">
									<h4>Name</h4>
									<input type="text" placeholder="Guest" />
								</td>
							</tr>
							
							<?php foreach($data['component'] as $key => $component) : ?>
								<tr>
									<td colspan="<?php echo $cell_spacing; ?>">
										<h4><?php echo $component['name'];?></h4>
										<p><input type="text" placeholder="what is your result?"></p>
										<p>
																						
											<span>
												<input type="checkbox" name="" /> Rx
											</span> 
											<span style="margin: 0 20px 0 20px">or</span> 
											<span>
												<input type="text" name="" placeholder="How do you scale?" />
											</span> 
												 
											
										</p>
									</td>
								</tr>									
							<?php endforeach;?>
							
						</table>
						
						<?php 
					}			
			}
		?>
		
	</div>
	
</div>

<div style="clear:both"></div>
