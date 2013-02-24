<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page extends CI_Controller {
	
	private $editing = false;
	private $suggesting = false;
	private $adventure_id = null;
	private $adventure = null;
	private $page = null;
	private $view_data = array();
	
	public function __construct() {
		parent::__construct();
		$this->load->helper('url');
		$this->load->model('adventure_model');
		$this->load->model('page_model');
		$this->load->model('content_model');
		$this->load->model('links_model');
		$this->load->model('suggest_model');
		if(ADV_PROFILE == 'yes') $this->output->enable_profiler(TRUE);
		$this->view_data['flashmessage'] = $this->session->flashdata('message');
	}
	
	public function index() {
		redirect("/adventure/"); //if called directly, just show adventures
	}
	
	public function page_header($id) {
		$this->view_data['page_id'] = $id;
		$this->view_data['edit'] = $this->editing; //show special items for edit mode
		if(!$this->page) $this->page = $this->page_model->get_page($id);
		if(!$this->adventure) $this->adventure = $this->adventure_model->get_adventure($this->page['adventure']);
		$this->view_data['suggestions'] = $this->suggest_model->get_suggestions($id, true);
		$this->view_data['css_page'] = $this->page['css'];
		$this->view_data['title'] = $this->adventure['title'];
		$this->view_data['adventure_id'] = $this->adventure['id'];
		$this->view_data['adventure_slug'] = $this->adventure['slug'];
		$this->view_data['css_embed'] = $this->adventure['css'];
		$this->view_data['editor'] = $this->check_user($id, true);
		$this->view_data['adventure'] = $this->adventure;
		$this->view_data['slug'] = $this->adventure['slug'];
		$this->load->view('templates/header', $this->view_data);
		$this->load->view('templates/nav', $this->view_data);
	}
	
	public function page_footer($id) {
		$this->view_data['page_id'] = $id;
		$this->load->view('templates/footer', $this->view_data);
	}
	
	public function view($id = false, $redirect = false) {
		if(!$id) redirect("/adventure/"); //if no page specified, show all adventures
		//perform a quick remap of the id
		$link_id = $id;
		$this->session->set_flashdata('link_id', $link_id);
		$id = $this->links_model->get_page_id($id, 'destination');
		if(empty($id)) show_404();
		$this->view_data['link_id'] = $link_id;
		if($redirect) { // we need to redirect the page to the named adventure page
			if(!$this->page) $this->page = $this->page_model->get_page($id);
			if(!$this->adventure) $this->adventure = $this->adventure_model->get_adventure($this->page['adventure']);
			redirect("/adventure/".$this->adventure['slug']."/$link_id", 'location', 301);
		}

		$content['id'] = $id;
		$content['editor'] = $this->check_user($id, true);
		if(($this->editing == false) && ($this->suggesting == false)) $this->page_header($id); //if we were called directly, show the header
		else $content['edit'] = true; //else set editing so we know how to display in the view
		if(!$this->page) $this->page = $this->page_model->get_page($id); //get the page if we don't have it already
		
		//get and parse content for page id, if any
		$raw_content = $this->content_model->get_content($id);
		
		//set up content for page template, and create embed code
		for ($i = 0; $i < count($raw_content); $i++) {
			if ($raw_content[$i]['type'] == 'video') 
				$content['items'][$i]['content'] = $this->youtube_embed($raw_content[$i]['value'],$raw_content[$i]['options']);
			if ($raw_content[$i]['type'] == 'audio') 
				$content['items'][$i]['content'] = $this->soundcloud_embed($raw_content[$i]['value'],$raw_content[$i]['options']);
			if ($raw_content[$i]['type'] == 'image') 
				$content['items'][$i]['content'] = $this->imgur_embed($raw_content[$i]['value'],$raw_content[$i]['options']);
			if ($raw_content[$i]['type'] == 'text') {
				$this->load->library('textile');
				$textile = new Textile;
				$content['items'][$i]['content'] = $textile->TextileThis($raw_content[$i]['value']);
			}
			$content['items'][$i]['id'] = $raw_content[$i]['id'];
			$content['items'][$i]['type'] = $raw_content[$i]['type'];
		}
		
		//set header from previous link URI, set to flashdata
		//retrieve flashdata if possible
		//display adventure title if all else fails, this should be less problematic under the new links model
		$content['header'] = $this->links_model->get_title($link_id);
		if(empty($content['header'])) {
			$content['header'] = $this->session->flashdata('link_title');
			if(!empty($content['header'])) $this->session->keep_flashdata('link_title');
			else {
				if(!$this->adventure) $this->adventure = $this->adventure_model->get_adventure($this->page['adventure']);
				$content['header'] = $this->adventure['title'];
			}
		}
		else $this->session->set_flashdata('link_title', $content['header']);

		//log history and view
		if(!$this->editing) {
			$this->load->model('history_model');
			$this->load->model('views_model');
			$this->load->model('adventure_editor_model');
			$history['adventure'] = $this->adventure['id'];
			$history['page'] = $this->page['id'];
			$history['link'] = $link_id;
			$history_type = 'session';
			if($this->tank_auth->is_logged_in()) {
				$history_type = 'user';
				$history['user'] = $this->tank_auth->get_user_id();
			}
			else $history['session'] = $this->session->userdata('session_id');
			$content['history_id'] = $this->history_model->log_history($history_type, $history);
			
			if($this->tank_auth->get_user_id() != $this->adventure['creator']
				&& !$this->adventure_editor_model->is_editor($this->tank_auth->get_user_id(), $this->adventure['id'])) //if this isn't your adventure
				$this->views_model->insert(array(
					"adventure" => $history['adventure'],
					"session_id" => $this->session->userdata('session_id')
					));
		}

		//check if suggestions are allowed for this user

		$content['suggest'] = $this->suggest_model->check_suggest($id);
		$content['link_id'] = $link_id;
		if($content['suggest']) $this->load->helper('form');
		
		$this->load->view('page/view', $content);
		$this->links($id,$link_id);

		if(!$this->editing) {
			$this->page_footer($id);
		}
	}
	
	public function edit($id = false) {
		if(!$id) { show_error('there is no id'); return; }
		//perform a quick remap of the id
		$link_id = $id;
		$this->session->set_flashdata('link_id', $link_id);
		$id = $this->links_model->get_page_id($id, 'destination');
		$this->view_data['link_id'] = $link_id;

		$this->check_user($id);
		$this->editing = true;
		$this->view_data['page'] = $id;
		$this->page_header($id);
		
		$this->load->helper('form');
		$this->load->library('form_validation');

		$this->view_data['css_edit'] = $this->page['css'];
		
		$this->form_validation->set_rules('video', 'Video', 'callback_youtube_check');
		$this->form_validation->set_rules('videoStart', 'Video Start Time', 'numeric');
		$this->form_validation->set_rules('image', 'Image', 'callback_imgur_check');
		$this->form_validation->set_rules('audio', 'Audio', 'callback_soundcloud_check');
		$this->form_validation->set_rules('text', 'Text', 'trim|xss_clean');

		//get suggestion availability
		$suggestion = $this->suggest_model->suggestions_allowed($id);
		if($suggestion) {
			$suggestStatus = $this->suggest_model->suggestion_status($id);
			if($suggestStatus['anonymous'] == 'yes') $this->view_data['suggest_all'] = true;
			else $this->view_data['suggest_auth'] = true;
			$this->view_data['suggestTimeout'] = $suggestStatus['timeout'];
		}
		else {
			$this->view_data['suggest_none'] = true;
			if($this->adventure['locked'] == 'no') $this->view_data['suggestions_disabled'] = true;
		}
		
		if($this->form_validation->run() === FALSE) {
			$this->view_data['misc_error'] = $this->session->flashdata('misc_error');
			$this->load->view('page/edit', $this->view_data);
		}
		else { //do inserts 
			$video = $this->input->post('video');
			$audio = $this->input->post('audio');
			$image = $this->input->post('image');
			$text = $this->input->post('text');

			if(empty($video) && empty($audio) && empty($image) && empty($text)) {
				$this->session->set_flashdata('misc_error', "You didn't enter any content!");
			}
			
			if($video) {
				//get other data
				$videoStart = $this->input->post('videoStart');
				$videoOptions = array(
					autoplay => ($this->input->post('videoAutoplay') == '1' ? 1 : 0),
					audio => ($this->input->post('videoAudio') == '1' ? 1 : 0),
					mute => ($this->input->post('videoMute') == '1' ? 1 : 0),
					loop => ($this->input->post('videoLoop') == '1' ? 1 : 0),
					hd => ($this->input->post('videoHD') == '1' ? 1 : 0),
					start => (empty($videoStart) ? 0 : $videoStart)
				);
				//build insert data
				$data = array(
					'page' => $id,
					'type' => 'video',
					'value' => $this->youtube_parse($video),
					'options' => serialize($videoOptions)
				);
				$this->content_model->add_content($data);
			}
			if($audio) {
				$audioOptions = array(
					autoplay => ($this->input->post('audioAutoplay') == '1' ? 1 : 0)
				);
				$data = array(
					'page' => $id,
					'type' => 'audio',
					'value' => $this->soundcloud_parse($audio),
					'options' => serialize($audioOptions)
				);
				$this->content_model->add_content($data);
			}
			if($image) {
				$imageWidth = $this->input->post('imageWidth');
				$imageHeight = $this->input->post('imageHeight');
				$imageOptions = array(
					width => (empty($imageWidth) ? 0 : $imageWidth),
					height => (empty($imageHeight) ? 0 : $imageHeight)
				);
				$data = array(
					'page' => $id,
					'type' => 'image',
					'value' => $this->imgur_parse($image),
					'options' => serialize($imageOptions)
				);
				$this->content_model->add_content($data);
			}
			if(!empty($text)) {
				$text = strip_tags($text, "<b><i><br><br/><p><a><ul><ol><li><pre>"); //strip out all the bad tags. all of them.
				$text = auto_link($text, 'both', TRUE); //automatically make URLs/emails into links
				$data = array(
					'page' => $id,
					'type' => 'text',
					'value' => $text
				);
				$this->content_model->add_content($data);
			}
			$this->session->keep_flashdata('link_title'); //save our current page title
			redirect("/page/edit/$link_id");
		}
		$this->view_data['add_error'] = $this->session->flashdata('add_links_error');
		$this->view_data['exist_error'] = $this->session->flashdata('add_exlinks_error');
		$this->view_data['options_error'] = $this->session->flashdata('add_options_error');
		
		//build page list
		$pages = $this->page_model->get_pages($this->adventure_id);

		$this->view_data['pages'] = $pages;
		$this->load->view('links/add', $this->view_data);
		$this->view($link_id);
		$this->page_footer($id);
	}

	public function options($link_id) {
		if(!$link_id) { show_error('there is no id'); return; }
		$page_id = $this->links_model->get_page_id($link_id, 'destination');
		$this->view_data['link_id'] = $link_id;

		$this->check_user($page_id);
		$this->editing = true;
		
		$this->load->helper('form');
		$this->load->library('form_validation');

		$this->form_validation->set_rules('css', 'CSS', 'trim|xss_clean|strip_tags');
		//$this->form_validation->set_rules('allowSuggestions', 'Allow Suggestions', 'required');
		//$this->form_validation->set_rules('suggestTimeout', 'Suggestion Timeout', 'required');
		
		if($this->form_validation->run() === FALSE) {
			show_error(validation_errors());
			$this->session->set_flashdata('add_options_error', validation_errors());
			redirect('/page/edit/'.$link_id.'/');
		}
		else { //do inserts
			$this->load->model('suggest_model');
			$css = $this->input->post('css');
			$this->page_model->update_page($page_id, array('css' => $css)); //insert per-page css to database

			$suggest = $this->input->post('allowSuggestions');

			//see if we're allowing suggestions already
			if($suggest == 'none') {
				$this->suggest_model->close_suggestions($page_id);
				$suggest = false;
			}
			else if ($suggest == 'all') $anonymous = 'yes';
			else if ($suggest == 'authenticated') $anonymous = 'no';

			$suggestTimeout = $this->input->post('suggestTimeout');
			if($suggestTimeout == 'Forever') $suggestTimeout = null;
			else if(empty($suggestTimeout)) $suggestTimeout = null;
			
			if(!empty($suggest)) 
				$this->suggest_model->open_suggestions($page_id, array('anonymous' => $anonymous, 'timeout' => $suggestTimeout));

			$this->session->set_flashdata('message', 'Options successfully changed.');
			redirect('/page/edit/'.$link_id);
		} 
	}
	
	public function links($id,$link_id) {
		$data = $this->links_model->get_links($id);
		$page_content['links'] = $data;
		$page_content['edit'] = $this->editing;
		$page_content['link_id'] = $link_id;
		$this->load->view('links/list', $page_content);
	}


	public function suggest($id) {
		if(!$id) { show_error('there is no id'); return; }
		$this->suggest_model->check_suggest($id);
		$this->suggesting = true;
		$this->view_data['page'] = $id;
		$this->page_header($id);


		$this->view($id);
		$this->page_footer($id);		
	}
	
	
	/* Below are callback functions used for checking the various data types that can be present.
	 * None should be used outside of that probably. Here be dragons and etc.
	 */	

	public function youtube_embed($id, $options = FALSE) {
		if (empty($id)) return FALSE;

		if($this->editing) $options = FALSE;
		else $options = unserialize($options);
		if($options['autoplay'] == 1) $autoplay = '&autoplay=1';
		if($options['loop'] == 1) $loop = '&loop=1';
		//if($options['mute'] == 1) $mute = '&thereisnomute';
		if($options['start'] > 0) $start = "&start={$options['start']}";
		if($options['hd'] == 1) {
			$hd = '&hd=1';
			$width = 960;
			$height = 540;
		}
		else {
			$width = 640;
			$height = 390;
		}
		if($options['audio'] == 1) {
			//return '<object width="420" height="25"><param name="movie" value="http://www.youtube.com/v/'.$id.'?fs=0&amp;hl=en_US'.$autoplay.$loop.$start.$hd.'"></param><param name="allowFullScreen" value="false"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/'.$id.'?fs=1&amp;hl=en_US'.$autoplay.$loop.$start.$hd.'" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="420" height="25"></embed></object>';
			$audio_start = '<div class="ytAudio" style="width:640px;height:38px;overflow:hidden;">';
			$audio_end = "</div>";
			$audio_iframe = "position:relative;top:-407px;";
			$height = 438;
		}
		return $audio_start.'<iframe width="'.$width.'" height="'.$height.'" src="http://www.youtube.com/embed/'.$id.'?'.$autoplay.$loop.$start.$hd.'" frameborder="0" style="'.$audio_iframe.'" allowfullscreen></iframe>'.$audio_end;
	}

	public function soundcloud_embed($url, $options = FALSE) {
		if($this->editing) $options = FALSE;
		else $options = unserialize($options);
		if($options['autoplay'] == 1) $autoplay = true;
		else $autoplay = false;
		return $this->soundcloud_check($url, true, $autoplay);
	}

	public function imgur_embed($id, $options = FALSE) {
		if (empty($id)) return FALSE;
		
		$options = unserialize($options);
		if($options['width'] > 0) $width = "width: {$options['width']}px;";
		if($options['height'] > 0) $height = "height: {$options['height']}px;";
		if ($this->editing) {
			$width .= "max-width: 700px;";
		}
		return '<img style="'.$width.$height.'" src="http://i.imgur.com/'.$id.'.gif" />';
	}

	public function youtube_check($url) {
		if(empty($url)) return true;
		$url = $this->youtube_parse($url);
		
		//check if youtube id is valid at insert time
		$this->load->library('curl');
		$this->curl->create("http://gdata.youtube.com/feeds/api/videos/$url");
		$this->curl->execute();
		
		if($this->curl->error_code) { //if there was an error checking the id
			$this->form_validation->set_message('youtube_check', 'It looks like the Youtube ID/URL you entered was invalid. Please try copying and pasting the URL of the Youtube page.');
			return false;
		}
		return true;
	}

	public function youtube_parse($url) {
		if (strpos($url, 'youtu')) {
			if (preg_match('%(?:youtube\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i',$url, $match)) return $match[1];
		}
		else return($url);
	}
	
	public function imgur_check($url) {
		 if(empty($url)) return true;
		 $url = $this->imgur_parse($url);
		 
		 //check if imgur id is valid at insert time
		 $this->load->library('curl');
		 $this->curl->create("http://imgur.com/$url");
		 $this->curl->execute();
		 
 		if($this->curl->error_code) { //if there was an error checking the id
 			$this->form_validation->set_message('imgur_check', 'It looks like the Imgur ID/URL you entered was invalid. Please try copying and pasting the URL of the Imgur page for the image you uploaded.');
 			return false;
 		}
 		return true;
	}

	public function imgur_parse($url) {
 		if(strpos($url, 'imgur')) {
 			if(preg_match('%(?<=imgur\.com/)([A-Za-z0-9]{5}$)|(?<=imgur\.com/gallery/)([A-Za-z0-9]{5}$)|(?<=i.imgur\.com/)([A-Za-z0-9]{5})|([A-Za-z0-9]{5}$)%i', $url, $match)) return $match[0];
 		}
 		else return($url);
	}
	
	public function soundcloud_check($url, $xml = false, $play = true) {
		 if(empty($url)) return true;
		 
		 $this->load->library('curl');
		 $this->curl->create("http://soundcloud.com/oembed?format=xml&maxwidth=420&show_artwork=false&auto_play=$play&show_comments=false&iframe=true&url=$url");
		 $audio = $this->curl->execute();
		 
		 if($this->curl->error_code) {
			 $this->form_validation->set_message('soundcloud_check', 'It looks like the Soundcloud URL you provided was invalid. Please try copying and pasting the URL of the Soundcloud page for the sound you\'re trying to embed');
			 return false;
		 }
		 if($xml) {
			 $this->load->library('simplexml');
			 $data = $this->simplexml->xml_parse($audio);
			 preg_match("/\<\!\[CDATA\[(.*?)\]\]\>/ies", $data['html'], $match);
			 return $match[1];
		 }
		 return true;
	}
	
	public function soundcloud_parse($url) {
		 return $url;
	}
	
	public function text_check($text) {
		//we actually handle everything up above
		return true;
	}
	
	public function check_user($id, $viewing = false) {
		if(!is_numeric($id)) return false;
		$this->load->model('adventure_model');
		$this->load->model('adventure_editor_model');
		$user = $this->tank_auth->get_user_id();
		$this->adventure_id = $this->page_model->get_adventure($id);
		$creator = $this->adventure_model->get_creator($this->adventure_id);
		$locked = $this->adventure_model->get_locked($this->adventure_id);
		if($creator == 1) return true;
		if($user != $creator) {
			if(!$locked) return true;
			if($this->adventure_editor_model->is_editor($user, $this->adventure_id)) return true;
			if($viewing) return false;
			$this->session->set_flashdata('message', "You aren't allowed to do that.");
			redirect('/');
		}
		return true;
	}
}