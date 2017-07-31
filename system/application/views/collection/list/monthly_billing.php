<style type="text/css">
	body, form, table {
		font-family: 'Arial';
		font-size: 11px;
		cursor: default;
		margin: 0;
		padding: 0;
	}
	
	input {
		border: 1px solid #0099cc;
	}
	
	span {
		color: #ff0000;
	}
	
	table 
	{
		border-collapse: collapse;
		width: 1360px;
	}
	
	tr th, tr td {
		border: 1px solid #000;
		padding: 5px;
	}
	
	.center {
		text-align: center;
	}
	
	.right {
		text-align: right;
	}
	
	.tag1 {
		background-color: #d0d0d0;
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
	
	.client_ledger {
		cursor: pointer;
	}
	
	.client_ledger:hover {
		text-decoration: underline;
	}
	
	.filter-table{
		position: fixed;
		margin-top: 0px;
		background-color: #fff;
		width: 100%;
		padding: 2px;
	}
	
	.trfixed{
		padding-bottom: 10px;
	}
</style>
<body>
	<table id="monthly_billing_list">
		<tbody>
			<tr>
				<td class="trfixed">&nbsp;</td>
			</tr>
			<tr>
				<th>Date</th>
				<th>Name</th>
				<th>PType</th>
				<th>Bank Branch</th>
				<th>Mo. Pension</th>
				<th>Amt To Be Withdrawn</th>
				<th>PN</th>
				<th>Payment</th>
				<th>Duration</th>
				<th>Total Mo. Payment</th>
				<th>Mo. Refund</th>
				<th>O.B.</th>
				<th>&nbsp;</th>
			</tr>
		<?php
		
		echo $ip;
		
		if(!empty($data)):
			$atbw = 0;
			$atd = 0;
			$balance_total = 0;
			$mp = 0;
			$mp_total = 0;
			$refund = 0;
			$refund_total = 0;
			
			$i = 0;
			$j = 1;
			$k = 0;
			$qq = 0;
			
			$ci_acctno = '';
			foreach($data as $data):
				$i = $ci_acctno != $data['ci_acctno'] ? $i ? 0 : 1 : $i;
				
				$bill_day = date('d', strtotime($data['billdate']));
				$bill_day = (int)$bill_day;
				
				if($coll_type == '2'):
					$k++;
				elseif($coll_type == '1'):
					if($ci_acctno != $data['ci_acctno']):
						$atbw += $data['amtobwdrawn'];
						$refund = $data['amtobwdrawn'];
						
						$mp = 0;
						$k++;
					endif;
				endif;
				
				$mp += $data['amtodrawn'];
				$refund -= $data['amtodrawn'];

				$is_collected = 0;				
				$this->db->select('bill_id');
				$this->db->where('cid', $data['ci_acctno']);
				$this->db->where('bill_id LIKE "%' . $data['bill_id'] . '%"');
				$query = $this->db->get('collection_entry');
				if($query->num_rows() > 0):
					$is_collected = 1;
				endif;
				
				echo "
					<tr id='" . $data['ci_acctno'] . "' class='data tag" . $i . " id" . $k . "' tag='id" . $k . "' tagdate='" . $data['billdate'] . "' style='background-color: " . ($is_collected == 1 ? '#f9f7ad' : '') . ";'>
						<td class='center' style='width:3%;'>" . (
							$coll_type == '2' ?
								$bill_day
							:
								$ci_acctno != $data['ci_acctno'] ?
									$bill_day
								:
									"" 
						). "</td>
						<td style='width:20%;'>" . $data['name'] . " <span>(" . $data['cp_pensiontype'] .")</span>" . "</td>
						<td class='center' style='width: 2%;'>" . (
							$coll_type == '2' ?
								$data['paytype']
							:
								$ci_acctno != $data['ci_acctno'] ?
									$data['paytype']
								:
									""
						) . "</td>
						<td style='width:10%;'>" . (
							$coll_type == '2' ?
								$data['bankbranch']
							:
								$ci_acctno != $data['ci_acctno'] ?
									$data['bankbranch']
								:
									""
						) . "</td>
						<td class='right' style='width:5%;'>" . (
							$coll_type == '2' ?
								number_format($data['cp_amount'], 2)
							:
								$ci_acctno != $data['ci_acctno'] ?
									number_format($data['cp_amount'], 2)
								:
									""
						) . "</td>
						<td class='right' style='width:5%;'>" . (
							$coll_type == '2' ?
								number_format($data['cp_amount'], 2)
							:
								$ci_acctno != $data['ci_acctno'] ?
									number_format($data['amtobwdrawn'], 2)
								:
									""
						) . "</td>
						<td id='" . $data['bill_id'] . "' class='client_ledger' style='width:10%;'>" . $data['lh_pn'] . " <span>(" . $data['loantrans'] . ")</span></td>
						<td class='right' style='width:5%;'>" . number_format($data['amtodrawn'], 2, '.', ',') . "</td>
						<td class='center' style='width:13%;'>" . $data['duration'] . "</td>
						<td class='right'  style='width:5%;'>" . number_format($mp, 2, '.', ',') . "</td>
						<td class='right' style='width:6%;'>" . number_format($refund, 2, '.', ',') . "</td>
						<td class='right' style='width:6%;'>" . number_format($data['lh_balance'], 2, '.', ',') . "</td>
						<td style='width:3%;'>" . (
							$coll_type == '2' ?
								"<input class='submit-btn' type='button' value='Submit' />"
							:
								$ci_acctno != $data['ci_acctno'] ?
									"<input class='submit-btn' type='button' value='Submit' />"
								:
									"" 
						) . "</td>
					</tr>
				";
				
				$ci_acctno = $data['ci_acctno'];
				
				$atd += $data['amtodrawn'];
				$balance_total += $data['lh_balance'];
				$mp_total += $data['amtodrawn'];
			
			endforeach;
			
			$refund_total = $atbw - $atd;
			
			echo "
				<tr>
					<td class='right' colspan='4'><b>Total</b></td>
					<td class='right' colspan='2'><b>" . number_format($atbw, 2, '.', ',') . "</b></td>
					<td class='right' colspan='2'><b>" . number_format($atd, 2, '.', ',') . "</b></td>
					<td class='right' colspan='2'><b>" . number_format($mp_total, 2, '.', ',') . "</b></td>
					<td class='right'><b>" . number_format($refund_total, 2, '.', ',') . "</b></td>
					<td class='right'><b>" . number_format($balance_total, 2, '.', ',') . "</b></td>
					<td></td>
				</tr>
			";
			
		endif;
		
		?>
			
		</tbody>
	</table>
</body>
<script src="<?= base_url() ?>system/application/assets/js/jquery-2.1.3.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.filterTable.js"></script>
<script type="text/javascript">
	function popupWindow(height, width, url)
	{
		var top = (screen.height/2) - (height/2);
		var left = (screen.width/2) - (width/2);
		return window.open(url, 'popupWindow', 'width = ' + width + ', height = ' + height + ', top = ' + top + ', left = ' + left + ',scrollbars=yes').focus();
	}
	
	var main = function() {
		$('.client_ledger').click(function() {
			height = 660; width = 880;
			var ci_acctno = $(this).parent().prop('id');
			var data = $(this).text();
			var loan_detail = data.split(' ');
			var lh_pn = loan_detail[0];
			url = '../../../../../master/clientLedgerList/' + ci_acctno + '/' + lh_pn;
			popupWindow(height, width, url);
		});
		
		$('.submit-btn').click(function() {
			var ci_acctno = $(this).parent().parent().prop('id');
			var bank_branch = $(this).parent().parent().children().eq(3).text();
			var bill_date = $(this).parent().parent().attr('tagdate');
			height = 450; width = 700;
			
			var data = $(this).text();
			var loan_detail = data.split(' ');
			var lh_pn = loan_detail[0];
			
			var c = $(this).parent().parent().attr('class');
			var d = c.split(' ');
			var bill = '';
			$.each($('.' + d[2]), function() {
				var e = $(this).children().eq(6).prop('id');
				var f = e.split(' ');
				bill += f[0] + '.';
			});
			url = '../../../../../collection/de/' + ci_acctno + '/' + bank_branch + '/' + bill_date + '/' + bill + '';
			popupWindow(height, width, url);
		});
		
		$('#monthly_billing_list').filterTable({
			autofocus: 1,
			placeholder: 'Search Client'
		});
			
		$('input[type=search]').keyup(function() {
			var string = $(this).val().toUpperCase();
			$(this).val(string);
		});
	}
	
	$(document).ready(main);
</script>