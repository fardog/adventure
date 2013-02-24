<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Changelog extends CI_Controller {

	private $view_data = array();
	
	public function __construct()
	{
		parent::__construct();
		$this->view_data['flashmessage'] = $this->session->flashdata('message');
		$this->load->driver('cache', array('adapter'=>'file'));
	}

	public function index() {
		$this->load->library('RSSParser', array('url' => '', 'life' => 2));
		$this->load->library('Textile');
  		
  		$this->view_data['items'] = $this->rssparser->getFeed(15);

  		foreach($this->view_data['items'] as &$item) {
  			$textile = new Textile;
  			$item['title'] = $textile->TextileThis($item['title']);
  		}

  		$this->view_data['title'] = "Change Log";

  		$this->load->view("templates/header", $this->view_data);
		$this->load->view("templates/nav", $this->view_data);
  		$this->load->view('changelog/view', $this->view_data);
  		$this->load->view("templates/footer", $this->view_data);
	}
}

?>