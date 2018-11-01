<?php
class Membership extends MY_Model{
	
	public $table = 'subscriptions';
	
	public $user;
	
	public $expiration_dated;
	
	// public $subscription_type = array('monthly' => 60*60*24*30, 'semi_annually' => 60*60*24*30*6, 'annually' => 60*60*24*30*12);
	
	public $prices = array('monthly' => 55, 'semi_annually' => 570, 'annually' => 912);
	
	public function __construct($options = array())
	{
		$this->_populate($options);
	}
	
	public function expire_membership()
	{
		
	}

	public function new_membership()
	{
		$data = array(
		'user_id' => $this->user_id,
		'type' => get_class($this),
		'expiration_date' => $this->get_expiration_date(),
		'expired' => FALSE,
		'created' => time(),
		'modified' => time()
		);
	}
	
	public function renew_membership()
	{
		
	}
}
?>