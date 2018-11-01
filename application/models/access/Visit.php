<?php
class Visit extends MY_Model{
	
	protected $table = 'visits';
	
	public $user_id;
	
	public $visitor_id;
	
	public $ip;
	
	public $item_type;
	
	public $item_id;
	
	public $created;
	
	public function __construct($options = array())
	{
		$this->load->database();
		$this->_populate($options);
	}	
	
	public function save()
	{
		$data = array(
		'user_id' => $this->user_id,
		'visitor_id' => $this->visitor_id,
		'item_type' => $this->item_type,
		'item_id' => $this->item_id,
		'ip' => $this->ip,
		'created' => time()
		);
		
		return $this->db->insert($this->table, $data);
	}
	
	public function record_visit($item)
	{
		if(is_object($item))
		{
			$this->item_type = get_class($item);
			$this->item_id = $item->id;
			$this->user_id = $item->user_id;
			if($this->user_id)
			{
				$this->load->model('User');
				$user = new User();
				$author = $user->get_first(array('id' => $this->user_id), 'id, num_views');
				if($author)
					$author->incr_num_views();
			}
			$this->save();
		}
	}
}

?>