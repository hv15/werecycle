<?php
class map_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}
	
	public function get_recycle_types()
	{
		$query = $this->db->get('recycle_types');
		return $query->result_array();
	}
}
?>