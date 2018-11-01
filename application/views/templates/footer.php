		</div>
		<footer class = 'footer'>
			<div class = 'container'>
				&copy 2017 Gamalami Technologies Inc
				| <a href= '/'>home</a> 
				| <a href= '/contact'>contact</a> 
				| <a href= '/settings'>settings</a> 
				| <a href= '/about'>about</a> 
				| <a href= '/terms'>T's & C's</a> 
				<?php if(isset($is_logged_in) && $is_logged_in):?>
				| <a href= '/login'>logout</a>
				<?php endif;?>
			</div>
		</footer>
		<script src = <?='/assets/js/jquery.min.js';?>></script>
		<?php if(isset($facebook_js_btn)):?>
		<?php print $facebook_js_btn; endif;?>
		<script src = <?='/assets/js/bootstrap.min.js';?>></script>
	</body>
</html>