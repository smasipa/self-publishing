<?php
class Sitemap
{
	public $name;
	
	public $item;
	
	public $item_type;
	
	public $file_default = 'sitemap.xml';
	
	public $file_xml_track = 'sitemap_track.xml';
	
	public $file_robots = 'robots.txt';
	
	public $prefix = 'sitemap_';
	
	public $last_loaded_xml;
	
	public $sitemap_track;
	
	public $url;
	
	// Takes in objects that implement the Item Interface
	public function __construct(Item $item = null)
	{
		$this->item = $item;
		
		if(!is_null($this->item) && is_object($this->item))
		{
			
			$this->last_loaded_xml = App\Helper\Array_Methods::to_object(array('size' => null, 'name' => null, 'xml_object' => null));
			
			$this->name = strtolower(get_class($this->item));
			
			$this->sitemap_track = simplexml_load_file(SITEMAP_XML_DIR.$this->file_xml_track);
			
			// $this->last_loaded_xml['name'] = $this->sitemap_track->{$this->name}->lastloaded;
			$this->last_loaded_xml->name = $this->sitemap_track->{$this->name}->lastloaded;
			
			
			if($this->last_loaded_xml->name && file_exists($this->last_loaded_xml->name))
			{
				
				$this->last_loaded_xml->size = filesize($this->last_loaded_xml->name);
				$this->last_loaded_xml->xml_object = simplexml_load_file($this->last_loaded_xml->name);
			}
			;
			
			if(!$this->update())
			{
				$this->create_new();
			}
		}
	}
	
	public function create_new()
	{
		if(($this->last_loaded_xml->size && ($this->last_loaded_xml->size > SITEMAP_SIZE_LIMIT)) || is_null($this->last_loaded_xml->size))
		{		
			$count = $this->sitemap_track->{$this->name}->count;
			
			//Make new file and index it in sitemap_track.xml		
			$count += 1;		
			$new_file = $this->prefix . $this->name.'_'.$count.".xml";		
					
			//Start on clean slate;		
			$urls = simplexml_load_file(SITEMAP_XML_DIR.$this->file_default);		
					
			$new_url = $urls->addChild('url');		
					
			// $newUrl->addChild('loc',urlencode($pageLink));		
			$new_url->addChild('loc', BASE_URL . $this->item->get_url());	
			
			/*
			if($this->item->cover_image){		
						
				//Hacked! but working, preferably use DOM API		
				$image = $new_url->addChild('hack:image:image','');		
						
				// $image->addChild('hack:image:loc',urlencode($imageLoc));		
				$image->addChild('hack:image:loc',$this->item->get_cover_image->location);		
				$image->addChild('hack:image:caption',htmlspecialchars($this->item->get_name()));		
			}*/		
					
			$urls->asXML($new_file);		
					
			$this->sitemap_track->{$this->name}->lastloaded = $new_file;		
			$this->sitemap_track->{$this->name}->count = $count;		
			$this->sitemap_track->asXML(SITEMAP_XML_DIR . $this->file_xml_track);		
					
			// print "created $newFile of size $filesize </br>";		
			//add sitemap file name to robots.txt		
			$robots = file_put_contents($this->file_robots, "\n".'Sitemap: '. $new_file, FILE_APPEND | LOCK_EX);		
		}		
	}
	
	public function update()
	{
		if($this->last_loaded_xml->size && $this->last_loaded_xml->size < SITEMAP_SIZE_LIMIT)
		{
			$urls_xml = $this->last_loaded_xml->xml_object;
			
			$new_url_xml = $urls_xml->addChild('url');	
			
			// $newUrl->addChild('loc',urlencode($pageLink));	
			$new_url_xml->addChild('loc', BASE_URL.$this->item->get_url());	
			
			/*if(isset($this->item->cover_image)){	

				$this->item =  $this->item->get_cover_image();
				//Hacked! but working, preferably use DOM API			
				$image = $new_url_xml->addChild('hack:image:image','');	
				
				// $image->addChild('hack:image:loc',urlencode($imageLoc));			
				$image->addChild('hack:image:loc', BASE_URL.$this->item->cover_image->location);
				
				$image->addChild('hack:image:caption', htmlspecialchars($this->item->get_name()));			
			}	*/
			
			// print "appended to $lastLoadedMap of size $filesize </br>";			
			$urls_xml->asXML($this->last_loaded_xml->name);	
			
			return TRUE;
		}		
	}
	
	public function delete()
	{
		
	}
}
?>