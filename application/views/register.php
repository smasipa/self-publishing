<?=form_open('/register');?>
<h2>Register</h2>
<hr>
	<div class = 'col-sm-5'>
	</br>
	<label>Username</label>
	</br>
	<input class = 'form-control' type = 'text' name = 'username' value = <?="'".$username."'";?>/>
	<?=form_error('username');?>
	</br>
	<label>Email</label>
	<br/>
	<input class = 'form-control' type = 'text' name = 'email' value = <?="'".$email."'";;?>/>
	<?=form_error('email');?>	
	<br/>
	<label>Password</label>
	<br/>
	<input class = 'form-control' type = 'password' name = 'password' />
	<?=form_error('password');?>
	
	<br/>
	<input class = 'form-control' type = 'submit' name = 'save' value = 'sign up'/>
	<br/>
	Already a member? Please <a href = '/login'>Login</a>
	</div>
</form>
<div class = 'clearfix'></div>