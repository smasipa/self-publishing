<h3>Purchases
</h3>				
<hr>					
<table class = 'table '>
	<thead>
		<tr>
			<th>
			Name
			</th>			
			<th>
			Date
			</th>	
			
			<th>
			Status
			</th>			
			
			<th>
			Price
			</th>
		</tr>

	</thead>
	<tbody>
	<?php  if($purchases) foreach($purchases as $item) : ?>						
		<tr>
			<td>			
				<a href =<?="'".$item->url."'";?>><?=$item->name;?></a>
			</td>		
			<td>				
				<?=$item->created;?>
			</td>

			<td>				
				<?=$item->status;?>
			</td>
			
			<td>				
				R <?=$item->payment_amount;?>
			</td>		
		</tr>
	<?php endforeach;?>
	</tbody>
</table>	
