<div class="athlates-white-board">
	<h1 id="whiteboard-window-<?php echo $post->ID;?>">Whiteboard</h1>
</div>
<div style="clear:both"></div>

<div class="athlates-white-board-popup">

	<div class="whiteboard-window whiteboard-window-shown" id="wwhiteboard-window-<?php echo $post->ID; ?>">
		
		<table class="whiteboard-entry-tabel">
			<tr class="whiteboard-entry-row">
				<td> <h2>Entries</h2> </td>
				<td> 
					<a class="white-board-entry-add" id="add-anew-entry_<?php echo $post->ID; ?>">+</a>
					<input type="hidden" id="selected-post-id_<?php echo $post->ID; ?>" value="<?php echo $post->ID; ?>" />
					
					<?php 
						if($board_data){
							$first_class = $board_data[0]['class'];
						}
					?>
					
					<input type="hidden" id="selected-class-name_<?php echo $post->ID?>" value="<?php echo $first_class; ?>" />
					<input type="hidden" id="selected-component-name<?php echo $post->ID; ?>" value="" />
				</td>
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
						<td class="<?php echo $class;?>" id="<?php echo 'boardclass' . '-' . $post->ID . '-' . $key; ?>" >
							<a> <?php echo $data['class']; ?> </a>
							<input type="hidden" id="<?php echo 'Classname' . '-' . $post->ID . '-' . $key; ?>" value="<?php echo $data['class']; ?>">
						</td> 
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
						
						<table class="<?php echo $class; ?>" id="whiteboard-entries-<?php echo $post->ID . '-' . $key;?>">
							<tr>
								<td>Name</td>
								<?php foreach($data['component'] as $k => $component) : ?>
									<td> <?php echo $component['name'];?> </td>
								<?php endforeach;?>
							</tr>
							
							<?php 
								$class_records = $records[$data['class']];
								if($class_records){
									foreach($class_records as $cr){
										?>
										<tr>
											<td><?php echo $cr['athlate']['name']; ?></td>
											<?php 
												if(is_array($cr['records']['components'])){
													foreach ($data['component'] as $com){
														$c = $cr['records']['components'][$com['name']];
														if($c){
															?>
															<td><?php echo $c['result']?> <?php echo $c['RxScore']?> <?php echo ($c['Rx'] == 'on') ? 'Rx' : '' ?></td>
															<?php 
														}
														else{
															?>
															<td>&nbsp</td>
															<?php 
														}
													}
												}
											?>
										</tr>
										<?php 
									}									
								}
							?>
							
						</table>
						
						<?php 
							$cell_spacing = count($data['component']) + 1;
						?>
						
						<!-- athlates new score input -->
						<form action="" method="post" class="whiteboardNewEntriesSubmitted" id="whiteboardNewEntries_<?php echo preg_replace('/[ ]/', '-', $data['class']) . '_' . $post->ID; ?>">
							
							<input type="hidden" name="post_id" value="<?php echo $post->ID; ?>" >
							<input type="hidden" name="class_name" value="<?php echo $data['class']; ?>" >
							
							<table class="althlates-new-entry whiteboard-entries" id="whiteboard-new-entries-<?php echo preg_replace('/[ ]/', '-', $data['class']) . '_' . $post->ID;  ?>">
								<tr> 
									<td colspan="<?php echo $cell_spacing; ?>">
										<h4>Email Adress</h4>
										<input type="text" name="email" placeholder="name@example.com" />
									</td>
								</tr>
								
								<tr>
									<td colspan="<?php echo $cell_spacing; ?>">
										<h4>Name</h4>
										<input name="name" type="text" placeholder="Guest" />
									</td>
								</tr>
								
								<?php foreach($data['component'] as $key => $component) : ?>
									<tr>
										<td colspan="<?php echo $cell_spacing; ?>">
											<h4><?php echo $component['name'];?></h4>
											<p><input name="result[<?php echo $component['name']; ?>]" type="text" placeholder="what is your result?"></p>
											<p>
																							
												<span>
													<input name="Rx[<?php echo $component['name']; ?>]" type="checkbox" name="" /> Rx
												</span> 
												<span style="margin: 0 20px 0 20px">or</span> 
												<span>
													<input name="RxScale[<?php echo $component['name']; ?>]" type="text" name="" placeholder="How do you scale?" />
												</span> 												 
												
											</p>
										</td>
									</tr>									
								<?php endforeach;?>
								
								<tr>
									<td><input type="submit" class="addRecords-<?php echo $post->ID ?>" value="Add Record"> <input class="cancel" type="button" value="cancel"> </td>
								</tr>
								
							</table>
						</form>
						<?php 
					}			
			}
		?>
		
	</div>
	
</div>

<div style="clear:both"></div>
