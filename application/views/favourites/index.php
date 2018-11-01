<?php if($favourites):?>
<h3>Your favourites</h3>
<hr>					
<div class = 'col-sm-12 col-xs-12 publications'>
	<?php if($favourites) foreach($favourites as $fav_type) : ?>
		<?php  foreach($fav_type as $item):?>
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
	<?php endforeach;?>	
</div>
<?php else:?>
<h3>You have no items in your favourites</h3>
<hr>
<div class = 'col-sm-12 col-xs-12 publications'>
Add items that you like to your favourites list.
</div>
<?php endif;?>			
