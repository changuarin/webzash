<?php

class Collection_model extends Model
{
	function Collection_model()
	{
		parent::Model();
	}
	
	function fetch_billings($month, $year, $ci_type, $coll_type)
	{
		$result = $this->fetch_branch_code();
		$result = explode(';', $result->value);
		$branch_code = $result[0];
		
		$status = array('adj', 'due', 'pay', 'rem');
		
		$this->db->select('nhgt_bills.header.bill_id,
			nhgt_bills.header.CI_AcctNo,
			nhgt_bills.header.LH_PN,
			nhgt_bills.header.loantrans,
			nhgt_bills.header.billdate,
			nhgt_bills.header.name,
			nhgt_bills.header.paytype,
			nhgt_bills.header.bankbranch,
			nhgt_bills.header.duration,
			nhgt_bills.header.amtodrawn', false);
		$this->db->from('nhgt_bills.header');
		$this->db->where('YEAR(nhgt_bills.header.billdate)', $year);
		$this->db->where('MONTH(nhgt_bills.header.billdate)', $month);
		$this->db->where('nhgt_bills.header.branchcode', $branch_code);
		$this->db->where('nhgt_bills.header.bankbranch !=', 'ACCOM');
		if($ci_type == 'SAL')
		{
			$this->db->like('nhgt_bills.header.bankbranch', 'SALARY');
		} else {
			$this->db->not_like('nhgt_bills.header.bankbranch', 'SALARY');
		}
		if($coll_type == '1')
		{
			$this->db->where('nhgt_bills.header.status', null);
		} elseif($coll_type == '2') {
			$this->db->where_in('nhgt_bills.header.status', $status);
		}
		//$this->db->limit(50);
		$this->db->order_by('nhgt_bills.header.billdate', 'ASC');
		$this->db->order_by('nhgt_bills.header.name', 'ASC');
		$this->db->order_by('nhgt_bills.header.CI_AcctNo', 'DESC');
		$this->db->order_by('nhgt_bills.header.paytype', 'DESC');
		$this->db->order_by('nhgt_bills.header.loantrans = "SPEC"', 'DESC');
		$this->db->order_by('nhgt_bills.header.amtodrawn', 'ASC');
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
	
	function fetch_client_billings($columns, $ci_acctno, $lh_pn)
	{
		$this->db->select($columns, false);
		$this->db->from('nhgt_bills.header');
		$this->db->where('CI_AcctNo', $ci_acctno);
		$this->db->where('LH_PN', $lh_pn);
		$this->db->order_by('billdate', 'ASC');
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
	
	function close_billing($billing)
	{
		$this->db->trans_start();
		
		foreach($billing as $bill_id)
		{
			$array = array(
				'status' => 'closed'
			);
			
			$this->db->where('bill_id', $bill_id);
			$this->db->update('nhgt_bills.header', $array) or die('Error: close billing function. Please contact your System Administrator.');
		}
		
		$this->db->trans_complete();
		return;
	}
	
	function insert_billing($ci_acctno, $lh_pn)
	{
		$result = $this->fetch_branch_code();
		$result = explode(';', $result->value);
		$branch_code = $result[0];
		
		$columns = 'CI_Name as ci_name, LH_LoanAmt as lh_loanamt, LH_Balance as lh_balance, LH_Terms as lh_terms, LH_LoanTrans as lh_loantrans, LH_StartDate as lh_startdate, LH_EndDate as lh_enddate, LH_PaymentType as lh_paymenttype';
		$ln_hdr = $this->fetch_loan_details($columns, $ci_acctno, $lh_pn);
		$lh_terms = intval($ln_hdr->lh_terms);
		$lh_startdate = strtotime($ln_hdr->lh_startdate);
		
		$columns = 'CP_BankBranch as cp_bankbranch, CP_BankAcctNo as cp_bankacctno, CP_PensionType as cp_pensiontype, CP_WithdrawalDay as cp_withdrawalday';
		$client_pension = $this->fetch_client_pension_details($columns, $ci_acctno);
		
		for($i = 0; $i < $lh_terms; $i++)
		{
			$bill_date = date('Y-m-t', strtotime('+' . $i .' month', $lh_startdate));
			$data = explode('-', $bill_date);
			$int_day = intval(date('d', strtotime($bill_date)));
			if($client_pension->cp_withdrawalday > $int_day)
			{
				$billdate = $data[0] . '-' . $data[1] . '-' . $int_day;
				$billdate = date('Y-m-d', strtotime($billdate));
			} else {
				$billdate = $data[0] . '-' . $data[1] . '-' . $client_pension->cp_withdrawalday;
				$billdate = date('Y-m-d', strtotime($billdate));
			}
			
			// Validate if billing already exists
			$this->db->select('*');
			$this->db->from('nhgt_bills.header');
			$this->db->where('CI_AcctNo', $ci_acctno);
			$this->db->where('LH_PN', $lh_pn);
			$this->db->where('billdate', $billdate);
			$query = $this->db->get();
			
			if($query->num_rows() == 0)
			{
				$array = array(
					'billtype'		=> 'manual',
					'branchcode'	=> $branch_code,
					'CI_AcctNo'		=> $ci_acctno,
					'LH_PN'			=> $lh_pn,
					'loantrans'		=> $ln_hdr->lh_loantrans,
					'billdate'		=> $billdate,
					'name'			=> $ln_hdr->ci_name,
					'paytype'		=> $ln_hdr->lh_paymenttype,
					'bankacctno'	=> $client_pension->cp_bankacctno,
					'bankbranch'	=> $client_pension->cp_bankbranch,
					'pentype'		=> $client_pension->cp_pensiontype,
					'duration'		=> date('F Y', strtotime($ln_hdr->lh_startdate)) . ' - ' . date('F Y', strtotime($ln_hdr->lh_enddate)),
					'terms'			=> $lh_terms,
					'balance'		=> $ln_hdr->lh_balance,
					'amtodrawn'		=> $ln_hdr->lh_loanamt,
					'generateby'	=> $this->session->userdata('user_name'),
					'dategenerate'	=> date('Y-m-d H:i:s'),
					'collectby'		=> '',
					'datecollected'	=> date('0000-00-00 00:00:00'),
				);
				
				$this->db->insert('nhgt_bills.header', $array) or die('Error: insert billing function. Please contact your system administrator');
			}
		}
		
		return;
	}
	
	function delete_billing($billing)
	{
		$this->db->trans_start();
		
		foreach($billing as $bill_id)
		{
			$this->db->where('bill_id', $bill_id);
			$this->db->delete('nhgt_bills.header') or die('Error: close billing function. Please contact your System Administrator.');
		}
		
		$this->db->trans_complete();
		
		return;
	}
		
	function fetch_branch_code()
	{
		$this->db->select('value', false);
		$this->db->from('parameter');
		$this->db->where('code', 'BRANCH');
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
	
	function fetch_loan_details($columns, $ci_acctno, $pnno, $database = '')
	{
		$this->db->select($columns, false);
		$this->db->from($database . '.ln_hdr');
		$this->db->where('CI_AcctNo', $ci_acctno);
		$this->db->where('LH_PN', $pnno);
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
	
	function fetch_coll_details($columns, $uid, $database = '')
	{
		$this->db->select($columns, false);
		$this->db->from($database . '.collection_entry');
		$this->db->where('uid', $uid);
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			$data = $query->row();
			return $data;
		}
		return false;
	}
}

?>