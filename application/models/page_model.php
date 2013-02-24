<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page_model extends CI_Model {
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	public function get_page($id) {
		error_log("page is requested was $id");
		$this->db->where('id', $id);
		$query = $this->db->get('page');
		$query = $query->result_array();
		return $query[0];
	}
	
	public function get_count($adventure) {
		$this->db->join('links', 'links.source = page.id')->where('adventure', $adventure);
		return $this->db->count_all_results('page');
	}
	
	public function get_pages($adventure) {
		$this->db->join('links', 'links.destination = page.id')->where('adventure', $adventure)->order_by('destination', 'asc');
		$query = $this->db->get('page');
		return $query->result_array();
	}
	
	public function get_orphans($adventure) {
		$this->db->join('links', 'links.source = page.id', 'left')->where('links.source IS NULL')->where('adventure', $adventure);
		return $this->db->count_all_results('page');
		show_error($this->db->last_query());
	}
	
	public function create_page($adventure, $creator = 1) {
		if(!isset($adventure)) return false;
		$this->load->helper('url');
		$creator = $this->tank_auth->get_user_id();
		if($creator == 0) $creator = 1;
		
		$data = array(
			'creator' => $creator,
			'adventure' => $adventure
		);
		
		try {
			$this->db->insert('page', $data);
		}
		catch (Exception $e) {
			log_message('error',"Failed page creation with: ".$e->getMessage());
			return false;
		}
		return $this->db->insert_id();
	}
	
	public function update_page($id, $data) {
		$data['creator'] = $this->tank_auth->get_user_id();
		if($data['creator'] == 0) $data['creator'] = 1;
		if ($this->db->where('id', $id))
			return $this->db->update('page', $data);
	}
	
	public function get_adventure($id) {
		$this->db->select('adventure')->where('id',$id)->limit(1);
		$query = $this->db->get('page');
		$query = $query->result_array();
		return $query[0]['adventure'];
	}
}

?>
