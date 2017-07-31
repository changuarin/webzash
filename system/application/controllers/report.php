<?php

class Report extends Controller
{
	var $acc_array;
	var $account_counter;

	function Report()
	{
		parent::Controller();
		date_default_timezone_set('Asia/Manila');
		$this->load->helper('code');
		$this->load->model('database');
		$this->load->model('Ledger_model');
		$this->load->model('master_model');
		$this->load->model('report_model');
		$this->load->model('sales_model');
		$this->load->model('rsm');

		/* Check access */
		if ( ! check_access('view reports') )
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('');
			return;
		}
		return;
	}
	
	function index()
	{
		$this->template->set('page_title', 'Reports');
		$this->template->load('template', 'report/index');
		return;
	}
	
	// 20150812 -> rsm -> adjusted due to other Branch Collection with Other Banks
	// and include still due to client, cash payment
	function crbpost()
	{		
		//$this->output->enable_profiler(TRUE);
		
		$now = $this->input->post('b');

		$arr['isOk'] = FALSE;
		$arr['error'] = '';		

		$this->db->trans_start();

			// CRB transaction
			$data = array
			(
				'aid'	   => 0,
				'crb_date' => $this->input->post('b'),
				'or_pr'    => $this->input->post('c'),
				'amount'   => $this->input->post('a'),
				'entry'    => '',
				'remarks'  => '',
				'post_by'  => $this->session->userdata('user_name')
			);

			$this->db->insert( 'crb_entry', $data ) or $arr['error'] = $this->db->_error_message();

			// CHECK IF HAVE OTHER BANK DEFAULT AND GET TOTAL NET
			$otherbank = $this->db->query(
				"SELECT banktopost AS code, SUM(amtdrawn) - SUM(directpaid) AS totalnet
				FROM collection_entry
				WHERE orprtype='{$this->input->post('c')}'
				AND duedate='{$this->input->post('b')}'
				GROUP BY banktopost;
			")->result_array();

			$totalnet = 0;
			foreach($otherbank as $ob):

				if($ob['code']=='') $totalnet += $ob['totalnet'];

				if($ob['code'] != ''):

					$totalnet += $ob['totalnet'];
					// Find and Check Default Bank
					$p = $this->db->query(
						"SELECT `code`,`command`
						FROM `parameter`
						WHERE `group`='BANK_COLLECTION'
						AND `code` LIKE '{$ob['code']}%'
						AND `status`=1;
					") or $arr['error'] = $this->db->_error_message();

					if( $p->num_rows() ):

						// CRB Entry From Parameter
						$p = $this->db->query(
							"SELECT `code`,`command`
							FROM `parameter`
							WHERE `group`='BANK_COLLECTION'
							AND `status`=1
							ORDER BY `command`;
						") or $arr['error'] = $this->db->_error_message();

						foreach ($p->result_array() as $v)
						{
							if( strpos($v['code'],':') ):

								$c = explode(':', $v['code']);

								$dr = $this->db->query(
									"SELECT `id`
									FROM {$v['command']}ledgers
									WHERE `code`='{$c[0]}';
								")->row();

								$cr = $this->db->query(
									"SELECT `id`
									FROM {$v['command']}ledgers
									WHERE `code`='{$c[1]}';
								")->row();

								$datas = array(
									'entry_type' => 1, // Cash Receipt
									'number'     => $this->rsm->get_entry_number(1),
									'date'       => $now,
									'dr_total'   => n($totalnet),
									'cr_total'   => n($totalnet),
									'narration'  => "Collection of receivables"
								);

								$this->db->insert("{$v['command']}entries", $datas) 
								or $arr['error'] = $this->db->_error_message();

								$a = $this->db->query(
									"SELECT id
									FROM {$v['command']}entries
									ORDER BY id DESC
									LIMIT 1;
								")->row()->id;

								$entry = array(
									'entry_id'  => $a,
									'ledger_id' => $dr->id,
									'dc'        => 'D',
									'amount'    => n( $totalnet )
								);

								$this->db->insert("{$v['command']}entry_items", $entry) 
								or $arr['error'] = $this->db->_error_message();

								$entry = array(
									'entry_id'  => $a,
									'ledger_id' => $cr->id,
									'dc'        => 'C',
									'amount'    => n( $totalnet )
								);

								$this->db->insert("{$v['command']}entry_items", $entry) 
								or $arr['error'] = $this->db->_error_message();

							else: 

								$arr['error'] = 'Parameters has not been set.';
								die(json_encode($arr));

							endif;
							
						}

					else:

						// IF OTHER BRANCH COLLECTION
						$dr = $this->db->query(
							"SELECT `id`
							FROM ledgers
							WHERE `code`='{$ob['code']}';
						")->row();

						$cr = $this->db->query(
							"SELECT `id`
							FROM ledgers
							WHERE `code`='1201';
						")->row();

						$datas = array(
							'entry_type' => 1, // Cash Receipt
							'number'     => $this->rsm->get_entry_number(1),
							'date'       => $now,
							'dr_total'   => n($ob['totalnet']),
							'cr_total'   => n($ob['totalnet']),
							'narration'  => "Collection of receivables"
						);

						$this->db->insert("entries", $datas) 
						or $arr['error'] = $this->db->_error_message();

						$a = $this->db->query(
							"SELECT id
							FROM entries
							ORDER BY id DESC
							LIMIT 1;
						")->row()->id;

						$entry = array(
							'entry_id'  => $a,
							'ledger_id' => $dr->id,
							'dc'        => 'D',
							'amount'    => n( $ob['totalnet'] )
						);

						$this->db->insert("entry_items", $entry) 
						or $arr['error'] = $this->db->_error_message();

						$entry = array(
							'entry_id'  => $a,
							'ledger_id' => $cr->id,
							'dc'        => 'C',
							'amount'    => n( $ob['totalnet'] )
						);

						$this->db->insert("entry_items", $entry) 
						or $arr['error'] = $this->db->_error_message();

					endif;

					$totalnet = 0;

				endif;
				
			endforeach;

			// UPDATE GREEN LEDGER / BALANCES
			$this->db->select('cid, paytype, bill_id, atmbegbal, amtdrawn, directpaid, orprno, orprtype');
			$this->db->where('orprtype', $this->input->post('c'));
			$this->db->where('duedate', $this->input->post('b'));
			$bills = $this->db->get('collection_entry')->result_array();

			foreach($bills as $bill)
			{
				$bill_refs  = explode('.', $bill['bill_id']);
				$amtopost = n($bill['amtdrawn']) - n($bill['directpaid']);
				
				if(!empty($bill_refs))
				{
					$i = 1;
					$j = count($bill_refs) - 1;
					$payment = 0;
					
					foreach($bill_refs as $bill_ref)
					{
						$this->db->select('CI_AcctNo, LH_PN, amtodrawn, status');
						$this->db->from('nhgt_bills.header');
						$this->db->where('bill_id', $bill_ref);
						$query = $this->db->get();
						$data = $query->row();
						
						if(!empty($data))
						{
							if($data->status != 'closed')
							{
								$columns = 'LH_BranchCode, LH_BankBranch, LH_BankAcctNo, LH_Reference, LH_MonthlyAmort, LH_LoanTrans';
								
								$this->db->select($columns, FALSE);
								$this->db->from('ln_hdr');
								$this->db->where('CI_AcctNo', $data->CI_AcctNo);
								$this->db->where('LH_PN', $data->LH_PN);
								$this->db->where('LH_IsPending', 0);
								$this->db->where('LH_Processed', 1);
								$this->db->where('LH_Cancelled', 0);
								$q = $this->db->get();
								$lh = $q->row();
								
								$lh_pn = $data->LH_PN;
								$lh_branchcode = $lh->LH_BranchCode;
								$lh_bankacctno = $lh->LH_BankAcctNo;
								$lh_loantrans = $lh->LH_LoanTrans;
								
								if($data->status == NULL)
								{
									// Check header table for uncollected billing
									$this->db->select('bill_id', FALSE);
									$this->db->from('nhgt_bills.header');
									$this->db->where('nhgt_bills.header.CI_AcctNo', $data->CI_AcctNo);
									$this->db->where('nhgt_bills.header.LH_PN', $data->LH_PN);
									$this->db->where('nhgt_bills.header.status', 'unc');
									$query_unc = $this->db->get();
									
									if($query_unc->num_rows() > 0)
									{
										$payment0 = $lh->LH_MonthlyAmort;
										
										$id0 = $this->database->getID($bill['cid'], $data->LH_PN, 'LL_IsPayment');
										
										foreach($query_unc->result() as $row_unc)
										{
											$datas0 = array(
												'ID'					  => $id0,
												'CI_AcctNo'				  => $bill['cid'],
												'LH_PN'					  => $lh_pn,
												'LH_BranchCode_Processed' => $lh_branchcode,
												'LH_BankAcctNo'			  => $lh_bankacctno,
												'LH_LoanType'			  => $lh_loantrans,
												'LL_ORNo'				  => $bill['orprno'],
												'LL_Rebates'			  => 0,
												'LL_InterestAmt'		  => 0,
												'LL_AmountCheck'		  => 0,
												'LL_AmountCash'			  => $payment0,
												'LL_AmountCash_Payment'   => $payment0,
												'LL_ShortPayment'		  => 0,
												'LL_Refund'				  => 0,
												'LL_Remarks'			  => '',
												'LL_PaymentDate'		  => $now,
												'LL_Processed'			  => 1,
												'LL_IsPayment'			  => 1,
												'LL_IsRefund'			  => 0,
												'LL_IsDeleted'			  => 0,
												'LL_CheckNo'			  => ''
											);
											$this->db->insert('ln_ldgr', $datas0);
											
											// Update status of uncollected billings
											$datas1 = array(
												'status' => NULL
											);
											$this->db->where('bill_id', $row_unc->bill_id);;
											$this->db->update('nhgt_bills.header', $datas1);
											
											/*echo '<pre>';
											print_r($datas0);
											echo '</pre>';*/
											
											$amtopost -= $lh->LH_MonthlyAmort;
										}
									}
								}
								//
								
								$id = $this->database->getID($bill['cid'], $data->LH_PN, 'LL_IsPayment');
								
								// Set remarks
								$remarks = '';
								if($data->status != NULL)
								{
									if($data->status == 'adj') {
										$remarks = 'ADJUSTMENT';
									} elseif($data->status == 'due') {
										$remarks = 'DUE TO CLIENT';
									} elseif($data->status == 'pay') {
										$remarks = 'PAYMENT';
									} elseif($data->status == 'rem') {
										$remarks = 'REMITTANCE';
									}
								}
								
								if($lh->LH_LoanTrans == 'SPEC')
								{
									$columns = 'LH_BranchCode, LH_BankBranch, LH_BankAcctNo, LH_LoanTrans, LH_LoanDate';
									$this->db->select($columns, false);
									$this->db->from('ln_hdr');
									$this->db->where('CI_AcctNo', $data->CI_AcctNo);
									$this->db->where('LH_PN', $lh->LH_Reference);
									$this->db->where('LH_IsPending', 0);
									$this->db->where('LH_Processed', 1);
									$this->db->where('LH_Cancelled', 0);
									$qq = $this->db->get();
									$loan = $qq->row();
									
									$lh_pn = $lh->LH_Reference;
									$lh_branchcode = $loan->LH_BranchCode;
									$lh_bankacctno = $loan->LH_BankAcctNo;
									
									$id = '0';
									$loan_date = explode('-', $loan->LH_LoanDate);
									$remarks = 'BONUS ' . $loan_date[0];
								}
								
								if($i == $j)
								{
									$payment =  $amtopost;
								} else {
									$payment = $lh->LH_MonthlyAmort;
								}
								
								$datas = array(
									'ID' => $id,
									'CI_AcctNo'				  => $bill['cid'],
									'LH_PN'					  => $lh_pn,
									'LH_BranchCode_Processed' => $lh_branchcode,
									'LH_BankAcctNo'			  => $lh_bankacctno,
									'LH_LoanType'			  => $lh_loantrans,
									'LL_ORNo'				  => $bill['orprno'],
									'LL_Rebates'			  => 0,
									'LL_InterestAmt'		  => 0,
									'LL_AmountCheck'		  => 0,
									'LL_AmountCash'			  => $payment,
									'LL_AmountCash_Payment'   => $payment,
									'LL_ShortPayment'		  => 0,
									'LL_Refund'				  => 0,
									'LL_Remarks'			  => $remarks,
									'LL_PaymentDate'		  => $now,
									'LL_Processed'			  => 1,
									'LL_IsPayment'			  => 1,
									'LL_IsRefund'			  => 0,
									'LL_IsDeleted'			  => 0,
									'LL_CheckNo'			  => ''
								);
								$this->db->insert('ln_ldgr', $datas);
								
								/*$balance -= $amtopost;
								
								$this->db->set('LH_Balance', $balance);
								$this->db->where('CI_AcctNo', $bill['cid']);
								$this->db->where('LH_PN', $data->LH_PN);
								$this->db->update('ln_hdr');*/
								
								/*echo '<pre>';
								print_r($datas);
								echo '</pre>';
								echo $i . ' ' . $j;*/
								
								$amtopost -= $lh->LH_MonthlyAmort;
								$i++;
							}
						}
						
					}
				}
			}

		$this->db->trans_complete();

		if($arr['error']=='')
		$arr['isOk'] = TRUE;

		die(json_encode($arr));
	}
	
	function collectionor($period = NULL)
	{
		if ( ! check_access('collection report - PR') )
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('');
			return;
		}
		
		$today = $this->uri->segment(3) ? $this->uri->segment(3) : date('Y-m-d');
		$source = $this->uri->segment(4) ? $this->uri->segment(4) : '0';
		$debit_type = $this->uri->segment(5) ? $this->uri->segment(5) : '0';
		$user=$this->uri->segment(6) ? $this->uri->segment(6) : 'a';
		
		$users = $this->rsm->get_report_users( $today, 'OR', $user );
		
		$this->template->set('page_title', 'Collection Report - O.R.');
		$this->template->set('others', 
			array(

				'style' => trims('
					.ddmenu{
						-moz-user-select: none;
						-khtml-user-select: none;
						-webkit-user-select: none;
						user-select: none;
					}
					.ddms{
						border-bottom:1px solid #d3d3d3;
						border-left:1px solid #d3d3d3;
						border-right:1px solid #d3d3d3;
						padding: 1px 2px 2px 2px;
						background-color: #F8F8F8;
					}
					.ddms:hover{
						border-bottom:1px solid grey;
						border-left:1px solid grey;
						border-right:1px solid grey;
						padding: 1px 2px 2px 2px;
						background-color: #E4E4E4;
					}
				'),

				'jscript' => trims('
					<script type="text/javascript" src="'.asset_url().'js/rsm.dropdownchecklist-1.js"></script>
					'),

				'element' => trims("<input id='ordate'type='date'value='$today'style='text-align:center;'/>&nbsp;".
				"Type:
				&nbsp;
				<select id='source'>
					<option value='0' ".($source=='0'?"selected":"").">ALL</option>
					<option value='GSIS' ".($source=='GSIS'?"selected":"").">GSIS</option>
					<option value='SSS' ".($source=='SSS'?"selected":"").">SSS</option>
					<option value='OTHERS' ".($source=='OTHERS'||$source=='PVAO'?"selected":"").">OTHERS</option>
				</select>
				&nbsp;
				Debit Type:
				&nbsp;
				<select id='debit_type'>
					<option value='0' " . ($debit_type == '0' ? "selected='selected'" : "") . "'>ALL</option>
					<option value='1' " . ($debit_type == '1' ? "selected='selected'" : "") . "'>AUTO-DEBIT</option>
					<option value='2' " . ($debit_type == '2' ? "selected='selected'" : "") . "'>POS</option>
				</select>
				&nbsp;
				<select id='s8' multiple>
					$users
				</select>
				".
					"<input id='bapp'type='button'value='Go'class='rsmbtn1'/>"),

				'script' => trims("
					
					$('#bapp').click(function()
					{
						parent.document.location='../../../'+$('#ordate').val()
						+'/'+$('#source').val()+'/'+$('#debit_type').val()+'/'+$('#ze-s8').attr('value');
					});

					$('#ordate').keyup(function(event)
					{	
						if(event.which==13)  $('#bapp').click();
					});

					$('a.btn1').click(function()
					{
						if(confirm('Are you sure you want to POST this Collection?'))
						{
							$.post(
								'../../../../crbpost',
								{
									a  :  $('#abt').html().replace(/,/g,''),
									b  :  $('#ordate').val(),
									c  :  'or'
								},
								function(r)
								{
									if(r.isOk)
										$('a.btn1').hide('slow',function()
										{
											alert(r);
											$(this).parent().html('CRB Posted');
											$('a.btn1').remove();
										});
									else console.log(r.error);
								},
								'json'
							);
						}
					});

					ddl($('#s8'),{placeholder:'Select User...'});
				")
			)
		); 
		$this->template->set('nav_links', 
			array(
				"report/download/orcollection/$today/$source/$debit_type/$user" 		=> 'Download CSV', 
				"report/printpreview/orcollection/$today/$source/$debit_type/$user" 	=> 'Print Preview'
			)
		);
		$data['left_width'] 	= "450";
		$data['right_width'] 	= "450";

		$this->load->model('database');

		$data['datas'] = $this->database->get_ors( $today , $source, $user, $debit_type );
		$data['is_not_crb_posted'] = $this->rsm->is_not_crb_posted( $today, 'or' );

		$this->template->load('template', 'report/orcollection', $data);
		return;
	}
	
	function collectionpr($period = NULL)
	{
		if ( ! check_access('collection report - PR') )
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('');
			return;
		}

		$this->load->helper('code');
		$this->load->model('rsm');

		$today = $this->uri->segment(3) ? $this->uri->segment(3) : date('Y-m-d');
		$source = $this->uri->segment(4) ? $this->uri->segment(4) : '0';
		$user = $this->uri->segment(5) ? $this->uri->segment(5) : 'a';
		$prno = $this->uri->segment(6) ? $this->uri->segment(6) : '0';
		$debit_type = $this->uri->segment(7) ? $this->uri->segment(7) : '0';
		
		$users = $this->rsm->get_report_users( $today, 'PR', $user );
		
		$this->template->set('page_title', 'Collection Report - P.R.');
		$this->template->set('others', 
			array(

				'style' => trims('
					.ddmenu{
						-moz-user-select: none;
						-khtml-user-select: none;
						-webkit-user-select: none;
						user-select: none;
					}
					.ddms{
						border-bottom:1px solid #d3d3d3;
						border-left:1px solid #d3d3d3;
						border-right:1px solid #d3d3d3;
						padding: 1px 2px 2px 2px;
						background-color: #F8F8F8;
					}
					.ddms:hover{
						border-bottom:1px solid grey;
						border-left:1px solid grey;
						border-right:1px solid grey;
						padding: 1px 2px 2px 2px;
						background-color: #E4E4E4;
					}
				'),

				'jscript' => trims('
					<script type="text/javascript" src="'.asset_url().'js/rsm.dropdownchecklist-1.js"></script>
					'),

				'element'=> trims("Date:&nbsp;<input id='prdate'type='date'value='$today'style='text-align:center;'/>&nbsp;".
					"
					Type:.&nbsp;<select id='source'>
						<option value='0' ".($source=='0'?"selected":"").">ALL</option>
						<option value='GSIS' ".($source=='GSIS'?"selected":"").">GSIS</option>
						<option value='SSS' ".($source=='SSS'?"selected":"").">SSS</option>
						<option value='OTHERS' ".($source=='OTHERS'?"selected":"").">OTHERS</option>
					</select>
					&nbsp;
					P.R. No.
					&nbsp;
					<input id='prno' type='text' name='prno' value='".$prno."' style='text-align:center;' />
					&nbsp;
					Debit Type:
					&nbsp;
					<select id='debit_type'>
						<option value='0' " . ($debit_type == '0' ? "selected='selected'" : "") . "'>ALL</option>
						<option value='1' " . ($debit_type == '1' ? "selected='selected'" : "") . "'>AUTO-DEBIT</option>
						<option value='2' " . ($debit_type == '2' ? "selected='selected'" : "") . "'>POS</option>
					</select>
					<select id='s8' multiple>
						$users
					</select>
					".
					"<input id='bapp'type='button'value='Go'class='rsmbtn1'/>"),

				'script'=>trims("
					$('#bapp').click(function()
					{
						var a = $('#ze-s8').attr('value');
						if(a==''){ var user = 'a'; } else { var user = a; }
						parent.document.location='../../../../'+$('#prdate').val()+'/'
						+$('#source').val()+'/'+user+'/'+$('#prno').val()+'/'+$('#debit_type').val();
					});

					$('#prdate').keyup(function(event)
					{
						if(event.which==13)$('#bapp').click();
					});
					
					$('a.btn1').click(function()
					{
						if(confirm('Are you sure you want to POST this Collection?'))
						{
							$.post(
								'../../../../../crbpost',
								{
									a  :  $('#abt').html().replace(/,/g,''),
									b  :  $('#prdate').val(),
									c  :  'pr'
								},
								function(r)
								{
									if(r.isOk)
										$('a.btn1').hide('slow',function()
										{
											$(this).parent().html('CRB Posted');
											$('a.btn1').remove();
										});
									else console.log(r.error);
								},
								'json'
							);
						}
					});

					ddl($('#s8'),{placeholder:'Select User...'});

				")
			)
		); 
		$this->template->set('nav_links', 
			array(
				"report/download/prcollection/$today/$source/$user/$prno/$debit_type" => 'Download CSV', 
				"report/printpreview/prcollection/$today/$source/$user/$prno/$debit_type" => 'Print Preview'
			)
		);
		$data['left_width'] = "450";
		$data['right_width'] = "450";

		$this->load->model('database');

		$data['datas'] = $this->database->get_prs($today, $source, $user, $prno, $debit_type);
		$data['is_not_crb_posted'] = $this->rsm->is_not_crb_posted($today, 'pr');
		
		//print_r($data);
		$this->template->load('template', 'report/prcollection', $data);
		return;
	}
	
	function collectionothers()
	{
		if ( ! check_access('collection report - Others') )
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('');
			return;
		}

		$this->load->helper('code');
		$this->load->model('rsm');

		$today=$this->uri->segment(3)?$this->uri->segment(3):date('Y-m-d');
		$type=$this->uri->segment(3)?$this->uri->segment(4):'0';
		
		$this->template->set('page_title', 'Collection Report - Others');
		$this->template->set('others',
			array(
				'stylesheets'=>"<link type='text/css' rel='stylesheet' href='<?=base_url()?>system/application/assets/css/jquery.datepick.css'>",
				'element'=>"
					Date:&nbsp;<input type='date' id='ocDate' name='ocDate' value='".$today."' style='text-align:center;'>&nbsp;
					Type:&nbsp;<select id='ocType'>
						<option value='0' ".($type=='0'?"selected":"").">ALL</option>
						<option value='BC' ".($type=='BC'?"selected":"").">BANK CHARGE</option>
						<option value='RD' ".($type=='RD'?"selected":"").">REDEPOSIT</option>
						<option value='RC' ".($type=='RC'?"selected":"").">REMITTANCE CHARGE</option>
					</select>
					<input id='bapp'type='button'value='Go'class='rsmbtn1'/>
					",
				'script'=>trims("
					
					$('#bapp').click(function()
					{
						window.location.href='../'+$('#ocDate').val()+'/'+$('#ocType').val();
					});
					
				")
			)
		);
		
		$this->template->set('nav_links', 
			array(
				"report/download/otherscollection/$today/$type" 		=> 'Download CSV', 
				"report/printpreview/otherscollection/$today/$type" 	=> 'Print Preview'
			)
		);
		$data['left_width'] = "450";
		$data['right_width'] = "450";

		$this->load->model('database');
		
		$data['datas'] = $this->database->get_ocr( $today, $type );
		
		$this->template->load('template', 'report/otherscollection', $data);
		return;
	}
	
	// New functions
	function autodebit_coll()
	{
		if ( ! check_access('collection report - Auto-debit') )
		{
			$this->messages->add('Permission denied.', 'error');
			redirect('');
			return;
		}

		$from_date = $this->uri->segment(3) ? $this->uri->segment(3) : date('Y-m-01');
		$to_date=$this->uri->segment(4) ? $this->uri->segment(4) : date('Y-m-t');
		
		$this->template->set('page_title', 'Collection Report - Auto-debit');
		$this->template->set('others',
			array(
				'stylesheets' => '',
				'element' => '
					From:
					&nbsp;
					<input type=\'date\' id=\'from_date\' name=\'from_date\' value=\'' . $from_date . '\' style=\'text-align:center;\'>
					&nbsp;
					To:
					&nbsp;
					<input type=\'date\' id=\'to_date\' name=\'to_date\' value=\'' . $to_date . '\' style=\'text-align:center;\'>
					&nbsp;
					<input id=\'bapp\' type=\'button\' value=\'Go\' class=\'rsmbtn1\'/>
					',
				'script' => trims('
					
					$(\'#bapp\').click(function()
					{
						window.location.href=\'../\' + $(\'#from_date\').val() + \'/\' + $(\'#to_date\').val();
					});
				')
			)
		);
		
		$this->template->set('nav_links', 
			array(
				'report/download/autodebit_coll/' . $from_date . '/' . $to_date => 'Download CSV', 
				'report/printpreview/autodebit_coll/' . $from_date . '/' . $to_date => 'Print Preview'
			)
		);
		$data['left_width'] = '450';
		$data['right_width'] = '450';
		
		$data['datas'] = $this->report_model->fetch_autodebit_coll($from_date, $to_date);
		
		$this->template->load('template', 'report/autodebit_coll', $data);
		return;
	}
	
	function mo_sales_summary($period = NULL)
	{
		$month = $this->uri->segment(3) ? $this->uri->segment(3) : date('m');
		$year = $this->uri->segment(4) ? $this->uri->segment(4) : date('Y');
		$reporttype = $this->uri->segment(5) ? $this->uri->segment(5) : '1';
		
		$months = $this->master_model->fetch_months();
		
		$mo_option = '';
		foreach($months as $months)
		{
			$mo_option .= "<option value='" . $months['id'] . "' " . ($months['id'] == intval($month) ? "selected" : "") . ">" . $months['name'] . "</option>";
		}
		
		$this->template->set('page_title', 'Monthly Sales Summary');
		$this->template->set('others',
			array(
				"stylesheets" => "",
				"element" => "
					Month:&nbsp;
					<select id='month' name='month'>" . $mo_option . "</select>&nbsp;
					Year:&nbsp;
					<input id='year' type='text' name='year' value='" . $year . "' style='text-align:center;'>&nbsp;
					Report Type: &nbsp;
					<select id='reporttype' name='reporttype'>
						<option value='1' " . ($reporttype == "1" ? "selected" : "") .">Clients Total</option>
						<option value='2' " . ($reporttype == "2" ? "selected" : "") .">Sales Total</option>
					</select>&nbsp;
					<input id='bapp' type='button' value='Go' class='rsmbtn1'/>
				",
				"script" => trims("
					
					$('#bapp').click(function()
					{
						window.location.href='../../' + $('#month').val() + '/' + $('#year').val() + '/' + $('#reporttype').val();
					});
					
				")
			)
		);
		
		$this->template->set('nav_links', 
			array(
				'report/download/mo_sales_summary/' . $month . '/' . $year . '/' . $reporttype => 'Download CSV', 
				'report/printpreview/mo_sales_summary/' . $month . '/' . $year . '/' . $reporttype => 'Print Preview'
			)
		);
		$data['left_width'] = '450';
		$data['right_width'] = '450';
		
		$data['datas'] = $this->report_model->fetch_mosalessum($month, $year, $reporttype);
		$data['reporttype'] = $reporttype;
		
		$data['curmoyr'] = date('Y-m-01', strtotime($year . '-' . $month . '-01'));
		$timestring = strtotime($data['curmoyr']);
		$data['premoyr'] = date('Y-m-01', strtotime('-1 month', $timestring));
		
		$this->template->load('template', 'report/mo_sales_summary', $data);
		return;
	}
	
	function mo_coll_summary($period = NULL)
	{
		$month = $this->uri->segment(3) ? $this->uri->segment(3) : date('m');
		$year = $this->uri->segment(4) ? $this->uri->segment(4) : date('Y');
		$colltype = $this->uri->segment(5) ? $this->uri->segment(5) : '1';
		
		$months = $this->master_model->fetch_months();
		
		$mo_option = '';
		foreach($months as $months)
		{
			$mo_option .= "<option value='" . $months['id'] . "' " . ($months['id'] == intval($month) ? "selected" : "") . ">" . $months['name'] . "</option>";
		}
		
		$this->template->set('page_title', 'Monthly Collection Summary');
		$this->template->set('others',
			array(
				"stylesheets" => "",
				"element" => "
					Month:&nbsp;
					<select id='month' name='month'>" . $mo_option . "</select>&nbsp;
					Year:&nbsp;
					<input id='year' type='text' name='year' value='" . $year . "' style='text-align:center;'>&nbsp;
					Collection Type:&nbsp;
					<select id='colltype' name='colltype'>
						<option value='1' " . ($colltype == '1' ? 'selected' : '') .">OR</option>
						<option value='2' " . ($colltype == '2' ? 'selected' : '') .">PR</option>
					</select>&nbsp;
					<input id='bapp' type='button' value='Go' class='rsmbtn1'/>
				",
				"script" => trims("
					
					$('#bapp').click(function()
					{
						window.location.href='../../' + $('#month').val() + '/' + $('#year').val() + '/' + $('#colltype').val();
					});
					
				")
			)
		);
		
		$this->template->set('nav_links', 
			array(
				'report/download/mo_coll_summary/' . $month . '/' . $year . '/' . $colltype => 'Download CSV', 
				'report/printpreview/mo_coll_summary/' . $month . '/' . $year . '/' . $colltype => 'Print Preview'
			)
		);
		$data['left_width'] = '450';
		$data['right_width'] = '450';
		
		$data['datas'] = $this->report_model->fetch_mocollsum($month, $year, $colltype);
		
		$this->template->load('template', 'report/mo_coll_summary', $data);
		return;
	}
	
	function sales($period = NULL)
	{
		$startdate = $this->uri->segment(3) ? $this->uri->segment(3) : date('Y-m-d');
		$enddate = $this->uri->segment(4) ? $this->uri->segment(4) : date('Y-m-d');
		$source = $this->uri->segment(5) ? $this->uri->segment(5) : '0';
		
		$this->template->set('page_title', 'Sales Report');
		$this->template->set('others', 
			array(
				"stylesheets" => "<link type='text/css' rel='stylesheet' href='<?=base_url()?>system/application/assets/css/jquery.datepick.css'>",
				"element" => "
					From:&nbsp;<input type='date' id='FromDate' name='FromDate' value='" . $startdate . "' style='text-align:center;'>&nbsp;
					To:&nbsp;<input type='date' id='ToDate' name='ToDate' value='" . $enddate . "' style='text-align:center;'>&nbsp;
				<select id='source'>
					<option value='0' " . ($source == "0" ? "selected" : "") . ">ALL</option>
					<option value='PEN' " . ($source == 'PEN' ? " selected" : "") . ">CLIENT-GSIS/SSS</option>
					<option value='EMP' " . ($source == 'EMP' ? " selected" : "") . ">ACCOM-EMPLOYEE</option>
					<option value='AGT' " . ($source == 'AGT' ? " selected" : "") . ">ACCOM-AGENT</option>
					<option value='SAL' " . ($source == 'SAL' ? " selected" : "") . ">CLIENT-SALARY</option>
				</select>
				<input id='bapp'type='button'value='Go'class='rsmbtn1'/>
				",
				'script' => trims("
					
					$('#bapp').click(function()
					{
						window.location.href='../../'+$('#FromDate').val()+'/'+$('#ToDate').val()+'/'+$('#source').val();
					});
					
				")
			)
		);
		
		$this->template->set('nav_links', array(
			'report/download/sales/' . $startdate . '/' . $enddate . '/' . $source => 'Download CSV',
			'report/printpreview/sales/' . $startdate . '/' . $enddate . '/' . $source => 'Print Preview'
		));
		
		$data['left_width'] = "450";
		$data['right_width'] = "450";
		
		if($source == '0')
		{
			$src = array('AGT', 'EMP', 'PEN', 'SAL');
			$count = count($src) - 1;
			for($i = 0; $i <= $count; $i++)
			{
				$data['datas'][$i] = $this->report_model->fetch_cv_sales($startdate, $enddate, $src[$i]);
			}
		} else {
			switch($source)
			{
				case 'AGT':
					$i = 0;
					break;
				case 'EMP':
					$i = 1;
					break;
				case 'PEN':
					$i = 2;
					break;
				case 'SAL':
					$i = 3;
					break;
			}
			$data['datas'][$i] = $this->report_model->fetch_cv_sales($startdate, $enddate, $source);
		}
		
		$this->template->load('template', 'report/sales', $data);
		return;
	}
	
	function disbursement($period = NULL)
	{
		$data['isreport'] = FALSE;
		
		$startdate = $this->uri->segment(3) ? $this->uri->segment(3) : date('Y-m-01');
		$enddate = $this->uri->segment(4) ? $this->uri->segment(4) : date('Y-m-d');
		$bankacct = $this->uri->segment(5) ? $this->uri->segment(5) : '0';
		$cvtype = $this->uri->segment(6) ? $this->uri->segment(6) : '0';
		
		$banks = $this->db->query("
			SELECT code,
				value
			FROM parameter
			WHERE code='BANK'
			ORDER BY value ASC;
		")->result_array();
		
		$acctval = '0';
		$bankoption = "<option value='" . $acctval . "' " . ($acctval == $bankacct ? 'selected' : '') . ">ALL</option>";
		
		foreach($banks as $bank)
		{
			$data = explode(';', $bank['value']);
			$bankoption .= "<option value='" . $data[2] . "' " . ($data[2] == $bankacct ? 'selected' : '') . ">" . $data[1] . "</option>";
		}
		
		$this->template->set('page_title', 'Disbursement Report');
		$this->template->set('others', 
			array(
				'stylesheets' => "<link type='text/css' rel='stylesheet' href='<?=base_url()?>system/application/assets/css/jquery.datepick.css'>",
				'element' => "
					From:&nbsp;<input type='date' id='startdate' name='startdate' value='" . $startdate . "' style='text-align: center;'>&nbsp;
					To:&nbsp;<input type='date' id='enddate' name='enddate' value='" . $enddate . "' style='text-align: center;'>&nbsp;
					Bank:&nbsp;<select id='bankacct'>" .
					$bankoption . "
					</select>&nbsp;
					Type&nbsp;
					<select id='cvtype'>
						<option value='0' " . ($cvtype == '0' ? 'selected' : '') . ">ALL</option>
						<option value='D' " . ($cvtype == 'D' ? 'selected' : '') . ">DISBURSEMENT</option>
						<option value='F' " . ($cvtype == 'F' ? 'selected' : '') . ">FUND TRANSFER</option>
						<option value='R' " . ($cvtype == 'R' ? 'selected' : '') . ">REFUND</option>
						<option value='S' " . ($cvtype == 'S' ? 'selected' : '') . ">SALES</option>
					</select>
				<input id='bapp'type='button'value='Go'class='rsmbtn1'/>
				",
				'script' => trims("
					$('#bapp').click(function()
					{
						window.location.href='../../../' + $('#startdate').val() + '/' + $('#enddate').val() + '/' + $('#bankacct').val() + '/' + $('#cvtype').val();
					});
				")
			)
		);
		
		$this->template->set('nav_links', array(
			'report/download/disbursement/' . $startdate . '/' . $enddate . '/' . $bankacct . '/' . $cvtype => 'Download CSV',
			'report/printpreview/disbursement/' . $startdate . '/' . $enddate . '/' . $bankacct . '/' . $cvtype => 'Print Preview'
		));
		
		$data['left_width'] = "450";
		$data['right_width'] = "450";
		
		$this->load->model('database');
		$data['cvtype'] = $cvtype;
		$data['bank'] = $this->db->query("
			SELECT code,
				value
			FROM parameter
			WHERE code='BANK'
			ORDER BY value ASC;
		")->result_array();
		
		foreach($data['bank'] as $b)
		{
			$bank = explode(';', $b['value']);
			$data['banks'][] = $bank[1];
		}
		
		if($bankacct == '0')
		{
			$i = 0;
			foreach($data['bank'] as $bank)
			{
				$bank = explode(';', $bank['value']);
				$data['datas'][$i] = $this->report_model->fetch_disbursement($startdate, $enddate, $bank[2], $cvtype);
				$i++;
			}
		} else {
			$i = 0;
			foreach($data['bank'] as $bank)
			{
				$bank = explode(';', $bank['value']);
				if($bankacct == $bank[2])
				{
					break;
				}
				$i++;
			}
			$data['datas'][$i] = $this->report_model->fetch_disbursement($startdate, $enddate, $bankacct, $cvtype);
		}
		
		$this->template->load('template', 'report/disbursement', $data);
		return;
	}
	
	function advancebonus()
	{
		$fromDate=$this->uri->segment(3)?$this->uri->segment(3):date('Y-m-01');
		$toDate=$this->uri->segment(4)?$this->uri->segment(4):date('Y-m-d');
		
		$this->template->set('page_title', 'Advance Bonus Summary Report');
		$this->template->set('others',
			array(
				'stylesheets'=>"<link type='text/css' rel='stylesheet' href='<?=base_url()?>system/application/assets/css/jquery.datepick.css'>",
				'element'=>"
					From:&nbsp;<input type='date' id='FromDate' name='FromDate' value='".$fromDate."' style='text-align:center;'>&nbsp;
					To:&nbsp;<input type='date' id='ToDate' name='ToDate' value='".$toDate."' style='text-align:center;'>&nbsp;
					<input id='bapp'type='button'value='Go'class='rsmbtn1'/>
					",
				'script'=>trims("
					
					$('#bapp').click(function()
					{
						window.location.href='../'+$('#FromDate').val()+'/'+$('#ToDate').val();
					});
					
				")
			)
		);
		
		$this->template->set('nav_links', 
			array(
				"report/download/advancebonus/$fromDate/$toDate" 		=> 'Download CSV', 
				"report/printpreview/advancebonus/$fromDate/$toDate" 	=> 'Print Preview'
			)
		);
		$data['left_width'] = "450";
		$data['right_width'] = "450";

		$this->load->model('database');
		
		$data['datas'] = $this->database->get_abr( $fromDate, $toDate );
		
		$this->template->load('template', 'report/advancebonus', $data);
		return;
	}
	
	function atmpb_release()
	{
		$from_date = $this->uri->segment(3) ? $this->uri->segment(3) : date('Y-m-01');
		$to_date=$this->uri->segment(4) ? $this->uri->segment(4) : date('Y-m-d');
		
		$this->template->set('page_title', 'ATM/PB Release Summary Report');
		$this->template->set('others',
			array(
				'stylesheets' => '',
				'element' => '
					From:
					&nbsp;
					<input type=\'date\' id=\'from_date\' name=\'from_date\' value=\'' . $from_date . '\' style=\'text-align:center;\'>
					&nbsp;
					To:
					&nbsp;
					<input type=\'date\' id=\'to_date\' name=\'to_date\' value=\'' . $to_date . '\' style=\'text-align:center;\'>
					&nbsp;
					<input id=\'bapp\' type=\'button\' value=\'Go\' class=\'rsmbtn1\'/>
					',
				'script' => trims('
					
					$(\'#bapp\').click(function()
					{
						window.location.href=\'../\' + $(\'#from_date\').val() + \'/\' + $(\'#to_date\').val();
					});
				')
			)
		);
		
		$this->template->set('nav_links', 
			array(
				'report/download/atmpb_release/' . $from_date . '/' . $to_date => 'Download CSV', 
				'report/printpreview/atmpb_release/' . $from_date . '/' . $to_date => 'Print Preview'
			)
		);
		$data['left_width'] = '450';
		$data['right_width'] = '450';
		
		$data['datas'] = $this->report_model->fetch_atmpb_release($from_date, $to_date);
		
		$this->template->load('template', 'report/atmpb_release', $data);
		return;
	}
	
	function comm_month_week()
	{
		$ai_refno = $this->uri->segment(3) ? $this->uri->segment(3) : '0';
		$added_date = $this->uri->segment(4) ? $this->uri->segment(4) : date('Y-m-d');
		
		$this->template->set('page_title', 'Commission Monthly/Weekly');
		$this->template->set('others',
			array(
				'stylesheets' => '',
				'element' => '
					Agent Ref. No.:
					&nbsp;
					<input type=\'text\' id=\'ai_refno\' name=\'ai_refno\' value=\'' . $ai_refno . '\' style=\'text-align:center;\'>
					&nbsp;
					Added Date:
					&nbsp;
					<input type=\'text\' id=\'added_date\' name=\'added_date\' value=\'' . $added_date . '\' style=\'text-align:center;\'>
					&nbsp;
					<input id=\'bapp\' type=\'button\' value=\'Go\' class=\'rsmbtn1\'/>
					',
				'script' => trims('
					
					$(\'#bapp\').click(function()
					{
						window.location.href=\'../\' + $(\'#ai_refno\').val() + \'/\' + $(\'#added_date\').val();
					});
				')
			)
		);
		
		$this->template->set('nav_links', 
			array(
				'report/download/comm_month_week/' . $ai_refno . '/' . $added_date => 'Download CSV', 
				'report/printpreview/comm_month_week/' . $ai_refno . '/' . $added_date => 'Print Preview'
			)
		);
		$data['left_width'] = '450';
		$data['right_width'] = '450';
		
		$data['datas'] = $this->report_model->fetch_agent_comm($ai_refno, $added_date);
		
		$this->template->load('template', 'report/comm_month_week', $data);
		return;
	}
	
	function commission()
	{
		$from_date = $this->uri->segment(3) ? $this->uri->segment(3) : date('Y-m-d');
		$to_date = $this->uri->segment(4) ? $this->uri->segment(4) : date('Y-m-d');
		$agent_type = $this->uri->segment(5) ? $this->uri->segment(5) : '0';
		
		$this->template->set('page_title', 'Commission Summary');
		$this->template->set('others',
			array(
				'stylesheets' => '',
				'element' => '
					From:
					&nbsp;
		<input type=\'text\' id=\'from_date\' name=\'from_date\' value=\'' . $from_date . '\' style=\'text-align:center;\'>
					&nbsp;
					To:
					&nbsp;
					<input type=\'text\' id=\'to_date\' name=\'to_date\' value=\'' . $to_date . '\' style=\'text-align:center;\'>
					&nbsp;
					<select id=\'agent_type\' name=\'agent_type\'>
						<option value=\'0\' ' . ($agent_type == '0' ? 'selected' : '') . '>Agent</option>
						<option value=\'1\' ' . ($agent_type == '1' ? 'selected' : '') . '>Sub-Agent</option>
					</select>
					&nbsp;
					<input id=\'bapp\' type=\'button\' value=\'Go\' class=\'rsmbtn1\'/>
					',
				'script' => trims('
					
					$(\'#bapp\').click(function()
					{
						window.location.href=\'../../\' + $(\'#from_date\').val() + \'/\' + $(\'#to_date\').val() + \'/\' + $(\'#agent_type\').val();
					});
				')
			)
		);
		
		$this->template->set('nav_links', 
			array(
				'report/download/commission/' . $from_date. '/' . $to_date . '/' . $agent_type => 'Download CSV', 
				'report/printpreview/commission/' . $from_date . '/' . $to_date . '/' . $agent_type => 'Print Preview'
			)
		);
		$data['left_width'] = '450';
		$data['right_width'] = '450';
		
		$data['datas'] = $this->report_model->fetch_commission($from_date, $to_date, $agent_type);
		
		$this->template->load('template', 'report/commission', $data);
		return;
	}
	
	function newclient()
	{
		$fromDate=$this->uri->segment(3)?$this->uri->segment(3):date('Y-m-01');
		$toDate=$this->uri->segment(4)?$this->uri->segment(4):date('Y-m-d');
		
		$this->template->set('page_title', 'New Client Summary Report');
		$this->template->set('others',
			array(
				'stylesheets'=>"<link type='text/css' rel='stylesheet' href='<?=base_url()?>system/application/assets/css/jquery.datepick.css'>",
				'element'=>"
					From:&nbsp;<input type='date' id='FromDate' name='FromDate' value='".$fromDate."' style='text-align:center;'>&nbsp;
					To:&nbsp;<input type='date' id='ToDate' name='ToDate' value='".$toDate."' style='text-align:center;'>&nbsp;
					<input id='bapp'type='button'value='Go'class='rsmbtn1'/>
					",
				'script'=>trims("
					
					$('#bapp').click(function()
					{
						window.location.href='../'+$('#FromDate').val()+'/'+$('#ToDate').val();
					});
					
				")
			)
		);
		
		$this->template->set('nav_links', 
			array(
				"report/download/newclient/$fromDate/$toDate" 		=> 'Download CSV', 
				"report/printpreview/newclient/$fromDate/$toDate" 	=> 'Print Preview'
			)
		);
		$data['left_width'] = "450";
		$data['right_width'] = "450";

		$this->load->model('database');
		
		$data['datas'] = $this->database->get_ncr( $fromDate, $toDate );
		
		$this->template->load('template', 'report/newclient', $data);
		return;
	}
	
	function returningclient()
	{
		$fromDate=$this->uri->segment(3)?$this->uri->segment(3):date('Y-m-01');
		$toDate=$this->uri->segment(4)?$this->uri->segment(4):date('Y-m-d');
		
		$this->template->set('page_title', 'Returning Client Summary Report');
		$this->template->set('others',
			array(
				'stylesheets'=>"<link type='text/css' rel='stylesheet' href='<?=base_url()?>system/application/assets/css/jquery.datepick.css'>",
				'element'=>"
					From:&nbsp;<input type='date' id='FromDate' name='FromDate' value='".$fromDate."' style='text-align:center;'>&nbsp;
					To:&nbsp;<input type='date' id='ToDate' name='ToDate' value='".$toDate."' style='text-align:center;'>&nbsp;
					<input id='bapp'type='button'value='Go'class='rsmbtn1'/>
					",
				'script'=>trims("
					
					$('#bapp').click(function()
					{
						window.location.href='../'+$('#FromDate').val()+'/'+$('#ToDate').val();
					});
					
				")
			)
		);
		
		$this->template->set('nav_links', 
			array(
				"report/download/returningclient/$fromDate/$toDate" 		=> 'Download CSV', 
				"report/printpreview/returningclient/$fromDate/$toDate" 	=> 'Print Preview'
			)
		);
		$data['left_width'] = "450";
		$data['right_width'] = "450";

		$this->load->model('database');
		
		$data['datas'] = $this->database->get_rtr( $fromDate, $toDate );
		
		$this->template->load('template', 'report/returningclient', $data);
		return;
	}
	
	function rpcf()
	{
		$rpcf_date = $this->uri->segment(3) ? $this->uri->segment(3) : date('Y-m-d');
		$group = $this->uri->segment(4) ? $this->uri->segment(4) : '0';
		
		$this->template->set('page_title', 'RPCF Summary Report');
		$this->template->set('others',
			array(
				'stylesheets' => '<link type=\'text/css\' rel=\'stylesheet\' href=\'<?=base_url()?>system/application/assets/css/jquery.datepick.css\'>',
				'element' => '
					Replenish Date:
					&nbsp;
					<input type=\'date\' id=\'rpcf_date\' name=\'rpcf_date\' value=\'' . $rpcf_date . '\' style=\'text-align:center;\'>
					&nbsp;
					Client Group:
					&nbsp;
					<select id=\'group\'>
						<option value=\'0\'' . ($group == '0' ? 'selected' : '') . '>ALL</option>
						<option value=\'1\'' . ($group == '1' ? 'selected' : '') . '>NEW</option>
						<option value=\'2\'' . ($group == '2' ? 'selected' : '') . '>OLD</option>
					</select>
					&nbsp;
					<input id=\'bapp\' type=\'button\' value=\'Go\'class=\'rsmbtn1\'/>
					',
				'script'=>trims('
					
					$(\'#bapp\').click(function()
					{
						window.location.href=\'../\'+$(\'#rpcf_date\').val()+\'/\'+$(\'#group\').val();
					});
					
				')
			)
		);
		
		$this->template->set('nav_links', 
			array(
				'report/download/rpcf/' . $rpcf_date . '/' . $group 		=> 'Download CSV', 
				'report/printpreview/rpcf/' . $rpcf_date . '/' . $group 	=> 'Print Preview'
			)
		);
		$data['left_width'] = "450";
		$data['right_width'] = "450";
		
		$data['datas'] = $this->report_model->fetch_rpcf($rpcf_date, $group);
		
		$this->template->load('template', 'report/rpcf', $data);
		return;
	}
	
	function balancesheet($period = NULL)
	{
		$this->template->set('page_title', 'Balance Sheet');
		$this->template->set('nav_links', array(
			'report/download/balancesheet' => 'Download CSV',
			'report/printpreview/balancesheet' => 'Print Preview'
		));
		$data['left_width'] = "450";
		$data['right_width'] = "450";
		$this->template->load('template', 'report/balancesheet', $data);
		return;
	}
	
	function profitandloss($period = NULL)
	{
		$this->template->set('page_title', 'Profit And Loss Statement');
		$this->template->set('nav_links', array(
			'report/download/profitandloss' 	=> 'Download CSV',
			'report/printpreview/profitandloss' => 'Print Preview'
		));
		$data['left_width'] = "450";
		$data['right_width'] = "450";
		$this->template->load('template', 'report/profitandloss', $data);
		return;
	}
	
	function trialbalance($period = NULL)
	{
		$this->template->set('page_title', 'Trial Balance');
		$this->template->set('nav_links', array('report/download/trialbalance' => 'Download CSV', 'report/printpreview/trialbalance' => 'Print Preview'));

		$this->load->library('accountlist');
		$this->template->load('template', 'report/trialbalance');
		return;
	}
	
	function ledgerst($ledger_id = 0)
	{
		$this->load->helper('text');

		/* Pagination setup */
		$this->load->library('pagination');

		$this->template->set('page_title', 'Ledger Statement');
		if ($ledger_id != 0)
			$this->template->set('nav_links', array(
				'report/download/ledgerst/' . $ledger_id  		=> 'Download CSV',
				'report/printpreview/ledgerst/' . $ledger_id 	=> 'Print Preview'
			));

		if ($_POST)
		{
			$ledger_id = $this->input->post('ledger_id', TRUE);
			redirect('report/ledgerst/' . $ledger_id);
		}
		$data['print_preview'] = FALSE;
		$data['ledger_id'] = $ledger_id;

		/* Checking for valid ledger id */
		if ($data['ledger_id'] > 0)
		{
			$this->db->from('ledgers')->where('id', $data['ledger_id'])->limit(1);
			if ($this->db->get()->num_rows() < 1)
			{
				$this->messages->add('Invalid Ledger account.', 'error');
				redirect('report/ledgerst');
				return;
			}
		} else if ($data['ledger_id'] < 0) {
			$this->messages->add('Invalid Ledger account.', 'error');
			redirect('report/ledgerst');
			return;
		}

		$this->template->load('template', 'report/ledgerst', $data);
		return;
	}

	function reconciliation($reconciliation_type = '', $ledger_id = 0)
	{
		$this->load->helper('text');

		/* Pagination setup */
		$this->load->library('pagination');

		$this->template->set('page_title', 'Reconciliation');

		/* Check if path is 'all' or 'pending' */
		$data['show_all'] = FALSE;
		$data['print_preview'] = FALSE;
		$data['ledger_id'] = $ledger_id;
		if ($reconciliation_type == 'all')
		{
			$data['reconciliation_type'] = 'all';
			$data['show_all'] = TRUE;
			if ($ledger_id > 0)
				$this->template->set('nav_links', array(
					'report/download/reconciliation/' . $ledger_id . '/all'  	=> 'Download CSV',
					'report/printpreview/reconciliation/' . $ledger_id . '/all' => 'Print Preview'
				));
		} else if ($reconciliation_type == 'pending') {
			$data['reconciliation_type'] = 'pending';
			$data['show_all'] = FALSE;
			if ($ledger_id > 0)
				$this->template->set('nav_links', array('report/download/reconciliation/' . $ledger_id . '/pending'  => 'Download CSV', 'report/printpreview/reconciliation/' . $ledger_id . '/pending'  => 'Print Preview'));
		} else {
			$this->messages->add('Invalid path.', 'error');
			redirect('report/reconciliation/pending');
			return;
		}

		/* Checking for valid ledger id and reconciliation status */
		if ($data['ledger_id'] > 0)
		{
			$this->db->from('ledgers')->where('id', $data['ledger_id'])->where('reconciliation', 1)->limit(1);
			if ($this->db->get()->num_rows() < 1)
			{
				$this->messages->add(
					'Invalid Ledger account or Reconciliation is not enabled for the Ledger account.', 
					'error'
				);
				redirect('report/reconciliation/' . $reconciliation_type);
				return;
			}
		} else if ($data['ledger_id'] < 0) {
			$this->messages->add('Invalid Ledger account.', 'error');
			redirect('report/reconciliation/' . $reconciliation_type);
			return;
		}

		if ($_POST)
		{
			/* Check if Ledger account is changed or reconciliation is updated */
			if ($_POST['submit'] == 'Submit')
			{
				$ledger_id = $this->input->post('ledger_id', TRUE);
				if ($this->input->post('show_all', TRUE))
				{
					redirect('report/reconciliation/all/' . $ledger_id);
					return;
				} else {
					redirect('report/reconciliation/pending/' . $ledger_id);
					return;
				}
			} else if ($_POST['submit'] == 'Update') {

				$data_reconciliation_date = $this->input->post('reconciliation_date', TRUE);

				/* Form validations */
				foreach ($data_reconciliation_date as $id => $row)
				{
					/* If reconciliation date is present then check for valid date else only trim */
					if ($row)
						$this->form_validation->set_rules('reconciliation_date[' . $id . ']', 'Reconciliation date', 'trim|required|is_date|is_date_within_range_reconcil');
					else
						$this->form_validation->set_rules('reconciliation_date[' . $id . ']', 'Reconciliation date', 'trim');
				}

				if ($this->form_validation->run() == FALSE)
				{
					$this->messages->add(validation_errors(), 'error');
					$this->template->load('template', 'report/reconciliation', $data);
					return;
				} else {
					/* Updating reconciliation date */
					foreach ($data_reconciliation_date as $id => $row)
					{
						$this->db->trans_start();
						if ($row)
						{
							$update_data = array(
								'reconciliation_date' => date_php_to_mysql($row),
							);
						} else {
							$update_data = array(
								'reconciliation_date' => NULL,
							);
						}
						if ( ! $this->db->where('id', $id)->update('entry_items', $update_data))
						{
							$this->db->trans_rollback();
							$this->messages->add('Error updating reconciliation.', 'error');
							$this->logger->write_message("error", "Error updating reconciliation for entry item [id:" . $id . "]");
						} else {
							$this->db->trans_complete();
						}
					}
					$this->messages->add('Updated reconciliation.', 'success');
					$this->logger->write_message("success", 'Updated reconciliation.');
				}
			}
		}
		$this->template->load('template', 'report/reconciliation', $data);
		return;
	}



	function download($statement, $id = NULL)
	{
		/********************** TRIAL BALANCE *************************/
		if ($statement == "trialbalance")
		{
			$this->load->model('Ledger_model');
			$all_ledgers = $this->Ledger_model->get_all_ledgers();
			$counter = 0;
			$trialbalance = array();
			$temp_dr_total = 0;
			$temp_cr_total = 0;

			$trialbalance[$counter] = array ("TRIAL BALANCE", "", "", "", "", "", "", "", "");
			$counter++;
			$trialbalance[$counter] = array ("FY " . date_mysql_to_php($this->config->item('account_fy_start')) . " - " . date_mysql_to_php($this->config->item('account_fy_end')), "", "", "", "", "", "", "", "");
			$counter++;

			$trialbalance[$counter][0]= "Ledger";
			$trialbalance[$counter][1]= "";
			$trialbalance[$counter][2]= "Opening";
			$trialbalance[$counter][3]= "";
			$trialbalance[$counter][4]= "Closing";
			$trialbalance[$counter][5]= "";
			$trialbalance[$counter][6]= "Dr Total";
			$trialbalance[$counter][7]= "";
			$trialbalance[$counter][8]= "Cr Total";
			$counter++;

			foreach ($all_ledgers as $ledger_id => $ledger_name)
			{
				if ($ledger_id == 0) continue;

				$trialbalance[$counter][0] = $ledger_name;

				list ($opbal_amount, $opbal_type) = $this->Ledger_model->get_op_balance($ledger_id);
				if (float_ops($opbal_amount, 0, '=='))
				{
					$trialbalance[$counter][1] = "";
					$trialbalance[$counter][2] = 0;
				} else {
					$trialbalance[$counter][1] = convert_dc($opbal_type);
					$trialbalance[$counter][2] = $opbal_amount;
				}

				$clbal_amount = $this->Ledger_model->get_ledger_balance($ledger_id);

				if (float_ops($clbal_amount, 0, '=='))
				{
					$trialbalance[$counter][3] = "";
					$trialbalance[$counter][4] = 0;
				} else if ($clbal_amount < 0) {
					$trialbalance[$counter][3] = "Cr";
					$trialbalance[$counter][4] = convert_cur(-$clbal_amount);
				} else {
					$trialbalance[$counter][3] = "Dr";
					$trialbalance[$counter][4] = convert_cur($clbal_amount);
				}

				$dr_total = $this->Ledger_model->get_dr_total($ledger_id);
				if ($dr_total)
				{
					$trialbalance[$counter][5] = "Dr";
					$trialbalance[$counter][6] = convert_cur($dr_total);
					$temp_dr_total = float_ops($temp_dr_total, $dr_total, '+');
				} else {
					$trialbalance[$counter][5] = "";
					$trialbalance[$counter][6] = 0;
				}

				$cr_total = $this->Ledger_model->get_cr_total($ledger_id);
				if ($cr_total)
				{
					$trialbalance[$counter][7] = "Cr";
					$trialbalance[$counter][8] = convert_cur($cr_total);
					$temp_cr_total = float_ops($temp_cr_total, $cr_total, '+');
				} else {
					$trialbalance[$counter][7] = "";
					$trialbalance[$counter][8] = 0;
				}
				$counter++;
			}

			$trialbalance[$counter][0]= "";
			$trialbalance[$counter][1]= "";
			$trialbalance[$counter][2]= "";
			$trialbalance[$counter][3]= "";
			$trialbalance[$counter][4]= "";
			$trialbalance[$counter][5]= "";
			$trialbalance[$counter][6]= "";
			$trialbalance[$counter][7]= "";
			$trialbalance[$counter][8]= "";
			$counter++;

			$trialbalance[$counter][0]= "Total";
			$trialbalance[$counter][1]= "";
			$trialbalance[$counter][2]= "";
			$trialbalance[$counter][3]= "";
			$trialbalance[$counter][4]= "";
			$trialbalance[$counter][5]= "Dr";
			$trialbalance[$counter][6]= convert_cur($temp_dr_total);
			$trialbalance[$counter][7]= "Cr";
			$trialbalance[$counter][8]= convert_cur($temp_cr_total);

			$this->load->helper('csv');
			echo array_to_csv($trialbalance, "trialbalance.csv");
			return;
		}

		/********************** LEDGER STATEMENT **********************/
		if ($statement == "ledgerst")
		{
			$this->load->helper('text');
			$ledger_id = (int)$this->uri->segment(4);
			if ($ledger_id < 1)
				return;

			$this->load->model('Ledger_model');
			$cur_balance = 0;
			$counter = 0;
			$ledgerst = array();

			$ledgerst[$counter] = array ("", "", "LEDGER STATEMENT FOR " . strtoupper($this->Ledger_model->get_name($ledger_id)), "", "", "", "", "", "", "", "");
			$counter++;
			$ledgerst[$counter] = array ("", "", "FY " . date_mysql_to_php($this->config->item('account_fy_start')) . " - " . date_mysql_to_php($this->config->item('account_fy_end')), "", "", "", "", "", "", "", "");
			$counter++;

			$ledgerst[$counter][0]= "Date";
			$ledgerst[$counter][1]= "Number";
			$ledgerst[$counter][2]= "Ledger Name";
			$ledgerst[$counter][3]= "Narration";
			$ledgerst[$counter][4]= "Type";
			$ledgerst[$counter][5]= "";
			$ledgerst[$counter][6]= "Dr Amount";
			$ledgerst[$counter][7]= "";
			$ledgerst[$counter][8]= "Cr Amount";
			$ledgerst[$counter][9]= "";
			$ledgerst[$counter][10]= "Balance";
			$counter++;

			/* Opening Balance */
			list ($opbalance, $optype) = $this->Ledger_model->get_op_balance($ledger_id);
			$ledgerst[$counter] = array ("Opening Balance", "", "", "", "", "", "", "", "", convert_dc($optype), $opbalance);
			if ($optype == "D")
				$cur_balance = float_ops($cur_balance, $opbalance, '+');
			else
				$cur_balance = float_ops($cur_balance, $opbalance, '-');
			$counter++;

			$this->db->select('entries.id as entries_id, entries.number as entries_number, entries.date as entries_date, entries.narration as entries_narration, entries.entry_type as entries_entry_type, entry_items.amount as entry_items_amount, entry_items.dc as entry_items_dc');
			$this->db->from('entries')->join('entry_items', 'entries.id = entry_items.entry_id')->where('entry_items.ledger_id', $ledger_id)->order_by('entries.date', 'asc')->order_by('entries.number', 'asc');
			$ledgerst_q = $this->db->get();
			foreach ($ledgerst_q->result() as $row)
			{
				/* Entry Type */
				$current_entry_type = entry_type_info($row->entries_entry_type);

				$ledgerst[$counter][0] = date_mysql_to_php($row->entries_date);
				$ledgerst[$counter][1] = full_entry_number($row->entries_entry_type, $row->entries_number);

				/* Opposite entry name */
				$ledgerst[$counter][2] = $this->Ledger_model->get_opp_ledger_name($row->entries_id, $current_entry_type['label'], $row->entry_items_dc, 'text');
				$ledgerst[$counter][3] = $row->entries_narration;
				$ledgerst[$counter][4] = $current_entry_type['name'];

				if ($row->entry_items_dc == "D")
				{
					$cur_balance = float_ops($cur_balance, $row->entry_items_amount, '+');
					$ledgerst[$counter][5] = convert_dc($row->entry_items_dc);
					$ledgerst[$counter][6] = $row->entry_items_amount;
					$ledgerst[$counter][7] = "";
					$ledgerst[$counter][8] = "";

				} else {
					$cur_balance = float_ops($cur_balance, $row->entry_items_amount, '-');
					$ledgerst[$counter][5] = "";
					$ledgerst[$counter][6] = "";
					$ledgerst[$counter][7] = convert_dc($row->entry_items_dc);
					$ledgerst[$counter][8] = $row->entry_items_amount;
				}

				if (float_ops($cur_balance, 0, '=='))
				{
					$ledgerst[$counter][9] = "";
					$ledgerst[$counter][10] = 0;
				} else if (float_ops($cur_balance, 0, '<')) {
					$ledgerst[$counter][9] = "Cr";
					$ledgerst[$counter][10] = convert_cur(-$cur_balance);
				} else {
					$ledgerst[$counter][9] = "Dr";
					$ledgerst[$counter][10] =  convert_cur($cur_balance);
				}
				$counter++;
			}

			$ledgerst[$counter][0]= "Closing Balance";
			$ledgerst[$counter][1]= "";
			$ledgerst[$counter][2]= "";
			$ledgerst[$counter][3]= "";
			$ledgerst[$counter][4]= "";
			$ledgerst[$counter][5]= "";
			$ledgerst[$counter][6]= "";
			$ledgerst[$counter][7]= "";
			$ledgerst[$counter][8]= "";
			if (float_ops($cur_balance, 0, '<'))
			{
				$ledgerst[$counter][9]= "Cr";
				$ledgerst[$counter][10]= convert_cur(-$cur_balance);
			} else {
				$ledgerst[$counter][9]= "Dr";
				$ledgerst[$counter][10]= convert_cur($cur_balance);
			}
			$counter++;

			$ledgerst[$counter] = array ("", "", "", "", "", "", "", "", "", "", "");
			$counter++;

			/* Final Opening and Closing Balance */
			$clbalance = $this->Ledger_model->get_ledger_balance($ledger_id);

			$ledgerst[$counter] = array ("Opening Balance", convert_dc($optype), $opbalance, "", "", "", "", "", "", "", "");
			$counter++;

			if (float_ops($clbalance, 0, '=='))
				$ledgerst[$counter] = array ("Closing Balance", "", 0, "", "", "", "", "", "", "", "");
			else if ($clbalance < 0)
				$ledgerst[$counter] = array ("Closing Balance", "Cr", convert_cur(-$clbalance), "", "", "", "", "", "", "", "");
			else
				$ledgerst[$counter] = array ("Closing Balance", "Dr", convert_cur($clbalance), "", "", "", "", "", "", "", "");

			$this->load->helper('csv');
			echo array_to_csv($ledgerst, "ledgerst.csv");
			return;
		}

		/********************** RECONCILIATION ************************/
		if ($statement == "reconciliation")
		{
			$ledger_id = (int)$this->uri->segment(4);
			$reconciliation_type = $this->uri->segment(5);

			if ($ledger_id < 1)
				return;
			if ( ! (($reconciliation_type == 'all') or ($reconciliation_type == 'pending')))
				return;

			$this->load->model('Ledger_model');
			$cur_balance = 0;
			$counter = 0;
			$ledgerst = array();

			$ledgerst[$counter] = array ("", "", "RECONCILIATION STATEMENT FOR " . strtoupper($this->Ledger_model->get_name($ledger_id)), "", "", "", "", "", "", "");
			$counter++;
			$ledgerst[$counter] = array ("", "", "FY " . date_mysql_to_php($this->config->item('account_fy_start')) . " - " . date_mysql_to_php($this->config->item('account_fy_end')), "", "", "", "", "", "", "");
			$counter++;

			$ledgerst[$counter][0]= "Date";
			$ledgerst[$counter][1]= "Number";
			$ledgerst[$counter][2]= "Ledger Name";
			$ledgerst[$counter][3]= "Narration";
			$ledgerst[$counter][4]= "Type";
			$ledgerst[$counter][5]= "";
			$ledgerst[$counter][6]= "Dr Amount";
			$ledgerst[$counter][7]= "";
			$ledgerst[$counter][8]= "Cr Amount";
			$ledgerst[$counter][9]= "Reconciliation Date";
			$counter++;

			/* Opening Balance */
			list ($opbalance, $optype) = $this->Ledger_model->get_op_balance($ledger_id);

			$this->db->select('entries.id as entries_id, entries.number as entries_number, entries.date as entries_date, entries.narration as entries_narration, entries.entry_type as entries_entry_type, entry_items.amount as entry_items_amount, entry_items.dc as entry_items_dc, entry_items.reconciliation_date as entry_items_reconciliation_date');
			if ($reconciliation_type == 'all')
				$this->db->from('entries')->join('entry_items', 'entries.id = entry_items.entry_id')->where('entry_items.ledger_id', $ledger_id)->order_by('entries.date', 'asc')->order_by('entries.number', 'asc');
			else
				$this->db->from('entries')->join('entry_items', 'entries.id = entry_items.entry_id')->where('entry_items.ledger_id', $ledger_id)->where('entry_items.reconciliation_date', NULL)->order_by('entries.date', 'asc')->order_by('entries.number', 'asc');
			$ledgerst_q = $this->db->get();
			foreach ($ledgerst_q->result() as $row)
			{
				/* Entry Type */
				$current_entry_type = entry_type_info($row->entries_entry_type);

				$ledgerst[$counter][0] = date_mysql_to_php($row->entries_date);
				$ledgerst[$counter][1] = full_entry_number($row->entries_entry_type, $row->entries_number);

				/* Opposite entry name */
				$ledgerst[$counter][2] = $this->Ledger_model->get_opp_ledger_name($row->entries_id, $current_entry_type['label'], $row->entry_items_dc, 'text');
				$ledgerst[$counter][3] = $row->entries_narration;
				$ledgerst[$counter][4] = $current_entry_type['name'];

				if ($row->entry_items_dc == "D")
				{
					$ledgerst[$counter][5] = convert_dc($row->entry_items_dc);
					$ledgerst[$counter][6] = $row->entry_items_amount;
					$ledgerst[$counter][7] = "";
					$ledgerst[$counter][8] = "";

				} else {
					$ledgerst[$counter][5] = "";
					$ledgerst[$counter][6] = "";
					$ledgerst[$counter][7] = convert_dc($row->entry_items_dc);
					$ledgerst[$counter][8] = $row->entry_items_amount;
				}

				if ($row->entry_items_reconciliation_date)
				{
					$ledgerst[$counter][9] = date_mysql_to_php($row->entry_items_reconciliation_date);
				} else {
					$ledgerst[$counter][9] = "";
				}
				$counter++;
			}

			$counter++;
			$ledgerst[$counter] = array ("", "", "", "", "", "", "", "", "", "");
			$counter++;

			/* Final Opening and Closing Balance */
			$clbalance = $this->Ledger_model->get_ledger_balance($ledger_id);

			$ledgerst[$counter] = array ("Opening Balance", convert_dc($optype), $opbalance, "", "", "", "", "", "", "");
			$counter++;

			if (float_ops($clbalance, 0, '=='))
				$ledgerst[$counter] = array ("Closing Balance", "", 0, "", "", "", "", "", "", "");
			else if (float_ops($clbalance, 0, '<'))
				$ledgerst[$counter] = array ("Closing Balance", "Cr", convert_cur(-$clbalance), "", "", "", "", "", "", "");
			else
				$ledgerst[$counter] = array ("Closing Balance", "Dr", convert_cur($clbalance), "", "", "", "", "", "", "");

			/************* Final Reconciliation Balance ***********/

			/* Reconciliation Balance - Dr */
			$this->db->select_sum('amount', 'drtotal')->from('entry_items')->join('entries', 'entries.id = entry_items.entry_id')->where('entry_items.ledger_id', $ledger_id)->where('entry_items.dc', 'D')->where('entry_items.reconciliation_date IS NOT NULL');
			$dr_total_q = $this->db->get();
			if ($dr_total = $dr_total_q->row())
				$reconciliation_dr_total = $dr_total->drtotal;
			else
				$reconciliation_dr_total = 0;

			/* Reconciliation Balance - Cr */
			$this->db->select_sum('amount', 'crtotal')->from('entry_items')->join('entries', 'entries.id = entry_items.entry_id')->where('entry_items.ledger_id', $ledger_id)->where('entry_items.dc', 'C')->where('entry_items.reconciliation_date IS NOT NULL');
			$cr_total_q = $this->db->get();
			if ($cr_total = $cr_total_q->row())
				$reconciliation_cr_total = $cr_total->crtotal;
			else
				$reconciliation_cr_total = 0;

			$reconciliation_total = float_ops($reconciliation_dr_total, $reconciliation_cr_total, '-');
			$reconciliation_pending = float_ops($clbalance, $reconciliation_total, '-');

			$counter++;
			if (float_ops($reconciliation_pending, 0, '=='))
				$ledgerst[$counter] = array ("Reconciliation Pending", "", 0, "", "", "", "", "", "", "");
			else if (float_ops($reconciliation_pending, 0, '<'))
				$ledgerst[$counter] = array ("Reconciliation Pending", "Cr", convert_cur(-$reconciliation_pending), "", "", "", "", "", "", "");
			else
				$ledgerst[$counter] = array ("Reconciliation Pending", "Dr", convert_cur($reconciliation_pending), "", "", "", "", "", "", "");

			$counter++;
			if (float_ops($reconciliation_total, 0, '=='))
				$ledgerst[$counter] = array ("Reconciliation Total", "", 0, "", "", "", "", "", "", "");
			else if (float_ops($reconciliation_total, 0, '<'))
				$ledgerst[$counter] = array ("Reconciliation Total", "Cr", convert_cur(-$reconciliation_total), "", "", "", "", "", "", "");
			else
				$ledgerst[$counter] = array ("Reconciliation Total", "Dr", convert_cur($reconciliation_total), "", "", "", "", "", "", "");

			$this->load->helper('csv');
			echo array_to_csv($ledgerst, "reconciliation.csv");
			return;
		}
		
		/************************ BALANCE SHEET ***********************/
		if ($statement == "balancesheet")
		{
			$this->load->library('accountlist');
			$this->load->model('Ledger_model');

			$liability = new Accountlist();
			$liability->init(2);
			$liability_array = $liability->build_array();
			$liability_depth = Accountlist::$max_depth;
			$liability_total = -$liability->total;

			Accountlist::reset_max_depth();

			$asset = new Accountlist();
			$asset->init(1);
			$asset_array = $asset->build_array();
			$asset_depth = Accountlist::$max_depth;
			$asset_total = $asset->total;

			$liability->to_csv($liability_array);
			Accountlist::add_blank_csv();
			$asset->to_csv($asset_array);

			$income = new Accountlist();
			$income->init(3);
			$expense = new Accountlist();
			$expense->init(4);
			$income_total = -$income->total;
			$expense_total = $expense->total;
			$pandl = float_ops($income_total, $expense_total, '-');
			$diffop = $this->Ledger_model->get_diff_op_balance();

			Accountlist::add_blank_csv();
			/* Liability side */
			$total = $liability_total;
			Accountlist::add_row_csv(array("Liabilities and Owners Equity Total", convert_cur($liability_total)));
		
			/* If Profit then Liability side, If Loss then Asset side */
			if (float_ops($pandl, 0, '!='))
			{
				if (float_ops($pandl, 0, '>'))
				{
					$total = float_ops($total, $pandl, '+');
					Accountlist::add_row_csv(array("Profit & Loss Account (Net Profit)", convert_cur($pandl)));
				}
			}

			/* If Op balance Dr then Liability side, If Op balance Cr then Asset side */
			if (float_ops($diffop, 0, '!='))
			{
				if (float_ops($diffop, 0, '>'))
				{
					$total = float_ops($total, $diffop, '+');
					Accountlist::add_row_csv(array("Diff in O/P Balance", "Dr " . convert_cur($diffop)));
				}
			}

			Accountlist::add_row_csv(array("Total - Liabilities and Owners Equity", convert_cur($total)));

			/* Asset side */
			$total = $asset_total;
			Accountlist::add_row_csv(array("Asset Total", convert_cur($asset_total)));
		
			/* If Profit then Liability side, If Loss then Asset side */
			if (float_ops($pandl, 0, '!='))
			{
				if (float_ops($pandl, 0, '<'))
				{
					$total = float_ops($total, -$pandl, '+');
					Accountlist::add_row_csv(array("Profit & Loss Account (Net Loss)", convert_cur(-$pandl)));
				}
			}
		
			/* If Op balance Dr then Liability side, If Op balance Cr then Asset side */
			if (float_ops($diffop, 0, '!='))
			{
				if (float_ops($diffop, 0, '<'))
				{
					$total = float_ops($total, -$diffop, '+');
					Accountlist::add_row_csv(array("Diff in O/P Balance", "Cr " . convert_cur(-$diffop)));
				}
			}

			Accountlist::add_row_csv(array("Total - Assets", convert_cur($total)));

			$balancesheet = Accountlist::get_csv();
			$this->load->helper('csv');
			echo array_to_csv($balancesheet, "balancesheet.csv");
			return;
		}

		/********************** PROFIT AND LOSS ***********************/
		if ($statement == "profitandloss")
		{
			$this->load->library('accountlist');
			$this->load->model('Ledger_model');

			/***************** GROSS CALCULATION ******************/

			/* Gross P/L : Expenses */
			$gross_expense_total = 0;
			$this->db->from('groups')->where('parent_id', 4)->where('affects_gross', 1);
			$gross_expense_list_q = $this->db->get();
			foreach ($gross_expense_list_q->result() as $row)
			{
				$gross_expense = new Accountlist();
				$gross_expense->init($row->id);
				$gross_expense_total = float_ops($gross_expense_total, $gross_expense->total, '+');
				$gross_exp_array = $gross_expense->build_array();
				$gross_expense->to_csv($gross_exp_array);
			}
			Accountlist::add_blank_csv();

			/* Gross P/L : Incomes */
			$gross_income_total = 0;
			$this->db->from('groups')->where('parent_id', 3)->where('affects_gross', 1);
			$gross_income_list_q = $this->db->get();
			foreach ($gross_income_list_q->result() as $row)
			{
				$gross_income = new Accountlist();
				$gross_income->init($row->id);
				$gross_income_total = float_ops($gross_income_total, $gross_income->total, '+');
				$gross_inc_array = $gross_income->build_array();
				$gross_income->to_csv($gross_inc_array);
			}

			Accountlist::add_blank_csv();
			Accountlist::add_blank_csv();

			/* Converting to positive value since Cr */
			$gross_income_total = -$gross_income_total;

			/* Calculating Gross P/L */
			$grosspl = float_ops($gross_income_total, $gross_expense_total, '-');

			/* Showing Gross P/L : Expenses */
			$grosstotal = $gross_expense_total;
			Accountlist::add_row_csv(array("Total Gross Expenses", convert_cur($gross_expense_total)));
			if (float_ops($grosspl, 0, '>'))
			{
				$grosstotal = float_ops($grosstotal, $grosspl, '+');
				Accountlist::add_row_csv(array("Gross Profit C/O", convert_cur($grosspl)));
			}
			Accountlist::add_row_csv(array("Total Expenses - Gross", convert_cur($grosstotal)));

			/* Showing Gross P/L : Incomes  */
			$grosstotal = $gross_income_total;
			Accountlist::add_row_csv(array("Total Gross Incomes", convert_cur($gross_income_total)));

			if (float_ops($grosspl, 0, '>'))
			{

			} else if (float_ops($grosspl, 0, '<')) {
				$grosstotal = float_ops($grosstotal, -$grosspl, '+');
				Accountlist::add_row_csv(array("Gross Loss C/O", convert_cur(-$grosspl)));
			}
			Accountlist::add_row_csv(array("Total Incomes - Gross", convert_cur($grosstotal)));

			/************************* NET CALCULATIONS ***************************/

			Accountlist::add_blank_csv();
			Accountlist::add_blank_csv();

			/* Net P/L : Expenses */
			$net_expense_total = 0;
			$this->db->from('groups')->where('parent_id', 4)->where('affects_gross !=', 1);
			$net_expense_list_q = $this->db->get();
			foreach ($net_expense_list_q->result() as $row)
			{
				$net_expense = new Accountlist();
				$net_expense->init($row->id);
				$net_expense_total = float_ops($net_expense_total, $net_expense->total, '+');
				$net_exp_array = $net_expense->build_array();
				$net_expense->to_csv($net_exp_array);
			}
			Accountlist::add_blank_csv();

			/* Net P/L : Incomes */
			$net_income_total = 0;
			$this->db->from('groups')->where('parent_id', 3)->where('affects_gross !=', 1);
			$net_income_list_q = $this->db->get();
			foreach ($net_income_list_q->result() as $row)
			{
				$net_income = new Accountlist();
				$net_income->init($row->id);
				$net_income_total = float_ops($net_income_total, $net_income->total, '+');
				$net_inc_array = $net_income->build_array();
				$net_income->to_csv($net_inc_array);
			}

			Accountlist::add_blank_csv();
			Accountlist::add_blank_csv();

			/* Converting to positive value since Cr */
			$net_income_total = -$net_income_total;

			/* Calculating Net P/L */
			$netpl = float_ops(float_ops($net_income_total, $net_expense_total, '-'), $grosspl, '+');

			/* Showing Net P/L : Expenses */
			$nettotal = $net_expense_total;
			Accountlist::add_row_csv(array("Total Expenses", convert_cur($nettotal)));

			if (float_ops($grosspl, 0, '>'))
			{
			} else if (float_ops($grosspl, 0, '<')) {
				$nettotal = float_ops($nettotal, -$grosspl, '+');
				Accountlist::add_row_csv(array("Gross Loss B/F", convert_cur(-$grosspl)));
			}
			if (float_ops($netpl, 0, '>'))
			{
				$nettotal = float_ops($nettotal, $netpl, '+');
				Accountlist::add_row_csv(array("Net Profit", convert_cur($netpl)));
			}
			Accountlist::add_row_csv(array("Total - Net Expenses", convert_cur($nettotal)));

			/* Showing Net P/L : Incomes */
			$nettotal = $net_income_total;
			Accountlist::add_row_csv(array("Total Incomes", convert_cur($nettotal)));

			if ($grosspl > 0)
			{
				$nettotal = float_ops($nettotal, $grosspl, '+');
				Accountlist::add_row_csv(array("Gross Profit B/F", convert_cur($grosspl)));
			}

			if ($netpl > 0)
			{

			} else if ($netpl < 0) {
				$nettotal = float_ops($nettotal, -$netpl, '+');
				Accountlist::add_row_csv(array("Net Loss", convert_cur(-$netpl)));
			}
			Accountlist::add_row_csv(array("Total - Net Incomes", convert_cur($nettotal)));

			$balancesheet = Accountlist::get_csv();
			$this->load->helper('csv');
			echo array_to_csv($balancesheet, "profitandloss.csv");
			return;
		}
		return;
	}

	function printpreview($statement, $id = NULL)
	{
		if($statement == 'sales')
		{
			$this->load->model('database');
			
			$startdate = $this->uri->segment(4) ? $this->uri->segment (4): date('Y-m-d');
			$enddate = $this->uri->segment(5) ? $this->uri->segment (5): date('Y-m-d');
			$source = $this->uri->segment(6) ? $this->uri->segment (6) : '0';
			
			if($source == '0')
			{
				$src = array('AGT', 'EMP', 'PEN', 'SAL');
				$count = count($src) - 1;
				for($i=0; $i <= $count; $i++)
				{
					$data['datas'][$i] = $this->report_model->fetch_cv_sales($startdate, $enddate, $src[$i]);
				}
				
				$src = '';
			} else {
				switch($source)
				{
					case 'AGT':
						$i = 0;
						$val = 'ACCOM-AGENT';
						break;
					case 'EMP':
						$i = 1;
						$val = 'ACCOM-EMPLOYEE';
						break;
					case 'PEN':
						$i = 2;
						$val = 'CLIENT-GSIS/SSS';
						break;
					case 'SAL':
						$i = 3;
						$val = 'CLIENT-SALARY';
						break;
				}
				$data['datas'][$i] = $this->report_model->fetch_cv_sales($startdate, $enddate, $source);
			}
			$data['isreport'] = TRUE;
			$data['report'] = 'report/sales';
			$data['title'] = 'Sales Report' . ($source == '0' ? '' : ' - ' . $val);
			$data['reportname'] = date('m/d/Y', strtotime($startdate)) . ' - ' . date('m/d/Y', strtotime($enddate));
			$this->load->view('report/report_template', $data);
			return;
		}
		
		if($statement == 'disbursement')
		{
			$startdate = $this->uri->segment(4) ? $this->uri->segment(4) : date('Y-m-01');
			$enddate = $this->uri->segment(5) ? $this->uri->segment(5) : date('Y-m-d');
			$bankacct = $this->uri->segment(6) ? $this->uri->segment(6) : '0';
			$cvtype = $this->uri->segment(7) ? $this->uri->segment(7) : '0';
			
			switch($cvtype)
			{
				case 'D':
					$val = 'DISBURSEMENT';
					break;
				case 'F':
					$val = 'FUND TRANSFER';
					break;
				case 'R':
					$val = 'REFUND';
					break;
				case 'S':
					$val = 'SALES';
					break;
			}
			
			$data['cvtype'] = $cvtype;
			$data['bank'] = $this->db->query("
				SELECT code,
					value
				FROM parameter
				WHERE code='BANK'
				ORDER BY value ASC;
			")->result_array();
			
			foreach($data['bank'] as $b)
			{
				$bank = explode(';', $b['value']);
				$data['banks'][] = $bank[1];
			}
			
			if($bankacct == '0')
			{
				$i = 0;
				foreach($data['bank'] as $bank)
				{
					$bank = explode(';', $bank['value']);
					$data['datas'][$i] = $this->report_model->fetch_disbursement($startdate, $enddate, $bank[2], $cvtype);
					$i++;
				}
			} else {
				$i = 0;
				foreach($data['bank'] as $bank)
				{
					$bank = explode(';', $bank['value']);
					if($bankacct == $bank[2])
					{
						break;
					}
					$i++;
				}
				$data['datas'][$i] = $this->report_model->fetch_disbursement($startdate, $enddate, $bankacct, $cvtype);
			}
			
			$data['isreport'] = TRUE;
			$data['report'] = 'report/disbursement';
			$data['title'] = 'Disbursement Report' . ($cvtype == '0' ?
				''
			:
				' - ' . $val);
			$data['reportname'] = ($startdate == $enddate ?
				date('m/d/Y', strtotime($startdate))
			:
				date('m/d/Y', strtotime($startdate)) . ' - ' . date('m/d/Y', strtotime($enddate)));
			$this->load->view('report/report_template', $data);
			return;
		}
		
		if($statement == 'atmpb_release')
		{
			$from_date = $this->uri->segment(4) ? $this->uri->segment(4) : date('Y-m-01');
			$to_date = $this->uri->segment(5) ? $this->uri->segment(5) : date('Y-m-d');
			
			$data['datas'] = $this->report_model->fetch_atmpb_release($from_date, $to_date);
			
			$data['isreport'] = TRUE;
			$data['report'] = 'report/atmpb_release';
			$data['title'] = 'ATM/PB Release Summary Report';
			$data['reportname'] = ($from_date == $to_date ? date('m/d/Y', strtotime($from_date)) :
				date('m/d/Y', strtotime($from_date)) . ' - ' . date('m/d/Y', strtotime($to_date)));
			$data['preparedby'] = $this->session->userdata('user_name');
			$this->load->view('report/report_template', $data);
		}
		
		if($statement == 'newclient')
		{
			$this->load->model('database');
			
			$fromDate=$this->uri->segment(4)?$this->uri->segment(4):date('Y-m-01');
			$toDate=$this->uri->segment(5)?$this->uri->segment(5):date('Y-m-d');
			
			$data['datas'] = $this->database->get_ncr($fromDate, $toDate);
			
			$data['isreport'] = TRUE;
			$data['report'] = 'report/newclient';
			$data['title'] = 'New Client Summary Report';
			$data['reportname'] = ($fromDate==$toDate?date('m/d/Y',strtotime($fromDate)):
				date('m/d/Y',strtotime($fromDate)).' - '.date('m/d/Y',strtotime($toDate)));
			$data['preparedby'] = $this->session->userdata('user_name');
			$this->load->view('report/report_template', $data);
		}
		
		if($statement == 'returningclient')
		{
			$this->load->model('database');
			
			$fromDate=$this->uri->segment(4)?$this->uri->segment(4):date('Y-m-01');
			$toDate=$this->uri->segment(5)?$this->uri->segment(5):date('Y-m-d');
			
			$data['datas'] = $this->database->get_rtr($fromDate, $toDate);
			
			$data['isreport'] = TRUE;
			$data['report'] = 'report/returningclient';
			$data['title'] = 'Returning Client Summary Report';
			$data['reportname'] = ($fromDate==$toDate?date('m/d/Y',strtotime($fromDate)):
				date('m/d/Y',strtotime($fromDate)).' - '.date('m/d/Y',strtotime($toDate)));
			$data['preparedby'] = $this->session->userdata('user_name');
			$this->load->view('report/report_template', $data);
		}
		
		if($statement == 'rpcf')
		{
			$this->load->model('database');
			
			$rpcf_date = $this->uri->segment(4) ? $this->uri->segment(4) : date('Y-m-d');
			$group = $this->uri->segment(5) ? $this->uri->segment(5) : '0';
			
			$data['datas'] = $this->report_model->fetch_rpcf($rpcf_date, $group);
			switch($group)
			{
				case '0':
					$group_report = '';
					break;
				
				case '1':
					$group_report = ' - NEW';
					break;
					
				case '2':
					$group_report = ' - OLD';
					break;
			}
			
			$data['isreport'] = TRUE;
			$data['report'] = 'report/rpcf';
			$data['title'] = 'RPCF Summary Report';
			$data['reportname'] = date('m/d/Y', strtotime($rpcf_date)) . $group_report;
			$data['preparedby'] = $this->session->userdata('user_name');
			$this->load->view('report/report_template', $data);
		}
		
		if($statement == 'commission')
		{
			$from_date = $this->uri->segment(4) ? $this->uri->segment(4) : date('Y-m-d');
			$to_date = $this->uri->segment(5) ? $this->uri->segment(5) : date('Y-m-d');
			$agent_type = $this->uri->segment(6) ? $this->uri->segment(6) : '0';
			
			$data['datas'] = $this->report_model->fetch_commission($from_date, $to_date, $agent_type);
			
			$agent = 'Agent';
			if($agent_type == '1')
			{
				$agent = 'Sub-Agent';
			}
			
			$data['isreport'] = TRUE;
			$data['report'] = 'report/commission';
			$data['title'] = 'Commission Summary Report';
			$data['reportname'] = $agent . ': ' . date('m/d/Y', strtotime($from_date)) . ' - ' . date('m/d/Y', strtotime($to_date));
			$data['preparedby'] = $this->session->userdata('user_name');
			$this->load->view('report/report_template', $data);
			$this->load->view('report/report_template', $data);
		}
		
		if($statement == 'comm_month_week')
		{
			$ai_refno = $this->uri->segment(4);
			$added_date = $this->uri->segment(5);
			
			$data['datas'] = $this->report_model->fetch_agent_comm($ai_refno, $added_date);
			
			$agent = $this->master_model->fetch_agent_details('CONCAT(AI_LName, \', \', AI_FName) as ai_name', $ai_refno);
			
			$data['isreport'] = TRUE;
			$data['report'] = 'report/comm_month_week';
			$data['title'] = 'Weekly/Monthly Commission Report';
			$data['reportname'] = $agent->ai_name . ': ' . date('m/d/Y', strtotime($added_date));
			$data['preparedby'] = $this->session->userdata('user_name');
			
			$this->load->view('report/report_template', $data);
		}
		
		if($statement == 'prcollection')
		{
			$this->load->model('database');
			
			$today = $this->uri->segment(4) ? $this->uri->segment(4) : date('Y-m-d');
			$source = $this->uri->segment(5) ? $this->uri->segment(5) : '0';
			$user = $this->uri->segment(6) ? $this->uri->segment(6) : 'a';
			$prno = $this->uri->segment(7) ? $this->uri->segment(7) : '0';
			$debit_type = $this->uri->segment(8) ? $this->uri->segment(8) : '0';
			
			//$prno = ' - '.$data['datas']['0']['orprno'];
			$data['datas'] = $this->database->get_prs($today, $source, $user, $prno, $debit_type);
			if($prno=='0'):
				$pr = '';
				if($data['datas']):
					foreach($data['datas'] as $dt):
						if(strpos($pr, $dt['orprno'])===FALSE):
							if($pr==''):
								$pr .= $dt['orprno'];
							else:
								$pr .= '|'.$dt['orprno'];
							endif;
						endif;
					endforeach;
				endif;
			else:
				$pr = $prno;
			endif;
			
			if($debit_type == '0')
			{
				$type = 'ALL';
			} elseif($debit_type == '1') {
				$type = 'AUTO-DEBIT';
			} elseif($debit_type == '2') {
				$type = 'P.O.S.';
			};
			
			$data['isreport'] = TRUE;
			$data['report'] = "report/prcollection";
			$data['title'] = "Collection Report - " . date('m/d/Y', strtotime($today));
			$data['reportname'] = 'Provisional Receipt - ' . $pr . ($source == '0' ? '' : ' - ' . $source) . ' ' . $type;
			$data['preparedby'] = $this->session->userdata('user_name');
			$this->load->view('report/report_template', $data);
			return;
		}
		
		if($statement == 'orcollection')
		{
			$today = $this->uri->segment(4) ? $this->uri->segment(4) : date('Y-m-d');
			$source = $this->uri->segment(5) ? $this->uri->segment(5) : '0';
			$debit_type = $this->uri->segment(6) ? $this->uri->segment(6) : '0';
			$user = $this->uri->segment(7) ? $this->uri->segment(7) : 'a';
			
			if($debit_type == '0')
			{
				$type = 'ALL';
			} elseif($debit_type == '1') {
				$type = 'AUTO-DEBIT';
			} elseif($debit_type == '2') {
				$type = 'P.O.S.';
			};
			
			$data['datas'] = $this->database->get_ors($today, $source, $user, $debit_type );
			$data['isreport'] = TRUE;
			$data['report'] = 'report/orcollection';
			$data['title'] = 'Collection Report - ' . date('m/d/Y', strtotime($today));
			$data['reportname'] = 'Official Receipt' . ($source == '0' ? '' : ' - ' . $source) . ' ' . $type;
			$data['preparedby'] = $this->session->userdata('user_name');
			$this->load->view('report/report_template', $data);
			return;
		}
		
		if($statement == 'otherscollection')
		{
			$this->load->model('database');
			$this->load->model('rsm');
			
			$today=$this->uri->segment(4)?$this->uri->segment(4):date('Y-m-d');
			$type=$this->uri->segment(5)?$this->uri->segment(5):'0';
			
			switch($type):
				case 'BC':
					$ocType = 'Bank Charge';
					break;
				case 'RC':
					$ocType = 'Remittance Charge';
					break;
				case 'RD':
					$ocType = 'Redeposit';
					break;
			endswitch;
			
			$data['datas'] = $this->database->get_ocr($today, $type);
			$data['isreport'] = TRUE;
			$data['report'] = 'report/otherscollection';
			$data['title'] = 'Collection Report - '.date('m/d/Y', strtotime($today));
			$data['reportname'] = 'Official Receipt'.($type=='0'?'':' - '.$ocType);
			$data['preparedby'] = $this->session->userdata('user_name');
			$this->load->view('report/report_template', $data);
			return;
		}
		
		if($statement == 'autodebit_coll')
		{
			$from_date = $this->uri->segment(4) ? $this->uri->segment(4) : date('Y-m-01');
			$to_date = $this->uri->segment(5) ? $this->uri->segment(5) : date('Y-m-t');
			
			$data['datas'] = $this->report_model->fetch_autodebit_coll($from_date, $to_date);
			$data['isreport'] = TRUE;
			$data['report'] = 'report/autodebit_coll';
			$data['title'] = 'Collection Report';
			$data['reportname'] = 'Auto-Debit: ' . date('m/d/Y', strtotime($from_date)) . ' - ' . date('m/d/Y', strtotime($to_date));
			$data['preparedby'] = $this->session->userdata('user_name');
			$this->load->view('report/report_template', $data);
			return;
		}

		/********************** TRIAL BALANCE *************************/
		if ($statement == "trialbalance")
		{
			$this->load->library('accountlist');
			$data['report'] = "report/trialbalance";
			$data['title'] = "Trial Balance";
			$this->load->view('report/report_template', $data);
			return;
		}

		if ($statement == "balancesheet")
		{
			$data['report'] = "report/balancesheet";
			$data['title'] = "Balance Sheet";
			$data['left_width'] = "";
			$data['right_width'] = "";
			$this->load->view('report/report_template', $data);
			return;
		}

		if ($statement == "profitandloss")
		{
			$data['report'] = "report/profitandloss";
			$data['title'] = "Profit and Loss Statement";
			$data['left_width'] = "";
			$data['right_width'] = "";
			$this->load->view('report/report_template', $data);
			return;
		}
		
		if ($statement == "ledgerst")
		{
			$this->load->helper('text');

			/* Pagination setup */
			$this->load->library('pagination');
			$data['ledger_id'] = $this->uri->segment(4);
			/* Checking for valid ledger id */
			if ($data['ledger_id'] < 1)
			{
				$this->messages->add('Invalid Ledger account.', 'error');
				redirect('report/ledgerst');
				return;
			}
			$this->db->from('ledgers')->where('id', $data['ledger_id'])->limit(1);
			if ($this->db->get()->num_rows() < 1)
			{
				$this->messages->add('Invalid Ledger account.', 'error');
				redirect('report/ledgerst');
				return;
			}
			$data['report'] = "report/ledgerst";
			$data['title'] = "Ledger Statement for '" . $this->Ledger_model->get_name($data['ledger_id']) . "'";
			$data['print_preview'] = TRUE;
			$this->load->view('report/report_template', $data);
			return;
		}

		if ($statement == "reconciliation")
		{
			$this->load->helper('text');

			$data['show_all'] = FALSE;
			$data['ledger_id'] = $this->uri->segment(4);

			/* Check if path is 'all' or 'pending' */
			if ($this->uri->segment(5) == 'all')
			{
				$data['reconciliation_type'] = 'all';
				$data['show_all'] = TRUE;
			} else if ($this->uri->segment(5) == 'pending') {
				$data['reconciliation_type'] = 'pending';
				$data['show_all'] = FALSE;
			} else {
				$this->messages->add('Invalid path.', 'error');
				redirect('report/reconciliation/pending');
				return;
			}

			/* Checking for valid ledger id and reconciliation status */
			if ($data['ledger_id'] > 0)
			{
				$this->db->from('ledgers')->where('id', $data['ledger_id'])->where('reconciliation', 1)->limit(1);
				if ($this->db->get()->num_rows() < 1)
				{
					$this->messages->add('Invalid Ledger account or Reconciliation is not enabled for the Ledger account.', 'error');
					redirect('report/reconciliation/' . $reconciliation_type);
					return;
				}
			} else if ($data['ledger_id'] < 0) {
				$this->messages->add('Invalid Ledger account.', 'error');
				redirect('report/reconciliation/' . $reconciliation_type);
				return;
			}

			$data['report'] = "report/reconciliation";
			$data['title'] = "Reconciliation Statement for '" . $this->Ledger_model->get_name($data['ledger_id']) . "'";
			$data['print_preview'] = TRUE;
			$this->load->view('report/report_template', $data);
			return;
		}
		return;
	}
}

/* End of file report.php */
/* Location: ./system/application/controllers/report.php */
