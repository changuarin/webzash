<!DOCTYPE html>
<html>
	<head>
		<title>Client Ledger</title>
		<style type="text/css">
		body {
			color: #000099;
			font-family: 'Courier New';
			font-size: 12px;
			margin: 0;
		}
		
		b, th {
			font-weight: bold;
		}
		
		label {
			color: #990033;
		}
		
		.ledgerTable {
			background-color: #abe0ca;
			border-collapse: collapse;
			margin-left: 4px;
		}
		
		.ledgerTable tr th, .ledgerTable tr td {
			border: 1px solid #000;
			padding: 2px 4px;
		}
		
		.ledgerHeaderTable {
			border-collapse: collapse;
			border-left:1px solid black;
			border-top: 1px solid black;
			border-right: 1px solid black;
			background-color: #abe0ca;
			margin-left: 4px;
		}
		
		.ledgerHeaderTable tr th, .ledgerHeaderTable tr td {
			padding: 2px 6px;
		}
		
		.tdCheckbox {
			background-color: #fff;
		}
		
		.ledgerPostEdit {
			cursor: pointer;
		}
		
		.ledgerPostEdit:hover {
			background-color: #ff0000;
		}
		
		.indent {
			text-indent: 25px;
		}
		
		.line {
			border-bottom: 1px solid #000;
		}
		
		.margin-35 {
			margin-left: 28px;
		}
		
		.normal {
			border: 1px solid #000;
			font-weight: normal;
		}
		
		.right-30 {
			margin-right: 30px;
		}
		
		.spec {
			font-weight: bold;
			text-decoration: underline;
		}
		
		.loan_date_form, .loan_duration_form {
			cursor: pointer;
		}
		
		.loan_date_form:hover, .loan_duration_form:hover {
			text-decoration: underline;
		}
		
		.center {
			text-align: center;
		}
		
		.left {
			text-align: left;
		}
		
		.right {
			text-align: right;
		}
		
		.hide {
			display: none;
		}
		
		#menuTab {
			padding: 4px 6px;
		}
		
		#rfp:hover {
			cursor: pointer;
			text-decoration: underline;
		}
		</style>
		
		<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery-2.1.3.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$('#addButton, #deleteButton, #cancelButton').attr('class', 'hide')
				$('.tagCheckbox').attr('disabled', true);
				
				$("#checkAll").click(function(){
					var is_checked = $(this).is(':checked');
					$('input[type=checkbox]').prop('checked', is_checked);
				});
				
				var acctno = $('#acctno').val();
				var pnno = $('#pnno').val();
				$('#pn').change(function(){
					var pn = $(this).val();
					document.location.href = '../../clientLedgerList/' + acctno + '/' + pn;
				});
				
				
				$('#editButton').click(function() {
					$('.edit_loan_duration').addClass('loan_duration_form');
					$('.edit_loan_date').addClass('loan_date_form');
					
					$('#editButton, #updateButton').attr('class', 'hide');
					$('#addButton, #deleteButton, #cancelButton').attr('class', '');
					$('.tagCheckbox').attr('disabled', false);
					$('.tdCheckbox').css('background-color', '#FF0000');
					var clss = $('.ledgerPost').attr('class');
				});
				
				$('#cancelButton').click(function() {
					$('.edit_loan_duration').removeClass('loan_duration_form');
					$('.edit_loan_date').removeClass('loan_date_form');
					
					$('#editButton, #updateButton').attr('class', '');
					$('#addButton, #deleteButton, #cancelButton').attr('class', 'hide');
					$('.tagCheckbox').attr('disabled', true).attr('checked', false);
					$('.tdCheckbox').css('background-color', '#FFF');
				});
				
				$('#addButton').click(function() {
					var data = $(this).attr('id');
					var data = data.split(';');
					
					var width = 560;
					var height = 440;
					var top = (screen.height/2) - (height/2);
					var left = (screen.width/2) - (width/2);
					window.open('../../clientLedgerForm/' + acctno + '/' + pnno + '/1/' + $('#LH_Balance').val() + '/' + $('#LH_Payment').val() + '/' + $('#LH_Refund').val(), 'clientLedgerForm', 'width=' + width + ', height=' + height + ', top=' + top + ', left=' + left + ', scrollbars=yes').focus();
				});
				$('#deleteButton').click(function() {
					if(confirm('Delete ledger post/s'))
					{
						$('#ledgerListForm').submit();
					}
				});
				
				$('.editLedgerPost').mouseover(function() {
					if($('#editButton').attr('class') == 'hide')
					{
						$(this).css('background-color', '#fff');
					}
				});
				
				$('.editLedgerPost').mouseout(function() {
					if($('#editButton').attr('class') == 'hide')
					{
						$(this).css('background-color', '#abe0ca');
						$(this).css('cursor', 'pointer');
					}
				});
				
				$('.editLedgerPost').click(function() {
					var data = $(this).attr('id');
					var data = data.split(';');
					if($('#editButton').attr('class') == 'hide')
					{
						var width = 560;
						var height = 440;
						var top = (screen.height/2) - (height/2);
						var left = (screen.width/2) - (width/2);
						window.open('../../clientLedgerForm/' + acctno + '/' + pnno + '/0/' + data[0] + '/' + data[1] + '/' + data[2] + '/' + data[3] + '/' + $('#LH_Balance').val() + '/' + $('#LH_Payment').val() + '/' + $('#LH_Refund').val(), 'clientLedgerForm', 'width=' + width + ', height=' + height + ', top=' + top + ', left=' + left + ', scrollbars=yes').focus();
					}
				});
				
				$('#updateButton').click(function() {
					document.location.href = '../../updateLn_HdrBalance/' + acctno + '/' + pnno + '/' + $('#LH_Balance').val() + '/' + $('#LH_Payment').val() + '/' + $('#LH_Refund').val();
				});
				
				$('#rfp').click(function() {
					window.open('../../../sales/refund', '_blank');
				});
				
				$('.edit_loan_date').click(function() {
					var class_prop = $(this).prop('class');
					var data = class_prop.split(' ');
					if(data[1] == 'loan_date_form')
					{
						height = 440; width = 560;
						var top = (screen.height/2) - (height/2);
						var left = (screen.width/2) - (width/2);
						window.open('../../loan_date_form/' + acctno + '/' + pnno + '/' + $('#lh_loandate').val(), 'loandateForm', 'width=' + width + ', height=' + height + ', top=' + top + ', left=' + left + ', scrollbars=yes').focus();
					}
				});
				
				$('.edit_loan_duration').click(function() {
					var class_prop = $(this).prop('class');
					var data = class_prop.split(' ');
					if(data[1] == 'loan_duration_form')
					{
						height = 440; width = 560;
						var top = (screen.height/2) - (height/2);
						var left = (screen.width/2) - (width/2);
						window.open('../../loan_duration_form/' + acctno + '/' + pnno + '/' + $('#lh_startdate').val() + '/' + $('#lh_enddate').val() + '/' + $('#lh_loandate').val(), 'loandurationForm', 'width=' + width + ', height=' + height + ', top=' + top + ', left=' + left + ', scrollbars=yes').focus();
					}
				});
			});
		</script>
		
	</head>
	<body>
		
		<form method="post" action="../../deleteLedgerPost" id="ledgerListForm">
		<?php
		
		if($datas)
		{
			foreach($datas as $d)
			{
				switch($d['LH_LoanTrans'])
				{
					case 'ADD':
						$loantrans = 'ADDITIONAL';
						break;
					case 'EXT':
						$loantrans = 'EXTENSION';
						break;
					case 'NEW':
						$loantrans = 'NEW CLIENT';
						break;
					case 'REN':
						$loantrans = 'RENEWAL';
						break;
					case 'RES':
						$loantrans = 'RESTURCTURE';
						break;
					case 'RET':
						$loantrans = 'RETURNING';
						break;
					case 'SPEC':
						$loantrans = 'ADVANCE BONUS';
						break;
				}
				
				echo '
				<div id="menuTab">
					<input class="hide" type="text" id="acctno"  name="acctno" value="' . $clientid . '">
					<input class="hide" type="text" id="pnno"  name="pnno" value="' . $loanid . '">
					<select id="pn" name="pn">
				';
					foreach($pn as $pn)
					{
						echo '<option value="' . $pn['LH_PN'] . '" ' . ($loanid == $pn['LH_PN'] ? 'selected' : '') . '>' . $pn['LH_PN'] . '</option>';
					}
				echo '
					</select>
					<input id="editButton" type="button" name="editButton" value="Edit">
					<input id="updateButton" type="button" name="updateButton" value="Update">
					<input id="addButton" type="button" name="addButton" value="Add">
					<input id="deleteButton" type="button" name="deleteButton" value="Delete">
					<input id="cancelButton" type="button" name="cancelButton" value="Cancel">
				</div>
				
				<table class="ledgerHeaderTable" style="width:810px;">
					<tbody>
						<tr>
							<td style="width:50%;">
								<label>NAME:&nbsp;</label>' .
								$d['CI_Name'] . '
							</td>
							<td style="width:50%;">
								<label>BANK:&nbsp;</label>' .
								$d['LH_BankBranch'] . '
							</td>
						</tr>
						<tr>
							<td>
								<label>S/A:&nbsp;</label>' .
								$d['LH_BankAcctNo'] . '
							</td>
							<td>
								<label>PN#:&nbsp;</label>' .
								$d['LH_Reference'] . '
							</td>
						</tr>
						<tr>
							<td>
								<label>AMT. OF PENSION: </label>' .
								number_format($d['LH_BankAmt'], 2) . '&nbsp;&nbsp;&nbsp;&nbsp;
								<label>SPA: </label>' . number_format($d['LH_MonthlyAmort'], 2) . '
							</td>
							<td>
								<label>DATE: </label>
								<span class=\'edit_loan_date\'>' . date('F d, Y', strtotime($d['LH_LoanDate'])) . '</span>
							</td>
						</tr>
						<tr>
							<td>
								<label>CODE NO.: </label>' .
								$d['CI_AcctNo'] . '
							</td>
							<td>
								<label>DURATION: </label>
								<span class=\'edit_loan_duration\'>' . date('F Y', strtotime($d['LH_StartDate'])) . ' To ' . date('F Y', strtotime($d['LH_EndDate'])) . '</span>
								<input id="lh_startdate" type="hidden" name="lh_startdate" value="' . $d['LH_StartDate'] . '" />
								<input id="lh_enddate" type="hidden" name="lh_enddate" value="' . $d['LH_EndDate'] . '" />
								<input id="lh_loandate" type="hidden" name="lh_loandate" value="' . $d['LH_LoanDate'] . '" />
							</td>
						</tr>
						<tr>
							<td>
								<label>AGENT: </label>' . $agent1name . '
							</td>
							<td>
								<label>O.B. CLOSED: </label>' . number_format($d['LH_OBC'], 2) . '
							</td>
						</tr>
						<tr>
							<td>
								<label>REMARKS: </label>' . $loantrans . '
							</td>
							<td>
								<label>BRANCH: </label>' . strtoupper($bname) . ' ' .
								($d['LH_PaymentTo'] > 0 ? 'PAYMENT TO: ' . $payto_ref . ';' . number_format($d['LH_PaymentTo'], 2) : '') . '
							</td>
						</tr>
					</tbody>
				</table>
				
				<table class="ledgerTable" style="width:810px;">
					<tbody>
						<tr>
							<th class="normal">
								<label>PN AMOUNT</label>
							</th>
							<th class="normal">
								<label>DUE DATE</label>
							</th>
							<th class="normal">
								<label>MO. INSTALL</label>
							</th>
							<th class="normal">
								<label>TERM</label>
							</th>
							<th class="normal">
								<label>U.I.</label>
							</th>
							<th class="normal">
								<label>CASH OUT</label>
							</th>
						</tr>
						<tr>
							<th class="normal">' .
								number_format($d['LH_Principal'], 2) . '
							</th>
							<th class="normal">
								&nbsp;
							</th>
							<th class="normal">' .
								number_format($d['LH_MonthlyAmort'], 2) . '
							</th>
							<th class="normal">' .
								$d['LH_Terms'] . '
							</th>
							<th class="normal">
								&nbsp;
							</th>
							<th class="normal">' .
								number_format($d['LH_NetProceeds'], 2) . '
							</th>
						</tr>
					</tbody>
				</table>
				
				<table class="ledgerTable">
					<tbody>
						<tr>
							<th class="normal" style="width:80px">
								<label>DATE</label>
							</th>
							<th class="normal" style="width:60px">
								<label>INS. NO</label>
							</th>
							<th class="normal" style="width:110px">
								<label>OR/CM</label>
							</th>
							<th class="normal" style="width:80px">
								<label>REFUND</label>
							</th>
							<th class="normal" style="width:80px">
								<label>AMOUNT WITHDRAWN</label>
							</th>
							<th class="normal" style="width:60px">
								<label>INTEREST</label>
							</th>
							<th class="normal" style="width:90px">
								<label>OUTSTANDING BALANCE</label>
							</th>
							<th class="normal" style="width:60px">
								<label>FOR REFUND</label>
							</th>
							<th class="normal" style="width:120px">
								<label>ARREARS/REMARKS</label>
							</th>
							<th class="tdCheckbox" style="background-color: #fff;width: 30px">
								<input type="checkbox" class="tagCheckbox" id="checkAll" name="checkAll">
							</th>
						</tr>
				';
				
				$adv_bonus = 0;
				$due_amount = 0;
				$for_refund = 0;
				$i = 0;
				$ob = $d['LH_Principal'];
				$total_collection = 0;
				$total_monthly_amort = 0;
				$total_refund = 0;
				$post_count = $post_count - 1;
				
				foreach($ledger as $l):
				
					$paymentdate = date('M-d-Y', strtotime($l['LL_PaymentDate']));
					
					$orcm = '';
					if($l['LL_IsPayment'] == 1):
						if($l['RFW_NO'] == ''):
							$orcm = $l['LL_ORNo'] . $l['LL_CRNo'];
						else:
							$orcm = $l['RFW_NO'];
						endif;
					endif;
					
					if($l['LL_IsRefund'] == '1'):
						$orcm = $l['LL_Remarks'];
					endif;
									
					$refund = $l['LL_Refund'] == 0 ? 0 : $l['LL_Refund'];
					$collection = $l['LL_AmountCash_Payment'] == 0 ? 0 : $l['LL_AmountCash_Payment'];
					$monthly_amort = $l['LL_AmountCash_Payment'] == 0 ? 0 : $d['LH_MonthlyAmort'];;
					
					$ob -= $collection;
					$ob += $refund;
					
					$total_collection += $collection;
					$total_refund += $refund;
					
					if($l['LL_IsRefund'] == 1 && strpos($l['LL_Remarks'], 'ADVANCE BONUS') !== FALSE):
						$adv_bonus += $refund;
						$for_refund = $for_refund;
					elseif($l['LL_IsPayment'] == 1 && ($l['LL_Remarks'] == 'ADJUSTMENT' || $l['LL_Remarks'] == 'DUE TO CLIENT' || $l['LL_Remarks'] == 'REMITTANCE')):
						$for_refund += $collection;
					elseif($l['LL_IsPayment'] == 1 && ($l['LL_Remarks'] == '13TH MONTH' || $l['LL_Remarks'] == 'BONUS')):
						$adv_bonus -= $collection;
						if($adv_bonus < 0):
							$for_refund += $collection;
						endif;
					elseif(($l['LL_IsPayment'] == 1 && $l['LL_Remarks'] == 'RENEWAL') || ($l['LL_IsPayment'] == 1 && strpos($l['LL_Remarks'], 'PAYMENT') !== FALSE)):
						$for_refund = $for_refund;
					else:
						$for_refund += $collection - ($monthly_amort + $refund);
					endif;
					
					echo '
						<tr class="ledgerPost">
							<th class="normal">' . $paymentdate . '</th>
							<th class="normal">' . ($l['LL_IsPayment'] == 1 && $l['ID'] != 0 ? $l['ID'] : '') . '</th>
							<th class="editLedgerPost normal" id="' . $l['ID'] . ';' . $l['LL_AmountCash_Payment'] . ';' . $l['LL_Refund'] . ';' . $l['LL_PaymentDate'] . '">' . ($l['LL_IsPayment'] == 1 && strpos($l['LL_Remarks'], 'RENEWAL') !== FALSE ? $l['LL_Remarks'] : $orcm) . '</th>
							<th class="normal">' . ($refund == 0 ? '' : number_format($refund, 2)) . '</th>
							<th class="normal">' . ($collection == 0 ? '' : number_format($collection, 2)) . '</th>
							<th class="normal">&nbsp;</th>
							<th class="normal">' . ($ob == '' ? '0.00' : number_format($ob, 2)) . '</th>
							
							<th class="normal">' .
							($i == $post_count ? '<span id="rfp">' . number_format($for_refund, 2) . '</span>' : '&nbsp;'  )
							. '</th>
							
							<th class="normal">' . ($l['LL_IsRefund'] == 1 ? $l['RFW_NO'] : ($l['LL_IsPayment'] == 1 && $l['LL_Remarks'] != '' ? (strpos($l['LL_Remarks'], 'RENEWAL') !== false ? $l['RFW_NO'] : $l['LL_Remarks']) : '&nbsp;')) . '</th>
							<th class="tdCheckbox">
								<input class="tagCheckbox" type="checkbox" name="ledgerPost' . $i . '" value="' . $l['ID'] . ';' . $l['LL_AmountCash_Payment'] . ';' . $l['LL_Refund'] . ';' . $l['LL_PaymentDate'] . '">
							</th>
						</tr>
					';
					$i++;
				endforeach;
				
				echo '
					</tbody>
				</table>
				';
			}
			
			$lh_payment = $total_collection - $total_refund;
			echo '
				<input id="postCount" type="hidden" name="postCount" value="' . $i . '">
				<input id="LH_Balance" type="hidden" name="LH_Balance" value="' . $ob . '">
				<input id="LH_Payment" type="hidden" name="LH_Payment" value="' . $lh_payment . '">
				<input id="LH_Refund" type="hidden" name="LH_Refund" value="' . $for_refund . '">
			';
		}
		
		?>
		</form>
	</body>
</html>