<?php
class Pages extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();
	    $this->load->model('map_model');
	}

	public function view($page = 'home')
	{				
		if ( ! file_exists('app/views/pages/'.$page.'.php'))
		{
			// Whoops, we don't have a page for that!
			show_404();
		}
		
		$data['title'] = "RecycleFinder: ".ucfirst($page); // Capitalize the first letter
		$data['page'] = $page;
		
		if($page == 'select') {
			if ($this->uri->segment(2) !== FALSE) {
				$sessiondata['home_latitude'] = $this->uri->segment(2, 0);
				$sessiondata['home_longitude'] = $this->uri->segment(3, 0);
				$sessiondata['user_state'] = 2;
				$this->session->set_userdata($sessiondata);
			}
			$data['categories'] = $this->map_model->get_categories();
		}
		
		if($page == 'map') {
			$segarray = $this->uri->segment_array();
			unset($segarray[1]);
			$sessiondata = array('types_selected' => $segarray);
			$this->session->set_userdata($sessiondata);
		}
		
		$this->load->view('templates/header', $data);
		$this->load->view('pages/'.$page, $data);
		$this->load->view('templates/footer', $data);

	}
	
	public function data()
	{
		$data['outlets'] = $this->map_model->get_outlets();
		$this->load->view('pages/data', $data);
	}
	
	public function get_session() 
	{
		$data['outlets'] = $this->session->all_userdata();
		$this->load->view('pages/data', $data);
	}
	
	public function set_session() 
	{
		echo $this->session->set_userdata();
	}
}

?>