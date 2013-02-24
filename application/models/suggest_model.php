<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class Suggest_model extends CI_Model {

	public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /* ----- FUNCTIONS RELATED TO SUGGESTIONS THEMSELVES ----- */
    public function add_suggestion($page_id, $data) {
    	$data['page'] = $page_id;
    	return $this->db->insert('suggestion', $data);
    }

    public function get_suggestions($page_id, $count = false) {
    	return $this->_get($page_id, false, $count);
    }

    public function get_suggestion($suggest_id) {
        $this->db->where('id', $suggest_id)->limit(1);
        $result = $this->db->get('suggestion');
        return $result->row_array();
    }

    public function get_all_suggestions($page_id) {
    	return $this->_get($page_id, true);
    }

    public function get_adventure_suggestions($adventure_id, $count = false, $denied = false) {
        $this->db->join('page', 'page.id = suggestion.page')->where('page.adventure', $adventure_id);
        $this->db->select('suggestion.id, suggestion.page, description, suggestion.created, user');
        if(!$denied) $this->db->where('deny', 'no');
        if($count) return $this->db->count_all_results('suggestion');
        $this->db->order_by('suggestion.page');
        $result = $this->db->get('suggestion');
        //show_error($this->db->last_query());
        return $result->result_array();
        
    }

    public function delete_suggestion($suggestion_id) {
        return $this->db->where('id', $suggestion_id)->delete('suggestion');
    }

    public function deny_suggestion($suggestion_id, $reason = null) {
    	$this->load->helper('date');
    	$this->db->set('deny_date', 'NOW()', FALSE);
    	return $this->db->where('id', $suggestion_id)->update('suggestion', array('deny' => 'yes', 'deny_reason' => $reason));
    }

    public function deny_all_suggestions($page_id) {
    	$this->db->set('deny_date', 'NOW()', FALSE);
    	return $this->db->where('page', $page_id)->update('suggestion', array('deny' => 'yes', 'deny_reason' => 'All remaining suggestions for this page were denied.'));
    }


    /* ----- FUNCTIONS RELATED TO SUGGESTION META ----- */
    public function suggestions_allowed($page_id) {
    	$result = $this->db->where('page', $page_id)->get('suggest_page');
    	return $result->num_rows();
    }

    public function suggestion_status($page_id) {
    	$result = $this->db->select('anonymous, timeout')->where('page', $page_id)->get('suggest_page');
        //show_error(print_r($result->row_array()));
    	return $result->row_array();
    }

    public function close_suggestions($page_id) {
    	return $this->db->where('page', $page_id)->delete('suggest_page');
    }

    public function close_all_suggestions($adventure_id) {
        $suggest_page = $this->db->dbprefix('suggest_page');
        $page = $this->db->dbprefix('page');
        return $this->db->query(
            "DELETE $suggest_page from `$suggest_page` JOIN `$page` ON `$page`.`id` = `$suggest_page`.`page` WHERE `$page`.`adventure` = ".$this->db->escape_str($adventure_id).";"
            );
    }

    public function get_suggest_page_count($adventure_id) {
        $this->db->join('page', 'page.id = suggest_page.page')->where('page.adventure', $adventure_id);
        return $this->db->count_all_results('suggest_page');
    }

    public function open_suggestions($page_id, $data) {
    	$open = $this->db->where('page', $page_id)->count_all_results('suggest_page');
    	$data['page'] = $page_id;
    	if($open) return $this->db->where('page', $page_id)->update('suggest_page', $data);
    	else return $this->db->insert('suggest_page', $data);
    }

    public function get_suggestions_for_email($since) {
        $adventure = $this->db->dbprefix('adventure');
        $users = $this->db->dbprefix('users');
        $suggestion = $this->db->dbprefix('suggestion');
        $page = $this->db->dbprefix('page');

        $sql = "select $adventure.id as adventure_id,$adventure.title,$users.email,
            (SELECT COUNT(*) FROM $suggestion JOIN $page ON $suggestion.page = $page.id 
            WHERE $page.adventure = adventure_id AND $suggestion.created > '$since'
            AND $suggestion.deny = 'no') as suggest_count from $adventure 
            JOIN $users ON $adventure.creator = $users.id;";
        $result = $this->db->query($sql);
        return $result->result_array();
    }

    function _get($page_id, $include_denied = false, $count = false) {
    	if(!$include_denied) $this->db->where('deny', 'no');
    	$this->db->where('page', $page_id)->join('users', 'users.id = suggestion.user');
        if($count) {
            return $this->db->count_all_results('suggestion');
        }
        $result = $this->db->get('suggestion');
    	return $result->result_array();
    }

    public function check_suggest($id) {
        $suggestAllowed = $this->suggestions_allowed($id);
        if(!$suggestAllowed) return false;
        
        $status = $this->suggestion_status($id);
        $statusConditional = false;

        if($this->tank_auth->get_user_id()) $statusConditional = true;
        else if ($status['anonymous'] == 'yes') $statusConditional = true;

        if($statusConditional) {
            //show_error(strtotime("December 11, 1982"));
            //show_error($status['timeout']);
            //show_error("status timeout is ".$status['timeout'].", time is ".date("Y-m-d", time()));
            //show_error("status timeout is ".strtotime($status['timeout'].' + 1 day').", time is ".date("Y-m-d", time()));
            if(strtotime($status['timeout']." + 1 day") >= time()) return true; //1 day fix to account for midnight
            else return false;
        }

        return false;
    }

}