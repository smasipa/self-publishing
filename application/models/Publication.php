<?php
include_once(INTERFACES_DIR.'Item.php');
class Publication extends MY_Model implements Item{
	
	public $table = 'publications';
	
	public $user_id;
	
	public $id;
	
	public $title;
	
	public $created;
	
	public $modified;
	
	public $accessibility = 'public';
	
	public $description;
	
	public $text;
	
	public $num_words;
	
	public $folder_id = 0;
	
	public $num_views = 0;
	
	/* @allow_user_comments Boolean */
	public $allow_user_comments;
	
	/* @document Document */
	public $document;
	
	/* @cover_image Image*/
	public $cover_image;
	
	public $document_location;
	
	public $document_id;
	
	private $hashed_document_name;
	
	public $load_object = FALSE;
	
	private $load_document = FALSE;
	
	/* @comments Comment */
	public $comments;
	
	/* @tags Tag */
	public $tags;
	
	/* @tag_names String */
	
	public $tag_names;
	
	public $is_banned;
	
	public function __construct($options = array()){
		$this->load->database();
		$this->_populate($options);
		
		if($this->load_object == TRUE)
		{
			$this->_populate($this->get_first(array(
			'id' => $options['id'], 
			'title' => $options['title'],
			'user_id' => $options['user_id'])));
		}		
		
		if($this->load_document == TRUE)
		{
			$this->_populate($this->get_first(array(
			'id' => $options['id'], 
			'title' => $options['title'],
			'user_id' => $options['user_id'])));
		}
	}
	
	public function save()
	{
		$data = array(
			'user_id' => $this->user_id,
			'accessibility' => $this->accessibility,
			'allow_user_comments' => TRUE, 
			'description' => $this->sanitize_string($this->description), 
			'folder_id' => $this->folder_id, 
			'text' => $this->sanitize_string($this->text), 
			'title' => $this->title,
			'num_views' => $this->num_views,
			'modified' => time()
		);
		
		if($data['text']) $data['num_words'] = $this->count_words();
		
		if($this->id)
		{
			// Only update field that are not empty
			if(!$data['title']) unset($data['title']);
			if(!$data['text']) unset($data['text']);
			
			
			$this->db->set($data);
			$this->db->where(array('id' => $this->id, 'user_id' => $this->user_id));
			$this->db->update($this->table);
			return TRUE;
		}
		
		$data['created'] = time();
		$this->db->insert($this->table, $data);
		$this->id = $this->db->insert_id();
		return $this->id;
	}
	
	public function update($data = array())
	{
		if($data)
		{
			if(isset($data['text']))
			{
				$data['text'] = $this->sanitize_string($data['text']);
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
	
	public function incr_num_views()
	{
		$num_views = $this->num_views + 1;
		$this->update(array('num_views' => $num_views));
	}
	
	public function get_name()
	{
		return $this->title;
	}
	
	public function get_url()
	{
		return App\Utility\StringMethods::make_slug($this->title)."/".$this->id;
	}
	
	public function get_download_link()
	{
		return 'download/p/'.$this->get_url();
	}
	
	public function get_edit_url()
	{
		return 'publications/edit/'.App\Utility\StringMethods::make_slug($this->title).'/'.$this->id;
	}
	
	public function count_words()
	{
		return preg_match_all("#[\w+]{2,}#", $this->text);
	}
	
	public function init_document()
	{
		$this->load->model('document/drivers/Publication_Document_Uploader');
		$this->document_uploader = new Publication_Document_Uploader($this);
		
		// Get the document if it exists
		$this->document = $this->get_document();
	}
	
	public function save_document()
	{
		if($this->document_uploader)
		{
			$this->document_uploader->upload();
		}
	}
	
	public function get_document()
	{
		if($this->document_uploader)
		return $this->document_uploader->get_document();
	}
	
	public function remove_document()
	{
		if($this->document_uploader)
		return $this->document_uploader->delete_document();
	}
	
	public function get_document_name($hashed_name = null)
	{
		if($this->document)
		return $this->document->get_document_name($hashed_name);
	}
	
	// Overrides delete in MY_Model
	public function delete()
	{
		// All files associated with this item must be removed
		if($this->id)
		{
			$this->load->model('Folder');
			$folder = new Folder();
			$folder->remove_old_item($this);
			
			if(parent::delete())
			{
				$this->init_document();
				$this->remove_document();
			}
		}
	}
	
	// Adding and removing user comments
	
	public function initialize_comments()
	{
		$this->load->model('Comment');
		$this->comments = new Comment(array('item_id' => $this->id));
	}
	
	public function add_comment($text, $logged_user_id)
	{
		if($this->comments && $this->allow_user_comments == 1)
		{
			$comment = new Comment(array('item_id' => $this->id, 'user_id' => $logged_user_id, 'text' => $text));
			return $comment->save();
		}
	}
	
	public function remove_comment($id = null)
	{
		$this->initialize_comments();
		if($this->comments->count(array('item_id' => $this->id, 'item_type' => get_class($this), 'id' => $id)))
		{
			$comment = new Comment(array('item_id' => $this->id, 'id' => $id));
			return $comment->delete();
		}
	}
	
	public function get_comments()
	{
		$comments = $this->comments->get_all(array('item_id' => $this->id), null, null, 'created DESC');
		
		if(is_array($comments))
		{
			$cache_user_names = array();
			$this->load->model('User');
			
			$this->load->helper('Time');
			
			foreach($comments as &$comment)
			{
				if(!array_key_exists($comment->user_id, $cache_user_names))
				{
					
					$user = new User();
					$user = $user->get_first(array('id' => $comment->user_id), 'username');
					
					
					$comment->author = $user ? $user->username : 'Anonymous';
					
					$cache_user_names[$comment->user_id] = $comment->author;
				}
				else
				{
					$comment->author = $cache_user_names[$comment->user_id];
				}
				
				$comment->created = App\Utility\Time_Methods::get_time_diff(array('par_date' => $comment->created));
			}
			
			return $comments;
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
	
	public function get_bread_crumb()
	{
		if($this->folder_id)
		{
			$this->load->model('Folder');
			$folder = new Folder();
			$folder = $folder->get_first(array('id' => $this->folder_id));
			
			if($folder && $folder->num_items > 1)
			{
				return "<a href="."'/".$folder->get_url()."'>".$folder->name."</a> > <a href='/".$this->get_url()."'>".$this->title."</a>";
			}
		}
		return null;
	}
	
	public function get_author_name()
	{
		$this->load->model('User');
		$user = new User();
		$user = $user->get_first(array('id' => $this->user_id));
		return $user ? $user->username : 'Admin';
	}
	
	public function get_db_name()
	{
		if($this->id)
		{
			$publication = $this->get_first(array('id' => $this->id), 'title');
			
			return $publication ? $publication->title : null;
		}
	}
}
?>