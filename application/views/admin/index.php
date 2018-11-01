<h4>Perfomance summary</h4>
<div class="panel">
	<div class="panel-body">
		<div class="col-xs-6 col-sm-3">
			<h3 class ='stats-numbers'><?=$perfomance['total_sales'];?></h3>
			<span class="text-muted">Total sales</span>          
		</div>  	
		
		<div class="col-xs-6 col-sm-3">
			<h3 class ='stats-numbers'><?=$perfomance['total_members'];?></h3>
			<span class="text-muted">Members</span>          
		</div>  	
		
		<div class="col-xs-6 col-sm-3">
			<h3 class ='stats-numbers'><?=$perfomance['total_views'];?></h3>
			<span class="text-muted">Total views</span>          
		</div>  	
		
		<div class="col-xs-6 col-sm-3">
			<h3 class ='stats-numbers'><?=$perfomance['total_signups'];?></h3>
			<span class="text-muted">This week sign-ups</span>          
		</div>   
	</div>
</div>
<div class = 'clearfix'></div>
<div class="panel col-xs-12  col-sm-12">
<div class = 'panel-heading'><h4>Recent activity</h4></div>
	<?php if($activities):?>
	<div class="table-responsive">          
	  <table class="table table-striped">          
		<thead>          
		  <tr>          
			<th>#</th>          
			<th>Admin email</th>          
			<th>Admin</th>          
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
<div class = 'clearfix'></div>