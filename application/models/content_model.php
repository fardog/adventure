<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Content_model extends CI_Model {
	public function __construct() {
		parent::__construct();
		$this->load->database();
	}
	
	public function add_content($data) {
		$data['place'] = $this->get_next_id($data['page']);
		$data['creator'] = $this->tank_auth->get_user_id();
		if($data['creator'] == 0) $data['creator'] = 1;
		try {
			$this->db->insert('content', $data);
		}
		catch (Exception $e) {
			log_message('error',"Failed content creation with: ".$e->getMessage());
			return false;
		}
		return $this->db->insert_id();
	}
	
	public function get_content($page = FALSE) {
		if($page === FALSE) return FALSE;
		$this->db->where('page', $page)->order_by('place', 'asc')->order_by('id','asc');
		$query = $this->db->get('content');
		return $query->result_array();
	}
	
	public function get_page_id($id = FALSE) {
		if($id === FALSE) return FALSE;
		$this->db->where('id', $id)->select('page');
		$query = $this->db->get('content');
		$query = $query->result_array();
		return $query[0]['page'];
	}
	
	public function delete($id = FALSE) {
		if($id === FALSE) return FALSE;
		$this->db->where('id', $id)->limit(1);
		return $this->db->delete('content');
	}
	
	public function get($id = FALSE) {
		if($id === FALSE) return FALSE;
		$this->db->where('id', $id)->limit(1);
		$query = $this->db->get('content');
		$query = $query->result_array();
		return $query[0];
	}
	
	public function edit($id, $data) {
		if(!is_numeric($id)) return false;
		$data['creator'] = $this->tank_auth->get_user_id();
		if($data['creator'] == 0) $data['creator'] = 1;
		$this->db->where('id', $id)->limit(1);
		$this->db->update('content', $data);
		return $this->db->affected_rows();
	}
	
	public function move($id = FALSE, $direction) {
		if($id === FALSE) return FALSE;
		$to_move = $this->get($id);
		$to_swap = $this->get_id_direction($to_move['page'], $to_move['place'], $direction);
		if(empty($to_swap['place'])) return false;
		
		$this->db->where('id', $to_move['id'])->limit(1);
		$this->db->update('content', array('place' => $to_swap['place']));
		
		$this->db->where('id', $to_swap['id'])->limit(1);
		$this->db->update('content', array('place' => $to_move['place']));
		return $to_move['page'];
	}
	
	public function get_id_direction($page, $place, $direction) {
		$this->db->where('page', $page);
		if($direction == 'up') $this->db->where('place <', $place)->order_by('place', 'desc');
		else if($direction == 'down') $this->db->where('place >', $place)->order_by('place', 'asc');
		else return false;
		$this->db->select('id, place')->limit(1);
		$query = $this->db->get('content');
		//show_error($this->db->last_query());
		$query = $query->result_array();
		return $query[0];
	}
	
	public function get_next_id($page) {
		$this->db->select('place')->where('page', $page)->order_by('place', 'desc')->limit(1);
		$query = $this->db->get('content');
		$query = $query->result_array();
		$query = $query[0];
		if(empty($query['place'])) return 1;
		else return $query['place']+1;
	}
	
	public function get_page($id) {
		$this->db->select('page')->where('id',$id)->limit(1);
		$query = $this->db->get('content');
		$query = $query->result_array();
		return $query[0]['page'];
	}
}