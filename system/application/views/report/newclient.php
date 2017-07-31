<?php

ini_set('display_errors', 'yes');
$this->load->helper('code');

echo trims('
<table border="0" cellpadding="5" class="simple-table collection-table" width="100%">
	<thead>
		<tr valign="center"height="30px">
			<th width="1">&nbsp;</th>
			<th>Date</th>
			<th>Client Name</th>
			<th>Bank/Branch</th>
			<th>ATM/PB</th>
			<th style="text-align:right;">Pension</th>
			<th style="text-align:right;">Cash Out</th>
			<th style="text-align:right;">Term</th>
			<th style="text-align:right;">Principal</th>
		</tr>
	</thead>
');

if(!empty($datas)):

	echo'<tbody>';
	
	$netproceeds=0;$principal=0;$gtnetproceeds=0;$gtprincipal=0;$ctr=1;$ctrlimit=1;
	$isgrandtotal=FALSE;
	foreach ($datas as $d):
		
		echo trims("
			<tr valign='center'height='30px'>
				<td style='text-align:right;'>$ctr)</td>
				<td>".date('m/d/Y', strtotime($d['LH_LoanDate']))."</td>
				<td>{$d['CI_Name']}</td>
				<td>{$d['LH_BankBranch']}</td>
				<td>{$d['LH_PaymentType']}</td>
				<td style='text-align:right;'>".number_format($d['LH_BankAmt'],2)."</td>
				<td style='text-align:right;'>".number_format($d['LH_NetProceeds'],2)."</td>
				<td style='text-align:right;'>{$d['LH_Terms']}</td>
				<td style='text-align:right;'>".number_format($d['LH_Principal'],2)."</td>
			</tr>
		");
		
		$netproceeds += $d['LH_NetProceeds'];
		$principal += $d['LH_Principal'];
		$gtnetproceeds += $d['LH_NetProceeds'];
		$gtprincipal += $d['LH_Principal'];
		$ctr++;
		
		if($ctrlimit==25&&isset($report)):
		
			$isgrandtotal = TRUE;
			echo trims("
				</tbody>
				<tfooter>
					<tr class='total-area' valign='center'height='30px'>
						<td class='bold' colspan='6' style='text-align:right;'>".(
						isset($isreport)?
							($isgrandtotal?
								'Sub Total'
							:
								'Total')
						:
							''
						)."</td>
						<td class='bold' style='text-align:right;'>".number_format($netproceeds,2)."</td>
						<td colspan='2' class='bold' style='text-align:right;'>".number_format($principal,2)."</td>
					</tr>
				</tfooter>
			</table>
			");
			
			echo trims('
			<div id="print-account-name"style="page-break-before: always;margin-top:50px;">
			<span class="value">'.$this->config->item('account_name').'</span></div>
			<div id="print-account-address"><span class="value">'.
				$this->config->item('account_address').'</span></div>
			<br />
			<div id="print-report-title"><span class="value">'.$title.'</span></div>
			<div id="print-report-period"><span class="value">'.$reportname.'</span>
			</div>
			<br>
			<table border="0" cellpadding="5" class="simple-table collection-table" width="100%">
				<thead>
					<tr valign="center"height="30px">
						<th width="1">&nbsp;</th>
						<th>Date</th>
						<th>Client Name</th>
						<th>Bank/Branch</th>
						<th>ATM/PB</th>
						<th style="text-align:right;">Pension</th>
						<th style="text-align:right;">Cash Out</th>
						<th style="text-align:right;">Term</th>
						<th style="text-align:right;">Principal</th>
					</tr>
				</thead>
			');
			
			$ctrlimit=1;
			$netproceeds=0;
			$principal=0;
		else:
			$ctrlimit++;
		endif;
		
	endforeach;
	
	echo '</tbody><tfooter>';
	
	if($principal>0&&isset($isreport)&&$isgrandtotal):
		echo trims("
			<tr class='total-area' valign='center'height='30px'>
				<td class='bold' colspan='6' style='text-align:right;'>".(
					isset($isreport)?
							($isgrandtotal?
								'Sub Total'
							:
								'Total')
						:
							''
				)."</td>
				<td class='bold' style='text-align:right;'>".number_format($netproceeds,2)."</td>
				<td colspan='2' class='bold' style='text-align:right;'>".number_format($principal,2)."</td>
			</tr>
		");
	endif;
	
	echo trims("
		<tr class='total-area'>
			<td class='bold' colspan='6' style='text-align:right;'>".(
			isset($isreport)?
					($isgrandtotal?
						'Grand Total'
					:
						'Total')
				:
					''
			)."</td>
			<td class='bold' style='text-align:right;'>".number_format($gtnetproceeds,2)."</td>
			<td colspan='2' class='bold' style='text-align:right;'>".number_format($gtprincipal,2)."</td>
		</tr>".
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
					<th colspan='2' style='text-align:right;'>".strtoupper(substr($preparedby, 0, 3))."</th>
					<th colspan='2' style='text-align:right;'>EPC</th>
					<th colspan='2' style='text-align:right;'>ACR</th>
				</tr>
			</tbody>
		</table>
		"
		:
		""
		)."
	");
	
	echo '</tfooter>';
	
endif;

echo '</table>';

?>