<?php

class Books extends MY_Controller{
	private $book;
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Book');
		$this->book = new Book();
	}
	
	public function view_all()
	{
	    
		$books = $this->book->get_all(array('created'), null, null, 'created DESC');
		
		if($books)
		{
			foreach($books as $book)
			{
				$book->name = $book->get_name();
				$book->url = $book->get_url();
				$book->cover_image = $book->get_cover_image();
			}
			
			$meta = array('description' => 'All books', 'author' => null);
			
			$this->load->view('templates/header', array('meta_info' => $meta, 'title' => 'Books', 'is_logged_in' => $this->is_logged_in));
			$this->load->view('books/index', array('author_name' => null, 'books' => $books, 'pagination' => null));
			$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
			return;
		}
		App\Activity\Access::nothing_found();
	}
	
	public function view($title_slug = null, $id = null)
	{
		$this->load->model('Purchase');
		
		$title = App\Utility\StringMethods::unslug($title_slug);
		
		$book = $this->book->get_first(array('title' => $title, 'id' => $id));
		
		if($book)
		{
			if($book->is_banned)
				App\Activity\Access::blocked_url();
			
			$book->name = $book->title;
		$book->author = $book->get_author_name();
		
		$book->author_names = $book->get_author_names();	
		
		$book->username = $book->author_names['username'];
		$book->author = $book->author_names['full_names'];
		
			//var_dump($book);
			//exit();
			$book->created = date('d M Y', $book->created);
			$book->cover_image = $book->get_cover_image();
			$book->initialize_tags();
			$book->tags = $book->get_tags();
			
			$purchase = new Purchase(array('user_id' => $this->logged_in_user->id, 'item' => $book));
			
			$book->is_purchased = null;
			
			$document = null;
			
			if($purchase->is_approved() 
				|| $this->logged_in_user->is_premium() 
				|| $book->price == 0
				)
			{
				$document = 'download/'.$book->get_url();
				$book->is_purchased = TRUE;
			}
			elseif($book->price)
			{
				// Only if a price was set
				$document = 'check_out/'.$book->get_url();
			}
			
			$edit = $book->user_id == $this->logged_in_user->id ? '/books/edit/'.$title_slug.'/'.$id : null;
			
			$meta = array('description' => $book->description, 'author' => $book->author);
			$book->description = nl2br($book->description);
			$book->price = $book->get_price();
			
			$this->load->library('Social_media');
			
			$data = array('book' => $book,
			'edit' => $edit,
			'document' => $document);
			
			$social_media =  new Social_media($book->title, $book->get_url(), $book->cover_image->location, 'Check out this book ');
			$data['social_media'] = $social_media->get_all_btns();
			
			
			$this->load->view('templates/header', array('meta_info' => $meta, 'title' => 'Book | '. $title, 'is_logged_in' => $this->is_logged_in));
			$this->load->view('books/info',$data
			);
			$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in, 'facebook_js_btn' => $social_media->init_fb_sharescript()));
		}
		else
		{
			App\Activity\Access::show_404();
		}
	}
	
	public function valid_title($value)
	{
		$this->form_validation->set_message('valid_title', 'Only letters, numbers, and spaces allowed. Please re-edit Title.');
		$eval = preg_match("#^[^\d][a-z0-9\s]+$#i", $value);
		return $eval ? TRUE : FALSE;
	}
	
	public function valid_price($value)
	{
		$this->form_validation->set_message('valid_price', 'Price must be a valid number, and not less than 15');
		
		// Replace currency
		$value = preg_replace("#[\R\s]#i", '', $value);
		
		return preg_match('/^[1-9][0-9\.]{0,5}$/', $value) && $value >= 15 ? TRUE : FALSE;
	}
	
	public function valid_document($value)
	{
		$this->form_validation->set_message('valid_document', 'Please upload a valid pdf document');
		return FALSE;
	}
	
	public function save()
	{
		if(!$this->is_logged_in)
			App\Activity\Access::login();
		
		// If is approved writer
		if($this->logged_in_user->approved_writer)
		{
			$this->load->library('form_validation');
			$data = array();
			$data['book'] = array('title' => $this->input->post('title'), 
			'price' => $this->input->post('price'),
			'tags' => $this->input->post('tags'),
			
			'description' => $this->input->post('description')
			);
			
			$this->form_validation->set_error_delimiters("<div class = 'form-errors alert-danger'>","</div>");
			$this->form_validation->set_rules('title', 'Title', 'callback_valid_title|required');
			$this->form_validation->set_rules('price', 'Price', 'callback_valid_price|numeric');
			$this->form_validation->set_rules('description', 'Description', 'required');
			
			
			if(empty($_FILES['document']['size']))
			{
				$this->form_validation->set_rules('document', 'Document', 'callback_valid_document');
			}
			
			if($this->input->post('save') && $this->form_validation->run())
			{
				
				$data = array(
				'user_id' => $this->logged_in_user->id,
				'title' => $this->input->post('title'),
				'price' => trim($this->input->post('price')),
				'description' => $this->input->post('description')
				);
				
				$book = new Book($data);
				
				if($book->save())
				{
					// Add link to books sitemap file
					$this->load->library('Sitemap');	
					$sitemap = new Sitemap($book);	
					$sitemap->create_new();	
					
					// Save document if it was uploaded
					$book->init_document();
					$book->save_document();
					
					// Save cover image
					$book->upload_cover_image();
					 
					// Save tags if specified
					$tags = $this->input->post('tags');
					
					$book->initialize_tags();
					$book->add_tags($tags);
					
					// if($tags)
					// $book->update();
				
					redirect($book->get_url());
				}
				redirect('/books');
			}
			else
			{
				$this->load->view('templates/header', array('title' => 'Upload', 'is_logged_in' => $this->is_logged_in));
				$this->load->view('books/create', $data);
				$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
			}
		}
		else
		{
			App\Activity\Access::get_approved();
		}
	}
	
	public function edit($slug_title = null, $id = null)
	{
		$title = App\Utility\StringMethods::unslug($slug_title);
		
		if(!$this->is_logged_in)
			App\Activity\Access::login();

		if($this->logged_in_user->approved_writer)
		{
			$old_book = $this->book->get_first(array('title'=> $title, 'id' => $id, 'user_id' => $this->logged_in_user->id));
			
			if($old_book)
			{
				
				$old_book->price = $old_book->get_price();
				$old_book->init_document();
				
				$this->load->library('form_validation');
				
				if($this->input->post('save'))
				{
					$this->form_validation->set_error_delimiters("<div class = 'form-errors alert-danger'>","</div>");
					$this->form_validation->set_rules('title', 'Title', 'callback_valid_title|required');
					
					if(trim($this->input->post('price')) != 0)
					$this->form_validation->set_rules('price', 'Price', 'callback_valid_price');
				
					$this->form_validation->set_rules('description', 'Description', 'required');
					
					$data = array(
						'book' => new Book($this->input->post()),
						'tags' => $this->input->post('tags')
					);
					
					if($this->form_validation->run())
					{
						$book = $old_book;
						
						$old_title = $old_book->title;
						
						$new_data = array(
						'title' => $this->input->post('title'),
						'price' => $this->input->post('price'),
						'description' => $this->input->post('description')
						);
						
						if($book->update($new_data))
						{
							
							// Do update on sitemap
							if(strtolower($new_data['title']) != strtolower($old_title))
							{
								$this->load->library('Sitemap');
								$sitemap = new Sitemap($book);
								$sitemap->create_new();
							}
							
							/* Save document if it was uploaded */
							
							if(isset($_FILES['image']) && $_FILES['image']['type'])
							$old_book->upload_cover_image();					
				
							if(isset($_FILES['document']) && $_FILES['document']['size'])
							$old_book->save_document();
						
							/* Save tags if specified */
							$tags = $this->input->post('tags');
							
							if($tags)
							{
								$book->initialize_tags();
								$book->add_tags($tags);
								// var_dump($book->save());
							}
							
							redirect($book->get_url());
						}
					}	
				}
				else
				{
					$old_book->initialize_tags();
					$tags = App\Helper\Array_Methods::flatten($old_book->get_tags());
					$tags = is_array($tags) ? implode(',', $tags) : null;
					// exit();
					$data = array(
					'tags' => $tags,
					'book' => $old_book
					);
				}
				
				$data['form_url'] = '/'.$this->uri->uri_string;
				
				$data['document'] = null;
				$document = null;
				
				$old_book->init_document();
				$document = $old_book->get_document();
					
				if($document)
				{
					$data['document']['name'] = $document->clean_name;
					$data['document']['num_downloads'] = $document->num_downloads;
					$data['document']['uploaded'] = date('j/m/Y', $document->created);
					$data['document']['download'] = $old_book->get_download_link();
				}
				
				$this->load->view('templates/header', array('title' => 'Edit | ' . $old_book->title, 'is_logged_in' => $this->is_logged_in));
				$this->load->view('books/edit', $data);
				$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
				return TRUE;
			}
		}
		else
		{
			App\Activity\Access::get_approved();
		}
		
		App\Activity\Access::show_404();
	}
	
	public function show_books($author_name = null)
	{
		
	}
	
	public function get_all()
	{
		
	}
}
?>