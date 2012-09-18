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
			$data['categories'] = $this->map_model->get_categories();
		}
		
		if($page == 'info') {
			if ($this->uri->segment(2) !== FALSE) {
				$data['info'] = $this->map_model->get_info($this->uri->segment(2));
				$data['categories'] = $this->map_model->get_outlet_categories($this->uri->segment(2));
			}
		}
		
		$this->load->view('templates/header', $data);
		$this->load->view('pages/'.$page, $data);
		$this->load->view('templates/footer', $data);

	}
	
	public function data()
	{
		$userdata = $this->session->all_userdata();
		$types  = $userdata['types_selected'];
		$latitude = $userdata['latitude'];
		$longitude = $userdata['longitude'];
		$distance = $userdata['distance'];
		$outletsarray = $this->map_model->get_outlets($types,$latitude,$longitude,$distance);
		
		$output = '';
		foreach ($outletsarray as $row) {
		    $output .= '{"id":'.$row['outlet_id'].',"type":'.$row['outlet_type'].',"lat":'.$row['latitude'].',"lon":'.$row['longitude'].',"name":"'.$row['outlet_name'].'"},';
		}
		$output = preg_replace('|(.+),|s','\1',$output);
		$output = 'var data = {"outlets": ['.$output.']}';
		
		$data['outlets'] = $output;
		$this->load->view('pages/data', $data);
	}
	
	public function check()
	{
		$segarray = $this->uri->segment_array();
		unset($segarray[1]);
		$userdata = $this->session->all_userdata();
		if(count($segarray)>0) {
			$types = implode($segarray,',');
		}
		$latitude = $userdata['latitude'];
		$longitude = $userdata['longitude'];
		
		$outletsarray = $this->map_model->get_outlets($types,$latitude,$longitude,1000);
		
		$data['outlets'] = count($outletsarray);
		$this->load->view('pages/data', $data);
	}
	
	public function print_session() 
	{
		$data['outlets'] = print_r($this->session->all_userdata(),1);
		$this->load->view('pages/data', $data);
	}
	
	public function get_session() 
	{
		$data['outlets'] = json_encode($this->session->all_userdata());
		$this->load->view('pages/data', $data);
	}
	
	public function set_session() 
	{
		if ($this->uri->segment(2) !== FALSE) {
			$json = urldecode($this->uri->segment(2));
			//$data['debug']['urlseg2'] = $json;
			$array = json_decode($json, true);
			//$data['debug']['jsondecoded'] = $array;
			$this->session->set_userdata($array);
			//$this->load->view('pages/debug', $data);
		}
	}
}

?>