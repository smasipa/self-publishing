<h3>Your file was successfully uploaded</h3>
<ul>		
	<?php foreach($upload_data as $item => $value): ?>		
	<li><?php print $item; ?>: <?php print $value; ?></li>		
	<?php endforeach; ?>		
</ul>		
<p><?php print anchor('upload','Upload another file'); ?></p>		
		
