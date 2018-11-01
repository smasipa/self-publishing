<?php
class Membership extends MY_Model{
	
	public $table = 'subscriptions';
	
	public $user_id;
	
	public $type;
	
	public $expiration_date;
	
	public $expired;
	
	public $url = 'premium';
	
	protected $duration = '1 month';
	
	public $created;
	
	public $modified;
	
	public function __construct($options = array())
	{
		$this->_populate($options);
	}
	
	public function get_expiration_date()
	{
		return strtotime($this->duration);
	}
	
	public function save()
	{
		$data = array(
		'user_id' => $this->user_id,
		'type' => get_class($this),
		'expired' => FALSE,
		'expiration_date' => $this->get_expiration_date(),
		'created' => time(),
		'modified' => time()
		);
		
		// Delete old subscription
		$this->delete();
		
		return $this->db->insert($this->table, $data);
	}
	
	public function update_user()
	{
		// Apply changes to user;
		$this->load->model('User');
		$user = new User(array('id' => $this->user_id));
		
		$user->update(
		array('modified' => time(), 
		'expires' => $this->expiration_date,
		'account_type' => $this->user_account_type,
		'expired' => $this->expired));
	}
	
	public function update($data = array())
	{
		if($data)
		{
			$data['modified'] = time();
			$this->db->set($data);
			$this->db->where(array('user_id' => $this->user_id));
			$this->_populate($data);
			return $this->db->update($this->table);
		}
	}
	
	public function expire()
	{
		$data = array(
		'type' => get_class($this),
		'expired' => TRUE,
		'modified' => time()
		);
		
		return $this->update($data);
	}
	
	public function renew()
	{
		$data = array(
		'type' => get_class($this),
		'expired' => FALSE,
		'expiration_date' => $this->get_expiration_date(),
		'modified' => time()
		);
		
		return $this->update($data);
	}
	
	public function get_premium_members()
	{
		$this->db->select('users.username, users.email, users.created  as user_created');
		$this->db->select('subscriptions.*');
		$this->db->select('subscriptions.created  as joined');
		$this->db->from('users');
		
		$this->db->join($this->table, 'users.id = subscriptions.user_id');
		$this->db->where("users.account_type = 'premium'");
		$this->db->group_by('users.id');
		$results = $this->db->get()->result('Membership');
		
		if(is_array($results))
		{
			foreach($results as $member)
			{
				$member->joined  = date('j/m/Y', $member->joined)." at ".date('g:ia', $member->joined);
				$member->created = date('j/m/Y', $member->user_created)." at ".date('g:ia', $member->user_created);
			}
		}
		return $results;
	}
	
	public function get_subscribers($where, $fields = null, $limit = null, $order_by = null)
	{
		$subscribers = $this->get_all($where, $fields, $limit, $order_by);
		$subscriber_ids = array();
		$results = array();
		if($subscribers)
		{
			foreach($subscribers as $subscriber)
			{
				$subscriber_ids[$subscriber->user_id] =  $subscriber->type;
			}
			
			if($subscribers)
			{
				$this->load->model('User');
				$user = new User();
				$ids = array_keys($subscriber_ids);
				
				$user->db->select('id, username, email');
				$user->db->where_in('id', $ids);
				$users = $user->db->get($user->table)->result('User');
				
				if($users)
				{
					foreach($users as $user)
					{
						if(array_key_exists((int)$user->id, $subscriber_ids))
						{
							$user->subscription_type = $subscriber_ids[$user->id];
							$results[] = $user;
						}
					}
				}
			}
		}
	
		return $results;
	}
	
	public function get_price()
	{
		return $this->int_to_float($this->price);
	}
	
	public function get_duration()
	{
		return $this->duration;
	}
	
	public function delete()
	{
		if($this->user_id)
		{
			$this->db->where(array('user_id' => $this->user_id));
			return $this->db->delete($this->table);
		}
	}
}
?>