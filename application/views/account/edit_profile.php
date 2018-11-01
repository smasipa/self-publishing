<h2>Edit Profile</h2>
<hr>
<div class = 'col-sm-7 col-xs-12'>	
		<?php if($edit)://Editing ?>
		<?=form_open_multipart('profile/edit/'.$edit);?>
			<?php if($edit == 'profile_image'):?>
			<label>Choose new profile image:</label>
			<br/>					
			<input  type = 'file' name = 'image'/>	
			<?=form_error('image');?>
			<br/>			
			<?php endif;?>			
			
			<?php if($edit == 'first_name'):?>
			<label>First Name:</label>
			<br/>					
			<input class = 'form-control' type = 'text' name = 'first_name' value = <?="'".$posted['first_name']."'";?>/>	
			<?=form_error('first_name');?>
			<br/>			
			<?php endif;?>
			
			<?php if($edit == 'last_name'):?>
			<label>Last Name</label>
			<br/>
			<input class = 'form-control' type = 'text' name = 'last_name' value = <?="'".$posted['last_name']."'";?>/>	
			<?=form_error('last_name');?>
			<br/>
			<?php endif;?>			
			
			<?php if($edit == 'about'):?>
			<label>About:</label>
			<br/>
			<?=form_error('about');?>
			<textarea class = 'form-control' name = 'about' rows = 9><?=$posted['about'];?></textarea>	
			<br/>
			<?php endif;?>
			
			<input class = 'form-control  btn-default' type = 'submit' name = 'save' value = 'Submit'/>			
		</form>	
		<?php endif;?>
		<div class = 'clearfix'></div>
</div>