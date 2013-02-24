<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Creator_model extends CI_Model {
	public function __construct() {
		parent::__construct();
		$this->load->database();
	}
	
	public function list_creators() {
		$this->db->select('name, user');
		$this->db->order_by('name', 'asc');
		$query = $this->db->get('creator');
		return $query->result_array();
	}
	
	public function set_creator($user, $name) {
		$insert['name'] = $name;
		$insert['user'] = $user;
		$this->db->insert('creator', $insert);
	}
	
	public function get_creator($user) {
		$this->db->where('user', $user)->limit(1);
		$query = $this->db->get('creator');
		if($query->num_rows() < 1) return false;
		$query = $query->result_array();
		return $query[0];
	}

	public function get_creator_by_name($name) {
		$this->db->where('name', $name)->limit(1);
		$query = $this->db->get('creator');
		if($query->num_rows() < 1) return false;
		$query = $query->result_array();
		return $query[0];
	}
}