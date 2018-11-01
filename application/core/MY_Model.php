<?php
class MY_Model extends CI_Model{
	
	public $url;
	
	protected $table;
	
	protected $update_data;
	
	public function _populate($options = array())
	{
		if(sizeof($options))
		{
			foreach($options as $key => $value)
			{
				if(property_exists($this, $key))
				{
					$this->$key = $value;
				}
			}
		}
	}
	
	public function update($data = array())
	{
		if($data)
		{
			$data['modified'] = time();
			$this->db->set($data);
			$this->db->where(array('id' => $this->id));
			$this->_populate($data);
			return $this->db->update($this->table);
		}
	}
	
	// Money methods
	public function float_to_int($value)
	{
		return $value * 100;
	}
	
	public function int_to_float($value)
	{
		return $value / 100;
	}
	
	public function _init_update_data($options = array(), $omits = array())
	{
		if(sizeof($options))
		{
			foreach($options as $key => $value)
			{
				if(!in_array($key, $omits) && property_exists($this, $key))
				{
					$this->update_data[$key] = $value;
				}
			}
		}
	}
	
	public function get_first($where, $fields = null, $order_by = null)
	{
		
		
		$query = $this->db;
		if($fields)
		{
			$query->select($fields);
		}
		
		$query = $query->where($where)->limit(1)->order_by($order_by)->get($this->table);
		
		$row = $query->result('array');
		
		// late static binding
		$child = get_class($this);
		
		if(isset($row[0]))
		$obj = new $child($row[0]);
	
		return isset($row[0]) ? $obj : null;
	}	
	
	// Can be overloaded for more specific deletes
	public function delete()
	{
		if($this->id)
		{
			$removed = $this->db->where(array('id' => $this->id))->limit(1)->delete($this->table);
			if($removed)
			{
				print "Item succesfuly removed";
			}
			return $removed;
		}
		return;
	}
	
	public function get_all($where, $fields = null, $limit = null, $order_by = null)
	{
		if($fields)
		{
			$this->db->select($fields);
		}
		
		$this->db->where($where);
		
		if($limit)
		{
			$this->db->limit($limit[0], $limit[1]);
		}		
		
		if($order_by)
		{
			$this->db->order_by($order_by);
		}
		
		$query = $this->db->get($this->table);
		
		if($results = $query->result())
		{
			$objects = array();
			
			// late static binding
			$child = get_class($this);
			foreach($results as $row)
			{
				$obj = new $child($row);
				
				array_push($objects, $obj);
			}
			
			return $objects;
		}
		return;
	}
	
	public function count($where = array())
	{
		if(empty($where))
			$where = array('id' => $this->id);
		
		$this->db->where($where);
		
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}
	
	public function get_calling_class()
	{
		$trace = debug_backtrace();
		
		//Get the class that is asking for who awoke it
		$class = $trace[1]['class'];
		
		// +1 to i since we have to account for this function
		$length = count($trace);
		for($i = 1; $i < $length; $i++)
		{
			if(isset($trace[$i]))
			{
				// Is it a different class?
				if($class != $trace[$i]['class'])
					return strtolower($trace[$i]['class']);
			}
		}
	}
	
	// Returns array of key value pairs that are not empty
	public function filled_fields(array $values)
	{
		$ret = array();
		if(is_array($values))
		{
			foreach($values as $key => $value)
			{
				if(!empty($value) || $value == 0)
					$ret[$key] = $value;
			}
		}
		return $ret;
	}
	
	public function only_update($data = array(), $fields)
	{
		$arr_fields = explode(',', $fields);
		
		$to_update = array();
		
		if($arr_fields)
		{
			foreach($arr_fields as $field)
			{
				$field = strtolower($field);
				
				$to_update[$field] = $to_update[$field];
			}
		}
		
		return $to_update;
	}
	
	public function convert_to_objects($items, $limit = null, $order_by = null)
	{
		if(is_array($items))
		{
			$class_groups = array();
			foreach($items as $item)
			{
				$type = $item['item_type'];
				$item_id = $item['item_id'];
				
				// Sort into same table name
				$class_groups[$type][] = $item_id;
			}
			
			$class_models = array_keys($class_groups);
		
			$result_items = array();
			
			if(is_array($class_models))
			{
				// Search for the found items' names in different database tables
				foreach($class_models as $class_model)
				{
					$class = ucfirst($class_model);
					$this->load->model($class);
					$obj = new $class();
					
					if($class == 'Publication')
					{
						$this->db->select('id, title, created, folder_id');
					}
					else
					{
						$this->db->select('id, title, created');
					}
					
					$this->db->distinct();
					
					// Get existing items for this particular class
					$this->db->where_in('id', $class_groups[$type]);
					
					if($order_by)
					{
						$this->db->order_by($order_by);
					}
					
					$result_items[$class] = $this->db->get($obj->table)->result($class);
				}
			}
			return $result_items;
		}
	}
	
	function results_to_object($results)
	{
		if(sizeof($results))
		{
			$cached_items = array();
			$cached_models = array();
			
			foreach($results as $item)
			{
				$class = $item->item_type;
				if(!array_key_exists($class, $cached_models))
				{
					$cached_models[$item->item_type] = TRUE;
					$this->load->model($class);
				}
				
				$item_cache_key = "{$class}_{$item->item_id}";
				
				if(!array_key_exists($item_cache_key, $cached_items))
				{
					$item_obj = new $class(array('id' => $item->item_id));
					$item_name = $item_obj->get_db_name();
					
					$cached_items[$item_cache_key] = $item_name;
				}
				
				$item->item_name = $cached_items[$item_cache_key];
				
				$item->created = date('j/m/y', $item->created)." ".date('g:ia', $item->created);
				if(isset($item->modified))
				{
					$item->modified = date('j/m/y', $item->modified)." ".date('g:ia', $item->modified);
				}
				
			}
			
			return $results;
		}
	}
	
	function get_db()
	{
		$db = (array)get_instance()->db;
		return mysqli_connect('localhost', $db['username'], $db['password'], $db['database']);
	}
	
	function sanitize_string($value){
		
		$value = trim($value);
		if(get_magic_quotes_gpc()) $value = stripcslashes($value);
		
		// $value = strip_tags($value);
		
		// Convert both single and double qoutes
		$value = htmlentities($value,  ENT_QUOTES);
		// $value = htmlspecialchars($value);
		$value = mysqli_real_escape_string($this->get_db(), $value);
		return App\Helper\String::fix_string($value);
	}
	
	function auto_load_model($class)
	{
		$models = array(
		'User' => 'User',
		'Publication' => 'Publication',
		'Book' => 'Book'
		);
	}
	
	function get_table()
	{
		return $this->table;
	}
}
?>
