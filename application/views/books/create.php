<h3>Upload Book</h3>					
<hr>					
<div class = 'col-sm-7 col-xs-12'>		
		<?=form_open_multipart('books/upload');?>
		<!--Image can be attached to posts that are not sub-posts of previous posts					
			Present a second form to upload image after uploading a new post.					
		-->					
		<label>Title:</label>
		<br/>					
		<input class = 'form-control' type = 'text' name = 'title' value = <?="'".$book['title']."'";?>/>	
		<?=form_error('title');?>
		<br/>					
		
		<label>Price:</label>					
		<br/>					
		<input class = 'form-control' type = 'text' name ='price' value = <?="'".$book['price']."'";?>/>	
		<?=form_error('price');?>
		<br/>		
		<label>Tags: </label><span> Separate tags with a comma(,) e.g science-fiction,non-fiction,educational</span>					
		<br/>					
		<input class = 'form-control' type = 'text' name ='tags' value = <?="'".$book['tags']."'";?>/>	
		<?=form_error('tags');?>
		<br/>				
		<label>Attach Pdf/ epub:</label>					
		<br/>					
		<input  type = 'file' name = 'document'/>
		<?=form_error('document');?>					
		<br/>		
			
		<label>Attach cover image</label>					
		<br/>					
		<input  type = 'file' name = 'image'/>
		<?=form_error('image');?>					
		<br/>					
		<label>Description:</label>
		<?=form_error('description')."<br/>";?>
		<textarea class = 'form-control' rows = '7' name = 'description' ></textarea>
		<br/>					
		<input class = 'form-control  btn-default' type = 'submit' name = 'save' value = 'Submit'/>					
	</form>					
</div>
<div class = 'clearfix'></div>