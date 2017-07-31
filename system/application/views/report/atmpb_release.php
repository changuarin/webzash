<?php

ini_set('display_errors', 'yes');
$this->load->helper('code');

echo trims('
<table border="0" cellpadding="5" class="simple-table collection-table" width="100%">
	<thead>
		<tr valign="center"height="30px">
			<th width="1">&nbsp;</th>
			<th>Client Name</th>
			<th>Bank/Branch</th>
			<th style="text-align: center;">Date Released</th>
			<th>Pension Type</th>
			<th style="text-align: center;">PB No.</th>
			<th>Remarks</th>
		</tr>
	</thead>
');

if(!empty($datas)):

	echo'<tbody>';
	
	$ctr=1;$ctrlimit=1;
	foreach ($datas as $d):
		echo trims('
			<tr style=\'height: 30px;vertical-align: center;\'>
				<td style=\'text-align: right;\'>' . $ctr . ')</td>
				<td>' . $d->ap_name . '</td>
				<td>' . $d->ap_bank . '</td>
				<td style="text-align: center;">' . date('Y-m-d', strtotime($d->ap_date)) . '</td>
				<td>' . $d->ap_typepension . '</td>
				<td style="text-align: center;">' . $d->ap_pbno . '</td>
				<td>' . $d->ap_otherremarks . '</td>
			</tr>
		');
		
		$ctr++;
		
		if($ctrlimit == 25 && isset($report)):
			echo trims('
				</tbody>
			</table>
			');
			
			echo trims('
				<div id="print-account-name"style="page-break-before: always;margin-top:50px;">
				<span class="value">' . $this->config->item('account_name') .' </span></div>
				<div id="print-account-address"><span class="value">' .
					$this->config->item('account_address') . '</span></div>
				<br />
				<div id="print-report-title"><span class="value">' . $title . '</span></div>
				<div id="print-report-period"><span class="value">' . $reportname . '</span>
				</div>
				<br>
				<table border="0" cellpadding="5" class="simple-table collection-table" width="100%">
					<thead>
						<tr style="height: 30px;vertical-align: center;">
							<th width="1">&nbsp;</th>
							<th>Client Name</th>
							<th>Bank/Branch</th>
							<th style="text-align: center;">Date Released</th>
							<th>Pension Type</th>
							<th style="text-align: center;">PB No.</th>
							<th>Remarks</th>
						</tr>
					</thead>
			');
			$ctrlimit=1;
		else:
			$ctrlimit++;
		endif;
	endforeach;
	
	echo '</tbody><tfooter>';
	
	echo trims(
		(isset($preparedby) ? '
		<table style=\'padding-top:50px\' align=\'center\' width=\'60%\'>
			<tbody>
				<tr style=\'border: 0;\'>
					<th>&nbsp;</th>
					<th style=\'text-align: right;\'>Prepared By:</th>
					<th>&nbsp;</th>
					<th style=\'text-align: right;\'>Checked By:</th>
					<th>&nbsp;</th>
					<th colspan=\'2\' style=\'text-align: right;\'>Posted By:</th>
				</tr>
				<tr style=\'border: 0;\'>
					<th colspan=\'2\' style=\'text-align:right;\'>' . strtoupper(substr($preparedby, 0, 3)) . '</th>
					<th colspan=\'2\' style=\'text-align:right;\'>EPC</th>
					<th colspan=\'2\' style=\'text-align:right;\'>ACR</th>
				</tr>
			</tbody>
		</table>
		'
		:
		''
		)
	);
	
	echo '</tfooter>';
	
endif;

echo '</table>';

?>