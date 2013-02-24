<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Suggest extends CI_Controller {

	private $view_data = array();
	private $adventure_id = false;

	public function __construct() {
		parent::__construct();
		$this->load->model('suggest_model');
		$this->view_data['flashmessage'] = $this->session->flashdata('message');
	}

	public function add($link_id) {
		$this->load->model('links_model');
		$page_id = $this->links_model->get_page_id($link_id, 'destination');
		if(!$this->suggest_model->check_suggest($page_id)) return false;

		$this->load->library('form_validation');
		$this->form_validation->set_rules('suggestText', 'Suggestion', 'required|xss_clean|max_length[256]');
		
		if($this->form_validation->run() === FALSE) {
			$this->session->set_flashdata('message', validation_errors());
			redirect('/page/view/'.$link_id.'/#suggest');
		}
		else {
			$data['user'] = $this->tank_auth->get_user_id();
			if($data['user'] != true) $data['user'] = 1;
			$data['description'] = $this->input->post('suggestText');
			$this->suggest_model->add_suggestion($page_id, $data);
			$this->session->set_flashdata('message', 'Suggestion submitted for approval!');
			redirect('/page/view/'.$link_id.'/');
		}
	}

	public function view($adventure_id) {
		$this->load->model('adventure_model');
		$this->load->model('links_model');
		if(!$this->check_user_adventure($adventure_id)) {
			$this->session->set_flashdata('message', "You aren't allowed to do that.");
			redirect ('/');
		}

		$this->view_data['suggestions'] = $this->suggest_model->get_adventure_suggestions($adventure_id);
		foreach ($this->view_data['suggestions'] as &$suggestion) {
			$suggestion['page_names'] = $this->links_model->get_links($suggestion['page'], 'destination');
		}
		$this->view_data['adventure'] = $this->adventure_model->get_adventure($adventure_id);
		$this->load->view('templates/header', $this->view_data);
		$this->load->view('templates/nav', $this->view_data);
		$this->load->view('suggest/view', $this->view_data);
		$this->load->view('templates/footer', $this->view_data);
	}

	public function approve($suggest_id) {
		$suggestion = $this->suggest_model->get_suggestion($suggest_id);
		if(!$this->check_user($suggestion['page'])) {
			$this->session->set_flashdata('message', "You aren't allowed to do that.");
			redirect ('/');
		}

		$this->load->model('adventure_model');
		$this->load->model('page_model');
		$this->load->model('links_model');
		
		$page = $this->page_model->get_page($suggestion['page']);
		$adventure_id = $page['adventure'];
		//create new page that will be linked
		$page_id = $this->page_model->create_page($adventure_id);
		$data['description'] = $suggestion['description'];
		$data['suggest_user'] = $suggestion['user'];
		$data['source'] = $suggestion['page'];
		$data['destination'] = $page_id;
		$link_id = $this->links_model->add_link($data);
		$suggestions = $this->adventure_model->get_suggest($adventure_id);
		if($suggestions > 0) { //if allowing suggestions, open suggestions on new page
			$anonymous = ($suggestions == 2 ? 'yes' : 'no');
			$this->suggest_model->open_suggestions($page_id, array('anonymous' => $anonymous));
		}
		$this->suggest_model->delete_suggestion($suggest_id);
		redirect("/page/edit/$link_id/"); //redirect to new page
	}

	public function deny($suggest_id) {
		$suggestion = $this->suggest_model->get_suggestion($suggest_id);
		if(!$this->check_user($suggestion['page'])) {
			$this->session->set_flashdata('message', "You aren't allowed to do that.");
			redirect('/');
		}

		$this->load->model('adventure_model');
		$this->load->model('page_model');
		$this->load->model('links_model');

		$page = $this->page_model->get_page($suggestion['page']);
		$adventure_id = $page['adventure'];

		$this->suggest_model->deny_suggestion($suggest_id);
		redirect("/suggest/view/$adventure_id/");
	}

	public function denyall($page_id) {
		if(!$this->check_user($page_id)) {
			$this->session->set_flashdata('message', "You aren't allowed to do that.");
			redirect ('/');
		}
		$this->suggest_model->deny_all_suggestions($page_id);
		$this->session->set_flashdata('message', "All suggestions denied for page <strong>$page_id</strong>!");
		redirect('/suggest/view/'.$this->adventure_id.'/');
	}

	public function closeall($adventure_id) {
		$this->load->model('adventure_model');
		$this->load->model('links_model');
		if($this->tank_auth->get_user_id() != $this->adventure_model->get_creator($adventure_id)) {
			$this->session->set_flashdata('message', "You aren't allowed to do that.");
			redirect ('/');
		}
		$this->suggest_model->close_all_suggestions($adventure_id);
		$this->session->set_flashdata('message', "All suggestions for adventure <strong>$adventure_id</strong> were closed!");
		redirect("/adventure/view/$adventure_id");
	}

	public function send_emails() {
		$this->load->model('suggest_emails_model');
		$this->load->model('adventure_model');
		$this->load->library('email');

		$now = time();
		$last_sent = $this->suggest_emails_model->get_last();
		//show_error($last_sent);
		if(empty($last_sent)) $last_sent = NULL;

		$suggest_emails_id = $this->suggest_emails_model->insert(false);
		$to_email = $this->suggest_model->get_suggestions_for_email($last_sent);

		$sent_count = 0;
		foreach ($to_email as $email) {
			if($email['suggest_count'] < 1) continue;
			
			//do email here
			$this->email->from('adventure@fardogllc.com', 'Adventure');
			$this->email->to($email['email']);
			$url = "http://{$_SERVER['SERVER_NAME']}/suggest/view/{$email['adventure_id']}/";
			$this->email->subject("[Adventure] Suggestions to approve on \"{$email['title']}\"");
			$this->email->message("
				<p>You have ({$email['suggest_count']}) suggestions to view!</p>
				<p>Follow this link to approve/deny: 
				<a href=\"$url\">$url</a></p>");

			$this->email->send();

			$sent_count++;
		}
		$this->suggest_emails_model->update($suggest_emails_id, array("number_sent" => $sent_count));
	}

	function check_user($id) {
		if(!$id) return false;
		$user = $this->tank_auth->get_user_id();
		if(!$user) return false;
		$this->load->model('adventure_model');
		$this->load->model('page_model');
		$this->load->model('adventure_editor_model');
		$this->adventure_id = $this->page_model->get_adventure($id);
		$creator = $this->adventure_model->get_creator($this->adventure_id);
		if ($creator == $user) return true;
		if($this->adventure_editor_model->is_editor($user, $this->adventure_id)) return true;
		return false;
	}

	function check_user_adventure($id) {
		if(!$id) return false;
		$user = $this->tank_auth->get_user_id();
		if(!$user) return false;
		$this->load->model('adventure_model');
		$this->load->model('adventure_editor_model');
		$this->adventure_id = $id;
		$creator = $this->adventure_model->get_creator($this->adventure_id);
		if($creator == $user) return true;
		if($this->adventure_editor_model->is_editor($user, $this->adventure_id)) return true;
		return false;
	}
}