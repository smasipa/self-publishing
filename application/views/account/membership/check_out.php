<h3><?=$msg;?>
</h3>				
<hr>					
<table class = 'table '>
	<thead>
		<tr>
			<th>
			Name
			</th>			
			
			<th>
			Item Type
			</th>
			
			<th>
			Date
			</th>	
			
			<th>
			Total Price
			</th>
		</tr>

	</thead>
	<tbody>					
		<tr>
			<td>			
				<a href =<?="'".$item->url."'";?>><?=$item->type;?></a>
			</td>

			<td>
				Premium subscription
			</td>
			
			<td>				
				<?=$item->created;?>
			</td>
			
			<td>				
				<strong>R <?=$item->price;?></strong>
			</td>		
		</tr>
	</tbody>
</table>
<?=$chech_out_btn;?>