<?=form_open('/login');?>
<h2>Login</h2>
<hr>
<?=form_error('email');?>
	<div class = 'col-sm-5'>
	<label>Email</label>
	<br/>
	<input class = 'form-control' type = 'email' name = 'email' value = <?="'".$email."'";;?>/>	
	<br/>
	<label>Password</label>
	<br/>
	<input class = 'form-control' type = 'password' name = 'password' />
	<?=form_error('password');?>
	<br/>
	<input class = 'form-control' type = 'submit' name = 'login' value = 'login'/>
	<br /> 
	<a href = '/password_reset'>Forgot password?</a>
	</div>
</form>
<div class = 'clearfix'></div>