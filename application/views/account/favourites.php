<h3>Favourites</h3>				
<hr>					
<div class = 'col-sm-12 col-xs-12 publications'>
	<?php  if($folders) foreach($folders as $folder) : ?>
	<div class = 'container-fluid publication pull-left'>
		<?php if($folder->cover_image) :?>
		<img class = 'pull-left' src = <?="'". $folder->cover_image->location ."'";?>  height = '100' width = '75'/><!--height = '100' width = '75'-->
		<?php endif;?>
		
		<div class = 'pull-left publication-about'>					
		<a href = <?=$folder->url;?>>					
		<?=$folder->name;?>					
		</a>					
		<?="<br/>".$folder->username."<br/>";?>	
		
		<?php if(isset($folder->edit)):?>
			<a href = <?="'".$folder->edit ."'";?>>Edit</a>	
			</br>
		<?php endif;?>
		
		</div>					
		<div class = 'clearfix'></div>					
	</div>	
	<?php endforeach;?>	
</div>			
<ul class = 'pagination'><?php print $pagination;?></ul>