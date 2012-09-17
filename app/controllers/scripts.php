<?php
class Scripts extends CI_Controller {

	public function view($file)
	{				
		if ( ! file_exists('app/views/scripts/'.$file.'.js.php'))
		{
			// Whoops, we don't have a page for that!
			show_404();
		}
		
		$this->load->view('scripts/'.$file.'.js, $data);
	}
}

?>