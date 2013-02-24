<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class History extends CI_Controller {

	private $view_data = array();
	
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->load->model('adventure_model');
		$this->load->model('page_model');
		$this->load->model('creator_model');
		$this->load->model('history_model');
		$this->view_data['flashmessage'] = $this->session->flashdata('message');
	}

	public function index()
	{
		
	}
	
	public function get($id) {
		if(!is_numeric($id)) return false;
		date_default_timezone_set('UTC'); //set the timezone. this should be moved elsewhere i bet.
		
		$adventure = $this->adventure_model->get_adventure($id); //get the adventure for its name and etc.
		if($this->tank_auth->is_logged_in()) //get history if we're logged in
			$history = $this->history_model->get_history('user', $this->tank_auth->get_user_id(), $id);
		else //else get it from session info
			$history = $this->history_model->get_history('session', $this->session->userdata('session_id'), $id);
		
		$i = 0;
		$data = array();
		foreach ($history as $item) { //build history items
			$data[$i]['url'] = "/page/view/{$item['id']}/";
			//if(!empty($item['id'])) $data[$i]['url'] .= "i/link/{$item['id']}/";
			if(!empty($item['description'])) $data[$i]['description'] = word_limiter($item['description'],4);
			else $data[$i]['description'] = "(direct)";
			$data[$i]['date'] = date("m/d, H:i", strtotime($item['created']));
			if($item['save'] == 'yes') $data[$i]['save'] = true;
			else $item['save'] = false;
			$i++;
		}
		
		//send json output
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($data));
	}

	public function save($id) {
		if(!is_numeric($id)) return false;
		$type = ($this->tank_auth->is_logged_in() ? 'user' : 'session');
		$user_id = ($this->tank_auth->is_logged_in() ? $this->tank_auth->get_user_id() : $this->session->userdata('session_id'));
		$result['success'] = $this->history_model->save_game($type, $user_id, $id);

		//send json output
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($result));
	}

	public function load($id) {
		$type = ($this->tank_auth->is_logged_in() ? 'user' : 'session');
		$user_id = ($this->tank_auth->is_logged_in() ? $this->tank_auth->get_user_id() : $this->session->userdata('session_id'));
		$game = $this->history_model->load_game($type, $user_id, $id);
		if(empty($game['page'])) {
			$this->session->set_flashdata('message', 'Sorry, but we couldn\'t find a savegame for this adventure.');
			$game = $this->history_model->load_game($type, $user_id, $id, true);
			if(empty($game['page'])) {
				$this->session->set_flashdata('message', "Couldn't load a savegame. Sorry.");
				redirect("/adventure/$id");
			}
		}
		else $this->session->set_flashdata('message', "Game loaded successfully.");
		redirect("/page/view/{$game['link']}/");
	}

	//occasionally purge history
	public function purge() {
		$this->history_model->purge_history(50);
	}

}

/* End of file history.php */
/* Location: ./application/controllers/history.php */
