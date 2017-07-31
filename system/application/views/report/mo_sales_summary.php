<?php

ini_set('display_errors', 'yes');
$this->load->helper('code');

if($reporttype == '1')
{
	$ac_total = 0;
	$atmpbh_total = 0;
	$nc_total = 0;
	$pc_total = 0;
	$ret_total = 0;
	
	echo trims("
		<table border='0' cellpadding='5' class='simple-table collection-table' width='100%'>
			<thead>
				<tr valign='center' height='30px'>
					<th align='center'>Branch</th>
					<th align='center'>Total No. of Active Clients</th>
					<th align='center'>Total No. of Problem Accounts</th>
					<th align='center'>No. of New Clients</th>
					<th align='center'>No. of Returning Clients</th>
					<th align='center'>No. of Released Clients</th>
				</tr>
			</thead>
			<tbody>
	");
	
	if(isset($datas))
	{
		 foreach($datas as $data)
		 {
		 	echo trims("
				<tr>
					<td align='center'>" . strtoupper($data['branchname']) . "</td>
					<td align='center'>0</td>
					<td align='center'>0</td>
					<td align='center'>" . $data['nctotal'] . "</td>
					<td align='center'>" . $data['rettotal'] . "</td>
					<td align='center'>" . $data['atmpbhtotal'] . "</td>
				</tr>
			");
			
			$atmpbh_total += $data['atmpbhtotal'];
			$nc_total += $data['nctotal'];
			$ret_total += $data['rettotal'];
		 }
	}
	
	echo trims("
			</tbody>
			<tfoot>
				<tr>
					<td class='bold' style='text-align:right;'>Total</td>
					<td class='bold' style='text-align:center;'>" . $ac_total . "</td>
					<td class='bold' style='text-align:center;'>" . $pc_total . "</td>
					<td class='bold' style='text-align:center;'>" . $nc_total . "</td>
					<td class='bold' style='text-align:center;'>" . $ret_total . "</td>
					<td class='bold' style='text-align:center;'>" . $atmpbh_total . "</td>
				</tr>
			</tfoot>
		</table>	
	");
} elseif($reporttype == '2') {
	$net_diff_total = 0;
	$net_per_total = 0;
	$netproceeds1_total = 0;
	$netproceeds2_total = 0;
	$net_per_ave = 0;
	$pn_diff_total = 0;
	$pn_per_total = 0;
	$pn_per_ave = 0;
	$principal1_total = 0;
	$principal2_total = 0;
		
	echo trims("
		<table border='0' cellpadding='5' class='simple-table collection-table' width='100%'>
			<thead>
				<tr>
					<th width='1'>&nbsp;</th>
					<th align='right' colspan='2'>Total Principal Amt</th>
					<th align='right' colspan='2'>Total Cash-Out</th>
					<th align='right' colspan='2'>Total PN</th>
					<th align='right' colspan='2'>Totla Cash-Out</th>
				</tr>
				<tr valign='center' height='30px'>
					<th align='right'>Branch</th>
					<th align='right'>Preceded Month</th>
					<th align='right'>Current Month</th>
					<th align='right'>Preceded Month</th>
					<th align='right'>Current Month</th>
					<th align='right'>Variance in Figures</th>
					<th align='right'>% of Change(s)</th>
					<th align='right'>Variance in Figures</th>
					<th align='right'>% of Change(s)</th>
				</tr>
				<tr valign='center' height='30px'>
					<th>&nbsp;</th>
					<th align='right'>" . date('M-Y', strtotime($premoyr)) . "</th>
					<th align='right'>" . date('M-Y', strtotime($curmoyr)) . "</th>
					<th align='right'>" . date('M-Y', strtotime($premoyr)) . "</th>
					<th align='right'>" . date('M-Y', strtotime($curmoyr)) . "</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
	");
	
	if(isset($datas))
	{
		foreach($datas as $data)
		{
			$net_diff = 0;
			$net_per = 0;
			$pn_diff = 0;
			$pn_per = 0;
			
			
			$net_diff = $data['netproceeds1'] - $data['netproceeds2'];
			$net_per = 100 * ($net_diff / $data['netproceeds2']);
			$pn_diff = $data['principal1'] - $data['principal2'];
			$pn_per = 100 * ($pn_diff / $data['principal2']);
			
			echo trims("
				<tr>
					<td align='right'>" . strtoupper($data['branchname']) . "</td>
					<td align='right'>" . number_format($data['principal2'], 2) . "</td>
					<td align='right'>" . number_format($data['principal1'], 2) . "</td>
					<td align='right'>" . number_format($data['netproceeds2'], 2) . "</td>
					<td align='right'>" . number_format($data['netproceeds1'], 2) . "</td>
					<td align='right'>" . ($pn_diff < 0 ? "<label style='color:#ff0000;'>" . number_format($pn_diff, 2) . "</label>" : number_format($pn_diff, 2)) . "</td>
					<td align='right'>" . ($pn_per < 0 ? "<label style='color:#ff0000;'>" . number_format($pn_per, 2) . "</label>" : number_format($pn_per, 2)) . "</td>
					<td align='right'>" . ($net_diff < 0 ? "<label style='color:#ff0000;'>" . number_format($net_diff, 2) . "</label>" : number_format($net_diff, 2)) . "</td>
					<td align='right'>" . ($net_per < 0 ? "<label style='color:#ff0000;'>" . number_format($net_per, 2) . "</label>" : number_format($net_per, 2)) . "</td>
				</tr>
			");
			
			$net_diff_total += $net_diff;
			$net_per_total += $net_per;
			$netproceeds1_total += $data['netproceeds1'];
			$netproceeds2_total += $data['netproceeds2'];
			$pn_diff_total += $pn_diff;
			$pn_per_total += $pn_per;
			$principal1_total += $data['principal1'];
			$principal2_total += $data['principal2'];
		}
	}
	
	$net_per_ave = $net_per_total / 26;
	$pn_per_ave = $pn_per_total / 26;
	
	echo trims("
		</tbody>
		<tfoot>
			<tr class='total-area' valign='center'height='30px'>
				<td class='bold' style='text-align:right;'>Total</td>
				<td class='bold' style='text-align:right;'>" . number_format($principal2_total, 2) . "</td>
				<td class='bold' style='text-align:right;'>" . number_format($principal1_total, 2) . "</td>
				<td class='bold' style='text-align:right;'>" . number_format($netproceeds2_total, 2) . "</td>
				<td class='bold' style='text-align:right;'>" . number_format($netproceeds1_total, 2) . "</td>
				<td class='bold' style='text-align:right;'>" . number_format($net_diff_total, 2) . "</td>
				<td class='bold' style='text-align:right;'>" . number_format($pn_per_ave, 2) . "</td>
				<td class='bold' style='text-align:right;'>" . number_format($pn_diff_total, 2) . "</td>
				<td class='bold' style='text-align:right;'>" . number_format($net_per_ave, 2) . "</td>
			</tr>
		</tfoot>
	</table>
	");
}

?>