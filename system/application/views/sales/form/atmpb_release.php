<style type="text/css">
	/* General styling */
	body, table{
		font-family: 'Arial';
		font-size: 12px;
	}
	
	/* Alignment styling */
	.center {
		text-align: center;
	}
	
	.right {
		text-align: right;
	}
	
	/* Button styling */
	input[type=text]:enabled {
		background-color: #ffff99;
		border: 1px solid #ff0000;
	}
	
	input[disabled], input[readonly], input[type=button] {
		background-color: #fff !important;
		border: 1px solid #0099cc !important;
	}
	
	input[type=text] {
		background-color: #ffff99;
		border: 1px solid #ff0000;
	}
	
	/* Heading */
	.tabHeadings {
		background-color: #09c;
		color: #fff;
		font-size: 12px;
		margin-top: 10px;
		margin-bottom: 10px;
		padding: 10px 5px;
	}
	
	/* Table styling */
	tr td {
		padding-left: 5px;
	}
	
	#atmpb_release_table {
		width: 480px;
	}
</style>
<body>
	<h3 class="tabHeadings"tag="">ATM/PB Release Form</h3>
	<form id="atmpb_release_form" method="post" action="atmpb_release_process">
		<table id="record_table">
			<tr>
				<td>
					Client ID
					&nbsp;
					<input id="ci_acctno" type="text" name="ci_acctno" required />
				</td>
				<td>
					Client Name
					&nbsp;
					<input id="ci_name" type="text" name="ci_name" />
				</td>
			</tr>
		</table>
		
		<fieldset id="atmpb_release_table">
			<table>
				<tr>
					<td class="right">
						Control No.
					</td>
					<td>
						<input id="ctrl_no" class="center" type="text" name="ctrl_no" />
					</td>
				</tr>
				<tr>
					<td class="right">
						Date
					</td>
					<td>
						<input id="date" class="center" type="text" name="date" value="<?= date('Y-m-d') ?>" required />
					</td>
				</tr>
				<tr>
					<td class="right">
						Verification/Instruction
					</td>
					<td>
						<input id="verification" type="text" name="verification" />
					</td>
				</tr>
				<tr>
					<td class="right">
						Name
					</td>
					<td>
						<input id="name" type="text" name="name" required />
					</td>
				</tr>
				<tr>
					<td class="right">
						Loan fully paid as of
					</td>
					<td>
						<input id="fully_paid_date" class="center" type="text" name="fully_paid_date" value="<?= date('Y-m-d') ?>" />
					</td>
				</tr>
				<tr>
					<td class="right">
						Bank/Branch
					</td>
					<td>
						<input id="cp_bankbranch" type="text" name="cp_bankbranch" required />
					</td>
				</tr>
				<tr>
					<td class="right">
						Verified/Certified by
					</td>
					<td>
						<input id="verified_by" type="text" name="verified_by" />
					</td>
				</tr>
				<tr>
					<td class="right">
						Type of Pension
					</td>
					<td>
						<input id="cp_pensiontype" type="text" name="cp_pensiontype" required />
					</td>
				</tr>
				<tr>
					<td class="right">
						Cleared of AP/AR
					</td>
					<td>
						<input id="cleared_apar" type="text" name="cleared_apar" />
					</td>
				</tr>
				<tr>
					<td class="right">
						S/A No.
					</td>
					<td>
						<input id="cp_bankacctno" class="center" type="text" name="cp_bankacctno" required />
					</td>
				</tr>
				<tr>
					<td class="right">
						Other Accountabilities
					</td>
					<td>
						<input id="other_acctabilities" type="text" name="other_acctabilities" />
					</td>
				</tr>
				<tr>
					<td class="right">
						Transaction prior to
					</td>
					<td>
						<input id="transaction_prior_to" class="center" type="text" name="transaction_prior_to"  value="<?= date('Y-m-d') ?>" />
					</td>
				</tr>
				<tr>
					<td class="right">
						Accounting
					</td>
					<td>
						<input id="accounting" type="text" name="accounting" />
					</td>
				</tr>
				<tr>
					<td class="right">
						Prior to release
					</td>
					<td>
						<input id="prior_to_release" class="center" type="text" name="prior_to_release" />
					</td>
				</tr>
				<tr style="display: none;">
					<td class="right">
						Time prior to release
					</td>
					<td>
						<input id="time_prior_to_release" class="center" type="text" name="time_prior_to_release" value="<?= date('h:i A') ?>" />
					</td>
				</tr>
				<tr>
					<td class="right">
						Other Remarks/Instructions
					</td>
					<td>
						<input id="other_remarks" type="text" name="other_remarks" />
					</td>
				</tr>
				<tr>
					<td class="right">
						Authorized Person
					</td>
					<td>
						<input id="authorized_person" type="text" name="authorized_person" />
					</td>
				</tr>
				<tr>
					<td class="right">
						Approved for release
					</td>
					<td>
						<input id="approved_for_release" type="text" name="approved_for_release" />
					</td>
				</tr>
				<tr>
					<td>
						&nbsp;
					</td>
					<td>
						<input id="preview-btn" type="button" value="Preview" />
					</td>
				</tr>
			</table>
		</fieldset>
	</form>
</body>
<script src="<?= base_url() ?>system/application/assets/js/jquery-2.1.3.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>system/application/assets/js/numeral.js" type="text/javascript"></script>
<script type="text/javascript">
	function popupWindow(height, width, url)
	{
		var top = (screen.height/2) - (height/2);
		var left = (screen.width/2) - (width/2);
		return window.open(url, 'popupWindow', 'width=' + width + ', height=' + height + ', top=' + top + ', left=' + left + ', scrollbars=no').focus();
	}
	
	var main = function() {
		$('#prior_to_release').blur(function() {
			var value = $(this).val();
			var amount = numeral(value).format('0,0.00');
			$(this).val(amount);
		});
		
		$('#preview-btn').click(function() {
			height = 640; width = 960;
			url = 'atmpb_release_print';
			popupWindow(height, width, url);
		});
		
		$('input[type=text]').keyup(function() {
			var string = $(this).val().toUpperCase();
			$(this).val(string);
		})
		
		$('input[type=button], input[type=text]').prop('disabled', true);
	}
	
	$(document).ready(main);
</script>