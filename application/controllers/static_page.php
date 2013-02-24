<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Static_page extends CI_Controller {

	private $view_data = array();
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('static_model');
		$this->view_data['flashmessage'] = $this->session->flashdata('message');
	}
	
	public function index() {
		redirect('/');
	}
	
	public function view($id) {
		if(!is_numeric($id)) show_404("/static/view/$id/");
		$content = $this->static_model->get_static($id);
		if(!$content) show_404("/static/view/$id/");
		$this->load->library('textile');
		$textile = new Textile;
		$page_content['title'] = $content['name'];
		$page_content['content'] = $textile->TextileThis($content['content']);
		$page_content['updated'] = $content['updated'];
		
		$this->load->view("templates/header", $page_content);
		$this->load->view("templates/nav", $page_content);
		$this->load->view("static/view", $page_content);
		$this->load->view("templates/footer", $page_content);
	}
	
	public function create() {
		$this->check_user($this->tank_auth->get_user_id());
		$this->load->helper('form');
		$this->load->library('form_validation');
	
		$data['title'] = 'Create a Static Page';
	
		$this->form_validation->set_rules('name', 'Name', 'required|trim|max_length[128]|xss_clean|strip_tags');
		$this->form_validation->set_rules('description','Description', 'trim|max_length[256]|xss_clean|strip_tags');
		$this->form_validation->set_rules('content','Content','xss_clean|strip_tags');
	
		if ($this->form_validation->run() === FALSE)
		{
			$this->load->view('templates/header', $data);
			$this->load->view('templates/nav');
			$this->load->view('static/edit', $page_content);
			$this->load->view('templates/footer');
		}
		else
		{
			$creator_id = 1;
			if($this->tank_auth->is_logged_in()) {
				$creator = $this->creator_model->get_creator($this->tank_auth->get_user_id());
				$creator_id = $creator['user'];
				if(!$creator_id) {
					$this->creator_model->set_creator($this->tank_auth->get_user_id(), $this->tank_auth->get_username());
					$creator_id = $this->tank_auth->get_user_id();
				}
			}
			$description = $this->input->post('description');
			if(empty($description)) $description = $plain_desc = NULL;
			$static = array(
				'name' => $this->input->post('name'),
				'creator' => $creator_id,
				'description' => $this->input->post('description'),
				'content' => $this->input->post('content')
			);
			$static_id = $this->static_model->create_static($static);
			redirect("/static_page/view/$static_id/");
		}
	}
	
	public function edit($id) {
		$this->check_user($this->tank_auth->get_user_id());
		$this->load->helper('form');
		$this->load->library('form_validation');
	
		$this->form_validation->set_rules('name', 'Name', 'required|trim|max_length[128]|xss_clean|strip_tags');
		$this->form_validation->set_rules('description','Description', 'trim|max_length[256]|xss_clean|strip_tags');
		$this->form_validation->set_rules('content','Content','xss_clean|strip_tags');
		
		if ($this->form_validation->run() === FALSE)
		{
			$content = $this->static_model->get_static($id);
			$page_content['editing'] = true;
			$page_content['title'] = "Edit a Static Page";
			$page_content['name'] = $content['name'];
			$page_content['description'] = $content['description'];
			$page_content['content'] = $content['content'];
			$page_content['id'] = $id;
			
			$this->load->view('templates/header', $page_content);
			$this->load->view('templates/nav');
			$this->load->view('static/edit', $page_content);
			$this->load->view('templates/footer');
		}
		else
		{
			$description = $this->input->post('description');
			if(empty($description)) $description = $plain_desc = NULL;
			$static = array(
				'name' => $this->input->post('name'),
				'description' => $this->input->post('description'),
				'content' => $this->input->post('content')
			);
			$static_id = $this->static_model->update_static($id, $static);
			redirect("/static_page/view/$id/");
		}
	}
	
	public function contact() {
		$page_content['title'] = "Contact";
		$this->load->view('templates/header', $page_content);
		$this->load->view('templates/nav');
		$this->load->view('static/contact', $page_content);
		$this->load->view('templates/footer');
	}

	public function check_user($id, $viewing = false) {
		if($id == 2) return true;
		$this->session->set_flashdata('message', "You aren't allowed to do that.");
		redirect('/');
	}
}
