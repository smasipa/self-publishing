<h2>Profile 
<?php if(2 > 3):?>
	<a class = 'btn btn-default' href = 'edit'>Edit</a>
<?php endif;?>
</h2>
<hr>
<?php if($updated):?>
		<?=$updated;?><br/><br/>
<?php endif;?>
<div class = 'container-fluid no-padding'>
<div class = 'col-sm-7 col-xs-12 profile-information panel'>
	<div class = 'panel-body'>
		<div class = 'pull-left profile-picture'> 
		<img src = <?=$profile_image;?> width = '95' height = '140' placeholder='profile picture'/></div>
		<div class = 'pull-left col-sm-9 details user'>
			<?php if($edit):?>
			<a href =<?='profile/edit/profile_image';?>> Change profile picture</a>
			<br/>
			<br/>
			<?php endif;?>
			<label><?=$user->username;?></label>
			<br/>
			<br/>
			
			<?php if($user->first_name || $edit):?><label>First Name: </label> <?=$user->first_name;?> <?php if($edit):?><a href =<?='profile/edit/first_name';?>> Edit </a><?php endif;?>
			<br/>
			<br/>
			<?php endif;?>
			
			<?php if($user->last_name || $edit):?>
			<label>Last Name: </label> <?=$user->last_name;?> <?php if($edit):?><a href =<?='profile/edit/last_name';?>> Edit </a><?php endif;?>
			<br/>
			<br/>
			<?php endif;?>
			<div class = 'about user'>
			<label>About: </label><?php if($edit):?><a href =<?='profile/edit/about';?>> Edit </a><?php endif;?>
			<br/>
			<p>
				<?=$user->about;?>
			</p>
			</div> 
		</div>
	</div>
</div>
<?php if(!empty($social_media)):?>						
	<div class = 'pull-right col-sm-4 col-xs-12 social-buttons' style = 'padding: 0 0 30px 0;'>						
		<a class ="btnz share facebook" id = 'share_button'						
		href ="#" ><i class ="fa fa-facebook" ></i> Share </a>			
				
		<a class ="btnz share twitter" href ="<?php print $social_media['twitter']; ?>"><i class = "fa fa-twitter" ></i> Tweet </a>				
				
		<a class ="btnz share whatsapp" href="<?php print $social_media['whatsapp']; ?>" ><i class = "fa fa-whatsapp" ></i> WhatsApp </a>		
				
		<a class ="btnz share gplus" href = "<?php print $social_media['gplus']; ?>"><i class = "fa fa-gplus" ></i> G+ </a>						
	</div>						
<?php endif;?>


<?php if($books):?>
<div class = 'pull-right col-sm-4 col-xs-12 user-stats panel'>
	<div class = 'panel-heading'>
		<strong>Books By this author</strong>
	</div>
	<div class = 'panel-body'>
		<ul class = 'list-group more-from-list'>

		<?php foreach($books as $book):?>
			<li><a href = <?="'".$book->url."'";?>><?=$book->title;?></a></li>
			<br/>
		<?php endforeach;?>
		</ul>
	</div>
	<div class = 'text-center more-from-panel'>
		<a href = '/books'>show more</a>
	</div>
</div>
<div class = 'clearfix'></div>
<?php endif;?>

<?php if($publications):?>
<div class = 'pull-right col-sm-4 col-xs-12 user-stats panel'>
	<div class = 'panel-heading'>
		<strong>Publications By this author</strong>
	</div>
	<div class = 'panel-body'>
		<ul class = 'list-group more-from-list'>

		<?php foreach($publications as $publication):?>
			<li><a href = <?="'".$publication->url."'";?>><?=$publication->title;?></a></li>
			<br/>
		<?php endforeach;?>
		</ul>
	</div>
	<div class = 'text-center more-from-panel'>
		<a href = <?=$user->username.'/publications';?>>show more</a>
	</div>
</div>
<?php endif;?>		
<div class = 'clearfix'></div>
</div>
