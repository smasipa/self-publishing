<h3>
<?php if($author_name):?>
	Publications by <?=$author_name;?>
<?php else:?>
	Publications
<?php endif?>
</h3>			
<hr>					
<div class = 'col-sm-12 col-xs-12 publications'>
	<?php  if($folders) foreach($folders as $folder) : ?>
	<div class = 'container-fluid publication pull-left item-data'>
		<?php if($folder->cover_image) :?>
		<a href = "<?=$folder->url;?>"><img class = 'pull-left' src = <?="'". $folder->cover_image->location ."'";?>/></a>
		<?php endif;?>
		
		<div class = 'pull-left'>				
		<a href = <?=$folder->url;?>>					
		<?=App\Helper\String::ellipsis($folder->name);?>					
		</a>
		<?php if(isset($folder->edit)):?>
			| <a href = <?="'".$folder->edit ."'";?>>Edit</a>	
			</br>
		<?php endif;?>
		</div>		
		
		<div class = 'clearfix'></div>					
	</div>	
	<?php endforeach;?>	
</div>			
<ul class = 'pagination'><?php print $pagination;?></ul>
<div class = 'clearfix'></div>