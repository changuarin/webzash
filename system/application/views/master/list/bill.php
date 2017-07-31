<style>
	body,table{
		font-family: 'Arial';
		font-size: 12px;
		cursor: default;
		margin: 0;
		padding: 0;
	}
	
	input {
		border: 1px solid #0099cc;
	}
	
	tr td, tr th{
		text-align: left;
		padding-right: 4px;
		padding-left: 4px;
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
		
	#bill_amt {
		margin-left: 5px;
		text-align: center;
		width: 80px;
	}
</style>
</script>
<body class="nomargin">
	<form method="post" action="../insert_billing" id="test" name="test">
		<table width="100%">
			<tr>
				<th colspan='8'>
					<select name="month">
					<?php
						
					for($i = 0; $i <= 11; $i++):
						$date = strtotime('Y-m-01');
						$month_name = date('F', strtotime('+' . $i . ' month', $date));
						$month = date('m', strtotime('+' . $i . ' month', $date));
						echo '<option value="' . $month . '" ' . ($month == date('m') ? 'selected="selected"' : '') . '>' . $month_name . '</option>';
					endfor;
					
					?>
					</select>
					<input class="center w2" type="text" name="year" value="<?= date('Y') ?>" />
					<select id="bill_type" name="bill_type">
						<option value="1">COLLECTION</option>
						<option value="2">ADJUSTMENT</option>
						<option value="3">DUE TO CLIENT</option>
						<option value="4">PAYMENT</option>
						<option value="5">REMITTANCE</option>
					</select>
					<input id='acctno' type="hidden" name="acctno" value="<?= $acctno ?>" />
					<input id='billButton' type="submit" name="billButton" value="Submit" />
				</th>
			</tr>
			<tr>
				<th class="center">&nbsp;</th>
				<th>&nbsp;</th>
				<th>PN</th>
				<th class="right">Balance</th>
				<th class="right">MA</th>
				<th class="right">Terms</th>
				<th>Duration</th>
				<th>Remarks</th>
			</tr>
			<?php
			
			if($datas)
			{
				$j=0;
				foreach($datas as $d)
				{
					$day = date('d', strtotime($d['LH_StartDate'])).' / '.
						date('d', strtotime($d['LH_EndDate']));
					$startStr = date('F', strtotime($d['LH_StartDate']));
					$endStr = date('F', strtotime($d['LH_EndDate']));
					$startMo = substr($startStr, 0, 3);
					$endMo = substr($endStr, 0, 3);
					$startMoYr = $startMo.' '.date('Y', strtotime($d['LH_StartDate']));
					$endMoYr = $endMo.' '.date('Y', strtotime($d['LH_EndDate']));
					echo "
					<tr class='ccid tag$j'>
						<td class='center'><input type='checkbox' name='loan[]' value='{$d['LH_PN']}' /></td>
						<td class='center'>{$d['LH_IsTop']}</td>
						<td title='".date('F d, Y',strtotime($d['LH_LoanDate']))."'>{$d['LH_PN']}</td>
						<td class='right'>{$d['LH_Balance']}</td>
						<td class='right'>{$d['LH_MonthlyAmort']}</td>
						<td class='right'>{$d['LH_Terms']}</td>
						<td title='$day'>{$startMoYr} - {$endMoYr}</td>
						<td>{$d['LH_LoanTrans']}</td>
					</tr>";   
					$j=$j?0:1;
				}
			}
			
			?>
		</table>
	</form>
</body>
<script type="text/javascript" src="<?= base_url() ?>system/application/assets/js/jquery.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('#bill_type').change(function() {
			var billtype = $(this).val()
			if(billtype == 4)
			{
				$('#bill_type').after('<input type="text" id="bill_amt" name="bill_amt" value="0" />');
				$('#bill_amt').focus();
			} else {
				$('#bill_amt').remove();
			}
		});
	});
</script>