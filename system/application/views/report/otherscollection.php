<?php

ini_set('display_errors', 'yes');
$this->load->helper('code');

echo trims('
<table border="0" cellpadding="5" class="simple-table collection-table" width="100%">
	<thead>
		<tr valign="center"height="30px">
			<th width="1">&nbsp;</th>
			<th>O.R. No</th>
			<th>Name</th>
			<th>Bank/Branch</th>
			<th style="text-align:right;">Payment</th>
			<th style="text-align:right;">Refund</th>
			<th style="text-align:right;">NetDue</th>
		</tr>
	</thead>
');

if(isset($datas)):

	echo'<tbody>';
	
	$ctr = 1;$tocrt =0;$torefund = 0;$tonetd = 0;
	foreach ($datas as $d):
		
		$q = $this->db->query("
			SELECT b.name as bankbranch
			FROM entry_items a, ledgers b
			WHERE a.entry_id='".$d['id']."'
			AND a.dc='D'
			AND a.ledger_id=b.id;
		")->row();
		$bankbranch = str_replace('Cash in Bank - ', '', $q->bankbranch);
		
		$refund = $d['cr_total'] - $d['dr_total'];
		$netdue = $d['cr_total'] - $refund;
		echo trims("
			<tr valign='center'height='30px'>
				<td style='text-align:right;'>$ctr)</td>
				<td>{$d['reference']}</td>
				<td nowrap><span style='font-family:Arial Narrow'>{$d['narration']}</span></td>
				<td nowrap><span style='font-family:Arial Narrow'>".$bankbranch."</td>
				<td style='text-align:right;'>".number_format($d['cr_total'],2)."</td>
				<td style='text-align:right;'>".number_format($refund,2)."</td>
				<td style='text-align:right;'>".number_format($netdue,2)."</td>
			</tr>
		");
		$ctr++;
		$tocrt += $d['cr_total'];
		$torefund += $refund;
		$tonetd += $netdue;
		
	endforeach;
	
	echo'</tbody><tfooter>';
	
	$tocrt = number_format($tocrt,2);
	$torefund = number_format($torefund,2);
	$tonetd = number_format($tonetd,2);
	echo trims("
	<tr class='total-area'>
		<td class='bold' colspan='4' style='text-align:right;'>Total</td>
		<td class='bold' style='text-align:right;'>$tocrt</td>
		<td class='bold' style='text-align:right;'>$torefund</td>
		<td class='bold' style='text-align:right;'>$tonetd</td>
	</tr>");
	
	echo '</tfooter>';
		
endif;

echo '</table>';

?>