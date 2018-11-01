<?php
abstract class Document_Uploader extends MY_Model{
	
	protected $upload_dir = DOCUMENTS_DIR;
	
	protected $prefix = 'doc_';
	
	/* @document Document */
	protected $document;
	
	protected $item;
	
	public function __construct($item = null)
	{
		$this->load->database();
		$this->load->model('document/Document');
		
		if(is_object($item))
		{
			$this->item = $item;
			
			if(get_class($item) == 'User')
				$this->item->user_id = $item->id;
			
			$this->document = new Document(array('user_id' => $this->item->user_id, 
			'location' => $this->upload_dir, 
			'item_type' => get_class($item),
			'document_name' => $this->item->get_name(),
			'item_id' => $item->id));
		}
	}
	
	abstract function upload();
	
	// abstract function get_document();
	
	function get_document()
	{
		$document = null;
		$document = $this->document->get();
		if($document)
		{
			$document->clean_name = trim($this->document->document_name).'.'.$document->document_type;
			// $document->clean_name = trim($this->document->document_name).$document->document_type;
			$document->location = $this->document->location;
		}
		return $document;
	}
	
	abstract function delete_document();
}
?>