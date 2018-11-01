<h3>Edit Post</h3>					
<hr>					
<div class = 'col-sm-7 col-xs-12'>					
	<?=form_open_multipart($form_url);?>			
		<label>Title:</label>					
		<br/>					
		<input class = 'form-control' name ='title' value = <?="'".$publication->title."'";?>/>	
		<?=form_error('title');?>
		<?php if($is_approved_writer):?>
			<br/>							
			<label>People who can access this post:</label><span> <?=$publication->accessibility;?></span>					
			<br/>					
			<select class = 'form-control' name = 'accessibility'>					
				<option value = '0'>Choose Audience</option>					
				<option value = '1'>Public</option>					
				<option value = '2'>Premium</option>					
			</select>	
			<br/>
			<?php if(isset($document['name'])):?>
				<label>Current Document:</label> <a href = <?="/{$document['download']}";?>><?=$document['name'];?></a>					
				<br/>Downloads(<?=$document['num_downloads'];?>) | <a href =<?="'".$delete."/?doc=5'";?>>Delete</a>
				<br/>	
				<br/>
			<?php else:?>
				<br/>					
				<label>Attach pdf/zip:</label>					
				<br/>					
				<input  type = 'file' name = 'document'/>
				<?=form_error('document');?>
				<br/>
			<?php endif;?>	
		<?php endif;?>	
		<br/>		
		<label>Tags:</label><span> Seperated with a comma(,) e.g fiction,non-fiction,science</span>					
		<br/>		
		<input class = 'form-control' name ='tags' value = <?="'".$publication->tag_names."'";?>/>
		<?=form_error('tags');?>
		<br/>
		<label>Write New:</label>					
		<textarea class = 'form-control' name = 'text' rows = '12'><?=$publication->text;?></textarea>					
		<br/>					
		<input class = 'form-control  btn-default' type = 'submit' name = 'save' value = 'save'/>					
		<br/>					
		<a href =<?="'".$delete."'";?>>Delete </a> (Permanent removal!)					
		<br/>					
	</form>					
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