<?php

ini_set('display_errors', 'yes');
$this->load->helper('code');

echo trims('
<table border="0" cellpadding="5" class="simple-table collection-table" width="100%">
	<thead>
		<tr valign="center"height="30px">
			<th width="1">&nbsp;</th>
			<th>Client</th>
			<th>Date</th>
			<th>CV No.</th>
			<th>Check No.</th>
			<th style="text-align:right;">Notes Recievable</th>
			<th style="text-align:right;">Other Income</th>
			<th style="text-align:right;">Cash In Bank</th>
		</tr>
	</thead>
');

if(!empty($datas)):

	echo'<tbody>';
	
	$ckamount = 0;
	$ctr = 1;
	$ctrlimit = 1;
	$gtprincipal = 0;
	$gtotherincome = 0;
	$gtckamount = 0;
	$toprincipal = 0;
	$tootherincome = 0;
	
	$isgrandtotal=FALSE;
	foreach ($datas as $d):
		
		$database = '';
		$payee = '';
		
		$q = $this->db->query('
			SELECT CONCAT(CI_LName, ", ", CI_FName, " ", CI_MName) AS fullname
			FROM client
			WHERE CI_AcctNo = "'.$d['payee'].'"
		');
		if($q->num_rows() > 0):
			$q = $q->row();
			$payee = $q->fullname;
		else:
			// Check branch_transfer table for advance bonus transfer
			$current_branch = $this->rsm->branch();
			
			$this->db->select('frombranch', false);
			$this->db->from('nhgt_master.branch_transfer');
			$this->db->where('tobranch', $current_branch);
			$this->db->where('status', 'done');
			$this->db->where('refid', $d['payee'] . '.ADVANCE_BONUS');
			$query_bt = $this->db->get();
			
			if($query_bt->num_rows() > 0)
			{
				$database = 'nhgt_' . $query_bt->row()->frombranch;
			}
			
			$q = $this->db->query('
				SELECT CONCAT(CI_LName, ", ", CI_FName, " ", CI_MName) AS fullname
				FROM ' . $database . '.client
				WHERE CI_AcctNo = "'.$d['payee'].'"
				LIMIT 1;
			')->row();
			$payee = $q->fullname;
		endif;
		
		$otherincome = 0;
		$principal = 0;
		
		$qq = $this->db->query('
			SELECT LH_Principal,
				LH_InterestAmt,
				LH_ProcFee,
				LH_CollFee
			FROM ln_hdr
			WHERE CI_AcctNo = "' . $d['payee'] . '"
			AND LH_LoanTrans = "SPEC"
			AND LH_IsPending = 0
			AND LH_Processed = 1
			AND LH_Cancelled = 0;
		');
		
		if($qq->num_rows() > 0):
			
			$qq = $qq->row();
			$principal = $qq->LH_Principal;
			$otherincome = $qq->LH_InterestAmt + $qq->LH_ProcFee + $qq->LH_CollFee;
			
		else:
			
			$database = '';
			$principal = 0;
			$otherincome = 0;
			
			// Check branch_transfer table for advance bonus transfer
			$current_branch = $this->rsm->branch();
			
			$this->db->select('frombranch', false);
			$this->db->from('nhgt_master.branch_transfer');
			$this->db->where('tobranch', $current_branch);
			$this->db->where('status', 'done');
			$this->db->where('refid', $d['payee'] . '.ADVANCE_BONUS');
			$query_bt = $this->db->get();
			
			if($query_bt->num_rows() > 0)
			{
				$database = 'nhgt_' . $query_bt->row()->frombranch;
				
				$qq = $this->db->query('
					SELECT LH_Principal,
						LH_InterestAmt,
						LH_ProcFee,
						LH_CollFee
					FROM ' . $database . '.ln_hdr
					WHERE CI_AcctNo = "' . $d['payee'] . '"
					AND LH_LoanTrans = "SPEC"
					AND LH_IsPending = 0
					AND LH_Processed = 1
					AND LH_Cancelled = 0;
				');
				
				$principal = $qq->row()->LH_Principal;
				$otherincome = $qq->row()->LH_InterestAmt + $qq->row()->LH_ProcFee + $qq->row()->LH_CollFee;
			}
			
		endif;
		
		
		echo trims("
			<tr valign='center'height='30px'>
				<td style='text-align:right;'>$ctr)</td>
				<td>{$payee}</td>
				<td>".date('m/d/Y', strtotime($d['cvdate']))."</td>
				<td>{$d['cvno']}</td>
				<td>{$d['ckno']}</td>
				<td style='text-align:right;'>".number_format($principal,2)."</td>
				<td style='text-align:right;'>".number_format($otherincome,2)."</td>
				<td style='text-align:right;'>".number_format($d['ckamount'],2)."</td>
			</tr>
		");
		
		$toprincipal += $principal;
		$tootherincome += $otherincome;
		$ckamount += $d['ckamount'];
		$gtprincipal += $principal;
		$gtotherincome += $otherincome;
		$gtckamount += $d['ckamount'];
		$ctr++;
		
		if($ctrlimit==25&&isset($report)):
		
			$isgrandtotal = TRUE;
			echo trims("
				</tbody>
				<tfooter>
					<tr class='total-area' valign='center'height='30px'>
						<td class='bold' colspan='5' style='text-align:right;'>".(
						isset($isreport)?
							($isgrandtotal?
								'Sub Total'
							:
								'Total')
						:
							''
						)."</td>
						<td class='bold' style='text-align:right;'>".number_format($toprincipal,2)."</td>
						<td class='bold' style='text-align:right;'>".number_format($tootherincome,2)."</td>
						<td class='bold' style='text-align:right;'>".number_format($ckamount,2)."</td>
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
						<th>Client</th>
						<th>Date</th>
						<th>CV No.</th>
						<th>Check No.</th>
						<th style="text-align:right;">Notes Recievable</th>
						<th style="text-align:right;">Other Income</th>
						<th style="text-align:right;">Cash In Bank</th>
					</tr>
				</thead>
			');
			
			$ctrlimit=1;
			$toprincipal=0;
			$tootherincome=0;
			$ckamount=0;
		else:
			$ctrlimit++;
		endif;
		
	endforeach;
	
	echo '</tbody><tfooter>';
	
	if($principal>0&&isset($isreport)&&$isgrandtotal):
		echo trims("
			<tr class='total-area' valign='center'height='30px'>
				<td class='bold' colspan='5' style='text-align:right;'>".(
					isset($isreport)?
							($isgrandtotal?
								'Sub Total'
							:
								'Total')
						:
							''
				)."</td>
				<td class='bold' style='text-align:right;'>".number_format($toprincipal,2)."</td>
				<td class='bold' style='text-align:right;'>".number_format($tootherincome,2)."</td>
				<td class='bold' style='text-align:right;'>".number_format($ckamount,2)."</td>
			</tr>
		");
	endif;
	
	echo trims("
		<tr class='total-area'>
			<td class='bold' colspan='5' style='text-align:right;'>".(
			isset($isreport)?
					($isgrandtotal?
						'Grand Total'
					:
						'Total')
				:
					''
			)."</td>
			<td class='bold' style='text-align:right;'>".number_format($gtprincipal,2)."</td>
			<td class='bold' style='text-align:right;'>".number_format($gtotherincome,2)."</td>
			<td class='bold' style='text-align:right;'>".number_format($gtckamount,2)."</td>
		</tr>".
		(isset($preparedby)?
		"
		<table style='padding-top:50px'align='center'width='60%'>
			<tbody>
				<tr style='border:0;'>
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