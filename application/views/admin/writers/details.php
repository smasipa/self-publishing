<h4>Writers > Details > <?=$username;?></h4>
<div class='pull-left panel col-xs-12 col-sm-12'>
	<div class = 'panel-body'>
	<label>First Name : </label> <span><?=$first_name;?></span>
	<br/>	
	<label>First Name : </label> <span><?=$last_name;?></span>
	<br/>	
	<label>ID Number  : </label> <span><?=$writer->id_number;?></span>
	<br/>	
	<label>Bank Account Number : </label> <span><?=$writer->account_number;?></span>
	<br/>	
	<label>Bank Name : </label> <span><?=$writer->bank_name;?></span>
	<br/>	
	<label>Phone Number : </label> <span><?=$writer->cellphone_number;?></span>
	<br/>
	<?php if(!$approved):?><label>Approved : </label> No <a class = 'btn btn-success' href = <?="'/monitor/writers/approve/{$username}/{$writer->user_id}'";?>>Approve</a>
	<?php else:?>
	<label>Approved : </label> Yes <a class = 'btn btn-danger' href = <?="'/monitor/writers/approve/{$username}/{$writer->user_id}?remove=1'";?>>Retract Approval</a>
	<?php endif;?>
	<div>
	<br/>
	<?php if($total_owed):?>
	
	<label>Amount owed: R<?=$total_owed;?></label>
	
	<a class = 'btn btn-warning' href = <?="'/monitor/payments/pay/{$username}/?amount={$total_owed}'";?>>Mark Payment As Made</a>
	</form>
	<br/>
	
	<?php else:?>
	<label>Amount owed: R0</label>
	<?php endif;?>
	</div>
	</div>
</div>

<div class = 'pull-right panel col-xs-12 col-sm-12'>
<div class = 'panel-heading'><h4>Payments made to <?=$username;?></h4></div>
	<?php if($payments):?>
	<div class="table-responsive">          
	  <table class="table table-striped">
		<thead>          
		  <tr>          
			<th>#</th>          
			<th>Admin email</th>          
			<th>Date</th>          
			<th>Amount paid</th>           
		  </tr>          
		</thead>          
		<tbody>  
			<?php foreach($payments as $key => $payment):?>
		  <tr>          
			<td><?=$key + 1;?></td>      
			<td><a href=<?="'/monitor/writers/details/".$payment->email."'";?>><?=$payment->email;?></td>
			
			<td><?=$payment->modified;?></td> 	
			
			<td>R<?=$payment->amount_paid;?></td>           
		  </tr>    
			<?php endforeach;?>
		</tbody>          
	  </table>          
	</div> 
	<?php endif;?>
</div>
<div class = 'clearfix'></div>