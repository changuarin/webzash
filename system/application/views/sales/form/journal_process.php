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
	
	#joutnal_form_table {
		height: 400px;
	}
</style>
<body>
	<h3 class="tabHeadings"tag="">Journal Process</h3>
	<form id="journal_form" method="post" action="../process_journal">
		<table id="joutnal_form_table">
			<tr>
				<td rowspan="12" style="border: 1px solid #333;">
					<iframe id="client_list" class="sales_journal" src="../../master/client_list" height="100%" width="100%" frameborder="0"></iframe>
				</td>
				<td class="right">
					Client ID
				</td>
				<td>
					<input class="center" id="ci_acctno" type="text" name="ci_acctno" readonly />
					<input class="center" id="ai_refno" type="text" name="ai_refno" value="<?= $ai_refno ?>" readonly />
				</td>
			</tr>
			<tr>
				<td class="right">
					Client Name
				</td>
				<td>
					<input id="ci_name" type="text" name="ci_name" readonly />
				</td>
			</tr>
			<tr>
				<td class="right">
					PN No.
				</td>
				<td>
					<select id="pn_no" name="pn_no"></select>
				</td>
			</tr>
			<tr>
				<td class="right">
					Process Date
				</td>
				<td>
					<input class="center" id="process_date" type="text" name="process_date" value="<?= date('Y-m-d') ?>" />
				</td>
			</tr>
			<tr>
				<td class="right">
					Amount
				</td>
				<td>
					<input class="center" id="payment_amount" type="text" name="payment_amount" />
				</td>
			</tr>
			<tr>
				<td>
					&nbsp;
				</td>
				<td>
					<input id="submit-btn" type="button" value="Submit" />
					<input id="cancel-btn" type="button" value="Cancel" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
					&nbsp;
				</td>
			</tr>
			<tr>
				<td colspan="2">
					&nbsp;
				</td>
			</tr>
			<tr>
				<td colspan="2">
					&nbsp;
				</td>
			</tr>
			<tr>
				<td colspan="2">
					&nbsp;
				</td>
			</tr>
			<tr>
				<td colspan="2">
					&nbsp;
				</td>
			</tr>
			<tr>
				<td colspan="2">
					&nbsp;
				</td>
			</tr>
		</table>
	</form>
</body>
<script src="<?= base_url() ?>system/application/assets/js/jquery-2.1.3.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>system/application/assets/js/numeral.js" type="text/javascript"></script>
<script type="text/javascript">
	var main = function() {
		$('#payment_amount').blur(function() {
			var value = $(this).val();
			var amount = numeral(value).format('0,0.00');
			$(this).val(amount);
		});
		
		$('#submit-btn').click(function() {
			var a = confirm("Proceed to process journal?");
			if (a == true) {
				$('#journal_form').submit();
			} else {
				return false;
			} 
		});
		
		$('#cancel-btn').click(function() {
			window.close();
		});
		
		$('input[type=text]').keyup(function() {
			var string = $(this).val().toUpperCase();
			$(this).val(string);
		});
	}
	
	$(document).ready(main);
</script>