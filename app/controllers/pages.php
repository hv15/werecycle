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
			$segarray = $this->uri->segment_array();
			$sessiondata = array(
				'homelatitude'  => $segarray[2],
				'homelongitude'  => $segarray[3]
			);
			$this->session->set_userdata($sessiondata);
			$data['categories'] = $this->map_model->get_categories();
		}
		
		$this->load->view('templates/header', $data);
		$this->load->view('pages/'.$page, $data);
		$this->load->view('templates/footer', $data);

	}
	
}

?>