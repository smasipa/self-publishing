<h4>Purchases</h4>
<div class = 'pull-right panel col-xs-12 col-sm-12'>
<div class = 'panel-heading'><h4>Purchases</h4></div>
	<?php if($purchases_approved):?>
	<div class="table-responsive">          
	  <table class="table table-striped">         
<table class = 'table '>
	<thead>
		<tr>
			<th>
			ID
			</th>			
			<th>
			Name
			</th>			
			<th>
			Date
			</th>	
			
			<th>
			Customer Email
			</th>			
			
			<th>
			Price
			</th>			
			
			<th>
			Status
			</th>			
			
			<th>
			Method
			</th>
		</tr>

	</thead>
	<tbody>
	<?php  if($purchases_approved) foreach($purchases_approved as $item) : ?>						
		<tr>
			<td>				
				<?=$item->id;?>
			</td>
			<td>			
				<a href =<?="'".$item->url."'";?>><?=$item->name;?></a>
			</td>		
			<td>				
				<?=$item->created;?>
			</td>

			<td>				
				<a href = "/<?=$item->cust_username;?>"><?=$item->cust_email;?></a>
			</td>
			
			<td>				
				R <?=$item->payment_amount;?>
			</td>		
			<td>				
				<?=$item->status;?>
			</td>			
			<td>				
				<?=$item->method;?>
			</td>		
		</tr>
	<?php endforeach;?>
	</tbody>
</table>	         
	</div> 
	<?php endif;?>
</div>
<div class = 'clearfix'></div>