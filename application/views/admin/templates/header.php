<html lang = 'en'>
	<head>
		<meta charset="utf-8">
		<base href = <?=base_url();?> />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content= "" >
		<meta name="author" content="">
		<link rel = 'stylesheet' href = <?='/assets/css/bootstrap.min.css';?>>
		<link rel = 'stylesheet' href = <?='/assets/css/dashboard.css';?>>
		<title><?=$title;?></title>
	</head>
	<body>
	<!--id = 'navbar-custom'-->
	<nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/" style = 'color:#FFF;'>Gamalami</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
			<ul class="nav navbar-nav navbar-right">
			<li><a href="/monitor/menu">Menu</a></li>
			
		  <?php if(isset($is_logged_in) && $is_logged_in):?>
            <li><a href="/account"> Account</a></li>
         
		  <?php else:?>
				<li><a href="/register">Sign Up</a></li>
				<li><a href="/login"> Login</a></li>
		  <?php endif;?>
		   </ul>
		<form class = 'navbar-form navbar-left' method = 'GET' action = '/monitor/search'>
				<input id = 'search-box' class = 'form-control pull-right' placeholder = 'search website' name = 'q'/>
				<input class = 'pull-left btn btn-info btn-search' type = 'submit' value = 'Go'/>
				<div class = 'clearfix'></div>
		</form>
        </div>
      </div>
    </nav><!--/.nav-collapse-->