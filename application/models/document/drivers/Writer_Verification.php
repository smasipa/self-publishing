<?php
include_once(APPPATH.'models'.DS.'document'.DS.'Document_Uploader.php');

class Writer_Verification extends Document_Uploader{
	
	protected $upload_dir = WRITERS_DOCS_DIR;
	
	/* @document Document */
	public $document;
	
	public function __construct(User $user = null)
	{
		parent::__construct($user);
	}
	
	function upload()
	{
		return $this->document->upload();
	}
	
	function get_document()
	{
		// var_dump($this->document);
		return $this->document->get();
	}
	
	function delete_document()
	{
		return $this->document->delete();
	}
}
?>