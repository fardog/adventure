<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Suggest_emails_model extends CI_Model {
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	//data is expected to be an array of adventure_id, session
	public function insert($data) {
		if(!$data) $data = array("number_sent" => 0);
		$this->db->insert('suggest_emails', $data);
		return $this->db->insert_id();
	}

	public function update($id,$data) {
		return $this->db->where('id', $id)->update('suggest_emails', $data);
	}

	public function get_last() {
		$this->db->order_by('last_sent', 'desc')->limit(1);
		$result = $this->db->get('suggest_emails');
		$result = $result->row_array();
		return $result['last_sent'];
	}
}

?>