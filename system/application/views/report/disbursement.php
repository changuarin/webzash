<?php

ini_set('display_errors', 'yes');
$this->load->helper('code');
$this->load->model('database');

	if(!empty($datas)):
	echo trims('
		<table border="0" cellpadding="5" class="simple-table collection-table" 
		style="font-size:11px;width:100%;">
	');
		
		$count = count($bank) - 1;
		for($z = 0; $z <= $count; $z++):
			
			$i = 1;
			$ototal = 0;
			$stotal = 0;
			
			if(!empty($datas[$z]))
			{
				if($z != 0 && isset($isreport) && $isreport)
				echo trims("
					</table>
					<div id='print-account-name'style='page-break-before: always;margin-top:50px;'>
					<span class='value'>" . $this->config->item('account_name') . "</span></div>
					<div id='print-account-address'><span class='value'>" .
						$this->config->item('account_address') . "</span></div>
					<br />
					<div id='print-report-title'><span class='value'>" . $title . "</span></div>
					<div id='print-report-period'><span class='value'>" . $reportname . "</span>
					</div>
					<br>
					<table border='0' cellpadding='5' class='simple-table collection-table' 
						style='font-size:11px;width:100%;'>
				");

				echo trims("
						<thead>
							<th>
								<th colspan='7'>" . $banks[$z] . "</th>
							</th>
							<tr>
								<th style='width:1px;'>&nbsp;</th>
								<th>DATE</th>
								<th>CHECK NO.</th>
								<th>CV NO.</th>
								<th>NAME</th>" .
								($cvtype != 'S' || $cvtype == '0' ?
									'<th style="text-align:right;">OTHERS</th>'
								:
									'') .
								($cvtype == 'S' || $cvtype == '0' ?
									'<th style="text-align:right;">SALES</th>'
								:
									'') . "
								<th>NATURE</th>
							</tr>
						</thead>
				");
				
				$i = 1;
				$ototal = 0;
				$stotal = 0;
				
				foreach($datas[$z] as $d):
					
					echo trims("
						<tbody>
							<tr>
								<td style='text-align:right;'>" . $i . ")</td>
								<td>" . date('m/d/Y', strtotime($d['cvdate'])) . "</td>
								<td>" . $d['ckno'] . "</td>
								<td>" . $d['cvno'] . "</td>
								<td>" . $d['payee'] . "</td>" .
								($cvtype != 'S' || $cvtype == '0' ?
									"<td style='text-align:right;'>" . ($d['cvtype'] != 'S' ? number_format($d['ckamount'], 2) : '0.00') . "</td>"
								:
									''
								) .
								($cvtype == 'S' || $cvtype == '0' ?
									"<td style='text-align:right;'>" . ($d['cvtype'] == 'S' ? number_format($d['ckamount'], 2) : '0.00') . "</td>"
								:
									''
								) . "
								<td>{$d['remarks']}</td>
							</tr>
					");
					
					if($d['cvtype'] == 'S'):
						$stotal += $d['ckamount'];
					else:
						$ototal += $d['ckamount'];
					endif;
					$i++;

				endforeach;
				
				echo '<tfooter>';
			
				echo trims("
						<tr class='total-area'>
							<td colspan='4'>&nbsp;</td>
							<td class='bold' style='text-align:right;'>Total</td>
							" .
							($cvtype != 'S' || $cvtype == '0' ?
								"<td class='bold' style='text-align:right;'>" . number_format($ototal, 2) . "</td>"
							:
								''
							) .
							($cvtype == 'S' || $cvtype == '0' ?
								"<td class='bold' style='text-align:right;'>".number_format($stotal, 2)."</td>"
							:
								''
							) . "
							<td>&nbsp;</td>
						</tr>
				");
				
				echo '</tfooter>';
		
			}
		
		endfor;
		
	endif;

echo '</table>';