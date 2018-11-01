<h4>Activity</h4>
<div class="panel col-xs-12  col-sm-12">
<div class = 'panel-heading'><h4>Recent activity</h4></div>
	<?php if($activities):?>
	<div class="table-responsive">          
	  <table class="table table-striped">          
		<thead>          
		  <tr>          
			<th>#</th>          
			<th>User email</th>          
			<th>Username</th>          
			<th>Action</th>  
			<th>Item type</th> 
			<th>Item ID</th>    
			<th>Item name</th>          
			<th>Date</th>           
			<th>Description</th>           
		  </tr>          
		</thead>          
		<tbody>  
			<?php foreach($activities as $key => $activity):?>
		  <tr>          
			<td><?=$key + 1;?></td>          
			<td><?=$activity->email;?></td>          
			<td><?=$activity->username;?></td>          
			<td><?=$activity->action_type;?></td>          
			<td><?=$activity->item_type;?></td>          
			<td><?=$activity->item_id;?></td>          
			<td><?=$activity->item_name;?></td>
			<td><?=$activity->created;?></td>           
			<td><?=$activity->description;?></td>           
		  </tr>    
			<?php endforeach;?>
		</tbody>          
	  </table>          
	</div> 
	<?php endif;?>
</div>