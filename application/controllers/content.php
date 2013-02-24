<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Content extends CI_Controller {

	private $view_data = array();
	
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->load->model('content_model');
		$this->view_data['flashmessage'] = $this->session->flashdata('message');
		$this->session->keep_flashdata('link_title'); //save our current page title
	}
	
	public function edit($id=FALSE,$link_id=FALSE) {
		if(!isset($id)) {
			$this->session->set_flashdata('message', 'You tried to edit an invalid content ID');
			redirect('/');
		}
		$this->check_user($id);
		$this->load->model('creator_model');
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		$content = $this->content_model->get($id);
		if($content['type'] != 'text') {
			$this->session->set_flashdata('message', 'You tried to edit something besides text. Stop messing with URLs.');
			redirect('/');
		}
		
		$this->form_validation->set_rules('text', 'Text', 'trim|xss_clean');
		
		if($this->form_validation->run() === FALSE) {
			$this->view_data['title'] = "Edit Content";
			$this->view_data['id'] = $id;
			$this->view_data['adv_text'] = form_prep($content['value']);
			$this->view_data['link_id'] = $link_id;
			$this->load->view('templates/header', $this->view_data);
			$this->load->view('templates/nav', $this->view_data);
			$this->load->view('content/edit', $this->view_data);
			$this->load->view('templates/footer', $this->view_data);
		}
		else {
			$text = $this->input->post('text');
			$text = strip_tags($text, "<b><i><br><br/><p><a><ul><li>"); //strip out all the bad tags. all of them.
			$text = auto_link($text, 'both', TRUE); //automatically make URLs/emails into links
			$data = array(
				'value' => $this->input->post('text')
			);
			$this->content_model->edit($id, $data);
			$page = $this->content_model->get_page($id);
			redirect("/page/edit/$link_id/");
		}
	}
	
	public function delete($id = FALSE, $link_id = FALSE) {
		if(!is_numeric($id)) redirect("/");
		$this->check_user($id);
		$page = $this->content_model->get_page_id($id);
		if($this->content_model->delete($id)) {
			$this->session->set_flashdata('message', "Deleted Content ID: $id");
			redirect("/page/edit/".$this->session->flashdata('link_id')."/");
		}
		else {
			$this->session->set_flashdata('message', "Couldn't delete Content ID: $id");
			redirect("/page/edit/$link_id/");
		}
	}
	
	public function move ($id = FALSE, $direction = FALSE, $link_id = FALSE) {
		if(!is_numeric($id)) redirect('/');
		$this->check_user($id);
		$this->content_model->move($id, $direction);
		redirect("/page/edit/$link_id/");
	}
	
	public function check_user($id) {
		if(!is_numeric($id)) return false;
		$this->load->model('page_model');
		$this->load->model('adventure_model');
		$this->load->model('adventure_editor_model');
		$user = $this->tank_auth->get_user_id();
		$page = $this->content_model->get_page($id);
		$adventure = $this->page_model->get_adventure($page);
		$creator = $this->adventure_model->get_creator($adventure);
		$locked = $this->adventure_model->get_locked($locked);
		if($creator == 1) return true;
		if($user != $creator) {
			if(!$locked) return true;
			if($this->adventure_editor_model->is_editor($user, $adventure)) return true;
			$this->session->set_flashdata('message', "You aren't allowed to do that.");
			redirect('/');
		}
	}
}