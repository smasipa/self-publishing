<h4>Writers</h4>
<ul class='pull-left list-group col-xs-12 col-sm-7 no-padding'>
<li class = 'list-group-item'><h4>Requests(<?=count($pending_writers);?>)</h4></li>
<?php if($pending_writers):?>
<?php foreach($pending_writers as $writer):?>
<li class = 'list-group-item'><a href = <?="'/".$writer->user->url."'";?>><?=$writer->user->username;?></a> <strong>&middot </strong> <a href = <?="'/monitor/writers/details/{$writer->user->username}'";?>>View Details</a><span class = 'pull-right'><?=$writer->created;?></span></li>
<?php endforeach;?>
<?php else:?>
<li class = 'list-group-item'>none</li>
<?php endif;?>
</ul>

<ul class='pull-right list-group col-xs-12 col-sm-5 no-padding'>
<li class = 'list-group-item'><h4>Approved(<?=count($approved_writers);?>)</h4></li>
<?php if($approved_writers):?>
<?php foreach($approved_writers as $writer):?>
<li class = 'list-group-item'><a href = <?="'/".$writer->user->url."'";?>><?=$writer->user->username;?></a> <strong>&middot </strong> <a href = <?="'/monitor/writers/details/{$writer->user->username}'";?>>View Details</a><span class = 'pull-right'><?=$writer->created;?></span></li>
<?php endforeach;?>
<?php else:?>
<li class = 'list-group-item'>none</li>
<?php endif;?>
</ul>

<ul class='pull-right list-group col-xs-12 col-sm-5 no-padding'>
<li class = 'list-group-item'><h4>Top writers</h4></li>
<?php if($top_writers):?>
<?php foreach($top_writers as $writer):?>
<li class = 'list-group-item'><a href = <?="'/".$writer->url."'";?>><?=$writer->username;?></a> <span class = 'pull-right'><?=$writer->num_views;?> views</span></li>
<?php endforeach;?>
<?php else:?>
<li class = 'list-group-item'>none</li>
<?php endif;?>
</ul>
<div class = 'clearfix'></div>