<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Adventure_model extends CI_Model {
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->driver('cache', array('adapter'=>'file'));
	}
	
	public function list_adventures($creator = false, $limit = false, $details = false, $hidden = false) {
		if(is_numeric($creator)) $this->db->where('creator', $creator);
		if(is_numeric($limit)) $this->db->order_by('id', 'desc')->limit($limit);
		else $this->db->order_by('title', 'asc');
		if(!$hidden) $this->db->where('hidden', 'no');
		if($details) $this->db->join('creator', 'adventure.creator = creator.user', 'left');
		$query = $this->db->get('adventure');
		return $query->result_array();
	}
	
	public function create_adventure($data) {
		$this->load->helper('url');

		$data['slug'] = $this->check_slug($data['slug']);
	
		try {
			$this->db->insert('adventure', $data);
		}
		catch (Exception $e) {
			log_message('error',"Failed adventure creation with: ".$e->getMessage());
			return false;
		}
		return $this->db->insert_id();
	}
	
	public function get_adventure($id) {
		$this->db->where('id', $id);
		$query = $this->db->get('adventure');
		$query = $query->result_array();
		return $query[0];
	}

	public function get_adventure_by_slug($slug) {
		$this->db->where('slug', $slug);
		$query = $this->db->get('adventure');
		$query = $query->result_array();
		return $query[0];
	}
	
	public function update_adventure($id, $data) {
		$this->db->where('id', $id);
		return $this->db->update('adventure', $data);
	}
	
	public function delete_adventure($id) {
		$this->db->where('id', $id)->limit(1);
		$this->db->delete('adventure');
		return $this->db->affected_rows();
	}
	
	public function get_creator($id) {
		$this->db->select('creator')->where('id',$id)->limit(1);
		$query = $this->db->get('adventure');
		$query = $query->result_array();
		return $query[0]['creator'];
	}
	
	public function get_locked($id) {
		$this->db->select('locked')->where('id',$id)->limit(1);
		$query = $this->db->get('adventure');
		$query = $query->result_array();
		if($query[0]['locked'] == 'no') return false;
		return true;
	}

	public function get_suggest($id) {
		$this->db->select('suggest')->where('id',$id)->limit(1);
		$query = $this->db->get('adventure');
		$query = $query->row_array();
		return $query['suggest'];
	}

	public function get_last_updated($count = 10, $order = "DESC") {
		//$this->cache->clean(); //comment this once working
		if ( ! $lastupdated = $this->cache->get("lastupdated-$count-$order")) {
			log_message('error', 'Filling Last Updated Cache');
			$adventure = $this->db->dbprefix('adventure');
			$views = $this->db->dbprefix('views');
			$page = $this->db->dbprefix('page');
			$content = $this->db->dbprefix('content');
			$creator = $this->db->dbprefix('creator');

			$sql = "select $adventure.title as adventure, $content.created as updated,
			$adventure.title as title, $adventure.id as id, $adventure.plain_desc as plain_desc,
			$adventure.slug as slug,
			$creator.name as name, $creator.user as creator
			FROM $adventure
			JOIN $page ON $page.adventure = $adventure.id
			LEFT JOIN (
				select MAX($content.id) max_id,$content.page,$adventure.id 
				from $content JOIN $page ON $page.id = $content.page 
				JOIN $adventure ON $page.adventure = $adventure.id 
				GROUP BY $adventure.id) as content_temp ON $page.id = content_temp.page
			JOIN $content ON $content.id = content_temp.max_id
			JOIN $creator ON $adventure.creator = $creator.user
			WHERE `hidden` = 'no'
			ORDER BY $content.created $order LIMIT $count;";

			$result = $this->db->query($sql);
			$lastupdated = $result->result_array();

     		// Save into the cache for 15 minutes
     		$this->cache->save("lastupdated-$count-$order", $lastupdated, 900);
     	}
		else log_message('error', 'Hit Last Updated Cache');

		return $lastupdated;
	}

	public function list_random($count = 10) {
		//$this->cache->clean(); //comment once working
		if ( ! $random = $this->cache->get("random-$count")) {
			log_message('error', 'Filling Random Cache');
			$adventure = $this->db->dbprefix('adventure');
			$views = $this->db->dbprefix('views');
			$page = $this->db->dbprefix('page');
			$content = $this->db->dbprefix('content');
			$creator = $this->db->dbprefix('creator');

			$sql = "select $adventure.title as adventure,
			$adventure.title as title, $adventure.id as id, $adventure.plain_desc as plain_desc,
			$adventure.slug as slug,
			$creator.name as name, $creator.user as creator
			FROM $adventure JOIN $creator on $adventure.creator = $creator.user
			WHERE `hidden` = 'no'
			ORDER BY RAND() LIMIT $count";

			$result = $this->db->query($sql);
			$random = $result->result_array();

     		// Save into the cache for 15 minutes
     		$this->cache->save("random-$count", $random, 300);
		}
		else log_message('error', 'Hit Random Cache');

		return $random;
	}

	public function check_slug($slug) {
		$illegal = array('view','edit','pages','editors','remove_editor','all','create','delete');

		if(empty($slug)) $slug = 'adv';
		if(array_search($slug, $illegal)) $slug = 'adv-'.$slug;

		$this->db->like('slug', $slug, 'after');
		$this->db->from('adventure');
		$count = $this->db->count_all_results();

		if ($count > 0) $slug = $slug.'-'.$count;

		return $slug;
	}

}

?>