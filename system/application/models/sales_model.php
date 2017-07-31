<?php

class Sales_model extends Model
{
	function Sales_model()
	{
		parent::Model();
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
	
	function fetch_refunds($ci_acctno, $database = '')
	{
		$this->db->select('LH_PN as lh_pn, LH_BankacctNo as lh_bankacctno, LH_Refund as lh_refund, LH_LoanTrans as lh_loantrans', false);
		$this->db->from($database . '.ln_hdr');
		$this->db->where('CI_AcctNo', $ci_acctno);
		$this->db->where('LH_Refund <', 0);
		$this->db->where('LH_LoanTrans !=', 'SPEC');
		$this->db->where('LH_IsPending', 0);
		$this->db->where('LH_Processed', 1);
		$this->db->where('LH_Cancelled', 0);
		$this->db->order_by('LH_LoanDate', 'DESC');
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$lh_refund = abs($row->lh_refund);
				
				$this->db->select('LL_PaymentDate as ll_paymentdate', false);
				$this->db->from($database . '.ln_ldgr');
				$this->db->where('CI_AcctNo', $ci_acctno);
				$this->db->where('LH_PN', $row->lh_pn);
				$this->db->where('LL_IsPayment', 1);
				$this->db->where('LL_IsDeleted', 0);
				$this->db->order_by('LL_PaymentDate', 'DESC');
				$this->db->limit(1);
				$ledger = $this->db->get();
				
				$ll_paymentdate = '';
				if($ledger->num_rows() > 0)
				{
					$ledger = $ledger->row();
					$ll_paymentdate = $ledger->ll_paymentdate;
				}
				$data[] = array(
					'lh_pn' => $row->lh_pn,
					'lh_bankacctno' => $row->lh_bankacctno,
					'lh_refund' => $lh_refund,
					'll_paymentdate' => $ll_paymentdate,
					'lh_loantrans' => $row->lh_loantrans
				);
			}
			return $data;
		}
		return false;
	}
	
	function fetch_parameter_value($code, $database = '')
	{
		$this->db->select('value', false);
		$this->db->from($database . '.parameter');
		$this->db->where('code', $code);
		$this->db->order_by('value');
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			if($query->num_rows() == 1)
			{
				return $query->row();
			} elseif($query->num_rows() > 1) {
				foreach($query->result() as $row)
				{
					$data[] = $row;
				}
				return $data;
			}
		}
		return false;
	}
	
	function validate_refund_inputs()
	{
		$this->form_validation->set_rules('ci_acctno', 'Client Account No.', 'trim|required|xss_clean');
		$this->form_validation->set_rules('ci_name', 'Client Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('pn_no', 'PN No.', 'trim|required|xss_clean');
		$this->form_validation->set_rules('refund_type', 'Refund Type', 'trim|required|xss_clean');
		$this->form_validation->set_rules('loan_trans', 'Loan Trans', 'trim|required|xss_clean');
		$this->form_validation->set_rules('refund_date', 'Refund Date', 'trim|required|xss_clean');
		$this->form_validation->set_rules('bank_branch', 'Bank Branch', 'trim|required|xss_clean');
		$this->form_validation->set_rules('bank_acctno', 'Bank Acct No.', 'trim|required|xss_clean');
		$this->form_validation->set_rules('refund_amount', 'Refund Amount', 'trim|required|xss_clean');
		$this->form_validation->set_rules('remarks', 'Remarks', 'trim|required|xss_clean');
		return;
	}
	
	function insert_refund()
	{
		$this->load->model('master_model');
		
		$lh_pn = $this->test_input($this->input->post('pn_no'));
		$refund_type = $this->input->post('refund_type') == '' ? 'RF' : $this->test_input($this->input->post('refund_type'));
		$loan_type = $this->test_input($this->input->post('loan_trans'));
		$ci_acctno = $this->test_input($this->input->post('ci_acctno'));
		$ci_name = $this->input->post('ci_name') == '' ? '' : $this->test_input($this->input->post('ci_name'));
		$transdate = $this->input->post('rfp_date') == '' ? date('Y-m-d') : $this->test_input($this->input->post('rfp_date'));
		$transrefno = $this->input->post('rfp_no') == '' ? '' : $this->test_input($this->input->post('rfp_no'));
		$bank_branch = $this->test_input($this->input->post('bank_branch'));
		$bank_acctno = $this->test_input($this->input->post('bank_acctno'));
		$refunddue = $this->input->post('refund_amount') == '' ? 0 : $this->test_input($this->input->post('refund_amount'));
		$status = $refund_type == 'RF' ? 'done' : $refund_type == 'CV' ? 'approved' : 'done';
		$remarks = $this->test_input($this->input->post('remarks'));
		
		$client = $this->fetch_client_details('CI_BankBranch as ci_bankbranch, CI_Source as ci_source', $ci_acctno);
		
		$this->db->trans_start();
		
		$refund_que_array = array(
			'queno' => 1,
			'colid' => '',
			'cvrf' => $refund_type,
			'ci_acctno' => $ci_acctno,
			'pnno' => $lh_pn,
			'ci_name' => $ci_name,
			'ci_bankbranch' => $client->ci_bankbranch,
			'transdate' => date('Y-m-d', strtotime($transdate)),
			'transtype' => $client->ci_source,
			'transrefno' => $transrefno,
			'atmadvance' => 0,
			'advrefund' => null,
			'refunddue' => str_replace(',', '', $refunddue),
			'status' => $status,
			'queby' => $this->session->userdata('user_name'),
			'proby' => '',
			'appby' => '',
			'remarks' => $remarks
		);
		$this->db->insert('refund_que', $refund_que_array) or die('Error: insert_refund function. Please contact your System Administrator.');
		
		if($refund_type == 'RF')
		{
			$result = $this->fetch_branch_code();
			$result = explode(';', $result->value);
			$branch_code = $result[0];
			
			$refundpost_array = array(
				'ID' => 1,
				'CI_AcctNo' => $ci_acctno,
				'LH_BranchCode_Processed' => $branch_code,
				'LH_BankAcctNo' => $bank_acctno,
				'LH_PN' => $lh_pn,
				'LH_LoanType' => $loan_type,
				'RFW_NO' => 'RFP#'.$transrefno,
				'LL_Rebates' => 0,
				'LL_InterestAmt' => 0,
				'LL_AmountCheck' => 0,
				'LL_AmountCash' => 0,
				'LL_AmountCash_Payment' => 0,
				'LL_ShortPayment' => 0,
				'LL_Refund' => str_replace(',', '', $refunddue),
				'LL_Remarks' => $remarks,
				'LL_PaymentDate' => date('Y-m-d', strtotime($transdate)),
				'LL_Processed' => 1,
				'LL_ProcessedDate' => date('Y-m-d 00:00:00'),
				'LL_ProcessedBy' => $this->session->userdata('user_name'),
				'LL_PaymentType' => '',
				'LL_IsPayment' => 0,
				'LL_IsRFW' => 1,
				'LL_IsRefund' => 1,
				'LL_CM' => 0,
				'LL_IsBounceCheck' => 0,
				'LL_IsUncollected' => 0,
				'LL_IsShortPayment' => 0,
				'LL_IsDeleted' => 0,
				'LL_Posted_BySales' => 0,
				'LL_Posted_ByAcct' => 0,
				'LL_IsAdded' => 1,
				'LL_IsModified' => 0
			);
			$this->db->insert('ln_ldgr', $refundpost_array) or die('Error: insert_refund function. Please contact your System Administrator.');
			
			$result = $this->fetch_loan_details('LH_Balance as lh_balance, LH_Refund as lh_refund, LH_Payment as lh_payment', $ci_acctno, $lh_pn);
			$lh_balance = $result->lh_balance + $refunddue;
			$lh_refund = $result->lh_refund + $refunddue;
			$lh_payment = $result->lh_payment - $refunddue;
			
			$lnhdr_array = array(
				'LH_Balance' => $lh_balance,
				'LH_Refund' => $lh_refund,
				'LH_Payment' => $lh_payment,
			);
			$this->db->where('CI_AcctNo', $ci_acctno);
			$this->db->where('LH_PN', $lh_pn);
			$this->db->update('ln_hdr', $lnhdr_array) or die('Error: insert_refund function. Please contact your System Administrator.');
		}
		
		$this->db->trans_complete();
		$data = $ci_name . '\'s refund has been processed.';
		return $data;
	}
	
	function validate_atmpb_release_inputs()
	{
		$this->form_validation->set_rules('ci_acctno', 'Client Acct. No.', 'trim|required|xss_clean');
		$this->form_validation->set_rules('date', 'Date', 'trim|required|xss_clean');
		$this->form_validation->set_rules('name', 'Client Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('cp_bankbranch', 'Bank Branch.', 'trim|required|xss_clean');
		$this->form_validation->set_rules('cp_pensiontype', 'Pension Type', 'trim|required|xss_clean');
		$this->form_validation->set_rules('cp_bankacctno', 'Bank Acct. No.', 'trim|required|xss_clean');
		return;
	}
	
	function process_atmpb_release()
	{
		$result = $this->fetch_branch_code();
		$result = explode(';', $result->value);
		$branch_code = $result[0];
		
		$ci_acctno = $this->test_input($this->input->post('ci_acctno'));
		$date = $this->test_input($this->input->post('date'));
		$ctrl_no = $this->input->post('ctrl_no') == '' ? '' : $this->test_input($this->input->post('ctrl_no'));
		$name = $this->test_input($this->input->post('name'));
		$cp_pensiontype = $this->test_input($this->input->post('cp_pensiontype'));
		$cp_bankbranch = $this->test_input($this->input->post('cp_bankbranch'));
		$cp_bankacctno = $this->test_input($this->input->post('cp_bankacctno'));
		$prior_to_release = $this->input->post('prior_to_release') == '' ? 0 : $this->test_input($this->input->post('prior_to_release'));
		$verification = $this->input->post('verification') == '' ? '' : $this->test_input($this->input->post('verification'));
		$fully_paid_date = $this->input->post('fully_paid_date');
		$verified_by = $this->input->post('verified_by') == '' ? '' : $this->test_input($this->input->post('verified_by'));
		$cleared_apar = $this->input->post('cleared_apar') == '' ? '' : $this->test_input($this->input->post('cleared_apar'));
		$other_acctabilities = $this->input->post('other_acctabilities') == '' ? '' : $this->test_input($this->input->post('other_acctabilities'));
		$accounting = $this->input->post('accounting') == '' ? '' : $this->test_input($this->input->post('accounting'));
		$other_remarks = $this->input->post('other_remarks') == '' ? '' : $this->test_input($this->input->post('other_remarks'));
		
		/* Fetch latest loan */
		$this->db->select('LH_PN', false);
		$this->db->from('ln_hdr');
		$this->db->where('CI_AcctNo', $ci_acctno);
		$this->db->order_by('LH_LoanDate', 'DESC');
		$query = $this->db->get();
		
		$lh_pn = '';
		if($query->num_rows() > 0)
		{
			$result = $query->row();
			$lh_pn = $result->LH_PN;
		}
		
		$this->db->trans_start();
		
		/* Insert inputs to atm_pb_history */
		$atmpb_array = array(
			'CI_AcctNo' => $ci_acctno,
			'LH_PN' => $lh_pn,
			'CI_BranchCode' => $branch_code,
			'AP_Date' => date('Y-m-d', strtotime($date)),
			'AP_Name' => $name,
			'AP_PBNo' => $ctrl_no,
			'AP_Bank' => $cp_bankbranch,
			'AP_TypePension' => $cp_pensiontype,
			'AP_BankAcctNo' => $cp_bankacctno,
			'AP_BalanceBeforeRelease' => str_replace(',', '', $prior_to_release),
			'AP_Verification' => $verification,
			'AP_LoansFullyPaid' => date('Y-m-d', strtotime($fully_paid_date)),
			'AP_VerifiedCertified' => $verified_by,
			'AP_Cleared_AP_AR' => $cleared_apar,
			'AP_Other' => $other_acctabilities,
			'AP_Accounting' => $accounting,
			'AP_OtherRemarks' => $other_remarks,
			'AP_PreparedBy' => $this->session->userdata('user_name'),
			'AP_IsAdded' => 1,
			'AP_IsModified' => 0
		);
		$this->db->insert('atm_pb_history', $atmpb_array) or die($this->db->_error_message() . 'Error: process_atmpb_release. Please contact your System Administrator.');
		
		/* Update client CI_Status */
		$client_array = array(
			'CI_Status' => 'I',
		);
		$this->db->where('CI_AcctNo', $ci_acctno);
		$this->db->update('client', $client_array) or die($this->db->_error_message() . 'Error: update_client function. Please contact your System Administrator.');
		
		/* Update ln_hdr LH_IsReleased */
		$ln_hdr_array = array(
			'LH_IsReleased' => 1,
			'LH_IsReleasedBy' => $this->session->userdata('user_name'),
			'LH_IsReleasedDate' => date('Y-m-d', strtotime($date))
		);
		$this->db->where('CI_AcctNo', $ci_acctno);
		$this->db->where('LH_PN', $lh_pn);
		$this->db->update('ln_hdr', $ln_hdr_array) or die($this->db->_error_message() . 'Error: update_client function. Please contact your System Administrator.');
		
		$this->db->trans_complete();
		
		$data = $name . '\'s ATM/PB release has been processed.';
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
	
	function fetch_agent_commission($from_date, $to_date, $ai_refno, $agent_type, $database = '')
	{
		$this->db->select('client.CI_AcctNo, ln_hdr.LH_PN, ln_hdr.LH_LoanDate, ln_hdr.CI_Name, ln_hdr.LH_Terms, ln_hdr.LH_LoanTrans, ln_hdr.LH_MonthlyAmort, ln_hdr.LH_Principal, ln_hdr.LH_Agent1 as ai1_refno, client.CI_Agent1_Rate as ai1_rate, ln_hdr.LH_Agent2 as ai2_refno, client.CI_Agent2_Rate as ai2_rate', false);
		$this->db->from('ln_hdr');
		$this->db->join('client', 'client.CI_AcctNo = ln_hdr.CI_AcctNo');
		if($agent_type == '0')
		{
			$this->db->where('ln_hdr.LH_LoanTrans !=', 'SPEC');
			$this->db->where('(ln_hdr.LH_Agent1 = "' . $ai_refno . '" OR ln_hdr.LH_Agent2 = "' . $ai_refno . '")');
		} elseif($agent_type == '1') {
			$this->db->where('ln_hdr.LH_LoanTrans !=', 'SPEC');
			$this->db->where('ln_hdr.LH_Agent1', $ai_refno);
		} elseif($agent_type == '2') {
			$this->db->where('ln_hdr.LH_LoanTrans', 'NEW');
			$this->db->where('ln_hdr.LH_Agent2', $ai_refno);
		}
		$this->db->where('ln_hdr.LH_LoanDate >=', $from_date);
		$this->db->where('ln_hdr.LH_LoanDate <=', $to_date);
		$this->db->where('ln_hdr.LH_LoanType', 'PEN');
		$this->db->order_by('ln_hdr.LH_LoanDate', 'ASC');
		$this->db->order_by('ln_hdr.CI_Name', 'ASC');
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$type = '0';
				if($row->ai1_refno == $ai_refno)
				{
					$ai_rate = $row->ai1_rate;
					$type = '0';
				} elseif($row->ai2_refno == $ai_refno) {
					$ai_rate = $row->ai2_rate;
					$type = '1';
				}
				$this->db->select('CONCAT(AI_LName, \', \', AI_FName) as name', false);
				$this->db->from('agent');
				$this->db->where('AI_RefNo', $ai_refno);
				$this->db->limit(1);
				$agent = $this->db->get();
				
				if($agent->num_rows() > 0)
				{
					$agent = $agent->row();
					$ai_name = $agent->name;
				}
				
				$this->db->select('*');
				$this->db->from('tbl_commission');
				$this->db->where('ai_refno', $ai_refno);
				$this->db->where('ci_acctno', $row->CI_AcctNo);
				$this->db->where('lh_pn', $row->LH_PN);
				$this->db->where('status !=', 1);
				$q = $this->db->get();
				
				$process_date = null;
				if(!empty($q))
				{
					$res = $q->row();
					$process_date = $res->process_date;
				}
				
				$data[] = array(
					'lh_pn' => $row->LH_PN,
					'lh_loandate' => $row->LH_LoanDate,
					'ci_name' => $row->CI_Name,
					'lh_loantrans' => $row->LH_LoanTrans,
					'lh_terms' => $row->LH_Terms,
					'lh_monthlyamort' => $row->LH_MonthlyAmort,
					'lh_principal' => $row->LH_Principal,
					'ai_name' => $ai_name,
					'ai_rate' => $ai_rate,
					'agent_type' => $type,
					'ci_acctno' => $row->CI_AcctNo,
					'process_date' => $process_date
				);
			}
			return $data;
		}
		return false;
	}
	
	function fetch_rpcf($from_date, $to_date, $group, $database = '')
	{
		$this->db->select('refund_que.aid, refund_que.ci_name, refund_que.ci_bankbranch, refund_que.transdate, refund_que.transrefno, refund_que.refunddue, refund_que.remarks, refund_que.replenish_date', false);
		$this->db->from('refund_que');
		$this->db->join('client', 'client.CI_AcctNo = refund_que.ci_acctno');
		$this->db->where('refund_que.transdate >=', $from_date);
		$this->db->where('refund_que.transdate <=', $to_date);
		$this->db->where('refund_que.cvrf', 'rf');
		if($group == '1')
		{
			$ci_grp = $this->db->where('client.CI_Grp', 'N');
		} elseif($group == '2')
		{
			$ci_grp = $this->db->where('client.CI_Grp', 'O');
		}
		$this->db->order_by('refund_que.transrefno', 'DES');
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
	
	function test_input($data)
	{
		$data = trim($data);
		$data = stripslashes(str_replace('/', '', $data));
		$data = htmlspecialchars($data);
		return $data;
	}
}

?>