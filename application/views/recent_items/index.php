<?php if($recent_items):?>
<h3>Your Recents List</h3>
<hr>					
<div class = 'col-sm-12 col-xs-12 publications'>
	<?php if($recent_items) foreach($recent_items as $fav_type) : ?>
		<?php  foreach($fav_type as $item):?>
	<div class = 'container-fluid publication pull-left item-data'>
		<?php if($item->cover_image) :?>
		<a href = <?=$item->url;?>><img src = <?="'". $item->cover_image->location ."'";?>/></a>
		<?php endif;?>
		
		<div>				
		<a href = <?=$item->url;?>>					
		<?=App\Helper\String::ellipsis($item->name);?>					

		<?php if(isset($item->edit)):?>
			| <a class = 'pull-left' href = <?="'".$item->edit ."'";?>>Edit</a>	
			</br>
		<?php endif;?>
		</div>		
		
		<div class = 'clearfix'></div>					
	</div>
		<?php endforeach;?>
	<?php endforeach;?>	
</div>
<?php else:?>
<h3>You have no items in your Recents List</h3>
<hr>
<div class = 'col-sm-12 col-xs-12 publications'>
Whenever you read something it automatically gets added to your recents list.
</div>
<?php endif;?>			
<div class = 'clearfix'></div>