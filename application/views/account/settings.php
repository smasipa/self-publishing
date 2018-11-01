<h2>Account Settings</h2>
<hr>
<div class = 'col-sm-7 col-xs-12'>	
		<?php if(!$edit): // Showing user settings?>
		
		<?php if($updated):?>
			<?=$updated;?><br/><br/>
		<?php endif;?>
		
		<label>Joined: </label> <?=$user->created;?>
		<br/>		
		<br/>		
		<label>Username: </label> <?=$user->username;?> <a href = 'settings/edit/username'> Edit </a>
		<br/>		
		<br/>		
		<label>Email: </label> <?=$user->email;?><a href = 'settings/edit/email'> Edit </a>
		<br/>		
		<br/>		
		<label>Account type: </label> 
		
		<?=$user->account_type;?> 
		
		<?php if($user->account_type != 'premium' &&  1 > 2):?>
		<a class ='btn btn-warning' href = 'premium'> Go premium </a>
		<?php endif;?>
		<br/>		
		<br/>
		
		<label>Verified publisher: </label> 
		<?php if($user->approved_writer):?>
			Yes
		<?php else:?>
			Not yet
			<a href = 'get_verified'> Get verified?
			</a>
		<?php endif;?>
		<br/>		
		<br/>		
		<label>Password:</label> <a href = 'settings/edit/password'> Change password </a>
		<br/>
		<br/>
		<?php endif;?>
		
		<?php if($edit)://Editing ?>
		<?=form_open('settings/edit/'.$edit);?>
			<?php if($edit == 'username'):?>
			<label>Username</label>
			<br/>					
			<input class = 'form-control' name = 'username' value = <?="'".$posted['username']."'";?>/>	
			<?=form_error('username');?>
			<br/>			
			<?php endif;?>
			
			<?php if($edit == 'email'):?>
			<label>New Email:</label>
			<br/>
			<input class = 'form-control' type = 'email' name = 'new_email' value = <?="'".$posted['new_email']."'";?>/>	
			<?=form_error('new_email');?>
			
			<br/>			
			<label>Confirm Email:</label>
			<br/>					
			<input class = 'form-control' type = 'email' name = 'repeat_email' value = <?="'".$posted['repeat_email']."'";?>/>	
			<?=form_error('repeat_email');?>			
			
			<br/>			
			<label>Please provide valid password:</label>
			<br/>					
			<input class = 'form-control' type = 'password' name = 'password' value = "<?=set_value('password');?>"/>	
			<?=form_error('password');?>
			<br/>
			<?php endif;?>
			
			<?php if($edit == 'password'):?>
			<label>Old password</label>
			<br/>					
			<input class = 'form-control' type = 'password' name = 'old_password' />	
			<?=form_error('old_password');?>
			
			<br/>		
			<label>New password</label>
			<br/>					
			<input class = 'form-control' type = 'password' name = 'new_password' value = <?="'".$posted['new_password']."'";?>/>	
			<?=form_error('new_password');?>
			
			<br/>		
			<label>Repeat password</label>
			<br/>					
			<input class = 'form-control' type = 'password' name = 'repeat_password' />	
			<?=form_error('repeat_password');?>
			<br/>	
			<?php endif;?>
			<input class = 'form-control  btn-default' type = 'submit' name = 'save' value = 'Submit'/>			
		</form>	
		<?php endif;?>
</div>
<div class = 'clearfix'></div>