<h4>Premium members</h4>
<div class = 'pull-right panel col-xs-12 col-sm-12'>
	<?php if($members):?>
	<div class="table-responsive">          
	  <table class="table table-striped">
		<thead>          
		  <tr>          
			<th>#</th>          
			<th>Email</th>          
			<th>Username</th>          
			<th>Subscription date</th>           
			<th>Joined</th>           
			<th>Subscription type</th>           
		  </tr>          
		</thead>          
		<tbody>  
			<?php foreach($members as $key => $member):?>
		  <tr>          
			<td><?=$key + 1;?></td>      
			
			<td><?=$member->email;?></td>
			<td><a href=<?="'/monitor/writers/details/".$member->username."'";?>><?=$member->username;?></td>
			<td><?=$member->joined;?></td> 	
			<td><?=$member->created;?></td> 	
			<td><?=$member->type;?></td>           
		  </tr>    
			<?php endforeach;?>
		</tbody>          
	  </table>          
	</div> 
	<?php endif;?>
</div>
<div class = 'clearfix'></div>