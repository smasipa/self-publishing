<h3>Recharge geeWallet</h3>
<hr />
<div class="col-sm-7 col-xs-12">	
	<?php if($success):?>
		<h3>Account successefully recharged with R <?=set_value('amount');?></h3>
		<br/>
		<br/>
		<a href = '/monitor/geeWallet/recharge'>Recharge another account</a>
	<?php else:?>
		<form action="/monitor/geeWallet/recharge" enctype="multipart/form-data" method="post" accept-charset="utf-8">
		
		<?php if($matched_details):?>
		<div class="form-errors">Account matched : <?=$matched_details;?><br /></div>
		<?php endif;?>
		
		<label>Reference Code:</label>
		<br>					
		<input class="form-control" name="ref_code" type = 'text' value = "<?=set_value('ref_code');?>">	
		<div class="form-errors alert-danger"><?=form_error('ref_code');?></div>
		<br/>
		<?php if($matched_details):?>
			<label>Recharge Amount : R</label>
			<br/>
			
			<input class="form-control" name="amount" type = 'text' value = "<?=set_value('amount');?>">	
			<div class="form-errors alert-danger"><?=form_error('amount');?></div>
			<br/>
			
			<label>Please enter your password :</label>
			<br>
			
			<input class="form-control" name="password" type = 'password' value = "<?=set_value('password');?>">	
			<div class="form-errors alert-danger"><?=form_error('password');?></div>
			<br>
		<?php endif;?>
		<input class="form-control  btn-default" name="confirm" value="Confirm" type="submit">
		
		</form>	
		<div>Gamalami geeWallet - The simplest way to purchase online</div>
	<?php endif;?>
	
</div>
<div class = 'clearfix'></div>