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
			$outletdata = $this->map_model->get_info($row['outlet_id']);
		    $output .= '{"id":'.$row['outlet_id'].',"type":'.$outletdata['type'].',"lat":'.$outletdata['latitude'].',"lon":'.$outletdata['longitude'].',"name":"'.$outletdata['name'].'"},';
		}
		$output = preg_replace('|(.+),|s','\1',$output);
		$output = 'var data = {"outlets": ['.$output.']}';
		
		$data['data'] = $output;
		$this->load->view('pages/data', $data);
	}
		
	public function datanew()
	{
		$userdata = $this->session->all_userdata();
		$types  = $userdata['types_selected'];
		$lat = $userdata['lat'];
		$lng = $userdata['lng'];
		$map_zoom = $userdata['map_zoom'];
		
		$outletsData = $this->map_model->get_outlets_new($types,$lat,$lng,$map_zoom);
		$clustersArray = $outletsData['clusters'];
		$singleOutletsArray = $outletsData['singleOutlets'];
		
		$clustersJSON = '';
		foreach ($clustersArray as $cluster) {
		    $clustersJSON .= '{"lat":'.$cluster['lat'].',"lng":'.$cluster['lng'].',"count":"'.$cluster['count'].'"},'."";
		}
		$clustersJSON = preg_replace('|(.+),|s','\1',$clustersJSON); // Strip trailing comma
		
		$singleOutletsJSON = '';
		foreach ($singleOutletsArray as $singleOutletID => $singleOutlet) {
		    $singleOutletsJSON .= '{"id":'.$singleOutletID.',"lat":'.$singleOutlet['lat'].',"lng":'.$singleOutlet['lng'].',"typrat":'.$singleOutlet['typesratio'].'},'."";
		}
		$singleOutletsJSON = preg_replace('|(.+),|s','\1',$singleOutletsJSON); // Strip trailing comma
		
		$data['data'] = "var clusterData = {\"clusters\": [$clustersJSON]}; \n\n var singleOutletData = {\"singleOutlets\": [$singleOutletsJSON]};";
		$this->load->view('pages/data', $data);
	}
	
	public function check()
	{
		$userdata = $this->session->all_userdata();
		$latitude = $userdata['latitude'];
		$longitude = $userdata['longitude'];
		$types = $userdata['types_selected'];
		
		$outletsarray1 = $this->map_model->get_outlets($types,$latitude,$longitude,1);
		if(count($outletsarray1) > 0) {
			$data['code'] = 1;
			$data['message'] = "There is at least one recycle point within 1 mile of this location which allows all of the types you have selected";
			//$data['debug'] = print_r($outletsarray1,1);
		} else {	
			$outletsarray5 = $this->map_model->get_outlets($types,$latitude,$longitude,5);
			if(count($outletsarray5) > 0) {
				$data['code'] = 5;
				$data['message'] = "There are no recycle points within 1 mile of this location which allow all of the types you have selected; however, there is at least one within 5 miles - you may need to zoom out on the map!";
			} else {		
				$outletsarray10 = $this->map_model->get_outlets($types,$latitude,$longitude,10);
				if(count($outletsarray10) > 0) {
					$data['code'] = 10;
					$data['message'] = "There are no recycle points within 10 miles of this location which allow all of the types you have selected; however, there is at least one within 10 miles - you may need to zoom out on the map!";
				} else {
					$outletsarray30 = $this->map_model->get_outlets($types,$latitude,$longitude,30);
					if(count($outletsarray30) > 0) {
						$data['code'] = 30;
						$data['message'] = "There are no recycle points within 10 miles of this location which allow all of the types you have selected; however, there is at least one within 30 miles - you may need to zoom out on the map!";
					} else {
						$outletsarray50 = $this->map_model->get_outlets($types,$latitude,$longitude,50);
						if(count($outletsarray50) > 0) {
							$data['code'] = 50;
							$data['message'] = "There are no recycle points within 30 miles of this location which allow all of the types you have selected; however, there is at least one within 50 miles - you may need to zoom out on the map a lot, or try a smaller combination of recycle types to find less specific recycle points.";
						} else {
							$outletsarray500 = $this->map_model->get_outlets($types,$latitude,$longitude,500);
							if(count($outletsarray500) > 0) {
								$data['code'] = 500;
								$data['message'] = "There are no recycle points within 50 miles of this location which allow all of the types you have selected; however, there is at least one within 500 miles - you may need to zoom out on the map a lot! Alternatively, try de-selecting less common types to show more recycle points.";
							} else {
								$data['code'] = 0;
								$data['message'] = "There are no recycle points within 500 miles of this location which allow all of the types you have selected; Try de-selecting less common types to show more recycle points!";
							}					
						}			
					}
				}		
			}
		}	
		$this->load->view('pages/check', $data);
	}
	
	
	public function print_session() 
	{
		$data['data'] = print_r($this->session->all_userdata(),1);
		$this->load->view('pages/data', $data);
	}
	
	public function get_session() 
	{
		$data['data'] = json_encode($this->session->all_userdata());
		$this->load->view('pages/data', $data);
	}
	
	public function set_session() 
	{
		if ($this->uri->segment(2) !== FALSE) {
			$json = urldecode($this->uri->segment(2));
			$data['debug']['urlseg2'] = $json;
			$array = json_decode($json, true);
			$data['debug']['jsondecoded'] = $array;
			$this->session->set_userdata($array);
			$this->load->view('pages/debug', $data);
		}
	}
}

?>