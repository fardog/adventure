<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Views_model extends CI_Model {
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->driver('cache', array('adapter'=>'file'));
	}

	//data is expected to be an array of adventure_id, session
	public function insert($data) {
		$sql = $this->db->insert_string('views', $data);
		$sql .= " ON DUPLICATE KEY UPDATE `views` = `views` + 1";
		return $this->db->query($sql);
	}

	public function get_popular($since, $count = 10, $order = "DESC") {
		//$this->cache->clean(); //comment this once working
		$since = date("Y-m-d", $since);

		if ( ! $popular = $this->cache->get("popular-$count-$order-$since")) {
			log_message('error', 'Filling Popular Cache');
			$adventure = $this->db->dbprefix('adventure');
			$views = $this->db->dbprefix('views');
			$page = $this->db->dbprefix('page');
			$creator = $this->db->dbprefix('creator');
			if(!empty($since)) $where = "AND updated > $since";

			//This is the second generation popularity formula
			$sql = "select SUM(`views`) as views, COUNT(`session_id`) as viewers,
			$adventure.title as title, $adventure.id as id, $adventure.plain_desc as plain_desc,
			$adventure.slug as slug,
			$creator.name as name, $creator.user as creator,
			SUM(`views`) / (SELECT COUNT(`$page`.`id`) from $adventure 
			JOIN $page ON $adventure.id = $page.adventure 
			WHERE $adventure.id = $views.adventure GROUP BY $adventure.id) / (SELECT 
			COUNT(`$views`.`session_id`) / (SELECT COUNT(`$page`.`id`) from $page 
			WHERE `$page`.`adventure` = `$views`.`adventure`)
			FROM `$views` WHERE `$views`.`adventure` = `$adventure`.`id`) - $adventure.wmod as weight 
			from $views JOIN $adventure ON $adventure.id = $views.adventure 
			JOIN $creator ON $adventure.creator = $creator.user
			WHERE $adventure.hidden = 'no' $where GROUP BY adventure ORDER BY weight $order LIMIT $count;";

			$result = $this->db->query($sql);
			$popular = $result->result_array();

     		// Save into the cache for 15 minutes
     		$this->cache->save("popular-$count-$order-$since", $popular, 900);
		}
		else log_message('error', 'Hit Popular Cache');

		return $popular;
	}
}

?>