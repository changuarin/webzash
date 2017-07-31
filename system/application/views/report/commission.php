<?php

ini_set('display_errors', 'yes');
$this->load->helper('code');

echo trims('
<table border="0" cellpadding="5" class="simple-table collection-table" width="100%">
	<thead>
		<tr valign="center"height="30px">
			<th style="width: 1px;">&nbsp;</th>
			<th>Loan I.D.</th>
			<th style="text-align: center;">Loan Date</th>
			<th>Client Name</th>
			<th style="text-align: right;">Monthly Payment</th>
			<th style="text-align: center;">Terms</th>
			<th style="text-align: center;">Loan Type</th>
			<th style="text-align: right;">CFP</th>
			<th style="text-align: right;">CB</th>
		</tr>
	</thead>
');

if(!empty($datas)):

	echo'<tbody>';
	
	$cfp_amount = 0; $cb_amount = 0; $gt_cfpamount = 0; $gt_cbamount = 0; $ctr = 1; $ctrlimit = 1;
	$isgrandtotal=FALSE;
	foreach($datas as $d):
		
		if(strpos($d['ai_name'], 'OFFICE') === false):
		
		$ai_rate = $d['ai_rate'] / 100;
		$cb_amount = $d['lh_monthlyamort'] * $d['lh_terms'] * $ai_rate;
		$cfp_amount = $d['lh_monthlyamort'] * $d['lh_terms'] * $ai_rate;
		echo trims("
			<tr valign='center'height='30px'>
				<td style='text-align: right;'>" . $ctr . ")</td>
				<td>" . $d['ai_name'] . "</td>
				<td style='text-align: center;'>" . date('m/d/Y', strtotime($d['lh_loandate'])) . "</td>
				<td>" . $d['ci_name'] . "</td>
				<td style='text-align: right;'>" . number_format($d['lh_monthlyamort'], 2) . "</td>
				<td style='text-align: center;'>" . $d['lh_terms'] . "</td>
				<td style='text-align: center;'>" . $d['lh_loantrans'] . "</td>
				<td style='text-align: right;'>" . number_format($cfp_amount, 2) . "</td>
				<td style='text-align: right;'>" . number_format($cb_amount, 2) . "</td>
			</tr>
		");
		
		$cfp_amount += $cfp_amount;
		$gt_cfpamount += $cfp_amount;
		$cb_amount += $cb_amount;
		$gt_cbamount += $cb_amount;
		$ctr++;
		
		if($ctrlimit == 25 && isset($report)):
		
			$isgrandtotal = TRUE;
			echo trims("
				</tbody>
				<tfooter>
					<tr class='total-area' valign='center'height='30px'>
						<td class='bold' colspan='8' style='text-align:right ;'>" . (
						isset($isreport)?
							($isgrandtotal?
								'Sub Total'
							:
								'Total')
						:
							''
						) . "</td>
						<td class='bold' style='text-align: right;'>" . number_format($gt_cfpamount, 2) . "</td>
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
						<th style="width: 1px;">&nbsp;</th>
						<th>Loan I.D.</th>
						<th style="text-align: center;">Loan Date</th>
						<th>Client Name</th>
						<th style="text-align: right;">Monthly Payment</th>
						<th style="text-align: center;">Terms</th>
						<th style="text-align: center;">Loan Type</th>
						<th style="text-align: right;">CFP</th>
						<th style="text-align: right;">CB</th>
					</tr>
				</thead>
			');
			
			$ctrlimit = 1;
			$cfp_amount = 0;
			$cb_amount = 0;
		else:
			$ctrlimit++;
		endif;
		
		endif;
		
	endforeach;
	
	echo '</tbody><tfooter>';
	
	if($gt_cfpamount>0&&isset($isreport)&&$isgrandtotal):
		echo trims("
			<tr class='total-area' valign='center'height='30px'>
				<td class='bold' colspan='8' style='text-align:right;'>" . (
					isset($isreport)?
							($isgrandtotal?
								'Sub Total'
							:
								'Total')
						:
							''
				) . "</td>
				<td class='bold' style='text-align: right;'>" . number_format($cfp_amount, 2) . "</td>
			</tr>
		");
	endif;
		
		echo trims("
			<tr class='total-area'>
				<td class='bold' colspan='8' style='text-align:right;'>" . (
				isset($isreport)?
						($isgrandtotal?
							'Grand Total'
						:
							'Total')
					:
						''
				) . "</td>
				<td class='bold' style='text-align: right;'>" . number_format($gt_cfpamount, 2) . "</td>
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
						<th colspan='2' style='text-align:right;'>Noted By:</th>
					</tr>
					<tr style='border:0;'>
						<th colspan='2' style='text-align:right;'>" . strtoupper(substr($preparedby, 0, 3)) . "</th>
						<th colspan='2' style='text-align:right;'>EPC</th>
						<th colspan='2' style='text-align:right;'>PML</th>
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