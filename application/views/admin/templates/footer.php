			</div><!--.main-->
      </div><!--Side-nav-->
</div><!--container-fluid-->


<footer class = 'footer'>
			<div class = 'container'>
				&copy 2017 
				| <a href= '/'>home</a> 
				| <a href= '/settings'>settings</a> 
				| <a href= '/about'>about</a> 
				| <a href= '/terms'>Ts & Cs</a> 
				<?php if(isset($is_logged_in) && $is_logged_in):?>
				| <a href= '/login'>logout</a>
				<?php endif;?>
			</div>
		</footer>
		<script src = <?='/assets/js/jquery.min.js';?>></script>
		<script src = <?='/assets/js/bootstrap.min.js';?>></script>
	</body>
</html>