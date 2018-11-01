<h4>Wallet Recharges</h4>
<div class = 'pull-right panel col-xs-12 col-sm-12'>

	<?php if($recharges):?>
	<div class="table-responsive">          
	  <table class="table table-striped">         
<table class = 'table '>
	<thead>
		<tr>
			<th>
			#ID
			</th>
			<th>
			Cust email
			</th>			
			<th>
			Amount
			</th>				
			
			<th>
			Method
			</th>	
			
			<th>
			Date
			</th>	
			
			<th>
			Admin email
			</th>	
		</tr>

	</thead>
	<tbody>
	<?php  if($recharges) foreach($recharges as $item) : ?>						
		<tr>
			<td>				
				<?=$item['id'];?>
			</td>
			<td>				
				<a href = "/<?=$item['cust_username'];?>"><?=$item['cust_email'];?></a>
			</td>
			<td>				
				R <?=$item['amount'];?>
			</td>
			
			<td>				
				<?=$item['method'];?>
			</td>
			<td>				
				<?=$item['created'];?>
			</td>
			<td>	
				<a href = "/<?=$item['admin_username'];?>"><?=$item['admin_email'];?></a>
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