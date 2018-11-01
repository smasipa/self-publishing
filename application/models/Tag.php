<?php
class Tag extends MY_Model{
	
	public $table = 'tags';
	
	public $id;
	
	public $item_type;
	
	public $item_id;
	
	public $name;
	
	public $created;
	
	public $items;
	
	public function __construct($options = array())
	{
		$this->load->database();
		$this->_populate($options);
		$this->item_type = $this->get_calling_class();
		$this->load->model('Tag_Item');
		
		$this->items = new Tag_Item(array('item_id' => $this->item_id, 'item_type' => $this->item_type));
	}
	
	public function save()
	{
		$this->get_first(array('name' => $this->name));
		
		$data = array(
		'name' => $this->name,
		'created' => time()
		);
		
		return $this->db->insert($this->table, $data);
	}
	
	public function add_tags($arg_tags)
	{
		// $tas = explode(',', preg_replace("#\s+#", '',$tags));
		//Tags format e.g science,non-fiction,...
		preg_match_all("#[\w-]{2,}(?=, || ?=)#", strtolower($arg_tags), $tag_names);
		
		// Remove tags for item calling method
		if(is_array(App\Helper\Array_Methods::flatten($tag_names)))
		{
			$this->remove_tags();
		}
		
		if(isset($tag_names[0]) && !empty($tag_names[0]))
		{
			$tag_names = $tag_names[0];
			
			$this->db->where_in('name',$tag_names);
			$result = $this->db->get($this->table)->result('array');

			$matches = array();
			$non_matches = array();
			
			// Check if tag names already exist
			if($result)
			{
				$result_tag_names = array();
				foreach($result as $array)
				{
					$result_tag_names[] =  $array['name'];
				}
				
				// Sort tags into matches and non-matches
				foreach($result as $array)
				{
					foreach($tag_names as $tag)
					{
						if(in_array($tag, $result_tag_names))
							$matches[$array['id']] = $array;
						else
							$non_matches[$tag] = $tag;
					}
				}
			}
			
			
			$tags = array();
			
			// If there were any matches, take the non-matches, else take all non-matches
			$non_matches = $result ? $non_matches : $tag_names;
			
			foreach($non_matches as $tag)
			{
				if($tag)
				array_push($tags, array('name' => $tag, 'created' => time()));
			}
			
			$tag_item = new Tag_Item();
			$tags_insert = array();
			
			if($tags)
			{
				 // Insert tags that do not already exist && !$result
				if($non_matches)
				{
					$this->db->insert_batch($this->table, $tags);
				}
				
				$this->db->where_in('name',$tag_names);
				$new_tags = $this->db->get($this->table)->result('array');
				
				$this->load->model('Tag_Item');
				
				
				foreach($new_tags as $tag)
				{
					array_push($tags_insert, array('tag_id' => $tag['id'],
					'item_type' => $this->item_type,
					'item_id' => $this->item_id,
					'created' => time()));
				}
				
				//put these into item's tags list
				$tag_item->add_items($tags_insert);
			}
			elseif($matches)
			{
				foreach($result as $tag)
				{
					array_push($tags_insert, array('tag_id' => $tag['id'],
					'item_type' => $this->item_type,
					'item_id' => $this->item_id,
					'created' => time()));
				}
				$tag_item->add_items($tags_insert);
			}
		}
	}
	
	public function get_tags()
	{
		$this->load->model('Tag_Item');
		
		$tag_items = new Tag_Item();
		
		$tag_items = $tag_items->get_all(array('item_id' => $this->item_id, 'item_type' => $this->item_type), 'tag_id');
		
		if($tag_items)
		{
			$tag_ids = array();
			foreach($tag_items as $tag)
			{
				$tag_ids[] = $tag->tag_id;
			}
			
			$this->db->select('name');
			$this->db->distinct();
			
			$result = $this->db->where_in('id', $tag_ids);
			$result = $this->db->get($this->table)->result('array');
			return $result;
		}
	}
	
	public function get_items($search_query)
	{
		if(is_array($search_query))
		{
			$search_query = App\Helper\Array_Methods::flatten($search_query);
			$search_query = implode(' ', $search_query);
		}
		
		$this->db->select('id');
		$this->db->distinct();
		$this->db->like('name', $search_query, 'both');
		$ids = $this->db->get($this->table)->result('array');
		
		// Nothing found? Break down query into sections, and continue search!
		if(!$ids)
		{
			$fields = explode(' ', $search_query);
			
			$this->db->select('id');
			$this->db->distinct();
			foreach($fields as $field)
			$this->db->or_like('name', $field, 'both');
			$ids = $this->db->get($this->table)->result('array');
		}
		
		// Was anything found
		$items = $ids ? $this->items->get_items(App\Helper\Array_Methods::flatten($ids)) : null;
		
		if(is_array($items))
		{
			$class_groups = array();
			foreach($items as $item)
			{
				$type = $item['item_type'];
				$item_id = $item['item_id'];
				
				// Sort into same class type
				$class_groups[$type][] = $item_id;
			}
			
			$class_models = array_keys($class_groups);
		
			$result_items = array();
			
			if(is_array($class_models))
			{
				$classes_cache = array();
				
				// Search for the found items' names in different database tables
				foreach($class_models as $class_model)
				{
					$class = ucfirst($class_model);
					
					if(!array_key_exists($class, $classes_cache))
					{
						$classes_cache[$class] = 'loaded';
						$this->load->model($class);
					}
					
					$obj = new $class();
					
					if(strtolower($class) == 'publication')
					{
						$this->db->select('id, title, created, folder_id');
					}
					else
					{
						$this->db->select('id, title, created');
					}
					$this->db->distinct();
					
					// Get existing items for this particular class
					$this->db->where_in('id', $class_groups[$class_model]);
					$result_items[$class] = $this->db->get($obj->table)->result($class);
				}
			}
			return $result_items;
		}
	}
	
	public function remove_tags()
	{
		$this->items->remove_from_tags();
	}
}

?>