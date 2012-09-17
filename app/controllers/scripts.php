<?php
class Scripts extends CI_Controller {

	public function view($file)
	{				
		if ( ! file_exists('app/views/scripts/'.$file.'.js.php'))
		{
			// Whoops, we don't have a page for that!
			show_404();
		}
		
		header ("Content-type: application/x-javascript");
		$this->load->view('scripts/'.$file.'.js.php');
	}
	public function vendor($file)
	{				
		if ( ! file_exists('app/views/scripts/vendor/'.$file))
		{
			// Whoops, we don't have a page for that!
			show_404();
		}
		
		header ("Content-type: application/x-javascript");
		$this->load->view('scripts/vendor/'.$file);
	}
}

?>