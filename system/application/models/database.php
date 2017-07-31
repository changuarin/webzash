<?php

class Database extends Model {
	
	function Database_model()
	{
		parent::Model();
	}
	
	function getID( $a, $b, $column )
	{
		$t = $this->db->query(
			"SELECT ID
			FROM ln_ldgr
			WHERE CI_AcctNo='$a'
			AND LH_PN='$b'
			AND LL_IsDeleted='0'
			AND ".$column."='1'
			ORDER BY ID DESC;
		");
		if($t->num_rows())
		{
			return $t->row()->ID+1;
		}else return 1;
	}
	
	function generateId($tableName, $columnName)
	{
		$query = $this->db->query('SELECT '.$columnName.' FROM '.$tableName.' ORDER BY '.$columnName.' DESC LIMIT 1;');
		return $query->row();
	}
	
	function getBranch() {
		$query = $this->db->query('SELECT code, value FROM parameter WHERE code="BRANCH"');
		return $query->row();
	}
	
	function getRates()
	{
		$query = $this->db->query('SELECT code, value FROM parameter WHERE code="RATES"');
		return $query->row();
	}
	
	function getCutOff()
	{
		$query = $this->db->query('SELECT code, value FROM parameter WHERE code="CUTOFF"');
		return $query->row();
	}
	
	function get_sr($fromDate, $toDate, $source)
	{
		$this->load->model('database');
		
		$current_branch = $this->db->database;
		
		$fromDate = date('Y-m-d', strtotime($fromDate));
		$toDate = date('Y-m-d', strtotime($toDate));
		
		$q = $this->db->query("
			SELECT payee,
				ckno,
				bankid,
				pnno
			FROM tbl_cvheadr 
			WHERE cvtype = 'S'
			AND cvdate >= '$fromDate'
			AND cvdate <= '$toDate'
			ORDER BY cvdate,
				ckno
			ASC;
		")->result_array();
		
		$data = array();
		if( count($q) )
		{
			foreach($q as $cv)
			{
				$database = '';
				
				$pnno = str_replace(';', '', $cv['pnno']);
				$columns = 'LH_LoanDate, CI_Name, LH_PaymentType, LH_LoanTrans, LH_BankAmt, LH_MonthlyAmort, LH_Terms, LH_Rate, LH_Principal, LH_ProcFee, LH_CollFee, LH_OBC, LH_NetProceeds, LH_BankBranch, LH_StartDate, LH_EndDate, LH_LoanType';
				
				// Check branch_transfer table for completed sales transfer
				/*$this->db->select('frombranch', false);
				$this->db->from('nhgt_master.branch_transfer');
				$this->db->where('tobranch', $current_branch);
				$this->db->where('status', 'done');
				$this->db->where('refid', $cv['payee'] . '.' . $pnno);
				$this->db->where('reftype', 'sales');
				$query = $this->db->get();
				
				
				if($query->num_rows() > 0)
				{
					$database = 'nhgt_' . $query->row()->frombranch;
				}*/
				
				$pn = $this->database->fetch_loan_details($cv['payee'], $pnno, $columns, $database);
				
				if($pn)
				{
					if($pn->LH_LoanType == $source)
					{
						$data[] = array(
							'LH_LoanDate'		=> $pn->LH_LoanDate,
							'CI_Name'			=> $pn->CI_Name,
							'LH_PaymentType'	=> $pn->LH_PaymentType,
							'LH_LoanTrans'		=> $pn->LH_LoanTrans,
							'LH_BankAmt'		=> $pn->LH_BankAmt,
							'LH_MonthlyAmort'	=> $pn->LH_MonthlyAmort,
							'LH_Terms'			=> $pn->LH_Terms,
							'LH_Rate'			=> $pn->LH_Rate,
							'LH_Principal'		=> $pn->LH_Principal,
							'LH_ProcFee'		=> $pn->LH_ProcFee,
							'LH_CollFee'		=> $pn->LH_CollFee,
							'LH_OBC'			=> $pn->LH_OBC,
							'LH_NetProceeds'	=> $pn->LH_NetProceeds,
							'LH_BankBranch'		=> $pn->LH_BankBranch,
							'LH_StartDate'		=> $pn->LH_StartDate,
							'LH_EndDate'		=> $pn->LH_EndDate,
							'ckno'				=> $cv['ckno'],
							'bankid'			=> $cv['bankid'],
							'ckno'				=> $cv['ckno']
						);
					}
				}
			}
		}
		
		return $data;
	}

	function get_c_detail( $type, $cid )
	{
		switch ($type)
		{
			case 'name_source':

				$qt = $this->db->query(
					"SELECT CONCAT(CI_LName, ', ', CI_FName, ' ', CI_MName) AS fullname,
						CI_Source AS source
					FROM client
					WHERE CI_AcctNo='$cid';
				");

				$dets[0] = '';
				$dets[1] = '';
				if($qt->num_rows()):
					$dets[0] = $qt->row()->fullname;
					$dets[1] = $qt->row()->source;
				endif;
				return $dets;

				break;

			case 'bankbranch_day':

				$qt = $this->db->query(
					"SELECT CP_BankBranch, CP_WithdrawalDay
					FROM client_pension
					WHERE CI_AcctNo='$cid';
				");

				$pendetails[0] = '';
				$pendetails[1] = '';
				if($qt->num_rows()):
					$pendetails[0] = $qt->row()->CP_BankBranch;
					$pendetails[1] = $qt->row()->CP_WithdrawalDay;
				endif;

				return $pendetails;
				
				break;
			
			case 'pentype':

				$qt = $this->db->query(
					"SELECT CP_PensionType
					FROM client_pension
					WHERE CI_AcctNo='$cid';
				");

				$pentype = '';
				if($qt->num_rows()):
					$pensiontype = $qt->row()->CP_PensionType;
				endif;

				return $pentype;
				
				break;
				
			case 'wday':
				
				break;
			
		}
	}

	function get_ors($date, $source, $user, $debit_type)
	{
		$users = '';
		if($user!=''&&$user!='a')
		{
			$t = explode(',',$user);
			
			foreach ($t as $v)
			{
				$users .= "'$v',";	
			}
			$users = 'AND encby IN ('.substr($users, 0, strlen($users)-1).')
			';
		}
		
		$debit = '';
		if($debit_type == '1') {
			$debit = 'AND tracerefno=\'ADA\'';
		} elseif($debit_type == '2') {
			$debit = 'AND tracerefno!=\'ADA\'';
		}
		
		$q = $this->db->query("
			SELECT cid,
				orprno, 
				tracerefno,
				atmbegbal,
				amtdrawn,
				atmendbal,
				directpaid,
				amtdrawn - directpaid AS netdue,
				banktopost
			FROM collection_entry
			WHERE duedate = '$date'
			AND orprtype='OR'
			$debit
			$users
			ORDER BY orprno ASC
		")->result_array();

		$data = array();
		if( count($q) ):

			foreach($q as $v):

				$det1 = $this->get_c_detail( 'name_source', $v['cid'] );

				$det2 = $this->get_c_detail( 'bankbranch_day', $v['cid'] );

				$passed = FALSE;
				if($source=='0')
				{
					$passed = TRUE;
				}elseif( $source=='OTHERS' && $det1[1]=='' ||
						$source=='OTHERS' && $det1[1]==$source )
				{
					$passed = TRUE;
				}elseif( $source=='SSS' && $det1[1]=='SSS' ||
						$source=='SSS' && $det1[1]=='PVAO' )
				{
					$passed = TRUE;
				}elseif( $source=='GSIS' && $det1[1]=='GSIS' )
				{
					$passed = TRUE;
				}

				if($passed):
				
					// Check if banktopost is a BDO POS bankcode and set charge value to 10
					$charge = 0;
					if($v['banktopost'] == '1031' || $v['banktopost'] == '3017' || $v['banktopost'] == '1046' || $v['banktopost'] == '1065' || $v['banktopost'] == '1075' || $v['banktopost'] == '1087' || $v['banktopost'] == '1052' || $v['banktopost'] == '1084' || $v['banktopost'] == '1055')
					{
						$charge = 10;
					}
					
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

	function get_prs($date, $source, $user, $prno, $debit_type)
	{
		$users = '';
		if($user!=''&&$user!='a')
		{
			$t = explode(',',$user);
			
			foreach ($t as $v)
			{
				$users .= "'$v',";	
			}
			$users = 'AND encby IN ('.substr($users, 0, strlen($users)-1).')
			';
		}
		
		$pr = '';
		if($prno != '0')
		{
			$pr = 'AND orprno="'.$prno.'"';
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
			AND a.cid=b.CI_AcctNo
			AND a.orprtype='PR'
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
				} elseif($source == 'OTHERS' && $det1[1] == '' || $source == 'OTHERS' && $det1[1] == $source) {
					$passed = TRUE;
				} elseif ($source=='SSS' && $det1[1]=='SSS' || $source == 'SSS' && $det1[1] == 'PVAO') {
					$passed = TRUE;
				} elseif ($source == 'GSIS' && $det1[1] == 'GSIS') {
					$passed = TRUE;
				}
				
				// Check if banktopost is a BDO POS bankcode and set charge value to 10
				$charge = 0;
				if($v['banktopost'] == '1031' || $v['banktopost'] == '1046' || $v['banktopost'] == '1052' || $v['banktopost'] == '1065' || $v['banktopost'] == '1075' || $v['banktopost'] == '1084' || $v['banktopost'] == '1087' || $v['banktopost'] == '3017' || $v['banktopost'] == '1055')
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

	function get_ocr($date, $ocType)
	{
		$type = '';
		switch($ocType)
		{
			case '0':
				$type = '';
				break;
			case 'BC':
				$type = "AND narration LIKE '%BANK CHARGE%'";
				break;
			case 'RD':
				$type = "AND narration REGEXP 'REDEPOSIT|PETTY CASH'";
				break;
			case 'RC':
				$type = "AND narration LIKE '%REMITTANCE CHARGE%'";
				break;
		}
		
		$q = $this->db->query("
			SELECT *
			FROM entries
			WHERE entry_type=1
			AND reference!=''
			".$type."
			AND date='".date('Y-m-d', strtotime($date))."';
		");
		
		if($q->num_rows()) return $q->result_array();
	}

	function get_dr($fromDate, $toDate, $bankacct, $cvtype)
	{
		$fromDate = date('Y-m-d', strtotime($fromDate));
		$toDate = date('Y-m-d', strtotime($toDate));
		
		if($bankacct!='0')
		{
			$bank = "AND bankid='".$bankacct."'";
		} elseif($bankacct=='0') {
			$bank = "";
		}
		if($cvtype!='0')
		{
			$type = "AND cvtype='".$cvtype."'";
		} elseif($cvtype=='0') {
			$type = "";
		}
		
		$q = $this->db->query("
			SELECT cvno,
				payee,
				ckno,
				cvdate,
				ckamount,
				cvtype,
				bankid,
				remarks
			FROM tbl_cvheadr
			WHERE DATE_FORMAT(cvdate,'%Y-%m-%d') >= '$fromDate'
			AND DATE_FORMAT(cvdate,'%Y-%m-%d') <= '$toDate'
			".$bank."
			".$type."
			ORDER BY ckno
			ASC;
		");
		if($q->num_rows()) return $q->result_array();
	}
	
	function get_abr($fromDate, $toDate)
	{
		$q = $this->db->query("
			SELECT cvno,
				payee,
				ckno,
				cvdate,
				ckamount,
				cvtype,
				remarks
			FROM tbl_cvheadr
			WHERE DATE_FORMAT(cvdate,'%Y-%m-%d') >= '$fromDate'
			AND DATE_FORMAT(cvdate,'%Y-%m-%d') <= '$toDate'
			AND remarks='ADVANCE BONUS'
			ORDER BY cvdate, cvno
			ASC;
		");
		if($q->num_rows()) return $q->result_array();
	}
	
	function get_ncr($fromDate, $toDate)
	{
		$this->load->model('database');
	
		$fromDate = date('Y-m-d', strtotime($fromDate));
		$toDate = date('Y-m-d', strtotime($toDate));
		
		$q = $this->db->query("
			SELECT LH_LoanDate,
				CI_Name,
				LH_PaymentType,
				LH_BankBranch,
				LH_BankAmt,
				LH_NetProceeds,
				LH_Terms,
				LH_Principal,
				CI_AcctNo
			FROM ln_hdr 
			WHERE LH_LoanTrans='NEW'
			AND LH_LoanDate >= '$fromDate'
			AND LH_LoanDate <= '$toDate'
			AND LH_IsPending = 0
			AND LH_Cancelled = 0
			AND LH_Processed = 1
			ORDER BY LH_LoanDate,
				CI_Name
			ASC;
		");
		
		if($q->num_rows()) return $q->result_array();
	}
	
	function get_rtr($fromDate, $toDate)
	{
		$this->load->model('database');
		
		$fromDate = date('Y-m-d', strtotime($fromDate));
		$toDate = date('Y-m-d', strtotime($toDate));
		
		$q = $this->db->query("
			SELECT LH_LoanDate,
				CI_Name,
				LH_PaymentType,
				LH_BankBranch,
				LH_BankAmt,
				LH_NetProceeds,
				LH_Terms,
				LH_Principal,
				CI_AcctNo
			FROM ln_hdr 
			WHERE LH_LoanTrans='RET'
			AND LH_LoanDate >= '$fromDate'
			AND LH_LoanDate <= '$toDate'
			AND LH_IsPending = 0
			AND LH_Cancelled = 0
			AND LH_Processed = 1
			ORDER BY LH_LoanDate,
				CI_Name
			ASC;
		");
		
		if($q->num_rows()) return $q->result_array();
	}
	
	function get_bname($bcode)
	{
		switch($bcode)
		{
			case 'AB':
				$bname = 'Alabang';
				break;
			case 'ALB':
				$bname = 'Alaminos';
				break;
			case 'BB':
				$bname = 'Baguio';
				break;
			case 'BCB':
				$bname = 'Baclaran';
				break;
			case 'BGB':
				$bname = 'Bangued';
				break;
			case 'BLB':
				$bname = 'Balagtas';
				break;
			case 'BNVB':
				$bname = 'Bambang';
				break;
			case 'BTB':
				$bname = 'Batangas';
				break;
			case 'CISB':
				$bname = 'Candon';
				break;
			case 'DB':
				$bname = 'Divisoria';
				break;
			case 'DGB':
				$bname = 'Dagupan';
				break;
			case 'LGB':
				$bname = 'Legazpi';
				break;
			case 'LTB':
				$bname = 'La Trinidad';
				break;
			case 'LUB':
				$bname = 'La Union';
				break;
			case 'NB':
				$bname = 'Novaliches';
				break;
			case 'NGB':
				$bname = 'Naga';
				break;
			case 'PBB':
				$bname = 'Bontoc';
				break;
			case 'PLB':
				$bname = 'Lagawe';
				break;
			case 'RIB':
				$bname = 'Roxas';
				break;
			case 'SJ':
				$bname = 'San Juan';
				break;
			case 'SNVB':
				$bname = 'Solano';
				break;
			case 'SPB':
				$bname = 'San Pablo';
				break;
			case 'STGB':
				$bname = 'Santiago';
				break;
			case 'SZB':
				$bname = 'Zambales';
				break;
			case 'TKB':
				$bname = 'Tabuk';
				break;
			case 'VGB':
				$bname = 'Vigan';
				break;
		}
		return $bname;
	}
	
	function fetch_loan_details($acctno, $pnno, $columns, $database)
	{
		$this->db->select($columns);
		$this->db->from($database . 'ln_hdr');
		$this->db->where('CI_AcctNo', $acctno);
		$this->db->where('LH_PN', $pnno);
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
	
	function fetch_client_details($acctno, $columns, $database='')
	{
		$this->db->select($columns);
		$this->db->from($database.'client');
		$this->db->where('CI_AcctNo', $acctno);
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			$data = $query->row();
			return $data;
		}
		return false;
	}
	
	function insert_bill($column, $billdate)
	{
		$principal = $column[2] * $column[3];
		if($column[1]== 'ADVANCE_BONUS')
		{
			$billdate = date('Y-12-01', strtotime($column[14]));
		} else {
			$yrmo = explode('.', $billdate);
			$blldt = date('Y-m-t', strtotime($yrmo[0].'-'.$yrmo[1].'-01'));
			$intdate = intval(date('d', strtotime($blldt)));
			if($column[3]>$intdate)
			{
				$billdate = $yrmo[0].'-'.$yrmo[1].'-'.$intdate;
				$billdate =date('Y-m-d', strtotime($billdate));
			} else {
				$billdate = $yrmo[0].'-'.$yrmo[1].'-'.$column[3];
				$billdate =date('Y-m-d', strtotime($billdate));
			}
		}
		
		$data = array(
			'billtype' 		=> 'manual',
			'branchcode' 	=> $column[13],
			'CI_AcctNo' 	=> $column[0],
			'LH_PN' 		=> $column[1],
			'loantrans' 	=> $column[2],
			'billdate' 		=> $billdate,
			'name' 			=> $column[4],
			'paytype' 		=> $column[5],
			'bankacctno' 	=> $column[6],
			'bankbranch' 	=> $column[7],
			'pentype' 		=> $column[15],
			'duration' 		=> date('F Y', strtotime($column['8'])).' - '.date('F Y', strtotime($column['9'])),
			'terms' 		=> $column[10],
			'balance' 		=> $column[11],
			'amtodrawn' 	=> $column[12],
			'generateby' 	=> $this->session->userdata('user_name'),
			'dategenerate' 	=> date('Y-m-d H:i:s'),
			'collectby' 	=> '',
			'datecollected' => date('0000-00-00 00:00:00')
		);
		$this->db->insert('nhgt_bills.header', $data) or die($this->db->_error_message());
	}
	
	function collection_update($post)
	{
		$this->load->helper('code');
		
		$datas = array(
			'paytype'	 => $post['ptype'],
			'tracerefno' => $post['traceno'],
			'atmbegbal'  => n($post['begbal']),
			'amtdrawn'	 => n($post['amtdrawn']),
			'atmendbal'  => n($post['endbal']),
			'directpaid' => $post['rfw']?n($post['rfw']):'0.00',
			'duedate'	 => date('Y-m-d', strtotime($post['duedate'])),
			'orprtype'	 => $post['orprtype'],
			'orprno'	 => $post['orprno'],
			'banktopost' => $post['bank'],
			'receipt_image' => $post['receiptimg']
		);

		$this->db->where('uid', $post['uid']);
		$this->db->where('cid', $post['cid']);
		$this->db->update('collection_entry', $datas) or die( print_r($datas) );
		
		$ip = $_SERVER['REMOTE_ADDR'];
		
		$larray = array(
			'date'  	 	=> date('Y-m-d H:i:s'),
			'level' 	 	=> 1,
			'host_ip'		=> $ip,
			'user'		 	=> $this->session->userdata('user_name'),
			'url'		 	=> '/collection/adjform',
			'user_agent' 	=> $this->session->userdata('user_agent'),
			'message_title'	=> 'Updated collection_entry [uid:'.$post['uid'].']',
			'message_desc'	=> ''
		);
		$this->db->insert('logs', $larray) or die( $this->db->_error_message() );
		
		return array(
			'isOkay' => TRUE,
			'script' => trims("
				window.close();opener.location.reload();
			")
		);
	}
}
?>
