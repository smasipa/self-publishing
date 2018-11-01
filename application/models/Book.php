<?php

include_once(INTERFACES_DIR.'Item.php');

class Book extends MY_Model implements Item{
	
	public $table = 'books';
	
	public $id;
	
	public $isbn;
	
	public $user_id;
	
	public $title;
	
	public $description;
	
	public $price = 0;
	
	public $num_views = 0;
	
	public $allow_user_comments = FALSE;
	
	public $num_words = 0;
	
	public $num_downloads = 0;
	
	/* @document Document */
	public $document;	
	
	/* @document Document_Uploader */
	public $document_uploader;
	
	/* @cover_image Image */
	public $cover_image;
	
	/* @comments Comment */
	public $comments;
	
	public $created;
	
	public $modified;
	
	public $is_banned;
	
	public function __construct($options = array())
	{
		$this->load->database();
		$this->_populate($options);
	}
	
	public function save()
	{
		$data = array(
		'user_id' => $this->user_id,
		'isbn' => $this->isbn,
		'title' => $this->title,
		'description' => $this->sanitize_string($this->description),
		'price' => App\Helper\String::price_to_int($this->price) , // Convert to integer
		'num_views' => $this->num_views,
		'allow_user_comments' => $this->allow_user_comments,
		'num_words' => $this->num_words,
		'num_downloads' => $this->num_downloads,
		'modified' => time()
		);
		
		// if($this->id)
		// {
			// $data = $this->filled_fields($data);
			// $this->db->set($data);
			// $this->db->where(array('id' => $this->id, 'user_id' => $this->user_id));
			// return $this->db->update($this->table);
		// }
		
		// Else create new
		$data['created'] = time();
		$this->db->insert($this->table, $data);
		$this->id = $this->db->insert_id();
		return $this->id;
	}
	
	public function update($data = array())
	{
		if($data)
		{
			if(isset($data['description']))
			{
				$data['description'] = $this->sanitize_string($data['description']);
			}
			
			if(isset($data['price']))
			{
				$data['price'] = App\Helper\String::price_to_int($data['price']);
			}
			
			$data['modified'] = time();
			$this->db->set($data);
			$this->db->where(array('id' => $this->id));
			$this->_populate($data);
			return $this->db->update($this->table);
		}
	}
	
	public function ban()
	{
		return $this->update(array('is_banned' => TRUE));
	}
	
	public function unban()
	{
		return $this->update(array('is_banned' => FALSE));
	}
	
	public function get_name()
	{
		return $this->title;
	}
	
	public function init_document()
	{
		$this->load->model('document/drivers/Book_Uploader');
		$this->document_uploader = new Book_Uploader($this);
		
		// Get the document if it exists
		$this->document = $this->get_document();
	}
	
	public function save_document()
	{
		if($this->document_uploader)
		{
			return $this->document_uploader->upload();
		}
	}
	
	public function get_document()
	{
		if($this->document_uploader)
		{
			return $this->document_uploader->get_document();
		}
	}
	
	// Tags
	
	public function initialize_tags()
	{
		$this->load->model('Tag');
		$this->tags = new Tag(array('item_id' => $this->id));
	}
	
	/* @tag_names String */
	public function add_tags($tag_names = null)
	{
		if($this->tags)
		{
			$this->tags->add_tags($tag_names);
		}
	}
	
	public function get_tags()
	{
		if($this->tags)
		{
			return $this->tags->get_tags();
		}
	}
	
	public function get_author_name()
	{
		$this->load->model('User');
		$user = new User();
		$user = $user->get_first(array('id' => $this->user_id));
		return $user ? $user->username : 'Admin';
	}
	
	public function get_author_names()
	{
	    $this->load->model('User');
		$user = new User();
		$user = $user->get_first(array('id' => $this->user_id));
		return array('username' => $user->username, 'full_names' => $user->first_name." ".$user->last_name);
	}
	
	public function get_db_name()
	{
		if($this->id)
		{
			$book = $this->get_first(array('id' => $this->id), 'title');
			
			return $book ? $book->title : null;
		}
	}
	
	public function get_url()
	{
		return 'book/'.App\Utility\StringMethods::make_slug($this->title)."/".$this->id;
	}
	
	public function init_cover_image()
	{
		$this->load->model('Image');
		
		$this->cover_image = new Image(array('item_id'=> $this->id, 'user_id'=> $this->id));
		
		if($this->id)
		{
			$old_image = $this->cover_image->get_first(array('item_id' => $this->id, 'item_type' => 'book'));
			
			if($old_image)
			{
				$this->cover_image = $old_image;
				$this->cover_image->user_id = $this->id;
			}
		}
	}
	
	public function upload_cover_image()
	{
		$this->init_cover_image();
		
		if($this->cover_image && $this->cover_image->save())
		{
			$this->delete_cover_image();
			return TRUE;
		}
	}	
	
	public function get_cover_image($size = 'small')
	{
		$this->init_cover_image();
		return $this->cover_image->get_image_link($size);
	}	
	
	public function delete_cover_image()
	{
		if($this->cover_image);
			return $this->cover_image->delete();
	}
	
	// public function get_num_downloads()
	// {
		// if($this->id)
		// {
			// $this->load->model('Document');
			// $document = new Document(array('item_id' => $this->id));
			// return $document->get_num_downloads();
		// }
	// }

	
	public function get_price()
	{
		return App\Helper\String::price_to_float($this->price);
	}	
	
	public function getIntPrice()
	{
		return $this->price;
	}
	
	public function get_download_link()
	{
		return 'download/'.$this->get_url();
	}
	
	public function init_comments()
	{
		
	}
	
	public function add_comment()
	{
		
	}
	
	public function remove_comment($id)
	{
	}
	
	public function remove_all_comments()
	{
		
	}
}
?>