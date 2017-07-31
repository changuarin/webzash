<?

class Report_model extends Model {
	
	function Report_model()
	{
		parent::Model();
	}
	
	function fetch_atmpb_release($from_date, $to_date)
	{
		$this->db->select('AP_Date as ap_date, AP_PBNo as ap_pbno, AP_Name as ap_name, AP_Bank as ap_bank, AP_TypePension as ap_typepension, AP_OtherRemarks as ap_otherremarks', false);
		$this->db->from('atm_pb_history');
		$this->db->where('AP_Date >=', $from_date);
		$this->db->where('AP_Date <=', $to_date);
		$this->db->order_by('AP_Date', 'ASC');
		$this->db->order_by('AP_PBNo', 'ASC');
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
	
	function fetch_rpcf($rpcf_date, $group, $database = '')
	{
		$this->db->select('refund_que.ci_name, refund_que.ci_bankbranch, refund_que.transdate, refund_que.transrefno, refund_que.refunddue, refund_que.remarks', false);
		$this->db->from('refund_que');
		$this->db->join('client', 'client.CI_AcctNo = refund_que.ci_acctno');
		$this->db->where('refund_que.replenish_date', $rpcf_date);
		$this->db->where('refund_que.cvrf', 'rf');
		if($group == '1')
		{
			$ci_grp = $this->db->where('client.CI_Grp', 'N');
		} elseif($group == '2')
		{
			$ci_grp = $this->db->where('client.CI_Grp', 'O');
		}
		$this->db->order_by('refund_que.transrefno', 'DESC');
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			$i = 0;
			foreach($query->result() as $row)
			{
				$data[] = $row;
				$i++;
			}
			
			$this->db->select('nhgt_bangued.refund_que.ci_name, nhgt_bangued.refund_que.ci_bankbranch, nhgt_bangued.refund_que.transdate, nhgt_bangued.refund_que.transrefno, nhgt_bangued.refund_que.refunddue, nhgt_bangued.refund_que.remarks', false);
			$this->db->from('nhgt_bangued.refund_que');
			$this->db->join('nhgt_bangued.client', 'nhgt_bangued.client.CI_AcctNo = nhgt_bangued.refund_que.ci_acctno');
			$this->db->where('nhgt_bangued.refund_que.ci_acctno', 'BGB-2007-04-19-028');
			$this->db->where('nhgt_bangued.refund_que.replenish_date', $rpcf_date);
			$this->db->where('nhgt_bangued.refund_que.cvrf', 'rf');
			if($group == '1')
			{
				$ci_grp = $this->db->where('nhgt_bangued.client.CI_Grp', 'N');
			} elseif($group == '2')
			{
				$ci_grp = $this->db->where('nhgt_bangued.client.CI_Grp', 'O');
			}
			$this->db->order_by('nhgt_bangued.refund_que.transrefno', 'DESC');
			$query = $this->db->get();
			
			if($query->num_rows() > 0)
			{
				foreach($query->result() as $row)
				{
					$data[$i] = $row;
				}
			}
			return $data;
		}
		return false;
	}
	
	function fetch_agent_comm($ai_refno, $added_date)
	{
		$this->db->select('agent_type, lh_pn, loan_date, client_name, lh_monthlyamort, terms, loan_type, cfp_amount, cb_amount', false);
		$this->db->from('nhgt_master.tbl_commission');
		$this->db->where('ai_refno', $ai_refno);
		$this->db->where('added_date', $added_date);
		$this->db->order_by('loan_date', 'DESC');
		$this->db->order_by('client_name', 'ASC');
		
		$query = $this->db->get() or die($this->db->_error_message());
		
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
	
	function fetch_commission($start_date, $end_date, $agent_type)
	{
		$this->db->select('client.CI_AcctNo, ln_hdr.LH_PN, ln_hdr.LH_LoanDate, ln_hdr.CI_Name, ln_hdr.LH_Terms, ln_hdr.LH_LoanTrans, ln_hdr.LH_MonthlyAmort, ln_hdr.LH_Principal, ln_hdr.LH_Agent1 as ai1_refno, client.CI_Agent1_Rate as ai1_rate, ln_hdr.LH_Agent2 as ai2_refno, client.CI_Agent2_Rate as ai2_rate', false);
		$this->db->from('ln_hdr');
		$this->db->join('client', 'client.CI_AcctNo = ln_hdr.CI_AcctNo');
		if($agent_type == '0') {
			$this->db->where('ln_hdr.LH_LoanTrans !=', 'SPEC');
		} elseif($agent_type == '1') {
			$this->db->where('ln_hdr.LH_LoanTrans', 'NEW');
		}
		$this->db->where('ln_hdr.LH_LoanDate >=', $start_date);
		$this->db->where('ln_hdr.LH_LoanDate <=', $end_date);
		$this->db->where('ln_hdr.LH_LoanType', 'PEN');
		$this->db->order_by('ln_hdr.LH_LoanDate', 'ASC');
		$this->db->order_by('ln_hdr.CI_Name', 'ASC');
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				if($agent_type == '0')
				{
					$ai_refno = $row->ai1_refno;
					$ai_rate = $row->ai1_rate;
					$type = '0';
				} elseif($agent_type == '1') {
					$ai_refno = $row->ai2_refno;
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
					'ci_acctno' => $row->CI_AcctNo
				);
			}
			return $data;
		}
		return false;
	}
	
	function fetch_collection_pr($date, $source, $user, $prno, $debit_type)
	{
		$users = '';
		if($user != '' && $user != 'a')
		{
			$t = explode(',', $user);
			
			foreach ($t as $v)
			{
				$users .= "'$v',";	
			}
			$users = 'AND encby IN (' . substr($users, 0, strlen($users)-1) . ')
			';
		}
		
		$pr = '';
		if($prno != '0')
		{
			$pr = 'AND orprno="' . $prno . '"';
		}
		
		$debit = '';
		if($debit_type == '1') {
			$debit = 'AND tracerefno=\'ADA\'';
		} elseif($debit_type == '2') {
			$debit = 'AND tracerefno!=\'ADA\'';
		}
		
		$q = $this->db->query("
			SELECT a.cid,
				a.orprno, 
				a.tracerefno,
				a.atmbegbal,
				a.amtdrawn,
				a.atmendbal,
				a.directpaid,
				a.amtdrawn - a.directpaid AS netdue,
				CONCAT(b.CI_LName, ', ', b.CI_FName, ' ', b.CI_MName) as name,
				a.banktopost
			FROM collection_entry a, client b
			WHERE a.duedate = '$date'
			AND a.cid = b.CI_AcctNo
			AND a.orprtype = 'PR'
			$debit
			$users
			$pr
			ORDER BY name ASC
		")->result_array();

		$data = array();
		if( count($q) ):

			foreach($q as $v):

				$det1 = $this->get_c_detail( 'name_source', $v['cid'] );

				$det2 = $this->get_c_detail( 'bankbranch_day', $v['cid'] );

				$passed = FALSE;
				if($source == '0')
				{
					$passed = TRUE;
				} elseif ($source == 'OTHERS' && $det1[1] == '' || $source == 'OTHERS' && $det1[1] == $source) {
					$passed = TRUE;
				} elseif($source == 'SSS' && $det1[1] == 'SSS' || $source == 'SSS' && $det1[1] == 'PVAO') {
					$passed = TRUE;
				} elseif($source == 'GSIS' && $det1[1] == 'GSIS') {
					$passed = TRUE;
				}
				
				$charge = 0;
				if($v['banktopost'] == '1031')
				{
					$charge = 10;
				}

				if($passed):

					$data[] = array(
						'cid'			=> $v['cid'],
						'orprno'		=> $v['orprno'],
						'fullname'		=> $det1[0],
						'bankbranch'	=> $det2[0],
						'tracerefno'	=> $v['tracerefno'],
						'atmbegbal'		=> $v['atmbegbal'],
						'atmendbal'		=> $v['atmendbal'],
						'amtdrawn'		=> $v['amtdrawn'],
						'atmendbal'		=> $v['atmendbal'],
						'directpaid'	=> $v['directpaid'],
						'netdue'		=> $v['netdue'],
						'wday'			=> $det2[1],
						'charge'		=> $charge
					);

				endif;

			endforeach;

		endif;

		return $data;
	}
	
	function fetch_autodebit_coll($from_date, $to_date, $database = '')
	{
		$this->db->select('client.CI_AcctNo, collection_entry.cid, collection_entry.orprno, collection_entry.tracerefno, collection_entry.atmbegbal, collection_entry.amtdrawn, collection_entry.directpaid, collection_entry.amtdrawn - collection_entry.directpaid AS netdue, CONCAT(client.CI_LName, \', \', client.CI_FName, \' \', client.CI_MName) AS name, collection_entry.duedate as duedate', false);
		$this->db->from('collection_entry');
		$this->db->join('client', 'collection_entry.cid = client.CI_AcctNo');
		$this->db->where('collection_entry.duedate >=', date('Y-m-d', strtotime($from_date)));
		$this->db->where('collection_entry.duedate <=', date('Y-m-d', strtotime($to_date)));
		$this->db->where('collection_entry.tracerefno', 'ADA');
		$this->db->order_by('collection_entry.duedate', 'ASC');
		$this->db->order_by('name', 'ASC');
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$bankbranch = '';
				$wday = 1;
				
				$this->db->select('CP_BankBranch as bankbranch, CP_WithdrawalDay as wday');
				$this->db->from('client_pension');
				$this->db->where('CI_AcctNo', $row->CI_AcctNo);
				$this->db->where('CP_IsDeleted', 0);
				$q = $this->db->get();
				
				if($q->num_rows() > 0)
				{
					$res = $q->row();
					$bankbranch = $res->bankbranch;
					$wday = $res->wday;
				}
				
				$data[] = array(
					'cid'		 => $row->cid,
					'orprno'	 => $row->orprno,
					'fullname'	 => $row->name,
					'bankbranch' => $bankbranch,
					'tracerefno' => $row->tracerefno,
					'atmbegbal'	 => $row->atmbegbal,
					'atmendbal'	 => 0,
					'amtdrawn'	 => $row->amtdrawn,
					'directpaid' => $row->directpaid,
					'netdue'	 => $row->netdue,
					'wday'		 => $wday,
					'duedate'	 => $row->duedate
				);
			}
			return $data;
		}
		return false;
	}
	
	function fetch_bank_charge() {
		
	}
	
	function fetch_mosalessum($month, $year, $reporttype)
	{
		// Get all branch names
		$this->db->select('Branch_Name as branchname');
		$this->db->from('nhgt_master.branch');
		$this->db->order_by('Branch_Code', 'ASC');
		$query = $this->db->get();
		
		$startdate = date('Y-m-01', strtotime($year . '-' . $month . '-01'));
		$enddate = date('Y-m-t', strtotime($year . '-' . $month . '-01'));
		
		$timestring = strtotime($startdate);
		$startdate2 = date('Y-m-01', strtotime('-1 month', $timestring));
		$enddate2 = date('Y-m-t', strtotime('-1 month', $timestring));
		
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$branchname = strtolower($row->branchname);
				$database = 'nhgt_' . str_replace(' ', '', $branchname);
				
				if($reporttype == '1')
				{
					$atmpbh_total = 0;
					$nc_total = 0;
					$ret_total = 0;
					
					// Get the total number of new clients
					$this->db->select('*');
					$this->db->from($database . '.ln_hdr');
					$this->db->where('LH_LoanType', 'PEN');
					$this->db->where('LH_LoanTrans', 'NEW');
					$this->db->where('LH_LoanDate >=', $startdate);
					$this->db->where('LH_LoanDate <=', $enddate);
					$this->db->where('LH_IsPending', 0);
					$this->db->where('LH_Processed', 1);
					$this->db->where('LH_Cancelled', 0);
					$this->db->order_by('LH_LoanDate', 'ASC');
					$query_nc = $this->db->get();
					
					$nc_total += $query_nc->num_rows();
					
					// Get the total number of new clients
					$this->db->select('*');
					$this->db->from($database . '.ln_hdr');
					$this->db->where('LH_LoanType', 'PEN');
					$this->db->where('LH_LoanTrans', 'RET');
					$this->db->where('LH_LoanDate >=', $startdate);
					$this->db->where('LH_LoanDate <=', $enddate);
					$this->db->where('LH_IsPending', 0);
					$this->db->where('LH_Processed', 1);
					$this->db->where('LH_Cancelled', 0);
					$this->db->order_by('LH_LoanDate', 'ASC');
					$query_ret = $this->db->get();
					$ret_total = $query_ret->num_rows();
					
					// Get the total number of released clients
					$this->db->select('*');
					$this->db->from($database . '.atm_pb_history');
					$this->db->where('AP_Date >=', $startdate);
					$this->db->where('AP_Date <=', $enddate);
					$query_atmpbh = $this->db->get();
					
					$atmpbh_total += $query_atmpbh->num_rows();
					
					$data[] = array(
						'branchname'  => $branchname,
						'atmpbhtotal' => $atmpbh_total,
						'nctotal'	  => $nc_total,
						'rettotal'	  => $ret_total
					);
				} elseif($reporttype == '2') {
					$netproceeds1 = 0;
					$netproceeds2 = 0;
					$principal1 = 0;
					$principal2 = 0;
					
					// Get the total sales for the month
					$this->db->select('payee, pnno');
					$this->db->from($database . '.tbl_cvheadr');
					$this->db->where('cvtype', 'S');
					$this->db->where('cvdate >=', $startdate);
					$this->db->where('cvdate <=', $enddate);
					$this->db->where('remarks !=', 'CANCELLED');
					$this->db->order_by('cvdate', 'ASC');
					$query_cvhdr = $this->db->get();
					
					if($query_cvhdr->num_rows() > 0)
					{
						foreach($query_cvhdr->result() as $cvhdr)
						{
							// Check branch_transfer table for refund
							$current_branch = str_replace('nhgt_', '', $database);
							
							$ci_acctno = $cvhdr->payee;
							$pnno = str_replace(';', '', $cvhdr->pnno);
							
							$this->db->select('frombranch');
							$this->db->from('nhgt_master.branch_transfer');
							$this->db->where('tobranch', $current_branch);
							$this->db->where('status', 'done');
							$this->db->where('refid', $ci_acctno . '.' . $pnno);
							$this->db->where('reftype', 'sales');
							$query_bt = $this->db->get();
							
							if($query_bt->num_rows() > 0)
							{
								$database = 'nhgt_' . $query_bt->row()->frombranch;
							}
							
							$this->db->select('LH_Principal as principal1, LH_NetProceeds as netproceeds1');
							$this->db->from($database . '.ln_hdr');
							$this->db->where('CI_AcctNo', $ci_acctno);
							$this->db->where('LH_PN', $pnno);
							$this->db->where('LH_LoanTrans !=', 'SPEC');
							$this->db->where('LH_LoanType', 'PEN');
							$this->db->where('LH_IsPending', 0);
							$this->db->where('LH_Processed', 1);
							$this->db->where('LH_Cancelled', 0);
							$query_lnhdr = $this->db->get();
							
							if($query_lnhdr->num_rows() > 0)
							{
								$netproceeds1 += $query_lnhdr->row()->netproceeds1;
								$principal1 += $query_lnhdr->row()->principal1;
							}
						}
					}
					
					// Get the total sales for the month
					$this->db->select('payee, pnno');
					$this->db->from($database . '.tbl_cvheadr');
					$this->db->where('cvtype', 'S');
					$this->db->where('cvdate >=', $startdate2);
					$this->db->where('cvdate <=', $enddate2);
					$this->db->where('remarks !=', 'CANCELLED');
					$this->db->order_by('cvdate', 'ASC');
					$query_cvhdr = $this->db->get();
					
					if($query_cvhdr->num_rows() > 0)
					{
						foreach($query_cvhdr->result() as $cvhdr)
						{
							// Check branch_transfer table for refund
							$current_branch = str_replace('nhgt_', '', $database);
							
							$ci_acctno = $cvhdr->payee;
							$pnno = str_replace(';', '', $cvhdr->pnno);
							
							$this->db->select('frombranch');
							$this->db->from('nhgt_master.branch_transfer');
							$this->db->where('tobranch', $current_branch);
							$this->db->where('status', 'done');
							$this->db->where('refid', $ci_acctno . '.' . $pnno);
							$this->db->where('reftype', 'sales');
							$query_bt = $this->db->get();
							
							if($query_bt->num_rows() > 0)
							{
								$database = 'nhgt_' . $query_bt->row()->frombranch;
							}
							
							$this->db->select('LH_Principal as principal2, LH_NetProceeds as netproceeds2');
							$this->db->from($database . '.ln_hdr');
							$this->db->where('CI_AcctNo', $ci_acctno);
							$this->db->where('LH_PN', $pnno);
							$this->db->where('LH_LoanTrans !=', 'SPEC');
							$this->db->where('LH_LoanType', 'PEN');
							$this->db->where('LH_IsPending', 0);
							$this->db->where('LH_Processed', 1);
							$this->db->where('LH_Cancelled', 0);
							$query_lnhdr = $this->db->get();
							
							if($query_lnhdr->num_rows() > 0)
							{
								$netproceeds2 += $query_lnhdr->row()->netproceeds2;
								$principal2 += $query_lnhdr->row()->principal2;
							}
						}
					}
					
					$data[] = array(
						'branchname'	=> $branchname,
						'principal1'	=> $principal1,
						'netproceeds1'	=> $netproceeds1,
						'principal2'	=> $principal2,
						'netproceeds2'	=> $netproceeds2
					);
				}
			}
			return $data;
		}
		return false;
	}
	
	function fetch_mocollsum($month, $year, $colltype)
	{
		// Get all branch names
		$this->db->select('Branch_Name as branchname');
		$this->db->from('nhgt_master.branch');
		$this->db->order_by('Branch_Name', 'ASC');
		$query = $this->db->get();
		
		$startdate = date('Y-m-01', strtotime($year . '-' . $month . '-01'));
		$enddate = date('Y-m-t', strtotime($year . '-' . $month . '-01'));
		
		$timestring = strtotime($startdate);
		$startdate2 = date('Y-m-01', strtotime('-1 month', $timestring));
		$enddate2 = date('Y-m-t', strtotime('-1 month', $timestring));
		
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$amtdrawn = 0;
				$atmbegbal = 0;
				$atmendbal = 0;
				$directpaid = 0;
				$net = 0;
				$netdue = 0;
				
				$branchname = strtolower($row->branchname);
				$database = 'nhgt_' . str_replace(' ', '', $branchname);
				
				$this->db->select('atmbegbal,
					amtdrawn,
					atmendbal,
					directpaid');
				$this->db->from($database . '.collection_entry');
				$this->db->where('duedate >=', $startdate);
				$this->db->where('duedate <=', $enddate);
				if($colltype == '1')
				{
					$this->db->where('orprtype', 'OR');
				} else {
					$this->db->where('orprtype', 'PR');
				}
				
				$query_ce = $this->db->get();
				
				if($query_ce->num_rows() > 0)
				{
					foreach($query_ce->result() as $row)
					{
						$amtdrawn += $row->amtdrawn;
						$atmbegbal += $row->atmbegbal;
						$atmendbal += $row->atmendbal;
						$directpaid += $row->directpaid;
						$net = $row->amtdrawn - $row->directpaid;
						$netdue += $net;
					}
					
					$data[] = array(
						'branchname' => $branchname,
						'atmbegbal'	 => $atmbegbal,
						'amtdrawn'	 => $amtdrawn,
						'atmendbal'	 => $atmendbal,
						'directpaid' => $directpaid,
						'netdue'	 => $netdue
					);
				}
			}
			return $data;
		}
		return false;
	}
	
	function fetch_cv_sales($startdate, $enddate, $source)
	{
		$current_branch = str_replace('nhgt_', '', $this->db->database);
		
		$this->db->select('payee, ckno, bankid, pnno');
		$this->db->from('tbl_cvheadr');
		$this->db->where('cvtype', 'S');
		$this->db->where('cvdate >=', $startdate);
		$this->db->where('cvdate <=', $enddate);
		$this->db->where('remarks !=', 'cancelled');
		$this->db->order_by('cvdate', 'ASC');
		$this->db->order_by('ckno', 'ASC');
		$query = $this->db->get();
		
		$data = array();
		
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$database = '';
				
				$ci_acctno = $row->payee;
				$pnno = str_replace(';', '', $row->pnno);
				
				// Check branch_transfer table for completed sales transfer
				$this->db->select('frombranch', false);
				$this->db->from('nhgt_master.branch_transfer');
				$this->db->where('tobranch', $current_branch);
				$this->db->where('status', 'done');
				$this->db->where('refid', $ci_acctno . '.' . $pnno);
				$this->db->where('reftype', 'sales');
				$query_bt = $this->db->get();
				
				if($query_bt->num_rows > 0)
				{
					$qbt = $query_bt->row();
					
					$database = 'nhgt_' . $qbt->frombranch . '.';
				}
				
				$columns = 'LH_LoanDate, CI_Name, LH_PaymentType, LH_LoanTrans, LH_BankAmt, LH_MonthlyAmort, LH_Terms, LH_Rate, LH_Principal, LH_ProcFee, LH_CollFee, LH_OBC, LH_PaymentTo, LH_AdvPayment, LH_NetProceeds, LH_BankBranch, LH_StartDate, LH_EndDate, LH_LoanType, LH_Agent1, LH_Agent2';
				$this->db->from($database . 'ln_hdr');
				$this->db->where('CI_AcctNo', $ci_acctno);
				$this->db->where('LH_PN', $pnno);
				$this->db->not_like('LH_LoanTrans', 'SPEC');
				$this->db->where('LH_IsPending', 0);
				$this->db->where('LH_Processed', 1);
				$this->db->where('LH_Cancelled', 0);
				$query_lnhdr = $this->db->get();
				
				if($query_lnhdr)
				{
					if($query_lnhdr->row()->LH_LoanType == $source)
					{
						$agent1_name = '';
						
						$this->db->select("CONCAT(AI_LName, ', ', AI_FName) as name", FALSE);
						$this->db->where('AI_RefNo', $query_lnhdr->row()->LH_Agent1);
						$this->db->where('AI_IsDeleted', 0);
						$agent1 = $this->db->get('agent');
						
						if($agent1->num_rows > 0)
						{
							$agent1_name = $agent1->row(0)->name;
						}
						
						$agent2_name = '';
						
						$this->db->select("CONCAT(AI_LName, ', ', AI_FName) as name", FALSE);
						$this->db->where('AI_RefNo', $query_lnhdr->row()->LH_Agent2);
						$this->db->where('AI_IsDeleted', 0);
						$agent2 = $this->db->get('agent');
						
						if($agent2->num_rows > 0)
						{
							$agent2_name = $agent2->row(0)->name;
						}
						
						$ob = $query_lnhdr->row()->LH_OBC + $query_lnhdr->row()->LH_AdvPayment + $query_lnhdr->row()->LH_PaymentTo;
						
						$data[] = array(
							'LH_LoanDate'			=> $query_lnhdr->row()->LH_LoanDate,
							'CI_Name'					=> $query_lnhdr->row()->CI_Name,
							'LH_PaymentType'	=> $query_lnhdr->row()->LH_PaymentType,
							'LH_LoanTrans'		=> $query_lnhdr->row()->LH_LoanTrans,
							'LH_BankAmt'			=> $query_lnhdr->row()->LH_BankAmt,
							'LH_MonthlyAmort'	=> $query_lnhdr->row()->LH_MonthlyAmort,
							'LH_Terms'				=> $query_lnhdr->row()->LH_Terms,
							'LH_Rate'					=> $query_lnhdr->row()->LH_Rate,
							'LH_Principal'		=> $query_lnhdr->row()->LH_Principal,
							'LH_ProcFee'			=> $query_lnhdr->row()->LH_ProcFee,
							'LH_CollFee'			=> $query_lnhdr->row()->LH_CollFee,
							'LH_OBC'					=> $ob,
							'LH_NetProceeds'	=> $query_lnhdr->row()->LH_NetProceeds,
							'LH_BankBranch'		=> $query_lnhdr->row()->LH_BankBranch,
							'LH_StartDate'		=> $query_lnhdr->row()->LH_StartDate,
							'LH_EndDate'			=> $query_lnhdr->row()->LH_EndDate,
							'ckno'						=> $row->ckno,
							'bankid'					=> $row->bankid,
							'ckno'						=> $row->ckno,
							'Agent1_Name'			=> $agent1_name,
							'Agent2_Name'			=> $agent2_name,
						);
					}
				}
			}
			return $data;
		}
		return false;
	}
	
	function fetch_disbursement($startdate, $enddate, $bankacct, $cvtype)
	{
		$current_branch = str_replace('nhgt_', '', $this->db->database);
		
		$this->db->select('cvno, payee, ckno, cvdate, ckamount, pnno, cvtype, bankid, remarks');
		$this->db->from('tbl_cvheadr');
		$this->db->where('cvdate >=', $startdate);
		$this->db->where('cvdate <=', $enddate);
		if($bankacct != '0')
		{
			$this->db->where('bankid', $bankacct);
		}
		if($cvtype != '0')
		{
			$this->db->where('cvtype', $cvtype);
		}
		$this->db->order_by('cvdate', 'ASC');
		$this->db->order_by('ckno', 'ASC');
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$database = '';
				$payee = $row->payee;
				
				if($row->cvtype == 'R' || $row->cvtype == 'S' || $row->remarks == 'ADVANCE BONUS' || $row->remarks == 'ADVANCE SSS INCREASE')
				{
					$ci_acctno = $row->payee;
					$pnno = $pnno = str_replace(';', '', $row->pnno);
					
					// Check branch_transfer table for completed sales transfer
					$this->db->select('frombranch', false);
					$this->db->from('nhgt_master.branch_transfer');
					$this->db->where('tobranch', $current_branch);
					$this->db->where('status', 'done');
					$this->db->where('refid', $ci_acctno . '.' . $pnno);
					$query_bt = $this->db->get();
					
					if($query_bt->num_rows > 0)
					{
						$qbt = $query_bt->row();
						
						$database = 'nhgt_' . $qbt->frombranch . '.';
					}
					
					$this->db->select("CONCAT(CI_LName, ', ', CI_FName, ' ', CI_MName) as name", false);
					$this->db->from($database . 'client');
					$this->db->where('CI_AcctNo', $row->payee);
					$this->db->where('CI_IsDeleted', 0);
					$query_client = $this->db->get();
					
					if($query_client->num_rows > 0)
					{
						$payee = $query_client->row()->name;
					}
				}
				
				$data[] = array(
					'cvno'	   => $row->cvno,
					'payee'    => $payee,
					'ckno'	   => $row->ckno,
					'cvdate'   => $row->cvdate,
					'ckamount' => $row->ckamount,
					'cvtype'   => $row->cvtype,
					'bankid'   => $row->bankid,
					'remarks'  => $row->remarks
				);
			}
			return $data;
		}
		return false;
	}
}

?>