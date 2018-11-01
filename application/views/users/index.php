<h3>
Authors
</h3>			
<hr>					
<div class = 'col-sm-12 col-xs-12 publications'>
	<?php  if($authors) foreach($authors as $item) : ?>
	<div class = 'container-fluid publication pull-left item-data'>
		<?php if($item->cover_image) :?>
		<a href = <?=$item->url;?>><img class = 'pull-left' src = <?="'". $item->cover_image->location ."'";?>/></a>
		<?php endif;?>
		
		<div>				
		<a href = <?=$item->url;?>>					
		<?=App\Helper\String::ellipsis($item->name);?>					
		</a>
		<?php if(isset($item->edit)):?>
			| <a href = <?="'".$item->edit ."'";?>>Edit</a>	
			</br>
		<?php endif;?>
		</div>		
		
		<div class = 'clearfix'></div>					
	</div>	
	<?php endforeach;?>	
</div>			
<ul class = 'pagination'><?php print $pagination;?></ul>
<!--
<div class = 'col-sm-12 col-xs-12 container-fluid pull-right related-content'>					
	 Only show for when in 	sub path e.g publications/diary-of-blesser-season-2/					
	<div class = 'container-fluid'>More From This Author					
		<ul class = 'list-group more-from-list'>					
			<li><a href = 'home'>Guns & Roses</a></li>					
			<li><a href = 'home'>Minagerie</a></li>					
			<li><a href = 'home'>Manipulate the user database</a></li>					
			<li><a href = 'home'>Specific to selecting rows</a></li>					
		</ul>					
	</div>									
					
	<div class = 'container-fluid'>Share					
		<ul class = 'list-group more-from-list'>					
			<li><a href = 'home'>Facebook</a></li>					
			<li><a href = 'home'>Twitter</a></li>					
		</ul>					
	</div>					
	<div class = 'clearfix'></div>					
</div>-->						
<div class = 'clearfix'></div>	