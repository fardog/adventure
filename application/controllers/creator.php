<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Creator extends CI_Controller {

	private $view_data = array();
	
	public function __construct() {
		parent::__construct();
		$this->load->model('adventure_model');
		$this->load->model('creator_model');
		$this->view_data['flashmessage'] = $this->session->flashdata('message');
	}
	
	public function index() {
		$this->all();
	}
	
	public function all() {
		$this->view_data['creators'] = $this->creator_model->list_creators();
		$this->view_data['title'] = 'Creators';
		$this->load->view('templates/header', $this->view_data);
		$this->load->view('templates/nav', $this->view_data);
		$this->load->view('creator/all', $this->view_data);
		$this->load->view('templates/footer');
	}
	
	public function view($user = false) {
		if(empty($user)) show_404();
		if(is_numeric($user)) {
			$user = $this->creator_model->get_creator($user);
			if(!$user) show_404();
			redirect('/creator/'.strtolower($user['name']), 'location', 301);
		}
		$this->view_data = array();
		$creator = $this->creator_model->get_creator_by_name($user);
		$user = $creator['user'];
		if ($user == $this->tank_auth->get_user_id()) {
			$this->view_data['you'] = true;
		}
		else if(empty($creator['user'])) redirect('/creator/');
		$this->view_data['adventures'] = $this->adventure_model->list_adventures($user);
		$this->view_data['list_title'] = "Adventures by {$creator['name']}";
		$this->view_data['creator'] = $creator['name'];
		if(empty($this->view_data['creator'])) $this->view_data['creator'] = $this->tank_auth->get_username(); //fix for empty username if not created an adventure
		$this->view_data['creator_uid'] = $user;
		$this->load->view('templates/header', $this->view_data);
		$this->load->view('templates/nav', $this->view_data);
		if($this->view_data['you']) $this->load->view('creator/account', $this->view_data);
		$this->load->view('creator/view', $this->view_data);
		if(count($this->view_data['adventures']) > 0) $this->load->view('adventure/all', $this->view_data);
		$this->load->view('templates/footer');
	}
}