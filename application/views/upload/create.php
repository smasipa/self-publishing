<h3>Create Post</h3>					
<hr>					
<div class = 'col-sm-7 col-xs-12'>		
		<?=form_open_multipart('publications/create');?>
		<!--Image can be attached to posts that are not sub-posts of previous posts					
			Present a second form to upload image after uploading a new post.					
		-->					
		<label>Title:</label>
		<br/>					
		<input class = 'form-control' name = 'title' value = <?="'".$item_title."'";?>/>	
		<?=form_error('title');?>
		<br/>					
		<label>Is this a chapter of an existing post?:</label>				
		<br/>					
		<select class = 'form-control' name = 'is_chapter'>					
			<option >Please specify</option>					
			<option value = '1'>Yes</option>						
			<option value = '2'>No</option>						
		</select>	
		<?=form_error('is_chapter');?>
		<br/>	
		<br/>	
		
		<label>Price:</label>					
		<br/>					
		<input class = 'form-control' name ='price' value = <?="'".$item_price."'";?>/>	
		<?=form_error('price');?>
		<br/>
		
		<label>Who can access this post:</label>					
		<br/>					
		<select class = 'form-control' name = 'accessibility'>					
			<option value = '1'>Public</option>					
			<option value = '2'>Premium</option>					
		</select>					
		<br/>					
		<label>Attach Pdf/ epub:</label>					
		<br/>					
		<input  type = 'file' name = 'document'/>
		<?=form_error('document');?>					
		<br/>					
		<label>Write Post:</label>
		<?=form_error('text');?>
		<textarea class = 'form-control' rows = '12' name = 'text' ></textarea>
		<br/>					
		<input class = 'form-control  btn-default' type = 'submit' name = 'save' value = 'Submit'/>					
	</form>					
</div>
<!--					
<div class = 'col-sm-3 col-xs-12 container-fluid pull-right related-content'>					
	<div class = 'container-fluid'>Posts Related To This					
		<ul class = 'list-group more-from-list'>					
			<li><a href = 'home'>Season 1</a></li>					
			<li><a href = 'home'>Season 2</a></li>					
			<li><a href = 'home'>Season 3</a></li>					
			<li><a href = 'home'>Season 4</a></li>					
		</ul>					
	</div>									
						
	<div class = 'container-fluid'>More From This Author					
		<ul class = 'list-group more-from-list'>					
			<li><a href = 'home'>Guns & Roses</a></li>					
			<li><a href = 'home'>Minagerie</a></li>					
			<li><a href = 'home'>Manipulate the user database</a></li>					
			<li><a href = 'home'>Specific to selecting rows</a></li>					
		</ul>					
	</div>							
	<div class = 'clearfix'></div>					
</div>							
<div class = 'clearfix'></div>	-->					