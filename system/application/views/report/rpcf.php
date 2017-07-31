<?php

ini_set('display_errors', 'yes');
$this->load->helper('code');

echo trims('
<table border="0" cellpadding="5" class="simple-table collection-table" width="100%">
	<thead>
		<tr valign="center"height="30px">
			<th width="1">&nbsp;</th>
			<th>Refund Date</th>
			<th>RFP No.</th>
			<th>Client Name</th>
			<th>Bank/Branch</th>
			<th style="text-align:right;">Amount</th>
			<th>Remarks</th>
		</tr>
	</thead>
');

if(!empty($datas)):

	echo'<tbody>';
	
	$refunddue=0;$gtrefunddue=0;$ctr=1;$ctrlimit=1;
	$isgrandtotal=FALSE;
	foreach ($datas as $d):
		
		echo trims("
			<tr valign='center'height='30px'>
				<td style='text-align: right;'>" . $ctr . ")</td>
				<td>" . date('Y-m-d', strtotime($d->transdate)) . "</td>
				<td>" . $d->transrefno . "</td>
				<td>" . $d->ci_name . "</td>
				<td>" . $d->ci_bankbranch . "</td>
				<td style='text-align:right;'>" . number_format($d->refunddue, 2) . "</td>
				<td>" . $d->remarks . "</td>
			</tr>
		");
		
		$refunddue += $d->refunddue;
		$gtrefunddue += $d->refunddue;
		$ctr++;
		
		if($ctrlimit == 25 && isset($report)):
		
			$isgrandtotal = TRUE;
			echo trims("
				</tbody>
				<tfooter>
					<tr class='total-area' valign='center'height='30px'>
						<td class='bold' colspan='5' style='text-align:right ;'>" . (
						isset($isreport)?
							($isgrandtotal?
								'Sub Total'
							:
								'Total')
						:
							''
						) . "</td>
						<td class='bold' style='text-align: right;'>" . number_format($refunddue, 2) . "</td>
						<td>&nbsp;</td>
					</tr>
				</tfooter>
			</table>
			");
			
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
					<tr valign="center"height="30px">
						<th width="1">&nbsp;</th>
						<th>Refund Date</th>
						<th>RFP No.</th>
						<th>Client Name</th>
						<th>Bank/Branch</th>
						<th style="text-align:right;">Amount</th>
						<th>Remarks</th>
					</tr>
				</thead>
			');
			
			$ctrlimit=1;
			$refunddue=0;
		else:
			$ctrlimit++;
		endif;
		
	endforeach;
	
	echo '</tbody><tfooter>';
	
	if($refunddue>0&&isset($isreport)&&$isgrandtotal):
		echo trims("
			<tr class='total-area' valign='center'height='30px'>
				<td class='bold' colspan='5' style='text-align:right;'>" . (
					isset($isreport)?
							($isgrandtotal?
								'Sub Total'
							:
								'Total')
						:
							''
				) . "</td>
				<td class='bold' style='text-align: right;'>" . number_format($refunddue, 2) . "</td>
				<td>&nbsp;</td>
			</tr>
		");
	endif;
	
	echo trims("
		<tr class='total-area'>
			<td class='bold' colspan='5' style='text-align:right;'>" . (
			isset($isreport)?
					($isgrandtotal?
						'Grand Total'
					:
						'Total')
				:
					''
			) . "</td>
			<td class='bold' style='text-align: right;'>" . number_format($gtrefunddue, 2) . "</td>
			<td>&nbsp;</td>
		</tr>" .
		(isset($preparedby)?
		"
		<table style='padding-top:50px'align='center'width='60%'>
			<tbody>
				<tr style='border:0;'>
					<th>&nbsp;</th>
					<th style='text-align:right;'>Prepared By:</th>
					<th>&nbsp;</th>
					<th style='text-align:right;'>Checked By:</th>
					<th>&nbsp;</th>
					<th colspan='2' style='text-align:right;'>Posted By:</th>
				</tr>
				<tr style='border:0;'>
					<th colspan='2' style='text-align:right;'>" . strtoupper(substr($preparedby, 0, 3)) . "</th>
					<th colspan='2' style='text-align:right;'>EPC</th>
					<th colspan='2' style='text-align:right;'>ACR</th>
				</tr>
			</tbody>
		</table>
		"
		:
		""
		) . "
	");
	
	echo '</tfooter>';
	
endif;

echo '</table>';

?>