<?php

ini_set('display_errors', 'yes');
$this->load->helper('code');

echo trims('
<table border="0" cellpadding="5" class="simple-table collection-table" width="100%">
	<thead>
		<tr valign="center"height="30px">
			<th width="1">&nbsp;</th>
			<th style="text-align:center;">Date</th>
			<th>Name</th>
			<th>BankBranch/WDay</th>
			<th style="text-align:right;">TraceNo</th>
			<th style="text-align:right;">ATMBeg</th>
			<th style="text-align:right;">Withdrawn</th>
			<th style="text-align:right;">ATMEnd</th>
			<th style="text-align:right;">Refund</th>
			<th style="text-align:right;">NetDue</th>
		</tr>
	</thead>
');

if(isset($datas)):

	echo'<tbody>';

	$ref=''; $ctr=1;
	$ctrlimit = 1;
	$beg=0; $dra=0; $end=0; $tnet=0; $paid=0;
	$gtbeg=0; $gtdra=0; $gtend=0; $gtnet=0; $gtpaid=0;
	$isgrandtotal = FALSE;
	
	if(!empty($datas)):
	
		foreach($datas as $d):

			if(strlen($d['fullname'])>25)
				$name = substr($d['fullname'],0,25).'..';
			else $name = $d['fullname'];

			$bankbranch = $d['bankbranch'];

			$netdue = $d['amtdrawn'] - $d['directpaid'];
			echo trims("
				<tr valign='center'height='30px'>
					<td style='text-align:right;'>{$ctr})</td>
					<td style='text-align:center;'>".date('m/d/Y',strtotime($d['duedate']))."</td>
					<td nowrap><span style='font-family:Arial Narrow'>$name</span></td>
					<td nowrap><span style='font-family:Arial Narrow'>$bankbranch</span> ({$d['wday']})</td>
					<td style='text-align:right;'>" . ($d['tracerefno'] == 'AD' ? 'ADA' : $d['tracerefno']) . "</td>
					<td style='text-align:right;'>".number_format($d['atmbegbal'],2)."</td>
					<td style='text-align:right;'>".number_format($d['amtdrawn'],2)."</td>
					<td style='text-align:right;'>".number_format($d['atmendbal'],2)."</td>
					<td style='text-align:right;'>".number_format($d['directpaid'],2)."</td>
					<td style='text-align:right;'>".number_format($netdue,2)."</td>
				</tr>
			");

			$ref=$d['orprno'];
			
			$beg+=$d['atmbegbal'];
			$dra+=$d['amtdrawn'];
			$end+=$d['atmendbal'];
			$paid+=$d['directpaid'];
			$tnet+=$netdue;

			$gtbeg+=$d['atmbegbal'];
			$gtdra+=$d['amtdrawn'];
			$gtend+=$d['atmendbal'];
			$gtpaid+=$d['directpaid'];
			$gtnet+=$netdue;
			$ctr++;
			if($ctrlimit==25&&isset($isreport))
			{
				$beg=number_format($beg,2);
				$dra=number_format($dra,2);
				$end=number_format($end,2);
				$paid=number_format($paid,2);
				$tnet=number_format($tnet,2);

				$isgrandtotal = TRUE;
				echo trims("
				</tbody><tfooter><tr class='total-area' valign='center'height='30px'>
					<td class='bold' colspan='5' style='text-align:right;'>" . (
						isset($isreport) ?
							($isgrandtotal ?
								'Sub Total'
							:
								'Total')
						:
							'Total') .
					"</td>
					<td class='bold' style='text-align:right;'>$beg</td>
					<td class='bold' style='text-align:right;'>$dra</td>
					<td class='bold' style='text-align:right;'>$end</td>
					<td class='bold' style='text-align:right;'>$paid</td>
					<td class='bold' style='text-align:right;'>$tnet</td>
				</tr></table>");

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
						<tr>
							<th width="1">&nbsp;</th>
							<th style="text-align:center;">Date</th>
							<th>Name</th>
							<th>BankBranch/WDay</th>
							<th style="text-align:right;">TraceNo</th>
							<th style="text-align:right;">ATMBeg</th>
							<th style="text-align:right;">Withdrawn</th>
							<th style="text-align:right;">ATMEnd</th>
							<th style="text-align:right;">Refund</th>
							<th style="text-align:right;">NetDue</th>
						</tr>
					</thead>
				');

				$ctrlimit=1;
				$beg = 0;
				$dra = 0;
				$end = 0;
				$paid = 0;
				$tnet = 0;

			}else $ctrlimit++;

		endforeach;
		
	endif;

	echo'</tbody><tfooter>';

	$gtbeg=number_format($gtbeg,2);
	$gtdra=number_format($gtdra,2);
	$gtend=number_format($gtend,2);
	$gtpaid=number_format($gtpaid,2);
	$gtnet=number_format($gtnet,2);

	if($tnet>0&&isset($isreport)&&$isgrandtotal):

		$beg=number_format($beg,2);
		$dra=number_format($dra,2);
		$end=number_format($end,2);
		$paid=number_format($paid,2);
		$tnet=number_format($tnet,2);
		echo trims("
		<tr class='total-area'>
			<td class='bold' colspan='5' style='text-align:right;'>" . (
					isset($isreport) ?
						($isgrandtotal ?
							'Sub Total'
						:
							'Total')
					:
						'Total') .
			"</td>
			<td class='bold' style='text-align:right;'>$beg</td>
			<td class='bold' style='text-align:right;'>$dra</td>
			<td class='bold' style='text-align:right;'>$end</td>
			<td class='bold' style='text-align:right;'>$paid</td>
			<td class='bold' style='text-align:right;'>$tnet</td>
		</tr>");

	endif;

	echo trims("
		<tr class='total-area'>
			<td class='bold' colspan='5' style='text-align:right;'>" . (
					isset($isreport) ?
						($isgrandtotal ?
							'Grand Total'
						:
							'Total')
					:
						'Total') .
			"</td>
			<td class='bold' style='text-align:right;'>$gtbeg</td>
			<td class='bold' style='text-align:right;'>$gtdra</td>
			<td class='bold' style='text-align:right;'>$gtend</td>
			<td class='bold' style='text-align:right;'>$gtpaid</td>
			<td class='bold' id='abt' style='text-align:right;'>$gtnet</td>
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
					<th colspan='2' style='text-align:right;'>WHB</th>
					<th colspan='2' style='text-align:right;'>ACR</th>
				</tr>
			</tbody>
		</table>
		"
		:
		""
		)."
	");

	echo'</tfooter>';
	
endif;

echo'</table>';