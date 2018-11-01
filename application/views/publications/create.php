<h3>Create Publication</h3>					
<hr>			
<div class = 'col-sm-7 col-xs-12'>		
		<?=form_open_multipart('publications/create');?>
		<label>Title:</label>
		<br/>					
		<input class = 'form-control' name = 'title' value = <?="'".$posted['title']."'";?>/>	
		<?=form_error('title');?>
		<br/>					
		<label>Is this a chapter of an existing folder?</label>				
		<br/>					
		<select class = 'form-control' name = 'is_chapter'>					
			<option >Please specify</option>					
			<option value = '1'>Yes</option>						
			<option value = '2'>No</option>						
		</select>	
		<?=form_error('is_chapter');?>
		<br/>	
		<br/>		
		<label>Tags:</label><span> Seperated with a comma(,) e.g fiction,non-fiction,science</span>					
		<br/>		
		<input class = 'form-control' name ='tags' value = <?="'".$posted['tags']."'";?>/>
		<?=form_error('tags');?>
		<br/>
		
		<?php if($is_approved_writer):?>
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
		<?php endif;?>
		
		<label>Write Post:</label>
		<?=form_error('text');?>
		<textarea class = 'form-control' rows = '12' name = 'text' ><?=$posted['text'];?></textarea>
		<br/>					
		<input class = 'form-control  btn-default' type = 'submit' name = 'save' value = 'Save'/>					
	</form>	
	<div>Got a book you want to publish? <a href = '/books/upload'>click here</a></div>
</div>	

<?php if(!$is_approved_writer):?>
<div class = 'col-sm-4 col-xs-12 container-fluid pull-right'>					
	<div class = 'container-fluid'><h3>Get Verified</h3>				
		<ol class = 'list-group more-from-list'>					
			As a verified writer you can:			
			<br/>
			<li>1) Publish premium items and books</li>							
			<li>2) Attach pdf/epub documents and zip files</li>							
			<li>3) Make money from every premium user that buys or reads your publications</li>	
			<a href = '/get_verified/info'>Get started >></a>
		</ol>					
	</div>									
	<div class = 'clearfix'></div>					
</div>	
<?php endif;?>
<div class = 'clearfix'></div>				