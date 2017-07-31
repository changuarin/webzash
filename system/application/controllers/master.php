<?php

class Master extends Controller
{
	function Master()
	{
		parent::Controller();
		
    date_default_timezone_set('Asia/Manila');
		$this->load->helper('code');
		$this->load->library('form_validation');
		$this->load->model('database');
		$this->load->model('master_model');
		$this->load->model('sales_model');
		return;
	}

	// rejected ft request
	function s_()
	{
		$this->load->helper('code');
		$t['ok'] = TRUE;

		$datenow = date('Y-m-d H:i:s');

		$this->db->set('status', 'rejected');
		$this->db->set('note', $this->input->post('b') );
		$this->db->where('status', 'forApproval');
		$this->db->where('ftid', $this->input->post('a') );
		$this->db->update('ft_request');

		if( $this->db->_error_message() )
		{
			$t['error'] = 'alert(\''.$this->db->_error_message().'\');';
			$t['ok'] = FALSE;
		}else $t['res'] = trims("$(o.find('td')[5]).html('Note:<br>{$this->input->post('b')}');
			var t_=$(o.find('td')[3]).html();
			$(o.find('td')[3]).html(t_.replace('forApproval','<mes>rejected</mes>'));
		");
		
		die( json_encode( $t ) );
	}

	// approved ft request
	function t_()
	{
		//$this->output->enable_profiler(TRUE);

		$this->load->helper('code');
		$this->load->model('rsm');
		$t['ok'] = TRUE;
		$error='';

		$datenow = date('Y-m-d H:i:s');

		$this->db->trans_start();

		$this->db->set('status', 'approved');
		$this->db->set('approvedby', $this->session->userdata('user_name') );
		$this->db->set('approvedate', $datenow );
		$this->db->where('status', 'forApproval');
		$this->db->where('ftid', $this->input->post('a') );
		$this->db->update('ft_request') or
		$error = 'line: 60 '.str_replace("'", "\'", $this->db->_error_message());

		$this->db->select('code1, code2, amount, remarks, transdate');
		$this->db->where('ftid', $this->input->post('a'));
		$q = $this->db->get('ft_request') or
		$error = 'line: 65 '.str_replace("'", "\'", $this->db->_error_message());

		$q = $q->row();
		$code1 = explode('|', $q->code1);
		$code2 = explode('|', $q->code2);
		$amount = $q->amount;
		$remarks = $q->remarks;
		$transdate = $q->transdate;
		unset($q);
		
		$this->db->select('branchcode, branch');
		$this->db->where('code', $code1[1]);
		$q = $this->db->get('nhgt_master.bankaccts') or
		$error = 'line: 77 '.str_replace("'", "\'", $this->db->_error_message());

		$q = $q->row();
		$code1[2] = $q->branchcode;
		$code1[3] = $q->branch;
		unset($q);

		$this->db->select('branchcode, branch');
		$this->db->where('code', $code2[1]);
		$q = $this->db->get('nhgt_master.bankaccts') or
		$error = 'line: 87 '.str_replace("'", "\'", $this->db->_error_message());

		$q = $q->row();
		$code2[2] = $q->branchcode;
		$code2[3] = $q->branch;
		//print_r($code1);echo'<br>';print_r($code2);die();
		unset($q);

		if( $this->input->post('b')=='Online' ):

			$lastentryid = $this->rsm->get_last_entryid();

			$entry = array(
				'entry_id'  => $lastentryid,
				'ledger_id' => $this->rsm->chartaccount_id( $code2[2] ),
				'dc'        => 'D',
				'amount'    => $amount
			);
			$this->db->insert("entry_items", $entry) or
			$error = 'line: 106 '.str_replace("'", "\'", $this->db->_error_message());

			$entry = array(
				'entry_id'  => $lastentryid,
				'ledger_id' => $this->rsm->chartaccount_id( $code1[1] ),
				'dc'        => 'C',
				'amount'    => $amount
			);
			$this->db->insert("entry_items", $entry) or
			$error = 'line: 115 '.str_replace("'", "\'", $this->db->_error_message());
			
			$datas = array(
				'id'		 => $lastentryid,
				'entry_type' => 2,					// General journal
				'number'     => $this->rsm->get_entry_number( 2 ),
				'date'       => $transdate,
				'dr_total'   => $amount,
				'cr_total'   => $amount,
				'narration'  => $remarks
			);

			$this->db->insert("entries", $datas) or
			$error = 'line: 128 '.str_replace("'", "\'", $this->db->_error_message());

			///////////////////////////

			$lastentryid = $this->rsm->get_last_entryid( 'nhgt_'.$code2[3] );

			$entry = array(
				'entry_id'  => $lastentryid,
				'ledger_id' => $this->rsm->chartaccount_id( $code2[1], 'nhgt_'.$code2[3] ),
				'dc'        => 'D',
				'amount'    => $amount
			);
			$this->db->insert('nhgt_'.$code2[3].".entry_items", $entry) or
			$error = 'line: 141 '.str_replace("'", "\'", $this->db->_error_message());

			$entry = array(
				'entry_id'  => $lastentryid,
				'ledger_id' => $this->rsm->chartaccount_id( $code1[2], 'nhgt_'.$code2[3] ),
				'dc'        => 'C',
				'amount'    => $amount
			);
			$this->db->insert('nhgt_'.$code2[3].".entry_items", $entry) or
			$error = 'line: 150 '.str_replace("'", "\'", $this->db->_error_message());
			
			$datas = array(
				'id'		 => $lastentryid,
				'entry_type' => 2,					// General journal
				'number'     => $this->rsm->get_entry_number( 2, 'nhgt_'.$code2[3] ),
				'date'       => $transdate,
				'dr_total'   => $amount,
				'cr_total'   => $amount,
				'narration'  => $remarks
			);

			$this->db->insert("nhgt_".$code2[3].".entries", $datas) or
			$error = 'line: 163 '.str_replace("'", "\'", $this->db->_error_message());

		elseif( $this->input->post('b')=='CvCheckPrint' ):

			$this->db->select('code2, amount, remarks');
			$this->db->where('ftid', $this->input->post('a'));
			$ftq = $this->db->get('ft_request') or
			$error = 'line: 163 '.str_replace("'", "\'", $this->db->_error_message());

			if($ftq->num_rows()):

				$datas = $ftq->result_array();
				foreach($datas as $data):

					$code2 = explode('|', $data['code2']);

					$this->db->set( 'trans_type', 'FT' );
					$this->db->set( 'trans_date', $datenow );
					$this->db->set( 'account1', $code1[0] );
					$this->db->set( 'account2', $code2[0] );
					$this->db->set( 'reference', $this->input->post('a') );
					$this->db->set( 'amount', $data['amount'] );
					$this->db->set( 'remarks', $data['remarks'] );
					$this->db->set( 'status', 'ForPrinting' );
					$this->db->set( 'entries', '-' );
					$this->db->insert( 'tbl_disbursement' ) or 
					$error = 'line: 234 '.str_replace("'", "\'", $this->db->_error_message());

				endforeach;

			endif;

		endif;

		if( $error )
		{
			$t['error'] = 'alert(\''.$error.'\');';
			$t['ok'] = FALSE;
		}else $t['res'] = trims("$(o.find('td')[5]).html('".
			"ApprovedBy: {$this->session->userdata('user_name')}/$datenow<br>".
			($this->input->post('b')=='CvCheckPrint'?
			"For CV/Check printing.":'Online Transaction Completed.')."');");
		
		$this->db->trans_complete();

		die( json_encode( $t ) );

	}

	// list of fund transfer request for approval/approved/rejected
	function u_()
	{
		$this->db->order_by('transdate', 'DESC');
		$this->db->where('status', 'forApproval');
		$this->db->or_where('status', 'approved');
		$this->db->or_where('status', 'rejected');
		$param['datas'] = $this->db->get('ft_request')->result_array();

		$this->load->view('master/list/ft4approval', $param);
		return;
	}

	// POST FT Request
	function v_()
	{
		$this->load->helper('code');

		$t['ok'] = TRUE;

		$this->db->trans_start();

		$this->db->set('status', 'forApproval');
		$this->db->where('ftid', $this->input->post('a'));
		$this->db->update('ft_request');

		if( $this->db->_error_message() ):

			$t['ok'] = FALSE;
			$t['error'] = 'alert(\''.$this->db->_error_message().'\');';
			die( json_encode($t) );

		endif;

		$this->db->select('remarks');
		$this->db->where('ftid', $this->input->post('a') );
		$qs = $this->db->get('ft_request')->result_array();
		$remarks='';
		foreach ($qs as $v):
			$remarks .= $v['remarks'].', ';
		endforeach;
		$remarks = substr($remarks, 0, strlen($remarks)-2);
		
		$this->db->set('status', '1');
		$this->db->set('type', 'ft');
		$this->db->set('mto', $this->session->userdata('fta'));
		$this->db->set('mfrom', $this->session->userdata('user_name'));
		$this->db->set('message', trims("$remarks"));
		$this->db->set('mdate', date('Y-m-d H:i:s'));
		$this->db->insert('nhgt_master.notification');

		$this->db->trans_complete();

		switch( substr($this->input->post('b'), 0, 5) )
		{
			case'draft':
				$t['res'] = str_replace('draft', 'forApproval', $this->input->post('b'));
			break;
		}
		die( json_encode($t) );
	}

	// cancel ft request
	function w_()
	{
		$t['ok'] = TRUE;
		$this->db->where('ftid', $this->input->post('a'));
		$this->db->delete('ft_request');
		if( $this->db->_error_message() )
		{
			$t['ok'] = FALSE;
			$t['error'] = $this->db->_error_message();
		}
		die( json_encode($t) );
	}

	// list of fund transfer request
	function x_()
	{
		$this->db->order_by('transdate', 'DESC');
		$param['datas'] = $this->db->get('ft_request')->result_array();

		$this->load->view('master/list/ftrequest', $param);
		return;
	}

	function y_()
	{
		$this->load->helper('code');

		if( isset($_POST['multidata']) ):

			$this->db->trans_start();

			$ftid = time();
			$date = date('Y-m-d H:i:s');

			$datas = json_decode( $this->input->post('multidata') );

			foreach($datas as $data)
			{
				$this->db->set('ftid', $ftid );
				$this->db->set('transdate', $date );
				$this->db->set('transtype', $this->input->post('e') );
				$this->db->set('amount', n( $data->amount ));
				$this->db->set('code1', $this->input->post('b') );
				$this->db->set('code2', $data->bank );
				$this->db->set('remarks', $data->remarks );
				$this->db->set('requestby', $this->session->userdata('user_name') );
				$this->db->set('status', 'draft' );
				$this->db->insert('ft_request') or die( $this->db->_error_message() );
			}

			$this->db->trans_complete();

		elseif( isset($_POST['a']) ):

			$this->db->set('ftid', time() );
			$this->db->set('transdate', date('Y-m-d H:i:s') );
			$this->db->set('transtype', $this->input->post('e') );
			$this->db->set('amount', n( $this->input->post('a') ));
			$this->db->set('code1', $this->input->post('b') );
			$this->db->set('code2', $this->input->post('c') );
			$this->db->set('remarks', $this->input->post('d') );
			$this->db->set('requestby', $this->session->userdata('user_name') );
			$this->db->set('status', 'draft' );
			$this->db->insert('ft_request') or die( $this->db->_error_message() );

		endif;

		die("<script>document.location='fndtrnsfr1';</script>");

	}

	//list of accounts
	function z_()
	{
		switch( $this->input->post('a') )
		{
			case'ba':

				$t = explode('_', $this->db->database);
				$branch = $t[1];

				if( $this->input->post('b') ):

					$this->db->select('code, bankaccount, bankname');
					$this->db->where('status', 'active');
					$this->db->where('remarks', 'ft');
					$this->db->order_by('bankname');
					$this->db->order_by('branch');
					$q = $this->db->get('nhgt_master.bankaccts')->result_array();
					
					echo json_encode( $q );

				else:

					$this->db->select('code, bankaccount, bankname');
					$this->db->where('branch', $branch);
					$this->db->where('status', 'active');
					$this->db->where('remarks', 'ft');
					$this->db->order_by('bankname');
					$this->db->order_by('branch');
					$q = $this->db->get('nhgt_master.bankaccts')->result_array();
					
					echo json_encode( $q );

				endif;

			break;
		}
	}

	//2015-03-31
	function fndtrnsfr1()
	{
		$this->load->helper('code');

		$p['script'] = ("
			$('#frm').submit(function()
			{
				return false;
			});

			var z=[
				'input[name=a]',
				'select[name=b]',
				'select[name=c]',
				'textarea[name=d]',
				'#sbmt',
				'select[name=e]'
			],x=[];

			for(y in z)
			{
				x[y]=$(z[y]);
				x[y].attr('disabled', true);
			}
			
			x[1].html('');
			$.post('z_',{a:'ba',b:0},function(r_)
			{
				x[1].append('<option value=\"\">Select a Bank Account</option>');
				for(b in r_)
				{
					c=r_[b];
					d=c.bankaccount;
					d=d.replace(/-/g,'');
					x[1].append('<option value=\"'+d+'|'+c.code+'\">'+d+' / '+c.bankname+'</option>');
				}
				x[1].attr('disabled', false);
			},
			'json');

			x[1].change(function()
			{
				if($(this).val()=='')
					x[2].find('option').css('display', 'none');
				else
				{
					var t_=$(this).val();
					x[2].val('').find('option').each(function()
					{
						if($(this).val()==t_)
							$(this).css('display', 'none');
						else $(this).css('display', '');
					});
				}
			});
			

			x[2].html('');
			$.post('z_',{a:'ba',b:1},function(r_)
			{
				x[2].append('<option value=\"\">Select a Bank Account</option>');
				for(b in r_)
				{
					c=r_[b];
					d=c.bankaccount;
					d=d.replace(/-/g,'');
					x[2].append('<option value=\"'+d+'|'+c.code+'\">'+d+' / '+c.bankname+'</option>');
				}
				x[2].attr('disabled', false);
				x[0].attr('disabled', false);
				x[3].attr('disabled', false);
				x[4].attr('disabled', false);
				x[5].attr('disabled', false).focus();
				x[1].change();
			},
			'json');
			
			x[3].blur(function()
			{
				$(this).val($(this).val().toUpperCase());
			});

			function foc(a_)
			{
				a_.blur(function()
				{
					a_.css('border', '1px solid gray')
					.css('background-color', 'transparent');
				});
				a_.css('border', '3px dashed red')
				.css('background-color', 'rgb(255,255,221)')
				.focus();				
			}

			x[4].click(function()
			{
				if(x[5].val()=='')
				{
					foc(x[5]);
				}else if(x[0].val()=='')
				{
					foc(x[0]);
				}else if(x[1].val()=='')
				{
					foc(x[1]);
				}else if(x[2].val()=='')
				{
					foc(x[2]);
				}else if(x[3].val()=='')
				{
					foc(x[3]);
				}
				else
				{
					for(i in x)
					{
						x[i].attr('disabled', true);
					}

					if($(this).val()=='Submit')
					{
						if(confirm('Are you sure you want to Submit this request?'))
						{
							for(i in x)
							{
								x[i].attr('disabled', false);
							}
							$('#frm').unbind('submit');
							$('#frm').attr('action','y_').submit();
						}
						else
						{
							for(i in x)
							{
								x[i].attr('disabled', false);
							}
						}
					}else
					{
						var a=$('.ftmul').find('tbody.data');
						var b=a.find('tr').length+1;
						a.append('<tr class=\'mldat\'>".
							"<td class=\'bl bt\'>'+b+'</td>".
							"<td class=\'bt\'>'+x[2].val()+'</td>".
							"<td class=\'bt padr\'align=\'right\'>'+x[0].val()+'</td>".
							"<td class=\'bt\'>'+x[3].val()+'</td>".
							"<td class=\'bt br\'width=\'1%\'>".
							"<input class=\'remb\'type=\'button\'value=\'Remove\'/>".
							"</td>".
						"</tr>');

						$('.remb').unbind('click').click(function()
						{
							$(this).parent().parent().remove();
							if(a.find('tr').length>0)
							{
								$('#submul').attr('disabled',false);
							}else
							{
								$('#submul').attr('disabled',true);
								x[1].attr('disabled', false);
							}

							var d=0;
							a.find('tr').each(function()
							{
								var c=$(this).find('td');
								d+=parseFloat($(c[2]).html());
							});
							$('td.total').html(d);
						});
						
						for(i in x)
						{
							x[i].attr('disabled', false);
						}
						x[1].attr('disabled', true);

						x[3].val('');
						x[2].val('');
						x[0].val('').focus();

						$('#submul').attr('disabled', false);

						var d=0;
						a.find('tr').each(function()
						{
							var c=$(this).find('td');
							d+=parseFloat($(c[2]).html());
						});
						$('td.total').html(d);

					}
				}
			});

			x[5].change(function()
			{
				if($(this).val()=='Online')
				{
					if($('.ftt').css('height')!='400px')
					{
						$('.ftt').animate({
							height:400
						},700,function()
						{
							$('.ftmul').hide();
							$('#sbmt').val('Submit');
							$('.rtd').addClass('td1');
						});
						
					}
				}else
				{
					if($('.ftt').css('height')!='230px')
					$('.ftt').animate({
						height: '230px'
					},700,function()
					{
						$('.ftmul').show();
						$('#sbmt').val('Add');
						$('.rtd').removeClass('td1');
					});
					
					$('#submul').click(function()
					{
						if(confirm('Are you sure you want to Submit this request?'))
						{
							for(i in x)
							{
								x[i].attr('disabled', false);
							}
							$('#frm').append('<input type=\'hidden\'name=\'multidata\'id=\'md\'/>');
							var arr=[];
							$('.mldat').each(function()
							{
								var ta=$(this).find('td'),tar={};
								tar.bank=$(ta[1]).html();
								tar.amount=$(ta[2]).html();
								tar.remarks=$(ta[3]).html();
								arr.push(tar);
							});
							
							$('#md').val(JSON.stringify(arr));
							$('#frm').unbind('submit');
							$('#frm').attr('action','y_').submit();
						}
						else
						{
							for(i in x)
							{
								x[i].attr('disabled', false);
							}
						}
					});
				}
			});

			$('iframe').attr('src','x_');

		");

		$this->template->load('template', 'master/ftrequest', $p);
		return;
	}

	//2015-03-31
	function fndtrnsfr2()
	{

		$this->load->helper('code');

		$p['script'] = trims("

			$('iframe').attr('src','u_');

		");

		$this->template->load('template', 'master/ftapproval', $p);
		return;
	}
	
	/**
	 * Updated master functions
	 */
	
	public function verification()
	{
		$this->template->load('template', 'master/verification');
	}
	
	public function client_list2()
	{
		$data['ci_type'] = $this->input->post('citype');
		$data['ci_status'] = $this->input->post('cistatus');
		$data['name'] = $this->input->post('name');
		
		$data['clients'] = $this->master_model->get_clients2();
		
		$this->load->view('master/list/client2', $data);
	}
	
	public function client_form2()
	{
		$data['branches'] = $this->master_model->get_branches();
		$data['client_types'] = $this->master_model->get_client_types();
		$data['pmt_sources'] = $this->master_model->get_payment_sources();
		$data['pmt_types'] = $this->master_model->get_payment_types();
		
		$this->load->view('master/form/client2', $data);
	}
	
	public function show_client2()
	{
		$ci_acctno = $this->input->post('ciacctno');
		$database = $this->input->post('database');
		
		$client = $this->master_model->get_client($ci_acctno, $database);
		$ci_bdate = date('Y-m-d', strtotime($client->CI_Bdate));
		
		$client_pension = $this->master_model->get_client_pension($ci_acctno, $database);
		$cp_dateofdeath = date('Y-m-d', strtotime($client_pension->CP_DateOfDeath));
		
		header('Content-type: text/javascript');
		
		die(trims("
				var form = $('#clientForm2', parent.document).contents();
				
				form.find('#ciAcctno').val('{$client->CI_AcctNo}');
				form.find('#ciSource').val('{$client->CI_Source}');
				form.find('#ciSssno').val('{$client->CI_SSSNo}');
				form.find('#ciStatus').val('{$client->CI_Status}');
				form.find('#perImg').prop('src', 'data:image/jpeg; base64, {$client->CI_Picture}');
				form.find('#ciFname').val('{$client->CI_FName}');
				form.find('#ciGrp').val('{$client->CI_Grp}');
				form.find('#ciMname').val('{$client->CI_MName}');
				form.find('#ciType').val('{$client->CI_Type}');
				form.find('#ciLname').val('{$client->CI_LName}');
				form.find('#ciBranchcode').val('{$client->CI_BranchCode}');
				form.find('#ciBdate').val('{$ci_bdate}');
				form.find('#ciSex').val('{$client->CI_Sex}');
				form.find('#ciCivilstatus').val('{$client->CI_CivilStatus}');
				form.find('#ciTelno').val('{$client->CI_TelNo}');
				form.find('#ciMobileno').val('{$client->CI_MobileNo}');
				form.find('#ciAdd1').val('{$client->CI_Add1}');
				form.find('#ciAdd2').val('{$client->CI_Add2}');
				form.find('#cpItf').val('{$client_pension->CP_ITF}');
				form.find('#cpPensiontype').val('{$client_pension->CP_PensionType}');
				form.find('#cpBankbranch').val('{$client_pension->CP_BankBranch}');
				form.find('#cpBankacctno').val('{$client_pension->CP_BankAcctNo}');
				form.find('#cpAmount').val('{$client_pension->CP_Amount}');
				form.find('#cpWithdrawalday').val('{$client_pension->CP_WithdrawalDay}');
				form.find('#cpPtype').val('{$client_pension->CP_PType}');
				form.find('#cpCauseofdeath').val('{$client_pension->CP_CauseOfDeath}');
				form.find('#cpDateofdeath').val('{$cp_dateofdeath}');
				form.find('#cpDisability').val('{$client_pension->CP_Disability}');
				form.find('#dependentList').attr('src', 'dependent_list2/{$database}|{$ci_acctno}');
				form.find('#database').val('{$database}').attr('disabled', false);
				form.find('#loanList').attr('src', 'loan_list2/{$database}|{$ci_acctno}');
			"));
	}
	
	public function dependent_list2($data)
	{
		$data = explode('|', $data);
		$ci_acctno = $data[1];
		$database = $data[0];
		
		$data['dependents'] = $this->master_model->get_dependents($ci_acctno, $database);
		
		$this->load->view('master/list/dependent2', $data);
	}
	
	public function loan_list2($data)
	{
		$data = explode('|', $data);
		$ci_acctno = $data[1];
		$database = $data[0];
		
		$data['loans'] = $this->master_model->get_loans($ci_acctno, $database);
		
		$this->load->view('master/list/loan2', $data);
	}
	
	
	public function agent()
	{
		$this->template->load('template', 'master/agent');
	}
	
	public function agent_list()
	{
		$data['uri_seg'] = $this->uri->segment(3) ? $this->uri->segment(3) : '';
		
		$data['name'] = $this->input->post('name');
		
		$data['agents'] = $this->master_model->get_agents();
		
		$this->load->view('master/list/agent', $data);
	}
	
	public function agent_form()
	{
		$this->form_validation->set_rules('agentprocess', 'Agent Process', 'required|trim|xss_clean');
		
		if($this->input->post('agentprocess') == 'updateagent' OR $this->input->post('agentprocess') == 'deleteagent')
		{
			$this->form_validation->set_rules('airefno', 'Old Ref. No.', 'required|trim|xss_clean|min_length[7]|max_length[9]');
		}
		
			if($this->input->post('agentprocess') == 'updateagent')
		{
			$this->form_validation->set_rules('airefno2', 'Ref. No.', 'required|trim|xss_clean|min_length[7]|max_length[9]');
		}
		
		if($this->input->post('agentprocess') == 'addagent' OR $this->input->post('agentprocess') == 'updateagent')
		{
			$this->form_validation->set_rules('aifname', 'First Name', 'required|trim|xss_clean|min_length[2]');
			$this->form_validation->set_rules('aimname', 'Middle Name', 'required|trim|xss_clean|min_length[2]');
			$this->form_validation->set_rules('ailname', 'Last Name', 'required|trim|xss_clean|min_length[2]');
			$this->form_validation->set_rules('aibranchcode', 'Branch', 'required|trim|xss_clean');
			$this->form_validation->set_rules('aibdate', 'Birth Date', 'required|trim|xss_clean');
			$this->form_validation->set_rules('aisex', 'Gender', 'required|trim|xss_clean');
			$this->form_validation->set_rules('aicivilstatus', 'Civil Status', 'required|trim|xss_clean');
			$this->form_validation->set_rules('aitelno', 'Tel. No.', 'trim|xss_clean');
			$this->form_validation->set_rules('aimobileno', 'Mobile No.', 'trim|xss_clean');
			$this->form_validation->set_rules('aiadd1', 'Present Address', 'required|trim|xss_clean');
			$this->form_validation->set_rules('aiadd2', 'Permanent Address', 'trim|xss_clean');
		}
		
		if($this->form_validation->run() == FALSE)
		{
			
			$data['branches'] = $this->master_model->get_branches();
			
			$this->load->view('master/form/agent', $data);
		} 
		else
		{
			if($this->input->post('agentprocess') == 'addagent')
			{
				if($this->master_model->add_agent())
				{
					$this->session->set_flashdata('added', 'Agent has been added');
				}
			}
			elseif($this->input->post('agentprocess') == 'updateagent')
			{
				if($this->master_model->update_agent())
				{
					$this->session->set_flashdata('updated', 'Agent has been updated');
				}
			}
			elseif($this->input->post('agentprocess') == 'deleteagent')
			{
				if($this->master_model->delete_agent())
				{
					$this->session->set_flashdata('deleted', 'Agent has been deleted');
				}
			}
			
			redirect('master/agent_form');
		}
	}
	
	public function show_agent()
	{
		$ai_refno = $this->input->post('airefno');
		
		$agent = $this->master_model->get_agent($ai_refno);
		
		$ai_bdate = date('Y-m-d', strtotime($agent->AI_Bdate));
		
		header('Content-type: text/javascript');
		
		die(trims("
				var agentForm = $('#agentForm', parent.document).contents();
				var menu = agentForm.find('.menu').contents();
				var main = agentForm.find('.main').contents();
				
				menu.find('input').hide();
				menu.find('#editBtn, #deleteBtn, #newBtn').show();
				
				main.find('input, select, textarea').prop('disabled', true);
				
				main.find('#aiRefno').val('{$ai_refno}');
				main.find('#aiRefno2').val('{$ai_refno}');
				main.find('#aiBranchcode').val('{$agent->AI_BranchCode}');
				main.find('#aiFname').val('{$agent->AI_FName}');
				main.find('#aiMname').val('{$agent->AI_MName}');
				main.find('#aiLname').val('{$agent->AI_LName}');
				main.find('#aiBdate').val('{$ai_bdate}');
				main.find('#aiSex').val('{$agent->AI_Sex}');
				main.find('#aiCivilstatus').val('{$agent->AI_CivilStatus}');
				main.find('#aiTelno').val('{$agent->AI_TelNo}');
				main.find('#aiMobileno').val('{$agent->AI_MobileNo}');
				main.find('#aiAdd1').val('{$agent->AI_Add1}');
				main.find('#aiAdd2').val('{$agent->AI_Add2}');
			"));
	}
	
	public function commission()
	{
		$this->template->load('template', 'master/commission');
	}
	
	public function agent_list2()
	{
		$data['name'] = $this->input->post('name');
		
		$data['agents'] = $this->master_model->get_agents2();
		
		$this->load->view('master/list/agent2', $data);
	}
	
	public function commission_form()
	{
		$this->form_validation->set_rules('airefno', 'Ref. No.', 'required|trim|xss_clean');
		
		if($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/form/commission');
		} else {
			//$this->master_model->add_commission();
			
			$this->db->trans_start();
			
			$name = $this->input->post('name');
			$start_date = date('m-d-y', strtotime($this->input->post('startdate')));
			$end_date = date('m-d-y', strtotime($this->input->post('enddate')));
			
			$remarks = 'CFP ' . $start_date . ' to ' . $end_date . ' ' . $name;
			
			// Generate control id
			$ctrl_id = 1;
			
			$this->db->select('ctrl_id');
			$this->db->group_by('ctrl_id');
			$this->db->order_by('ctrl_id', 'DESC');
			
			$query = $this->db->get('nhgt_master.tbl_commission');
			
			if( ! empty($query))
			{
				$ctrl_id = $query->row(0)->ctrl_id + 1;
			}
			
			$cfp_total = 0;
			
			foreach($this->input->post('commn') as $commn)
			{
				$data = explode('|', $commn);
				
				$commn_array = array(
					'ai_refno'				=> $this->input->post('airefno'),
					'ctrl_id'					=> $ctrl_id,
					'agent_type'			=> $data[0],
					'ci_acctno'				=> $data[1],
					'lh_pn'						=> $data[2],
					'loan_date'				=> $data[3],
					'client_name'			=> $data[4],
					'lh_monthlyamort' => $data[5],
					'terms'						=> $data[6],
					'loan_type'				=> $data[7],
					'cfp_amount'			=> $data[8],
					'cb_amount'				=> $data[9],
					'added_date'			=> date('Y-m-d'),
					'added_by'				=> $this->session->userdata('user_name')
				);
				
				$this->db->insert('nhgt_master.tbl_commission', $commn_array) or die($this->db->_error_message());
				
				$cfp_total += $data[8];
			}
			
			$disbmt_array = array(
				'trans_type' => 'CFP',
				'reference'	 => $ctrl_id,
				'trans_date' => date('Y-m-d H:i:s'),
				'amount'		 => $cfp_total,
				'remarks'		 => $remarks,
				'status'		 => 'ForPrinting',
				'entries'		 => '-'
			);
			
			$this->db->insert('tbl_disbursement', $disbmt_array) or die($this->db->_error_message());
			
			$this->db->trans_complete();
			
			redirect('master/commission_form');
		}
	}
	
	public function show_agent2()
	{
		$ai_refno = $this->input->post('airefno');
		$database = $this->input->post('database');
		
		$agent = $this->master_model->get_agent($ai_refno, $database);
		
		$name = $agent->AI_LName . ', ' . $agent->AI_FName . ' ' . $agent->AI_MName;
		
		header('Content-type: text/javascript');
		
		die(trims("
			var commnForm = $('#commnForm', parent.document).contents();
			commnForm.find('#name').val('{$name}');
			commnForm.find('#aiRefno').val('{$agent->AI_RefNo}');
		"));
	}
	
	public function list_commissions()
	{
		//$commn = $this->master_model->get_commission();
		
		$ai_refno = $this->input->post('airefno');
		$agent_type = $this->input->post('agenttype');
		$start_date = $this->input->post('startdate');
		$end_date = $this->input->post('enddate');
		
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
		
			$this->db->select('
					client.CI_AcctNo,
					ln_hdr.LH_PN,
					ln_hdr.LH_LoanDate,
					ln_hdr.CI_Name,
					ln_hdr.LH_Terms,
					ln_hdr.LH_LoanTrans,
					ln_hdr.LH_MonthlyAmort,
					ln_hdr.LH_Principal,
					ln_hdr.LH_Agent1,
					client.CI_Agent1_Rate,
					ln_hdr.LH_Agent2,
					client.CI_Agent2_Rate',
				false);
			$this->db->from($database . 'ln_hdr');
			$this->db->join($database . 'client', 'client.CI_AcctNo = ln_hdr.CI_AcctNo');
			
			if($agent_type == 0)
			{
				$this->db->not_like('ln_hdr.LH_LoanTrans', 'SPEC');
				$this->db->where('(ln_hdr.LH_Agent1 = "' . $ai_refno . '" OR ln_hdr.LH_Agent2 = "' . $ai_refno . '")');
			}
			elseif($agent_type == 1)
			{
				$this->db->not_like('ln_hdr.LH_LoanTrans', 'SPEC');
				$this->db->where('ln_hdr.LH_Agent1', $ai_refno);
			}
			elseif($agent_type == 2)
			{
				$this->db->where('ln_hdr.LH_LoanTrans', 'NEW');
				$this->db->where('ln_hdr.LH_Agent2', $ai_refno);
			}
			
			$this->db->where('ln_hdr.LH_LoanDate >=', $start_date);
			$this->db->where('ln_hdr.LH_LoanDate <=', $end_date);
			//$this->db->where('ln_hdr.LH_LoanType', 'PEN');
			$this->db->where('ln_hdr.LH_IsPending', 0);
			$this->db->where('ln_hdr.LH_Processed', 1);
			$this->db->where('ln_hdr.LH_Cancelled', 0);
			$this->db->order_by('ln_hdr.LH_LoanDate', 'ASC');
			$this->db->order_by('ln_hdr.CI_Name', 'ASC');
			
			$query = $this->db->get();
			
			if($query->num_rows() > 0)
			{
				foreach($query->result() as $row)
				{
					$type = 0;
					
					if($ai_refno == $row->LH_Agent1)
					{
						$ai_rate = $row->CI_Agent1_Rate;
						
						$type = 1;
					}
					elseif($ai_refno == $row->LH_Agent2)
					{
						$ai_rate = $row->CI_Agent2_Rate;
						
						$type = 2;
					}
					
					$this->db->select("CONCAT(AI_LName, ', ', AI_FName) as name", false);
					$this->db->from($database . 'agent');
					$this->db->where('AI_RefNo', $ai_refno);
					
					$query2 = $this->db->get();
					
					if($query2->num_rows() > 0)
					{
						$ai_name = $query2->row()->name;
					}
					
					//Qu
					$this->db->where('ai_refno', $ai_refno);
					$this->db->where('ci_acctno', $row->CI_AcctNo);
					$this->db->where('lh_pn', $row->LH_PN);
					
					$query3 = $this->db->get('nhgt_master.tbl_commission');
					
					$added_date = '';
					
					if($query3->num_rows() > 0)
					{
						$added_date = $query3->row()->added_date;
					}
					
					$data[] = array(
							'lh_pn'						=> $row->LH_PN,
							'lh_loandate'			=> $row->LH_LoanDate,
							'ci_name'					=> $row->CI_Name,
							'lh_loantrans'		=> $row->LH_LoanTrans,
							'lh_terms'				=> $row->LH_Terms,
							'lh_monthlyamort' => $row->LH_MonthlyAmort,
							'lh_principal'		=> $row->LH_Principal,
							'ai_name'					=> $ai_name,
							'ai_rate'					=> $ai_rate,
							'agent_type'			=> $type,
							'ci_acctno'				=> $row->CI_AcctNo,
							'added_date'			=> $added_date
						);
				}
				
				//return $data;
			}
		}
		
		header('Content-type: application/json');
		
		echo json_encode($data);
	}
	
	public function referenceno()
	{
		$this->template->load('template', 'master/referenceno');
	}
	
	public function agent_list3()
	{
		$data['name'] = $this->input->post('name');
		
		$data['agents'] = $this->master_model->get_agents2();
		
		$this->load->view('master/list/agent3', $data);
	}
	
	public function refno_form()
	{
		$this->form_validation->set_rules('airefno', 'Ref. No.', 'required|trim|xss_clean');
		
		if($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/form/refno');
		} else {
			$ai_refno2 = $this->input->post('airefno');
			
			//$this->master_model->update_agents();
			
			$this->db->trans_start();
			
			foreach($this->input->post('agent') as $agent)
			{
				$data = explode('|', $agent);
				$ai_refno = $data[0];
				$database = $data[1];
				
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
			
			$this->db->trans_complete();
			
			redirect('master/refno_form');
		}
	}
	
	public function list_agents()
	{
		$agents = $this->master_model->get_agents2();
		
		header('Content-type: application/json');
		
		echo json_encode($agents);
	}
	
	public function show_agent3()
	{
		$ai_refno = $this->input->post('airefno');
		$database = $this->input->post('database');
		
		$agent = $this->master_model->get_agent($ai_refno, $database);
		
		$name = $agent->AI_LName . ', ' . $agent->AI_FName;
		
		header('Content-type: text/javascript');
		
		die(trims("
			var refnoForm = $('#refnoForm', parent.document).contents();
			refnoForm.find('#name').val('{$name}');
			refnoForm.find('#aiRefno').val('{$agent->AI_RefNo}');
		"));
	}
	
	public function client_list3()
	{
		$data2 = explode('|', $this->uri->segment(3));
		$data['ai_refno'] = $data2[0];
		$data['database'] = $data2[1];
		
		$this->db->select("CI_AcctNo, CONCAT(CI_LName, ', ', CI_FName, ' ', CI_MName) as name, CI_Agent1, CI_Agent2, CI_Agent1_Rate, CI_Agent2_Rate", FALSE);
		$this->db->where('CI_Agent1', $data['ai_refno']);
		$this->db->or_where('CI_Agent2', $data['ai_refno']);
		
		$query = $this->db->get($data['database'] . 'client');
		
		$data['clients'] = $query->result();
		
		/*echo '<pre>';
		print_r($data['clients']);
		echo '</pre>';*/
		
		$this->load->view('master/list/client3', $data);
	}
	
	/***/
	
	function fetch_client_details()
	{
		$data = explode(' ', $this->input->post('data'));
		$ci_acctno = $data[0];
		
		$client = $this->master_model->fetch_client_details('*', $ci_acctno);
		$pension = $this->master_model->fetch_client_pension_details('*', $ci_acctno);
		$agent1 = $this->master_model->fetch_agent_details('CONCAT(AI_LName, \', \', AI_FName, \' \', AI_MName) as name', $client->CI_Agent1);
		$agent2 = $this->master_model->fetch_agent_details('CONCAT(AI_LName, \', \', AI_FName, \' \', AI_MName) as name', $client->CI_Agent2);
		$comaker = $this->master_model->fetch_comaker_details('CONCAT(CM_LName, \', \', CM_FName, \' \', CM_FName) as name', $client->CI_CoMaker);
		
		die(trims('
			var client_form = $(\'#client_form\', parent.document).contents();
			var client = client_form.find(\'#clientTab\');
			var pension = client_form.find(\'#pensionTab\');
			var dependent = client_form.find(\'#dependentTab\');
			var loan = client_form.find(\'#loan_listTab\');
			
			client.find(\'input, select, textarea\').prop(\'disabled\', true);
			pension.find(\'input, select\').prop(\'disabled\', true);
			dependent.find(\'input\').prop(\'disabled\', true);
			loan.find(\'input[type=button]\').prop(\'disabled\', false);
			client_form.find(\'#cancel-btn, #submit-btn, #system_message\').hide();
			client_form.find(\'#edit-btn, #new-btn\').show();
			
			client.find(\'#ci_source\').val(\'' . $client->CI_Source . '\');
			client.find(\'#ci_sssno\').val(\'' . $client->CI_SSSNo . '\');
			client.find(\'#ci_acctno\').val(\'' . $client->CI_AcctNo . '\');
			client.find(\'#ci_status\').val(\'' . $client->CI_Status . '\');
			client.find(\'#perimg\').prop(\'src\', \'data:image/jpeg; base64, ' . $client->CI_Picture . '\');
			client.find(\'#ci_picture\').val(\'' . $client->CI_Picture . '\');
			client.find(\'#ci_fname\').val(\'' . $client->CI_FName . '\');
			client.find(\'#ci_grp\').val(\'' . (!empty($client->CI_Grp) ? $client->CI_Grp : '') . '\');
			client.find(\'#ci_mname\').val(\'' . $client->CI_MName . '\');
			client.find(\'#ci_type\').val(\'' . $client->CI_Type . '\');
			client.find(\'#ci_lname\').val(\'' . $client->CI_LName . '\');
			client.find(\'#ci_branchcode\').val(\'' . $client->CI_BranchCode . '\');
			client.find(\'#ci_bdate\').val(\'' . date('Y-m-d', strtotime($client->CI_Bdate)) . '\');
			client.find(\'#age\').val(\'\');
			client.find(\'#ci_sex\').val(\'' . $client->CI_Sex . '\');
			client.find(\'#ci_civilstatus\').val(\'' . $client->CI_CivilStatus . '\');
			client.find(\'#ci_telno\').val(\'' . $client->CI_TelNo . '\');
			client.find(\'#ci_mobileno\').val(\'' . $client->CI_MobileNo . '\');
			client.find(\'#ci_add1\').val(\'' . $client->CI_Add1 . '\');
			client.find(\'#ci_add2\').val(\'' . $client->CI_Add2 . '\');
			client.find(\'#ci_cedulano\').val(\'' . $client->CI_CedulaNo . '\');
			client.find(\'#ci_ceduladate\').val(\'' . date('Y-m-d', strtotime($client->CI_CedulaDate)) . '\');
			client.find(\'#ci_cedulaplace\').val(\'' . $client->CI_CedulaPlace . '\');
			client.find(\'#agent1_name\').val(\'' . (!empty($agent1->name) ? $agent1->name : '') . '\');
			client.find(\'#ci_agent1\').val(\'' . $client->CI_Agent1 . '\');
			client.find(\'#ci_agent1_rate\').val(\'' . $client->CI_Agent1_Rate . '\');
			client.find(\'#agent2_name\').val(\'' . (!empty($agent2->name) ? $agent2->name : '') . '\');
			client.find(\'#ci_agent2\').val(\'' . $client->CI_Agent2 . '\');
			client.find(\'#ci_agent2_rate\').val(\'' . $client->CI_Agent2_Rate . '\');
			client.find(\'#comaker_name\').val(\'' . (!empty($comaker->name) ? $comaker->name : '') . '\');
			client.find(\'#ci_comaker\').val(\'' . $client->CI_CoMaker . '\');
			client.find(\'#ci_remarks\').val(\'' . $client->CI_Remarks . '\');
			client.find(\'#ci_problemacct\').prop(\'checked\', \'' . (strpos($client->CI_ProblemAcct, '>PA') !== false ? true : '') . '\');
			client.find(\'#arrears\').prop(\'checked\', \'' . (strpos($client->CI_ProblemAcct, '>AR') !== false ? true : '') . '\');
			
			pension.find(\'#cp_itf\').val(\'' . $pension->CP_ITF . '\');
			pension.find(\'#cp_pensiontype\').val(\'' . $pension->CP_PensionType . '\');
			pension.find(\'#cp_adno\').val(\'' . $pension->CP_ADNo . '\');
			pension.find(\'#cp_bankbranch\').val(\'' . $pension->CP_BankBranch . '\');
			pension.find(\'#cp_bankacctno\').val(\'' . $pension->CP_BankAcctNo . '\');
			pension.find(\'#cp_amount\').val(\'' . number_format($pension->CP_Amount, 2, '.', ',') . '\');
			pension.find(\'#cp_withdrawalday\').val(\'' . $pension->CP_WithdrawalDay . '\');
			pension.find(\'#cp_ptype\').val(\'' . $pension->CP_PType . '\');
			pension.find(\'#cp_causeofdeath\').val(\'' . $pension->CP_CauseOfDeath . '\');
			pension.find(\'#cp_dateofdeath\').val(\'' . date('Y-m-d', strtotime($pension->CP_DateOfDeath)) . '\');
			pension.find(\'#cp_disability\').val(\'' . $pension->CP_Disability . '\');
			
			dependent.find(\'#dependent_list\').prop(\'src\', \'dependent_list/' . $client->CI_AcctNo . '\');
			loan.find(\'#loan_list\').prop(\'src\', \'loan_list/' . $client->CI_AcctNo . '\');
		'));
		return;
	}
	
	function generate_ci_acctno()
	{
		$ci_acctno = $this->master_model->generate_ci_acctno();
		die(trims('
			$(\'#ci_acctno\').val(\'' . $ci_acctno . '\');
		'));
		return;
	}
	
	function fill_comaker_input()
	{
		$data = explode(' ', $this->input->post('data'));
		$cm_refno = $data[0];
		
		$comaker = $this->master_model->fetch_comaker_details('CONCAT(CM_LName, \', \', CM_FName, \' \', CM_MName) as name', $cm_refno);
		
		$function = '
			$(\'#comaker_name\', window.opener.document).val(\'' . $comaker->name . '\');
			$(\'#ci_comaker\', window.opener.document).val(\'' . $cm_refno . '\');
		';
		if($this->input->post('module') == 'loan_application')
		{
			$function = '
				$(\'#CoMakerName\', window.opener.document).val(\'' . $comaker->name . '\');
				$(\'#LH_CoMaker\', window.opener.document).val(\'' . $cm_refno . '\');
			';
		}
		
		die(trims(
		$function .
		'
			window.close();
		'));
		return;
	}
	
	function picture_form()
	{
		$this->load->view('master/form/picture');
		return;
	}
	
	function upload_picture()
	{
		$target_dir = $_SERVER['DOCUMENT_ROOT'] . '/tmp';
		$target_file = $target_dir . date('YmdHis') . basename($_FILES['file_to_upload']['name']);
		$upload_ok = 1;
		$image_file_type = pathinfo($target_file, PATHINFO_EXTENSION);
		
		if(isset($_POST['submit']))
		{
			$check = getimagesize($_FILES['file_to_upload']['tmp_name']);
			if($check === false)
			{
				echo 'File is not an image.';
				$upload_ok = 0;
			} else {
				$upload_ok = 1;
			}
		}
		
		if($upload_ok == 1)
		{
			if(move_uploaded_file($_FILES['file_to_upload']['tmp_name'], $target_file))
			{
				$bin_string = file_get_contents($target_file);
				$hex_string = base64_encode($bin_string);
			}
		}
		
		die(trims('
			<script src=\'' . base_url() . 'system/application/assets/js/jquery-2.1.3.min.js\' type=\'text/javascript\'></script>
			<script type=\'text/javascript\'>
				$(\'#perimg\', window.opener.document).prop(\'src\', \'data:image/jpeg; base64, ' . $hex_string . '\');
				$(\'#ci_picture\', window.opener.document).val(\'' . $hex_string . '\');
				window.close();
			</script>
		'));
		return;
	}
	
	function fetch_agent_details()
	{
		$data = explode(' ', $this->input->post('data'));
		$ai_refno = $data[0];
		
		$agent = $this->master_model->fetch_agent_details('*', $ai_refno);
		die(trims('
			var agent_form = $(\'#agent_form\', parent.document).contents();
			var agent = agent_form.find(\'#agentTab\');
			
			agent.find(\'input, select, textarea\').prop(\'disabled\', true);
			agent_form.find(\'#cancel-btn, #submit-btn, #system_message\').hide();
			agent_form.find(\'#edit-btn, #new-btn\').show();
			
			agent.find(\'#ai_refno\').val(\'' . $agent->AI_RefNo . '\');
			agent.find(\'#ai_branchcode\').val(\'' . $agent->AI_BranchCode . '\');
			agent.find(\'#ai_fname\').val(\'' . $agent->AI_FName . '\');
			agent.find(\'#ai_mname\').val(\'' . $agent->AI_MName . '\');
			agent.find(\'#ai_lname\').val(\'' . $agent->AI_LName . '\');
			agent.find(\'#ai_bdate\').val(\'' . date('Y-m-d', strtotime($agent->AI_Bdate)) . '\');
			agent.find(\'#ai_sex\').val(\'' . $agent->AI_Sex . '\');
			agent.find(\'#ai_civilstatus\').val(\'' . $agent->AI_CivilStatus . '\');
			agent.find(\'#ai_telno\').val(\'' . $agent->AI_TelNo . '\');
			agent.find(\'#ai_mobileno\').val(\'' . $agent->AI_MobileNo . '\');
			agent.find(\'#ai_add1\').val(\'' . $agent->AI_Add1 . '\');
			agent.find(\'#ai_add2\').val(\'' . $agent->AI_Add2 . '\');
		'));
		return;
	}
	
	function generate_ai_refno()
	{
		$ai_refno = $this->master_model->generate_ai_refno();
		die(trims('
			$(\'#ai_refno\').val(\'' . $ai_refno . '\');
		'));
		return;
	}
	
	function fetch_comaker_details()
	{
		$data = explode(' ', $this->input->post('data'));
		$cm_refno = $data[0];
		
		$comaker = $this->master_model->fetch_comaker_details('*', $cm_refno);
		die(trims('
			var comaker_form = $(\'#comaker_form\', parent.document).contents();
			var comaker = comaker_form.find(\'#comakerTab\');
			
			comaker.find(\'input, select, textarea\').prop(\'disabled\', true);
			comaker_form.find(\'#cancel-btn, #submit-btn, #system_message\').hide();
			comaker_form.find(\'#edit-btn, #new-btn\').show();
			
			comaker.find(\'#cm_refno\').val(\'' . $comaker->CM_RefNo . '\');
			comaker.find(\'#cm_branchcode\').val(\'' . $comaker->CM_BranchCode . '\');
			comaker.find(\'#cm_fname\').val(\'' . $comaker->CM_FName . '\');
			comaker.find(\'#cm_mname\').val(\'' . $comaker->CM_MName . '\');
			comaker.find(\'#cm_lname\').val(\'' . $comaker->CM_LName . '\');
			comaker.find(\'#cm_bdate\').val(\'' . date('Y-m-d', strtotime($comaker->CM_BDate)) . '\');
			comaker.find(\'#cm_sex\').val(\'' . $comaker->CM_Sex . '\');
			comaker.find(\'#cm_civilstatus\').val(\'' . $comaker->CM_CivilStatus . '\');
			comaker.find(\'#cm_telno\').val(\'' . $comaker->CM_TelNo . '\');
			comaker.find(\'#cm_mobileno\').val(\'' . $comaker->CM_MobileNo . '\');
			comaker.find(\'#cm_add1\').val(\'' . $comaker->CM_Add1 . '\');
			comaker.find(\'#cm_add2\').val(\'' . $comaker->CM_Add2 . '\');
			comaker.find(\'#cm_cedulano\').val(\'' . $comaker->CM_CedulaNo . '\');
			comaker.find(\'#cm_cedulaplace\').val(\'' . $comaker->CM_CedulaPlace . '\');
			comaker.find(\'#cm_ceduladate\').val(\'' . date('Y-m-d', strtotime($comaker->CM_CedulaDate)) . '\');
		'));
		return;
	}
	
	function generate_cm_refno()
	{
		$cm_refno = $this->master_model->generate_cm_refno();
		die(trims('
			$(\'#cm_refno\').val(\'' . $cm_refno . '\');
		'));
		return;
	}
	
	function dependent_list()
	{
		$ci_acctno = $this->uri->segment(3, '');
		$data['results'] = '';
		
		if(!empty($ci_acctno))
		{
			$columns = '*';
			$results = $this->master_model->fetch_dependents($columns, $ci_acctno);
			if(!empty($results))
			{
				foreach($results as $j => $results)
				{
					$data['results'][] = $results;
				}
			}
		}
		$this->load->view('master/list/dependent', $data);
		return;
	}
	
	function loan_list()
	{
		$ci_acctno = $this->uri->segment(3, '');
		$data['results'] = '';
		
		if(!empty($ci_acctno))
		{
			$columns = 'CI_AcctNo as ci_acctno, LH_PN as lh_pn, LH_MonthlyAmort as lh_monthlyamort, LH_Principal as lh_principal, LH_NetProceeds as lh_netproceeds, LH_InterestAmt as lh_interestamt, LH_Balance as lh_balance, LH_LoanTrans as lh_loantrans, LH_Terms as lh_terms, LH_LoanDate as lh_loandate, LH_StartDate as lh_startdate, LH_EndDate as lh_enddate, LH_IsTop as lh_istop';
			$results = $this->master_model->fetch_loans($columns, $ci_acctno);
			if(!empty($results))
			{
				foreach($results as $j => $results)
				{
					$data['results'][] = $results;
				}
			}
		}
		//print_r($data);
		$this->load->view('master/list/loan', $data);
		return;
	}
	
	function comaker_list()
	{
		$data['cm_lname'] = $this->input->post('cm_lname') == '' ? 'CRUZ' : $this->input->post('cm_lname');
		$data['results'] = '';
		
		
		$columns = 'CM_RefNo as cm_refno, CONCAT(CM_LName, \', \', CM_FName) as name';
		$results = $this->master_model->fetch_comakers($columns, $data['cm_lname']);
		if(!empty($results))
		{
			foreach($results as $j => $results)
			{
				$data['results'][] = $results;
			}
		}
		
		$this->load->view('master/list/comaker', $data);
		return;
	}
	
	function index()
	{
		$this->template->set('page_title', 'Master');
		$this->template->load('template', 'master/index');
		return;
	}

	function client()
	{
		$this->template->load('template', 'master/client');
		return;
	}

	function comaker()
	{
		$this->template->load('template', 'master/comaker');
		return;
	}
	
	function client_list()
	{
		$data['ci_type'] = $this->input->post('ci_type') == '' ? 'PEN' : $this->input->post('ci_type');
		$data['ci_status'] = $this->input->post('ci_status') == '' ? 'A' : $this->input->post('ci_status');
		$data['ci_lname'] = $this->input->post('ci_lname') == '' ? 'ABAC' : $this->input->post('ci_lname');
		$data['results'] = '';
		
		//$data['ci_lname'] = $ci_lname != '' ? $ci_lname : $this->input->post('ci_lname') == '' ? 'ABAC' : $this->input->post('ci_lname');
		
		$total_rows = $this->master_model->record_count($data['ci_lname'], $data['ci_type'], $data['ci_status']); //echo $config['total_rows'];
		if($total_rows > 0)
		{
			$columns = 'client.CI_AcctNo as ci_acctno, CONCAT(client.CI_LName, \', \', client.CI_FName, \' \', client.CI_MName) as name, client_pension.CP_PensionType as cp_pensiontype, client_pension.CP_BankBranch as cp_bankbranch';
			$results = $this->master_model->fetch_clients($columns, $data['ci_lname'], $data['ci_type'], $data['ci_status']);
			if(!empty($results))
			{
				foreach($results as $j => $results)
				{
					$data['results'][] = $results;
				}
			}
		}
		
		$this->load->view('master/list/client', $data);
		return;
	}
	
	function client_form($system_message = '')
	{
		$data['system_message'] = $system_message;
		$data['results'] = $this->master_model->fetch_branches();
		
		$result = $this->master_model->fetch_branch_code();
		$result = explode(';', $result->value);
		$data['branch_code'] = $result[0];
		
		$this->load->view('master/form/client', $data);
		return;
	}
	
	function pension_type_list()
	{
		$data['ci_source'] = $this->uri->segment(3) ? $this->uri->segment(3) : 'SSS';
		
		$data['results'] = $this->master_model->fetch_pension_types();
		
		$this->load->view('master/list/pension_type', $data);
		return;
	}
		
	function fill_pension_type_input()
	{
		$data = explode('|', $this->input->post('data'));
		die(trims('
			$(\'#cp_pensiontype\', window.opener.document).val(\'' . $data[0] . '\');
			$(\'#pensiontype\', window.opener.document).val(\'' . $data[1] . '\');
			window.close();
		'));
		return;
	}
	
	function insert_client()
	{
		$this->master_model->validate_client_inputs();
		
		if($this->form_validation->run() != FALSE)
		{
			$system_message = $this->master_model->insert_client();
		}
		$this->client_form($system_message);
		return;
	}
	
	function update_client()
	{
		$this->master_model->validate_client_inputs();
		
		if($this->form_validation->run() != FALSE)
		{
			$system_message = $this->master_model->update_client();
		}
		$this->client_form($system_message);
		return;
	}
	
	function delete_dependent()
	{
		$system_message = 'test';
		$system_message = $this->master_model->delete_dependent();
		
		die(trims('
			$(\'#dependent_process\').html(\'' . $system_message . '\').css(\'display\', \'\');
			$(\'#dependent_list\').prop(\'src\', \'dependent_list/' . $this->input->post('ci_acctno') . '\');
		'));
		return;
	}
	
	function insert_dependent()
	{
		$this->master_model->validate_dependent_inputs();
		
		$system_message = '';
		if($this->form_validation->run() != FALSE)
		{
			$system_message = $this->master_model->insert_dependent();
		}
		
		if($system_message == '')
		{
			$system_message = validation_errors();
		}
		
		die(trims('
			$(\'#dependent_process\').html(\'' . $system_message . '\').css(\'display\', \'\');
			$(\'#dependent_list\').prop(\'src\', \'dependent_list/' . $this->input->post('ci_acctno') . '\');
		'));
		return;
	}
	
	function update_dependent()
	{
		$this->master_model->validate_dependent_inputs();
		
		$system_message = '';
		if($this->form_validation->run() != FALSE)
		{
			$system_message = $this->master_model->update_dependent();
		}
		
		if($system_message == '')
		{
			$system_message = validation_errors();
		}
		
		die(trims('
			$(\'#dependent_process\').html(\'' . $system_message . '\').css(\'display\', \'\');
			$(\'#dependent_list\').prop(\'src\', \'dependent_list/' . $this->input->post('ci_acctno') . '\');
		'));
		return;
	}
	
	function insert_agent()
	{
		$this->master_model->validate_agent_inputs();
		
		$system_message = '';
		if($this->form_validation->run() != FALSE)
		{
			$system_message = $this->master_model->insert_agent();
		}
		$this->agent_form($system_message);
		return;
	}
	
	function update_agent()
	{
		$this->master_model->validate_agent_inputs();
		
		$system_message = '';
		if($this->form_validation->run() != FALSE)
		{
			$system_message = $this->master_model->update_agent();
		}
		$this->agent_form($system_message);
		return;
	}
	
	function insert_comaker()
	{
		$this->master_model->validate_comaker_inputs();
		
		$system_message = '';
		if($this->form_validation->run() != FALSE)
		{
			$system_message = $this->master_model->insert_comaker();
		}
		$this->comaker_form($system_message);
		return;
	}
	
	function update_comaker()
	{
		$this->master_model->validate_comaker_inputs();
		
		$system_message = '';
		if($this->form_validation->run() != FALSE)
		{
			$system_message = $this->master_model->update_comaker();
		}
		$this->comaker_form($system_message);
		return;
	}
	
	/******************** Old functions ********************/
	function updateLoanIsTop()
	{
		$this->load->helper('code');
		
		$data = explode(' ', $this->input->post('data'));
		$tag = $this->input->post('tag');
		if($tag=='0')
		{
			$isTop = '1';
		} else if($tag=='1') {
			$isTop = '0';
		}
		$loanArray = array(
			'LH_IsTop' => $isTop
		);
		$this->db->where('CI_AcctNo', $data[1]);
		$this->db->where('LH_PN', $data[2]);
		$this->db->update('ln_hdr', $loanArray) or die( $this->db->_error_message() );
		die(trims("
			document.location.reload();
		"));
		return;
	}
	
	/*function updateAllLoanIsTop()
	{
		$this->load->helper('code');
		
		$CI_AcctNo = $this->input->post('acctno');
		$loanArray = array(
			'LH_IsTop' => '0'
		);
		$this->db->where('CI_AcctNo', $CI_AcctNo);
		$this->db->update('ln_hdr', $loanArray) or die( $this->db->_error_message() );
		die(trims("
			document.location.reload();
		"));
		return;
	}*/
	
	function clientLedgerList()
	{
		$clientid = $this->uri->segment(3, '');
		$loanid = $this->uri->segment(4, '');
		$param['clientid'] = $clientid;
		$param['loanid'] = $loanid;
			
		$result = $this->db->query(
			"SELECT *
			FROM ln_hdr
			WHERE CI_AcctNo='$clientid'
			AND LH_IsPending='0'
			AND LH_Processed='1'
			AND LH_Cancelled='0'
			AND LH_PN='$loanid';
		");
		$param['datas'] = $result->result_array();
		
		$LnHdr = $result->row();
		$agent1 = $this->db->query("
			SELECT CONCAT(AI_LName, ', ', AI_FName, ' ', AI_MName) as name
			FROM agent
			WHERE AI_RefNo='".$LnHdr->LH_Agent1."'
		")->row();
		if(empty($agent1->name))
		{
			$param['agent1name'] = '';
		} else {
			$param['agent1name'] = $agent1->name;
		}
		
		$param['pn'] = $this->db->query(
			"SELECT LH_PN, LH_LoanDate
			FROM ln_hdr
			WHERE CI_AcctNo='$clientid'
			AND LH_LoanTrans!='SPEC'
			AND LH_IsPending='0'
			AND LH_Processed='1'
			AND LH_Cancelled='0'
			ORDER BY LH_LoanDate DESC;
		")->result_array();
		
		$query = $this->db->query("
			SELECT *
			FROM ln_ldgr 
			WHERE CI_AcctNo='$clientid' 
			AND LH_PN='$loanid' 
			AND LL_IsDeleted='0'
			ORDER BY LL_PaymentDate ASC, LL_IsPayment DESC;
		");
		
		$param['ledger'] = $query->result_array();
		$param['post_count'] = $query->num_rows();
		
		$this->load->model('database');
		$param['bname'] = $this->database->get_bname($LnHdr->LH_BranchCode);
		$this->load->view("master/list/clientledger", $param);
		return;
	}
	function clientLedgerForm()
	{
		$clientid = $this->uri->segment(3, '');
		$loanid = $this->uri->segment(4, '');
		$param['datas'] = $this->db->query("
			SELECT LH_PN,
				LH_MonthlyAmort,
				LH_LoanTrans,
				LH_Terms
			FROM ln_hdr
			WHERE CI_AcctNo='".$clientid."'
			AND LH_LoanTrans!='SPEC'
			AND LH_IsPending='0'
			AND LH_Processed='1'
			AND LH_Cancelled='0'
			ORDER BY LH_PN DESC;
		")->result_array();
		
		$process = $this->uri->segment(5, '');
		$param['lh_balance'] = $this->uri->segment(6, '');
		$param['lh_payment'] = $this->uri->segment(7, '');
		$param['for_refund'] = $this->uri->segment(8, '');
		if($process == 0)
		{
			$id = $this->uri->segment(6, '');
			$amountcash_payment = $this->uri->segment(7, '');
			$refund = $this->uri->segment(8, '');
			$paymentdate = $this->uri->segment(9, '');
			$param['lh_balance'] = $this->uri->segment(10, '');
			$param['lh_payment'] = $this->uri->segment(11, '');
			$param['for_refund'] = $this->uri->segment(12, '');
			$ledger = $this->db->query("
				SELECT *
				FROM ln_ldgr
				WHERE ID='".$id."'
				AND CI_AcctNo='".$clientid."'
				AND LH_PN='".$loanid."'
				AND LL_AmountCash_Payment = '".$amountcash_payment."'
				AND LL_Refund = '".$refund."'
				AND LL_PaymentDate = '".date('Y-m-d', strtotime($paymentdate))."';
			")->row();
			
			$param['CVNo'] = '';
			$param['ID'] = $ledger->ID;
			$param['LL_ORNo'] = $ledger->LL_ORNo;
			$param['LL_CRNo'] = $ledger->LL_CRNo;
			$param['LL_CheckNo'] = '';
			$param['RFW_NO'] = '';
			if(strpos($ledger->RFW_NO, 'RFP')!==FALSE)
			{
				$param['RFW_NO'] = str_replace('RFP#', '', $ledger->RFW_NO);
			} elseif(strpos($ledger->RFW_NO, 'RPF')!==FALSE) {
				$param['RFW_NO'] = str_replace('RPF#', '', $ledger->RFW_NO);
			} elseif(strpos($ledger->RFW_NO, 'CV#')!==FALSE||strpos($ledger->RFW_NO, 'CK#')!==FALSE&&strpos($ledger->RFW_NO, 'RFP#')&&strpos($ledger->RFW_NO, 'RPF#')) {
				$data = explode('#', $ledger->RFW_NO);
				
				if(strpos($ledger->RFW_NO, 'CK#')!==FALSE)
				{
					$param['CVNo'] = str_replace('CK', '', $data[1]);
					$param['LL_CheckNo'] = $data[2];
				} else {
					$param['CVNo'] = str_replace('CK', '', $data[1]);
					$param['LL_CheckNo'] = '';
				}
			}
			
			if($ledger->LL_AmountCash>0&&$ledger->LL_AmountCheck==0)
			{
				$param['LL_AmountCash_Payment'] = $ledger->LL_AmountCash;
			} elseif($ledger->LL_AmountCash==0&&$ledger->LL_AmountCheck>0) {
				$param['LL_AmountCash_Payment'] = $ledger->LL_AmountCheck;
			} elseif($ledger->LL_AmountCash==0&&$ledger->LL_AmountCheck==0) {
				$param['LL_AmountCash_Payment'] = '';
			}
			$param['LL_Refund'] = $ledger->LL_Refund;
			$param['LL_Remarks'] = $ledger->LL_Remarks;
			$param['LL_PaymentDate'] = $ledger->LL_PaymentDate;
		}

		$param['process'] = $this->uri->segment(5, '');		
		$param['CI_AcctNo'] = $this->uri->segment(3, '');
		$param['LH_PN'] = $this->uri->segment(4, '');
		$this->load->view("master/form/clientledger", $param);
		return;
	}
	
	function addRemarks()
	{
		$paymentdate = $this->input->post('paymentdate');
		$paymentmonth = strtoupper(date('M', strtotime($paymentdate)));
		$paymentdate = $paymentmonth.' '.date('Y', strtotime($paymentdate)).' REFUND';
		die(trims("
			$('#LL_Remarks').val('{$paymentdate}');
		"));
	}
	
	function comaker_form($system_message = '')
	{
		$data['system_message'] = $system_message;
		$data['results'] = $this->master_model->fetch_branches();
		
		$result = $this->master_model->fetch_branch_code();
		$result = explode(';', $result->value);
		$data['branch_code'] = $result[0];
		
		$this->load->view('master/form/comaker', $data);
		return;
	}
	
	function insertDependent()
	{
		date_default_timezone_set('Asia/Manila');
		
		$this->load->helper('code');
		$this->load->model('database');
		$result = $this->database->generateId('client_dependents', 'SysID');
		$nextSysID = $result->SysID + 1;
		$dependentArray = array(
			'SysID'				=> $nextSysID,
			'CI_AcctNo'			=> $_POST['AcctNo'],
			'CD_RefNo'			=> '',
			'CD_IsSurvivingDep' => (isset($_POST['CD_IsSurvivingDep'])?1:NULL),
			'CD_FName'			=> ($_POST['CD_FName']==''?'':$_POST['CD_FName']),
			'CD_LName'			=> ($_POST['CD_LName']==''?'':$_POST['CD_LName']),
			'CD_SSSNo'			=> ($_POST['CD_SSSNo']==''?'':$_POST['CD_SSSNo']), 
			'CD_BDate'			=> date('Y-m-d', strtotime($_POST['CD_BDate'])),
			'CD_Profession'		=> ($_POST['CD_Profession']==''?'':$_POST['CD_Profession']),
			'CD_Relation'		=> ($_POST['CD_Relation']==''?'':$_POST['CD_Relation']),
			'CD_IsAdded'		=> 1,
			'CD_AddedBy'		=> $this->session->userdata['user_name'],
			'CD_AddedDate'		=> date('Y-m-d'),
			'CD_IsDeleted'		=> 0,
			'CD_IsDeletedBy'	=> 0,
			'CD_IsModified'		=> 0
		);
		$this->db->insert('client_dependents', $dependentArray) or die($this->db->_error_message());
		
		die(trims("
			<script>
				window.parent.document.location.href='client';
			</script>
		"));
		return;
	}
	
	function updateDependent()
	{
		date_default_timezone_set('Asia/Manila');
		
		$this->load->helper('code');
		$dependentArray = array(
			'CD_IsSurvivingDep' => (isset($_POST['CD_IsSurvivingDep'])?1:NULL),
			'CD_FName'			=> ($_POST['CD_FName']==''?'':$_POST['CD_FName']),
			'CD_LName'			=> ($_POST['CD_LName']==''?'':$_POST['CD_LName']),
			'CD_SSSNo'			=> ($_POST['CD_SSSNo']==''?'':$_POST['CD_SSSNo']), 
			'CD_BDate'			=> date('Y-m-d', strtotime($_POST['CD_BDate'])),
			'CD_Profession'		=> ($_POST['CD_Profession']==''?'':$_POST['CD_Profession']),
			'CD_Relation'		=> ($_POST['CD_Relation']==''?'':$_POST['CD_Relation']),
			'CD_IsModified'		=> 1
		);
		$this->db->where('SysID', $_POST['SysID']);
		$this->db->where('CI_AcctNo', $_POST['AcctNo']);
		$this->db->update('client_dependents', $dependentArray) or die($this->db->_error_message());
		die(trims("
			<script>
				document.location.href='clientForm';
			</script>
		"));
		return;
	}
	
	function insertLedgerPost()
	{
		$CheckNo = '';
		$CVNo = '';
		$LL_CheckNo = '';
		$LL_CRNo = '';
		$LL_ORNo = '';
		$LL_PRNo = '';
		$RFW_NO = '';
		
		if($_POST['LL_ORNo']!='')
		{
			$LL_ORNo = $_POST['LL_ORNo'];
		}
		if($_POST['LL_CRNo']!='')
		{
			$LL_CRNo = $_POST['LL_CRNo'];
		}
		if($_POST['PRNo']!='')
		{
			$LL_ORNo = $_POST['PRNo'];
		}
		if($_POST['CVNo']!='')
		{
			$CVNo = $_POST['CVNo'];
		}
		if($_POST['LL_CheckNo']!='')
		{
			$LL_CheckNo = $_POST['LL_CheckNo'];
		}
		if($_POST['RFW_NO']=='')
		{
			if($CVNo!='')
			{
				$CVNo = 'CV#'.$CVNo;
			}
			if($LL_CheckNo!='')
			{
				$CheckNo = 'CK#'.$LL_CheckNo;
			}
			$RFW_NO = $CVNo.$CheckNo;
		} else
		{
			$RFW_NO = 'RFP#'.$_POST['RFW_NO'];
		}
		
		if($_POST['TransType']=='Collection')
		{
			$LL_IsPayment = '1';
			$LL_IsRFW = '0';
			$LL_IsRefund = '0';
		} elseif($_POST['TransType']=='Refund')
		{
			$LL_IsPayment = '0';
			$LL_IsRFW = '1';
			$LL_IsRefund = '1';
		}
		
		$this->db->trans_start();
		
		$ledgerPostArray = array(
			'ID'						=> $_POST['ID'],
			'CI_AcctNo'					=> $_POST['CI_AcctNo'],
			'LH_BranchCode_Processed'	=> '',
			'LH_BankAcctNo_Branch'		=> '',
			'LH_BankAcctNo'				=> '',
			'LH_PN'						=> $_POST['LH_PN'],
			'LH_LoanType'				=> '',
			'LL_ORNo'					=> $LL_ORNo,
			'LL_CRNo'					=> $LL_CRNo,
			'LL_CheckNo'				=> $LL_CheckNo,
			'RFW_NO'					=> $RFW_NO,
			'LL_Rebates'				=> 0,
			'LL_InterestAmt'			=> 0,
			'LL_AmountCheck'			=> ($_POST['LL_CheckNo'] == '' ? 0 : ($_POST['LL_AmountCash_Payment'] == '' ? 0 : $_POST['LL_AmountCash_Payment'])),
			'LL_AmountCash'				=> ($_POST['LL_CheckNo'] != '' ? 0 : ($_POST['LL_AmountCash_Payment'] == '' ? 0 : $_POST['LL_AmountCash_Payment'])),
			'LL_AmountCash_Payment'		=> ($_POST['LL_AmountCash_Payment'] == '' ? 0 : $_POST['LL_AmountCash_Payment']),
			'LL_ShortPayment'			=> 0,
			'LL_Refund'					=> ($_POST['LL_Refund'] == '' ? 0 : $_POST['LL_Refund']),
			'LL_Remarks'				=> $_POST['LL_Remarks'],
			'LL_PaymentDate'			=> date('Y-m-d', strtotime($_POST['LL_PaymentDate'])),
			'LL_Processed'				=> 1,
			'LL_IsPayment'				=> $LL_IsPayment,
			'LL_IsRFW'					=> $LL_IsRFW,
			'LL_IsRefund'				=> $LL_IsRefund,
			'LL_CM'						=> 0,
			'LL_IsBounceCheck'			=> 0,
			'LL_IsUncollected'			=> 0,
			'LL_IsShortPayment'			=> 0,
			'LL_IsDeleted'				=> 0,
			'LL_IsModifiedBy'			=> $this->session->userdata('user_name'),
			'LL_IsModifiedDate'			=> date('Y-m-d'),
			'LL_Posted_BySales'			=> 0,
			'LL_Posted_ByAcct'			=> 0,
			'LL_IsAdded'				=> 1
		);
		$this->db->insert('ln_ldgr', $ledgerPostArray) or die($this->db->_error_message());
		
		$val = 0;
		if($this->input->post('TransType') == 'Collection')
		{
			$val = -1 * ($this->input->post('LL_AmountCash_Payment'));
		} elseif($this->input->post('TransType') == 'Refund') {
			$val = $this->input->post('LL_Refund');
		}
		
		$lh_refund = $this->input->post('for_refund') - $val;
		if($lh_refund <= 0)
		{
			$lh_refund = 0;
		} else {
			$lh_refund = -1 * $lh_refund;
		}
		
		$lh_balance = $this->input->post('lh_balance') + $val;
		$lh_payment = $this->input->post('lh_payment') - $val;
		
		$lh_istop = 0;
		if($lh_balance > 0)
		{
			$lh_istop = 1;
		}
		$pn_array = array(
			'LH_Balance' => $lh_balance,
			'LH_Refund' => $lh_refund,
			'LH_Payment' => $lh_payment,
			'LH_IsTop' => $lh_istop
		);
		$this->db->where('CI_AcctNo', $_POST['CI_AcctNo']);
		$this->db->where('LH_PN', $_POST['LH_PN']);
		$this->db->update('ln_hdr', $pn_array) or die($this->db->_error_message());
		
		$this->db->trans_complete();
			
		die(trims("
			<script>
				window.opener.location.reload();
				window.close();
			</script>
		"));
		return;
	}
	
	function deleteLedgerPost()
	{
		$acctno = $this->input->post('acctno');
		$pnno = $this->input->post('pnno');
		$postCount = $this->input->post('postCount');
		$i = 0;
		while($i<$postCount)
		{
			$ledgerPost[$i] = $this->input->post('ledgerPost'.$i);
			if(isset($ledgerPost[$i])&&$ledgerPost[$i]!=NULL)
			{
				$data = explode(';', $ledgerPost[$i]);
				$post_array = array(
					'LL_IsDeleted'		=> 1,
					'LL_IsDeletedBy'	=> $this->session->userdata('user_name'),
					'LL_IsDeletedDate'	=> date('Y-m-d')
				);
				$this->db->where('ID', $data[0]);
				$this->db->where('CI_AcctNo', $acctno);
				$this->db->where('LH_PN', $pnno);
				$this->db->where('LL_AmountCash_Payment', $data[1]);
				$this->db->where('LL_Refund', $data[2]);
				$this->db->where('LL_PaymentDate', $data[3]);
				$this->db->update('ln_ldgr', $post_array) or die($this->db->_error_message());
			}
			$i++;
		}
		
		die(trims("
			<script>
				window.parent.document.location.href='clientLedgerList/{$acctno}/{$pnno}';
			</script>
		"));
		return;
	}
	
	function updateLedgerPost()
	{
		$CheckNo = '';
		$CVNo = '';
		$LL_CheckNo = '';
		$LL_CRNo = '';
		$LL_ORNo = '';
		$LL_PRNo = '';
		$RFW_NO = '';
		
		if($_POST['LL_ORNo'] != '')
		{
			$LL_ORNo = $_POST['LL_ORNo'];
		}
		if($_POST['LL_CRNo'] != '')
		{
			$LL_CRNo = $_POST['LL_CRNo'];
		}
		if($_POST['PRNo'] != '')
		{
			$LL_ORNo = $_POST['PRNo'];
		}
		if($_POST['CVNo'] != '')
		{
			$CVNo = $_POST['CVNo'];
		}
		if($_POST['LL_CheckNo'] != '')
		{
			$LL_CheckNo = $_POST['LL_CheckNo'];
		}
		if($_POST['RFW_NO'] == '')
		{
			if($CVNo != '')
			{
				$CVNo = 'CV#' . $CVNo;
			} elseif($CVNo == '') {
				$CVNo == '';
			}
			if($LL_CheckNo != '')
			{
				$CheckNo = 'CK#' . $LL_CheckNo;
			} elseif($LL_CheckNo == '') {
				$CheckNo = '';
			}
			$RFW_NO = $CVNo . $CheckNo;
		} else {
			$RFW_NO = 'RFP#' . $_POST['RFW_NO'];
		}
		
		$this->db->trans_start();
		
		$post_array = array(
			'ID' => $_POST['ID'],
			'LH_PN' => $_POST['LH_PN'],
			'LL_ORNo' => $LL_ORNo,
			'LL_CRNo' => $LL_CRNo,
			'LL_CheckNo' => $LL_CheckNo,
			'RFW_NO' => $RFW_NO,
			'LL_AmountCheck' => ($_POST['LL_CheckNo'] == '' ? 0 : ($_POST['LL_AmountCash_Payment'] == '' ? 0 : $_POST['LL_AmountCash_Payment'])),
			'LL_AmountCash' => ($_POST['LL_CheckNo'] != '' ? 0 : ($_POST['LL_AmountCash_Payment'] =='' ? 0 : $_POST['LL_AmountCash_Payment'])),
			'LL_AmountCash_Payment' => ($_POST['LL_AmountCash_Payment'] == '' ? 0 : $_POST['LL_AmountCash_Payment']),
			'LL_Refund' => ($_POST['LL_Refund'] == '' ? 0 : $_POST['LL_Refund']),
			'LL_Remarks' => $_POST['LL_Remarks'],
			'LL_PaymentDate' => date('Y-m-d', strtotime($_POST['LL_PaymentDate'])),
			'LL_IsModifiedBy' => $this->session->userdata('user_name'),
			'LL_IsModifiedDate' => date('Y-m-d')
		);
		
		$AmountCash_Payment = ($_POST['AmountCash_Payment'] == '' ? 0 : $_POST['AmountCash_Payment']);
		$Refund = ($_POST['Refund'] == '' ? 0 : $_POST['Refund']);
		$this->db->where('CI_AcctNo', $_POST['CI_AcctNo']);
		$this->db->where('LH_PN', $_POST['PNNo']);
		$this->db->where('ID', $_POST['TransNo']);
		$this->db->where('LL_AmountCash_Payment', $AmountCash_Payment);
		$this->db->where('LL_Refund', $Refund);
		$this->db->where('LL_PaymentDate', date('Y-m-d', strtotime($_POST['PaymentDate'])));
		$this->db->update('ln_ldgr', $post_array) or die($this->db->_error_message());
		
		$val = 0;
		if($this->input->post('TransType') == 'Collection')
		{
			$val = -1 * ($this->input->post('LL_AmountCash_Payment') - $this->input->post('AmountCash_Payment'));
		} elseif($this->input->post('TransType') == 'Refund') {
			$val = $this->input->post('LL_Refund') - $this->input->post('Refund');
		}
		
		$lh_refund = $this->input->post('for_refund') - $val;
		if($lh_refund <= 0)
		{
			$lh_refund = 0;
		} else {
			$lh_refund = -1 * $lh_refund;
		}
		
		$lh_balance = $this->input->post('lh_balance') + $val;
		$lh_payment = $this->input->post('lh_payment') - $val;
		
		$lh_istop = 0;
		if($lh_balance > 0)
		{
			$lh_istop = 1;
		}
		$pn_array = array(
			'LH_Balance' => $lh_balance,
			'LH_Refund' => $lh_refund,
			'LH_Payment' => $lh_payment,
			'LH_IsTop' => $lh_istop
		);
		
		$this->db->where('CI_AcctNo', $_POST['CI_AcctNo']);
		$this->db->where('LH_PN', $_POST['LH_PN']);
		$this->db->update('ln_hdr', $pn_array) or die($this->db->_error_message());
		
		$this->db->trans_complete();
		
		die(trims("
			<script>
				window.opener.location.reload();
				window.close();
			</script>
		"));
		return;
	}
	
	function updateLn_HdrBalance()
	{
		$this->load->helper('code');
		
		$CI_AcctNo = $this->uri->segment(3, '');
		$LH_PN = $this->uri->segment(4, '');
		$LH_Balance = $this->uri->segment(5, '');
		$LH_Payment = $this->uri->segment(6, '');
		$LH_Refund = $this->uri->segment(7, '');
		
		$LH_Refund = -1 * abs($LH_Refund);
		
		$LH_IsTop = 1;
		if($LH_Balance <= 0)
		{
			$LH_IsTop = 0;
		}
		if($LH_PN)
		{
			$loan_array = array(
				'LH_Balance' => $LH_Balance,
				'LH_Refund' => $LH_Refund,
				'LH_Payment' => $LH_Payment,
				'LH_IsTop' => $LH_IsTop
			);
			$this->db->where('CI_AcctNo', $CI_AcctNo);
			$this->db->where('LH_PN', $LH_PN);
			$this->db->update('ln_hdr', $loan_array) or die($this->db->_error_message());
			
			die(trims("
				<script src='" . base_url() . "system/application/assets/js/jquery-2.1.3.min.js' type='text/javascript''></script>
				<script>
					$(document).ready(function() {
						window.opener.$('#loan_list').prop('src', 'loan_list/" . $CI_AcctNo . "');
						window.close();
					});
				</script>
			"));
		}
		return;
	}
	
	function obcard()
	{
		if(strpos($this->uri->segment(3, ''),'|') !== FALSE)
		{
			$data = explode('|', $this->uri->segment(3, ''));
			$database = $data[0];
			$acctno = $data[1];
		}
		else
		{
			$database = '';
			$acctno = $this->uri->segment(3, '');
		}
		
		$this->db->select('CI_CoMaker, CI_Agent1, CI_Agent2');
		$this->db->where('CI_AcctNo', $acctno);
		$client = $this->db->get('client')->row();
		
		$param['datas'] = $this->db->query("
			SELECT CONCAT(a.CI_LName, ', ', a.CI_FName, ' ', a.CI_MName) as name,
				a.CI_Add1,
				a.CI_Bdate,
				a.CI_SSSNo,
				a.CI_TelNo,
				b.CP_BankBranch,
				b.CP_BankAcctNo,
				b.CP_ITF,
				b.CP_DateOfDeath,
				b.CP_CauseOfDeath,
				b.CP_PensionType,
				b.CP_Amount,
				b.CP_WithdrawalDay
			FROM {$database}client a,
				{$database}client_pension b
			WHERE a.CI_AcctNo='" . $acctno . "'
			AND a.CI_AcctNo=b.CI_AcctNo
		")->result_array();
		
		$param['cmname'] = '';
		$param['agent1name'] = '';
		$param['agent2name'] = '';
		$param['cd'] = $this->db->query("
			SELECT CD_LName,
				CD_FName,
				CD_BDate
			FROM {$database}client_dependents
			WHERE CI_AcctNo='" . $acctno . "';
		")->result_array();
		
		$cm = $this->db->query("
			SELECT CONCAT(CM_LName, ', ', CM_FName, ' ', CM_MName) as name
			FROM {$database}comaker
			WHERE CM_RefNo='" . $client->CI_CoMaker . "'
		")->row();
		
		if($cm)
		{
			$param['cmname'] = $cm->name;
		}
		
		$agent1 = $this->db->query("
			SELECT CONCAT(AI_LName, ', ', AI_FName, ' ', AI_MName) as name
			FROM {$database}agent
			WHERE AI_RefNo='" . $client->CI_Agent1 . "'
		")->row();
		
		if($agent1)
		{
			$param['agent1name'] = $agent1->name;
		}
		
		$agent2 = $this->db->query("
			SELECT CONCAT(AI_LName, ', ', AI_FName, ' ', AI_MName) as name
			FROM {$database}agent
			WHERE AI_RefNo='".$client->CI_Agent2."'
		")->row();
		
		if($agent2)
		{
			$param['agent2name'] = $agent2->name;
		}
		
		$query = $this->db->query("
			SELECT code,
				value
			FROM {$database}parameter
			WHERE code='COMPANY'
		")->row();
		
		$data = explode(';', $query->value);
		$param['companyname'] = $data[1];
		
		$this->load->view("master/print/obcard", $param);
	}
	
	function billinglist()
	{
		$acctno = $this->uri->segment(3, '');
		
		$param['acctno'] = $this->uri->segment(3, '');
		$param['datas'] = '';
		if($param['acctno'])
		{
			$param['datas'] = $this->db->query("
				SELECT CI_AcctNo, 
					LH_PN,
					LH_Reference,
					LH_MonthlyAmort,
					LH_Principal,
					LH_NetProceeds,
					LH_InterestAmt,
					LH_Balance,
					LH_LoanTrans,
					LH_Terms,
					LH_LoanDate,
					LH_StartDate,
					LH_EndDate,
					LH_IsTop
				FROM ln_hdr
				WHERE CI_AcctNo='" . $param['acctno'] . "'
				AND LH_LoanTrans!='SPEC'
				AND LH_IsPending='0'
				AND LH_Processed='1'
				AND LH_Cancelled='0'
				ORDER BY LH_LoanDate DESC;
			")->result_array();
		}
		$this->load->view("master/list/bill", $param);
		return;
	}
	
	function insert_billing()
	{
		if($this->input->post('loan'))
		{
			$billdate = $this->input->post('year') . '.' . $this->input->post('month');
			$yrmo = explode('.', $billdate);
			
			foreach($this->input->post('loan') as $i => $loan)
			{
				$acctno = $this->input->post('acctno');
				$pnno = $loan;
				$columns = 'CI_AcctNo, LH_PN, LH_LoanTrans, LH_WithdrawalDate, CI_Name, LH_PaymentType, LH_BankAcctNo, LH_BankBranch, LH_StartDate, LH_EndDate, LH_Terms, LH_Balance, LH_MonthlyAmort, LH_BranchCode, LH_LoanDate';
				$results = $this->master_model->fetch_loan_details($columns, $acctno, $pnno);
				
				foreach($results as $j => $results)
				{
					$column[] = $results;
				}
				$columns = 'CI_Source';
				$results = $this->master_model->fetch_client_details($columns, $acctno);
				foreach($results as $j => $results)
				{
					$column[] = $results;
				}
				
				// Generate bill date
				$blldt = date('Y-m-t', strtotime($yrmo[0] . '-' . $yrmo[1]. '-01'));
				$intdate = intval(date('d', strtotime($blldt)));
				if($column[3] > $intdate)
				{
					$billdate = $yrmo[0] . '-' . $yrmo[1] . '-' . $intdate;
					$billdate =date('Y-m-d', strtotime($billdate));
				} else {
					$billdate = $yrmo[0] . '-' . $yrmo[1] . '-' . $column[3];
					$billdate =date('Y-m-d', strtotime($billdate));
				}
				
				// Set amtodrawn and status if billing is not regular payment
				$status = NULL;
				if($this->input->post('bill_type') != '1')
				{
					$column[12] = 0;
					
					if($this->input->post('bill_type') == '2')
					{
						$status = 'adj';
					} elseif($this->input->post('bill_type') == '3') {
						$status = 'due';
					} elseif($this->input->post('bill_type') == '4') {
						$status = 'pmt';
						$column[12] = $this->input->post('bill_amt');
					} elseif($this->input->post('bill_type') == '5') {
						$status = 'rem';
					}
				}
				
				$header_array = array(
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
					'datecollected' => date('0000-00-00 00:00:00'),
					'status' 		=> $status
				);
				$this->db->insert('nhgt_bills.header', $header_array) or die($this->db->_error_message());
			}
		}
		
		die(trims('
			<script>
				window.opener.location.reload();
				window.close();
			</script>
		'));
		return;
	}
	
	function loan_date_form()
	{
		$data['ci_acctno'] = $this->uri->segment(3);
		$data['lh_pn'] = $this->uri->segment(4);
		$data['lh_loandate'] = $this->uri->segment(5);
		
		$this->load->view('master/form/loan_date', $data);
		return;
	}
	
	function update_loan_date()
	{
		$this->master_model->validate_loan_date_inputs();
		
		$system_message = '';
		if($this->form_validation->run() != FALSE)
		{
			$system_message = $this->master_model->update_loan_date();
		}
		
		die(trims('
			<script>
				alert(\'' . $system_message . '\');
				window.close();
				window.opener.close();
				window.opener.opener.location.getElementById(\'loan_list\').src = \'loan_list/' . $this->input->post('ci_acctno') . '\';
			</script>
		'));
		return;
	}
	
	function loan_duration_form()
	{
		$data['ci_acctno'] = $this->uri->segment(3);
		$data['lh_pn'] = $this->uri->segment(4);
		$data['lh_startdate'] = $this->uri->segment(5);
		$data['lh_enddate'] = $this->uri->segment(6);
		$data['lh_loandate'] = $this->uri->segment(7);
		
		$this->load->view('master/form/loan_duration', $data);
		return;
	}
	
	function update_loan_duration()
	{
		$this->master_model->validate_loan_duration_inputs();
		
		$system_message = '';
		if($this->form_validation->run() != FALSE)
		{
			$system_message = $this->master_model->update_loan_duration();
		}
		
		die(trims('
			<script>
				alert(\'' . $system_message . '\');
				window.opener.location.reload();
				window.close();
			</script>
		'));
		return;
	}
	
	function reset_agent_form()
	{
		$result = $this->master_model->fetch_branch_code();
		$result = explode(';', $result->value);
		$branch_code = $result[0];
		
		die(trims('
			$(\'#ai_branchcode\').val(\'' . $branch_code . '\');
		'));
		return;
	}
	
	function reset_client_form()
	{
		$result = $this->master_model->fetch_branch_code();
		$result = explode(';', $result->value);
		$branch_code = $result[0];
		
		die(trims('
			$(\'#ci_status\').val(\'A\');
			$(\'#ci_grp\').val(\'N\');
			$(\'#ci_type\').val(\'PEN\');
			$(\'#ci_branchcode\').val(\'' . $branch_code . '\');
		'));
		return;
	}
	
	function reset_comaker_form()
	{
		$result = $this->master_model->fetch_branch_code();
		$result = explode(';', $result->value);
		$branch_code = $result[0];
		
		die(trims('
			$(\'#cm_branchcode\').val(\'' . $branch_code . '\');
		'));
		return;
	}
	
	function update_loan_istop()
	{
		$data = explode(';', $this->input->post('data'));
		$is_active = $data[2];
		
		$value = 0;
		if($is_active == 0)
		{
			$value = 1;
		}
		
		$loan_array = array(
			'LH_IsTop' => $value
		);
		$this->db->where('CI_AcctNo', $data[0]);
		$this->db->where('LH_PN', $data[1]);
		$this->db->update('ln_hdr', $loan_array) or die($this->db->_error_message());
		
		die(trims('
			document.location.reload();
		'));
		return;
	}
	
	function process()
	{
		$data['ci_acctno'] = $this->uri->segment(3);
		$data['lh_pn'] = $this->uri->segment(4);
		
		$columns = 'CI_Name, LH_LoanAmt, LH_Principal, LH_Balance, LH_Refund, LH_LoanTrans, LH_Terms, LH_LoanDate, LH_StartDate, LH_EndDate';
		$data['results'] = $this->master_model->fetch_loan_details($columns, $data['ci_acctno'], $data['lh_pn']);
		
		$data['rates'] = $this->sales_model->fetch_parameter_value('rates');
		
		$this->load->view('master/process', $data);
		return;
	}
}

/* End of file account.php */
/* Location: ./system/application/controllers/account.php */