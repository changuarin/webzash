<!DOCTYPE html>
<html>
	<head>
		<title>Ledger Preview</title>
		
		<style type="text/css">
		
		body {
			font-size: 12px;
			font-family: 'Courier New';
			color: #000099;
			margin: 0;
		}
		b, th {
			font-weight: bold;
		}
		label {
			color: #990033;
		}
		
		.ledger-table {
			width: 740px;
			border: 1px solid black;
			border-collapse: collapse;
			background-color: #abe0ca;
		}
		.ledger-table th, .ledger-table td {
			padding: 2px 4px;
		}
		
		.margin-35 {
			margin-left: 28px;
		}
		.right-30 {
			margin-right: 30px;
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
		.line {
			border-bottom: 1px solid black;
		}
		.indent {
			text-indent: 25px;
		}
		.spec {
			text-decoration: underline;
			font-weight: bold;
		}
		.normal {
			font-weight: normal;
			border: 1px solid black;
		}
		.hide {
			display: none;
		}
		
		#menuTab {
			padding: 4px 6px;
		}
		
		</style>
		
		<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				
				$('#pn').change(function(){
					var id = $('#acctno').val();
					var pn = $(this).val();
					$('#lp', parent.document).attr('src', 'ledger/'+id+'/'+pn);
				});
				
			});
		</script>
		
	</head>
	<body>
	
		<?php
		
		if($datas)
		{
			foreach($datas as $d)
			{
				switch($d['LH_LoanTrans'])
				{
					case 'NEW':
						$loantrans = 'NEW CLIENT';
						break;
					case 'REN':
						$loantrans = 'RENEWAL';
						break;
					case 'ADD':
						$loantrans = 'ADDITIONAL';
						break;
					case 'EXT':
						$loantrans = 'EXTENSION';
						break;
				}
				echo '
				<div id="menuTab">
					<form id="ledger-form" method="post">
						<input class="hide" type="text" id="acctno" value="'.$d['CI_AcctNo'].'">
						<input class="hide" type="text" id="pnno" value="'.$d['LH_PN'].'">
						<select name="pn" id="pn">
					';
						foreach($pn as $pn)
						{
							echo '<option value="'.$pn['LH_PN'].'" '.($loanid==$pn['LH_PN']?'selected':'').'>'.$pn['LH_PN'].'</option>';
						}
					echo '	
						</select>
					</form>
				</div>
				<table class="ledger-table" style="border-bottom: 0;">
					<tbody>
						<tr>
							<td width="370"><label>NAME: </label>'.$d['CI_Name'].'</td>
							<td width="370"><label>BANK: </label>'.$d['LH_BankBranch'].'</td>
						</tr>
						<tr>
							<td><label>S/A: </label>'.$d['LH_BankAcctNo'].'</td>
							<td><label>PN#: </label>'.$d['LH_Reference'].'</td>
						</tr>
						<tr>
							<td>
								<label>AMT. OF PENSION: </label>'.
								number_format($d['LH_BankAmt'], 2).'&nbsp;&nbsp;&nbsp;&nbsp;
								<label>SPA: </label>'.number_format($d['LH_MonthlyAmort'], 2).'
							</td>
							<td><label>DATE: </label>'.$d['LH_LoanDate'].'</td>
						</tr>
						<tr>
							<td><label>CODE NO.: </label>'.$d['CI_AcctNo'].'</td>
							<td><label>DURATION: </label>'.$d['LH_StartDate'].' To '.$d['LH_EndDate'].'</td>
						</tr>
						<tr>
							<td><label>AGENT: </label>'.$d['LH_Agent1'].'</td>
							<td><label>O.B. CLOSED: </label>'.number_format($d['LH_OBC'], 2).'</td>
						</tr>
						<tr>
							<td><label>REMARKS: </label>'.$loantrans.'</td>
							<td><label>BRANCH: </label>'.$d['LH_BranchCode'].'</td>
						</tr>
					</tbody>
				</table>
				
				<table class="ledger-table">
					<tbody>
						<tr>
							<th class="normal" width="122"><label>PN AMOUNT</label></th>
							<th class="normal" width="122"><label>DUE DATE</label></th>
							<th class="normal" width="122"><label>MO. INSTALL</label></th>
							<th class="normal" width="122"><label>TERM</label></th>
							<th class="normal" width="122"><label>U.I.</label></th>
							<th class="normal" width="122"><label>CASH OUT</label></th>
						</tr>
						<tr>
							<th class="normal">'.number_format($d['LH_Principal'], 2).'</th>
							<th class="normal">&nbsp;</th>
							<th class="normal">'.number_format($d['LH_MonthlyAmort'], 2).'</th>
							<th class="normal">'.$d['LH_Terms'].'</th>
							<th class="normal">&nbsp;</th>
							<th class="normal">'.number_format($d['LH_NetProceeds'], 2).'</th>
						</tr>
					</tbody>
				</table>
				<table class="ledger-table">
					<tbody>
						<tr>
							<th class="normal" width="70"><label>DATE</label></th>
							<th class="normal" width="60"><label>INS. NO</label></th>
							<th class="normal" width="110"><label>OR/CM</label></th>
							<th class="normal" width="80"><label>REFUND</label></th>
							<th class="normal" width="100"><label>AMOUNT WITHDRAWN</label></th>
							<th class="normal" width="70"><label>INTEREST</label></th>
							<th class="normal" width="90"><label>OUTSTANDING BALANCE</label></th>
							<th class="normal" width="70"><label>FOR REFUND</label></th>
							<th class="normal" width="90"><label>ARREARS/REMARKS</label></th>
						</tr>
				';
				
				$ob = $d['LH_Principal'] + $d['LH_OBC'];
				foreach($ledger as $l)
				{
					$date = date('M-d-Y', strtotime($l['LL_PaymentDate']));
					$orno = ($l['LL_ORNo']==null?'':$l['LL_ORNo']);
					$crno = ($l['LL_CRNo']==null?'':$l['LL_CRNo']);
					$refund = ($l['LL_Remarks']==''?0:$l['LL_Refund']);
					$collection = ($l['LL_AmountCash']==0?0:$l['LL_AmountCash']);
					$payment = ($l['LL_AmountCash']==0&&$l['LL_AmountCash_Payment']>0?$l['LL_AmountCash_Payment']:0);
					$checkno = ($l['LL_AmountCash']==0&&$l['LL_AmountCash_Payment']>0?$l['RFW_NO']:$l['LL_CheckNo']);
					$ob = $ob + $refund - $collection - $payment;
					$re = $l['LL_Remarks'];
					echo '
						<tr>
							<th class="normal">'.$date.'</th>
							<th class="normal">'.($l['LL_IsPayment']==1?$l['ID']:'').'</th>
							
							<th class="normal">'.$orno.$crno.$checkno.'</th>
							<th class="normal">'.($refund==0?'':number_format($refund, 2)).'</th>
							<th class="normal">'.($collection==0?($payment==0?'':number_format($payment, 2)):number_format($collection, 2)).'</th>
							<th class="normal">&nbsp;</th>
							<th class="normal">'.number_format($ob, 2).'</th>
							<th class="normal">&nbsp;</th>
							<th class="normal">'.($l['LL_Remarks']!=''?$re:'').'</th>
						</tr>
					';
				}
				
				echo '
					</tbody>
				</table>
				';
			}
		}
		
		?>
		
	</body>
</html>