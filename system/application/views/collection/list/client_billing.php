<style type="text/css">
	body, table {
		font-family: 'Arial', sans-serif;
		font-size: 12px;
	}
	
	span {
		color: #ff0000;
	}
	
	input {
		border: 1px solid #0099cc;
	}
	
	tr td, tr th {
		font-weight: 500;
		padding-left: 5px;
		padding-right: 5px;
	}
	
	#client_billing_list {
		border-collapse: collapse;
	}
	
	#client_billing_list tr td, #client_billing_list tr th {
		border: 1px solid #000;
		text-align: center;
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
	
	.tag1 {
		background-color: #ff9;
	}
	
	.tag1:hover {
		background-color: #99ffff;
	}
	
	.tag0 {
		background-color: #f0f0f0;
	}
	
	.tag0:hover {
		background-color: #99ffff;
	}
</style>
<body>
	<div class="menu">
		<form id="menu_form" method="post">
			<table>
				<tbody>
					<tr>
						<td class="right">
							<?= $client->name ?>
						</td>
						<td class="right">
							<?= $ci_acctno ?>
							&nbsp;
							<span>(<?= $client_pension->cp_pensiontype ?>)</span>
						</td>
						<td>
							<?= $client_pension->cp_bankbranch ?>
						</td>
					</tr>
					<tr>
						<td class="left" colspan="3">
							<select id="lh_pn" name="lh_pn">
							<?php
							
							if(!empty($loans)):
								foreach($loans as $data):
									echo "<option value='" . $data->lh_pn . "' " . ($lh_pn == $data->lh_pn ? "selected=='selected'" : "" ) . ">" . $data->lh_pn . " <span>(" . $data->lh_loantrans . ")</span></option>";
								endforeach;
							endif;
							
							?>
							</select>
							&nbsp;
							<input id="bill-btn" type="button" value="Bill" />
							&nbsp;
							<input id="close-btn" type="button" value="Close" />
							&nbsp;
							<input id="delete-btn" type="button" value="Delete" />
							&nbsp;
							<input id="bill_month-btn" type="button" value="Bill Month" />
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
	<div>
		<form id="client_billing_form" method="post">
			<table id="client_billing_list">
				<tbody>
					<tr style="background-color: #999">
						<th>Branch</th>
						<th>Loan Trans</th>
						<th>Bill Date</th>
						<th>Duration</th>
						<th>Terms</th>
						<th>Payment</th>
						<th>Status</th>
						<th>
							<input id="main-chkbox" type="checkbox" value="" />
							<input id="ci_acctno" name="ci_acctno" type="hidden" value="<?= $ci_acctno ?>" />
							<input name="lh_pn" type="hidden" value="<?= $lh_pn ?>" />
						</th>
					</tr>
					<?php
					
					if(!empty($results)):
						$i = 1;
						foreach($results as $data):
							echo "
							<tr class='tag" . $i . "'>
								<td>" . $data->branchcode . "</td>
								<td>" . $data->loantrans . "</td>
								<td><span>" . $data->billdate . "</span></td>
								<td>" . $data->duration . "</td>
								<td>" . $data->terms . "</td>
								<td>" . number_format($data->amtodrawn, 2, '.', ',') . "</td>
								<td>" . $data->status . "</td>
								<td><input name='billing[]' type='checkbox' value='" . $data->bill_id . "' /></td>
							</tr>
							";
							
							$i = $i ? 0 : 1;
						endforeach;
					endif;
					
					?>
				</tbody>
			</table>
		</form>
	</div>
</body>
<script src="<?= base_url() ?>system/application/assets/js/jquery-2.1.3.min.js" type="text/javascript"></script>
<script type="text/javascript">
	var main = function() {
		$('#bill-btn').click(function() {
			var action = '../../insert_billing';
			$('#client_billing_form').prop('action', action);
			$('#client_billing_form').submit();
		});
		
		$('#bill_month-btn').click(function() {
			var action = '../../../master/billinglist/' + $('#ci_acctno').val();
			$('#client_billing_form').prop('action', action);
			$('#client_billing_form').submit();
		});
		
		$('#close-btn').click(function() {
			var action = '../../close_billing';
			$('#client_billing_form').prop('action', action);
			$('#client_billing_form').submit();
		});
		
		$('#delete-btn').click(function() {
			var action = '../../delete_billing';
			$('#client_billing_form').prop('action', action);
			$('#client_billing_form').submit();
		});
		
		$('#lh_pn').change(function() {
			var action = $(this).val();
			$('#menu_form').prop('action', action);
			$('#menu_form').submit();
		});
		
		$('#main-chkbox').change(function() {
			var is_checked = $(this).is(':checked');
			$('input[type=checkbox]').prop('checked', is_checked);
		});
	}
	
	$(document).ready(main);
</script>