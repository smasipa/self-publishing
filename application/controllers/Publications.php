<?php
class Publications extends MY_Controller{
	public $Cart;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function valid_title($value)
	{
		$this->form_validation->set_message('valid_title', 'Only letters, numbers, and spaces allowed. Please re-edit Title.');
		$eval = preg_match("#^[^\d][a-z0-9\s]+$#i", $value);
		return $eval ? TRUE : FALSE;
	}
	
	public function save()
	{
		$this->load->model('publication');
		$this->load->library('form_validation');
		
		if(!$this->is_logged_in)
			App\Activity\Access::login();
			
		$is_approved_writer = $this->logged_in_user->is_approved();	
			
		$data['posted'] = array(	
		'title' => null,	
			
		'tags' => null,	
		'text' => null	
		);	
			
		$data['posted'] = $this->input->post('save') ? $this->input->post() : $data['posted'];	
			
		$this->load->library('form_validation');	
		$this->form_validation->set_rules('title', 'Title', 'required|callback_valid_title');	
			
		$this->form_validation->set_rules('is_chapter', 'is_chapter', 'required');	
		$this->form_validation->set_error_delimiters("<div class = 'form-errors alert-danger'>","</div>");	
			
		if($this->input->post('save') && $this->form_validation->run() == TRUE)	
		{	
			$publication = new Publication();	
			$publication->user_id = $this->logged_in_user->id;	
			$publication->title = $this->input->post('title') ? $this->input->post('title') : $publication->title;	
			$publication->text = $this->input->post('text') ? $this->input->post('text') : $publication->title;	
			

			if($is_approved_writer)
			{
				if($this->input->post('accessibility') == 2)	
					$publication->accessibility = 'premium';
			}	
	    
			if($publication->save())	
			{

				// Add link to publications sitemap file	
				$this->load->library('Sitemap');		
				$sitemap = new Sitemap($publication);		
				$sitemap->create_new();		
				
				$tags = $this->input->post('tags');	
				$publication->initialize_tags();	
				$publication->tags->add_tags($tags);	
					
				if($tags)	
				$publication->save();	
				
				if($is_approved_writer)
				{
					// Save document if it was uploaded	
					$publication->init_document();	
					$publication->save_document();	
				}
				
				$this->load->model('Folder');
				// Save new folder if old folder was not specified	
				if($this->input->post('is_chapter') == 2)	
				{	
					$folder = new Folder(array('name' => $publication->title, 'user_id' => $publication->user_id));	
						
					if($folder->id = $folder->save())	
					{	
						$folder->add_item($publication);	
						redirect('folders/edit/'.$folder->id);	
					}	
				}	
				else	
				{	
					$this->session->set_flashdata('new_publication', TRUE);
					redirect('folders/add_item/'.App\Utility\StringMethods::make_slug($publication->title)."/".$publication->id);
				}	
			}	
		}	
			
		$this->load->view('templates/header', array('title' => 'Create Publication', 'is_logged_in' => $this->is_logged_in));		
		$this->load->view('publications/create', array('is_approved_writer' => $is_approved_writer, 'posted' => $data['posted']));		
		$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));		
	}	
	
	public function in_favourites($item)
	{
		if($this->logged_in_user->id)
		{
			$this->load->model('Favourite');
			$favourite = new Favourite(array('user_id' => $this->logged_in_user->id, 'item' => $item));
			
			if($favourite->exists())
			{
				// Change class type at end
				return 'favourites/remove/'.$item->get_url().'/p';
			}
		}
		return;
	}
	
	public function get()
	{
		$this->load->model('Publication');
		$pub_id = $this->uri->segment(2, 0);
		$pub_title = $this->uri->segment(1, 0);	
		$pub_title_slug = $this->uri->segment(1, 0);	
		if($pub_id && $pub_title)
		{
			$pub_title = App\Utility\StringMethods::unslug($pub_title);
			// App\Utility\Url::redirect('/create');
			$publication = new Publication();
			$publication = $publication->get_first(array('id' => $pub_id, 'title' => $pub_title));
			$data = null;
			
			if($publication)
			{
				if($publication->is_banned)
					App\Activity\Access::blocked_url();
			
				$original_publication = $publication;
				
				$is_author = $this->logged_in_user->is_admin() || 
				($this->logged_in_user->id == $original_publication->user_id  && $this->logged_in_user->is_approved())? TRUE : FALSE;
			
				if($this->input->get('remove_comment') == 1 && $comment_id = $this->input->get('c_id'))
				{
					if($is_author && $original_publication->remove_comment($comment_id))
					{
						redirect($original_publication->get_url());
					}
					else
					{
						App\Activity\Access::show_404();
					}
				}
				
				if(empty($this->input->post()))
				{
					// Incriment for GET requests only
					$publication->incr_num_views();
					
					$this->load->model('access/Visit');
					$visitor_id = $this->is_logged_in ? $this->logged_in_user->id : null;
					$visit = new Visit(array('visitor_id' => $visitor_id));
					$visit->record_visit($publication);
				}
				
				if($this->is_logged_in)
				{
					$this->load->model('Recent_Item');
					$recent_item = new Recent_Item(array('user_id' => $this->logged_in_user->id, 'item' => $publication));
					$recent_item->add_item();
				}

				$publication->initialize_tags();
		
				$comments = array();
				if($publication->allow_user_comments)
				{
					$publication->initialize_comments();
					//Add or edit comment and then refresh page
					if($this->input->post('comment') && $text = $this->input->post('text'))
					{
						if(!$this->logged_in_user->id)
							App\Activity\Access::login();
						
						if(!empty(trim($text)))
						{
							// var_dump($publication->sanitize_string($text));
							// return;
							$publication->add_comment($text, $this->logged_in_user->id);
						}
					}
					
					$comments = $publication->get_comments();
				}
				
				//Document
				$publication->init_document();
				
				$data = array(
					'title' => $publication->title,
					'date' => date('d M Y',$publication->created),
					'text' => nl2br(html_entity_decode($publication->text)),
					'premium' => $publication->accessibility == 'premium' ? TRUE : FALSE,
					'word_count' => $publication->num_words . " words"
				);
				
				// Is it in logged_in_user's favourites?
				$data['in_favs'] = FALSE;
				$favs_url = $this->in_favourites($publication);
				$data['in_favs'] = $favs_url ? $favs_url : FALSE;
				
				$data['add_to_favs'] = 'favourites/add/'.$publication->get_url().'/p';
				
				$data['comment_form_url'] = $publication->url;
				
				$this->load->model('user');
				
				$data['author_name'] = $publication->get_author_name();
				
				// Logged In user's account details
				$data['premium_member'] = FALSE;
				
				
				
				$data['in_cart'] = FALSE;
				
				// Handle access to publication and its documents
				if($this->logged_in_user)
				{
					// Premium users do not need to add items to cart
					if($this->logged_in_user->account_type == 'premium')
					{
						$data['premium_member'] = TRUE;
					}
					else
					{
						// Is this item in cart
						$this->load->model('Cart');
						$this->Cart = new Cart(array('user_id' => $this->logged_in_user->id));
						$this->Cart->initialize();
						$data['in_cart'] = $this->Cart->in_cart($publication);
					}
				}
				
				$document = $publication->get_document();
				
				$document_str = "download/p/{$pub_title_slug}/{$publication->id}";
				
				$data['download_pdf'] = $document ? $document_str : null;
				
				$data['tags'] = $publication->get_tags();
				
				$data['comments'] = $comments;
				
				$data['more_from_author'] = array();
				
				// Will be improved at a later stage
				// $data['similar'] = $data['tags'] ? $this->get_similar_items($publication, $data['tags']) : null;
				
				$data['similar'] = null;
				// $data['similar'] = null;
				$author_publications = $publication->get_all(array('id !=' => $publication->id, 'folder_id !=' => $publication->folder_id , 'user_id' => $publication->user_id), 'id, title', array(7, 0), 'created DESC');
				
				if($author_publications)
				{
					foreach($author_publications as &$publication)
					{
						$publication->slug = App\Utility\StringMethods::make_slug($publication->title);
						
						$publication->url = "{$publication->slug}/{$publication->id}";
					}
					
					$data['more_from_author'] = $author_publications;
				}
				
				$data['bread_crumb'] = $original_publication->get_bread_crumb();
				
				$data['paginate'] = array('prev' => null, 'next' => null);
				
				// Containing folder has more than one item
				if($data['bread_crumb'])
				{
					// Re visit for new approach, make it a single query instead of two
					// $pubs = $publication->get_all(array('id !=' => $original_publication->id , 'folder_id' => $original_publication->folder_id), 'title, id', array(2, 0));
					$pubs_prev = $publication->get_first(array('id < ' => $original_publication->id, 'folder_id' => $original_publication->folder_id), 'title, id', 'id DESC');
					
					$pubs_next = $publication->get_first(array('id > ' => $original_publication->id, 'folder_id' => $original_publication->folder_id), 'title, id', 'id ASC');
					
					$data['paginate']['prev'] = is_object($pubs_prev) ? $pubs_prev->get_url() : null;
					$data['paginate']['next'] = is_object($pubs_next) ? $pubs_next->get_url() : null;
					// exit();
/* 					foreach($pubs as $pub)
					{
						if($pub->id > $original_publication->id)
						{
							if(empty($paginate['next']))
							$paginate['next'] = $pub->get_url();
						}
						else
						{
							$paginate['prev'] =  $pub->get_url();
						}
					} */
					
				}
				
				$this->load->model('Folder');
				$folder = new Folder(array('id' => $publication->folder_id, 'user_id' => $publication->user_id));
				$folder->cover_image = $folder->get_cover_image();
				$this->load->library('Social_media');
				$social_media =  new Social_media($original_publication->title, $original_publication->get_url(), $folder->cover_image->location, 'Check out this story ');
				
				$data['social_media'] = $social_media->get_all_btns();
				
				$data['num_views'] = $original_publication->num_views;
				
				$data['is_author'] = $is_author;
				$data['edit_publication'] = $original_publication->get_edit_url();
				
				$data['publication_url'] = $original_publication->get_url();
				
				$data['is_logged_in'] = $this->is_logged_in;
				
				//Prepare comments section
				$this->load->library('form_validation');
				$meta = array('description' => substr( $original_publication->text, 0, 100), 'author' => $data['author_name']);
				
				$this->load->view('templates/header', array('meta_info' => $meta, 'title' => $data['author_name'] . " | " . $data['title'], 'is_logged_in' => $this->is_logged_in));
				$this->load->view('publications/publication', $data);
		$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in, 'facebook_js_btn' => $social_media->init_fb_sharescript()));
				return;
			}
		}
		
		App\Activity\Access::show_404();
	}
	
	public function get_similar_items($publication, $tags)
	{
		// Improve to include pagination
		$similar_items = $publication->tags->get_items($tags);
		
		$ret = array();
		foreach($similar_items as $class => &$items)		
		{		
			foreach($items as &$item)		
			{	
				$item->url = $item->get_url();
				if($item->id != $publication->id)
				$ret[] = $item;
			}		
		}

		return $ret;
	}
	
	public function valid_price($value)
	{
		$this->form_validation->set_message('valid_price', 'Price must be an integer, and not less than R10');
		
		$value = preg_replace("#[R\s]#i", '', $value);
		if($value == null || $value == 0)
			return TRUE;
		
		if($value < 10)
			return FALSE;
		
		$eval = preg_match("#^(\d+)$#", $value);
		return $eval ? TRUE : FALSE;
	}
	
	public function edit($title, $pub_id)
	{
		if($this->is_logged_in && $title && $pub_id)
		{
			$this->load->model('User');
			$this->load->library('form_validation');
			$this->form_validation->set_error_delimiters("<div class = 'form-errors alert-danger'>","</div>");
			$this->form_validation->set_rules('title', 'Title', 'required|callback_valid_title');
		
			$this->load->model('Publication');
			
			$where = array(
			'id' => $pub_id, 'title' => App\Utility\StringMethods::unslug($title)
			);
			
			$is_approved_writer = $this->logged_in_user->is_approved();	
			
			if(!$this->logged_in_user->is_admin())
			{
				$where['user_id'] = $this->logged_in_user->id;
			}
			
			$publication = new Publication();
			
			
			$old_publication = $publication->get_first($where);
			
			
			if($old_publication)
			{
				$publication = $old_publication;
				
				$old_title = $old_publication->title;
				if($publication->is_banned)
					App\Activity\Access::blocked_url();
				
				$publication->initialize_tags();
				$old_tags = $publication->get_tags();
				
				$publication->tag_names = !is_null($old_tags) ?
				implode(',', App\Helper\Array_Methods::flatten($old_tags)) : null;
				
				if($this->input->post('save'))
				{
					$publication->title = $this->input->post('title') ? $this->input->post('title') : $publication->title;
					
					$publication->text = $this->input->post('text') ? $this->input->post('text') : $publication->text;
					// $publication->tag_names =  ? $this->input->post('tags') : $publication->tag_names;
					
					
					// $publication->add_tags('science,comedy');
					
					if($is_approved_writer)
					{
						// if($this->input->post('accessibility') == 1)
							// $publication->accessibility = 'public';
						if($this->input->post('accessibility') == 2)
							$publication->accessibility = 'premium';
					}
					
					if($this->form_validation->run() == TRUE && $publication->save())
					{
						$tags = $this->input->post('tags');
				
						$publication->tags->add_tags($tags);
						
						if(strtolower($this->input->post('title') != strtolower($old_title)))
						{
							// Add link to publications sitemap file	
							$this->load->library('Sitemap');		
							$sitemap = new Sitemap($publication);		
							$sitemap->create_new();	
						}
						
						if($is_approved_writer)
						{
							// Save document if it was uploaded
							$publication->init_document();
							$publication->save_document();
						}
						
						redirect(App\Utility\StringMethods::make_slug($publication->title)."/".$publication->id);
					}
				}
				
				$document = null;
				if($is_approved_writer)
				{
					$publication->init_document();
					$document = $publication->get_document_name();
				}
			
				$data = array('publication' => $publication, 
				'form_url' => $this->uri->uri_string,
				'document' => null,
				'is_approved_writer' => $is_approved_writer, 
				'delete' => 'publications/delete/'.App\Utility\StringMethods::make_slug($publication->title)."/".$publication->id);
				
				if($this->logged_in_user->is_approved())
				{
					$document = null;
					$publication->init_document();
					$document = $publication->get_document();
					
					if($document)
					{
						$data['document']['name'] = $document->clean_name;
						$data['document']['num_downloads'] = $document->num_downloads;
						$data['document']['uploaded'] = date('j/m/Y', $document->created);
						$data['document']['download'] = $publication->get_download_link();
					}
				}
				
				$this->load->view('templates/header', array('title' => 'Edit | '.$publication->title, 'is_logged_in' => $this->is_logged_in));
				$this->load->view('publications/edit', $data);
				$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
				return;
			}
		}
		App\Activity\Access::show_404();
	}
	
	public function delete()
	{
		if(!$this->is_logged_in)
			App\Activity\Access::login();
		
		if($this->uri->segment(2 ,0) == 'delete')
		{
			$title_slug = $this->uri->segment(3, 0);
			$id = $this->uri->segment(4, 0);
			
			$title = App\Utility\StringMethods::unslug($title_slug);
			$this->load->model('Publication');
			$publication = new Publication();
			
			$where = array(
			'id' => $id, 'title' => App\Utility\StringMethods::unslug($title)
			);
			
			if(!$this->logged_in_user->is_admin())
			{
				$where['user_id'] = $this->logged_in_user->id;
			}
			
			$publication = $publication->get_first($where);
			
			if(!$publication)
				App\Activity\Access::show_404();
		
			$redirect_link = "publications/edit/".$title_slug."/".$publication->id;	
			$delete_link = $this->uri->uri_string.'/?confirm=1';	
			$abort_link = $this->logged_in_user->username.'/publications';	
				
			if($this->input->get('doc') == 5 && $this->input->get('confirm') == 1)	
			{	
				$publication->init_document();	
				$publication->remove_document();	
					
				redirect($redirect_link);	
			}					
				
			if($this->input->get('doc') == 5)	
			{	
				$delete_link = $this->uri->uri_string.'/?confirm=1&doc=5';	
				$title = "documents of ".$title;	
			}	
				
			if($this->input->get('confirm') == 1)	
			{	
				$publication->delete();	
				redirect($this->logged_in_user->username."/publications");	
			}					
				
			$this->load->view('templates/header', array('title' => 'Delete | '.$publication->title));	
			$this->load->view('important/delete_confirmation', array('message' => 'You are about to delete "'.ucfirst($title) . '"! <br/> Are you sure?', 'delete' => $delete_link, 'abort' => $abort_link));	
			$this->load->view('templates/footer');	
		}
	}
}

?>