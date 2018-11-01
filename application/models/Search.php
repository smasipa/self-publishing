<?php
class Search extends MY_Model{
	public $search_query;
	
	protected $results = array();
	
	public $search_type;
	
	public function __construct($options = array())
	{
		$this->load->database();
		$this->_populate($options);
		
		$this->search_query = trim($this->search_query);
	}
	
	public function exploded_query()
	{
		$array = explode(' ', $this->search_query . ' ');
		return $this->filled_fields($array);
	}
	
	public function search()
	{
		if(is_string($this->search_query) && !empty($this->search_query))
		{
			
			if($this->search_type == 'tag')
			{
				$this->results = $this->search_tags();
			}
			else
			{
				$this->results['users'] = $this->search_users();
				$this->results['publications'] = $this->search_publications();
				$this->results['folders'] = $this->search_folders();
				$this->results['books'] = $this->search_books();
				$this->results['tags'] = null;
				
				if(empty($this->results['users']) && empty($this->results['publications']) 
					&& empty($this->results['books'])
					&& empty($this->results['folders'])
				)
				{
					$this->results = $this->search_tags();
				}
			}
			
			if(empty($this->results['users']) 
			&& empty($this->results['publications']) 
			&& empty($this->results['books']) 
			&& empty($this->results['folders']) 
			&& empty($this->results))
			{
				return null;
			}
			
			
			foreach($this->results as $type => $result_type)
			{
				$cover_images_cache = array();
				
				if(sizeof($result_type))
				{
					foreach($result_type as $item)
					{
						
						$item->name = $item->get_name();
						$item->url = $item->get_url();
						
						if($type == 'publications' || $type == 'Publication')
						{
							if($item->folder_id > 0)
							{
								$this->load->model('Folder');
								$folder = new Folder(array('id' => $item->folder_id));
								
								// Stop repeat database folder_image requests to save time
								if(!array_key_exists($item->folder_id, $cover_images_cache))
								{
									$cover_images_cache[$item->folder_id] = $folder->get_cover_image('small');
								}
								
								$item->cover_image = $cover_images_cache[$item->folder_id];
							}
						}
						else
						{
							$item->cover_image = $item->get_cover_image('small');
						}
					}
				}
			}
		}
	}
	
	
	public function search_users()
	{
		/* 	
		* Search users	
		*/	
			
		$this->load->model('User');	
			
		$user = new User();	
			
		$user->db->distinct();	
			
		$user->db->like(array('username' => $this->search_query));	
		$users = $user->db->get($user->table)->result('User');	
			
			
		if(empty($users))	
		{	
			$arr_query = $this->exploded_query();	
				
			if(sizeof($arr_query) <= 2)	
			{	
				$user->db->distinct();	
					
				foreach($arr_query as $field)	
				{	
					$user->db->or_like('first_name', $field, 'both');	
					$user->db->or_like('last_name', $field, 'both');	
				}	
					
				$users = $user->db->get($user->table)->result('User');	
			}	
		}	
		return $users;	
	}
	
	public function search_publications()
	{
		/* 	
		* Search publications	
		*/	
			
		$this->load->model('Publication');	
			
		$publication = new Publication();	
			
		$publication->db->distinct();	
			
		$publication->db->like(array('title' => $this->search_query));	
		$publications = $publication->db->get($publication->table)->result('Publication');	
				
		return $publications;
	}	
	
	public function search_books()
	{
		/* 	
		* Search publications	
		*/	
			
		$this->load->model('Book');	
			
		$publication = new Book();	
			
		$publication->db->distinct();	
			
		$publication->db->like(array('title' => $this->search_query));	
		
		$publications = $publication->db->get($publication->table)->result('Book');	
				
		return $publications;
	}	
	
	public function search_folders()
	{
		/* 	
		* Search publications	
		*/	
			
		$this->load->model('Folder');	
			
		$folder = new Folder();	
			
		$folder->db->distinct();	
			
		$folder->db->like(array('name' => $this->search_query));	
		$folders = $folder->db->get($folder->table)->result('Folder');
		return $folders;
	}
	
	public function search_tags()
	{
		$this->load->model('Tag');
		$tag = new Tag();
		return $tag->get_items($this->search_query);
	}
	
	public function get_results()
	{
		$this->search();
		return $this->results;
	}
	
}
?>