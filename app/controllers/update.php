<?php
class Update extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function recycleForScotland()
	{		
		print `echo /usr/bin/php -q /home/recycle/public_html/app/libraries/updateRecycleForScotland.php | at now`;
		
		$data['outputPath'] = '/home/recycle/public_html/tmp/updateRecycleForScotland.html';
		
		$this->load->view('templates/header', $data);
		$this->load->view('pages/update', $data);
		$this->load->view('templates/footer', $data);
	}
	
	public function getLog($path)
	{		
		echo file_get_contents(urldecode($path));
	}
	
}
?>
