<h3>Cart
<a class = 'btn btn-default' href = 'cart/edit'>Edit</a>
</h3>
<!-- Setlalepula's publications-->					
<hr>					
<table class = 'table'>
	<thead>
		<tr>
			<th>
			Name
			</th>			
			<th>
			Date
			</th>			
			
			<th>
			Price
			</th>
		</tr>

	</thead>
	<tbody>
	<?php  if($cart) foreach($cart as $item) : ?>						
		<tr>
			<td>
			<a href = <?="'".$item->url."'";?>>					
				<?=$item->title;?></a>
			</td>		
			<td>				
				<?=$item->created;?>
			</td>			
			
			<td>				
				R <?=$item->price;?>
			</td>		
		</tr>
	<?php endforeach;?>
	<tr>
		<td>
		<strong>Total Cost (ZAR)</strong>
		</td>		
		<td>
		</td>		
		<td>
		<strong>R <?=$amount_due;?></strong>
		</td>
	</tr>
	</tbody>
</table>	
<a class = 'btn btn-warning' href = 'buy'>Checkout</a>
