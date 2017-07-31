<?php

class Master_model extends Model
{
	/**
	 * Updated Master_model functions
	 */
	
	public function Master_model()
	{
		parent::Model();
	}
	
	public function get_branches()
	{
		$this->db->select('Branch_Code, Branch_Name', FALSE);
		$this->db->from('nhgt_master.branch');
		$this->db->where('Branch_IsActive', 1);
		$this->db->order_by('Branch_Name', 'ASC');
		
		$query = $this->db->get();
		
		return $query->result();
	}
	
	public function get_clients()
	{
		if($this->input->post('name') != '')
		{
			$ci_type = $this->input->post('citype');
			$ci_status = $this->input->post('cistatus');
			$ci_lname = $this->input->post('name');
			$ci_fname = '';
			
			if(strpos($this->input->post('name'), ', ') !== FALSE)
			{
				$data = explode(', ', $this->input->post('name'));
				$ci_lname = $data[0];
				$ci_fname = $data[1];
			}
			
			$this->db->select("client.CI_AcctNo, CONCAT(client.CI_LName, ', ', client.CI_FName, ' ', client.CI_MName) AS name, client_pension.CP_PensionType, client_pension.CP_BankBranch", FALSE);
			$this->db->from('client');
			$this->db->join('client_pension', 'client_pension.CI_AcctNo = client.CI_AcctNo');
			$this->db->like('client.CI_LName', $ci_lname);
			$this->db->like('client.CI_FName', $ci_fname);
			$this->db->where('client.CI_Type', $ci_type);
			$this->db->where('client.CI_Status', $ci_status);
			$this->db->where('client.CI_IsDeleted', 0);
			$this->db->where('client_pension.CP_IsDeleted', 0);
			$this->db->order_by("CONCAT(client.CI_LName, ', ', client.CI_FName, ' ', client.CI_MName)", 'ASC');
			
			$query = $this->db->get();
			
			return $query->result();
		}
	}
	
	public function get_clients2()
	{
		//Get all branch names
		$this->db->select('Branch_Name', FALSE);
		$this->db->from('nhgt_master.branch');
		$this->db->where('Branch_IsActive', 1);
		$this->db->order_by('Branch_Code', 'ASC');
		$branches = $this->db->get();
		
		$data = array();
		
		foreach($branches->result() as $branch)
		{
			$branch_name = strtolower($branch->Branch_Name);
			$database = 'nhgt_' . str_replace(' ', '', $branch_name) . '.';
			
			if($this->input->post('name') != '')
			{
				$ci_type = $this->input->post('citype');
				$ci_status = $this->input->post('cistatus');
				$ci_lname = $this->input->post('name');
				$ci_fname = '';
				
				if(strpos($this->input->post('name'), ', ') !== FALSE)
				{
					$data2 = explode(', ', $this->input->post('name'));
					$ci_lname = $data2[0];
					$ci_fname = $data2[1];
				}
				
				$this->db->select("client.CI_AcctNo, CONCAT(client.CI_LName, ', ', client.CI_FName, ' ', client.CI_MName) AS name, client_pension.CP_PensionType, client_pension.CP_BankBranch", FALSE);
				$this->db->from($database . 'client');
				$this->db->join($database . 'client_pension', 'client_pension.CI_AcctNo = client.CI_AcctNo');
				$this->db->like('client.CI_LName', $ci_lname);
				$this->db->like('client.CI_FName', $ci_fname);
				$this->db->where('client.CI_Type', $ci_type);
				$this->db->where('client.CI_Status', $ci_status);
				$this->db->where('client.CI_IsDeleted', 0);
				$this->db->where('client_pension.CP_IsDeleted', 0);
				$this->db->order_by("CONCAT(client.CI_LName, ', ', client.CI_FName, ' ', client.CI_MName)", 'ASC');
				
				$query = $this->db->get();
				
				if($query->num_rows > 0)
				{
					foreach($query->result() as $row)
					{
						$data[] = array(
								'CI_AcctNo'			 => $row->CI_AcctNo,
								'name'					 => $row->name,
								'CP_PensionType' => $row->CP_PensionType,
								'CP_BankBranch'	 => $row->CP_BankBranch,
								'database'			 => $database
							);
					}
				}
			}
		}
		
		return $data;
	}
	
	public function get_client($ci_acctno, $database = '')
	{
		$this->db->where('CI_AcctNo', $ci_acctno);
		$this->db->where('CI_IsDeleted', 0);
		
		$query = $this->db->get($database . 'client');
		
		return $query->row();
	}
	
	public function get_client_pension($ci_acctno, $database = '')
	{
		$this->db->where('CI_AcctNo', $ci_acctno);
		$this->db->where('CP_IsDeleted', 0);
		
		$query = $this->db->get($database . 'client_pension');
		
		return $query->row();
	}
	
	public function get_dependents($ci_acctno, $database = '')
	{
		$this->db->select("SysID, CONCAT(CD_LName, ', ', CD_FName) AS name", FALSE);
		$this->db->from($database . 'client_dependents');
		$this->db->where('CI_AcctNo', $ci_acctno);
		$this->db->where('CD_IsDeleted', 0);
		$this->db->order_by("CONCAT(CD_LName, ', ', CD_FName)", 'ASC');
		
		$query = $this->db->get();
		
		return $query->result();
	}
	
	public function get_loans($ci_acctno, $database = '')
	{
		$this->db->select('CI_AcctNo, LH_PN, LH_MonthlyAmort, LH_Principal, LH_Balance, LH_LoanTrans, LH_Terms, LH_LoanDate, LH_StartDate, LH_EndDate, LH_IsTop', FALSE);
		$this->db->from($database . 'ln_hdr');
		$this->db->where('CI_AcctNo', $ci_acctno);
		$this->db->not_like('LH_LoanTrans', 'SPEC');
		$this->db->where('LH_IsPending', 0);
		$this->db->where('LH_Processed', 1);
		$this->db->where('LH_Cancelled', 0);
		$this->db->order_by('LH_LoanDate', 'DESC');
		
		$query = $this->db->get();
		
		return $query->result();
	}
	
	public function get_agents()
	{
		if($this->input->post('name') != '')
		{
			$ai_lname = $this->input->post('name');
			$ai_fname = '';
			
			if(strpos($this->input->post('name'), ', ') !== FALSE)
			{
				$data = explode(', ', $this->input->post('name'));
				$ai_lname = $data[0];
				$ai_fname = $data[1];
			}
			
			$this->db->select("AI_RefNo, CONCAT(AI_LName, ', ', AI_FName, ' ', AI_MName) AS name", FALSE);
			$this->db->from('agent');
			$this->db->like('AI_LName', $ai_lname);
			$this->db->like('AI_FName', $ai_fname);
			$this->db->where('AI_IsDeleted', 0);
			$this->db->order_by("CONCAT(AI_LName, ', ', AI_FName, ' ', AI_MName)", 'ASC');
			
			$query = $this->db->get();
			
			return $query->result();
		}
	}
	
	public function get_agents2()
	{
		//Get all branch names
		$this->db->select('Branch_Name', FALSE);
		$this->db->from('nhgt_master.branch');
		$this->db->where('Branch_IsActive', 1);
		$this->db->order_by('Branch_Code', 'ASC');
		
		$branches = $this->db->get();
		
		$data = array();
		
		foreach($branches->result() as $branch)
		{
			$branch_name = strtolower($branch->Branch_Name);
			$database = 'nhgt_' . str_replace(' ', '', $branch_name) . '.';
			
			if($this->input->post('name') != '')
			{
				$ai_lname = $this->input->post('name');
				$ai_fname = '';
				
				if(strpos($this->input->post('name'), ', ') !== FALSE)
				{
					$data2 = explode(', ', $this->input->post('name'));
					$ai_lname = $data2[0];
					$ai_fname = $data2[1];
				}
				
				$this->db->select("AI_RefNo, CONCAT(AI_LName, ', ', AI_FName, ' ', AI_MName) AS name", FALSE);
				$this->db->from($database . 'agent');
				$this->db->like('AI_LName', $ai_lname);
				$this->db->like('AI_FName', $ai_fname);
				$this->db->where('AI_IsDeleted', 0);
				$this->db->order_by("CONCAT(AI_LName, ', ', AI_FName, ' ', AI_MName)", 'ASC');
				
				$query = $this->db->get();
				
				if($query->num_rows > 0)
				{
					foreach($query->result() as $row)
					{
						$data[] = array(
								'AI_RefNo' => $row->AI_RefNo,
								'name'		 => $row->name,
								'database' => $database
							);
					}
				}
			}
		}
		
		return $data;
	}
	
	public function get_agent($ai_refno, $database = '')
	{
		$this->db->where('AI_RefNo', $ai_refno);
		$this->db->where('AI_IsDeleted', 0);
		
		$query = $this->db->get($database . 'agent');
		
		return $query->row();
	}
	
	public function get_agent2($ai_refno)
	{
		$this->db->where('AI_RefNo', $ai_refno);
		$this->db->where('AI_IsDeleted', 0);
		
		$query = $this->db->get('agent');
		
		return $query->row();
	}
	
	public function get_commission()
	{
		
	}
	
	public function add_agent()
	{
		$ai_branchcode = $this->input->post('aibranchcode');
		
		$this->db->select('AI_RefNo', FALSE);
		$this->db->from('agent');
		$this->db->where('AI_IsDeleted', 0);
		$this->db->where('AI_BranchCode', $ai_branchcode);
		$this->db->order_by('SysID', 'DESC');
		$this->db->limit(1);
		
		$query = $this->db->get();
		
		if($query->num_rows() == 0)
		{
			$ai_refno = $ai_branchcode . '-0001';
		}
		else
		{
			$data = explode('-', $query->row()->AI_RefNo);
			$i = (int)$data[1] + 1;
			$count = str_pad($i, 4, '0', STR_PAD_LEFT);
			
			$ai_refno = $ai_branchcode . '-' . $count;
		}
		
		$agent_array = array(
				'AI_BranchCode'	 => $this->input->post('aibranchcode'),
				'AI_RefNo'			 => $ai_refno,
				'AI_FName'			 => $this->input->post('aifname'),
				'AI_MName'			 => $this->input->post('aimname'),
				'AI_LName'			 => $this->input->post('ailname'),
				'AI_Bdate'			 => date('Y-m-d', strtotime($this->input->post('aibdate'))),
				'AI_Sex'				 => $this->input->post('aisex'),
				'AI_CivilStatus' => $this->input->post('aicivilstatus'),
				'AI_TelNo'			 => $this->input->post('aitelno'),
				'AI_MobileNo'		 => $this->input->post('aimobileno'),
				'AI_Add1'				 => $this->input->post('aiadd1'),
				'AI_Add2'				 => $this->input->post('aiadd2'),
				'AI_IsAdded'		 => 1,
				'AI_AddedBy'		 => $this->session->userdata('user_name'),
				'AI_AddedDate'	 => date('Y-m-d H:i:s')
			);
		
		$query = $this->db->insert('agent', $agent_array);
		
		return $query;
	}
	
	
	public function update_agent()
	{
		$ai_refno = $this->input->post('airefno');
		$ai_refno2 = $this->input->post('airefno2');
		
		$this->db->trans_start();
		
		$agent_array = array(
				'AI_BranchCode'			=> $this->input->post('aibranchcode'),
				'AI_Refno'					=> $ai_refno2,
				'AI_FName'					=> $this->input->post('aifname'),
				'AI_MName'					=> $this->input->post('aimname'),
				'AI_LName'					=> $this->input->post('ailname'),
				'AI_Bdate'					=> date('Y-m-d', strtotime($this->input->post('aibdate'))),
				'AI_Sex'						=> $this->input->post('aisex'),
				'AI_CivilStatus'		=> $this->input->post('aicivilstatus'),
				'AI_TelNo'					=> $this->input->post('aitelno'),
				'AI_MobileNo'				=> $this->input->post('aimobileno'),
				'AI_Add1'						=> $this->input->post('aiadd1'),
				'AI_Add2'						=> $this->input->post('aiadd2'),
				'AI_IsModified'			=> 1,
				'AI_IsModifiedBy'		=> $this->session->userdata('user_name'),
				'AI_IsModifiedDate' => date('Y-m-d H:i:s')
			);
		
		$this->db->where('AI_RefNo', $ai_refno);
		$this->db->update('agent', $agent_array) or die($this->db->_error_message());
		
		if($ai_refno != $ai_refno2)
		{
			//Update Agent Ref No of Loans
			$loan_array = array(
					'LH_Agent1' => $ai_refno2
				);
			
			$this->db->where('LH_Agent1', $ai_refno);
			$this->db->update($database . 'ln_hdr', $loan_array);
			
			//Update Sub-Agent Ref No of Loans
			$loan_array = array(
					'LH_Agent2' => $ai_refno2
				);
			
			$this->db->where('LH_Agent2', $ai_refno);
			$this->db->update($database . 'ln_hdr', $loan_array);
			
			//Update Agent Ref No of Clients
			$client_array = array(
					'CI_Agent1' => $ai_refno2
				);
			
			$this->db->where('CI_Agent1', $ai_refno);
			$query = $this->db->update($database . 'client', $client_array);
			
			//Update Sub-Agent Ref No of Clients
			$client_array = array(
					'CI_Agent2' => $ai_refno2
				);
			
			$this->db->where('CI_Agent2', $ai_refno);
			$this->db->update($database . 'client', $client_array);
		}
		
		$query = $this->db->trans_complete();
		
		return $query;
	}
	
	public function delete_agent()
	{
		$agent_array= array(
				'AI_IsDeleted'		 => 1,
				'AI_IsDeletedBy'	 => $this->session->userdata('user_name'),
				'AI_IsDeletedDate' => date('Y-m-d H:i:s')
			);
		
		$this->db->where('AI_RefNo', $this->input->post('airefno'));
		$query = $this->db->update('agent', $agent_array);
		
		return $query;
	}
	
	public function get_client_types()
	{
		$data = array(
				0 => array(
						'code' => '',
						'name' => '-SELECT-'
					),
				1 => array(
						'code' => 'PEN',
						'name' => 'Client-Pension'
					),
				2 => array(
						'code' => 'SAL',
						'name' => 'Client-Salary'
					),
				3 => array(
						'code' => 'SPC',
						'name' => 'Client-Others'
					),
				4 => array(
						'code' => 'EMP',
						'name' => 'Accom-Employee'
					),
				5 => array(
						'code' => 'AGT',
						'name' => 'Accom-Agent'
					)
			);
		
		return $data;
	}
	
	public function get_payment_sources()
	{
		$data = array(
				0 => array(
						'code' => '',
						'name' => '-SELECT-'
					),
				1 => array(
						'code' => 'SSS',
						'name' => 'SSS'
					),
				2 => array(
						'code' => 'GSIS',
						'name' => 'GSIS'
					),
				3 => array(
						'code' => 'PVAO',
						'name' => 'PVAO'
					),
				4 => array(
						'code' => 'OTHERS',
						'name' => 'Others'
					)
			);
		
		return $data;
	}
	
	public function get_pension_types()
	{
		$data = array(
			0 => array
			(
				'code' => 'GUARDIAN',
				'name' => 'Guardian'
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
				'code' => 'SP',
				'name' => 'Partial Disability'
			),
			6 => array
			(
				'code' => 'ST',
				'name' => 'Total Disability'
			)
		);
		
		return $data;
	}
	
	public function get_payment_types()
	{
		$data = array(
				0 => array(
						'code' => '',
						'name' => '-SELECT-'
					),
				1 => array(
						'code' => 'ATM',
						'name' => 'ATM'
					),
				2 => array(
						'code' => 'PB',
						'name' => 'PB'
					),
				3 => array(
						'code' => 'CASH',
						'name' => 'CASH'
					),
				4 => array(
						'code' => 'CHECK',
						'name' => 'CHECK'
					)
			);
		
		return $data;
	}
		
	/***/
	
	function record_count($ci_lname, $ci_type, $ci_status, $database = '')
	{
		$this->db->select('*', false);
		$this->db->from($database . '.client');
		$this->db->like('CI_LName', $ci_lname);
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
	
	function fetch_clients($columns, $ci_lname, $ci_type, $ci_status, $database = '')
	{
		$this->db->select($columns, false);
		$this->db->from($database . '.client');
		$this->db->join($database . '.client_pension', 'client_pension.CI_AcctNo = client.CI_AcctNo');
		$this->db->like('client.CI_LName', $ci_lname);
		$this->db->where('client.CI_Type', $ci_type);
		$this->db->where('client.CI_Status', $ci_status);
		$this->db->where('client.CI_IsDeleted', 0);
		$this->db->where('client_pension.CP_IsDeleted', 0);
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
	
	function fetch_client_details($columns, $ci_acctno, $database = '')
	{
		$this->db->select($columns, false);
		$this->db->from($database . '.client');
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
	
	function fetch_client_pension_details($columns, $ci_acctno, $database = '')
	{
		$this->db->select($columns, false);
		$this->db->from($database . '.client_pension');
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
	
	function fetch_agents($columns, $ai_lname, $database = '')
	{
		$this->db->select($columns, false);
		$this->db->from($database . '.agent');
		$this->db->like('AI_LName', $ai_lname);
		$this->db->where('AI_IsDeleted', 0);
		$this->db->order_by('CONCAT(AI_LName, \', \', AI_FName, \' \', AI_MName)', 'ASC');
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
	
	function fetch_agent_details($columns, $ai_refno, $database = '')
	{
		$this->db->select($columns, false);
		$this->db->from($database . '.agent');
		$this->db->where('AI_RefNo', $ai_refno);
		$this->db->where('AI_IsDeleted', 0);
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			$data = $query->row();
			return $data;
		}
		return false;
	}
	
	function fetch_comakers($columns, $cm_lname, $database = '')
	{
		$this->db->select($columns, false);
		$this->db->from($database . '.comaker');
		$this->db->like('CM_LName', $cm_lname);
		$this->db->where('CM_IsDeleted', 0);
		$this->db->order_by('CONCAT(CM_LName, \', \', CM_FName, \' \', CM_MName)', 'ASC');
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
	
	function fetch_comaker_details($columns, $cm_refno, $database = '')
	{
		$this->db->select($columns, false);
		$this->db->from($database . '.comaker');
		$this->db->where('CM_RefNo', $cm_refno);
		$this->db->where('CM_IsDeleted', 0);
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			$data = $query->row();
			return $data;
		}
		return false;
	}
	
	function fetch_dependents($columns, $ci_acctno, $database = '')
	{
		$this->db->select($columns, false);
		$this->db->from($database . '.client_dependents');
		$this->db->where('CI_AcctNo', $ci_acctno);
		$this->db->where('CD_IsDeleted', 0);
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
	
	function fetch_loans($columns, $ci_acctno, $database = '')
	{
		$this->db->select($columns, false);
		$this->db->from($database . '.ln_hdr');
		$this->db->where('CI_AcctNo', $ci_acctno);
		$this->db->where('LH_LoanTrans !=', 'SPEC');
		$this->db->where('LH_LoanTrans !=', 'SPEC2');
		$this->db->where('LH_IsPending', 0);
		$this->db->where('LH_Processed', 1);
		$this->db->where('LH_Cancelled', 0);
		$this->db->order_by('LH_LoanDate', 'DESC');
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
	
	function fetch_branches()
	{
		$this->db->select('Branch_Code as code, Branch_Name as name', false);
		$this->db->from('nhgt_master.branch');
		$this->db->where('Branch_IsActive', 1);
		$this->db->order_by('Branch_Name', 'ASC');
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
	
	function fetch_months()
	{
		$data = array(
			0 => array
			(
				'id' => 1,
				'name' => 'January'
			),
			1 => array
			(
				'id' => 2,
				'name' => 'February'
			),
			2 => array
			(
				'id' => 3,
				'name' => 'March'
			),
			3 => array
			(
				'id' => 4,
				'name' => 'April'
			),
			4 => array
			(
				'id' => 5,
				'name' => 'May'
			),
			5 => array
			(
				'id' => 6,
				'name' => 'June'
			),
			6 => array
			(
				'id' => 7,
				'name' => 'July'
			),
			7 => array
			(
				'id' => 8,
				'name' => 'August'
			),
			8 => array
			(
				'id' => 9,
				'name' => 'September'
			),
			9 => array
			(
				'id' => 10,
				'name' => 'October'
			),
			10 => array
			(
				'id' => 11,
				'name' => 'November'
			),
			11 => array
			(
				'id' => 2,
				'name' => 'December'
			)
		);
		return $data;
	}
	
	function fetch_pension_types()
	{
		$data = array (
			0 => array
			(
				'code' => 'GUARDIAN',
				'name' => 'Guardiann'
			),
			1 => array (
				'code' => 'EC',
				'name' => 'Employee Compensation'
			),
			2 => array
			(
				'code' => 'RT',
				'name' => 'Retirement'
			),
			3 => array
			(
				'code' => 'RT/SD',
				'name' => 'Retirement/Surviving Dependent'
			),
			4 => array
			(
				'code' => 'SD',
				'name' => 'Surviving Dependent'
			),
			5 => array
			(
				'code' => 'SD/EC',
				'name' => 'Surviving Dependent/Employee Compensation'
			),
			6 => array
			(
				'code' => 'SP',
				'name' => 'Partial Disability'
			),
			7 => array
			(
				'code' => 'ST',
				'name' => 'Total Disability'
			),
		);
		return $data;
	}
	
	function fetch_branch_code($database = '')
	{
		$this->db->select('value', false);
		$this->db->from($database . '.parameter');
		$this->db->where('code', 'BRANCH');
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			$data = $query->row();
			return $data;
		}
		return false;
	}
	
	function generate_id($column, $table, $database = '')
	{
		$this->db->select($column . ' as id', false);
		$this->db->from($database . '.' . $table);
		$this->db->order_by('id', 'DESC');
		$this->db->limit(1);
		$query = $this->db->get();
		
		if($query->num_rows() == 0)
		{
			$data = 1;
		} else {
			$result = $query->row();
			$data = $result->id + 1;
		}
		return $data;
	}
	
	function generate_ci_acctno($database = '')
	{
		$result = $this->fetch_branch_code();
		$result = explode(';', $result->value);
		$branch_code = $result[0];
		$date = date('Y-m-d');
		
		$this->db->select('CI_AcctNo', false);
		$this->db->from($database . '.client');
		$this->db->where('CI_IsDeleted', 0);
		$this->db->like('CI_AcctNo', $branch_code . '-' . $date);
		$this->db->order_by('CI_AcctNo', 'DESC');
		$this->db->limit(1);
		$query = $this->db->get();
		
		if($query->num_rows() == 0)
		{
			$data = $branch_code . '-' . $date . '-001';
		} else {
			$result = $query->row();
			$ci_acctno = explode('-', $result->CI_AcctNo);
			$i = substr($ci_acctno[4], 0, 3);
			$j = (int)$i + 1;
			$count = str_pad($j, 3, '0', STR_PAD_LEFT);
			$data = $branch_code . '-' . $date . '-' . $count;
		}
		return $data;
	}
	
	function generate_ai_refno($database = '')
	{
		$result = $this->fetch_branch_code();
		$result = explode(';', $result->value);
		$branch_code = $result[0];
		
		$this->db->select('AI_RefNo', false);
		$this->db->from($database . '.agent');
		$this->db->where('AI_IsDeleted', 0);
		$this->db->like('AI_RefNo', $branch_code);
		$this->db->order_by('AI_RefNo', 'DESC');
		$this->db->limit(1);
		$query = $this->db->get();
		
		if($query->num_rows() == 0)
		{
			$data = $branch_code . '-' . '0001';
		} else {
			$result = $query->row();
			$ai_refno = explode('-', $result->AI_RefNo);
			$i = substr($ai_refno[1], 0, 4);
			$j = (int)$i + 1;
			$count = str_pad($j, 4, '0', STR_PAD_LEFT);
			$data = $branch_code . '-' . $count;
		}
		return $data;
	}
	
	function generate_cm_refno($database = '')
	{
		$result = $this->fetch_branch_code();
		$result = explode(';', $result->value);
		$branch_code = $result[0];
		
		$this->db->select('CM_RefNo', false);
		$this->db->from($database . '.comaker');
		$this->db->where('CM_IsDeleted', 0);
		$this->db->like('CM_RefNo', $branch_code);
		$this->db->order_by('CM_RefNo', 'DESC');
		$this->db->limit(1);
		$query = $this->db->get();
		
		if($query->num_rows() == 0)
		{
			$data = $branch_code . '-' . 'CM0001';
		} else {
			$result = $query->row();
			$cm_refno = explode('-', $result->CM_RefNo);
			$i = substr($cm_refno[1], 2, 4);
			$j = (int)$i + 1;
			$count = str_pad($j, 4, '0', STR_PAD_LEFT);
			$data = $branch_code . '-CM' . $count;
		}
		
		if($this->db->database == 'nhgt_balagtas')
		{
			$this->db->select('CM_RefNo', false);
			$this->db->from($database . '.comaker');
			$this->db->where('CM_IsDeleted', 0);
			$this->db->where('CM_RefNo <=', 'BLB-CM9640');
			$this->db->like('CM_RefNo', $branch_code);
			$this->db->order_by('CM_RefNo', 'DESC');
			$this->db->limit(1);
			$query = $this->db->get();
			
			if($query->num_rows() == 0)
			{
				$data = $branch_code . '-' . 'CM0001';
			} else {
				$result = $query->row();
				$cm_refno = explode('-', $result->CM_RefNo);
				$i = substr($cm_refno[1], 2, 4);
				$j = (int)$i + 1;
				$count = str_pad($j, 4, '0', STR_PAD_LEFT);
				$data = $branch_code . '-CM' . $count;
			}
		}
		
		return $data;
	}
	
	function insert_client()
	{
		$acctno = $this->generate_id('AcctNo', 'client');
		$ci_branchcode = $this->test_input($this->input->post('ci_branchcode'));
		$ci_type = $this->test_input($this->input->post('ci_type'));
		$ci_status = $this->test_input($this->input->post('ci_status'));
		$ci_grp = $this->test_input($this->input->post('ci_grp'));
		$ci_acctno = $this->generate_ci_acctno();
		$ci_fname = $this->test_input($this->input->post('ci_fname'));
		$ci_mname = $this->input->post('ci_mname') == '' ? '' : $this->test_input($this->input->post('ci_mname'));
		$ci_lname = $this->test_input($this->input->post('ci_lname'));
		$ci_bdate = $this->test_input($this->input->post('ci_bdate'));
		$ci_sex = $this->input->post('ci_sex') == '' ? 'M' : $this->test_input($this->input->post('ci_sex'));
		$ci_civilstatus = $this->input->post('ci_civilstatus') == '' ? 'S' : $this->test_input($this->input->post('ci_civilstatus'));
		$ci_add1 = $this->test_input($this->input->post('ci_add1'));
		$ci_add2 = $this->input->post('ci_add2') == '' ? '' : $this->test_input($this->input->post('ci_add2'));
		$ci_telno = $this->input->post('ci_telno') == '' ? '' : $this->test_input($this->input->post('ci_telno'));
		$ci_mobileno = $this->input->post('ci_mobileno') == '' ? '' : $this->test_input($this->input->post('ci_mobileno'));
		$ci_sssno = $this->test_input($this->input->post('ci_sssno'));
		$ci_cedulano = $this->test_input($this->input->post('ci_cedulano'));
		$ci_cedulaplace = $this->input->post('ci_cedulaplace') == '' ? '' : $this->test_input($this->input->post('ci_cedulaplace'));
		$ci_ceduladate = $this->input->post('ci_ceduladate') == '' ? date('Y-m-d') : $this->test_input($this->input->post('ci_ceduladate'));
		$ci_comaker = $this->input->post('ci_comaker') == '' ? $this->input->post('ci_comaker') : $this->test_input($this->input->post('ci_comaker'));
		$ci_agent1 = $this->input->post('ci_agent1') == '' ? '' : $this->test_input($this->input->post('ci_agent1'));
		$ci_agent2 = $this->input->post('ci_agent2') == '' ? '' : $this->test_input($this->input->post('ci_agent2'));
		$ci_agent1_rate = $this->input->post('ci_agent1_rate') == '' ? 0 : $this->test_input($this->input->post('ci_agent1_rate'));
		$ci_agent2_rate = $this->input->post('ci_agent2_rate') == '' ? 0 : $this->test_input($this->input->post('ci_agent2_rate'));
		$ci_source = $this->test_input($this->input->post('ci_source'));
		$ci_picture = $this->input->post('ci_picture') == '' ? null : $this->input->post('ci_picture');
		$ci_problemacct = '';
		$ci_problemacct .= $this->input->post('ci_problemacct') == '' ? '' : $this->input->post('ci_problemacct');
		$ci_problemacct .= $this->input->post('arrears') == '' ? '' : $this->input->post('arrears');
		$ci_remarks = $this->input->post('ci_remarks') == '' ? '' : $this->test_input($this->input->post('ci_remarks'));
		
		$cp_id = $this->generate_id('CP_ID', 'client_pension');
		$cp_itf = $this->input->post('cp_itf') == '' ? '' : $this->test_input($this->input->post('cp_itf'));
		$cp_pensiontype = $this->test_input($this->input->post('cp_pensiontype'));
		$cp_adno = $this->test_input($this->input->post('cp_adno'));
		$cp_bankacctno = $this->test_input($this->input->post('cp_bankacctno'));
		$cp_bankbranch = $this->test_input($this->input->post('cp_bankbranch'));
		$cp_amount = $this->test_input($this->input->post('cp_amount'));
		$cp_withdrawalday = $this->test_input($this->input->post('cp_withdrawalday'));
		$cp_ptype = $this->test_input($this->input->post('cp_ptype'));
		$cp_causeofdeath = $this->input->post('cp_causeofdeath') == '' ? '' : $this->test_input($this->input->post('cp_causeofdeath'));
		$cp_dateofdeath = $this->input->post('cp_causeofdeath') == '' || $this->input->post('cp_deathofdeath') == '1970-01-01' ? null : $this->test_input($this->input->post('cp_dateofdeath'));
		$cp_disability = $this->input->post('cp_disability') == '' ? '' : $this->test_input($this->input->post('cp_disability'));
		
		$this->db->trans_start();
		
		$ci_array = array(
			'AcctNo' => $acctno,
			'CI_BranchCode' => $ci_branchcode,
			'CI_Type' => $ci_type,
			'CI_Status' => $ci_status,
			'CI_Grp' => $ci_grp,
			'CI_CreditMemo' => 0,
			'CI_AcctNo' => $ci_acctno,
			'CI_FName' => $ci_fname, 
			'CI_MName' => $ci_mname,
			'CI_LName' => $ci_lname,
			'CI_BDate' => date('Y-m-d', strtotime($ci_bdate)),
			'CI_Sex' => $ci_sex,
			'CI_CivilStatus' => $ci_civilstatus,
			'CI_Add1' => $ci_add1,
			'CI_Add2' => $ci_add2,
			'CI_TelNo' => $ci_telno,
			'CI_MobileNo' => $ci_mobileno,
			'CI_SSSNo' => $ci_sssno,
			'CI_BankAcctNo' => $cp_bankacctno,
			'CI_BankBranch' => $cp_bankbranch,
			'CI_BankAcctNo_Released' => 0,
			'CI_CedulaNo' => $ci_cedulano,
			'CI_CedulaPlace' => $ci_cedulaplace,
			'CI_CedulaDate' => date('Y-m-d', strtotime($ci_ceduladate)),
			'CI_CoMaker' => $ci_comaker,
			'CI_Agent1' => $ci_agent1,
			'CI_Agent2' => $ci_agent2,
			'CI_Agent1_Rate' => $ci_agent1_rate,
			'CI_Agent2_Rate' => $ci_agent2_rate,
			'CI_Source' => $ci_source,
			'CI_PType' => $cp_ptype,
			'CI_NoOfLoan' => 0,
			'CI_Picture' => $ci_picture,
			'CI_AllowSpecialLoan' => 0,
			'CI_ProblemAcct' => $ci_problemacct,
			'CI_Remarks' => $ci_remarks,
			'CI_Gross_Principal' => 0,
			'CI_Gross_Net' => 0,
			'CI_Gross_Interest' => 0,
			'CI_Gross_Payment' => 0,
			'CI_Gross_Refund' => 0,
			'CI_IsLocked' => 0,
			'CI_IsAdded' => 1,
			'CI_AddedBy' => $this->session->userdata('user_name'),
			'CI_AddedDate' => date('Y-m-d'),
			'CI_IsDeleted' => 0,
			'CI_IsModified' => 0
		);
		$this->db->insert('client', $ci_array) or die('Error: insert_client function. Please contact your System Administrator.');
		
		
		$cp_array = array(
			'CP_ID' => $cp_id,
			'CI_AcctNo' => $ci_acctno,
			'CP_PensionType' => $cp_pensiontype,
			'CP_PType' => $cp_ptype,
			'CP_ADNo' => $cp_adno,
			'CP_ITF' => $cp_itf,
			'CP_BankAcctNo_Ref' => $cp_bankacctno,
			'CP_BankAcctNo' => $cp_bankacctno,
			'CP_BankBranch' => $cp_bankbranch,
			'CP_BankAcctNo_Released' => 0,
			'CP_Amount' => str_replace(',', '', $cp_amount),
			'CP_WithdrawalDay' => $cp_withdrawalday,
			'CP_CauseOfDeath' => $cp_causeofdeath,
			'CP_DateOfDeath' => $cp_dateofdeath,
			'CP_Disability' => $cp_disability,
			'CP_IsAdded' => 1,
			'CP_AddedBy' => $this->session->userdata('user_name'),
			'CP_AddedDate' => date('Y-m-d'),
			'CP_IsDeleted' => 0,
			'CP_IsModified' => 0
		);
		$this->db->insert('client_pension', $cp_array) or die('Error: insert client function. Please contact your System Administrator.');
		
		$this->db->trans_complete();
		
		$data = $ci_lname . ', ' . $ci_fname . ' ' . $ci_mname . ' has been added to client database.';
		return $data;
	}
	
	function update_client()
	{
		$ci_acctno = $this->test_input($this->input->post('ci_acctno'));
		$acctno = $this->generate_id('AcctNo', 'client');
		$ci_branchcode = $this->test_input($this->input->post('ci_branchcode'));
		$ci_type = $this->test_input($this->input->post('ci_type'));
		$ci_status = $this->test_input($this->input->post('ci_status'));
		$ci_grp = $this->test_input($this->input->post('ci_grp'));
		$ci_fname = $this->test_input($this->input->post('ci_fname'));
		$ci_mname = $this->input->post('ci_mname') == '' ? '' : $this->test_input($this->input->post('ci_mname'));
		$ci_lname = $this->test_input($this->input->post('ci_lname'));
		$ci_bdate = $this->test_input($this->input->post('ci_bdate'));
		$ci_sex = $this->input->post('ci_sex') == '' ? 'M' : $this->test_input($this->input->post('ci_sex'));
		$ci_civilstatus = $this->input->post('ci_civilstatus') == '' ? 'S' : $this->test_input($this->input->post('ci_civilstatus'));
		$ci_add1 = $this->test_input($this->input->post('ci_add1'));
		$ci_add2 = $this->input->post('ci_add2') == '' ? '' : $this->test_input($this->input->post('ci_add2'));
		$ci_telno = $this->input->post('ci_telno') == '' ? '' : $this->test_input($this->input->post('ci_telno'));
		$ci_mobileno = $this->input->post('ci_mobileno') == '' ? '' : $this->test_input($this->input->post('ci_mobileno'));
		$ci_sssno = $this->test_input($this->input->post('ci_sssno'));
		$ci_cedulano = $this->test_input($this->input->post('ci_cedulano'));
		$ci_cedulaplace = $this->input->post('ci_cedulaplace') == '' ? '' : $this->test_input($this->input->post('ci_cedulaplace'));
		$ci_ceduladate = $this->input->post('ci_ceduladate') == '' ? date('Y-m-d') : $this->test_input($this->input->post('ci_ceduladate'));
		$ci_comaker = $this->input->post('ci_comaker') == '' ? '' : $this->test_input($this->input->post('ci_comaker'));
		$ci_agent1 = $this->input->post('ci_agent1') == '' ? '' : $this->test_input($this->input->post('ci_agent1'));
		$ci_agent2 = $this->input->post('ci_agent2') == '' ? '' : $this->test_input($this->input->post('ci_agent2'));
		$ci_agent1_rate = $this->input->post('ci_agent1_rate') == '' ? 0 : $this->test_input($this->input->post('ci_agent1_rate'));
		$ci_agent2_rate = $this->input->post('ci_agent2_rate') == '' ? 0 : $this->test_input($this->input->post('ci_agent2_rate'));
		$ci_source = $this->test_input($this->input->post('ci_source'));
		$ci_picture = $this->input->post('ci_picture') == '' ? null : $this->input->post('ci_picture');
		$ci_problemacct = '';
		$ci_problemacct .= $this->input->post('ci_problemacct') == '' ? '' : $this->input->post('ci_problemacct');
		$ci_problemacct .= $this->input->post('arrears') == '' ? '' : $this->input->post('arrears');
		$ci_remarks = $this->input->post('ci_remarks') == '' ? '' : $this->test_input($this->input->post('ci_remarks'));
		
		$cp_id = $this->generate_id('CP_ID', 'client_pension');
		
		$cp_itf = $this->input->post('cp_itf') == '' ? '' : $this->test_input($this->input->post('cp_itf'));
		$cp_pensiontype = $this->test_input($this->input->post('cp_pensiontype'));
		$cp_adno = $this->test_input($this->input->post('cp_adno'));
		$cp_bankacctno = $this->test_input($this->input->post('cp_bankacctno'));
		$cp_bankbranch = $this->test_input($this->input->post('cp_bankbranch'));
		$cp_amount = $this->test_input($this->input->post('cp_amount'));
		$cp_withdrawalday = $this->test_input($this->input->post('cp_withdrawalday'));
		$cp_ptype = $this->test_input($this->input->post('cp_ptype'));
		$cp_causeofdeath = $this->input->post('cp_causeofdeath') == '' ? '' : $this->test_input($this->input->post('cp_causeofdeath'));
		$cp_dateofdeath = $this->input->post('cp_causeofdeath') == '' || $this->input->post('cp_deathofdeath') == '1970-01-01' ? null : $this->test_input($this->input->post('cp_dateofdeath'));
		$cp_disability = $this->input->post('cp_disability') == '' ? '' : $this->test_input($this->input->post('cp_disability'));
		
		$lh_isreleased = $ci_status == 'A' ? 0 : 1;
		
		$ci_name = $ci_lname . ', ' . $ci_fname . ' ' . $ci_mname;
		
		$this->db->trans_start();
		
		/* Update record in client database */
		$client_array = array(
			'CI_BranchCode' => $ci_branchcode,
			'CI_Type' => $ci_type,
			'CI_Status' => $ci_status,
			'CI_Grp' => $ci_grp,
			'CI_FName' => $ci_fname, 
			'CI_MName' => $ci_mname,
			'CI_LName' => $ci_lname,
			'CI_BDate' => date('Y-m-d', strtotime($ci_bdate)),
			'CI_Sex' => $ci_sex,
			'CI_CivilStatus' => $ci_civilstatus,
			'CI_Add1' => $ci_add1,
			'CI_Add2' => $ci_add2,
			'CI_TelNo' => $ci_telno,
			'CI_MobileNo' => $ci_mobileno,
			'CI_SSSNo' => $ci_sssno,
			'CI_BankAcctNo' => $cp_bankacctno,
			'CI_BankBranch' => $cp_bankbranch,
			'CI_CedulaNo' => $ci_cedulano,
			'CI_CedulaPlace' => $ci_cedulaplace,
			'CI_CedulaDate' => date('Y-m-d', strtotime($ci_ceduladate)),
			'CI_CoMaker' => $ci_comaker,
			'CI_Agent1' => $ci_agent1,
			'CI_Agent2' => $ci_agent2,
			'CI_Agent1_Rate' => $ci_agent1_rate,
			'CI_Agent2_Rate' => $ci_agent2_rate,
			'CI_Source' => $ci_source,
			'CI_PType' => $cp_ptype,
			'CI_Picture' => $ci_picture,
			'CI_ProblemAcct' => $ci_problemacct,
			'CI_Remarks' => $ci_remarks,
			'CI_IsModified' => 1,
			'CI_IsModifiedBy' => $this->session->userdata('user_name'),
			'CI_IsModifiedDate' => date('Y-m-d')
		);
		$this->db->where('CI_AcctNo', $ci_acctno);
		$this->db->update('client', $client_array) or die('Error: update_client function. Please contact your System Administrator.');
		
		/* Update record in client_pension database */
		$client_pen_array = array(
			'CP_PensionType' => $cp_pensiontype,
			'CP_PType' => $cp_ptype,
			'CP_ADNo' => $cp_adno,
			'CP_ITF' => $cp_itf,
			'CP_BankAcctNo_Ref' => $cp_bankacctno,
			'CP_BankAcctNo' => $cp_bankacctno,
			'CP_BankBranch' => $cp_bankbranch,
			'CP_Amount' => str_replace(',', '', $cp_amount),
			'CP_WithdrawalDay' => $cp_withdrawalday,
			'CP_CauseOfDeath' => $cp_causeofdeath,
			'CP_DateOfDeath' => $cp_dateofdeath,
			'CP_Disability' => $cp_disability,
			'CP_IsModified' => 1
		);
		$this->db->where('CI_AcctNo', $ci_acctno);
		$this->db->update('client_pension', $client_pen_array) or die('Error: update_client function. Please contact your System Administrator.');
		
		/* Update records in ln_hdr database */
		$ln_hdr_array = array(
			'LH_BranchCode' => $ci_branchcode,
			'CI_Name' => $ci_name,
			'LH_BankBranch' => $cp_bankbranch,
			'LH_BankAcctNo' => $cp_bankacctno,
			'LH_BankAmt' => str_replace(',', '', $cp_amount),
			'LH_LoanType' => $ci_type,
			'LH_PaymentType' => $cp_ptype,
			'LH_WithdrawalDate' => $cp_withdrawalday,
			'LH_CoMaker' => $ci_comaker,
			'LH_Agent1' => $ci_agent1,
			'LH_Agent2' => $ci_agent2,
			'LH_CedulaNo' => $ci_cedulano,
			'LH_CedulaPlace' => $ci_cedulaplace,
			'LH_CedulaDate' => $ci_ceduladate,
			'LH_IsReleased' => $lh_isreleased
		);
		$this->db->where('CI_AcctNo', $ci_acctno);
		$this->db->update('ln_hdr', $ln_hdr_array) or die('Error: update_client function. Please contact your System Administrator.');
		
		/* Update records in nhgt_bills.header table */
		$this->db->select('bill_id, billdate');
		$this->db->from('nhgt_bills.header');
		$this->db->where('CI_AcctNo', $ci_acctno);
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$data = explode('-', $row->billdate);
				$billdate = date('Y-m-t', strtotime($row->billdate));
				$int_day =intval(date('d', strtotime($billdate)));
				
				if($cp_withdrawalday > $int_day)
				{
					$bill_date = $data[0] . '-' . $data[1] . '-' . $int_day;
					$bill_date = date('Y-m-d', strtotime($bill_date));
				} else {
					$bill_date = $data[0] . '-' . $data[1] . '-' . $cp_withdrawalday;
					$bill_date = date('Y-m-d', strtotime($bill_date));
				}
				
				$header_array = array(
					'billdate' => $bill_date,
					'name' => $ci_name,
					'paytype' => $cp_ptype,
					'bankacctno' => $cp_bankacctno,
					'bankbranch' => $cp_bankbranch,
					'pentype' => $ci_source
				);
				$this->db->where('bill_id', $row->bill_id);
				$this->db->where('CI_AcctNo', $ci_acctno);
				$this->db->update('nhgt_bills.header', $header_array) or die('Error: update client function. Please contact your System Administrator.');
			}
		}
		
		$refund_que_array = array(
			'ci_name' => $ci_name,
			'ci_bankbranch' => $cp_bankbranch,
			'transtype' => $ci_source
		);
		$this->db->where('ci_acctno', $ci_acctno);
		$this->db->update('refund_que', $refund_que_array) or die('Error: update_client function. Please contact your System Administrator.');
		
		$this->db->trans_complete();
		
		$data = $ci_fname . ' ' . $ci_mname . ' ' . $ci_lname . '\'s record in client database has been updated.';
		return $data;
	}
	
	function delete_dependent()
	{
		$sysid = $this->input->post('sysid');
		$ci_acctno = $this->input->post('ci_acctno');
		$cd_fname = $this->test_input($this->input->post('cd_fname'));
		$cd_lname = $this->test_input($this->input->post('cd_lname'));
		
		$this->db->where('SysID', $sysid);
		$this->db->where('CI_AcctNo', $ci_acctno);
		$this->db->delete('client_dependents') or die('Error: delete dependent function. Please contact your System Administrator');
		
		$data = $cd_lname . ', ' . $cd_fname . ' has been deleted from client dependents database.';
		return $data;
	}
	
	function insert_dependent()
	{
		$sysid = $this->generate_id('SysID', 'client_dependents');
		$ci_acctno = $this->test_input($this->input->post('ci_acctno'));
		$cd_issurvivingdep = $this->input->post('cd_issurvivingdep');
		$cd_fname = $this->test_input($this->input->post('cd_fname'));
		$cd_lname = $this->test_input($this->input->post('cd_lname'));
		$cd_sssno = $this->input->post('cd_sssno') == '' ? '' : $this->test_input($this->input->post('cd_sssno'));
		$cd_bdate = $this->test_input($this->input->post('cd_bdate'));
		$cd_profession = $this->input->post('cd_profession') == '' ? '' : $this->test_input($this->input->post('cd_profession'));
		$cd_relation = $this->input->post('cd_relation');
		
		$cd_array = array(
			'SysID' => $sysid,
			'CI_AcctNo'=> $ci_acctno,
			'CD_IsSurvivingDep' => $cd_issurvivingdep,
			'CD_FName' => $cd_fname,
			'CD_LName' => $cd_lname,
			'CD_SSSNo' => $cd_sssno,
			'CD_BDate' => date('Y-m-d', strtotime($cd_bdate)),
			'CD_Profession' => $cd_profession,
			'CD_Relation' => $cd_relation,
			'CD_IsAdded' => 1,
			'CD_AddedBy' => $this->session->userdata['user_name'],
			'CD_AddedDate' => date('Y-m-d 00:00:00'),
			'CD_IsDeleted' => 0,
			'CD_IsDeletedBy' => 0,
			'CD_IsModified' => 0
		);
		$this->db->insert('client_dependents', $cd_array) or die('Error: insert dependent function. Please contact your System Administrator');
		
		$data = $cd_lname . ', ' . $cd_fname . ' has been added to client dependents database.';
		return $data;
	}
	
	function update_dependent()
	{
		$sysid = $this->input->post('sysid');
		$ci_acctno = $this->input->post('ci_acctno');
		$cd_issurvivingdep = $this->input->post('cd_issurvivingdep');
		$cd_fname = $this->input->post('cd_fname');
		$cd_lname = $this->input->post('cd_lname');
		$cd_sssno = $this->input->post('cd_sssno') == '' ? '' : $this->input->post('cd_sssno');
		$cd_bdate = $this->test_input($this->input->post('cd_bdate'));
		$cd_profession = $this->input->post('cd_profession') == '' ? '' : $this->input->post('cd_profession');
		$cd_relation = $this->input->post('cd_relation');
		
		$cd_array = array(
			'CD_IsSurvivingDep' => $cd_issurvivingdep,
			'CD_FName' => $cd_fname,
			'CD_LName' => $cd_lname,
			'CD_SSSNo' => $cd_sssno,
			'CD_BDate' => date('Y-m-d', strtotime($cd_bdate)),
			'CD_Profession' => $cd_profession,
			'CD_Relation' => $cd_relation,
			'CD_IsModified' => 1
		);
		$this->db->where('SysID', $sysid);
		$this->db->where('CI_AcctNo', $ci_acctno);
		$this->db->update('client_dependents', $cd_array) or die('Error: update dependent function. Please ocntact your System Administrator');
		
		$data = $cd_lname . ', ' . $cd_fname . 's record in client dependents database has been updated.';
		return $data;
	}
	
	function insert_comaker()
	{
		$sysid = $this->generate_id('SysID', 'comaker');
		$cm_branchcode = $this->test_input($this->input->post('cm_branchcode'));
		$cm_refno = $this->generate_cm_refno();
		$cm_fname = $this->test_input($this->input->post('cm_fname'));
		$cm_mname = $this->input->post('cm_mname') == '' ? '' : $this->test_input($this->input->post('cm_mname'));
		$cm_lname = $this->test_input($this->input->post('cm_lname'));
		$cm_bdate = $this->test_input($this->input->post('cm_bdate'));
		$cm_sex = $this->input->post('cm_sex') == '' ? 'M' : $this->test_input($this->input->post('cm_sex'));
		$cm_civilstatus = $this->input->post('cm_civilstatus') == '' ? 'S' : $this->test_input($this->input->post('cm_civilstatus'));
		$cm_telno = $this->input->post('cm_telno') == '' ? '' : $this->test_input($this->input->post('cm_telno'));
		$cm_mobileno = $this->input->post('cm_mobileno') == '' ? '' : $this->test_input($this->input->post('cm_mobileno'));
		$cm_add1 = $this->test_input($this->input->post('cm_add1'));
		$cm_add2 = $this->input->post('cm_add2') == '' ? '' : $this->test_input($this->input->post('cm_add2'));
		$cm_cedulano = $this->test_input($this->input->post('cm_cedulano'));
		$cm_ceduladate = $this->input->post('cm_ceduladate') == '' ? date('Y-m-d') : $this->test_input($this->input->post('cm_ceduladate'));
		$cm_cedulaplace = $this->input->post('cm_cedulaplace') == '' ? '' : $this->test_input($this->input->post('cm_cedulaplace'));
		
		$array = array(
			'SysID' => $sysid,
			'CM_BranchCode' => $cm_branchcode,
			'CM_RefNo' => $cm_refno,
			'CM_FName' => $cm_fname,
			'CM_MName' => $cm_mname,
			'CM_LName' => $cm_lname,
			'CM_Bdate' => date('Y-m-d 00:00:00', strtotime($cm_bdate)),
			'CM_Sex' => $cm_sex,
			'CM_CivilStatus' => $cm_civilstatus,
			'CM_Add1' => $cm_add1,
			'CM_Add2' => $cm_add2,
			'CM_TelNo' => $cm_telno,
			'CM_MobileNo' => $cm_mobileno,
			'CM_CedulaNo' => $cm_cedulano,
			'CM_CedulaDate' => date('Y-m-d 00:00:00', strtotime($cm_ceduladate)),
			'CM_CedulaPlace' => $cm_cedulaplace,
			'CM_IsLocked' => 0,
			'CM_IsAdded' => 1,
			'CM_AddedBy' => $this->session->userdata('user_name'),
			'CM_AddedDate' => date('Y-m-d 00:00:00'),
			'CM_IsDeleted' => 0,
			'CM_IsModified' => 0
		);
		$this->db->insert('comaker', $array) or die('Error: insert comaker function. Please contact your System Administrator.');
		
		$data = $cm_fname . ' ' . $cm_mname . ' ' . $cm_lname . ' has been added to comaker database.';
		return $data;
	}
	
	function update_comaker()
	{
		$cm_refno = $this->test_input($this->input->post('cm_refno'));
		$cm_branchcode = $this->test_input($this->input->post('cm_branchcode'));
		$cm_fname = $this->test_input($this->input->post('cm_fname'));
		$cm_mname = $this->input->post('cm_mname') == '' ? '' : $this->test_input($this->input->post('cm_mname'));
		$cm_lname = $this->test_input($this->input->post('cm_lname'));
		$cm_bdate = $this->test_input($this->input->post('cm_bdate'));
		$cm_sex = $this->input->post('cm_sex') == '' ? 'M' : $this->test_input($this->input->post('cm_sex'));
		$cm_civilstatus = $this->input->post('cm_civilstatus') == '' ? 'S' : $this->test_input($this->input->post('cm_civilstatus'));
		$cm_telno = $this->input->post('cm_telno') == '' ? '' : $this->test_input($this->input->post('cm_telno'));
		$cm_mobileno = $this->input->post('cm_mobileno') == '' ? '' : $this->test_input($this->input->post('cm_mobileno'));
		$cm_add1 = $this->test_input($this->input->post('cm_add1'));
		$cm_add2 = $this->input->post('cm_add2') == '' ? '' : $this->test_input($this->input->post('cm_add2'));
		$cm_cedulano = $this->test_input($this->input->post('cm_cedulano'));
		$cm_ceduladate = $this->input->post('cm_ceduladate') == '' ? date('Y-m-d') : $this->test_input($this->input->post('cm_ceduladate'));
		$cm_cedulaplace = $this->input->post('cm_cedulaplace') == '' ? '' : $this->test_input($this->input->post('cm_cedulaplace'));
		
		$array = array(
			'CM_BranchCode' => $cm_branchcode,
			'CM_FName' => $cm_fname,
			'CM_MName' => $cm_mname,
			'CM_LName' => $cm_lname,
			'CM_BDate' => date('Y-m-d 00:00:00', strtotime($cm_bdate)),
			'CM_Sex' => $cm_sex,
			'CM_CivilStatus' => $cm_civilstatus,
			'CM_Add1' => $cm_add1,
			'CM_Add2' => $cm_add2,
			'CM_TelNo' => $cm_telno,
			'CM_MobileNo' => $cm_mobileno,
			'CM_CedulaNo' => $cm_cedulano,
			'CM_CedulaDate' => date('Y-m-d 00:00:00', strtotime($cm_ceduladate)),
			'CM_CedulaPlace' => $cm_cedulaplace,
			'CM_IsModified' => 1,
			'CM_IsModifiedBy' => $this->session->userdata('user_name'),
			'CM_IsModifiedDate' => date('Y-m-d')
		);
		$this->db->where('CM_RefNo', $cm_refno);
		$this->db->update('comaker', $array) or die('Error: update comaker function. Please contact your System Administrator.');
		
		$data = $cm_fname . ' ' . $cm_mname . ' ' . $cm_lname . '\'s record in comaker database has been updated.';
		return $data;
	}
	
	function update_loan_date($database = '')
	{
		$ci_acctno = $this->master_model->test_input($this->input->post('ci_acctno'));
		$lh_pn = $this->master_model->test_input($this->input->post('lh_pn'));
		$loan_date = $this->master_model->test_input($this->input->post('lh_loandate'));
		
		$data = explode('-', $lh_pn);
		$updated_lh_pn = $data[0] . '-' . $loan_date . '-' . $data[4];
		
		$this->db->trans_start();
		
		/* Update nhgt_master.branch_transfer records */
		$this->db->select('aid, refid', false);
		$this->db->from('nhgt_master.branch_transfer');
		$this->db->like('refid', $ci_acctno);
		$this->db->like('refid', $lh_pn);
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$refid = explode('.', $row->refid);
				$updated_refid = $refid[0] . '.' . $updated_lh_pn;
				
				$transfer_array = array(
					'refid' => $updated_refid
				);
				$this->db->where('aid', $row->aid);
				$this->db->update('nhgt_master.branch_transfer', $transfer_array);
			}
		}
		
		/* Update ln_hdr records */
		$lnhdr_array = array(
			'LH_PN' => $updated_lh_pn,
			'LH_LoanDate' => $loan_date
		);
		$this->db->where('CI_AcctNo', $ci_acctno);
		$this->db->where('LH_PN', $lh_pn);
		$this->db->update('ln_hdr', $lnhdr_array);
		
		/* Update ln_ldgr records */
		$lnldgr_array = array(
			'LH_PN' => $updated_lh_pn
		);
		$this->db->where('CI_AcctNo', $ci_acctno);
		$this->db->where('LH_PN', $lh_pn);
		$this->db->update('ln_ldgr', $lnldgr_array);
		
		/* Update refund_que records */
		$refund_array = array(
			'pnno' => $updated_lh_pn
		);
		$this->db->where('ci_acctno', $ci_acctno);
		$this->db->where('pnno', $lh_pn);
		$this->db->update('refund_que', $refund_array);
		
		/* Update tbl_cvheadr records */
		$cv_array = array(
			'pnno' => $updated_lh_pn . ';'
		);
		$this->db->where('payee', $ci_acctno);
		$this->db->where('pnno', $lh_pn . ';');
		$this->db->update('tbl_cvheadr', $cv_array);
		
		/* Update nhgt_bilss.header records */
		$header_array = array(
			'LH_PN' => $updated_lh_pn
		);
		$this->db->where('CI_AcctNo', $ci_acctno);
		$this->db->where('LH_PN', $lh_pn);
		$this->db->update('nhgt_bills.header', $header_array);
		
		$this->db->trans_complete();
		
		$data = 'Loan No. ' . $lh_pn . ' loan date has been updated. And its Loan No. updated to ' . $updated_lh_pn . '.';
		return $data;
	}
	
	function update_loan_duration()
	{
		$ci_acctno = $this->test_input($this->input->post('ci_acctno'));
		$lh_pn = $this->test_input($this->input->post('lh_pn'));
		$loan_date = $this->input->post('lh_loandate');
		$start_date = $this->input->post('lh_startdate');
		$end_date = $this->input->post('lh_enddate');
		
		$this->db->trans_start();
		
		$loan_day = date('d', strtotime($loan_date));
		$start_day = date('t', strtotime($start_date));
		$end_day = date('t', strtotime($end_date));
		
		if($loan_day > $start_day)
		{
			$lh_startdate = date('Y-m-' . $start_day, strtotime($start_date));
		} else {
			$lh_startdate = date('Y-m-' . $loan_day, strtotime($start_date));
		}
		
		if($loan_day > $end_day)
		{
			$lh_enddate = date('Y-m-' . $end_day, strtotime($end_date));
		} else {
			$lh_enddate = date('Y-m-' . $loan_day, strtotime($end_date));
		}		
		
		$lnhdr_array = array(
			'LH_StartDate' => $lh_startdate,
			'LH_EndDate' => $lh_enddate
		);
		$this->db->where('CI_AcctNo', $ci_acctno);
		$this->db->where('LH_PN', $lh_pn);
		$this->db->update('ln_hdr', $lnhdr_array) or die('Error: update loan function. Please contact your System Administartor');
		
		$this->db->where('CI_AcctNo', $ci_acctno);
		$this->db->where('LH_PN', $lh_pn);
		$this->db->delete('nhgt_bills.header') or die('Error: update loan funcion. Please contact your System Administrator.');
		
		/* Generate billing */
		$result = $this->fetch_branch_code();
		$result = explode(';', $result->value);
		$branch_code = $result[0];
		
		$columns = 'CI_Name as ci_name, LH_LoanAmt as lh_loanamt, LH_Balance as lh_balance, LH_Terms as lh_terms, LH_LoanTrans as lh_loantrans, LH_PaymentType as lh_paymenttype';
		$ln_hdr = $this->fetch_loan_details($columns, $ci_acctno, $lh_pn);
		
		$columns = 'CP_BankBranch as cp_bankbranch, CP_BankAcctNo as cp_bankacctno, CP_PensionType as cp_pensiontype, CP_WithdrawalDay as cp_withdrawalday';
		$client_pension = $this->fetch_client_pension_details($columns, $ci_acctno);
		
		$start_date = date('Y-m', strtotime($lh_startdate));
		$start_date = new DateTime($start_date);
		$end_date = date('Y-m', strtotime($lh_enddate));
		$end_date = new DateTime($end_date);
		$diff = $end_date->diff($start_date);
		$months = $diff->y * 12 + $diff->m + $diff->d / 30;
		$months = round($months);
		$terms = $months + 1;
		
		$startdate = strtotime($lh_startdate);
		for($i = 0; $i < $terms; $i++)
		{
			$bill_date = date('Y-m-t', strtotime('+'. $i .' month', $startdate));
			$data = explode('-', $bill_date);
			$int_day = intval(date('d', strtotime($bill_date)));
			if($client_pension->cp_withdrawalday > $int_day)
			{
				$billdate = $data[0].'-' . $data[1] . '-' . $int_day;
				$billdate = date('Y-m-d', strtotime($billdate));
			} else {
				$billdate = $data[0] . '-' . $data[1] . '-' . $client_pension->cp_withdrawalday;
				$billdate = date('Y-m-d', strtotime($billdate));
			}
			
			$billing_array = array(
				'billtype'=> 'auto',
				'branchcode' => $branch_code,
				'CI_AcctNo' => $ci_acctno,
				'LH_PN' => $lh_pn,
				'loantrans' => $ln_hdr->lh_loantrans,
				'billdate' => $billdate,
				'name' => $ln_hdr->ci_name,
				'paytype' => $ln_hdr->lh_paymenttype,
				'bankacctno'=> $client_pension->cp_bankacctno,
				'bankbranch' 	=> $client_pension->cp_bankbranch,
				'pentype' => $client_pension->cp_pensiontype,
				'duration' => date('F Y', strtotime($lh_startdate)) . ' - ' . date('F Y', strtotime($lh_enddate)),
				'terms' => $ln_hdr->lh_terms,
				'balance' => $ln_hdr->lh_balance,
				'amtodrawn' => $ln_hdr->lh_loanamt,
				'generateby' => $this->session->userdata('user_name'),
				'dategenerate' => date('Y-m-d H:i:s'),
				'collectby' => '',
				'datecollected' => date('0000-00-00 00:00:00'),
			);
			$this->db->insert('nhgt_bills.header', $billing_array) or die('Error: insert_billing function. Please contact your system administrator');
		}
		
		$this->db->trans_complete();
		
		$data = 'Loan No. ' . $lh_pn . ' duration has been updated.';
		return $data;
	}
	
	function validate_client_inputs()
	{
		$this->form_validation->set_rules('ci_source', 'Source', 'trim|required|xss_clean');
		$this->form_validation->set_rules('ci_sssno', 'GSIS/SSS No.', 'trim|required|xss_clean');
		$this->form_validation->set_rules('ci_acctno', 'Client Code', 'trim|required|xss_clean');
		$this->form_validation->set_rules('ci_fname', 'First Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('ci_lname', 'Last Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('ci_bdate', 'Birth Date', 'trim|required|xss_clean');
		$this->form_validation->set_rules('ci_status', 'Status', 'trim|required|xss_clean');
		$this->form_validation->set_rules('ci_grp', 'Group', 'trim|required|xss_clean');
		$this->form_validation->set_rules('ci_type', 'Client', 'trim|required|xss_clean');
		$this->form_validation->set_rules('ci_branchcode', 'Branch', 'trim|required|xss_clean');
		$this->form_validation->set_rules('ci_add1', 'Present Address', 'trim|required|xss_clean');
		
		$this->form_validation->set_rules('cp_bankbranch', 'Bank/Branch', 'trim|required|xss_clean');
		$this->form_validation->set_rules('cp_bankacctno', 'Bank Acct. No.', 'trim|required|xss_clean');
		$this->form_validation->set_rules('cp_amount', 'Amount', 'trim|required|xss_clean');
		$this->form_validation->set_rules('cp_withdrawalday', 'Withdrawal Day', 'trim|required|xss_clean');
		$this->form_validation->set_rules('cp_ptype', 'Payment', 'trim|required|xss_clean');
		return;
	}
	
	function validate_dependent_inputs()
	{
		$this->form_validation->set_rules('cd_fname', 'First Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('cd_lname', 'Last Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('cd_bdate', 'Birth Date', 'trim|required|xss_clean');
		return;
	}
	
	function validate_agent_inputs()
	{
		$this->form_validation->set_rules('ai_refno', 'Reference No.', 'trim|required|xss_clean');
		$this->form_validation->set_rules('ai_branchcode', 'Branch', 'trim|required|xss_clean');
		$this->form_validation->set_rules('ai_fname', 'First Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('ai_lname', 'Last Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('ai_bdate', 'Birth Date', 'trim|required|xss_clean');
		$this->form_validation->set_rules('ai_add1', 'Present Address', 'trim|required|xss_clean');
		return;
	}
	
	function validate_comaker_inputs()
	{
		$this->form_validation->set_rules('cm_refno', 'Reference No.', 'trim|required|xss_clean');
		$this->form_validation->set_rules('cm_branchcode', 'Branch', 'trim|required|xss_clean');
		$this->form_validation->set_rules('cm_fname', 'First Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('cm_lname', 'Last Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('cm_bdate', 'Birth Date', 'trim|required|xss_clean');
		$this->form_validation->set_rules('cm_add1', 'Present Address', 'trim|required|xss_clean');
		$this->form_validation->set_rules('cm_cedulano', 'Res. Cert. No.', 'trim|required|xss_clean');
		return;
	}
	
	function validate_loan_date_inputs()
	{
		$this->form_validation->set_rules('lh_loandate', 'Loan Date', 'trim|required|xss_clean');
		$this->form_validation->set_rules('ci_acctno', 'Client Acct. No.', 'trim|required|xss_clean');
		$this->form_validation->set_rules('lh_pn', 'Loan No.', 'trim|required|xss_clean');
		return;
	}
	
	function validate_loan_duration_inputs()
	{
		$this->form_validation->set_rules('lh_startdate', 'Start Date', 'trim|required|xss_clean');
		$this->form_validation->set_rules('lh_enddate', 'End Date', 'trim|required|xss_clean');
		$this->form_validation->set_rules('ci_acctno', 'Client Acct. No.', 'trim|required|xss_clean');
		$this->form_validation->set_rules('lh_pn', 'Loan No.', 'trim|required|xss_clean');
		return;
	}
	
	function fetch_loan_details($columns, $ci_acctno, $lh_pn, $database = '')
	{
		$this->db->select($columns, false);
		$this->db->from($database . '.ln_hdr');
		$this->db->where('CI_AcctNo', $ci_acctno);
		$this->db->where('LH_PN', $lh_pn);
		$this->db->where('LH_LoanTrans !=', 'SPEC');
		$this->db->where('LH_IsPending', 0);
		$this->db->where('LH_Processed', 1);
		$this->db->where('LH_Cancelled', 0);
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			$data = $query->row();
			return $data;
		}
		return false;
	}
	
	function test_input($data)
	{
		$data = trim($data);
		$data = stripslashes(str_replace('/', '', $data));
		$data = htmlspecialchars($data);
		return $data;
	}
}
//8128
?>