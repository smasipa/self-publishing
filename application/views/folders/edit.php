<h3>Folder</h3>					
<hr>					
<div class = 'col-sm-7 col-xs-12'>		
	<?=form_open_multipart($form_url);?>	
		
		<label>Name</label>
		<br/>
		<input class = 'form-control' type = 'text' name = 'name' 
		<?php if(isset($folder->name)):?> value =  <?="'".$folder->name."'";?><?php endif;?> />
		<?=form_error('name');?>
		<br/>		
		
		
		<label>Description</label>
		<br/>
		<textarea class = 'form-control' name = 'description'><?=$folder->description;?></textarea>
		<br/>
		
		
		<label>Add cover image</label>					
		<br/>					
		<input  type = 'file' name = 'image'/>	
		<?php if(isset($publications) && $publications):?>
		<br/>
		<label>Remove publication</label>
		<br/>
		<select class = 'form-control' name = 'main_folder'>
			<option value = '0'>Choose Publication</option>
			<?php foreach($publications as $publication):?>
			<option value = <?=$publication->id;?>><?=$publication->title;?></option>
			<?php endforeach;?>
		</select>
		<br/>
		<?php endif;?>		
		<br/>			
		<input class = 'form-control  btn-default' type = 'submit' value = 'Submit' name = 'save'/>					
		<br/>					
		<a href ='/link'>Not now?</a> (You can do this later)					
		<br/>					
	</form>					
</div>					
<div class = 'col-sm-3 col-xs-12 container-fluid pull-right related-content'>					
	<div class = 'container-fluid'>Go premium:					
		<ul class = 'list-group more-from-list'>					
			<li>Earn money for your work!</li>					
			<li>Link your EasyReads Account					
			<br/> to your bank account</li>													
			<li>Sell ebooks of your work</li>					
			<li>Use our pdf creator</li>					
		</ul>					
	</div>					
	<div class = 'clearfix'></div>					
</div>							
<div class = 'clearfix'></div>						