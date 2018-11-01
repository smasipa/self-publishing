<?php
/* 
* Tracks activity that has been taking place on the website.
* For instance when a admin or user performs an important action, it is stored in the database for future reference
*
*/
class Activity extends MY_Model{
	
	public $table  = 'activities';
	
	public $descriptions_table = 'activities_track';
	
	public $id;
	
	public $action_type;
	
	public $user;
	
	public $user_account_type;
	
	public $item;
	
	public $item_type;
	
	public $item_id;
	
	public $item_name;
	
	public $action_description;
	
	public $created;

	public function __construct($options = array())
	{
		$this->load->database();
		$this->_populate($options);
	}
	
	public function save()
	{
		$this->item_type = get_class($this->item);
		$data = array(
		'user_id' => $this->user->id,
		'item_type' => $this->item_type,
		'item_id' => $this->item->id,
		'action_type' => $this->action_type,
		'created' => time()
		);
		
		$this->db->insert($this->table, $data);
		
		$this->id = $this->db->insert_id();
		
		if($this->id && $this->action_description)
		{
			return $this->db->insert($this->descriptions_table, array('activity_id' => $this->id, 'description' => $this->action_description));
		}
		
		return $this->id;
	}
	
	public function writer_approved(User $writer)
	{
		$this->item = $writer;
		$this->action_type = 'writer_approved';
		// $this->action_description = "application approved";
		$this->save();
	}
	
	public function writer_rejected(User $writer)
	{
		$this->item = $writer;
		$this->action_type = 'writer_rejected';
		// $this->action_description = "application rejected";
		$this->save();
	}
	
	public function username_changed(User $writer, $old_name)
	{
		$this->item = $writer;
		$this->action_type = 'username_changed';
		$this->action_description = "{$old_name} changed to {$writer->username}";
		$this->save();
	}
	
	public function email_changed(User $writer, $old_email)
	{
		$this->item = $writer;
		$this->action_type = 'email_changed';
		$this->action_description = "{$old_email} changed to {$writer->email}";
		$this->save();
	}	
	
	public function forbidden_admin_access()
	{
		$this->item = $this->user;
		$this->action_type = 'forbidden_admin_access';
		$this->save();
	}	
	
	public function banned_item_access(Item $item)
	{
		$this->item = $item;
		$this->action_type = 'access_banned_item';
		$this->description = "{$item->get_name()}";
		$this->save();
	}
	
	public function item_deleted()
	{
		$this->action_type = 'item_deleted';
		$this->action_description = " '{$item->get_name()}' was deleted";
		$this->save();
	}	
	
	public function price_changed($old_price)
	{
		$this->action_type = 'price_changed';
		$this->action_description = "{$old_price} changed to {$this->item_price}";
		$this->save();
	}
	
	public function get_users_table()
	{
		$this->load->model('User');
		$user = new User();
		return $user->table;
	}
	
	public function get_admin_activity($where = array(), $limit = array(), $order_by = null)
	{
		$users_table = $this->get_users_table();
		$default_where = array("{$users_table}.account_type" => 'admin');
		
		$default_order_by = "created DESC";
		
		if($where)
		{
			$default_where = array_merge($default_where, $where);
		}		
		
		if($order_by)
		{
			$default_order_by = $default_order_by ." ".$order_by;
		}
		
		return $this->get_activity($users_table, $default_where, $limit, $default_order_by);
		
	}
	
	public function get_all_activity($where = array(), $limit = array(), $order_by = null)
	{
		$users_table = $this->get_users_table();
		$default_where = array("{$users_table}.id > " => 0);
		

		
		$default_order_by = "created DESC";
		
		if($where)
		{
			$default_where = array_merge($default_where, $where);
		}		
		
		if($order_by)
		{
			$default_order_by = $default_order_by ." ".$order_by;
		}
		
		return $this->get_activity($users_table, $default_where, $limit, $default_order_by);
	}
	
	public function get_activity($users_table ,$where = array(), $limit = array(), $order_by = null)
	{
		
		$this->db->select($this->table.'.*');
		$this->db->select("{$users_table}.email, {$users_table}.username");
		$this->db->select("{$this->descriptions_table}.description");
		$this->db->from($this->table);
		$this->db->join($users_table, "{$this->table}.user_id = {$users_table}.id", 'left');
		$this->db->join($this->descriptions_table, "{$this->table}.id = {$this->descriptions_table}.activity_id", 'left');
		$this->db->group_by("{$this->table}.id");
		$this->db->where($where);
		$this->db->order_by($order_by);
		
		$results = $this->db->get()->result();
		
		if(sizeof($results))
		{
			$cached_items = array();
			$cached_models = array();
			
			foreach($results as $activity)
			{
				$class = $activity->item_type;
				if(!array_key_exists($class, $cached_models))
				{
					$cached_models[$activity->item_type] = TRUE;
					$this->load->model($class);
				}
				
				$item_cache_key = "{$class}_{$activity->item_id}";
				
				if(!array_key_exists($item_cache_key, $cached_items))
				{
					$item_obj = new $class(array('id' => $activity->item_id));
					$item_name = $item_obj->get_db_name();
					
					$cached_items[$item_cache_key] = $item_name;
				}
				
				$activity->item_name = $cached_items[$item_cache_key];
				
				$activity->created = date('j/m/y', $activity->created)." ".date('g:ia', $activity->created);
				if(!is_null($activity->description))
				{
					
				}
			}
		}
		// var_dump($results);
		return $results;
	}
}

?>