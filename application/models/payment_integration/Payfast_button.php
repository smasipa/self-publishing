<?php 
class Payfast_button extends MY_Model
{
	public $merchant_id = MERCHANT_ID;
	
	public $merchant_key = MERCHANT_KEY;
	
	public $data = array();
	
	public $pre_extra_html;
	
	public $post_extra_html;
	
	public $pass_phrase = PAYFAST_PASS_PHRASE;
	
	public $is_sandbox = PAYFAST_SANDBOX_MODE;
	
	public $button = null;
	
	public $btn_properties = array('class' => 'btn btn-warning', 'value' => 'Pay now');
	
	public function __construct($options = array(), $data = array())
	{
		
		if($data)
		{
			$tmp = array('merchant_id' => $this->merchant_id, 'merchant_key' => $this->merchant_key);
			$this->data = array_merge($tmp, $data);
		}
		
		$this->_populate($options);
	}
	
	public function create_button($btn_properties = array(), $pre_html = null, $post_html = null)
	{
		// Replace default button html attributes
		if($btn_properties)
		{
			foreach($btn_properties as $key => $val)
			{$key = strtolower($key); if(array_key_exists($key, $this->btn_properties)) $this->btn_properties[$key] = $val;}
		}
		
		// Any html that must be included inside of form
		if($pre_html)
		{
			$this->pre_extra_html = $pre_html;
		}		
		
		if($post_html)
		{
			$this->post_extra_html = $pre_html;
		}
		
		$pf_output = null;
		
		//Create GET string
		foreach($this->data as $key => $val)
		{
			if(!empty($val))
			{
				$pf_output .= $key . '=' . urlencode(trim($val)) . '&';
			}
		}
		
		// Remove last ampersand
		$get_string = substr($pf_output, 0, -1);
		
		if(!$this->is_sandbox && !is_null($this->pass_phrase))
		{
			$get_string .= '&passphase='. urlencode(trim($this->pass_phrase));
		}
		
		$this->data['signature'] = md5($get_string);
		
		// If in testing mode use the sandbox domain ? sandbox.payfast.co.za
		// Else www.payfast.co.za
		
		$pf_host = $this->is_sandbox ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';
		
		$html_form = array();
		
		$html_form []= '<form action="https://' . $pf_host.'/eng/process" method="post">'; 
		// $html_form []= "<form action='".BASE_URL."notify/purchase' method='post'>"; 
		
		foreach($this->data as $name => $value){ $html_form []= '<input name = "'. $name .'" type="hidden" value="'.$value.'"/>'; }
		
		$html_form []="{$this->pre_extra_html}<input class = '{$this->btn_properties['class']}' type='submit' value='{$this->btn_properties['value']}'/>{$this->post_extra_html}</form>";
		
		$this->button = implode('',$html_form);
	}
	
	public function get_button()
	{
		return $this->button;
	}
}
?>