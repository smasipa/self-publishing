<h4>Logins</h4>
<div class = 'panel col-xs-12 col-sm-12'>
	<div class = 'table-responsive'>
	<table class ='table table-striped'>
	<thead>
	<tr>
		<th>
		User email
		</th>
		
		<th>
		Username
		</th>
		
		<th>
		User ID
		</th>
		<th>
		Account type
		</th>
		
		<th>
		Date
		</th>
	</tr>
	</thead>
	<tbody>
	
	<?php if($logins):?>
		<?php foreach($logins  as $login):?>
		<tr>
			<td>
			<?=$login->email;?>
			</td>
			
			<td>
			<?=$login->username;?>
			</td>

			<td>
			<?=$login->user_id;?>
			</td>
			
			<td>
			<?=$login->account_type;?>
			</td>
			
			<td>
			<?=$login->created;?>
			</td>
		</tr>
		<?php endforeach;?>
	<?php endif;?>
	</tbody>
	</table>
	</div>
</div>