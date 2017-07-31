<?php

class Client_model extends Model
{
	function Client_model()
	{
		parent::Model();
	}
	
	function record_count($ci_name, $ci_type, $ci_status, $database='')
	{
		if($ci_name == 'EMPTY')
		{
			$ci_name = '';
		}
		
		$this->db->select('*', false);
		$this->db->from($database.'client');
		$this->db->like('CONCAT(CI_LName, \', \', CI_FName, \' \', CI_MName)', $ci_name);
		$this->db->where('CI_Type', $ci_type);
		$this->db->where('CI_Status', $ci_status);
		$this->db->where('CI_IsDeleted', 0);
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			return $query->num_rows();
		}
		return false;
	}
	
	function fetch_clients($columns, $ci_name, $ci_type, $ci_status, $limit, $start, $database='')
	{
		if($ci_name == 'EMPTY')
		{
			$ci_name = '';
		}
		
		$this->db->select($columns, false);
		$this->db->from($database.'client');
		$this->db->join($database.'client_pension', 'client_pension.CI_AcctNo = client.CI_AcctNo');
		$this->db->like('CONCAT(client.CI_LName, \', \', client.CI_FName, \' \', client.CI_MName)', $ci_name);
		$this->db->where('client.CI_Type', $ci_type);
		$this->db->where('client.CI_Status', $ci_status);
		$this->db->where('client.CI_IsDeleted', 0);
		$this->db->where('client_pension.CP_IsDeleted', 0);
		$this->db->limit($limit, $start);
		$this->db->order_by('CONCAT(client.CI_LName, \', \', client.CI_FName, \' \', client.CI_MName)', 'ASC');
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$data[] = $row;
			}
			
			return $data;
		}
		return false;
	}
	
	function fetch_client_details($columns, $ci_acctno, $database='')
	{
		$this->db->select($columns, false);
		$this->db->from($database.'client');
		$this->db->where('CI_AcctNo', $ci_acctno);
		$this->db->where('CI_IsDeleted', 0);
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			$data = $query->row();
			return $data;
		}
		return false;
	}
	
	function fetch_client_pension_details($columns, $ci_acctno, $database='')
	{
		$this->db->select($columns, false);
		$this->db->from($database.'client_pension');
		$this->db->where('CI_AcctNo', $ci_acctno);
		$this->db->where('CP_IsDeleted', 0);
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			$data = $query->row();
			return $data;
		}
		return false;
	}
	
	function fetch_pension_types()
	{
		$data = array (
			0 => array
			(
				'code' => 'GUARDIAN',
				'name' => 'Guardiann'
			),
			1 => array
			(
				'code' => 'RT',
				'name' => 'Retirement'
			),
			2 => array
			(
				'code' => 'RT/SD',
				'name' => 'Retirement/Surviving Dependent'
			),
			3 => array
			(
				'code' => 'SD',
				'name' => 'Surviving Dependent'
			),
			4 => array
			(
				'code' => 'SD/EC',
				'name' => 'Surviving Dependent/EC'
			),
			5 => array
			(
				'code' => 'ST',
				'name' => 'Total Disability'
			),
			6 => array
			(
				'code' => 'SD/EC',
				'name' => 'Surviving Dependent/EC'
			)
		);
		
		return $data;
	}
}

?>