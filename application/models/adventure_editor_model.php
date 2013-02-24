<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Adventure_editor_model extends CI_Model {
	public function __construct() {
		parent::__construct();
		$this->load->database();
	}
	
	public function add_editor($user, $adventure) {
		$this->load->model('creator_model');
		if(!$this->creator_model->get_creator($user)) $this->creator_model->set_creator($user, $this->get_username($user));
		$this->db->insert('adventure_editor', array('user' => $user, 'adventure' => $adventure));
		return $this->db->insert_id();
	}

	public function delete_editor($adventure, $id) {
		$this->db->where('user', $id)->where('adventure', $adventure)->limit(1);
		return $this->db->delete('adventure_editor');
	}

	public function is_editor($user, $adventure) {
		$this->db->where('user', $user)->where('adventure', $adventure);
		$result = $this->db->get('adventure_editor');
		if($result->num_rows() > 0) return true;
		return false;
	}
	
	public function list_editors($adventure) {
		$this->db->where('adventure', $adventure);
		$this->db->select('users.username,adventure_editor.user');
		$this->db->join('users', 'users.id = adventure_editor.user');
		$result = $this->db->get('adventure_editor');
		if($result->num_rows() < 1) return false;
		return $result->result_array();

	}

	public function get_user_id($username) {
		$this->db->where('username', $username);
		$this->db->select('id');
		$result = $this->db->get('users');
		$result = $result->row_array();
		return $result['id'];
	}

	public function get_username($id) {
		$this->db->where('id', $id);
		$this->db->select('username');
		$result = $this->db->get('users');
		$result = $result->row_array();
		return $result['username'];
	}
}