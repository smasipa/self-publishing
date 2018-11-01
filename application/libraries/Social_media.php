<?php
class Social_media
{
	public $item_name;
	
	public $item_url;
	
	public $item_url_encoded;
	
	public $item_image;
	
	public $description;
	
	public $default_info = "Please check out ";
	
	public function __construct($item_name = null, $item_url = null, $item_image = null, $item_description = null)
	{
		$this->name = $item_name;
		$this->item_url = BASE_URL.$item_url;
		$this->item_image = BASE_URL.$item_image;
		$this->description = $item_description;
		
		$this->item_url_encoded = urlencode($this->default_info." ".$this->item_url);
	}
	
	public function get_facebook_btn()
	{
		
	
	$facebook =<<<_END
	<script type="text/javascript">
	
	$(document).ready(function(){
		$('#share_button').click(function(e){
			e.preventDefault();
			FB.ui(
			{
method: 'feed',
name: 'Gamalami | $this->name',
link: "$this->item_url" ,
picture: "{$this->item_image}",
caption: "{$this->item_name}" ,
description: "An online platform for writers to sell and showcase their work.",
message: ''
			});
		});
	});
	</script>
_END;
	return $facebook;
	}
	
	public function get_twitter_btn()
	{
		return "https://twitter.com/intent/tweet?text={$this->item_url_encoded}";
	}
	
	public function get_whatsapp_btn()
	{
		return "whatsapp://send?text={$this->item_url_encoded}";
	}
	
	public function get_gplus_btn()
	{
		return "http://plus.google.com/share?url={$this->item_url_encoded}";
	}
	
	public function get_all_btns()
	{
		return array(
		'whatsapp' => $this->get_whatsapp_btn(),
		'twitter' => $this->get_twitter_btn(),
		'gplus' => $this->get_gplus_btn(),
		);
	}

	/* ===================================================Social media============================================================ */
function init_fb_sharescript(){
	$appID = 362119674186532;
	$appURL = BASE_URL;
	$scriptJS =<<<_END
		<div id="fb-root"></div>
		<!-- USE 'Asynchronous Loading' version, for IE8 to work
		http://developers.facebook.com/docs/reference/javascript/FB.init/ -->
		<script>
		  window.fbAsyncInit = function(){
			FB.init({
				appId  : 362119674186532,
 status : true,  cookie : true,
 xfbml  : true });
		  };
		
		  (function() {
			var e = document.createElement('script');
			e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
			e.async = true;
			document.getElementById('fb-root').appendChild(e);
		  }());
		</script>
_END;
	return $scriptJS . $this->get_facebook_btn();
}

/* ===================================================/Social media============================================================ */
}
?>