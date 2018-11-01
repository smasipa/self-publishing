<h4>Sign Ups</h4>
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
	
	<?php if($signups):?>
		<?php foreach($signups  as $user):?>
		<tr>
			<td>
			<?=$user->email;?>
			</td>
			
			<td>
			<?=$user->username;?>
			</td>

			<td>
			<?=$user->id;?>
			</td>
			
			<td>
			<?=$user->account_type;?>
			</td>
			
			<td>
			<?=$user->created;?>
			</td>
		</tr>
		<?php endforeach;?>
	<?php endif;?>
	</tbody>
	</table>
	</div>
</div>