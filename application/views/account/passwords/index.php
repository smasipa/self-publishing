<h2>Reset Password</h2>
<hr>
<div class = 'col-sm-7 col-xs-12'>
	<?php if($email):?>
	Reset code has been sent to <strong><?=$email;?></strong>
	<br/>
	Did not recieve link? Click <a href='/password_reset'>here</a> to resend.
	<?php else:?>
	<?=form_open('/password_reset');?>	
		<label>Email:</label>	
		<br/>	
		<input class = 'form-control' type = 'email' name = 'email'/>	
		<?=form_error('email');?>	
		<br/>		
		<input class = 'form-control  btn-default' type = 'submit' name = 'save' value = 'Send'/>				
	</form>	
	<?php endif;?>
</div>