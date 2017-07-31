<style type="text/css">
	body, table {
		font-family: Century Gothic, sans-serif;
		font-size: 14px;
	}
	
	input[type=button] {
		border: 1px solid #0099cc;
	}
</style>
<body>
	<div id="menu">
		<input id="print-btn" type="button" value="Print">
		&nbsp;
		<input id="process-btn" type="button" value="Process">
	</div>
	<table style="width: 100%;" cellpadding="0" cellspacing="0">
		<tbody>
			<tr>
				<td style="height: <?= $x[0] ?>;" colspan="2">
					&nbsp;
				</td>
			</tr>
			<tr>
				<td class="date" style="padding-left: <?= $y[0] ?>;width: 40%;"></td>
				<td class="verification" style="padding-right: 30px;text-align: right;width: 13%;"></td>
			</tr>
			<tr style="height: 20px;">
				<td class="name" style="padding-left: <?= $y[0] ?>;padding-top: 3px;"></td>
				<td class="fully_paid_date" style="padding-right: 30px;text-align: right;"></td>
			</tr>
			<tr style="height: 20px;">
				<td class="cp_bankbranch" style="padding-left: <?= $y[0] ?>;padding-top: 3px;"></td>
				<td class="verified_by" style="padding-right: 30px;text-align: right;"></td>
			</tr>
			<tr style="height: 20px;">
				<td class="cp_pensiontype" style="padding-left: <?= $y[0] ?>;padding-top: 3px;"></td>
				<td class="cleared_apar" style="padding-right: 30px;text-align: right;"></td>
			</tr>
			<tr style="height: 20px;">
				<td class="cp_bankacctno" style="padding-left: <?= $y[0] ?>;padding-top: 3px;"></td>
				<td class="other_acctabilities" style="padding-right: 30px;text-align: right;"></td>
			</tr>
			<tr style="height: 20px;">
				<td class="transaction_prior_to" style="padding-left: <?= $y[0] ?>;padding-top: 3px;"></td>
				<td class="accounting" style="padding-right: 30px;text-align: right;"></td>
			</tr>
			<tr style="height: 20px;">
				<td class="prior_to_release" style="padding-left: <?= $y[0] ?>;padding-top: 3px;"></td>
				<td class="other_remarks" style="padding-right: 30px;text-align: right;"></td>
			</tr>
			<tr>
				<td colspan="2" style="height: 20px;">
					&nbsp;
				</td>
			</tr>
			<tr style="height: 25px;vertical-align: bottom;">
				<td colspan="4">
					<table style="width: 100%;">
						<tbody>
							<tr style="vertical-align: bottom;">
								<td style="padding-left: 110px;width: 50%;">
									<?= $user_name ?>
								</td>
								<td class="approved_for_release" style="padding-right: 120px;text-align: right;width: 50%;">
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr style="height: 85px;vertical-align: bottom;">
				<td class="date" colspan="4" style="padding-right: 155px;text-align: center;">
				</td>
			</tr>
			<tr style="height: <?= $x[1] ?>;vertical-align: bottom;">
				<td colspan="2">
					&nbsp;
				</td>
			</tr>
		</tbody>
	</table>
	
	<table id="table-bottom" style=";width: 100%;" cellpading="0" cellspacing="0">
		<tbody>
			<tr>
				<td style="width: 50%;">
				</td>
				<td class="cp_bankacctno" style="padding-left: 200px;width: 50%;">
				</td>
			</tr>
			<tr>
				<td style="width: 50%;">
				</td>
				<td class="transaction_prior_to" style="padding-left: 200px;width: 50%;">
				</td>
			</tr>
			<tr>
				<td style="width: 50%;">
				</td>
				<td class="prior_to_release" style="padding-left: 200px;width: 50%;">
				</td>
			</tr>
			<tr style="height: 50px;">
				<td class="name" style="padding-left: 100px;width: 50%;">
				</td>
				<td class="authorized_person" style="padding-right: 120px;text-align:right;width: 50%;">
				</td>
			</tr>
		</tbody>
	</table>
</body>
<script src="<?= base_url() ?>system/application/assets/js/jquery-2.1.3.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>system/application/assets/js/jquery.validate.min.js" type="text/javascript"></script>
<script type="text/javascript">
	function formatDate(date)
	{
		var month_array = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
		var date = new Date(date);
		var month = month_array[date.getMonth()];
		var formatted_date = month + ' ' + date.getDate() + ', ' + date.getFullYear();
		return formatted_date;
	}
	
	var main = function() {
		$('#print-btn').click(function() {
			$('#menu').hide();
			window.print();
			$('#menu').show();
		})
		
		$('#process-btn').click(function() {
			var do_process = confirm("Process ATM/PB release?");
			if (do_process == true) {
				$('#atmpb_release_form', window.opener.document).validate();
				$('#atmpb_release_form', window.opener.document).submit();
				window.close();
			} else {
				return false;
			}
		})
		
		var date = $('#date', window.opener.document).val()
		$('.date').html(formatDate(date));
		$('.verification').html($('#verification', window.opener.document).val());
		$('.name').html($('#name', window.opener.document).val());
		var full_paid_date = $('#fully_paid_date', window.opener.document).val();
		$('.fully_paid_date').html(formatDate(full_paid_date));
		$('.cp_bankbranch').html($('#cp_bankbranch', window.opener.document).val());
		$('.verified_by').html($('#verified_by', window.opener.document).val());
		$('.cp_pensiontype').html($('#cp_pensiontype', window.opener.document).val());
		$('.cleared_apar').html($('#cleared_apar', window.opener.document).val());
		$('.cp_bankacctno').html($('#cp_bankacctno', window.opener.document).val());
		$('.other_acctabilities').html($('#other_acctabilities', window.opener.document).val());
		var transaction_prior_to = $('#transaction_prior_to', window.opener.document).val();
		$('.transaction_prior_to').html(formatDate(transaction_prior_to));
		$('.accounting').html($('#accounting', window.opener.document).val());
		var prior_to_release = 'P ' + $('#prior_to_release', window.opener.document).val()
		$('.prior_to_release').html(prior_to_release);
		$('.other_remarks').html($('#other_remarks', window.opener.document).val());
		$('.approved_for_release').html($('#approved_for_release', window.opener.document).val());
		$('.prior_to_release').html($('#prior_to_release', window.opener.document).val());
		$('.authorized_person').html($('#authorized_person', window.opener.document).val());
	}
	
	$(document).ready(main);
</script>