<?php if($msg):?>
<h3><?=$msg;?></h3>

<?php else:?>
<h3>Check out
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
				<a href =<?="'".$item->url."'";?>><?=$item->title;?></a>
			</td>

			<td>
				<?=$item->type;?>
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
<?php endif;?>