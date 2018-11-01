<h3><?=$folder_name;?> 
<?php if($edit):?>
<a class = 'btn btn-default' href = 
<?=$edit;?>> Edit </a>
<?php endif;?>
</h3>				
<hr>					
<div class = 'col-sm-12 col-xs-12 publications'>
	<?php  if($posts) foreach($posts as $post) : ?>
	<ul class = 'container-fluid'>
		<li class = 'pull-left'>					
		<a href = <?="'".$post->url."'";?>>					
		<?=$post->title;?>					
		</a>
		<?php if(isset($post->edit)):?>
			</br>
			<a href = <?="'".$post->edit ."'";?>>Edit</a>	
			</br>
		<?php endif;?>		
		</li>				
	</ul>	
	<?php endforeach;?>	
	
	
	
</div>			
<ul class = 'pagination'><?php //print $pagination;?></ul>
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
</div>							
<div class = 'clearfix'></div>	
-->						
					
