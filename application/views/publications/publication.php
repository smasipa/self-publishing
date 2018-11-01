<h3><?=$title;?>
<?php if(!$premium_member && 1 > 2):?>
	<?php if($premium):?> <a class = 'btn btn-warning' href = '/premium'>Go Premium</a><?php endif;?>
<?php endif;?>

</h3>				
<hr>				
<div class = 'pull-left'>Publisher : <strong><a href =<?="'/".$author_name."'";?>><?=$author_name;?></a></strong>, <?=$date;?> | <?=$word_count;?> | views <?=$num_views;?></div>
				
<div class = 'container-fluid pull-left'>
	<?php if($download_pdf):?>				
	<a href = <?="'/".$download_pdf."'";?>><div class = 'btn btn-danger'>Download File</div></a>	
	<?php endif;?>	
	
	<?php if($in_favs):?>
		<a href = <?="'/".$in_favs."'";?>><div class = 'btn btn-default'>Unfav</div></a>
	<?php else:?>
		<a href = <?="'/".$add_to_favs."'";?>><div class = 'btn btn-default'>+Fav</div></a>
	<?php endif;?>
	
	<?php if($is_author):?>
		<a class = 'btn btn-default' href = <?="/{$edit_publication}";?>>Edit</a>
	<?php endif;?>
</div>	
			
<div class = 'clearfix'></div>	
			
<?php if($bread_crumb):?><div>You are here : <?=$bread_crumb;?></div><?php endif;?>				
<div class = 'col-sm-7 col-xs-12 read-platform'>	
<?=$text;?>	

	<?php if($tags):?>
	
		<br/>
		<br/><br/>
	<div class = 'col-sm-12 no-padding'>
		Tags :
		<?php foreach($tags as $tag):?>
		 <a href=<?="'/search/?tag=".$tag['name']."'";?>><?=$tag['name'];?></a>
		<?php endforeach;?>
	</div>
	<?php endif;?>	

	

	<?php if($paginate):?>
	<br/>
	<br/>
	<div class = 'col-sm-12 no-padding'>
		<?php if(isset($paginate['prev'])):?>
		<a class = 'pull-left btn btn-success' href = <?=$paginate['prev'];?>>Previous</a>
		<?php endif;?>	
		
		<?php if(isset($paginate['next'])):?>
		<a class = 'pull-right btn btn-success' href = <?=$paginate['next'];?>>Next</a>
		<?php endif;?>
	</div>
	<?php endif;?>
</div>	

<div class = 'col-sm-3 col-xs-12 container-fluid pull-right'>				
	<?php if(!$is_logged_in):?>
	<div class = 'container-fluid'>
	Get access to exlcusive content!
	<br/>	
	<br/>	
	<a class = "btn btn-primary" href='/register'>Become a member</a>
	</div>	
	<br/>	
	<?php endif;?>
	
	<?php if($similar):?>
	<div class = 'container-fluid'>Related Publications		
		<ul class = 'list-group more-from-list'>
			<?php foreach($similar as $item):?>
			<li><a href = <?="'/".$item->url."'";?>><?=$item->title;?></a></li>
			<?php endforeach;?>
		</ul>				
	</div>	
	<br/>
	<?php endif;?>
			
	
	
	<?php if($more_from_author):?>
	<div class = 'container-fluid'>More From This Author				
		<ul class = 'list-group more-from-list'>
			<?php foreach($more_from_author as $item):?>
			<li><a href = <?="'/".$item->url."'";?>><?=$item->title;?></a></li>
			<?php endforeach;?>				
		</ul>				
	</div>	
	<?php endif;?>
	<br/>
<?php if(!empty($social_media)):?>						
	<div class = 'pull-left col-xs-12 col-sm-12  social-buttons' style = 'padding: 0 0 30px 0;'>						
		<a class ="btnz share facebook" id = 'share_button'						
		href ="#" ><i class ="fa fa-facebook" ></i> Share </a>			
				
		<a class ="btnz share twitter" href ="<?php print $social_media['twitter']; ?>"><i class = "fa fa-twitter" ></i> Tweet </a>				
				
		<a class ="btnz share whatsapp" href="<?php print $social_media['whatsapp']; ?>" ><i class = "fa fa-whatsapp" ></i> WhatsApp </a>		
				
		<a class ="btnz share gplus" href = "<?php print $social_media['gplus']; ?>"><i class = "fa fa-gplus" ></i> G+ </a>						
	</div>						
<?php endif;?>	
	<div class = 'clearfix'></div>				
</div>				
<div class = 'col-sm-8 col-xs-12 comments'>	
	
	<div class = 'comments-heading'>Comments (<?=count($comments);?>)</div>				
	<div>
		<?=form_open($comment_form_url)?>
			<textarea class = 'form-control' name = 'text'></textarea>
			<br/>
			<input style = 'width:150px;'class = 'form-control' type ='submit' name = 'comment' value = 'Write comment'/>
		</form>
	</div>	

	<?php if($comments):?>
		<?php ; foreach($comments as $comment):?>
		<div class = 'comment-container'>				
			<div class = 'col-sm-12'><label><a href =<?="'/".$comment->author."'";?>><?=$comment->author;?></a></label><span> <?=$comment->created;?>
			</span> <?php if($is_author):?><a href = <?="'/".$publication_url."/?remove_comment=1&c_id=".$comment->id."'";?>>Delete</a><?php endif;?></div>				
			<p class = 'col-sm-12'><?=nl2br($comment->text);?></p>				
			<div class = 'clearfix'></div>				
		</div>
		<?php endforeach;?>
	<?php endif;?>
</div>										
<div class = 'clearfix'></div>			
