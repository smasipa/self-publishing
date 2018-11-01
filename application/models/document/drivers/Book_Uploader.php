<?php
include_once(APPPATH.'models'.DS.'document'.DS.'Document_Uploader.php');

class Book_Uploader extends Document_Uploader{
	
	protected $upload_dir = BOOKS_DIR;
	
	/* @document Document */
	public $document;
	
	public function __construct(Book $book = null)
	{
		parent::__construct($book);
	}
	
	function upload()
	{
		$this->document->upload();
	}
	
	function delete_document()
	{
		$this->document->delete();
	}
}
?>