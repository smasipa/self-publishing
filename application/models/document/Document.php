<?php
class Document extends MY_Model
{
	public $table = 'documents';
	
	public $id;
	
	public $user_id;
	
	public $document_name;
	
	public $document_type;
	
	public $location = DOCUMENTS_DIR;
	
	public $created;
	
	public $modified;
	
	public $item_id;
	
	public $item_type;
	
	public $num_downloads = 0;
	
	public function __construct($options = array())
	{
		$this->load->database();
		$this->_populate($options);
	}
	
	public function upload()
	{
		$this->load->library('Upload');
		$handle = new Upload($_FILES['document']);
		
		// Is file on server?
		if ($handle->uploaded){

			$docs_dir = $this->location;
			
			$file_name = $this->user_id . " ".preg_replace("#\s+#", " ",$this->document_name)." ". time();
			
			$handle->file_new_name_body = $file_name;
			$handle->allowed = array('application/pdf','application/msword', 'application/zip');
		
			$handle->file_max_size = 20*1024*1024; // 20mb
			$handle->Process($docs_dir);

			// we check if everything went OK
			if ($handle->processed){
				
				$uploaded_data = $this->upload->data();
				$file_data = array(
				'document_name' => $handle->file_dst_name,
				'document_type' => $handle->file_dst_name_ext,
				'item_id' => $this->item_id,
				'item_type' => $this->item_type,
				'hashed_name' => hash('ripemd128',$handle->file_dst_name),
				'num_downloads' => $this->num_downloads,
				'created' => time()
				);
				
				// Delete existing document in database
				$this->delete();
				
				$this->db->insert($this->table, $file_data);
				
				return $this->db->insert_id();
				
			}else {
				// one error occured
				return $handle->error;
			}
		}
	}
	
	// Increase number of downloads by 1
	public function incr_num_downloads()
	{
		if($this->id)
		{
			$this->num_downloads =  $this->num_downloads + 1;
			$this->db->set(array('num_downloads' => $this->num_downloads));
			$this->db->where(array('id' => $this->id, 'item_type' => $this->item_type, 'item_id' => $this->item_id));
			$this->db->update($this->table);
		}
	}
	
	public function get()
	{
		if($this->item_id)
		{
			return $this->get_first(array('item_id' => $this->item_id, 'item_type' => $this->item_type));
		}
	}
	
	public function get_document_name($hashed_name = null)
	{
		if($this->item_id)
		{
			$query = $this->db->select('document_name')->where(array('item_id' => $this->item_id, 
			'item_type' => $this->item_type))->get($this->table);
		}
		
		$row = $query->row();
		return $row ? array('actual_file' => $this->location. $row->document_name, 
		'file' => App\Utility\StringMethods::unslug($row->document_name)) 
		: null;
	}
	
	// public function get_num_downloads()
	// {
		// if($this->item_id)
		// {
			// return $this->count(array('item_type' => $this->item_type, 'item_id' => $this->item_id));
		// }
	// }
	
	public function get_file()
	{
		return $this->document_name ? $this->location. $this->document_name : null; 
	}
	
	public function delete()
	{
		if($this->item_id && $file = $this->get_document_name())
		{
			$actual_file = $file['actual_file'];
			
			// Are you sure you want to delete? Dialog
			if(file_exists($actual_file))
			{
				// Move to deleted files directory
				$doc = $this->get();
				$deleted_file = $this->location . DS . 'deleted' . DS . $doc->document_name;
				
				rename($actual_file, $deleted_file);
				// if(unlink($actual_file))
				return $this->db->where(array('item_id' => $this->item_id, 'item_type' => $this->item_type))->delete($this->table);
			}
		}
		return;
	}
}
?>