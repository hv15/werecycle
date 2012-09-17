<?php
class Pages extends CI_Controller {

	public function view($page = 'home')
	{				
		if ( ! file_exists('app/views/pages/'.$page.'.php'))
		{
			// Whoops, we don't have a page for that!
			show_404();
		}
		
		$data['title'] = "RecycleFinder: ".ucfirst($page); // Capitalize the first letter
		$data['page'] = $page;
		
		$this->load->view('templates/header', $data);
		$this->load->view('pages/'.$page, $data);
		$this->load->view('templates/footer', $data);

	}

	
	public function select()
	{				
		$page = 'select';
		$data['title'] = "RecycleFinder: ".ucfirst($page); // Capitalize the first letter
		$data['page'] = $page;
		
		$array = $this->uri->segment_array();
		$data['arraydata'] = $array;
		
		$this->load->view('templates/header', $data);
		$this->load->view('pages/select', $data);
		$this->load->view('templates/footer', $data);

	}

	
}

?>