<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Adventure extends CI_Controller {

	private $view_data = array();
	
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->load->model('adventure_model');
		$this->load->model('page_model');
		$this->load->model('creator_model');
		$this->load->model('links_model');
		$this->load->library('textile');
		if(ADV_PROFILE == 'yes') $this->output->enable_profiler(TRUE);
		$this->view_data['flashmessage'] = $this->session->flashdata('message');
	}

	public function index()
	{
		$this->all();
	}
	
	public function about() {
		$this->all(true);
	}
	
	public function view($id, $list_pages = false) {
		if(!isset($id)) $this->all();
		$this->load->model('creator_model');
		$this->load->model('links_model');
		$this->load->model('adventure_editor_model');

		$adventure = null;
		if(is_numeric($id)) { //if we were passed an int, look it up and redirect if necessary
			$adventure = $this->adventure_model->get_adventure($id);
			if(!$adventure) show_404();
			redirect('/adventure/'.$adventure['slug'], 'location', 301);
		}
		else {
			$adventure = $this->adventure_model->get_adventure_by_slug($id);
			$id = $adventure['id'];
			if(!$adventure) show_404();
		}
		
		$this->view_data['id'] = $id;
		$this->view_data['title'] = $adventure['title'];
		$this->view_data['adventure'] = $adventure;
		$textile = new Textile;
		$this->view_data['description'] = $textile->TextileThis($adventure['description']);
		$creator = $this->creator_model->get_creator($adventure['creator']);
		$this->view_data['adv_editor'] = $this->check_user($id, true);
		$this->view_data['adv_delete'] = $this->check_user_delete($id);
		$this->view_data['creator'] = $creator['name'];
		$this->view_data['creator_id'] = $creator['user'];
		$this->view_data['start'] = $adventure['start'];
		$this->view_data['editors'] = $this->adventure_editor_model->list_editors($id);
		$this->view_data['slug'] = $adventure['slug'];
		
		$this->view_data['pages_count'] = $this->page_model->get_count($id)+1;
		//$this->view_data['orphan_count'] = $this->page_model->get_orphans($id);
		$this->view_data['locked'] = $adventure['locked'];
		
		if($list_pages) {
			$this->view_data['pages'] = $this->page_model->get_pages($id);
		}

		$this->load->model('suggest_model');
		if($this->view_data['creator_id'] == $this->tank_auth->get_user_id()
			|| $this->adventure_editor_model->is_editor($this->tank_auth->get_user_id(), $id)) { //check for suggestions in need of approval
			$this->view_data['suggestion_count'] = $this->suggest_model->get_adventure_suggestions($id, true);
		}
		$this->view_data['suggest_page_count'] = $this->suggest_model->get_suggest_page_count($id);
		
		$this->load->view('templates/header', $this->view_data);
		$this->load->view('templates/nav');
		$this->load->view('adventure/view', $this->view_data);
		if($list_pages) $this->load->view('adventure/pages', $this->view_data);
		$this->load->view('templates/footer');
	}
	
	public function edit($id) {
		if(!isset($id)) redirect('/');
		$this->check_user($id);
		$this->load->model('creator_model');
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		$adventure = $this->adventure_model->get_adventure($id);
		if(!$adventure) redirect('/');
		
		$this->form_validation->set_rules('title', 'Title', 'required|trim|max_length[128]|xss_clean|strip_tags');
		$this->form_validation->set_rules('description','Description', 'trim|max_length[512]|xss_clean|strip_tags');
		$this->form_validation->set_rules('css','CSS','trim|xss_clean|strip_tags');
		
		if ($this->form_validation->run() === FALSE) {
			$this->view_data['editing'] = true;
			$this->view_data['adv_title'] = form_prep($adventure['title']);
			$this->view_data['description'] = form_prep($adventure['description']);
			$this->view_data['locked'] = false;
			$this->view_data['id'] = $id;
			$this->view_data['css'] = form_prep($adventure['css']);
			$this->view_data['lock_possible'] = $this->tank_auth->is_logged_in();
			$this->view_data['suggest'] = $adventure['suggest'];
			if($adventure['creator'] == 1) $this->view_data['lock_possible'] = false;
			if($adventure['locked'] == 'no')$this->view_data['locked'] = true;
			$this->view_data['adv_editor'] = $this->check_user($id, true);
			$this->view_data['edit'] = true;
			$this->load->view('templates/header', $this->view_data);
			$this->load->view('templates/nav');
			$this->load->view('adventure/create', $this->view_data);
			$this->load->view('templates/footer');
		}
		else {
			$description = $this->input->post('description');
			if(empty($description)) $description = $plain_desc = NULL;
			else { //create the plain text description
				$this->load->library('textile');
				$textile = new Textile;
				$plain_desc = trim(strip_tags($textile->TextileThis($description)));
			}
			$css = $this->input->post('css');
			if(empty($css)) $css = NULL;
			$locked = 'yes';
			if($this->input->post('locked') == 'lock') $locked = 'no';
			$adventure = array(
				'title' => $this->input->post('title'),
				'description' => $description,
				'locked' => $locked,
				'css' => $css,
				'plain_desc' => $plain_desc,
				'suggest' => $this->input->post('suggest')
			);
			$adventure_id = $this->adventure_model->update_adventure($id, $adventure);
			$adventure = $this->adventure_model->get_adventure($id);
			$this->links_model->edit($adventure['start'], array('description' => $this->input->post('title')));
			redirect("/adventure/view/$id");
		}
		
	}
	
	public function pages($id) {
		$this->view($id, true);
	}

	public function editors($id) {
		if(!isset($id)) redirect('/');
		$this->check_user($id);
		$this->load->model('creator_model');
		$this->load->model('adventure_editor_model');
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		$adventure = $this->adventure_model->get_adventure($id);
		if(!$adventure) redirect('/');

		$this->view_data['id'] = $id;
		$this->view_data['title'] = $adventure['title'];
		$this->view_data['adventure'] = $adventure;
		

		$this->form_validation->set_rules('editor', 'Editor', 'required|trim|max_length[50]|xss_clean|strip_tags|callback_is_valid_editor');
		
		if ($this->form_validation->run() === FALSE) {

		} else {
			$editor_id = $this->adventure_editor_model->get_user_id($this->input->post('editor'));
			$this->adventure_editor_model->add_editor($editor_id, $id);
			$this->session->set_flashdata('message', 'Successfully added editor.');
		}

		$this->view_data['editors'] = $this->adventure_editor_model->list_editors($id);

		$this->load->view('templates/header');
		$this->load->view('templates/nav');
		$this->load->view('adventure/editors', $this->view_data);
		$this->load->view('templates/footer');
	}

	public function remove_editor($id, $editor_id) {
		if(!isset($id)) redirect('/');
		$this->check_user($id);
		$this->load->model('creator_model');
		$this->load->model('adventure_editor_model');

		$adventure = $this->adventure_model->get_adventure($id);
		if(!$adventure) redirect('/');

		$this->adventure_editor_model->delete_editor($id, $editor_id);
		$this->session->set_flashdata('message', "Successfully removed editor.");
		redirect('/adventure/editors/'.$id);
	}

	public function is_valid_editor($username) {
		if($this->tank_auth->is_username_available($username)) {
			$this->form_validation->set_message('is_valid_editor', 'Sorry, there isn\'t a user by that name.');
			return false;
		}
		else return true;
	}
	
	public function all($describe = false, $popular = true, $lastupdated = true, $random = true) {
		$this->load->view('templates/header', $this->view_data);
		$this->load->view('templates/nav');
		$latest = false;
		$this->view_data['list_title'] = "Most Recently Created";
		if($describe) {
			$this->load->view('adventure/about');
			$latest = 5;
		}
		if($popular) { //get most popular adventures
			$this->load->model('views_model');
			$this->view_popular['list_title'] = "Most Popular";
			$this->view_popular['adventures'] = $this->views_model->get_popular(time() - (3 * 24 * 60 * 60),5);
			$this->load->view('adventure/all', $this->view_popular);
		}
		if($lastupdated) { //get latest updated adventures
			$this->view_latest['list_title'] = "Most Recently Updated";
			$this->view_latest['adventures'] = $this->adventure_model->get_last_updated(5);
			$this->load->view('adventure/all', $this->view_latest);
		}
		if($random) { //get some random adventures
			$this->view_random['list_title'] = "Randomly Selected";
			$this->view_random['adventures'] = $this->adventure_model->list_random(5);
			$this->load->view('adventure/all', $this->view_random);
		}
		if(!$latest) $this->view_data['list_title'] = "All Adventures";
		$this->view_data['adventures'] = $this->adventure_model->list_adventures(false, $latest, true);
		$this->load->view('adventure/all', $this->view_data);
		$this->load->view('templates/footer');
	}
	
	public function create() {
		$this->load->helper('form');
		$this->load->library('form_validation');
	
		$this->view_data['title'] = 'Create an Adventure';
		$this->view_data['lock_possible'] = $this->tank_auth->is_logged_in();
	
		$this->form_validation->set_rules('title', 'Title', 'required|trim|is_unique[adventure.title]|max_length[128]|xss_clean|strip_tags');
		$this->form_validation->set_rules('description','Description', 'trim|max_length[512]|xss_clean|strip_tags');
		$this->form_validation->set_rules('css','CSS','trim|xss_clean|strip_tags');
	
		if ($this->form_validation->run() === FALSE)
		{
			$this->load->view('templates/header', $this->view_data);
			$this->load->view('templates/nav');
			$this->load->view('adventure/create');
			$this->load->view('templates/footer');
		}
		else
		{
			$creator_id = 1;
			$locked = 'yes';
			if($this->tank_auth->is_logged_in()) { //set creator and lock if allowed/requested
				$creator = $this->creator_model->get_creator($this->tank_auth->get_user_id());
				$creator_id = $creator['user'];
				if(!$creator_id) {
					$this->creator_model->set_creator($this->tank_auth->get_user_id(), $this->tank_auth->get_username());
					$creator_id = $this->tank_auth->get_user_id();
				}
				if($this->input->post('locked') == 'lock') $locked = 'no';
			}
			else $locked = 'no';
			$description = $this->input->post('description');
			if(empty($description)) $description = $plain_desc = NULL;
			else { //create the plain text description
				$this->load->library('textile');
				$textile = new Textile;
				$plain_desc = trim(strip_tags($textile->TextileThis($description)));
			}
			$css = $this->input->post('css');
			if(empty($css)) $css = NULL;
			$adventure = array(
				'title' => $this->input->post('title'),
				'creator' => $creator_id,
				'slug' => url_title($this->input->post('title'), 'dash', TRUE),
				'description' => $description,
				'locked' => $locked,
				'css' => $css,
				'plain_desc' => $plain_desc,
				'suggest' => $this->input->post('suggest')
			);
			$adventure_id = $this->adventure_model->create_adventure($adventure);
			$page_id = $this->page_model->create_page($adventure_id);
			$suggestions = $this->adventure_model->get_suggest($adventure_id);
			if($suggestions > 0) { //if allowing suggestions, open suggestions on new page
				$this->load->model('suggest_model');
				$anonymous = ($suggestions == 2 ? 'yes' : 'no');
				$this->suggest_model->open_suggestions($page_id, array('anonymous' => $anonymous, 'timeout' => '9999-12-31'));
			}
			$link_id = $this->links_model->add_link(array(
				'description' => $adventure['title'],
				'source' => NULL,
				'destination' => $page_id,
				'place' => 0,
				'start' => 'yes'
				));
			$this->adventure_model->update_adventure($adventure_id, array('start' => $link_id));
			$this->session->set_flashdata('message', "Welcome to your new Adventure!");
			redirect("/page/edit/$link_id");
		}
	}
	
	public function delete($id) {
		if(!$this->check_user_delete($id)) {
			$this->session->set_flashdata('message', "You aren't allowed to do that.");
			redirect ('/');
		}
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		$adventure = $this->adventure_model->get_adventure($id);
		
		$this->form_validation->set_rules('submit','Submit','required');
		
		if ($this->form_validation->run() === FALSE)
		{
			$this->view_data['title'] = $adventure['title'];
			$this->view_data['id'] = $id;
			$this->load->view('templates/header', $this->view_data);
			$this->load->view('templates/nav');
			$this->load->view('adventure/delete', $this->view_data);
			$this->load->view('templates/footer');
		}
		else {
			$this->adventure_model->delete_adventure($id);
			$this->session->set_flashdata('message', "Adventure ID: $id successfully deleted.");
			redirect('/');
		}
		
	}
	
 	public function check_user($id, $viewing = false) {
 		if(!is_numeric($id)) return false;
 		$user = $this->tank_auth->get_user_id();
 		$creator = $this->adventure_model->get_creator($id);
		$locked = $this->adventure_model->get_locked($id);
		if($creator == 1) return true;
 		if($user != $creator) { 
			if(!$locked) return true;
			if($viewing) return false;
			$this->session->set_flashdata('message', "You aren't allowed to do that.");
			redirect('/');
		}
		return true;
 	}
	
	public function check_user_delete($id) {
		if(!is_numeric($id)) return false;
 		$user = $this->tank_auth->get_user_id();
 		$creator = $this->adventure_model->get_creator($id);
		if($creator == 1) return false;
		if($user == $creator) {
			return true;
		}
		return false;
	}
	
}

/* End of file adventure.php */
/* Location: ./application/controllers/adventure.php */
