<h3>Cart 
</h3>

<!-- Setlalepula's publications-->					
<hr>	
Remove items from cart	
<?=form_open('cart/remove');?>			
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
				<input type = 'checkbox' name = 'products[]' value = <?="'".$item->id.",1'";?>>
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
<input type ='submit' class = 'btn btn-danger' value = 'Remove'>	
<a class = 'btn btn-success' href = 'cart'>Discard</a>	
</form>	
