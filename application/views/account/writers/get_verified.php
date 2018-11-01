<?php if($request_recieved):?>
	<h2>Verification Status</h2>
	<p><?=$request_recieved;?></p>
<?php elseif($verification_status):?>
	<h2>Verification Status</h2>
	<p><?=$verification_status;?></p>
<?php else:?>
<h2>Get Verified</h2>
<hr>
<div class = 'col-sm-7 col-xs-12'>
	<?=form_open_multipart('get_verified');?>	
	<label>	
	ID number<span style = 'color:red;'>*</span>	
	</label>		
		
	<input class = 'form-control' type = 'text' name = 'id_number' value = <?="'".$posted['id_number']."'";?>/>	
	<?=form_error('id_number');?>	
	<br/>	
		
	<label>	
	Account number<span style = 'color:red;'>*</span>	
	</label>	
		
	<input class = 'form-control' type = 'text' name = 'account_number' value = <?="'".$posted['account_number']."'";?>/>	
	<?=form_error('account_number');?>	
	<br/>			
		
	<label>	
	Bank Name<span style = 'color:red;'>*</span>	
	</label>	
		
	<input class = 'form-control' type = 'text' name = 'bank_name' value = <?="'".$posted['bank_name']."'";?>/>	
	<?=form_error('bank_name');?>	
	<br/>			
		
	<label>	
	Phone Number	
	</label>	
		
	<input class = 'form-control' type = 'text' name = 'phone_number' value = <?="'".$posted['phone_number']."'";?>/>	
	<?=form_error('phone_number');?>	
	<br/>	
	<input class = 'btn btn-info' type = 'submit' name = 'save' value = 'Get verified'/> <a class = 'btn btn-danger' href = 'get_verified/info'>Back</a>	
	</form>	
</div>

<?php endif;?>