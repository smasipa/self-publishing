<?php

/* To be added later when more users start to use the website */
class Notification extends MY_Model{
	
	public $table = 'notifications';
	
	public $id;
	
	public $user_id;
	
	/* 0 = System is notifying, notifier_id > 0 means user is notifying */
	public $notifier_id;
	
	public $item_id;
	
	public $item_type;
	
	public $action_type;
	
	/* 1 = low, 2 = medium, 3 = high */
	public $priority = '1';
	
	/* @data string, the data that was submited with the notification */
	public $data;
	
	public $created;
	
	public function __construct($options = array())
	{
		$this->load->database();
		$this->_populate($options);
	}
	
	public function notify()
	{
		$data = array(
		'user_id' => $this->user_id,
		'action' => $this->action
		);
	}
	
	public function notify_by_email()
	{
		
	}
	
	public function get_message($action)
	{
		$messages = array(
		'new_purchases' => ' you have new purchase',
		'purchased' => ' has been purchased by ',
		'writer_approved' => ' was approved by ',
		'approval_status' => ' your request to become an author was ',
		'writer_published' => ' published a new item ',
		array('purchase' => array(
		'new' => 'user purchased item ',
		'blocked' => 'blocked user with id of',
		'paycheck' => ''
		)),		
		
		array('user' => array(
		'deleted' => 'deleted user with id of',
		'blocked' => 'blocked user with id of',
		'paycheck' => 'funds have been transfered to your bank account'
		)),		
		array('content' => array(
		'deleted' => ' deleted item ',
		'new' => 'check these new items',
		'new_by_writer' => ' published a new item',
		'copyright' => ' you have breached our terms and conditions'
		)),
		
		array('notices' => array(
		//Create a notices table
		'writer' => ' new notice from author ',
		'writers' => ' new notice from author ',
		'system' => ' new notice'
		'features' => ' new features have been added',
		))
		);
	}
	
	
	public function get_notifications()
	{
		
	}
	
	public function clear_notifications()
	{
		
	}
}

?>