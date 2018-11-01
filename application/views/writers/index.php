<h4>Performance Statistics</h4>
<div class="panel">
	<div class="panel-body">
		<div class="col-xs-6 col-sm-3">
			<h3 class ='stats-numbers'><?=$performance['total_sales'];?></h3>
			<span class="text-muted">Total sales</span>          
		</div>  	
		
		<div class="col-xs-6 col-sm-3">
			<h3 class ='stats-numbers'>R <?=$performance['total_due'];?></h3>
			<span class="text-muted">Total due</span>          
		</div>  	
		
		<div class="col-xs-6 col-sm-3">
			<h3 class ='stats-numbers'><?=$performance['total_views'];?></h3>
			<span class="text-muted">Total views</span>          
		</div>  	
		
		<div class="col-xs-6 col-sm-3">
			<h3 class ='stats-numbers'><?=$performance['books_downloaded'];?></h3>
			<span class="text-muted">Books downloads</span>          
		</div>   
	</div>
</div>
<div class = 'clearfix'></div>
<div class="panel col-xs-12  col-sm-5 pull-left">
<div class = 'panel-heading'><h4>Books Sold</h4></div>
	<div class = 'panel-body'>
	<?php foreach($sold_books  as $key => $pub):?>
		<?=$key + 1;?>. <a href =<?="'".$pub['url']."'";?>><?=$pub['name'];?></a> | sales(<?=$pub['total_sales'];?>)
		<br />
	<?php endforeach;?>
	</div>
</div>

<div class="panel col-xs-12  col-sm-5 pull-right">
<div class = 'panel-heading'><h4>Your Top 40 Performers</h4></div>
	<div class = 'panel-body'>
	Publications <br />
	<?php foreach($top_3['publications'] as $key => $pub):?>
		<?=$key + 1;?>. <a href =<?="'".$pub['url']."'";?>><?=$pub['name'];?></a> | views(<?=$pub['num_views'];?>)
		<br />
	<?php endforeach;?>
	<br />
	Folders<br />
	<?php foreach($top_3['folders'] as $key => $pub):?>
		<?=$key + 1;?>. <a href =<?="'".$pub['url']."'";?>><?=$pub['name'];?></a> | views(<?=$pub['num_views'];?>)
		<br />
	<?php endforeach;?>
	</div>	
	

</div>
<div class = 'clearfix'></div>