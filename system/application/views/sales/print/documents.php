<!DOCTYPE html>
<html>
	<head>
		<title>Documents</title>
		<style type="text/css">
		
		body {margin:0;border:none;}
		b, th {font-weight: bold;}
		small {font-size:10px;}
		table {font-family:'Arial';font-size:12px;}
		table tr th, table tr td {border:0 solid #000;}

		.iLedgerTable {font-family:'Arial';font-size:13px;width:100%;}
		.iLedgerTable tr th {text-align:left;}
		.iLedgerTable tr th, .iLedgerTable tr td {border:0 solid #000;height:16px;}
		
		.salesDocsTable {font-size:12px;font-family:'Arial';width:100%;}
		.salesDocsTable tr th, .salesDocsTable tr td {height:16px;vertical-align:middle;}
		
		.center {text-align:center;}
		.hide {display:none;}
		.left {text-align:left;}
		.nw {white-space:nowrap;}
		.pageBreak {page-break-after:always;}
		.pl15 {padding-left:15px;}
		.pl50 {padding-left:50px;}
		.pr10 {padding-right:10px;}
		.pr50 {padding-right:50px;}
		.right {text-align:right;}
		.ul {text-decoration:underline;}
		
		.bt {border-top:1px solid #000;}
		.br {border-right:1px solid #000;}
		.bb {border-bottom:1px solid #000;}
		.bb2 {border-bottom:2px solid #000;}
		.bl {border-left:1px solid #000;}
		.pt {padding:4px 4px 0 4px;}
		.pb {padding:0 4px 4px 4px;}
		
		#menuTab {background-color:#0099CC;width:100%;}
		#iLNetProceeds {text-align:right;}
		#dcTable {font-size:12px;font-family:'Arial';width:100%;}
		#dcTable tr th, #dcTable tr td {height:14px;vertical-align:middle;}
		#selectTable {background-color:#FFF;border:1px solid #0099CC;position:fixed;}
		
		</style>
		<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script>
		<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/format.js"></script>
		<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/functions.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
			
				var a = window.opener.$('#rpd-btn').attr('id');
				var b = window.opener.$('#reprintButton').attr('id');
				if(a=='rpd-btn'||b=='reprintButton')
				{
					$('#processButton').css('display', 'none').attr('disabled', true);
				}
				
				$('#checkBox1').attr('checked', true);
				$('#selectPrint').click(function(){
					var a = $('#selectTable').attr('class');
					if(a=='hide')
					{
						$('#selectTable').attr('class', '');
						$('#printButton').attr('class', 'hide');
					} else {
						$('#selectTable').attr('class', 'hide');
						$('#printButton').attr('class', '');
						for(i=1;i<=5;i++)
						{
							var a = $('#checkBox'+i).attr('checked');
							if(a==1)
							{
								$('#table'+i).attr('class', '');
							} else {
								$('#table'+i).attr('class', 'hide');
							}
						}
					}
				});
				$('#printButton').click(function(){
					$('#menuTab').css('display', 'none');
					window.print();
					$('#menuTab').css('display', '');
				});
				
				$('#processButton').click(function(){
					var a = confirm("Process loan?");
					if (a == true) {
						window.opener.$('#loanProcForm').submit();
						window.close();
					} else {
						return false;
					} 
				})
				
				$('#selectDoc').change(function(){
					$('.docs').css('display', 'none');
					var a = $(this).val();
					$('#'+a).css('display', '')
				});
				
				var a = toWords(N($('#principal').html()));
				$('.principaltowords').html(a);
				
				var a = toWords(N($('#monthlyamort').html()));
				$('.matowords').html(a);
				
				var a = toWords(N($('#minus1terms').html()));
				var a = a.replace(' PESOS ONLY', '');
				$('.m1towords').html(a);
				
				var a = toWords(N($('#principal').html()));
				var a = a.replace(' PESOS ONLY', '');
				$('#sap').html(a);
				
			});
		</script>
	</head>
	<body>
	<?php
	
	$i = 0;
	foreach($atty as $a)
	{
		$b = explode(';', $a['value']);
		$atty[$i] = $b[1];
		$i++;
	}
		
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
				$loantrans = 'RESTRUCTURE';
				break;
			case 'RET':
				$loantrans = 'RETURNING';
				break;
			case 'SPEC':
				$loantrans = 'ADVANCE BONUS';
				break;
		}
		
		$StartDate = strtotime($d['LH_StartDate']);
		$sidate = date('F Y', strtotime('+1 month', $StartDate));
		
		$tofinancecharges = $d['LH_InterestAmt'] + $d['LH_ProcFee'] + $d['LH_CollFee'];
		
		$age = intval(substr(date('Ymd') - date('Ymd', strtotime($bdate)), 0, -4));
		
		$penalty = $rate;
		if($branch_code == 'SPB')
		{
			$penalty = 3;
		}
		
		echo '
		<div id="menuTab">
			<input type="button" value="Select" id="selectPrint">
			<input type="button" value="Print" id="printButton">
			<input type="button" value="Process" id="processButton">
		</div><!-- Installment Ledger -->
		<table id="selectTable" class="hide">
			<tbody>
				<tr>
					<td><input id="checkBox1" type="checkbox"></td>
					<td>INSTALLMENT LEDGER</td>
				</tr>
				<tr>
					<td><input id="checkBox2" type="checkbox"></td>
					<td>PROMISORY NOTE</td>
				</tr>
				<tr>
					<td><input id="checkBox3" type="checkbox"></td>
					<td>DISCLOSURE</td>
				</tr>
				<tr>
					<td><input id="checkBox4" type="checkbox"></td>
					<td>SPECIAL POWER OF ATTORNEY</td>
				</tr>
				<tr>
					<td><input id="checkBox5" type="checkbox"></td>
					<td>SURETY AGREEMENT</td>
				</tr>
			</tbody>
		</table>
		<div id="table1">
			<table class="iLedgerTable" cellpadding="0" cellspacing="0">
				<tbody>
					<tr>
						<td colspan="3" style="height: ' . $x[0] . ';">&nbsp;</td>
					</tr>
					<tr>
						<th style="padding-left:200px;width:50%;" colspan="2">' . $d['CI_Name'] . '</th>
						<th style="padding-left: ' . $y[0] . ';width:50%;">' . $d['LH_BankBranch'] . '</th>
					</tr>
					<tr>
						<th style="padding-left:180px;" colspan="2">' . $d['LH_BankAcctNo'] . '</th>
						<th style="padding-left: ' . $y[1] . ';">' . $d['LH_Reference'] . '</th>
					</tr>
					<tr>
						<th style="padding-left:' . $y[8] . ';">' . number_format($d['LH_BankAmt'], 2) . '</th>
						<th style="padding-left: ' . $y[2] . ';">' . number_format($d['LH_MonthlyAmort'], 2) . '</th>
						<th style="padding-left: ' . $y[3] . ';">' . date('F d, Y', strtotime($d['LH_LoanDate'])) . '</th>
					</tr>
					<tr>
						<th style="padding-left:220px;" colspan="2">' . $d['CI_AcctNo'] . '</th>
						<th style="padding-left: ' . $y[4] . ';">' . date('M Y', strtotime($d['LH_StartDate'])) . ' - ' . date('M Y', strtotime($d['LH_EndDate'])) . '</th>
					</tr>
					<tr>
						<th style="padding-left:200px;" colspan="2">' . $agent1name . '</th>
						<th style="padding-left: ' . $y[5] . ';">' . number_format($d['LH_OBC'], 2) . '</th>
					</tr>
					<tr>
						<th style="padding-left:220px;" colspan="2">' . $loantrans . '</th>
						<th style="padding-left:' . ($d['LH_PaymentTo'] > 0 ? '100px' : '300px') . ';">' . strtoupper($bname) . '&nbsp;' . ($d['LH_PaymentTo'] > 0 ? 'PAYMENT TO: ' . $payto . ';' . number_format($d['LH_PaymentTo'],2) : '') . '</th>
					</tr>
				</tbody>
			</table>
			<table class="iLedgerTable">
				<tbody>
					<tr>
						<th colspan="6">&nbsp;</th>
					</tr>
					<tr>
						<th style="height: ' . $x[1] . ';padding-left:' . $y[9] . ';width:16.6%;">' . number_format($d['LH_Principal'], 2) . '</th>
						<th style="padding-left:0;width:16.6%;">&nbsp;</th>
						<th style="padding-left:70px;width:16.6%;">' . number_format($d['LH_MonthlyAmort'], 2) . '</th>
						<th style="padding-left: ' . $y[6] . ';width:16.6%;">' . $d['LH_Terms'] . '</th>
						<th style="width:16.6%;">&nbsp;</th>
						<th class="right" style="padding-right: ' . $y[7] . ';width:16.6%;"id="iLNetProceeds">' . number_format($d['LH_NetProceeds'], 2) . '</th>
					</tr>
				</tbody>
			</table>
		</div><!-- End Table1 -->
		<div id="table2" class="hide">
			<table class="salesDocsTable" cellpadding="0" cellspacing="0">
				<tbody>
					<tr>
						<th>'.strtoupper($companyname).'</th>
					</tr>
					<tr>
						<th>'.$companyaddress.'</th>
					</tr>
					<tr>
						<th>Tel. No.:'.$telno.' Fax: '.$faxno.'</th>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th style="text-decoration:underline;">PROMISORY NOTE</th>
					</tr>
					<tr>
						<th>
							<table style="margin:0 auto;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td class="bb" style="width:1%;">P</td>
										<th class="bb" id="principal" style="width:15%;">'.number_format($d['LH_Principal'], 2).'</th>
										<th style="width:80%;"></th>
									</tr>
								</tbody>
							</table>
						</th>
					</tr>
					<tr>
						<td>FOR THE VALUE RECEIVED I/WE JOINTLY AND SEVERALLY PROMISE TO PAY TO THE ORDER OF</td>
					</tr>
					<tr>
						<td class="bb pl50">'.strtoupper($companyname).'</td>
					</tr>
					<tr>
						<td>
							<table style="width:100%;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td class="nw" style="width:1%;">THE SUM</td>
										<td class="bb pl50 principaltowords"></td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td class="bb nw" style="width:1%;">(P</td>
										<td class="bb center" style="width:15%;">'.number_format($d['LH_Principal'], 2).'</td>
										<td class="bb">)</td>
										<td>&nbsp;PESOS, PHILIPPINE CURRENCY.</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>
							<table cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td class="nw" style="width:1%;">IN</td>
										<td class="bb center" style="width:10%;">'.$d['LH_Terms'].'</td>
										<td>&nbsp;month/s EQUAL INSTALLMENT AS FOLLOWS:</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>
							<table cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td class="nw" style="width:20%;">1ST INSTALLMENT DUE ON</td>
										<td class="bb pl50" style="width:80%;">'.date('F Y', strtotime($d['LH_StartDate'])).'</td>
										<td class="nw">&nbsp;IN THE</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table style="width:100%;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td class="nw" style="width:1%;">AMOUNT OF&nbsp;</td>
										<td class="bb pl50 matowords"></td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table style="width:100%;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td class="bb nw" style="width:1%;">(P</td>
										<td class="bb pl50" id="monthlyamort">'.number_format($d['LH_MonthlyAmort'], 2).'</td>
										<td class="bb nw">)</td>
										<td class="nw" style="width:1%;">&nbsp;AND THE BALANCE IN</td>
										<td class="bb pl50 m1towords"></td>
										<td class="bb" nw>(</td>
										<td class="bb center" id="minus1terms">'.($d['LH_Terms'] - 1).'</td>
										<td class="bb">)</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table style="width:100%;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td class="nw" style="width:1%;">EQUAL SUCCESSIVE MONTHLY INSTALLMENT(S) OF</td>
										<td class="bb">&nbsp;</td>
										<td class="bb nw">(</td>
										<td class="bb center">'.number_format($d['LH_MonthlyAmort'], 2).'</td>
										<td class="bb nw right">)</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
						<td>
							<table style="width:100%;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td class="nw" style="width:1%;">2ND INSTALLMENT ON&nbsp;</td>
										<td class="bb pl50">'.$sidate.'</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>THEREAFTER WITH LIQUIDATED DAMAGES ON EACH UNPAID INSTALLMENT(S) FORM DUE DATE THEREOF</td>
					</tr>
					<tr>
						<td>UNTIL PAID AT THE RATE OF ' . $penalty . '% PER MONTH</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>
							<table style="font-family:Arial;font-size:11px;margin:0 auto;width:88%;height:17px;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Time is declared of the essence hereof and upon default of payment of any installment when due all the other installment shall become due and payable together with all interest that may have accrued, and if this note is placed in the hands of an Attorney for collection, the maker and endorsers shall in addition, pay 25% of the amount due as attorneys fees, which sum shall in all cases not be less than P 200.00 besides the cost and expenses of litigation. and any legal action arising out of this note maybe instituted in the proper courts of Q.C. and in case of judicial execution the rights conferred by rule, 39, section 13 of the Rule of Civil Procedures as amended 1997.</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>
							<table style="font-family:Arial;font-size:11px;margin:0 auto;width:88%;height:17px;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;holder may accept partial payment reserving his rights of recourse againts each and all endorsers who hereby waive DEMAND, PRESENTMENT and NOTICE.</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>
							<table style="width:100%;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td class="nw" style="width:1%;">Furthermore, I hereby agree to entrust my&nbsp;</td>
										<td class="bb pl50">'.$d['LH_BankBranch'].'</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table style="width:100%;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td class="nw" style="width:1%;">savings passbook Account No.&nbsp;</td>
										<td class="bb pl50">'.$d['LH_BankAcctNo'].'</td>
										<td>to '.strtoupper($companyname).' to facilitate the payment of my</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>outstanding loan. The Companys duly authorized officer shall withdraw from my savings account the</td>
					</tr>
					<tr>
						<td>
							<table style="width:100%;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td class="nw" style="width:1%;">amount of PESOS</td>
										<td class="bb nw pl50 principaltowords"></td>
										<td class="bb">(P</td>
										<td class="bb right pr50">'.number_format($d['LH_Principal'], 2).'</td>
										<td class="bb">)</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>broken down as follow</td>
					</tr>
					<tr>
						<td>
							<table style="width:100%;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td style="width:1%;">PESO</td>
										<td class="bb pl50 matowords"></td>
										<td class="bb">(P</td>
										<td class="bb right pr50">'.number_format($d['LH_MonthlyAmort'], 2).'</td>
										<td class="bb">)</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table style="width:100%;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td class="nw" style="width:1%;">as payment for my monthly installment and PESOS</td>
										<td class="bb"></td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table style="width:100%;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td>(</td>
										<td style="width:150px;"></td>
										<td>)</td>
										<td class="nw">&nbsp;to be refunded to me monthly, beginning&nbsp;</td>
										<td class="bb center nw" style="width:15%;">'.date('F Y', strtotime($d['LH_StartDate'])).'</td>
										<td class="nw">for a period of</td>
										<td class="bb center nw" style="width:10%;">'.$d['LH_Terms'].'</td>
										<td class="nw" style="width:20%;"></td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>months until my loan is fully paid.</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>IT IS UNDERSTOOD THAT MY SAVINGS PASSBOOK SHALL BE RETURNED ONLY UPON FULL SETTLEMENT OF MY OBLIGATION.</td>
					</tr>
					<tr>
						<td>
							<table style="width:100%;" cellpadding="0" cellspacing="0">
								<tr>
									<tbody>
										<td class="bb pl50" style="width:250px;">'.$companyaddress.'</td>
										<td style="width:1%;">&nbsp;Philippines&nbsp;</td>
										<td class="bb pl50" style="width:200px;">'.date('F t, Y', strtotime($d['LH_EndDate'])).'</td>
										<td>&nbsp;</td>
									</tbody>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>
							<table style="width:100%;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td class="bb" style="width:330px;">Co-Maker&nbsp;:&nbsp;&nbsp;'.(isset($cmname)?$cmname:'&nbsp;').'</td>
										<td style="width:90px">&nbsp;</td>
										<td class="bb" style="width:330px;">Borrower&nbsp;:&nbsp;&nbsp;'.$d['CI_Name'].'</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table style="width:100%;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td style="width:10%;">Address</td>
										<td class="bb" style="width:35%;">'.(isset($cmadd1)?$cmadd1:'&nbsp;').'</td>
										<td style="width:10%;">&nbsp;</td>
										<td style="width:10%;">Address</td>
										<td style="width:35%;">'.$d['LH_Address'].'</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>
							<table style="width:100%;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td style="width:10%;">Res. Cert. No.</td>
										<td class="bb" style="width:35%;">'.(isset($cmcedulano)?$cmcedulano:'&nbsp;').'</td>
										<td style="width:10%;">&nbsp;</td>
										<td style="width:10%;">Res. Cert. No.</td>
										<td class="bb" style="width:35%;">'.$d['LH_CedulaNo'].'</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table style="width:100%;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td style="width:10%;">Issued at</td>
										<td class="bb" style="width:35%;">'.(isset($cmcedulaplace)?$cmcedulaplace:'&nbsp;').'</td>
										<td style="width:10%;">&nbsp;</td>
										<td style="width:10%;">Issued at</td>
										<td class="bb" style="width:35%;">'.$d['LH_CedulaPlace'].'</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table style="width:100%;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td style="width:10%;">Issued on</td>
										<td class="bb" style="width:35%;">'.(isset($cmceduladate)?date('F d, Y', strtotime($cmceduladate)):'&nbsp;').'</td>
										<td style="width:10%;">&nbsp;</td>
										<td style="width:10%;">Issued on</td>
										<td class="bb" style="width:35%;">'.date('F d, Y', strtotime($d['LH_CedulaDate'])).'</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th>SIGNED IN THE PRESENCE OF</th>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>
							<table style="width:100%;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td class="bb" style="width:330px;">&nbsp;</td>
										<td style="width:90px">&nbsp;</td>
										<td class="bb" style="width:330px;">&nbsp;</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table style="width:100%;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td style="width:10px;">Address</td>
										<td class="bb" style="width:280px;">&nbsp;</td>
										<td style="width:90px;">&nbsp;</td>
										<td style="width:50px;">Address</td>
										<td class="bb" style="width:280px;">&nbsp;</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<br>
			<br class="pageBreak">
		</div><!-- End Table2 -->
		<div id="table3" class="hide">
			<table id="dcTable" cellpadding="0" cellspacing="0">
				<tbody>
					<tr>
						<th colspan="2">'.strtoupper($companyname).'</th>
					</tr>
					<tr>
						<th colspan="2">'.$companyaddress.'</th>
					</tr>
					<tr>
						<th colspan="2">Tel. No.:'.$telno.' Fax: '.$faxno.'</th>
					</tr>
					<tr>
						<th colspan="2">(Business Names of Creditor)</th>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<th colspan="2">DISCLOSURE STATEMENT OF LOAN CREDIT TRANSACTION</th>
					</tr>				
					<tr>
						<th colspan="2">(SINGLE PAYMENT OR INSTALLMENT  PLAN)</th>
					</tr>
					<tr>
						<th colspan="2">As required Under R.A. 3766, Truth in Lending Act</th>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td>
							<table style="width:100%;" cellpadding="0" cellspacing="0">
								<tr>
									<th class="nw" style="width:1%;">Name of Borrower</th>
									<td class="bb pl15" style="width:44%;">'.$d['CI_Name'].'</td>
									<td style="width:6%;">&nbsp;</td>
								</tr>
								<tr>
									<th style="width:20px;">Address</th>
									<td class="bb pl15">'.$d['LH_Address'].'</td>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<th class="left" colspan="2">1. Cash/Purchase Price or Net Proceeds of Loan</th>
								</tr>
								<tr>
									<td class="pl50" colspan="2">(Item Purchased)</td>
								</tr>
								<tr>
									<th class="left" colspan="2">1. Cash/Purchase Price or Net Proceeds of Loan</th>
								</tr>
								<tr>
									<th class="left" colspan="2">2. Less: Downpayment  and/or Trade-in value (Not applicable of loan transaction)</th>
								</tr>
								<tr>
									<th class="left" colspan="2">3. Unpaid Balance of Cash/Purchases  Price or Net Proceeds of Loan</th>
								</tr>
								<tr>
									<th class="left" colspan="2">4. Non-Finance Charges (advanced by Seller/Creditor)</th>
								</tr>
							</table>
							<table style="width:68%;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td class="nw" style="width:30%;">'.str_repeat('&nbsp;', 10).'a. Insurance Premium</td>
										<td class="nw right pr10" style="width:25%;">'.str_repeat('&nbsp', 40).'</td>
										<td class="nw right ul" style="width:30%;">'.str_repeat('&nbsp', 65).'</td>
									</tr>
									<tr>
										<td class="nw" style="width:30%;">'.str_repeat('&nbsp;', 10).'b. Taxes</td>
										<td class="nw right pr10" style="width:25%;">'.str_repeat('&nbsp', 40).'</td>
										<td class="nw right ul" style="width:30%;">'.str_repeat('&nbsp', 65).'</td>
									</tr>
									<tr>
										<td class="nw" style="width:30%;">'.str_repeat('&nbsp;', 10).'c. Registration Fee</td>
										<td class="nw right pr10" style="width:25%;">'.str_repeat('&nbsp', 40).'</td>
										<td class="nw right ul" style="width:30%;">'.str_repeat('&nbsp', 65).'</td>
									</tr>
									<tr>
										<td class="nw" style="width:30%;">'.str_repeat('&nbsp;', 10).'d. Documentary/Science Stamps</td>
										<td class="nw right pr10" style="width:25%;">'.str_repeat('&nbsp', 40).'</td>
										<td class="nw right ul" style="width:30%;">'.str_repeat('&nbsp', 65).'</td>
									</tr>
									<tr>
										<td class="nw" style="width:30%;">'.str_repeat('&nbsp;', 10).'d. Documentary/Science Stamps</td>
										<td class="nw right pr10" style="width:25%;">'.str_repeat('&nbsp', 40).'</td>
										<td class="nw right ul" style="width:30%;">'.str_repeat('&nbsp', 65).'</td>
									</tr>
									<tr>
										<td class="nw" style="width:30%;">'.str_repeat('&nbsp;', 10).'e. Notarial Fees</td>
										<td class="nw right pr10" style="width:25%;">'.str_repeat('&nbsp', 40).'</td>
										<td class="nw right ul" style="width:30%;">'.str_repeat('&nbsp', 65).'</td>
									</tr>
									<tr>
										<td class="nw" style="width:30%;">'.str_repeat('&nbsp;', 10).'f. Others</td>
										<td class="nw right pr10 ul" style="width:25%;">'.str_repeat('&nbsp', 40).'</td>
										<td class="nw right ul" style="width:30%;">'.str_repeat('&nbsp', 65).'</td>
									</tr>
									<tr>
										<td class="nw" style="width:30%;">'.str_repeat('&nbsp;', 10).'g. OC</td>
										<td class="nw right pr10 ul" style="width:25%;">'.str_repeat('&nbsp', 40).'</td>
										<td class="nw right ul" style="width:30%;">'.str_repeat('&nbsp', 65).'</td>
									</tr>
									<tr>
										<td class="nw" style="width:30%;">&nbsp;</td>
										<td class="nw right pr10 ul" style="width:25%;"></td>
										<th class="nw left" style="width:30%;">Total Non-Finance Charge</th>
									</tr>
									<tr>
										<th class="left" colspan="3">5. Amount to be finance (item 3....4)</th>
									</tr>
									<tr>
										<th class="left" colspan="3">6. Finance Charges</th>
									</tr>
									<tr>
										<td colspan="3">
											<table style="width:100%;" cellpadding="0" cellspacing="0">
												<tbody>
													<tr>
														<td class="nw" style="width:90px;">a. Interest</td>
														<td class="bb" style="width:70px;"></td>
														<td class="nw">&nbsp;P.A. from</td>
														<td class="bb center" style="width:100px;">'.date('F Y', strtotime($d['LH_StartDate'])).'</td>
														<td class="nw">&nbsp;to&nbsp;</td>
														<td class="bb center" style="width:100px;">'.date('F Y', strtotime($d['LH_EndDate'])).'</td>
														<td class="nw">&nbsp;P&nbsp;</td>
														<td class="bb right" style="padding-right:9px;">'.number_format($d['LH_InterestAmt'], 2).'</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
									<tr>
										<td colspan="3" style="width:50px;">
											<table style="width:100%;" cellpadding="0" cellspacing="0">
												<tbody>
													<tr>
														<td style="padding:1px;">
															<table cellpadding="0" cellspacing="0" style="margin:0 auto;">
																<tbody>
																	<tr>
																		<td class="bt br bl pt nw left">(  ) Single</td>
																	</tr>
																	<tr>
																		<td class="br bb bl pb nw">(  ) Compound</td>
																	</tr>
																</tbody>
															</table>
														</td>
														<td class="center" style="padding:1px;">
															<table class="center" cellpadding="0" cellspacing="0" style="margin:0 auto;">
																<tbody>
																	<tr>
																		<td class="bt bl pt left nw" style="width:90px;">(  ) monthly</td>
																		<td class="bt br pt left nw">(  ) semi-annual</td>
																	</tr>
																	<tr>
																		<td class="bb bl pb left nw">(  ) quarterly</td>
																		<td class="br bb pb left nw">(  ) annual</td>
																	</tr>
																</tbody>
															</table>
														</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
									<tr>
										<td colspan="3">
											<table style="width:100%;" cellpadding="0" cellspacing="0">
												<tbody>
													<tr>
														<td>'.str_repeat('&nbsp;', 10).'b. Discount</td>
														<td class="bb" style="width:160px;padding-right:9px;"></td>
													</tr>
													<tr>
														<td>'.str_repeat('&nbsp;', 10).'c. Service/Handling Charge</td>
														<td class="bb right" style="width:160px;padding-right:9px;">'.number_format($d['LH_ProcFee'], 2).'</td>
													</tr>
													<tr>
														<td>'.str_repeat('&nbsp;', 10).'d. Collection Charge</td>
														<td class="bb right" style="width:160px;padding-right:9px;">'.number_format($d['LH_CollFee'], 2).'</td>
													</tr>
													<tr>
														<td>'.str_repeat('&nbsp;', 10).'e. Credit Investigation Fee</td>
														<td class="bb right" style="width:160px;padding-right:9px;"></td>
													</tr>
													<tr>
														<td>'.str_repeat('&nbsp;', 10).'f. Attorney Legal Fee</td>
														<td class="bb right" style="width:160px;padding-right:9px;"></td>
													</tr>
													<tr>
														<td>'.str_repeat('&nbsp;', 10).'g. Other charges incidental to the extension of credit (specify)</td>
														<td class="bb2 right" style="width:160px;padding-right:9px;"></td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
									<tr>
										<th class="right" style="height:26px;padding-right:100px;" colspan="3">Total Finance Charges</th>
									</tr>
									<tr>
										<th class="left" colspan="3">7. Percentage of Finance Charges to Total Amount Financed</th>
									</tr>
									<tr>
										<td class="left" colspan="3">'.str_repeat('&nbsp;', 10).'Computed in accordance with sec. 2(1) of CB Circular (58)</td>
									</tr>
									<tr>
										<th class="left" colspan="3">8. Effective interest Rate (method computation attached)</th>
									</tr>
									<tr>
										<th class="left" colspan="3">9. Payment</th>
									</tr>
									<tr>
										<td colspan="3">
											<table style="width:68%;" cellpadding="0" cellspacing="0">
												<tbody>
													<tr>
														<td class="nw" style="width:50%;">'.str_repeat('&nbsp;', 10).'a. Single Payment due</td>
														<td class="bb nw" style="width:50%;"></td>
													</tr>
													<tr>
														<td class="nw" style="width:50%;">'.str_repeat('&nbsp;', 10).'b. Total Installment Payments</td>
														<th class="nw" style="width:50%;">Date</th>
													</tr>
												</tbody>
											</table>
											<table style="width:68%;" cellpadding="0" cellspacing="0">
												<tbody>
													<tr>
														<td class="nw pl15" style="width:25%;">'.str_repeat('&nbsp;', 10).'Payable</td>
														<th class="bb nw" style="width:25%;">'.$d['LH_Terms'].'</th>
														<td class="nw" style="width:15%;">month/s</td>
														<th class="bb nw" style="width:35%;padding-right:9px;">'.number_format($d['LH_MonthlyAmort'], 2).'</th>
													</tr>
													<tr>
														<td class="nw">&nbsp;</td>
														<td class="center nw">(No. of Payments)</td>
														<td>&nbsp;</td>
														<td>&nbsp;</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
									<tr>
										<th class="left nw" colspan="3">10. Additional charges in case certain stipulation in the contract are not met by debtor</th>
									</tr>
									<tr>
										<td colspan="3">
											<table style="width:95%;" cellpadding="0" cellspacing="0">
												<tbody>
													<tr>
														<td class="center nw" style="width:33.3%;">NATURE</td>
														<td class="center nw" style="width:33.3%;">RATE</td>
														<td class="center nw" style="width:33.3%;">AMOUNT</td>
													</tr>
													<tr>
														<td class="center nw">'.str_repeat('_', 20).'</td>
														<td class="center nw">'.str_repeat('_', 20).'</td>
														<td class="center nw">'.str_repeat('_', 20).'</td>
													</tr>
													<tr>
														<td class="center nw">'.str_repeat('_', 20).'</td>
														<td class="center nw">'.str_repeat('_', 20).'</td>
														<td class="center nw">'.str_repeat('_', 20).'</td>
													</tr>
													<tr>
														<td class="center nw">'.str_repeat('_', 20).'</td>
														<td class="center nw">'.str_repeat('_', 20).'</td>
														<td class="center nw">'.str_repeat('_', 20).'</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
						<td style="vertical-align:top;">
							<table style="width:200px;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<th class="bb2 right" style="padding-right:9px;">'.number_format($d['LH_NetProceeds'], 2).'</th>
									</tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<th class="bb2 right" style="padding-right:9px;">&nbsp;</th>
									</tr>
									<tr>
										<th class="bb2 right" style="padding-right:9px;">'.number_format($d['LH_OBC'], 2).'</th>
									</tr>'.
										str_repeat('<tr><td>&nbsp;</td></tr>', 10).'
									<tr>
										<th class="bb2 right" style="padding-right:9px;">'.($d['LH_AdvPayment']>0?'ADVANCE PAYMENT: '.number_format($d['LH_AdvPayment'], 2):'').'</th>
									</tr>'.
									($d['LH_PaymentTo']>0?
										'<tr>
											<th class="bb2 right" style="padding-right:9px;">PAYMENT TO: </th>
										</tr>
										<tr>
											<th class="bb2 right" style="padding-right:9px;">'.$payto.'</th>
										</tr>
										<tr>
											<th class="bb2 right" style="padding-right:9px;">'.number_format($d['LH_PaymentTo'], 2).'</th>
										</tr>'
									:
										str_repeat('<tr><td>&nbsp;</td></tr>', 3)
									).
										str_repeat('<tr><td>&nbsp;</td></tr>', 7).'
									<tr>
										<td style="height:27px;">&nbsp;</td>
									</tr>
									<tr>
										<th class="bb2 right" style="padding-right:9px;">'.number_format($tofinancecharges, 2).'</th>
									</tr>
									<tr>
										<th class="bb2 right" style="padding-right:9px;"></th>
									</tr>'
										.str_repeat('<tr><td>&nbsp;</td></tr>', 3).'
									<tr>
										<td class="height:27px;"></td>
									</tr>
									<tr>
										<th class="bb2 right" style="padding-right:9px;">'.number_format($d['LH_Principal'], 2).'</th>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<th colspan="2" style="height:26px;vertical-align:middle;">CERTIFIED CORRECT</th>
					</tr>
					<tr>
						<th class="ul" colspan="2" style="height:;vertical-align:bottom;">'.$atty[2].'</th>
					</tr>
					<tr>
						<th colspan="2">'.($atty[2]=='RAMOS, ANNALIZA C.'?'HEAD OF OPERATIONS':'OFFICER IN CHARGE').'</th>
					</tr>
					<tr>
						<td class="center" colspan="2">(Signature of Creditor Authorized Representative over Printed Name)</td>
					</tr>
					<tr>
						<th class="nw" colspan="2">I ACKNOWLEDGE RECEIPT OF A COPY OF THIS STATEMENT PRIOR TO THE CONSUMPTION OF THE CREDIT</th>
					</tr>
					<tr>
						<th class="nw" colspan="2">TRANSACTION AND THAT I UNDERSTAND AND FULLY AGREE TO THE TERMS AND CONDITION THEREOF.</th>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2">
							<table style="width:100%;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td class="pl50" style="height:30px;width:50%;vertical-align:bottom;">
											<table style="width:70%;" cellpadding="0" cellspacing="0">
												<tbody>
													<tr>
														<td class="bb center">'.date('F d, Y', strtotime($d['LH_LoanDate'])).'</td>
													</tr>
													<tr>
														<td class="center">Date</td>
													</tr>
												</tbody>
											</table>
										</td>
										<td class="right pr50" style="height:30px;width:50%;vertical-align:bottom;">
											<table style="width:70%;" cellpadding="0" cellspacing="0">
												<tbody>
													<tr>
														<td class="bb center">'.$d['CI_Name'].'</td>
													</tr>
													<tr>
														<td class="center">(Signature of Buyer/Borrower Over Printed Name)</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<th colspan="2">NOTICE TO BUYER/BORROWER: YOU ARE RNTITILED TO A COPY OF THIS PAPER YOU SHALL SIGN</th>
					</tr>
				</tbody>
			</table>
			<br class="pageBreak">
		</div><!-- End table3 -->
		<div id="table4" class="hide">
			<table class="salesDocsTable" style="width:100%;" cellpadding="0" cellspacing="0">
				<tbody>
					<tr>
						<th>'.strtoupper($companyname).'</th>
					</tr>
					<tr>
						<th>'.$companyaddress.'</th>
					</tr>
					<tr>
						<th>Tel. No.:'.$telno.'</th>
					</tr>
					<tr>
						<th>Fax: '.$faxno.'</th>
					</tr>
					<tr>
						<th>&nbsp;</th>
					</tr>
					<tr>
						<th class="ul">SPECIAL POWER OF ATTORNEY</th>
					</tr>
					<tr>
						<th>&nbsp;</th>
					</tr>
					<tr>
						<td>KNOW ALL MEN BY THESE PRESENTS:</td>
					</tr>
					<tr>
						<th>&nbsp;</th>
					</tr>
					<tr>
						<td>
							<table style="width:100%;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr style="line-height:17px;">
										<td style="width:100%;">'.str_repeat('&nbsp;', 10).'That, I, <b class="ul">'.strtoupper($d['CI_Name']).',</b> of legal age Filipino ___________<span class="ul">'.$age.'</span>___________ with residence and postal address at <b class="ul">'.strtoupper($d['LH_Address']).'</b> have NAMED, CONSTITUTED AND APPOINTED and by these present do hereby NAME, CONSTITUTE AND APPOINT '.strtoupper($companyname).' my true and lawful attorney-in-fact for me and in my name, place and stead do and perform the following acts and things:</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>
							<table style="width:100%;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr style="line-height:17px;">
										<td style="width:100%;">'.str_repeat('&nbsp;', 10).'1. To receive, encash my monthly SSS pension and/or withdraw once from my Savings Account ATM/Passbook No. <b class="ul">'.$d['LH_BankAcctNo'].'</b> carried with the <b class="ul">'.$d['LH_BankBranch'].'</b> branch of the _________________________ Bank, an amount not exeeding <b class="ul matowords"> ONLY</b>&nbsp;(P&nbsp;<b class="ul">'.number_format($d['LH_MonthlyAmort'], 2).'</b>) Philippine Currency for a period of <b class="ul">&nbsp;'.$d['LH_Terms'].'&nbsp;</b> months beginning <b class="ul">'.date('F Y', strtotime($d['LH_StartDate'])).'</b> To <b class="ul">'.date('F Y', strtotime($d['LH_EndDate'])).'</b>.</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>'.str_repeat('&nbsp;', 10).'2. To sign receipt or any document which may be required by _______________________________________ referred to aboved.</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>'.str_repeat('&nbsp;', 10).'3. To receive, and/or withdraw from my savings Account No. <b class="ul">&nbsp;'.$d['LH_BankAcctNo'].'&nbsp;</b> my 13th month bonus from SSS.</td></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>
							<table style="width:100%;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td style="line-height:17px;">'.str_repeat('&nbsp;', 10).'GIVING AND GRANTING unto my said attorney-in-fact full power and authority to do and perform all and every act and thing whatsoever requisite and necessary to be done in and about the promises, and fully to all intents and purpose as I might of could do if personally present, and hereby ratifying and confirming all that my said attorney-in-fact shall lawfully do or cause to be done by virtue of these presents.</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>'.str_repeat('&nbsp;', 10).'IN WITNESS WHEREOF, I have hereunto signed this instrument as Manila, on <b class="ul">'.date('F d, Y', strtotime($d['LH_LoanDate'])).'</b>.<br /><br /><br /><br /><br /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>
							<table style="width:100%;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td class="bb center" style="width:40%;">'.strtoupper($atty[0]).'</td>
										<td style="width:20%;">&nbsp;</td>
										<td class="bb center" style="width:40%;">'.strtoupper($d['CI_Name']).'</td>
									</tr>
									<tr>
										<th style="width:40%;">Attorney in-fact</th>
										<td style="width:20%;">&nbsp;</td>
										<th style="width:40%;">Principal</th>
									</tr>
									<tr>
										<td colspan="3">&nbsp;</td>
									</tr>
									<tr>
										<td class="bb center" style="width:40%;">'.strtoupper($atty[1]).'</td>
										<td style="width:20%;">&nbsp;</td>
										<td style="width:40%;">&nbsp;</td>
									</tr>
									<tr>
										<th style="width:40%;">Attorney in-fact</th>
										<td style="width:20%;">&nbsp;</td>
										<th style="width:40%;">&nbsp;</th>
									</tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td class="bb center" style="width:40%;">'.strtoupper($atty[2]).'</td>
										<td style="width:20%;">&nbsp;</td>
										<td style="width:40%;">&nbsp;</td>
									</tr>
									<tr>
										<th style="width:40%;">Attorney in-fact</th>
										<td style="width:20%;">&nbsp;</td>
										<th style="width:40%;">&nbsp;</th>
									</tr>
									<tr>
										<td colspan="3">&nbsp;</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<th style="width:100%;">SIGNED IN THE PRESENCE OF:</th>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>
							<table style="width:100%;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td class="bb center" style="width:40%;">&nbsp;</td>
										<td style="width:20%;">&nbsp;</td>
										<td class="bb center" style="width:40%;">&nbsp;</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th>A C K N O W L E D G E M E N T</th>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th class="left">REPUBLIC OF THE PHILIPPINES)</th>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th class="left">MANILA'.str_repeat('&nbsp;', 50).')&nbsp;S.S.</th>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td style="line-height:17px;">'.str_repeat('&nbsp;', 10).'At the above-state locality, on this _________________________________ day of ___________________________________ personally appeared before me <b class="ul">'.date('F d, Y').'</b> with his/her Residence Certificate No. <b class="ul">'.($d['LH_CedulaNo']=='0'?'&nbsp;':$d['LH_CedulaNo']).'</b> issued at <b class="ul">'.($d['LH_CedulaPlace']=='0'?'&nbsp;':$d['LH_CedulaPlace']).'</b> known to me to be the same person who executed the foregoing instrument and acknowledge to me that the same his/her free and deed.</td>
					</tr>
				</tbody>
			</table>
			<br>
			<br>
			<br>
			<br class="pageBreak">
		</div><!-- End Table4 -->
		<div id="table5" class="hide">
			<table class="salesDocsTable" style="width:100%;" cellpadding="0" cellspacing="0">
				<tbody>
					<tr>
						<th colspan="2">'.strtoupper($companyname).'</th>
					</tr>
					<tr>
						<th colspan="2">'.$companyaddress.'</th>
					</tr>
					<tr>
						<th colspan="2">Tel. No.:'.$telno.'</th>
					</tr>
					<tr>
						<th colspan="2">Fax: '.$faxno.'</th>
					</tr>
					<tr>
						<th class="ul" colspan="2">SURETYSHIP AGREEMENT</th>
					</tr>
					<tr>
						<th colspan="2">&nbsp;</th>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2">'.str_repeat('&nbsp;', 10).'KNOW ALL MEN BY THESE PRESENTS:</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2">'.str_repeat('&nbsp;', 10).'This SURETY AGREEMENT, entered on <b class="ul">'.date('F d, Y',strtotime($d['LH_LoanDate'])).'</b> '.strtoupper($companyname).', A domestic corporation duly organized and existing under and by virtue of Philippine laws with principal offices at 606 Pinaglabanan St., San Juan, Metro Manila, herein referred to as the CREDITOR, represented by '.strtoupper($vicepres).'.</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td class="center" colspan="2">and</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td>'.str_repeat('&nbsp;', 10).'CO-MAKER NAME</td>
						<td class="right pr50">, herein referred to as the SURETY</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td class="center" colspan="2">WITNESSETH:</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2" style="line-height:17px;">'.str_repeat('&nbsp;', 10).'WHEREAS <b style="text-decoration:underline;">'.strtoupper($d['CI_Name']).'</b> of legal age, married, Filipino with Address at <b class="ul">'.strtoupper($d['LH_Address']).'</b> herein referred to as the PRINCIPAL obtained a loan from the CREDITOR as a Promissory Note dated executed by the PRINCIPAL in favor of the CREDITOR in the amount of <b class="ul principaltowords" id="sap"></b> AND XX / 100 PESOS ONLY <b class="ul">'.number_format($d['LH_Principal'],2).'</b> payable as follows:&nbsp;&nbsp;&nbsp;&nbsp;(specify the terms) <b class="ul">&nbsp;'.$d['LH_Terms'].'&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2">'.str_repeat('&nbsp;', 10).'WHEREAS, the CREDITOR requires a security for the prompt payment of monthly installment above mentioned:</td>
					</tr>
					<tr>
						<td colspan="2">'.str_repeat('&nbsp;', 10).'WHEREAS, the SURETY on account of valuable consideration received from the PRINCIPAL, is desirous in giving the security required by the CREDITOR:</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2">'.str_repeat('&nbsp;', 10).'NOW THEREFORE, for and in consideration of the foregoing promises, the parties have hereunto agreed as follows:</td>
					</tr>
					<tr>
						<td colspan="2" style="line-height:16px;">'.str_repeat('&nbsp;', 10).'1. The SURETY jointly and severally with the PRINCIPAL, hereby guarantees and warrants to the CREDITOR, its successors or assigns, the prompt payment of maturity of the monthly installments owing to the CREDITOR and other obligations of any kind on which the PRINCIPAL may now be indebted or may hereafter become indebted to the CREDITOR plus interest thereon at the rate of <b class="ul">' . ($branch_code == 'PBB' ? '2' : '2.5') . '</b> percent per month, and the cost and expenses of the CREDITOR incurred in connection therewith.</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2" style="line-height:16px;">'.str_repeat('&nbsp;', 10).'2. In case of default by the PRINCIPAL in the payment at maturity of any of the monthly installments above mentioned, or in case of the PRINCIPALS failure to promptly respond to any other lawful demand made by the CREDITOR, the surety agrees to pay too the CREDITOR, its successors of assignees, upon demand, all monthly installments mentioned above, whether due or not due, plus the interest herein stated.</td>
					</tr>
					<tr>
						<td colspan="2" style="line-height:16px;">'.str_repeat('&nbsp;', 10).'3. The SURETY expressly waives all the rights to demand payment and notice of nonpayment and protest, and the liability on this guaranty, shall be solidary, direct and immediate and not contingent upon the pursuit by the CREDITOR, its successors, endorsees, or assignees, of whatever remedies it or they have against the PRINCIPAL and SURETY will at any time on demand pay to the creditor any unpaid installment plus the interest  herein stated.</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2" style="line-height:16px;">'.str_repeat('&nbsp;', 10).'4. This instrument is intended to be a complete and perfect indemnity to the CREDITOR to the extent above stated, for any indebtedness or<br/>',
	'liability of any kind owing by the PRINCIPAL to the CREDITOR from time, and to be valid and continous without further notice to the<br/>SURETY until the obligation of the PRINCIPAL is fully settled.</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td class="center" style="width:50%;">
							&nbsp;
						</td>
						<td style="width:50%;">
							<table style="width:430px;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td style="width:20px;">&nbsp;</td>
										<td class="bb center">'.strtoupper($companyname).'</td>
									</tr>
									<tr>
										<td style="width:20px;">&nbsp;</td>
										<th>CREDITOR</th>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td class="center" style="width:50%;">
							<table style="width:430px;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td class="bb center">'.(isset($cmname)?$cmname:'&nbsp;').'</td>
									</tr>
									<tr>
										<th>SURETY</th>
									</tr>
								</tbody>
							</table>
						</td>
						<td style="width:50%;">
							<table style="width:430px;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td style="width:20px;">&nbsp;</td>
										<td class="bb center">'.strtoupper($atty[2]).'</td>
									</tr>
									<tr>
										<td style="width:20px;">BY:</td>
										<th>'.($atty[2]=='RAMOS, ANNALIZA C.'?'HEAD OF OPERATIONS':'OFFICER IN CHARGE').'</th>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td style="width:50%;">
							<table style="width:430px;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td class="bb center"></td>
									</tr>
									<tr>
										<td>REPUBLIC OF THE PHILIPPINES)</td>
									</tr>
								</tbody>
							</table>
						</td>
						<td style="width:50%;">
							<table style="width:430px;" cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td style="width:20px;">&nbsp;</td>
										<td class="bb center">&nbsp;</td>
									</tr>
									<tr>
										<td style="width:20px;">&nbsp;</td>
										<tD>&nbsp;</tD>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<th colspan="2">ACKNOWLEDGEMENT</th>
					</tr>
					<tr>
						<td colspan="2" style="height:30px;">'.str_repeat('&nbsp;', 10).'BEFORE me, a Notary Public for an <b class="ul">'.str_repeat('&nbsp;', 40).'</b> this <b class="ul">'.str_repeat('&nbsp;', 40).'</b> personally appeared the follow</td>
					</tr>
					<tr>
						<td colspan="2">
							<table cellpadding="0" cellspacing="0">
								<tbody>
									<tr>
										<td class="center nw" style="width:35%;">Name</td>
										<td class="nw" style="width:20%;">Community Tax Certificate</td>
										<td class="center nw" style="width:25%;">Date</td>
										<td class="center nw" style="width:25%;">Place Issued</td>
									</tr>
									<tr>
										<td class="nw">&nbsp;</td>
										<td class="nw">&nbsp;</td>
										<td class="center nw">'.date('F d, Y').'</td>
										<td class="nw" style="width:25%;">&nbsp;</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td style="padding-top:9px;">
							<small>
								Doc. No<br>
								Page No<br>
								Book No<br>
								Series of<br>
							</small>
						</td>
						<th class="right pr50" style="padding-top:9px;vertical-align:top;">NOTARY PUBLIC</th>
					</tr>
				</tbody>
			</table>
		</div><!-- End Table5 -->
		';
	}
		
	?>
	</body>
</html>