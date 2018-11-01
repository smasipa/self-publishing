<html lang = 'en'>
	<head>
		<meta charset="utf-8">
		
		<base href = <?=base_url();?> />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<?php if(isset($meta_info)):?>
		<meta name="description" content = <?="'{$meta_info['description']}'";?>>
		<meta name="author" content = <?="'{$meta_info['author']}'";?>>
		<?php endif;?>
		
		
		<link rel = 'stylesheet' href = <?='/assets/css/bootstrap.min.css';?>>
		<link rel = 'stylesheet' href = <?='/assets/css/main.css';?>>
		<link rel="icon" href="<?php echo base_url(); ?>/favicon.ico" type="image/jpg">
		<link rel= "stylesheet" href ="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" >
		<title><?=$title;?></title>
	</head>
	<body>
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
	
	  ga('create', 'UA-92007258-1', 'auto');
	  ga('send', 'pageview');
	
	</script>
	<nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/"><span>Gamalami</span></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
			
          <ul class="nav navbar-nav">
            
				<li class="dropdown">      
				  <a href="#" class="dropdown-toggle" 
				  data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Upload<span class="caret"></span></a>      
				  <ul class="dropdown-menu">      
					<li><a href="publications/create">Article</a></li>     
					<li><a href="books/upload">Book</a></li>     
				  </ul>      
				</li>  
          </ul>
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">      
				  <a href="#" class="dropdown-toggle" 
				  data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Discover <span class="caret"></span></a>      
				  <ul class="dropdown-menu">      
					<li><a href="/recent">Recently Viewed</a></li>          
					<li><a href="/favourites">Favourites</a></li>          
					<li><a href="/publications">Publications</a></li>  	
					<li><a href="/books">Books</a></li>      
					<li><a href="/authors">Authors</a></li>    
				  </ul>      
				</li>  
		  <?php if(isset($is_logged_in) && $is_logged_in):?>
            <li><a href="account"> Account</a></li>
         
		  <?php else:?>
				<li><a href="/register">Sign Up</a></li>
				<li><a href="/login"> Login</a></li>
		  <?php endif;?>
		   </ul>
		<form class = 'navbar-form navbar-left' method = 'GET' action = '/search'>
				<input id = 'search-box' class = 'form-control pull-right' placeholder = 'search website' name = 'q'/>
				<input class = 'pull-left btn btn-warning btn-search' type = 'submit' value = 'Go'/>
				<div class = 'clearfix'></div>
		</form>
        </div>
      </div>
    </nav><!--/.nav-collapse-->
			<div class = 'main-container'>