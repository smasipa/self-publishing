<h2>Reset Password</h2>
<hr>
<div class = 'col-sm-7 col-xs-12'>	
		<?=form_open('/'.$form_url);?>
			<label>Username:</label>
			<br/>
			
			<input class = 'form-control' type = 'text' name = 'username' value = <?="'".$posted['username']."'";?>/>
			<?=form_error('username');?>
			<br/>
			<label>New password:</label>
			<br/>					
			<input class = 'form-control' type = 'password' name = 'new_password'/>	
			<?=form_error('new_password');?>
			
			<br/>		
			<label>Repeat password:</label>
			<br/>					
			<input class = 'form-control' type = 'password' name = 'repeat_password' />	
			<?=form_error('repeat_password');?>
			<br/>	
			<input class = 'form-control  btn-default' type = 'submit' name = 'reset' value = 'Reset'/>			
		</form>	
</div>