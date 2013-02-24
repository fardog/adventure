<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class Static_model extends CI_Model {
    
	public function __construct() {
        parent::__construct();
        $this->load->database();
    }
	
	public function get_static($id) {
		if(!is_numeric($id)) return false;
		$this->db->where('id', $id)->limit(1);
		$query = $this->db->get('static');
		$query = $query->result_array();
		return $query[0];
	}
	
	public function list_static() {
		$query = $this->db->get('static');
		return $query->result_array();
	}
	
	public function create_static($static) {
		$this->db->insert('static', $static);
		return $this->db->insert_id();
	}
	
	public function update_static($id, $static) {
		$this->db->where('id', $id)->limit(1);
		$this->db->update('static', $static);
	}
}