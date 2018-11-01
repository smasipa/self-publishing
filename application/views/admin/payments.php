<h4>Payments summary</h4>
<div class="panel">
	<div class="panel-body">
		<div class="col-xs-6 col-sm-3">
			<h3 class ='stats-numbers'>R<?=$perfomance['total_paid'];?></h3>
			<span class="text-muted">Total paid</span>          
		</div>  	
		
		<div class="col-xs-6 col-sm-3">
			<h3 class ='stats-numbers'>R<?=$perfomance['total_due'];?></h3>
			<span class="text-muted">Total due</span>          
		</div>  	
		
		<div class="col-xs-6 col-sm-3">
			<h3 class ='stats-numbers'><?=$perfomance['total_writers_owed'];?></h3>
			<span class="text-muted">Total writers owed</span>          
		</div> 		
		
		<div class="col-xs-6 col-sm-3">
			<h3 class ='stats-numbers'><?=$perfomance['total_writers_paid'];?></h3>
			<span class="text-muted">Total writers paid</span>          
		</div>   
	</div>
</div>
<div class="pull-left panel col-xs-12  col-sm-6">
<div class = 'panel-heading'><h4>Owed writers</h4></div>
	<?php if($owed_writers):?>
	<div class="table-responsive">          
	  <table class="table table-striped">          
		<thead>          
		  <tr>          
			<th>#</th>          
			<th>Seller email</th>          
			<th>Username</th>          
			<th>Total</th>           
		  </tr>          
		</thead>          
		<tbody>  
			<?php foreach($owed_writers as $key => $writer):?>
		  <tr>          
			<td><?=$key + 1;?></td>          
			<td><?=$writer->email;?></td>          
			<td><a href=<?="'/monitor/writers/details/".$writer->username."'";?>><?=$writer->username;?></td>          
			<td>R<?=$writer->payment_amount;?></td>           
		  </tr>    
			<?php endforeach;?>
		</tbody>          
	  </table>          
	</div> 
	<?php else:?>
	<div class = 'panel-body'>None</div>
	<?php endif;?>
</div>

<div class="pull-right panel col-xs-12  col-sm-5">
<div class = 'panel-heading'><h4>Paid writers</h4></div>
	<?php if($paid_writers):?>
	<div class="table-responsive">          
	  <table class="table table-striped">          
		<thead>          
		  <tr>          
			<th>#</th>          
			<th>Seller email</th>          
			<th>Username</th>          
			<th>Total</th>           
		  </tr>          
		</thead>          
		<tbody>  
			<?php foreach($paid_writers as $key => $writer):?>
		  <tr>          
			<td><?=$key + 1;?></td>          
			<td><?=$writer->email;?></td>          
			<td><a href=<?="'/monitor/writers/details/".$writer->username."'";?>><?=$writer->username;?></td>          
			<td>R<?=$writer->payment_amount;?></td>           
		  </tr>    
			<?php endforeach;?>
		</tbody>          
	  </table>          
	</div> 
	<?php else:?>
	<div class = 'panel-body'>None</div>
	<?php endif;?>
</div>
<div class = 'clearfix'></div>