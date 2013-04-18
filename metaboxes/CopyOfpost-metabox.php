<style type="text/css">

		.removeFields
		{
				color:red;
		}
		.addMoreFields
		{
				color:green;
		}		
		
		a {
				color: black;
		}
		a:hover {
				color: maroon;
		}
		.divTag {
				float: right;
				width: 100px;
		}

		.Birds
		{    
				margin:5px;
		}

		.imgBird
		{
				border:1px solid #ccc;
				padding:2px;
				width:240px;
		}

		.addImage
		{
				background: url(add.png) no-repeat 0px 1px;
				padding-left: 17px;    
				margin-bottom: 10px;    
				line-height:20px;
				clear:both:
						
		}
		.removeImage
		{
				background: url(delete.png) no-repeat 0px 1px;
				padding-left: 17px;    
				margin-bottom: 10px;    
				line-height:20px;
		}
		
		.athlates-white-board-parent-class{
			padding: 10px;
			border: 1px solid #7F7F7F;
			margin-bottom: 10px;	
			/*text-align: center;*/
		}
		
		.addresponder-with-email-templates-class span{
			padding-right: 30px;
		}
		
		.addresponder-with-email-templates-class select{
			background: transparent;
		  /*  width: 268px; */
		    padding: 5px;
		    font-size: 13px;
		    border: 1px solid #ccc; 
		    height: 30px;
		}
		
		.reponder-type{
			width: 150px;
		}				
		
		.responder-template{
			 width: 268px;
		}
		
		.responder-digit{
			width: 70px;
		}
		
		.responder_cform{
			background: transparent;
		    width: 268px;
		    padding: 5px;
		    font-size: 13px;
		    border: 1px solid #ccc; 
		    height: 30px;
		}
		
		.respond-select-guide{
			margin-right: 268px;
			padding: 10px;
		}
		
		.board-class-name{
			background: transparent;
			padding: 2px;
		    font-size: 17px;		  
		  }
		
						
</style>

<?php 
	$board_data = get_post_meta($post->ID, 'Athlates_White_Board', true);
		
?>


<div class='wrap'>
	
	<h4>Manage Athlates WhiteBoard</h4>
	<p>Classes Scheduled for this date</p>
	 <div class="athlates-white-board-parent-class" id="athlates-white-board-parent-id">
	 	<p class="board-class-name"> Class Name <input size="50" type="text" recname="cllassname" name="ahtlates-white-board-class-names[]" ></p>
		
		<table>
			<tr>
				<td>Component 1</td>
				<td><input type="text" recname="componentname1" name="ahtlates-white-board-component-names-1[]" ></td>
				<td><textarea rows="2" cols="60" recname="componentdes1" name="ahtlates-white-board-component-descriptions-1[]"></textarea></td>
			</tr>
			<tr>
				<td>Component 2</td>
				<td><input type="text" recname="componentname2" name="ahtlates-white-board-component-names-2[]" ></td>
				<td><textarea rows="2" recname="componentdes2" cols="60" name="ahtlates-white-board-component-descriptions-2[]"></textarea></td>
			</tr>
			<tr>
				<td>Component 3</td>
				<td><input type="text" recname="componentname3" name="ahtlates-white-board-component-names-3[]" ></td>
				<td><textarea rows="2" recname="componentdes3" cols="60" name="ahtlates-white-board-component-descriptions-3[]"></textarea></td>
			</tr>
		</table>
								
	 </div>	
	
	
</div>


<script type="text/javascript">
	jQuery('#athlates-white-board-parent-id').EnableMultiField({
		 data:[
				 <?php if($board_data){
				 		foreach($board_data as $key => $data){
				 			$com_name_1 = ($data['component'][0]['name']) ? $data['component'][0]['name'] : '';
				 			$com_des_1 = ($data['component'][0]['des']) ? $data['component'][0]['des'] : '';
				 			$com_name_2 = ($data['component'][1]['name']) ? $data['component'][1]['name'] : '';
				 			$com_des_2 = ($data['component'][1]['des']) ? $data['component'][1]['des'] : '';
				 			$com_name_3 = ($data['component'][2]['name']) ? $data['component'][2]['name'] : '';
				 			$com_des_3 = ($data['component'][2]['des']) ? $data['component'][2]['des'] : '';
						 	?>
						 	{
						       cllassname : "<?php echo $data['class'];?>",
						       componentname1 : "<?php echo $com_name_1;?>",
						       componentdes1 : "<?php echo $com_des_1;?>",
						       componentname2 : "<?php echo $com_name_2;?>",
						       componentdes2 : "<?php echo $com_des_2;?>",
						       componentname3 : "<?php echo $com_name_3; ?>",
						       componentdes3 : "<?php echo $com_des_3; ?>"
						    },
						 	<?php 
					 	}
				 }
				 ?>
		       
		]
	});
			
			
</script>
