<h2><?=$folder->name;?> 
<?php if($edit):?>
<a class = 'btn btn-default' href = 
<?=$edit;?>> Edit </a>
<?php endif;?>
</h2>	
<hr>
<?php if(isset($updated)):?>
		<?=$updated;?><br/><br/>
<?php endif;?>

<div class = 'col-sm-7 col-xs-12 profile-information panel'>
	<div class = 'panel-body'>
		<div class = 'pull-left profile-picture'> 
		<img src = <?=$folder->cover_image->location;?> /></div>
		<div class = 'pull-left col-sm-10 details folder'>
			<label>Author : </label> <?=$folder->author;?>
			<br/>
			<br/>

			<label>Publication Date: </label> <?=$folder->created;?>
			<br/>
			<br/>
			<div class = 'about user'>
			<label>Description: </label>
			<br/>
			<p>
				<?=$folder->description;?>
			</p>
			</div> 
		</div>
	</div>
</div>

<?php if($publications):?>
<div class = 'pull-right col-sm-4 col-xs-12 user-stats panel'>
	<div class = 'panel-heading'>
		<strong>Chapters (<?=count($publications);?>)</strong>
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
		<a href = '/publications'>show more</a>
	</div>
</div>
<?php endif;?>
<?php if(!empty($social_media)):?>						
	<div class = 'pull-right col-sm-4 col-xs-12 social-buttons' style = 'padding: 0 0 30px 0;'>						
		<a class ="btnz share facebook" id = 'share_button'						
		href ="#" ><i class ="fa fa-facebook" ></i> Share </a>			
				
		<a class ="btnz share twitter" href ="<?php print $social_media['twitter']; ?>"><i class = "fa fa-twitter" ></i> Tweet </a>				
				
		<a class ="btnz share whatsapp" href="<?php print $social_media['whatsapp']; ?>" ><i class = "fa fa-whatsapp" ></i> WhatsApp </a>		
				
		<a class ="btnz share gplus" href = "<?php print $social_media['gplus']; ?>"><i class = "fa fa-gplus" ></i> G+ </a>						
	</div>						
<?php endif;?>	
<div class = 'clearfix'></div>


