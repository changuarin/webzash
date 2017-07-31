<?php

class Testmain extends Model {
	
	function Testmain_model()
	{
		parent::Model();
	}
	
	function gen_sysid($table, $id) {
		$query = $this->db->query('SELECT '.$id.' FROM '.$table.' ORDER BY '.$id.' DESC LIMIT 1;');
		return $query->row();
	}
	
	/*function gen_acctno($table, $val) {
		$query = $this->db->query('SELECT CI_AcctNo FROM '.$table.' WHERE CI_AcctNo="'.$val.'" LIMIT 1;');
		return $query->row();
	}*/
	
	function get_data($table, $id, $val) {
		$query = $this->db->query('SELECT * FROM '.$table.' WHERE '.$id.' = "'.$val.'";');
		return $query->row();
	}
	
}
?>