<? $this->load->helper('url'); 

	$branches = $this->db->query("
		SELECT Branch_Code AS code, Branch_Name AS name
		FROM nhgt_master.branch
		WHERE Branch_IsActive=1;
	")->result_array();
?>
<link type="text/css" rel="stylesheet" href="<?=base_url()?>system/application/assets/css/forms.css">
<link rel="stylesheet" type="text/css" href="<?=base_url()?>system/application/assets/css/jquery.datepick.css"> 
<style>
	body {margin-top:4px}
	form {margin-bottom:0;}
	fieldset {width:490px;padding-top:8px;}
	h3 {font-family:Courier;margin:4px 0 6px 0;}
	input[type=text], textarea {padding:3px;}
	input[type=text], select, #LM, #PaymentTo {border:1px solid #0099CC;font-size:12px;height:22px;}
	select {width:100px;}
	table {font-family:Arial;margin:0;border-collapse:collapse;}
	table tr td {border:0 solid black;}
	textarea {border:1px solid #0099CC;font-family:Arial;font-size:12px;width:128px;}
	
	.center {text-align:center;}
	.column1 {width:110px;}
	.column2 {width:110px;}
	.column3 {width:100px;}
	.column4 {width:110px;}
	.hide {display:none;}
	.right {text-align:right;}
	.top {vertical-align:top;}
	.toppad0 {padding-top:0;}
	.width90 {width:90px;}
	
	#menuTab {padding:6px 5px 6px 5px;position:fixed;right:8px;}
	#LH_LoanAmt, #LH_Rate, #LH_CollFeeRate, #LH_Principal, #LH_MonthlyAmort, #LH_InterestAmt, #LH_CollFee, #LH_ProcFee, #LH_Notarial, #LH_OBC, #LH_OBC_SPEC, #LH_PaymentTo, #LH_AdvPayment, #LH_NetProceeds {text-align:right;}
	#LH_Terms, #LH_BankAcctNo, #LH_WithdrawalDate, #LH_LoanDate, #LH_ApprovedDate, #Duration, #LH_CedulaDate {text-align:center;}
	#CI_Name {width:245px;}
	#Atty1Name, #Atty2Name, #Atty3Name, #LH_Witness1, #LH_Witness2 {width:160px;}
	#LM, #PaymentTo {background-color:#CFF;color:#00F;padding-bottom:2px;}
	#table2 {margin-top:5px;}
	#table2 tr td {padding-left:2px;padding-right:6px;}
	
</style>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.plugin.js"></script> 
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.datepick.js"></script>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/format.js"></script>

<script type="text/javascript">
	$(document).ready(function(){
		
		$('input[type=text], textarea').attr('readonly', true).css('background-color', '#FFFFFF');
		$('input[type=button]').attr('disabled', true);
		
		$('#printButton').click(function(){
			var width = 840;
			var height = 540;
			var top = (screen.height/2)-(height/2);
			var left = (screen.width/2)-(width/2);
			window.open('documents/'+$('#CI_AcctNo').val()+'/'+$('#LH_PN').val(),'popupWindow','width='+width+',height='+height+',top='+top+',left='+left+',scrollbars=yes').focus();
		});
		// +$('#LH_LoanDate').val()
		
		$('#cancelButton').click(function(){
			window.location.href='cancelLoan/'+$('#CI_AcctNo').val()+'/'+$('#LH_PN').val();
		});
		
	});
</script>
<body style="">
	<div id="menuTab">
		<input type="button" id="printButton" value="Print Docs">
		<input type="button" id="cancelButton" value="Cancel">
	</div><!-- end menuTab -->
	
	<form id="loanProcForm" method="post" action="processLoan">
		<h3 class="tabHeadings"tag="loanProcTab">Loan Processing</h3>
		<div id="loanProcTab">
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
							<td>PN</td>
							<td>
								<input id="LH_PN" type="text" name="LH_PN">
							</td>
							<td>&nbsp;&nbsp;&nbsp;Bank Acct. No.</td>
							<td>
								<input id="LH_BankAcctNo" type="text" name="LH_BankAcctNo">
							</td>
						</tr>
						<tr>
							<td class="top">Remarks</td>
							<td>
								<textarea id="Remarks" name="Remarks" rows="2"></textarea>
							</td>
							<td class="top">&nbsp;&nbsp;&nbsp;Withdrawal Day</td>
							<td class="top">
								<input id="LH_WithdrawalDate" type="text" name="LH_WithdrawalDate">
							</td>
						</tr>
						<tr>
							<td>Type</td>
							<td>
								<input id="LH_LoanTrans" type="text" name="LH_LoanTrans">
							</td>
							<td>&nbsp;&nbsp;&nbsp;Loan Date</td>
							<td>
								<input id="LH_LoanDate" type="text" name="LH_LoanDate">
							</td>
						</tr>
						<tr>
							<td>Payment Type</td>
							<td>
								<input id="LH_PayOption" type="text" name="LH_PayOption">
							</td>
							<td>&nbsp;&nbsp;&nbsp;Approved Date</td>
							<td>
								<input id="LH_ApprovedDate" type="text" name="LH_ApprovedDate">
							</td>
						</tr>
						<tr>
							<td>Computation</td>
							<td>
								<input id="LH_Computation" type="text" name="LH_Computation">
							</td>
							<td>
								&nbsp;&nbsp;&nbsp;Duration&nbsp;
							</td>
							<td>
								<input id="Duration" type="text" name="Duration">
							</td>
						</tr>
						<tr>
							<td>Terms</td>
							<td>
								<input id="LH_Terms" type="text" name="LH_Terms">
							</td>
							<td>&nbsp;&nbsp;&nbsp;Loanable Amt.</td>
							<td>
								<input id="LH_LoanAmt" type="text" name="LH_LoanAmt">
							</td>
						</tr>
						<tr>
							<td colspan="4">&nbsp;</td>
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
							<td class="right">
								<input id="LH_Rate" type="text" name="LH_Rate">
							</td>
							<td class="right" colspan="2">
								<input id="LH_InterestAmt" type="text" name="LH_InterestAmt">
							</td>
						</tr>
						<tr>
							<td>Other Charges</td>
							<td class="right">
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
							<td colspan="2">Payment To</td>
							<td class="right" colspan="2">
								<input id="LH_PaymentTo" type="text" name="LH_PaymentTo">
							</td>
						</tr>
						<tr>
							<td colspan="2">Advance Payment</td>
							<td class="right" colspan="2">
								<input id="LH_AdvPayment" type="text" name="LH_AdvPayment">
							</td>
						</tr>
						<tr>
							<td colspan="3">NET PROCEEDS</td>
							<td>
								<input id="LH_NetProceeds" type="text" name="LH_NetProceeds">
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
			
			<table id="table2" cellpadding="0" cellspacing="0">
				<tbody>
					<tr>
						<td></td>
						<td></td>
						<td class="width100"></td>
						<td></td>
					</tr>
					<tr>
						<td class="right">Agent</td>
						<td colspan="3">
							<input id="AiName1" type="text" name="AiName1">
						</td>
					</tr>
					<tr>
						<td class="right">Sub-Agent</td>
						<td>
							<input id="AiName2" type="text" name="AiName2">
						</td>
						<td>Atty-In-Fact #1</td>
						<td>
							<input class="hide" id="LH_Atty1" type="text" name="LH_Atty1">
							<input id="Atty1Name" type="text" name="Atty1Name">
						</td>
					</tr>
					<tr>
						<td class="right">Co-Maker</td>
						<td>
							<input id="CoMakerName" type="text" name="CoMakerName">
						</td>
						<td class="right">Atty-In-Fact #2</td>
						<td>
							<input class="hide" id="LH_Atty2" type="text" name="LH_Atty2">
							<input id="Atty2Name" type="text" name="Atty2Name">
						</td>
					</tr>
					<tr>
						<td class="right">Address</td>
						<td>
							<input id="LH_Address" type="text" name="LH_Address">
						</td>
						<td class="right">Atty-In-Fact #3</td>
						<td>
							<input class="hide" id="LH_Atty3" type="text" name="LH_Atty3">
							<input id="Atty3Name" type="text" name="Atty3Name">
						</td>
					</tr>
					<tr>
						<td class="right">Res. Cert. No.</td>
						<td colspan="3">
							<input id="LH_CedulaNo" type="text" name="LH_CedulaNo">
						</td>
					</tr>
					<tr>
						<td class="right">Issued At</td>
						<td>
							<input id="LH_CedulaPlace" type="text" name="LH_CedulaPlace">
						</td>
						<td class="right toppad0">Witness #1</td>
						<td>
							<input id="LH_Witness1" type="text" name="LH_Witness1">
						</td>
					</tr>
					<tr>
						<td class="right">Date</td>
						<td>
							<input id="LH_CedulaDate" type="text" name="LH_CedulaDate">
						</td>
						<td class="right">Witness #2</td>
						<td>
							<input id="LH_Witness2" type="text" name="LH_Witness2">
						</td>
					</tr>
				</tbody>
			</table>
		</div><!-- end loanAppTab -->
	</form><!-- end loanAppForm -->
	
</body>