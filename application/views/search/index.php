<?php if($search_results):?>
<h3>Search Results</h3>
<hr>					
<div class = 'col-sm-12 col-xs-12 publications'>
	<?php if($search_results) foreach($search_results as $result_type) : ?>
		<?php  if(sizeof($result_type)) foreach($result_type as $item):?>
		<div class = 'container-fluid pull-left item-data'>
			<?php if($item->cover_image) :?>
			<a href = <?=$item->url;?>><img class = 'pull-left' src = <?="'". $item->cover_image->location ."'";?>/></a>
			<?php endif;?>
			<div ><a href = <?=$item->url;?>>					
			<?=App\Helper\String::ellipsis($item->name);?>					
			</a></div>				
		</div>	
		<?php endforeach;?>
	<?php endforeach;?>	
	<div class = 'clearfix'></div>
</div>
<?php else:?>
<h3>Oops! nothing was found</h3>
<hr>
<div class = 'col-sm-12 col-xs-12 publications'>
Please try a different search.
</div>
<?php endif;?>			
<div class = 'clearfix'></div>