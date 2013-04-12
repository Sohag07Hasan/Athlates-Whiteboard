<div class="athlates-white-board">
	<h1>Whiteboard</h1>
</div>
<div style="clear:both"></div>

<div class="athlates-white-board-popup">

	<div class="whiteboard-window whiteboard-window-hidden">	
		 
		
		<div post_id="<?php echo $post->ID; ?>" class="whiteboard-branding">
			<span style="float: left;">Entries</span>
			<span class="add-new-entry" style="float: right;">+</span>
		</div> 
		<div style="clear:both"></div>
		 		
		<?php 
		
			if($board_data){
				?>
				<table class="whiteboard-classes">
					<tr>
						<?php 
						foreach($board_data as $key => $data) : 
							$c = ($key == 0) ? 'whiteboard-class selected-class' : 'whiteboard-class';												
						?> 						
						<td whiteboard_class="<?php echo $data['class']; ?>" post_id="<?php echo $post->ID; ?>" class="<?php echo $c; ?>">
							<a> <?php echo $data['class']; ?> </a>						
						</td> 
						<?php endforeach; ?>
					</tr>
				</table>
								
				<!--  some ajax elements are to be shown -->
				<div class="ajax-showing-div" post_id="<?php echo $post->ID; ?>"></div>
								
								
				<?php 
				//athlates entries are showing								
					foreach ($board_data as $key => $data){
						$table_id = preg_replace('/[ ]/', '-', $data['class']) . '_' . $post->ID;	
						$form_id = 'form_' . $table_id;
						
						if($key == 0){
							$class = "whitebaord-class-entries";
							?>
							<input type="hidden" post_id="<?php echo $post->ID; ?>" class_name="<?php echo $data['class']; ?>" />
							<?php 
						}
						else{
							$class= "whitebaord-class-entries hidden-entries";
						}
						?>
						
						<table post_id="<?php echo $post->ID; ?>" class="<?php echo $class; ?>" id="<?php echo $table_id; ?>" class_name="<?php echo $data['class']; ?>" >
							<tr class="headlinesholder" style="cursor: default">
								<td>Name</td>
								<?php foreach($data['component'] as $k => $component) : ?>
									<td> <?php echo $component['name'];?> </td>
								<?php endforeach;?>
								<td>&nbsp</td>
							</tr>
							
							<?php 
								$class_records = $records[$data['class']];
								if($class_records){
									foreach($class_records as $cr){
										?>
										<tr>
											<td user_id="<?php echo $cr['athlate']['id']; ?>"><?php echo $cr['athlate']['name']; ?></td>
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
											<td user_id="<?php echo $cr['athlate']['id']; ?>" class="whiteboard-more"> > </td>
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
						<form action="" method="post" post_id="<?php echo $post->ID; ?>" id="<?php echo $form_id; ?>" class="new-entry-form hidden-entries" >
							
							<input type="hidden" name="post_id" value="<?php echo $post->ID; ?>" >
							<input type="hidden" name="class_name" value="<?php echo $data['class']; ?>" >
							
							<table>
								<tr> 
									<td colspan="<?php echo $cell_spacing; ?>">
										<h4>Email Adress</h4>
										<input type="text" name="email" placeholder="name@example.com" value="" />
									</td>
								</tr>
								
								<tr>
									<td colspan="<?php echo $cell_spacing; ?>">
										<h4>Name</h4>
										<input name="name" type="text" placeholder="Guest" value="" />
									</td>
								</tr>
								
								<?php foreach($data['component'] as $key => $component) : ?>
									<tr>
										<td colspan="<?php echo $cell_spacing; ?>">
											<h4><?php echo $component['name'];?></h4>
											<p><input name="result[<?php echo $component['name']; ?>]" type="text" placeholder="what is your result?" value="" > </p>
											<p>
																							
												<span>
													<input name="Rx[<?php echo $component['name']; ?>]" type="checkbox" value="" /> Rx
												</span> 
												<span style="margin: 0 20px 0 20px">or</span> 
												<span>
													<input name="RxScale[<?php echo $component['name']; ?>]" type="text" placeholder="How do you scale?" value="" />
												</span> 												 
												
											</p>
										</td>
									</tr>									
								<?php endforeach;?>
								
								<tr>
									<td><input class="entry-from-submit-button" type="button" post_id="<?php echo $post->ID ?>" value="Add Record"> <input class="cancel" type="button" value="cancel"> </td>
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
