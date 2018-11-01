<h4>Banned list</h4>
<div class = 'pull-right panel col-xs-12 col-sm-12'>

	<?php if($banned_list):?>
	<div class="table-responsive">          
	  <table class="table table-striped">         
<table class = 'table '>
	<thead>
		<tr>
			<th>
			Admin email
			</th>			
			<th>
			Item type
			</th>			
			<th>
			Item ID
			</th>	
			
			<th>
			Item name
			</th>			
			
			<th>
			Date
			</th>
		</tr>

	</thead>
	<tbody>
	<?php  if($banned_list) foreach($banned_list as $item) : ?>						
		<tr>
			<td>				
				<?=$item->email;?>
			</td>
			<td>			
				<?=$item->item_type;?>
			</td>		
			<td>				
				<?=$item->item_id;?>
			</td>

			<td>				
				<?=$item->item_name;?>
			</td>
			
			<td>				
				<?=$item->modified;?>
			</td>		
		</tr>
	<?php endforeach;?>
	</tbody>
</table>	         
	</div> 
	<?php else:?>
	<div class = 'panel-heading'><h4>None</h4></div>
	<?php endif;?>
</div>
<div class = 'clearfix'></div>