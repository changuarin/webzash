<?php

class Client extends CI_Model
{
	
	function insert_data($data)
	{
		$this->db->insert('nhgt_alaminos.client', $data);
	}
	
}

?>