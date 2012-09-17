<?php
class Map extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('map_model');
	}

	public function view($page = 'index')
	{
		$segarray = $this->uri->segment_array();
		$data['outlets'] = $this->map_model->get_outlets();
		
		$this->load->view('templates/header', $data);
		$this->load->view('map/map', $data);
		$this->load->view('templates/footer');
	}

	public function data()
	{
		$data['outlets'] = $this->map_model->get_outlets();
		$this->load->view('map/data', $data);
	}
	
}
?>