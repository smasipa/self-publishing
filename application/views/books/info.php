<h2><?=$book->name;?> 
<?php if($edit):?>
<a class = 'btn btn-default' href = 
<?="'".$edit."'";?>> Edit </a>
<?php endif;?>
</h2>	
<hr>
<?php if(isset($updated)):?>
		<?=$updated;?><br/><br/>
<?php endif;?>

<div class = 'col-sm-7 col-xs-12 profile-information panel'>
	<div class = 'panel-body'>
		<div class = 'pull-left profile-picture'> 
		<img src = <?=$book->cover_image->location;?> width = '95' height = '140'/></div>
		<div class = 'pull-left col-sm-9 details book'>
			<label>Published By </label> <a href=<?="'/".$book->username."'";?>><?=$book->author ? $book->author : $book->username;?></a>
			<br/>
			<br/>			
			
			<?php if($document):?>
			<label>Price : </label> <strong><?php if($book->price):?>R<?=$book->price;?><?php else:?>FREE<?php endif;?></strong>
				<?php if($book->is_purchased):?>
					<a class = 'btn btn-danger' href = <?="'/".$document."'";?>>Download Book</a>
				<?php else:?>
					<a class = 'btn btn-warning' href = <?="'/".$document."'";?>>Buy Book</a>
				<?php endif;?>
			<br/>
			<br/>
			<?php endif;?>
			<label>Publication Date: </label> <?=$book->created;?>
			<br/>
			<br/>
			
			<?php if($book->tags):?>
			<div class = 'col-sm-12 no-padding'>
				<label>Tags :</label>
				<?php foreach($book->tags as $tag):?>
				 <a href=<?="'/search/?tag=".$tag['name']."'";?>><?=$tag['name'];?></a>
				<?php endforeach;?>
				
			</div>
			<br/>
			<br/>
			<?php endif;?>	
			
			<div class = 'about book'>
			<label>Description: </label>
			<br/>
			<p>
				<?=$book->description;?>
			</p>
			</div> 
		</div>
	</div>
</div>
<?php if(!empty($social_media)):?>						
	<div class = 'pull-right  social-buttons' style = 'padding: 0 0 30px 0;'>						
		<a class ="btnz share facebook" id = 'share_button'						
		href ="#" ><i class ="fa fa-facebook" ></i> Share </a>			
				
		<a class ="btnz share twitter" href ="<?php print $social_media['twitter']; ?>"><i class = "fa fa-twitter" ></i> Tweet </a>				
				
		<a class ="btnz share whatsapp" href="<?php print $social_media['whatsapp']; ?>" ><i class = "fa fa-whatsapp" ></i> WhatsApp </a>		
				
		<a class ="btnz share gplus" href = "<?php print $social_media['gplus']; ?>"><i class = "fa fa-gplus" ></i> G+ </a>						
	</div>						
<?php endif;?>
<div class = 'clearfix'></div>