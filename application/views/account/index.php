<?php if($user->first_name || $user->last_name):?>
<h2><?=$user->first_name . ' ' .$user->last_name;?></h2>
<?php else:?>
<h2>Account</h2>
<?php endif;?>
<hr>


<div class="pull-left col-sm-5 col-xs-12 user-stats panel">
	<div class="panel-heading">
		<!--<strong>Transactions</strong>-->
	</div>
	<div class="panel-body">
		<ul class="list-group more-from-list">
			<?php if($approved_writer):?><li><a href = "/account/stats">Statistics</a></li><br /><?php endif;?>
			<li><a href = <?=$user->url;?>>Profile</a></li>
			<br/>
			<li><a href = '/purchases'>Purchases</a></li>
			<br />
			<li><a href = '/account/geeWallet'>geeWallet</a></li>
			<br/>
			<li><a href = '/settings'>Settings</a></li>
			<br/><li><a href = '/help'>Help</a></li>
			<br/>
			<li><a href = '/login'>Logout</a></li>
		</ul>
	</div>
</div>

<div class = 'clearfix'></div>