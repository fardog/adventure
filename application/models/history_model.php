<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class History_model extends CI_Model {
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	public function log_history($type, $data) {
		try {
			if($type == 'user') {
				$this->db->insert('history', $data);
			}
			else if($type == 'session') {
				$this->db->insert('anon_history', $data);
			}
			else return false;
		}
		catch (Exception $e) {
			log_message('error',"Failed history log with: ".$e->getMessage());
			return false;
		}
		return $this->db->insert_id();
	}
	
	public function get_history($type, $id, $adventure, $limit = 50) {
		$this->db->where('adventure', $adventure)->limit($limit)->order_by('created', 'desc');
		$query = '';
		if($type == 'user') {
			$this->db->select('page, description, save, history.created, links.id')->join('links', 'history.link = links.id', 'left')->where('user', $id);
			$query = $this->db->get('history');
		}
		else if($type == 'session') {
			$this->db->select('page, description, save, anon_history.created, links.id')->join('links', 'anon_history.link = links.id', 'left')->where('session', $id);
			$query = $this->db->get('anon_history');
		}
		return $query->result_array();
	}
	
	public function save_game($type, $user_id, $id) {
		try {
			if($type == 'user') {
				$this->db->where('id', $id)->where('user', $user_id)->limit(1);
				$save['save'] = 'yes';
				$this->db->update('history', $save);
			}
			else if($type == 'session') {
				$this->db->where('id', $id)->where('session', $user_id)->limit(1);
				$save['save'] = 'yes';
				$this->db->update('anon_history', $save);
			}
			else return false;
		}
		catch (Exception $e) {
			log_message('error',"Failed saving game with: ".$e->getMessage());
			return false;
		}
		return true;
	}
	
	public function load_game($type, $id, $adventure, $last = false) {
		$game = array();
		try {
			if($type == 'user') {
				$this->db->where('user', $id)
					->where('adventure', $adventure)
					->order_by('id', 'desc')->limit(1);
				if(!$last) $this->db->where('save', 'yes');
				$game = $this->db->get('history');
			}
			else if($type == 'session') {
				$this->db->where('session', $id)
					->where('adventure', $adventure)
					->order_by('id', 'desc')->limit(1);
				if(!$last) $this->db->where('save', 'yes');
				$game = $this->db->get('anon_history');
			}
			else return false;
		}
		catch (Exception $e) {
			log_message('error',"Failed loading game with: ".$e->getMessage());
			return false;
		}
		$game = $game->result_array();
		return $game[0];
	}

	public function purge_history($limit) {
		$this->db->select('user')->distinct();
		$users = $this->db->get('history');
		$this->purge_do_per_user($users->result_array(), 'user', $limit);
		$this->db->select('session')->distinct();
		$sessions = $this->db->get('anon_history');
		$this->purge_do_per_user($sessions->result_array(), 'session', $limit);
	}

	public function purge_do_per_user($list, $type, $limit) {
		foreach($list as $item) {
			$database = 'anon_history';
			$field = 'session';
			$distinct = $item['session'];
			if($type == 'user') {
				$database = 'history';
				$field = 'user';
				$distinct = $item['user'];
			}
			$this->db->select('adventure')->distinct()->where($field, $distinct);
			$adventures = $this->db->get($database);
			$adventures = $adventures->result_array();
			foreach($adventures as $adventure) {
				$this->db->select('id')->order_by('id', 'asc')->where($field, $distinct)->where('adventure', $adventure['adventure'])->where('save !=', 'yes');
				$items = $this->db->get($database);
				$items = $items->result_array();
				if(count($items) < $limit) continue;
				$count = count($items) - $limit;
				$remove = array_slice($items, 0, $count);
				foreach($remove as $id) {
					$this->db->where('id', $id['id']);
					$this->db->delete($database);
				}
			}
		}
	}
}

?>
