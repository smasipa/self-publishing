<?php
class Downloader extends MY_Model{
	
	public $item;
	
	public $title;
	
	public function __construct($options = array())
	{
		$this->_populate($options);
	}
	
	public function download()
	{
		$this->load->helper('Download');
		
		$file = $this->item->get_file();
		
 		if($file)	
		{	
			if(file_exists($file))
			{
				$file = file_get_contents($file);
				
				// Increase number of downloads by 1
				$this->item->incr_num_downloads();
				
				force_download($this->item->clean_name, $file);
				return;
			}	
		}	
		
		// File does not exist
		App\Activity\Access::file_not_found();
	}
}
?>