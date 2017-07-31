<?

$year = $this->uri->segment(3,'');
$month = $this->uri->segment(4,'');

$day = date('t', strtotime("$year-$month-1"));

$month = (strlen($month)==1?"0$month":$month);

$dateto = "$year$month";

$tmp = str_replace('nhgt_', '', $this->db->database);

switch($tmp):
	case'sanjuan':$branch='San Juan';break;
	case'sanpablo':$branch='San Pablo';break;
	case'launion':$branch='La Union';break;
	default: 
		$branch = $tmp;
endswitch;

$this->db->select('branch_code');
$this->db->where('branch_name', $branch);
$qb = $this->db->get('nhgt_master.branch');

$branchcode = '';

if($qb->num_rows()) $branchcode = $qb->row()->branch_code;

//echo $day.$month.$year;
//echo $branchcode;

if($day.$month.$year!='')
{
	/*$data = '';
	$sql = "SELECT a.CI_AcctNo,
			CONCAT(b.CI_LName, ', ', b.CI_FName, ' ',b.CI_MName) AS name,
			a.CP_PType AS paytype,
			a.CP_BankAcctNo_Ref AS bankacctno,
			a.CP_BankBranch AS bankbranch,
			a.CP_WithdrawalDay AS day,
			c.LH_PN,
			c.LH_Balance,
			b.CI_Source,
			b.CI_BranchCode as branch,
			c.LH_MonthlyAmort,
			c.LH_StartDate,
			c.LH_EndDate,
			c.LH_Terms,
			c.LH_LoanTrans,
			c.LH_LoanDate,
			c.LH_Principal,
			c.LH_OBC
		FROM client_pension a,
			client b,
			ln_hdr c
		WHERE a.CI_AcctNo = b.CI_AcctNo
		AND a.CI_AcctNo = c.CI_AcctNo
		AND a.CP_IsDeleted != 1
		AND b.CI_IsDeleted != 1
		AND c.LH_LoanType = 'PEN'
		AND c.LH_IsTop = 1
		AND c.LH_Cancelled != 1
		AND c.LH_LoanTrans != 'SPEC'
		AND DATE_FORMAT(c.LH_EndDate,'%Y%m') >= '$dateto'
		AND DATE_FORMAT(c.LH_StartDate,'%Y%m') <= '$dateto'
		ORDER BY a.CP_WithdrawalDay,
			a.CP_PType,
			b.CI_LName,
			b.CI_FName,
			c.LH_LoanDate;";

	flush();
	$data = $this->db->query( $sql )->result_array();*/
	
	$data = $this->db->query(
		"SELECT * FROM nhgt_bills.tempbill
		WHERE monthbill='$day.$month.$year'
		AND branch='$branchcode';")->result_array();

	if(!count($data)):

		$sql = "INSERT INTO nhgt_bills.tempbill(SELECT a.CI_AcctNo,
				CONCAT(b.CI_LName, ', ', b.CI_FName, ' ',b.CI_MName) AS name,
				a.CP_PType AS paytype,
				a.CP_BankAcctNo_Ref AS bankacctno,
				a.CP_BankBranch AS bankbranch,
				a.CP_WithdrawalDay AS day,
				c.LH_PN,
				c.LH_Balance,
				b.CI_Source,
				c.LH_MonthlyAmort,
				c.LH_StartDate,
				c.LH_EndDate,
				c.LH_Terms,
				c.LH_LoanTrans,
				c.LH_LoanDate,
				c.LH_Principal,
				c.LH_OBC,
				b.CI_BranchCode AS branch,
				'{$this->session->userdata('user_name')}',
				'$day.$month.$year'
			FROM client_pension a,
				client b,
				ln_hdr c
			WHERE a.CI_AcctNo = b.CI_AcctNo
			AND a.CI_AcctNo = c.CI_AcctNo
			AND a.CP_IsDeleted != 1
			AND b.CI_IsDeleted != 1
			AND c.LH_LoanType != 'SAL'
			AND c.LH_IsTop = 1
			AND c.LH_Cancelled != 1
			AND c.LH_LoanTrans != 'SPEC'
			AND DATE_FORMAT(c.LH_EndDate,'%Y%m') >= '$dateto'
			AND DATE_FORMAT(c.LH_StartDate,'%Y%m') <= '$dateto'
			ORDER BY a.CP_WithdrawalDay
			LIMIT 0, 499);";

		$this->db->query( $sql ) or die( $this->db->_error_message() );
		
		flush();
		$data = $this->db->query("SELECT * FROM nhgt_bills.tempbill 
			WHERE monthbill='$day.$month.$year'
			AND branch='$branchcode';")->result_array();

	endif;

}
?>
<link type="text/css" rel="stylesheet" href="<?=base_url()?>system/application/assets/css/forms.css">
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script>

<script>$(document).ready(function(){
	<? if(isset($data)&&count($data)): ?>
	$('#nofr',parent.document).html('<?=count($data)?> Records(s).');
	<?endif;?>
	$('#ldri',parent.document).hide();
});</script>
<body style="margin:0px;"><table width="100%"border="1"cellpadding="0"cellspacing="0">
	<tr>
		<th align="left">W/Day</th>
		<th align="left">Name</th>
		<th align="left">Payment Type</th>
		<th align="left">Bank AcctNo</th>
		<th align="left">Bank Branch</th>
		<th align="left"colspan="2">Loan Reference</th>
		<th align="left"title="Pension Type">Pen.Type</th>
		<th colspan="2">Duration</th>
		<th align="left">Terms</th>
		<th align="right">Balance</th>
		<th align="right">Loan Amount</th>
		<th>Status</th>
	</tr><?

	while (ob_get_level() > 0)
	{
	    ob_end_flush();
	}
	ob_start();

	$isallbilled=TRUE;
	if(isset($data)&&count($data))
	{
		$j=0; $tname=''; $i = 1;
		foreach ($data as $d)
		{
			$date = date('Y-m-d',strtotime("$year-$month-{$d['day']}"));
			$tmonth = date('m',strtotime("$year-$month-{$d['day']}"));
			$red='';
			if($tmonth!=$month):
				$enday = date('t',strtotime("$year-$month-1"));
				$date = date('Y-m-d',strtotime("$year-$month-$enday"));
				$red = "<small style='color:red'>$enday</small>/";
			endif;
			$bill = $this->db->query(
				"SELECT *
				FROM nhgt_bills.header
				WHERE CI_AcctNo='{$d['CI_AcctNo']}'
				AND LH_PN='{$d['LH_PN']}'
				AND billdate='$date'
				AND status!='closed';");
			$billed='&nbsp;';
			if($bill->num_rows())$billed='Billed';
			else if($isallbilled)$isallbilled=FALSE;
			unset($bill);

			if(strlen($d['name'])>20):
				$name = '<small>'.$d['name'].'</small>';
			else:
				$name = $d['name'];
			endif;

			$durationfrom = date('F Y',strtotime($d['LH_StartDate']));
			$durationto = date('F Y',strtotime($d['LH_EndDate']));

			if(strlen($d['bankbranch'])>15):
				$bankbranch = substr($d['bankbranch'], 0, 11).'...';
				$bbtitle = ' title="'.$d['bankbranch'].'"';
			else:
				$bankbranch = $d['bankbranch'];
				$bbtitle = '';
			endif;
			
			if($tname!=$name):
				$j=$j?0:1;
				$tname=$name;
			endif;

		/* VALIDATE OUTSTANDING BALANCE */
			$this->db->select('
				LL_Remarks,
				LL_Refund,
				LL_AmountCash,
				LL_AmountCash_Payment,
				LL_PaymentDate
			');
			$this->db->where('LL_Processed', 1);
			$this->db->where('LL_IsDeleted', 0);
			$this->db->where('CI_AcctNo', $d['CI_AcctNo']);
			$this->db->where('LH_PN', $d['LH_PN']);
			$this->db->order_by('LL_PaymentDate', 'ASC');
			$ledger = $this->db->get('ln_ldgr')->result_array();
			$ob = $d['LH_Principal'] + $d['LH_OBC'];
			foreach($ledger as $l)
			{
				$refund = ($l['LL_Remarks']==''?0:$l['LL_Refund']);
				$collection = ($l['LL_AmountCash']==0?0:$l['LL_AmountCash']);
				$payment = ($l['LL_AmountCash']==0&&$l['LL_AmountCash_Payment']>0?$l['LL_AmountCash_Payment']:0);
				$ob = $ob + $refund - $collection - $payment;
			}

			if($d['LH_Balance']!=$ob):
				$this->db->set('LH_Balance', $ob);
				$this->db->where('CI_AcctNo', $d['CI_AcctNo']);
				$this->db->where('LH_PN', $d['LH_PN']);
				$this->db->update('ln_hdr') or die( $this->db->_error_message() );
				$d['LH_Balance']=$ob;
			endif;
		/* END VALIDATION */

			if($d['LH_Balance']>0):
				$bgcolor = $j?'#dadada':'white';
				$color =  $j?'black':'#464646';
				echo"<tr class='data'style='background-color:$bgcolor;color:$color;'>
				<td align='center'>$red{$d['day']}</td>
				<td id='{$d['CI_AcctNo']}'bc='{$d['branch']}'>$name</td>
				<td>{$d['paytype']}</td>
				<td>{$d['bankacctno']}</td>
				<td$bbtitle>$bankbranch</td>
				<td nowrap>{$d['LH_PN']}</td>
				<td>{$d['LH_LoanTrans']}</td>
				<td>{$d['CI_Source']}</td>
				<td align='right'>$durationfrom</td>
				<td align='right'>$durationto</td>
				<td align='right'>{$d['LH_Terms']}</td>
				<td align='right'>".number_format($d['LH_Balance'],2)."</td>
				<td align='right'>".number_format($d['LH_MonthlyAmort'],2)."</td>
				<td align='center'>$billed</td>
				</tr>";
			endif;

			$i++;
			echo"<script>$('#nofr',parent.document).html('$i Record(s);');</script>";
			flush();
			ob_flush();

		}
		$j=$j?0:1;
		$this->db->where("MONTH(billdate)='$month'");
		$this->db->where("YEAR(billdate)='$year'");
		$this->db->where('billtype','manual');
		$this->db->where('status !=','closed');
		$mb = $this->db->get('nhgt_bills.header');
		// 2015-05-06 Temporary removal of manual billed from list
		/*if($mb->num_rows()):
			
			$data = $mb->result_array();
			$j=0; $tname='';

			foreach ($data as $d)
			{
				$this->db->select(
					'CI_LName, CI_FName, CI_MName, CI_Source'
				);
				$this->db->where('CI_AcctNo', $d['CI_AcctNo']);
				$q = $this->db->get('client');
				$name=''; $pentype='';
				if($q->num_rows()):
					$dname=$q->row()->CI_LName.', '.
						$q->row()->CI_FName.' '.
						$q->row()->CI_MName;
					$pentype=$q->row()->CI_Source;
				endif;

				if(strlen($dname)>20):
					$name = '<small>'.$dname.'</small>';
				else:
					$name = $dname;
				endif;

				$dur = explode(' - ', $d['duration']);
				$durationfrom = $dur[0];
				$durationto = $dur[1];

				if(strlen($d['bankbranch'])>15):
					$bankbranch = substr($d['bankbranch'], 0, 11).'...';
					$bbtitle = ' title="'.$d['bankbranch'].'"';
				else:
					$bankbranch = $d['bankbranch'];
					$bbtitle = '';
				endif;
				
				if($tname!=$name):
					$j=$j?0:1;
					$tname=$name;
				endif;

				$day = date('d', strtotime($d['billdate']));

				$bgcolor = $j?'#FFEECD':'#FFFFDD';
				$color =  $j?'black':'#464646';
				echo"<tr class='data'style='background-color:$bgcolor;color:$color;'>
				<td align='center'>$day</td>
				<td id='{$d['CI_AcctNo']}'>$name</td>
				<td>{$d['paytype']}</td>
				<td>{$d['bankacctno']}</td>
				<td$bbtitle>$bankbranch</td>
				<td nowrap>{$d['LH_PN']}</td>
				<td>{$d['loantrans']}</td>
				<td>{$d['pentype']}</td>
				<td align='right'>$durationfrom</td>
				<td align='right'>$durationto</td>
				<td align='right'>{$d['terms']}</td>
				<td align='right'>".number_format($d['balance'],2)."</td>
				<td align='right'>".number_format($d['amtodrawn'],2)."</td>
				<td align='center'title='Manual Billing'>M.Billed</td>
				</tr>";

				flush();
				ob_flush();

			}
		endif;*/
		if(!$isallbilled)
			echo"<script>$('#procon',parent.document).attr('disabled',false);</script>";
		else echo"<script>$('#procon',parent.document).attr('disabled',true);</script>";
		echo"<script>$('#manbil',parent.document).attr('disabled',false);</script>";
	}

?></table>