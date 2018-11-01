<?php
include_once(APPPATH.'models'.DS.'document'.DS.'Document_Uploader.php');

class Publication_Document_Uploader extends Document_Uploader{
	
	protected $upload_dir = PUBLICATIONS_DIR;
	
	/* @document Document */
	public $document;
	
	public function __construct(Publication $publication = null)
	{
		parent::__construct($publication);
	}
	
	function upload()
	{
		return $this->document->upload();
	}
	
	function delete_document()
	{
		return $this->document->delete();
	}
}
?>