<?php
class Update extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{		
		$data['title'] = 'Update';
		$data['page'] = 'update';
		
		$this->load->view('templates/header', $data);
		$this->load->view('pages/update', $data);
		$this->load->view('templates/footer', $data);
	}
	
	public function showUpdateLog($source)
	{
		$data['title'] = 'showUpdateLog';
		$data['page'] = 'showUpdateLog';
		$data['url'] = "/update/getLog/$source";
		
		$this->load->view('templates/header', $data);
		$this->load->view('pages/showUpdateLog', $data);
		$this->load->view('templates/footer', $data);
	}
	
	public function getLog($source)
	{		
		$data['outlets'] = file_get_contents("/home/recycle/public_html/tmp/$source.html");
		$this->load->view('pages/data', $data);
	}
	
	public function startUpdate($source)
	{		
		$data['outlets'] = `/home/recycle/public_html/phpworker.sh start /home/recycle/public_html/app/libraries/$source.php 2>1`;
		$this->load->view('pages/data', $data);
	}

	public function stopUpdate()
	{		
		$data['outlets'] = `/home/recycle/public_html/phpworker.sh stop`;
		$this->load->view('pages/data', $data);
	}
	
}
?>
