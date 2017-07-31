<link type="text/css" rel="stylesheet" href="<?=base_url()?>system/application/assets/css/forms.css">
<link rel="stylesheet" type="text/css" href="<?=base_url()?>system/application/assets/css/jquery.datepick.css"> 
<style>
	body {background-color:#FF9;}
	fieldset {border:1px solid #0099CC;width:480px;margin:0 auto;}
	input[type=text], select {font-size:12px;border:1px solid #0099CC;}
	input[type=text] {text-align:center;}
	input[type=text]:readonly {background-color:#FF0000;}
	table {font-family:'Arial';font-size:12px;margin:0 auto;}
	table tr td {border:0 solid black;padding:2px;}
	
	.hide {display:none;}
	.right {text-align:right;}
	
	#TTRefund, #TTCollection, #TTForRefund {margin:auto 0;}
	#TransType {width:124px;}
	#LH_PN {width:180px;}
	#LL_Remarks {width:240px;}
</style>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.datepick.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
	
		$('#TTForRefund').attr('disabled', true);
		$('#CI_AcctNo').attr('readonly', false);
		
		<?=($process=="1"?"$('input[type=text]').attr('readonly', true);":'')?>
		$('#ID, #LL_PaymentDate, #LL_Remarks').attr('readonly', false);
		
		$('#LL_Refund').blur(function(){
			var transtype = $('#TransType').val();
			if(transtype == 'Refund')
			{
				<?= ($process == 1 ?
					"
					$.post('../../../../../../addRemarks', {paymentdate: $('#LL_PaymentDate').val()}, function(r)
					{
						eval(r)
					});
					"
				:
					"
					$.post('../../../../../../../../../../addRemarks', {paymentdate: $('#LL_PaymentDate').val()}, function(r)
					{
						eval(r)
					});
					"
				) ?>
			}
		});
		
		$('#postButton').click(function(){
			$('#clientLedgerForm').submit();
		});
		$('#cancelButton').click(function(){
			window.close();
		});
		
		$('#ID').focus();
		
		$('#ID').keypress(function(e){
			if(e.which == 13) {
				<?= ($process == 1 ?
					"$('#TransType').focus();"
				:
					"$('#LL_PaymentDate').select();"
				) ?>
			}
		});
		$('#TransType').keypress(function(e){
			if(e.which == 13) {
				var transtype = $(this).val();
				if(transtype=='Collection')
				{
					$('#RFW_No, #LL_Refund').val('').attr('readonly', true).css('background-color', '#DDD');
					$('#LL_ORNo, #LL_CRNo, #PRNo, #CVNo, #LL_CheckNo, #LL_AmountCash_Payment').val('').attr('readonly', false).css('background-color', '#FFF');
					$('#LL_Remarks').val('').css('background-color', '#FFF');
				} else if(transtype=='Refund') {
					$('#LL_ORNo, #LL_CRNo, #PRNo, #LL_AmountCash_Payment').val('').attr('readonly', true).css('background-color', '#DDD');
					$('#RFW_No, #CVNo, #LL_CheckNo, #LL_Refund').val('').attr('readonly', false).css('background-color', '#FFF');;
				}
				$('#LL_PaymentDate').select();
			}
		});
		$('#LL_PaymentDate').keypress(function(e){
			if(e.which == 13) {
				var a = $(this).val();
				if(a.indexOf('-') === -1)
				{
					var b = [a.slice(0, 4), '-', a.slice(4)].join('');
					var c = [b.slice(0, 7), '-', b.slice(7)].join('');
					$(this).val(c);
				}
				$('#LL_ORNo').select();
			}
		});
		$('#LL_ORNo').keypress(function(e){
			if(e.which == 13) {
				$('#LL_CRNo').select();
			}
		});
		$('#LL_CRNo').keypress(function(e){
			if(e.which == 13) {
				$('#PRNo').select();
			}
		});
		$('#LL_CRNo').keypress(function(e){
			if(e.which == 13) {
				$('#PRNo').select();
			}
		});
		$('#PRNo').keypress(function(e){
			if(e.which == 13) {
				$('#RFW_NO').select();
			}
		});
		$('#RFW_NO').keypress(function(e){
			if(e.which == 13) {
				$('#CVNo').select();
			}
		});
		$('#CVNo').keypress(function(e){
			if(e.which == 13) {
				$('#LL_CheckNo').select();
			}
		});
		$('#LL_CheckNo').keypress(function(e){
			if(e.which == 13) {
				$('#LL_AmountCash_Payment').select();
			}
		});
		$('#LL_AmountCash_Payment').keypress(function(e){
			if(e.which == 13) {
				$('#LL_Refund').select();
			}
		});
		$('#LL_Refund').keypress(function(e){
			if(e.which == 13) {
				var transtype = $('#TransType').val();
				if(transtype == 'Refund')
				{
					if(transtype == 'Refund')
					{
						<?= ($process == 1 ?
							"
							$.post('../../../../../../addRemarks', {paymentdate: $('#LL_PaymentDate').val()}, function(r)
							{
								eval(r)
							});
							"
						:
							"
							$.post('../../../../../../../../../../addRemarks', {paymentdate: $('#LL_PaymentDate').val()}, function(r)
							{
								eval(r)
							});
							"
						) ?>
					}
				}
				$('#LL_Remarks').select();
			}
		});
		
		$('#LL_Remarks').keypress(function(e){
			if(e.which == 13) {
				$('#postButton').focus();
			}
		});
		
		$('input[type=text]').keyup(function() {
			var string = $(this).val().toUpperCase();
			$(this).val(string);
		})
	});
</script>
<body>
	<form id="clientLedgerForm" method="post" action="<?= ($process == 1 ? '../../../../../../insertLedgerPost' : '../../../../../../../../../../updateLedgerPost') ?>">	
		<fieldset>
			<table border="0" cellpadding="0" cellspacing="0">
				<tbody>
					<tr>
						<td style="width:120px;"></td>
						<td>
							<input id="CI_AcctNo" type="text" name="CI_AcctNo" value="<?= $CI_AcctNo ?>">
						</td>
					</tr>
					<tr>
						<td class="right">PN No.:&nbsp;&nbsp;</td>
						<td>
							<select id="LH_PN" name="LH_PN">
							<?php
							
							foreach($datas as $d):
								echo '<option value="' . $d['LH_PN'] . '" ' . ($d['LH_PN'] == $LH_PN ? 'selected' : '') . '>' . $d['LH_PN'] . '(' . $d['LH_LoanTrans'] . ')</option>';
							endforeach;
							
							?>
							</select>
							<input type="text" class="hide" id="PNNo" name="PNNo" value="<?= $LH_PN ?>">
						</td>
					</tr>
					<tr>
						<td class="right">Installment No.:&nbsp;&nbsp;</td>
						<td>
							<input type="text" id="ID" name="ID" value="<?= ($process == 1 ? '' : $ID) ?>">
							<input type="text" class="hide" id="TransNo"  name="TransNo" value="<?= ($process == 1 ? '' : $ID) ?>">
						</td>
					</tr>
					<tr>
						<td class="right">Trans Type&nbsp;&nbsp;</td>
						<td>
							<select id="TransType" name="TransType">
								<option value="Collection" <?= ($process == 1 ? '' : ($LL_Refund == 0 ? 'selected' : '')) ?>>Collection</option>
								<option value="Refund" <?= ($process == 1 ? '' : ($LL_Refund > 0 ? 'selected' : '')) ?>>Refund</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="right">Date:&nbsp;&nbsp;</td>
						<td>
							<input type="text" class="LL_PaymentDate" id="LL_PaymentDate" name="LL_PaymentDate" value="<?= ($process == 1 ? date('Y-m-d') : date('Y-m-d', strtotime($LL_PaymentDate))) ?>">
							<input type="text" class="hide" id="PaymentDate" name="PaymentDate" value="<?= ($process == 1 ? date('Y-m-d') : date('Y-m-d', strtotime($LL_PaymentDate))) ?>">
						</td>
					</tr>
					<tr>
						<td class="right">OR#:&nbsp;&nbsp;</td>
						<td><input type="text" id="LL_ORNo" name="LL_ORNo" value="<?= ($process == 1 ? '' : $LL_ORNo) ?>"></td>
					</tr>
					<tr>
						<td class="right">CR#:&nbsp;&nbsp;</td>
						<td><input type="text" id="LL_CRNo" name="LL_CRNo" value="<?= ($process == 1 ? '' : $LL_CRNo) ?>"></td>
					</tr>
					<tr>
						<td class="right">PR#:&nbsp;&nbsp;</td>
						<td><input type="text" id="PRNo" name="PRNo"></td>
					</tr>
					<tr>
						<td class="right">RFP#:&nbsp;&nbsp;</td>
						<td><input type="text" id="RFW_NO" name="RFW_NO" value="<?= ($process == 1 ? '' : $RFW_NO) ?>"></td>
					</tr>
					<tr>
						<td class="right">CV#&nbsp;&nbsp;</td>
						<td><input type="text" id="CVNo" name="CVNo" value="<?= ($process == 1 ? '' : $CVNo) ?>"></td>
					</tr>
					<tr>
						<td class="right">Check#&nbsp;&nbsp;</td>
						<td><input type="text" id="LL_CheckNo" name="LL_CheckNo" value="<?= ($process == 1 ? '' : $LL_CheckNo) ?>"></td>
					</tr>
					<tr>
						<td class="right">Amount Withdrawn:&nbsp;&nbsp;</td>
						<td>
							<input type="text" id="LL_AmountCash_Payment" name="LL_AmountCash_Payment" value="<?= ($process == 1 ? '' : ($LL_AmountCash_Payment == 0 ? '' : $LL_AmountCash_Payment)) ?>">
							<input type="hidden" id="AmountCash_Payment" name="AmountCash_Payment" value="<?= ($process == 1 ? '' : ($LL_AmountCash_Payment == 0 ? '' : $LL_AmountCash_Payment)) ?>">
						</td>
					</tr>
					<tr>
						<td class="right">Refund:&nbsp;&nbsp;</td>
						<td>
							<input type="text" id="LL_Refund" name="LL_Refund" value="<?= ($process == 1 ? '' : ($LL_Refund == 0 ? '' : $LL_Refund)) ?>">
							<input type="hidden" id="Refund" name="Refund" value="<?= ($process == 1 ? '' : ($LL_Refund == 0 ? '' : $LL_Refund)) ?>">
						</td>
					</tr>
					<tr>
						<td colspan="2" style="padding-left:20px;">Arrears/Remarks:&nbsp;&nbsp;</td>
					</tr>
					<tr>
						<td class="right" colspan="2">
							<input type="text" id="LL_Remarks" name="LL_Remarks" value="<?= ($process == 1 ? '' : $LL_Remarks) ?>">
							<input id="lh_balance" type="hidden" name="lh_balance" value="<?= $lh_balance ?>" />
							<input id="lh_payment" type="hidden" name="lh_payment" value="<?= $lh_payment ?>" />
							<input id="for_refund" type="hidden" name="for_refund" value="<?= $for_refund ?>" />
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>
							<input id="postButton" type="button" name="postButton" value="Post">
							<input id="cancelButton" type="button" name="cancelButton" value="Cancel">
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</form>
</body>