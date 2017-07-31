<?php

class Collection extends Controller
{
	function Collection()
	{
		parent::Controller();
		
		date_default_timezone_set('Asia/Manila');
		$this->load->helper('code');
		$this->load->library('form_validation');
		$this->load->model('client_model');
		$this->load->model('collection_model');
		$this->load->model('master_model');
		$this->load->model('sales_model');
		return;
	}


	// 20150820 -> rsm -> OR Validation
	function validateOR()
	{
		$this->db->where('orprno', $_POST['a']);
		$this->db->where('orprtype', 'OR');
		$res = $this->db->get('collection_entry') or $err = $this->db->_error_message();
		if($res->num_rows()>0)
			$return = TRUE;
		else $return = FALSE;

		header('Content-type: application/json');

		die( json_encode( array( 
			'res' => $return,
			'eval' => 'alert("O.R. Number already exists.");$("#or").val("");'
		) ) );
	}


	function bnkntrypst()
	{
		$this->load->model('rsm');
		$this->load->helper('code');

		$this->db->trans_start();

			$lastentryid = $this->rsm->get_last_entryid();

			switch($_POST['c'])
			{
				// 20150827 -> rsm -> Re-Deposit Petty Cash Fund
				// With O.R. Number
				case'1011': // Petty Cash fund

					$entryinsert = array(
						'entry_id'  => $lastentryid,
						'ledger_id' => $this->rsm->chartaccount_id( $_POST['b'] ),
						'dc'        => 'D',
						'amount'    => n($_POST['d'])
					);
					$this->db->insert("entry_items", $entryinsert) or
					die('line: 57 '.$this->db->_error_message());

					$entryinsert = array(
						'entry_id'  => $lastentryid,
						'ledger_id' => $this->rsm->chartaccount_id( $_POST['c'] ),
						'dc'        => 'C',
						'amount'    => n($_POST['d'])
					);
					$this->db->insert("entry_items", $entryinsert) or
					die('line: 66 '.$this->db->_error_message());
					
					$datas = array(
						'id'		 => $lastentryid,
						'entry_type' => 1, // Cash receipt book
						'reference'  => $_POST['f'], // O.R. Number
						'number'     => $this->rsm->get_entry_number( 1 ),
						'date'       => date('Y-m-d H:i:s', strtotime($_POST['a'])),
						'dr_total'   => n($_POST['d']),
						'cr_total'   => n($_POST['d']),
						'narration'  => $_POST['e']
					);
					$this->db->insert("entries", $datas) or
					die('line: 79 '.$this->db->_error_message());

				break;
				// 20150820 -> rsm -> Remittance Charge
				// With O.R. Number
				case'6003': // Other Income

					$entryinsert = array(
						'entry_id'  => $lastentryid,
						'ledger_id' => $this->rsm->chartaccount_id( $_POST['b'] ),
						'dc'        => 'D',
						'amount'    => n($_POST['d'])
					);
					$this->db->insert("entry_items", $entryinsert) or
					die('line: 93 '.$this->db->_error_message());

					$entryinsert = array(
						'entry_id'  => $lastentryid,
						'ledger_id' => $this->rsm->chartaccount_id( $_POST['c'] ),
						'dc'        => 'C',
						'amount'    => n($_POST['d'])
					);
					$this->db->insert("entry_items", $entryinsert) or
					die('line: 102 '.$this->db->_error_message());
					
					$datas = array(
						'id'		 => $lastentryid,
						'entry_type' => 1, // Cash receipt book
						'reference'  => $_POST['f'], // O.R. Number
						'number'     => $this->rsm->get_entry_number( 1 ),
						'date'       => date('Y-m-d H:i:s', strtotime($_POST['a'])),
						'dr_total'   => n($_POST['d']),
						'cr_total'   => n($_POST['d']),
						'narration'  => $_POST['e']
					);
					$this->db->insert("entries", $datas) or
					die('line: 115 '.$this->db->_error_message());

				break;

				case'6001': // Interest on deposit

					$entryinsert = array(
						'entry_id'  => $lastentryid,
						'ledger_id' => $this->rsm->chartaccount_id( $_POST['b'] ),
						'dc'        => 'D',
						'amount'    => n($_POST['d'])
					);
					$this->db->insert("entry_items", $entryinsert) or
					die('line: 128 '.$this->db->_error_message());

					$entryinsert = array(
						'entry_id'  => $lastentryid,
						'ledger_id' => $this->rsm->chartaccount_id( $_POST['c'] ),
						'dc'        => 'C',
						'amount'    => n($_POST['d'])
					);
					$this->db->insert("entry_items", $entryinsert) or
					die('line: 137 '.$this->db->_error_message());
					
					$datas = array(
						'id'		 => $lastentryid,
						'entry_type' => 2,
						'number'     => $this->rsm->get_entry_number( 2 ),
						'date'       => date('Y-m-d H:i:s', strtotime($_POST['a'])),
						'dr_total'   => n($_POST['d']),
						'cr_total'   => n($_POST['d']),
						'narration'  => $_POST['e']
					);
					$this->db->insert("entries", $datas) or
					die('line: 149 '.$this->db->_error_message());

				break;

				case'5015': // bank charges

					$entryinsert = array(
						'entry_id'  => $lastentryid,
						'ledger_id' => $this->rsm->chartaccount_id( $_POST['c'] ),
						'dc'        => 'D',
						'amount'    => n($_POST['d'])
					);
					$this->db->insert("entry_items", $entryinsert) or
					die('line: 162 '.$this->db->_error_message());

					$entryinsert = array(
						'entry_id'  => $lastentryid,
						'ledger_id' => $this->rsm->chartaccount_id( $_POST['b'] ),
						'dc'        => 'C',
						'amount'    => n($_POST['d'])
					);
					$this->db->insert("entry_items", $entryinsert) or
					die('line: 171 '.$this->db->_error_message());
					
					$datas = array(
						'id'		 => $lastentryid,
						'entry_type' => 2,
						'number'     => $this->rsm->get_entry_number( 2 ),
						'date'       => date('Y-m-d H:i:s', strtotime($_POST['a'])),
						'dr_total'   => n($_POST['d']),
						'cr_total'   => n($_POST['d']),
						'narration'  => $_POST['e']
					);

					$this->db->insert("entries", $datas) or
					die('line: 184 '.$this->db->_error_message());

				break;

				case'1201': // refund NR Auto Debit Cash Card

					$entryinsert = array(
						'entry_id'  => $lastentryid,
						'ledger_id' => $this->rsm->chartaccount_id( $_POST['c'] ),
						'dc'        => 'D',
						'amount'    => n($_POST['d'])
					);
					$this->db->insert("entry_items", $entryinsert) or
					die('line: 197 '.$this->db->_error_message());

					$entryinsert = array(
						'entry_id'  => $lastentryid,
						'ledger_id' => $this->rsm->chartaccount_id( $_POST['b'] ),
						'dc'        => 'C',
						'amount'    => n($_POST['d'])
					);
					$this->db->insert("entry_items", $entryinsert) or
					die('line: 206 '.$this->db->_error_message());
					
					$datas = array(
						'id'		 => $lastentryid,
						'entry_type' => 2,
						'number'     => $this->rsm->get_entry_number( 2 ),
						'date'       => date('Y-m-d H:i:s', strtotime($_POST['a'])),
						'dr_total'   => n($_POST['d']),
						'cr_total'   => n($_POST['d']),
						'narration'  => $_POST['e']
					);

					$this->db->insert("entries", $datas) or
					die('line: 219 '.$this->db->_error_message());

				break;
			}

		$entrynumber = $this->rsm->get_entry_no( $lastentryid );

		$this->db->trans_complete();

		$this->messages->add("Entry has been successfully saved. GL Entry # $entrynumber.", 'success');
		redirect('collection/bankentry');
		return;
	}


	function bankentry()
	{
		$this->db->select('code, name');
		$this->db->where('group_id', 6);
		$this->db->where('type', 1);
		
		$banks = $this->db->get('ledgers')->result_array();
		$p['bank'] = '';

		foreach($banks as $bank)
		{
			$name = str_replace('Cash in bank ', '', $bank['name']);
			$name = str_replace('Cash in Bank', '', $name);
			$name = str_replace(' - ', '', $name);
			$name = str_replace('(', '', $name);
			$name = str_replace(')', '', $name);
			$p['bank'] .= "<option value='{$bank['code']}'>$name</option>";
		}

		$this->template->load('template', 'collection/bankentryform', $p);
		return;
	}


	function validate_balance( $clientcode, $pn, $balance, $principal, $obc )
	{
		$this->db->select('
			LL_Remarks,
			LL_Refund,
			LL_AmountCash,
			LL_AmountCash_Payment,
			LL_PaymentDate
		');
		$this->db->where('LL_Processed', 1);
		$this->db->where('LL_IsDeleted', 0);
		$this->db->where('CI_AcctNo', $clientcode);
		$this->db->where('LH_PN', $pn);
		$this->db->order_by('LL_PaymentDate', 'ASC');
		$ledger = $this->db->get('ln_ldgr')->result_array();
		$ob = $principal + $obc;
		foreach($ledger as $l)
		{
			$refund = ($l['LL_Remarks']==''?0:$l['LL_Refund']);
			$collection = ($l['LL_AmountCash']==0?0:$l['LL_AmountCash']);
			$payment = ($l['LL_AmountCash']==0&&$l['LL_AmountCash_Payment']>0?$l['LL_AmountCash_Payment']:0);
			$ob = $ob + $refund - $collection - $payment;
		}

		if($balance!=$ob):
			$this->db->set('LH_Balance', $ob);
			$this->db->where('CI_AcctNo', $clientcode);
			$this->db->where('LH_PN', $pn);
			$this->db->update('ln_hdr') or die( $this->db->_error_message() );
			$balance=$ob;
		endif;

		return $balance;
	}

// Post Manual Billing
	function a_()
	{
		$now = date('Y-m-d H:i:s');

		$this->db->select(
			'CP_PType, CP_BankAcctNo, CP_BankBranch, CP_WithdrawalDay'
		);
		$this->db->where('CI_AcctNo', $_POST['a']);
		$q = $this->db->get('client_pension');
		$paytype=''; $bankacctno=''; 
		$bankbranch=''; $wday='';
		if($q->num_rows()):
			$paytype = $q->row()->CP_PType;
			$bankacctno = $q->row()->CP_BankAcctNo;
			$bankbranch = $q->row()->CP_BankBranch;
			$wday = $q->row()->CP_WithdrawalDay;
		endif;

		unset($q);
		$this->db->select(
			'CI_BranchCode','CI_LName, CI_FName, CI_MName, CI_Source'
		);
		$this->db->where('CI_AcctNo', $_POST['a']);
		$q = $this->db->get('client');
		$name=''; $pentype=''; $branchcode = '';
		if($q->num_rows()):
			$name=$q->row()->CI_LName.', '.
				$q->row()->CI_FName.' '.
				$q->row()->CI_MName;
			$pentype=$q->row()->CI_Source;
			$branchcode = $q->row()->CI_BranchCode;
		endif;

		unset($q);
		$this->db->select(
			'LH_Balance, LH_MonthlyAmort, LH_StartDate, LH_EndDate,
			LH_Terms, LH_LoanTrans, LH_Principal, LH_OBC'
		);
		$this->db->where('CI_AcctNo', $_POST['a']);
		$this->db->where('LH_PN', $_POST['b']);
		$q = $this->db->get('ln_hdr');
		$balance=0; $amtodrawn=0;
		$terms=0; $duration='';
		$loantrans=''; $principal=0; $obc=0;
		if($q->num_rows()):
			$balance 	= $q->row()->LH_Balance;
			$amtodrawn	= $q->row()->LH_MonthlyAmort;
			$terms 		= $q->row()->LH_Terms;
			$duration 	= date('F Y', strtotime($q->row()->LH_StartDate)).' - '.
				date('F Y', strtotime($q->row()->LH_EndDate));
			$loantrans 	= $q->row()->LH_LoanTrans;
			$principal 	= $q->row()->LH_Principal;
			$obc 		= $q->row()->LH_OBC;
		endif;

		$balance = $this->validate_balance( $_POST['a'], $_POST['b'], $balance, $principal, $obc );

		$date = date('Y-m-d', strtotime($_POST['d'].'-'.$_POST['e'].'-01'));
		$lastday = date('t', strtotime($date));

		if( $wday>$lastday || $wday==0 ):
			$date = date('Y-m-d', strtotime($_POST['d'].'-'.$_POST['e'].'-'.$lastday));
		else:
			$date = date('Y-m-d', strtotime($_POST['d'].'-'.$_POST['e'].'-'.$wday));
		endif;

		$datas = array(
			'branchcode'=> $branchcode,
			'billtype'	=> 'manual',
			'CI_AcctNo'	=> $_POST['a'],
			'name'		=> $name,
			'paytype'	=> $paytype,
			'bankacctno'=> $bankacctno,
			'bankbranch'=> $bankbranch,
			'LH_PN'		=> $_POST['b'],
			'pentype'	=> $pentype,
			'duration'	=> $duration,
			'terms'		=> $terms,
			'balance'	=> $balance,
			'amtodrawn'	=> $amtodrawn,
			'billdate'	=> $date,
			'generateby'=> $this->session->userdata('user_name'),
			'dategenerate'=> $now,
			'collectby'	=> '',
			'datecollected'=> '0000-00-00 00:00:00',
			'loantrans'	=> $loantrans
		);

		$this->db->insert('nhgt_bills.header', $datas) or die( $this->db->_error_message() );

		$this->load->helper('code');

		echo "$($($('input.mbill')[{$_POST['c']}]).parent().parent().find('td')[8]).html('Billed');";
	}


	function manclst()
	{
		$this->load->view('collection/list/clientlist');
		return;
	}

	function gtpnlst()
	{
		$cid = $this->uri->segment(3);
		$year = $this->uri->segment(4);
		$month = $this->uri->segment(5);

		$this->db->where('CI_AcctNo', $cid);
		$this->db->where('LH_IsTop', 1);
		$this->db->where('LH_Cancelled', 0);
		$this->db->order_by('LH_LoanDate', 'ASC');
		$datas = $this->db->get('ln_hdr');

		if($datas->num_rows()):

			$datas = $datas->result_array();

			$param['data'] = array();
			foreach ($datas as $d)
			{
				$this->db->select('bill_id');
				$this->db->where('CI_AcctNo', $cid);
				$this->db->where('LH_PN', $d['LH_PN']);
				$this->db->where("MONTH(billdate)='$month'");
				$this->db->where("YEAR(billdate)='$year'");
				$t = $this->db->get('nhgt_bills.header');
				
				$param['data'][] = array(
					'LH_BankBranch'	=>	$d['LH_BankBranch'],
					'LH_PN' => $d['LH_PN'],
					'LH_LoanTrans' => $d['LH_LoanTrans'],
					'LH_LoanDate' => $d['LH_LoanDate'],
					'LH_Terms' => $d['LH_Terms'],
					'LH_Balance' => $d['LH_Balance'],
					'LH_MonthlyAmort' => $d['LH_MonthlyAmort'],
					'LH_StartDate' => $d['LH_StartDate'],
					'LH_EndDate' => $d['LH_EndDate'],
					'isBilled' => ($t->num_rows()?TRUE:FALSE)
				);
				
			}

			$param['cid'] = $cid;

			$this->load->view('collection/list/pnlist', $param);

			return;

		endif;
	}

	// Submit OR PR
	function orprsub()
	{	
		$this->load->view('submit/orpr');
	}


	// Assinging of OR PR form view
	function orpr()
	{
		$this->load->model('rsm');

		$t['orno'] = $this->rsm->get_op('ORNO');
		$t['prno'] = $this->rsm->get_op('PRNO');

		$this->template->load('template','collection/assign_orpr', $t);
		return;
	}

	// CHANGE WITHDRAWAL DATE
	function cwd()
	{
		$this->load->model('rsm');

		$clientcode = $this->uri->segment(3, '');
		$wdate = date('d', strtotime($this->uri->segment(4, '')));
		$billids = $this->uri->segment(5, '');

		$p['wdate'] = $wdate;
		$p['cid'] = $clientcode;
		$p['bill_ids'] = $billids;

		$this->load->view('collection/updateform', $p);
		return;
	}

	
	// DATA ENTRY
	function de()
	{
		$this->load->model('rsm');

		$clientcode = $this->uri->segment(3, '');
		$loans = $this->uri->segment(4, '');
		$date = $this->uri->segment(5, '');
		$bill_ids = $this->uri->segment(6, '');

		$client = $this->rsm->get_client_details( $clientcode );

		$p['client'] = $client;
		$p['bill_amount'] = $this->rsm->total_loans($clientcode, $loans, $date);
		$p['atmpension'] = $this->rsm->get_pension_details( $clientcode );
		$p['atmpension'] = intval($p['atmpension']/100)*100;
		$p['atmpension'] = $p['atmpension']-100;

		$p['bill_ids'] = $bill_ids;

		$t = $this->rsm->get_default_bank('BANK_COLLECTION');

		$p['banks'] = "<option value='{$t->code}'>{$t->bankname}</option>";

		$t = $this->rsm->get_bank_in_ledgers( isset($t->code)?$t->code:'' );

		foreach ($t as $v)
		{
			$name = str_replace('Cash in bank', '', $v['name']);
			$name = str_replace('Cash in Bank', '', $name);
			$name = str_replace('(', '', $name);
			$name = str_replace(')', '', $name);
			$p['banks'] .= "<option value='{$v['code']}'>$name</option>";
		}

		$this->load->view('collection/entryform', $p);
		return;
	}

	function billed()
	{
		$this->load->helper('code');

		$date = $_POST['l'];
		$tmp = explode('-', $date);
		$enday = date('t', strtotime($date));
		$month = date('m', strtotime($date));
	
		if($month!=$tmp[1]):
			$enday = date('t', strtotime("{$tmp[0]}-{$tmp[1]}-1"));
			$date="{$tmp[0]}-{$tmp[1]}-$enday";
		endif;

		$now = date('Y-m-d H:i:s');

		$name = str_replace('<small>', '', $_POST['b']);
		$name = str_replace('</small>', '', $name);

		$datas = array(
			'branchcode'=>$_POST['o'],
			'CI_AcctNo'=>$_POST['a'],
			'name'=> $name,
			'paytype'=>$_POST['c'],
			'bankacctno'=>$_POST['d'],
			'bankbranch'=>$_POST['e'],
			'LH_PN'=>$_POST['f'],
			'pentype'=>$_POST['g'],
			'duration'=>$_POST['h'],
			'terms'=>$_POST['i'],
			'balance'=>n($_POST['j']),
			'amtodrawn'=>n($_POST['k']),
			'billdate'=>$date,
			'generateby'=>$this->session->userdata('user_name'),
			'dategenerate'=>$now,
			'collectby'=>'',
			'datecollected'=>'0000-00-00 00:00:00',
			'loantrans'=>$_POST['n']
		);

		$this->db->insert('nhgt_bills.header', $datas);
		$error = $this->db->_error_message();
		$error = str_replace("'", '', $error);
		if( !$error )
			die("$($($('#glif').contents().find('tr')[{$_POST['m']}]).css('background-color','orange').find('td')[13]).html('Billed');");
		else die("alert('$error');");
	}

	function index()
	{
		$this->template->set('page_title', 'Collection');
		$this->template->load('template', 'collection/index');

		return;
	}

	function generatebills()
	{
		$this->template->load('template', 'collection/generatebills');
		return;
	}

	function collectionsched()
	{
		$this->template->load('template', 'collection/collectionsched');
		return;
	}
	
	function collectionschedadpr()
	{
		$this->template->load('template', 'collection/collectionschedadpr');
		return;
	}
	
	function collist()
	{
		$this->load->view('collection/list/collection');
		return;
	}
		
	function collistadpr()
	{
		$this->load->view('collection/list/collectionadpr');
		return;
	}
	
	function orprlist()
	{
		$this->load->view('collection/list/orpr');
		return;
	}

	function genlist()
	{
		$this->load->view('collection/list/bills');
		return;
	}

	

	function ajbmnt()
	{
		$tmp = $this->config->item('account_name');
		$tmp = explode('-',$tmp);
		$branch = strtolower(trim($tmp[1]));

		$t = $this->db->query(
			"SELECT branch_code
			FROM nhgt_master.branch
			WHERE branch_name='$branch';
		");

		$branchcode = '';
		if($t->num_rows())$branchcode = $t->row()->branch_code;
		unset($t, $tmp, $branch);

		$year = $this->input->post('a');
		$month = $this->input->post('b');
		$day = $this->input->post('c');		

		$t0 = $this->db->query(
			"SELECT *
			FROM nhgt_master.billpost
			WHERE branch='$branchcode'
			AND monthof='$month'
			AND yearof='$year'
			AND dayof='$day';
		");

		if($t0->num_rows())$res = "$('#procon').attr('disabled',true);";
		else $res = "$('#procon').attr('disabled',false);";

		die($res);
	}
	
	function monthlycollection()
	{
		$this->template->load('template', 'collection/monthlycollection');
		return;
	}
	
	function monthlylist()
	{
		$this->load->model('database');
		
		$fromDate = $this->uri->segment(3)?$this->uri->segment(3):date('Y-m-d');
		$toDate = $this->uri->segment(4)?$this->uri->segment(4):date('Y-m-d');
		
		$param['coll'] = $this->db->query("
			SELECT a.cid as acctno,
				a.tracerefno as tracerefno,
				a.paytype as paytype,
				a.atmbegbal as atmbegbal,
				a.amtdrawn as amtdrawn,
				a.atmendbal as atmendbal,
				a.directpaid as directpaid,
				a.duedate as duedate,
				a.orprno as orprno,
				a.encby as encby,
				a.uid as uid,
				CONCAT(b.CI_LName, ', ', b.CI_FName, ' ', b.CI_MName) AS fullname
			FROM collection_entry a, client b
			WHERE a.cid = b.CI_AcctNo
			AND	a.duedate >= '".$fromDate."'
			AND a.duedate <= '".$toDate."'
			ORDER BY fullname
			ASC;
		")->result_array();
		
		$param['deleted_coll'] = $this->db->query("
			SELECT a.cid as acctno,
				a.tracerefno as tracerefno,
				a.paytype as paytype,
				a.atmbegbal as atmbegbal,
				a.amtdrawn as amtdrawn,
				a.atmendbal as atmendbal,
				a.directpaid as directpaid,
				a.duedate as duedate,
				a.orprno as orprno,
				a.encby as encby,
				a.uid as uid,
				CONCAT(b.CI_LName, ', ', b.CI_FName, ' ', b.CI_MName) AS fullname
			FROM deleted_collection_entry a, client b
			WHERE a.cid = b.CI_AcctNo
			AND	a.duedate >= '".$fromDate."'
			AND a.duedate <= '".$toDate."'
			ORDER BY fullname
			ASC;
		")->result_array();
		
		$param['datas'] = array_merge($param['coll'], $param['deleted_coll']);
		
		$cnt = count($param['datas']) - 1;
		
		for($i = 0; $i <= $cnt; $i++)
		{
			$data = $this->database->get_c_detail( 'bankbranch_day', $param['datas'][$i]['acctno'] ); 
			$param['datas'][$i]['bankbranch'] = $data[0];
		}
		
		$this->load->view('collection/list/monthly', $param);
		return;
	}
	
	// Collection Entry update and delete modules with log and backup sql 2015-08-08
	function colladj()
	{
		$this->template->load('template', 'collection/colladj');
		return;
	}
	
	function adjlist()
	{
		$this->load->model('database');
			
		$fromDate = $this->uri->segment(3)?$this->uri->segment(3):date('Y-m-d');
		$toDate = $this->uri->segment(4)?$this->uri->segment(4):date('Y-m-d');
		
		$param['datas'] = $this->db->query("
			SELECT a.cid as acctno,
				a.tracerefno as tracerefno,
				a.paytype as paytype,
				a.atmbegbal as atmbegbal,
				a.amtdrawn as amtdrawn,
				a.atmendbal as atmendbal,
				a.directpaid as directpaid,
				a.duedate as duedate,
				a.orprno as orprno,
				a.encby as encby,
				a.uid as uid,
				CONCAT(b.CI_LName, ', ', b.CI_FName, ' ', b.CI_MName) AS fullname,
				a.receipt_image as receiptimg
			FROM collection_entry a, client b
			WHERE a.cid = b.CI_AcctNo
			AND	a.duedate >= '" . $fromDate . "'
			AND a.duedate <= '" . $toDate . "'
			ORDER BY fullname
			ASC;
		")->result_array();
		
		$cnt = count($param['datas']) - 1;
		for($i=0;$i<=$cnt;$i++):
			$data = $this->database->get_c_detail( 'bankbranch_day', $param['datas'][$i]['acctno'] ); 
			$param['datas'][$i]['bankbranch'] = $data[0];
		endfor;
		
		
		
		$this->load->view('collection/list/adj', $param);
		return;
	}
	function adjform()
	{
		$this->load->model('database');
		
		$id = $this->uri->segment(3)?$this->uri->segment(3):date('Y-m-d');
		
		$data = explode('|', $id);
		$this->db->select('*');
		$this->db->where('uid', $data[0]);
		$this->db->where('cid', $data[1]);
		$param['datas'] = $this->db->get('collection_entry')->row();
		
		$this->db->select('CONCAT(CI_LName, \', \', CI_FName, \' \', CI_MName) AS name', false);
		$this->db->where('CI_AcctNo', $data[1]);
		$this->db->limit(1);
		$param['ciname'] = $this->db->get('client')->row();
		
		if(!empty($param['datas']->banktopost)):
			$banktopost = $param['datas']->banktopost;
		else:
			$banktopost = '';
		endif;
		$this->load->model('rsm');
		$t = $this->rsm->get_default_bank('BANK_COLLECTION');
		$param['banks'] = "<option value='{$t->code}' ".($banktopost==$t->code?'selected':'').">{$t->bankname}</option>";
		
		$t = $this->rsm->get_bank_in_ledgers( isset($t->code)?$t->code:'' );
		foreach ($t as $v)
		{
			$name = str_replace('Cash in bank', '', $v['name']);
			$name = str_replace('Cash in Bank', '', $name);
			$name = str_replace('(', '', $name);
			$name = str_replace(')', '', $name);
			$param['banks'] .= "<option value='{$v['code']}' ".($banktopost==$v['code']?'selected':'').">$name</option>";
		}
		
		$this->load->view("collection/form/adj", $param);
		return;
	}
	
	function colldelete()
	{
		$this->db->trans_start();
		
		if($this->input->post('coll'))
		{
			foreach($this->input->post('coll') as $i => $coll)
			{
				$this->db->select('*');
				$this->db->where('uid', $coll);
				$q = $this->db->get('collection_entry')->row();
				$sql = 'INSERT INTO collection_entry
					VALUES ("'.$q->uid.'", "'.$q->cid.'", "'.$q->paytype.'", "'.$q->bill_id.'", "'.$q->tracerefno.'", "'.$q->atmbegbal.'", "'.$q->amtdrawn.'", "'.$q->atmendbal.'", "'.$q->directpaid.'", "'.$q->duedate.'", "'.$q->orprtype.'", "'.$q->orprno.'", "'.$q->encby.'", "'.$q->encdate.'");';
				$dtarray = array(
					'sql' 	 		=> $sql,
					'deletedby'		=> $this->session->userdata('user_name'),
					'datedeleted'	=> date('Y-m-d')
				);
				$this->db->insert('deleted_trans', $dtarray) or die( $this->db->_error_message() );
				
				$ip = $_SERVER['REMOTE_ADDR'];
				
				$larray = array(
					'date'  	 	=> date('Y-m-d H:i:s'),
					'level' 	 	=> 1,
					'host_ip'		=> $ip,
					'user'		 	=> $this->session->userdata('user_name'),
					'url'		 	=> '/collection/colldelete',
					'user_agent' 	=> $this->session->userdata('user_agent'),
					'message_title'	=> 'Deleted collection_entry [uid:'.$coll.']',
					'message_desc'	=> ''
				);
				$this->db->insert('logs', $larray) or die( $this->db->_error_message() );
				
				$this->db->where('uid', $coll);
				$this->db->delete('collection_entry') or die( $this->db->_error_message() );
			}
			unset($coll);
		}
		
		$this->db->trans_complete();
		
		die(trims("
			<script>
				window.location.href='../../adjlist/" . date('Y-m-01') ."/" . date('Y-m-t') . "';
			</script>
		"));
		return;
	}
	
	// 2016-01-07
	function monthly_billing()
	{
		$this->template->load('template', 'collection/monthly_billing');
		return;
	}
	
	function monthly_billing_list()
	{
		$year = $this->uri->segment(3) ? $this->uri->segment(3) : date('Y');
		$month = $this->uri->segment(4) ? $this->uri->segment(4) : date('m');
		$ci_type = $this->uri->segment(5) ? $this->uri->segment(5) : date('PEN');
		$coll_type = $this->uri->segment(6) ? $this->uri->segment(6) : date('1');
		
		$results = $this->collection_model->fetch_billings($month, $year, $ci_type, $coll_type);
		
		$data = '';
		if(!empty($results))
		{
			foreach($results as $results)
			{
				$cp_columns = 'CP_PensionType as cp_pensiontype, CP_Amount as cp_amount, CP_WithdrawalDay as cp_withdrawalday';
				$client_pension = $this->master_model->fetch_client_pension_details($cp_columns, $results->CI_AcctNo);
				
				$lnhdr_columns = 'LH_Balance as lh_balance';
				$ln_hdr = $this->master_model->fetch_loan_details($lnhdr_columns, $results->CI_AcctNo, $results->LH_PN);
				
				/*$a = $client_pension->cp_amount - 100;
				$b = explode('.', $a);
				$c = strlen($b[0]) - 2;
				$amtobwdrawn = substr($b[0], 0, $c) . '00';
				
				$lh_balance = !empty($ln_hdr->lh_balance) ? $ln_hdr->lh_balance : 0;*/
				
				$amtobwdrawn = 0;
				$lh_balance = 0;
				
				$data['data'][] = array(
					'ci_acctno' => $results->CI_AcctNo,
					'cp_withdrawalday' => '1', //$client_pension->cp_withdrawalday,
					'name' => $results->name,
					'paytype' => $results->paytype,
					'bankbranch' => $results->bankbranch,
					'cp_amount' => 0, //$client_pension->cp_amount,
					'amtobwdrawn' => $amtobwdrawn,
					'amtodrawn' => $results->amtodrawn,
					'duration' => $results->duration,
					//'total_mo_payment' => 0,
					'mo_refund' => 0,
					'cp_pensiontype' => 'PEN', //$client_pension->cp_pensiontype,
					'lh_pn' => $results->LH_PN,
					'loantrans' => $results->loantrans,
					'billdate' => $results->billdate,
					'lh_balance' => $lh_balance,
					'bill_id' => $results->bill_id
				);
			}
		}
		
		$data['ip'] = $_SERVER['REMOTE_ADDR'];
		
		$data['coll_type'] = $coll_type;
		$this->load->view('collection/list/monthly_billing', $data);
		return;
	}
	
	function client_billing_list()
	{
		$data['ci_acctno'] = $this->uri->segment(3);
		$data['lh_pn'] = $this->uri->segment(4);
		$data['loans'] = '';
		$data['results'] = '';
		
		$columns = 'CONCAT(CI_LName, \', \', CI_FName, \' \', CI_MName) as name';
		$data['client'] = $this->master_model->fetch_client_details($columns, $data['ci_acctno']);
		
		$columns = 'CP_PensionType as cp_pensiontype, CP_BankBranch as cp_bankbranch';
		$data['client_pension'] = $this->master_model->fetch_client_pension_details($columns, $data['ci_acctno']);
		
		$columns = 'LH_PN as lh_pn, LH_LoanTrans as lh_loantrans';
		$loans = $this->master_model->fetch_loans($columns, $data['ci_acctno']);
		
		if(!empty($loans))
		{
			foreach($loans as $j => $loans)
			{
				$data['loans'][] = $loans;
			}
		}
		
		$columns = 'bill_id, branchcode, loantrans, billdate, duration, terms, amtodrawn, status';
		$data['results'] = $this->collection_model->fetch_client_billings($columns, $data['ci_acctno'], $data['lh_pn']);
		
		$this->load->view('collection/list/client_billing', $data);
		return;
	}
	
	function close_billing()
	{
		$billing = $this->input->post('billing');
		if(!empty($billing))
		{
			$this->collection_model->close_billing($this->input->post('billing'));
		}
		
		die(trims('
			<script>
				window.opener.location.reload();
				window.close();
			</script>
		'));
		return;
	}
	
	function delete_billing()
	{
		$billing = $this->input->post('billing');
		if(!empty($billing))
		{
			$this->collection_model->delete_billing($this->input->post('billing'));
		}
		
		die(trims('
			<script>
				window.opener.location.reload();
				window.close();
			</script>
		'));
		return;
	}
	
	function insert_billing()
	{
		$ci_acctno = $this->input->post('ci_acctno');
		$lh_pn = $this->input->post('lh_pn');
		
		if(!empty($ci_acctno)&&!empty($lh_pn))
		{
			$this->collection_model->insert_billing($ci_acctno, $lh_pn);
		}
		
		die(trims('
			<script>
				window.opener.location.reload();
				window.close();
			</script>
		'));
		return;
	}
	
	function auto_debit($system_message= '')
	{
		$data['system_message'] = $system_message;
		
		$this->template->load('template', 'collection/form/auto_debit', $data);
		return;
	}
	
	function process_auto_debit_file()
	{
		$month = $this->input->post('month');
		$year = $this->input->post('year');
		$start_date = date('Y-m-01', strtotime($year . '-' . $month . '-' . '01')); 
		$end_date = date('Y-m-t', strtotime($year . '-' . $month . '-' . '01'));
		$coll_date = $year . $month;
		
		$banktopost = '';
		
		$file = $_FILES['auto_debit_file']['tmp_name'];
		$file = fopen($file, 'r');
		
		$i = 0;
		while(!feof($file))
		{
			$data = fgetcsv($file, 4096, '~');
			foreach($data as $data)
			{
				$d[$i][] = $data;
			}
			$i++;
		}
		fclose($file);
		
		foreach($d as $d)
		{
			$date = explode('/', $d[1]);
			$due_date = $date[2] . '-' . $date[1] . '-' . $date[0];
			
			$this->db->select('CI_AcctNo as ci_acctno');
			$this->db->from('client_pension');
			$this->db->where('CP_ADNo', $d[3]);
			$query = $this->db->get();
			
			if($query->num_rows() > 0)
			{
				$result = $query->row();
				$ci_acctno = $result->ci_acctno;
				
				/* Get bill_id for the collection month */
				$this->db->select('bill_id');
				$this->db->from('nhgt_bills.header');
				$this->db->where('CI_AcctNo', $ci_acctno);
				$this->db->where('billdate >=', $start_date);
				$this->db->where('billdate <=', $end_date);
				$this->db->where('status', null);
				$q = $this->db->get();
				
				$bill_id = '';
				$b = '';
				if($q->num_rows() > 0)
				{
					$header = $q->result();
					
					foreach($header as $header)
					{
						$b = strval($header->bill_id);
						$bill_id = $bill_id . $b . '.';
						
						/* Update billing as collected */
						$header_array = array(
							'collectby' => $this->session->userdata('user_name'),
							'datecollected' => date('Y-m-d H:i:s')
						);
						$this->db->update('nhgt_bills.header', $header_array);
						$this->db->where('bill_id', $header->bill_id) or die('Error: process auto debit file. Please contact your System Administrator.');
					}
				}
				
				$array = array(
					'cid' => $ci_acctno,
					'paytype' => 'TR',
					'bill_id' => $bill_id,
					'tracerefno' => 'ADA',
					'atmbegbal' => $d[2],
					'amtdrawn' => $d[2],
					'atmendbal' => 0,
					'directpaid' => 0,
					'duedate' => $due_date,
					'orprtype' => '',
					'orprno' => '',
					'encby' => $this->session->userdata('user_name'),
					'encdate' => date('Y-m-d H:i:s'),
					'banktopost' => $banktopost
				);
				$this->db->insert('collection_entry', $array) or die('Error: process auto debit file. Please contact your System Administrator.');
			}
		}
		$system_message = 'Auto Debit process for ' . $due_date . ' collection has been processed';
		
		$this->auto_debit($system_message);
		return;
	}
	
	function receipt_image_form()
	{
		$this->load->view('collection/form/receipt_image');
		return;
	}
	
	function receipt_preview()
	{
		$data['uid'] = $this->uri->segment(3) ? $this->uri->segment(3) : '0';
		$data['traceno'] = $this->uri->segment(4) ? $this->uri->segment(4) : '0';
		$data['atmbegbal'] = $this->uri->segment(5) ? $this->uri->segment(5) : '0';
		$data['amtdrawn'] = $this->uri->segment(6) ? $this->uri->segment(6) : '0';
		$data['atmendbal'] = $this->uri->segment(7) ? $this->uri->segment(7) : '0';
		$data['directpaid'] = $this->uri->segment(8) ? $this->uri->segment(8) : '0';
		
		$data['coll_data'] = $this->collection_model->fetch_coll_details('cid, duedate, receipt_image', $data['uid']);
		$data['client_data'] = $this->master_model->fetch_client_details("CONCAT(CI_LName, ', ', CI_FName, ' ', CI_MName) AS name", $data['coll_data']->cid);
		$data['pension_data'] = $this->master_model->fetch_client_pension_details('CP_PensionType', $data['coll_data']->cid);
		
		$this->db->select('name, address');
		$this->db->from('settings');
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
		{
			$data['branch_data'] = $query->row();
		}
		
		$data['user'] = $this->session->userdata('user_name');
		
		$this->load->view('collection/print/receipt', $data);
		return;
	}
}

/* End of file account.php */
/* Location: ./system/application/controllers/account.php */