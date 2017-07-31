<?php

ini_set('display_errors', 'yes');
$this->load->helper('code');

echo trims("
	<table border='0' cellpadding='5' class='simple-table collection-table' width='100%'>
		<thead>
			<tr valign='center' height='30px'>
				<th align='center'>Branch</th>
				<th align='right'>ATM Beg. Bal.</th>
				<th align='right'>Amt. Drawn</th>
				<th align='right'>ATM End Bal.</th>
				<th align='right'>Directpaid</th>
				<th align='right'>Net Due</th>
			</tr>
		</thead>
		<tbody>
");

if(isset($datas))
{
	$atmbegbal = 0;
	$amtdrawn = 0;
	$atmendbal = 0;
	$directpaid = 0;
	$netdue = 0;
	
	foreach($datas as $data)
	{
		echo trims("
			<tr>
				<td align='center'>" . strtoupper($data['branchname']) . "</td>
				<td align='right'>" . number_format($data['atmbegbal'], 2) . "</td>
				<td align='right'>" . number_format($data['amtdrawn'], 2) . "</td>
				<td align='right'>" . number_format($data['atmendbal'], 2) . "</td>
				<td align='right'>" . number_format($data['directpaid'], 2) . "</td>
				<td align='right'>" . number_format($data['netdue'], 2) . "</td>
			</tr>
		");
		
		$atmbegbal += $data['atmbegbal'];
		$amtdrawn += $data['amtdrawn'];
		$atmendbal += $data['atmendbal'];
		$directpaid += $data['directpaid'];
		$netdue += $data['netdue'];
	}
}

echo trims("
		</tbody>
		<tfoot>
			<tr>
				<td class='bold' style='text-align:right;'>Total</td>
				<td class='bold' style='text-align:right;'>" . number_format($atmbegbal, 2) . "</td>
				<td class='bold' style='text-align:right;'>" . number_format($amtdrawn, 2) . "</td>
				<td class='bold' style='text-align:right;'>" . number_format($atmendbal, 2) . "</td>
				<td class='bold' style='text-align:right;'>" . number_format($directpaid, 2) . "</td>
				<td class='bold' style='text-align:right;'>" . number_format($netdue, 2) . "</td>
			</tr>
		</tfoot>
	</table>	
");

?>