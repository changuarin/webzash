<? $this->load->helper('url'); 

	$branches = $this->db->query("
		SELECT Branch_Code AS code, Branch_Name AS name
		FROM nhgt_master.branch
		WHERE Branch_IsActive=1;
	")->result_array();
	
?>
<link type="text/css" rel="stylesheet" href="<?=base_url()?>system/application/assets/css/forms.css">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>system/application/assets/css/jquery.ui.1.11.4.css">
<style>
	body {margin-top:4px}
	form {margin-bottom:0;}
	fieldset {width:490px;padding-top:8px;}
	h3 {font-family:Courier;margin:4px 0 6px 0;}
	input[type=text], textarea {padding:3px;}
	input[type=text], select, #LM, #PayToButton, #cancelPayToButton, .coTag0, .coTag1, .coTag2 {border:1px solid #0099CC;font-size:12px;height:22px;}
	select {width:100px;}
	table {font-family:Arial;margin:0;border-collapse:collapse;}
	table tr td {border:0 solid black;}
	textarea {border:1px solid #0099CC;font-family:Arial;font-size:12px;width:135px;}
	
	.coTag0 {background-color:#CFF;color:#00F;padding-bottom:2px;}
	.coTag1, #cancelPayToButton {background-color:#FF0000;color:#00F;padding-bottom:2px;}
	.coTag2 {background-color:#00F;color:#FF0000;padding-bottom:2px;}
	.center {text-align:center;}
	.column1 {width:110px;}
	.column2 {width:145px;}
	.column3 {width:120px;}
	.column4 {width:140px;}
	.hide {display:none;}
	.right {text-align:right;}
	.top {vertical-align:top;}
	.toppad0 {padding-top:0;}
	
	#menuTab {padding:6px 5px 6px 5px;position:fixed;right:8px;}
	#Terms, #Paid, #UnPaid, #LH_Terms, #LH_Rate, #LH_CollFeeRate{width:70px;}
	#CP_Amount, #MonthlyAmort, #NetProceeds, #Balance, #LH_LoanAmt, #LH_Principal, #LH_MonthlyAmort, #LH_Rate, #LH_InterestAmt, #LH_CollFeeRate, #LH_CollFee, #LH_ProcFee, #LH_Notarial, #LH_OBC, #LH_OBC_Spec, #LH_PaymentTo, #LH_AdvPayment, #LH_NetProceeds {text-align:right;}
	#pnRef, #pnBal {font-weight:bold;}
	#pnBal {color: #FF0000;}
	#principalFormula {color:#000;font-weight:bold;}
	#AvailableAmt, #Terms, #Duration, #LH_LoanDate, #LH_StartDate, #LH_EndDate, #LH_CedulaDate{text-align:center;}
	#AvailableAmt {width:85px;}
	#BankAcctNo, #LH_LoanTrans, #LH_PayOption, #LH_Computation {width:128px;}
	#CI_Name {width:245px;}
	#Duration {width:180px;}
	#ReferencePN, #LH_Address {width:180px;}
	#LH_WithdrawalDate {width:40px;}
	#AiName1, #AiName2 {width:160px;}
	#CI_Agent1_Rate, #CI_Agent2_Rate {width:40px;}
	#LM, #PayToButton {background-color:#CFF;color:#00F;padding-bottom:2px;}
	#table2 {margin-top:5px;}
	#table2 tr td {padding-left:2px;padding-right:6px;}
	#AgentTable tr td {background-color:#666666;color:#FFFFFF;}
	#PaymentToName {color:#FF0000;font-weight:bold;}
</style>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/format.js"></script>
<script type="text/javascript"src="<?=base_url()?>system/application/assets/js/jquery.ui.1.11.4.js"></script>

<script type="text/javascript">
	$(document).ready(function(){
		
		$('input[type=checkbox], input[type=text], select, textarea').attr('disabled', true).css('background-color', '#FFFFFF');
		$('input[type=button]').attr('disabled', true);
		
		//$('#LH_LoanDate').datepicker({dateFormat: 'yy-mm-dd'});
		$('#LH_LoanDate').blur(function(){
			$('#LH_LoanAmt').blur();
		});
		
		$('#ReferencePN').change(function(){
			$('#LH_LoanTrans').val('').change();
			$.post('changeReferencePN',{acctno:$('#CI_AcctNo').val(),pnno:$(this).val()},function(r)
			{
				eval(r)
			});
		});
		
		$('#LH_LoanTrans').change(function(){
			if($(this).val()=='EXT'&&$('#ReferencePN').val()==''||$(this).val()=='REN'&&$('#ReferencePN').val()==''||$(this).val()=='RES'&&$('#ReferencePN').val()==''||$(this).val()=='SPEC'&&$('#ReferencePN').val()==''||$(this).val()=='SPEC2'&&$('#ReferencePN').val()=='')
			{
				alert('Cannot Proceed. Select a Reference PN.');
				$(this).val('');
			} else if($(this).val()=='ADD'&&$('#ReferencePN').val()!=''||$(this).val()=='NEW'&&$('#ReferencePN').val()!=''||$(this).val()=='RET'&&$('#ReferencePN').val()!='') {
				alert('Cannot Proceed. Reference PN must be blank.');
				$(this).val('');
			} else {
				if($(this).val()=='REN')
				{
					var width = 700;
					var height = 400;
					var top = (screen.height/2)-(height/2);
					var left = (screen.width/2)-(width/2);
					window.open('referencePNList/'+$('#CI_AcctNo').val()+'/'+$('#LH_Reference').val(),'popupWindow','width='+width+',height='+height+',top='+top+',left='+left+',scrollbars=yes').focus();
				}
				$.post('changeLH_LoanTrans',{acctno:$('#CI_AcctNo').val(),availableamt:$('#AvailableAmt').val(),loantrans:$(this).val(),res_ob:$('#NetProceeds').val()},function(r)
				{
					eval(r);
				});
			}
		});
		
		$('#LH_Terms').change(function(){
			$('#LH_LoanAmt').blur();
		});
		$('#LH_Rate').blur(function(){
			$('#LH_LoanAmt').blur();
		});
		$('#LH_CollFeeRate').blur(function(){
			$('#LH_LoanAmt').blur();
		});
		$('#LH_ProcFee').blur(function(){
			$('#LH_LoanAmt').blur();
		});
		$('#LH_PaymentTo').blur(function(){
			var a = $('#LH_PaymentTo_Ref').val();
			var b = $(this).val();
			if(a==''&&b!='0')
			{
				alert('Cannot proceeed. Attach a Client.');
				$(this).val('0');
			} else {
				$('#LH_LoanAmt').blur();
			}
		});
		$('#LH_AdvPayment').blur(function(){
			$('#LH_LoanAmt').blur();
		});
		
		$('#LH_LoanAmt').blur(function(){
			$.post('blurLH_LoanAmt',{loantrans:$('#LH_LoanTrans').val(),loandate:$('#LH_LoanDate').val(),terms:$('#LH_Terms').val(),loanamt:$('#LH_LoanAmt').val(),rate:$('#LH_Rate').val(),collfeerate:$('#LH_CollFeeRate').val(),procfee:$('#LH_ProcFee').val(),notarial:$('#LH_Notarial').val(),obc:$('#LH_OBC').val(),advpayment:$('#LH_AdvPayment').val(),paymentto:$('#LH_PaymentTo').val(),refpnenddate:$('#refPNEndDate').val(),coTag:$('#coTag').attr('class'),acctno:$('#CI_AcctNo').val(),refpn:$('#ReferencePN').val()},function(r)
			{
				eval(r);
			});
		});
		$('#PayToButton').click(function(){
			var width = 560;
			var height = 480;
			var top = (screen.height/2)-(height/2);
			var left = (screen.width/2)-(width/2);
			window.open('PaymentToList','popupWindow','width='+width+',height='+height+',top='+top+',left='+left+',scrollbars=yes').focus();
		});
		$('#cancelPayToButton').click(function(){
			$('#PaymentToName').html('');
			$('#LH_PaymentTo_Ref').val('');
			$('#LH_PaymentTo').val('0');
			$(this).attr('class', 'hide');
			$('#LH_LoanAmt').blur();
		});
		
		$('#previewButton').click(function(){
			var a = $('#LH_LoanTrans').val();
			if(a!='')
			{
				var width = 840;
				var height = 540;
				var top = (screen.height/2)-(height/2);
				var left = (screen.width/2)-(width/2);
				var lhref = $('#LH_Reference').val();
				if(lhref=='') { lhref = '0'; }
				var b = $('#LH_PaymentTo_Ref').val();
				if(b=='')
				{
					lhpaytorefpn = '0';
				} else {
					var data = b.split('|');
					lhpaytorefpn = data[1];
				}
				var paytoname = $('#PaymentToName').html();
				if(paytoname=='') { paytoname = '0'; }
				window.open('loanComputation/'+$('#LH_LoanDate').val()+'/'+$('#CI_Name').val()+'/'+$('#LH_Address').val()+'/'+$('#CP_Amount').val()+'/'+$('#LH_LoanTrans').val()+'/'+$('#LH_StartDate').val()+'/'+$('#LH_EndDate').val()+'/'+$('#LH_LoanAmt').val()+'/'+$('#LH_Terms').val()+'/'+$('#LH_OBC').val()+'/'+$('#LH_PaymentTo').val()+'/'+$('#LH_AdvPayment').val()+'/'+lhref+'/'+$('#LH_Rate').val()+'/'+$('#LH_ProcFee').val()+'/'+$('#LH_CollFeeRate').val()+'/'+lhpaytorefpn+'/'+paytoname,'popupWindow','width='+width+',height='+height+',top='+top+',left='+left+',scrollbars=yes').focus();
			} else {
				alert('Loan Type is blank.');
			}
		});
		
		$('#CoMakerName').click(function(){
			var width = 400;
			var height = 480;
			var top = (screen.height/2)-(height/2);
			var left = (screen.width/2)-(width/2);
			window.open('../master/comaker_list','popupWindow','width='+width+',height='+height+',top='+top+',left='+left+',scrollbars=yes').focus();
		});
		
		$('#coTag').click(function(){
			var a = $(this).attr('class');
			if(a == 'coTag0')
			{
				$(this).attr('class', 'coTag1');
			} else if(a == 'coTag1') {
				$(this).attr('class', 'coTag2');
			} else if(a == 'coTag2') {
				$(this).attr('class', 'coTag0');
			}
			$('#LH_LoanAmt').blur();
		});
		
		$('#resetButton').click(function() {
			$('#LH_LoanTrans').val('').change();
			
		});
		
		$('#submitButton').click(function() {
			var a = confirm("Submit loan?");
			if (a == true) {
				return true;
			} else {
				return false;
			} 
		});
		
	});
</script>
<body style="">
	<div id="menuTab">
		<input type="button" id="previewButton" value="Preview">
	</div><!-- end menuTab -->
	
	<form id="loanAppForm" method="post" action="insertLoan">
		<h3 class="tabHeadings"tag="loanAppTab">Loan Application</h3>
		<div id="loanAppTab">
			<table border="0" cellpadding="0" cellspacing="0" style="margin:8px 0 8px 0;">
				<tbody>
					<tr>
						<td class="column1"></td>
						<td></td>
						<td class="column3"></td>
						<td></td>
					</tr>
					<tr>
						<td colspan="4">
							&nbsp;&nbsp;Client ID&nbsp;
							<input id="CI_AcctNo" type="text" name="CI_AcctNo">
							&nbsp;Name&nbsp;
							<input id="CI_Name" type="text" name="CI_Name">
						</td>
					</tr>
				</tbody>
			</table>
			
			<fieldset>
				<table border="0" cellpadding="0" cellspacing="0">
					<tbody>
						<tr>
							<td class="column1"></td>
							<td></td>
							<td class="column3"></td>
							<td></td>
						</tr>
						<tr>
							<td>Bank Acct. No.</td>
							<td>
								<select id="BankAcctNo" name="BankAcctNo">
								</select>
							</td>
							<td>&nbsp;&nbsp;&nbsp;Pension Amt.</td>
							<td>
								<input id="CP_Amount" type="text" name="CP_Amount">
							</td>
						</tr>
						<tr>
							<td>Reference PN</td>
							<td colspan="3">
								<input class="hide" id="LH_Reference" type="text" name="LH_Reference">
								<select id="ReferencePN" name="ReferencePN">
								</select>
								&nbsp;
								<span id="pnRef"></span>
								&nbsp;
								<span id="pnBal"></span>
							</td>
						</tr>
						<tr>
							<td>Total Terms</td>
							<td>
								<input id="Terms" type="text" name="Terms">
							</td>
							<td>&nbsp;&nbsp;&nbsp;M/A</td>
							<td>
								<input id="MonthlyAmort" type="text" name="MonthlyAmort">
							</td>
						</tr>
						<tr>
							<td>Paid</td>
							<td>
								<input id="Paid" type="text" name="Paid">
							</td>
							<td>&nbsp;&nbsp;&nbsp;Net Proceeds</td>
							<td>
								<input id="NetProceeds" type="text" name="NetProceeds">
							</td>
						</tr>
						<tr>
							<td>Un-Paid</td>
							<td>
								<input id="UnPaid" type="text" name="UnPaid">
							</td>
							<td>&nbsp;&nbsp;&nbsp;Actual Balance</td>
							<td>
								<input id="Balance" type="text" name="Balance">
							</td>
						</tr>
						<tr>
							<td>Available Amt.</td>
							<td>
								<input id="AvailableAmt" type="text" name="AvailableAmt">
								<input id="LM" type="button" name="LM" value="LM">
							</td>
							<td colspan="2">
								&nbsp;&nbsp;&nbsp;Duration&nbsp;
								<input class="hide" id="refPNEndDate" type="text" name="refPNEndDate">
								<input class="center" id="Duration" type="text" name="Duration">
							</td>
						</tr>
						<tr>
							<td>Type</td>
							<td colspan="3">
								<select id="LH_LoanTrans" name="LH_LoanTrans">
									<option value=""></option>
									<option value="SPEC">ADVANCE BONUS</option>
									<option value="SPEC2">ADVANCE SSS INCREASE</option>
									<option value="ADD">ADDITIONAL</option>
									<option value="EXT">EXTENSION</option>
									<option value="NEW">NEW CLIENT</option>
									<option value="REN">RENEWAL</option>
									<option value="RES">RESTRUCTURE</option>
									<option value="RET">RETURNING</option>
								</select>
								<input class="hide" id="resetButton" type="button" name="resetButton" value="Reset">
							</td>
						</tr>
						<tr>
							<td>Payment Type</td>
							<td>
								<select id="LH_PayOption" name="LH_PayOption">
									<option value="M">Monthly</option>
									<option value="SM">Semi-Monthly</option>
								</select>
							</td>
							<td>&nbsp;&nbsp;&nbsp;Loan Date</td>
							<td>
								<input id="LH_LoanDate" type="text" name="LH_LoanDate" value="<?=date('Y-m-d')?>">
							</td>
						</tr>
						<tr>
							<td>Computation</td>
							<td>
								<select id="LH_Computation" name="LH_Computation">
									<option value="REG">Regular</option>
									<option value="ADD">Add-On</option>
								</select>
							</td>
							<td>&nbsp;&nbsp;&nbsp;Withdrawal Date</td>
							<td>
								<select id="LH_WithdrawalDate" name="LH_WithdrawalDate">
									<?php
										for($day=1;$day<=31;$day++)
										{
											echo '<option value="'.$day.'">'.$day.'</option>';
										}
									?>
								</select>
								<input id="coTag" class="coTag0" type="button" name="coTag" value="CO">
							</td>
						</tr>
						<tr>
							<td>Terms</td>
							<td class="right">
								<select id="LH_Terms" name="LH_Terms">
								</select>
							</td>
							<td>&nbsp;&nbsp;&nbsp;From</td>
							<td>
								<input id="LH_StartDate" type="text" name="LH_StartDate">
							</td>
						</tr>
						<tr>
							<td>Loanable Amt.</td>
							<td>
								<input id="LH_LoanAmt" type="text" name="LH_LoanAmt">
							</td>
							<td>&nbsp;&nbsp;&nbsp;To</td>
							<td>
								<input id="LH_EndDate" type="text" name="LH_EndDate">
							</td>
						</tr>
						<tr>
							<td>Principal Amt.</td>
							<td class="right">
								<label id="principalFormula"></label>
							</td>
							<td class="right" colspan="2">
								<input id="LH_Principal" type="text" name="LH_Principal">
							</td>
						</tr>
						<tr>
							<td colspan="3">Monthly Amort.</td>
							<td>
								<input id="LH_MonthlyAmort" type="text" name="LH_MonthlyAmort">
							</td>
						</tr>
						<tr>
							<td>Interest</td>
							<td>
								<input id="LH_Rate" type="text" name="LH_Rate">
							</td>
							<td class="right" colspan="2">
								<input id="LH_InterestAmt" type="text" name="LH_InterestAmt">
							</td>
						</tr>
						<tr>
							<td>Other Charges</td>
							<td>
								<input id="LH_CollFeeRate" type="text" name="LH_CollFeeRate">
							</td>
							<td class="right" colspan="2">
								<input id="LH_CollFee" type="text" name="LH_CollFee">
							</td>
						</tr>
						<tr>
							<td colspan="3">Processing Fee</td>
							<td>
								<input id="LH_ProcFee" type="text" name="LH_ProcFee">
							</td>
						</tr>
						<tr>
							<td colspan="3">Collection Fee</td>
							<td>
								<input id="LH_Notarial" type="text" name="LH_Notarial">
							</td>
						</tr>
						<tr>
							<td colspan="2">O.B. - Notes Recievable</td>
							<td class="right" colspan="2">
								<input class="hide" id="LH_Ref_OB" type="text" name="LH_Ref_OB">
								<input id="LH_OBC" type="text" name="LH_OBC">
							</td>
						</tr>
						<tr>
							<td colspan="3">O.B. - 13th Month Bonus</td>
							<td>
								<input id="LH_OBC_SPEC" type="text" name="LH_OBC_SPEC">
							</td>
						</tr>
						<tr>
							<td colspan="2">
								Payment To&nbsp;
								<label id="PaymentToName"></label>
							</td>
							<td class="center">
								<input class="hide" id="cancelPayToButton" type="button" name="cancelPayToButton" value="X">
								<input id="PayToButton" type="button" name="PayToButton" value="Client">
							</td>
							<td>
								<input class="hide" id="LH_PaymentTo_Ref" type="text" name="LH_PaymentTo_Ref">
								<input id="LH_PaymentTo" type="text" name="LH_PaymentTo">
							</td>
						</tr>
						<tr>
							<td colspan="2">Advance Payment</td>
							<td>
								<input id="AdvPayment" type="checkbox" name="AdvPayment">
								Include to O.B.
							</td>
							<td>
								<input id="LH_AdvPayment" type="text" name="LH_AdvPayment">
							</td>
							<td>
								<input class="hide" id="LH_PaymentTo_Ref" type="text" name="LH_PaymentTo_Ref">
							</td>
						</tr>
						<tr>
							<td colspan="3">NET PROCEEDS</td>
							<td>
								<input id="LH_NetProceeds" type="text" name="LH_NetProceeds">
							</td>
						</tr>
						<tr>
							<td class="right" colspan="4"><input id="submitButton" type="submit" name="submitButton" value="Process" disabled></td>
						</tr>
					</tbody>
				</table>
			</fieldset>
			
			<table id="table2" cellpadding="0" cellspacing="0">
				<tbody>
					<tr>
						<td class="right"><label id="comaker_list" class="loan_application">Co-Maker</label></td>
						<td>
							<input class="hide" id="LH_CoMaker" type="text" name="LH_CoMaker">
							<input id="CoMakerName" type="text" name="CoMakerName">
						</td>
						<td class="right">Address</td>
						<td>
							<input id="LH_Address" type="text" name="LH_Address">
						</td>
					</tr>
					<tr>
						<td class="right">Res. Cert. No.</td>
						<td>
							<input id="LH_CedulaNo" type="text" name="LH_CedulaNo">
						</td>
						<td colspan="2" rowspan="3">
							<table id="AgentTable" cellpadding="3" cellspacing="0">
								<tbody>
									<tr>
										<td class="right">Agent</td>
										<td>
											<input class="hide" id="LH_Agent1" type="text" name="LH_Agent1">
											<input id="AiName1" type="text" name="AiName1">
											<input id="CI_Agent1_Rate" type="text" name="CI_Agent1_Rate">
										</td>
									</tr>
									<tr>
										<td class="right toppad0">Sub-Agent</td>
										<td class="toppad0">
											<input class="hide" id="LH_Agent2" type="text" name="LH_Agent2">
											<input id="AiName2" type="text" name="AiName2">
											<input id="CI_Agent2_Rate" type="text" name="CI_Agent2_Rate">
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td class="right">Issued At</td>
						<td>
							<input id="LH_CedulaPlace" type="text" name="LH_CedulaPlace">
						</td>
					</tr>
					<tr>
						<td class="padBottom right">Date</td>
						<td class="padBottom">
							<input id="LH_CedulaDate" type="text" name="LH_CedulaDate">
						</td>
					</tr>
				</tbody>
			</table>
		</div><!-- end loanAppTab -->
	</form><!-- end loanAppForm -->
	
</body>