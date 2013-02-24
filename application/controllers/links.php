<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Links extends CI_Controller {

	private $view_data = array();
	
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->load->model('adventure_model');
		$this->load->model('page_model');
		$this->load->model('links_model');
		$this->load->model('suggest_model');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->view_data['flashmessage'] = $this->session->flashdata('message');
		$this->session->keep_flashdata('link_title'); //save our current page title
	}
	
	public function index() {
		redirect('/adventure/all/');
	}
	
	public function add($id, $internal_data = FALSE) {
		$this->form_validation->set_rules('description', 'Description', 'required|xss_clean|max_length[256]');
		$link_id = $id;
		$id = $this->links_model->get_page_id($link_id, 'destination');
		
		if($this->form_validation->run() === FALSE) {
			$this->session->set_flashdata('add_links_error', validation_errors());
			redirect('/page/edit/'.$link_id.'/');
		}
		else {
			$page = $this->page_model->get_page($id);
			$adventure_id = $page['adventure'];
			//create new page that will be linked
			$page_id = $this->page_model->create_page($adventure_id);
			$data['description'] = $this->input->post('description');
			$data['source'] = $id;
			$data['destination'] = $page_id;
			$this->links_model->add_link($data);
			$suggestions = $this->adventure_model->get_suggest($adventure_id);
			if($suggestions > 0) { //if allowing suggestions, open suggestions on new page
				$anonymous = ($suggestions == 2 ? 'yes' : 'no');
				$this->suggest_model->open_suggestions($page_id, array('anonymous' => $anonymous));
			}
			redirect("/page/edit/$link_id/"); //redirect to originating page
		}
	}
	
	public function existing($id) {
		$this->form_validation->set_rules('destination', 'Destination', 'required|integer');
		$this->form_validation->set_rules('desc_exists', 'Description', 'required|xss_clean|max_length[256]');
		$link_id = $id;
		$id = $this->links_model->get_page_id($link_id, 'destination');
		
		if($this->form_validation->run() === FALSE) {
			$this->session->set_flashdata('add_exlinks_error', validation_errors());
			redirect('/page/edit/'.$link_id.'/');
		}
		else {
			$page = $this->page_model->get_page($id);
			$adventure_id = $page['adventure'];
			$data['destination'] = $this->input->post('destination');
			$page_dest = $this->page_model->get_page($data['destination']);
			if(!$page_dest) {
				$this->session->set_flashdata('add_exlinks_error', "The page ID you entered doesn't exist.");
				redirect("/page/edit/$link_id/");
			}
			if($page_dest['adventure'] != $page['adventure']) {
				$this->session->set_flashdata('add_exlinks_error', "The page ID you entered isn't part of your adventure.");
				redirect("/page/edit/$link_id/");
			}
			$data['source'] = $id;
			$data['description'] = $this->input->post('desc_exists');
			$this->links_model->add_link($data);
			redirect("/page/edit/$link_id/"); //redirect to originating page
		}
	}
	
	public function edit($id = FALSE,$link_id = FALSE) {
		if(!isset($id)) {
			$this->session->set_flashdata('message', 'You tried to edit an invalid link ID');
			redirect('/');
		}
		$this->check_user($id);
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		$link = $this->links_model->get($id);
		$this->form_validation->set_rules('description', 'Description', 'required|xss_clean|max_length[256]');
		
		if($this->form_validation->run() === FALSE) {
			$page_content['title'] = "Edit Link";
			$page_content['id'] = $id;
			$page_content['adv_description'] = form_prep($link['description']);
			$page_content['link_id'] = $link_id;
			$this->load->view('templates/header', $page_content);
			$this->load->view('templates/nav', $page_content);
			$this->load->view('links/edit', $page_content);
			$this->load->view('templates/footer', $page_content);
		}
		else {
			$data = array(
				'description' => $this->input->post('description')
			);
			$this->links_model->edit($id, $data);
			$this->session->set_flashdata('message', 'Link edited successfully.');
			redirect("/page/edit/$link_id/");
		}
	}
	
	public function delete($id = FALSE,$link_id = FALSE) {
		if(!is_numeric($id)) redirect("/");
		$this->check_user($id);
		if($this->links_model->delete($id)) {
			$this->session->set_flashdata("Successfully deleted Link ID: $id");
			redirect("/page/edit/$link_id");
		}
		else { 
			$this->session->set_flashdata("Couldn't delete Link ID: $id");
			redirect("/page/edit/$link_id");
		}
	}

	public function move($id = FALSE, $direction = FALSE, $link_id = FALSE) {
		if(!is_numeric($id)) redirect('/');
		$this->check_user();
		$this->links_model->move($id, $direction);
		redirect('/page/edit/'.$link_id);
	}
	
	public function check_user($id) {
		if(!is_numeric($id)) return false;
		$this->load->model('page_model');
		$this->load->model('adventure_model');
		$this->load->model('adventure_editor_model');
		$user = $this->tank_auth->get_user_id();
		$page = $this->links_model->get_source($id);
		$adventure = $this->page_model->get_adventure($page);
		$creator = $this->adventure_model->get_creator($adventure);
		$locked = $this->adventure_model->get_locked($adventure);
		if($creator == 1) return true;
		if($user != $creator) {
			if(!$locked) return true;
			if($this->adventure_editor_model->is_editor($user, $adventure)) return true;
			$this->session->set_flashdata('message', "You aren't allowed to do that.");
			redirect('/');
		}
	}
}

?>