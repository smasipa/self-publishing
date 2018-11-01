<h3>Insufficient Funds in geeWallet</h3>
<hr />
<div class="col-sm-7 col-xs-12">
		<h3>Could not process transaction due to insufficient funds</h3>
		<br/>	
	<?php if($purchase_url):?>

		<a class = 'btn btn-primary' href = "/<?=$purchase_url;?>">Back to purchase</a>
		<br/>
	<?php else:?>
	<a href = '/account/geeWallet'>Go to wallet</a>
	<?php endif;?>
	
</div>
<div class = 'clearfix'></div>