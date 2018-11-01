<h3><?=$publication_title;?></h3>					
<hr>					
<div class = 'col-sm-7 col-xs-12'>		
	<?=form_open_multipart($form_url);?>	
		<?php if($folders):?>
		<label>Add to folder</label>
		<br/>
		<select class = 'form-control' name = 'main_folder'>
			<option value = '0'>Choose Folder</option>
			<?php foreach($folders as $folder):?>
			<option value = <?=$folder['id'];?>><?=$folder['name'];?></option>
			<?php endforeach;?>
		</select>
		<br/>
		<?php endif;?>	
		<input class = 'form-control btn-default' type = 'submit' name = 'add' value = 'add'/> 
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