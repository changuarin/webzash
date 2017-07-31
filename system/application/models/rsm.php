
<?php

class rsm extends Model
{
	public function __construct()
    {
        parent::__construct();

        $this->load->helper('code');

        date_default_timezone_set('Asia/Manila');
    }
    
    //20150822 -> rsm -> get office account
    function get_office_account( $cbranch, $other='' )
    {
        $branch = $this->get_branch( $cbranch );
        $this->db->select('code');
        $this->db->where('group_id', 17);
        $this->db->where('name', $branch.' office');
        $qr = $this->db->get($other.'ledgers') or die( $this->db->_error_message() );
        return $qr->row()->code;
    }
    
    // 20150821 -> rsm -> get default branch
    function branch()
    {
        $t = $this->db->database;
        $t = str_replace('nhgt_', '', $t);
        return $t;
    }
    
    // 20150821 -> rsm -> get default branch
    function get_branch( $branch )
    {
        switch( $branch ):
            case'sanjuan': return'San Juan';break;
            case'sanpablo': return'San Pablo';break;
            case'launion': return'La Union';break;
            case'latrinidad': return'La Trinidad';break;
            default:
                return strtoupper(substr($branch,0,1)).substr($branch,1,strlen($branch));
        endswitch;
    }    
    
    // 20150821 -> rsm -> interbranch checker
    function isInterBranch( $id, $branch )
    {
        $this->db->select('tobranch');
        $this->db->where('refid', $id);
        $this->db->where('frombranch', $branch);
        $t = $this->db->get('nhgt_master.branch_transfer');
        
        $res['result'] = FALSE;
        
        if( $t->num_rows() ):
            $res['branch'] = $this->get_branch( $t->row()->tobranch );
            $res['result'] = TRUE;
        endif;

        $res = json_encode( $res );
        $res = json_decode( $res );

        return $res;
    }
    
    // 20150805 -> rsm -> List of Adjustment Type
    function adj_type( $code )
    {
    	switch ($code)
        {
    		case 0:
    			return 'ATM Balance';
    			break;
    		case 1:
    			return 'Advance Refund';
    			break;
    		case 2:
    			return '13th Month';
    			break;
    		case 3:
    			return 'Adjustment';
    			break;
    	}
    }
    
    // 2015-07-31
    function get_bank_in_ledgers( $except )
    {
    	//$this->output->enable_profiler(TRUE);
    	$this->db->select('code, name');
    	if($except) $this->db->where('code !=', $except);
    	$this->db->like('name', 'Cash in bank');
    	$qt = $this->db->get('ledgers')->result_array();
    	return $qt;
    }
    
	// 2015-07-31
	function get_bank( $code )
	{
		$this->db->select('code, bankname');
		$this->db->where('code', $code);
		$qt = $this->db->get('nhgt_master.bankaccts')->row();

		return $qt;
	}
    
    // 2015-07-31
    function get_default_bank( $param )
    {
    	$res = '';

    	$this->db->select('code');
    	$this->db->where('group', $param);
    	$r = $this->db->get('parameter');

    	if($r->num_rows()):

    		$r = $r->result_array();
    		foreach ($r as $v)
    		{
    			$t = explode(':', $v['code']);
    			if(!$res)$res = $this->get_bank( $t[0] );
    		}

    	endif;

    	return $res;
    }
    
    function get_report_users( $date, $type, $user )
    {
    	$qq = $this->db->query(
    		"SELECT encby
    		FROM collection_entry
    		WHERE orprtype='$type'
    		AND duedate='$date'
            GROUP BY encby;
    	")->result_array();
    	
    	$res = '';
    	foreach($qq as $v):
    		if(strpos($user, $v['encby'])>-1)$checked='selected';
    		else $checked='';
    		$res.="<option value='{$v['encby']}'$checked>{$v['encby']}</option>";
    	endforeach;

        unset($qq, $checked);
    	return $res;
    }
    
    function get_entry_no( $lastid )
    {
    	$this->db->select('number');
    	$this->db->where('id', $lastid );
    	
    	return $this->db->get( "entries" )->row()->number;
    }
    
    function chartaccount_id( $code, $other='' )
    {
        $other = str_replace('.', '', $other);
    	$this->db->select('id');
    	$this->db->where('code', $code );
    	
    	return $this->db->get( ($other?"$other.":'')."ledgers" )->row()->id;
    }
    
    function get_last_entryid( $other='' )
    {
        $other = str_replace('.', '', $other);
    	$this->db->select('entry_id');
        $this->db->group_by('entry_id');
    	$this->db->order_by('entry_id', 'DESC');
    	$this->db->limit(1);
    	$tmp = $this->db->get( ($other?"$other.":'')."entry_items" );
    	if($tmp->num_rows()) return $tmp->row()->entry_id + 1;
    	else return 1;
    }
    
    function get_loantrans( $trans )
    {
    	switch($trans)
    	{
    		case'NEW':return'NEW CLIENT';break;
    		case'REN':return'RENEWAL';break;
    		case'ADD':return'ADDITIONAL';break;
    		case'EXT':return'EXTENSION';break;
    		case'RES':return'RESTRUCTURE';break;
    		case'SPEC':return'13TH MONTH BONUS';break;
    		case'RET':return'RETURNING';break;
    	}
    }
    
    function get_pn_details( $client, $pnno, $database='' )
    {
    	$this->db->select('LH_Principal, LH_NetProceeds, 
    		LH_InterestAmt, LH_ProcFee, LH_CollFee, LH_Notarial,
    		LH_LoanTrans, LH_OBC, LH_Rate, LH_CollFeeRate');
    	$this->db->where('CI_AcctNo', $client);
    	$this->db->where('LH_PN', $pnno);
    	return $this->db->get($database.'ln_hdr');
    }
    
    function get_pension_details( $client )
    {
    	$this->db->select('CP_Amount');
    	$this->db->where('CI_AcctNo', $client);
    	$q = $this->db->get('client_pension');
    	if($q->num_rows()) return $q->row()->CP_Amount;
    	else return 0;
    }
    
    function is_not_crb_posted( $date, $type )
    {
    	$q = $this->db->query(
    		"SELECT aid
    		FROM crb_entry a
    		WHERE crb_date = '$date'
    		AND or_pr = '$type';
    	");

    	if( $q->num_rows() ) return FALSE;
    	else return TRUE;
    }
    
    // GET LIST OF O.R. COLLECTION 
    function get_ors( $date, $source, $user )
    {
    	$q = $this->db->query(
    		"SELECT DISTINCT a.cid, a.orprno, 
    			CONCAT(b.CI_LName, ', ', b.CI_FName, ' ', b.CI_MName) AS fullname,
    			c.CP_BankBranch AS bankbranch, a.tracerefno, a.atmbegbal,
    			a.amtdrawn, a.atmendbal, a.directpaid, a.amtdrawn-a.directpaid AS netdue,
    			c.CP_WithdrawalDay AS wday
    		FROM collection_entry a, client b, client_pension c
    		WHERE a.cid = b.CI_AcctNo
    		AND a.cid = c.CI_AcctNo
			AND b.CI_IsDeleted != 1
			AND c.CP_IsDeleted != 1
    		AND a.duedate = '$date'
    		AND a.orprtype='OR'
    		ORDER BY a.orprno ASC
    	");
    	if($q->num_rows()) return $q->result_array();
    }
    
    // GET LIST OF P.R. COLLECTION 
    function get_prs($date)
    {
    	$q = $this->db->query(
    		"SELECT a.cid, a.orprno, 
    			CONCAT(b.CI_LName, ', ', b.CI_FName, ' ', b.CI_MName) AS fullname,
    			c.CP_BankBranch AS bankbranch, a.tracerefno, a.atmbegbal,
    			a.amtdrawn, a.atmendbal, a.directpaid, a.amtdrawn-a.directpaid AS netdue,
    			c.CP_WithdrawalDay AS wday
    		FROM collection_entry a, client b, client_pension c
    		WHERE a.cid = b.CI_AcctNo
    		AND a.cid = c.CI_AcctNo
    		AND a.duedate = '$date'
    		AND a.orprtype='PR'
    		ORDER BY fullname ASC
    	");
    	if($q->num_rows()) return $q->result_array();
    }
    
    // GET OR / PR NUMBER
    function get_op($type)
    {
    	$q = $this->db->query(
    		"SELECT `value`
    		FROM `parameter`
    		WHERE `group`='COLLECTION'
    		AND `status`=1
    		AND `code`='$type';
    	");

    	if($q->num_rows())
    	{
    		return $q->row()->value;
    	}else return'';
    }
    
	function cwd_submit($post)
	{
		//print_r($post);
		//$bill_ids = explode('.', $post['bid']);

		$this->db->trans_start();

		$this->db->set('LH_WithdrawalDate', $post['wdate']);
		$this->db->where('CI_AcctNo', $post['cid']);
		$this->db->update('ln_hdr') or die(mysql_error());

		$this->db->set('CP_WithdrawalDay', $post['wdate']);
		$this->db->where('CI_AcctNo', $post['cid']);
		$this->db->update('client_pension') or die(mysql_error());
        
        $bill_ids = $this->db->query("
            SELECT bill_id
            FROM nhgt_bills.header
            WHERE CI_AcctNo='".$post['cid']."'
            AND loantrans!='SPEC'
        ")->result_array();
        
		foreach ($bill_ids as $id)
		{
			if($id!='')
			{
				$get = $this->db->query(
					"SELECT bill_id, billdate
					FROM nhgt_bills.header
					WHERE bill_id='".$id['bill_id']."';
				")->row();
				$billdate = $get->billdate;
				
                // 2015-09-02 Added condition if $pot['wdate'] > last day of month
                $ldate = date('Y-m-t', strtotime($billdate));
                $intdate = intval(date('d', strtotime($ldate)));
                if($post['wdate']>$intdate)
                {
                    $wdate = date('d', strtotime($ldate));
                } else {
                    $wdt = date('Y-m-'.$post['wdate'], strtotime($billdate));
                    $wdate = date('d', strtotime($wdt));
                }
                $bdate = date('Y', strtotime($billdate)).'-'.
					date('m', strtotime($billdate)).'-'.$wdate;
				echo $bdate;
                $bdate = date('Y-m-d', strtotime($bdate));

				$this->db->set('billdate', $bdate);
				$this->db->where('bill_id', $id['bill_id']);
				$this->db->update('nhgt_bills.header') or die(mysql_error());
			}
		}
		

		$this->db->trans_complete();

		return TRUE;
	}
    
	function getID( $a, $b )
	{
		$t = $this->db->query(
			"SELECT ID
			FROM ln_ldgr
			WHERE CI_AcctNo='$a'
			AND LH_PN='$b'
			ORDER BY ID DESC;
		");
		if($t->num_rows())
		{
			return $t->row()->ID+1;
		}else return 1;
	}
    
	function get_loan_header_dtls($a, $b)
	{
		$cres = $this->db->query(
			"SELECT LH_BankAcctNo, LH_BranchCode, LH_LoanTrans
			FROM ln_hdr
			WHERE CI_AcctNo='$a'
			AND LH_PN='$b';
		");
		if($cres->num_rows())return $cres->row();
		else return FALSE;
	}
    
	function get_clientname($a)
	{
		$b = $this->db->query(
			"SELECT CONCAT(CI_LName, ',', CI_FName, ' ', CI_MName) AS name
			FROM client
			WHERE CI_AcctNo='$a';
		");
		if($b->num_rows())return $b->row()->name;
		else return 'NO RECORD';
	}
    
	function get_entry_number( $a, $other='' )
	{
        $other = str_replace('.', '', $other);
		$b = $this->db->query(
			"SELECT `number`
			FROM ".($other?"$other.":'')."entries
			WHERE entry_type='$a'
			ORDER BY `number` DESC
			LIMIT 1;
		");
		if($b->num_rows())return $b->row()->number+1;
		else return 1;
	}
    
	function collection_submit($post)
	{
		$now = date('Y-m-d H:i:s');
        
        if($post['ptype'] == 'TR')
        {
    		$refs = explode('.', $post['billref']);
    		$refupdate='';
    		foreach ($refs as $ref)
    		{
    			if($ref) $refupdate.="$ref,";
    		}
    		$refupdate=substr($refupdate,0,strlen($refupdate)-1);
    		$refupdate="IN ($refupdate)";
    		
    		$this->db->trans_start();

    		$datas = array(
    			'cid'		 => $post['cid'],
    			'paytype'	 => $post['ptype'],
    			'bill_id'	 => $post['billref'],
    			'tracerefno' => $post['traceno'],
    			'atmbegbal'  => n($post['begbal']),
    			'amtdrawn'	 => n($post['amtdrawn']),
    			'atmendbal'  => n($post['endbal']),
    			'directpaid' => $post['rfw']?n($post['rfw']):'0.00',
    			'duedate'	 => date('Y-m-d', strtotime($post['duedate'])),
    			'orprtype'	 => "",
    			'orprno'	 => "",
    			'encby'		 => $this->session->userdata('user_name'),
    			'encdate'	 => $now,
    			'banktopost' => $post['bank'],
                'receipt_image' => $post['receiptimg']
    		);
    		$this->db->insert('collection_entry', $datas) or die( print_r($datas) );

    		$this->db->query("
                UPDATE nhgt_bills.header
    			SET collectby='{$this->session->userdata('user_name')}',
    				datecollected='$now'
    			WHERE bill_id $refupdate;
    		") or die(mysql_error());
    		
    		$this->db->trans_complete();
        } elseif($post['ptype'] == 'SI') {
            $this->db->trans_start();
            
            $q = $this->db->query("SELECT CONCAT(a.CI_LName, ', ', a.CI_FName, ' ', a.CI_MName) AS ci_name,
                    b.CP_WithdrawalDay as wday,
                    b.CP_PType as ptype,
                    b.CP_BankAcctNo as bankacctno,
                    b.CP_BankBranch as bankbranch,
                    a.CI_Source as paymenttype
                FROM client a, client_pension b
                WHERE a.CI_AcctNo = b.CI_AcctNo
                AND a.CI_AcctNo = '" . $post['cid'] . "'
                AND a.CI_IsDeleted = 0
                AND b.CP_IsDeleted = 0
            ")->result_array();
            
            foreach($q as $q)
            {
                $blldt = date('Y-m-t');
                $yrmo = explode('-', $blldt);
                $intdate = intval(date('d', strtotime($blldt)));
                if($q['wday']>$intdate)
                {
                    $billdate = $yrmo[0].'-'.$yrmo[1].'-'.$intdate;
                    $billdate = date('Y-m-d', strtotime($billdate));
                } else {
                    $billdate = $yrmo[0].'-'.$yrmo[1].'-'.$q['wday'];
                    $billdate = date('Y-m-d', strtotime($billdate));
                }
                
                $this->db->select("value");
                $this->db->where("code", 'BRANCH');
                $param = $this->db->get("parameter")->row();
                $branch = $param->value;
                $branch = explode(';', $branch);
                
                $data = array(
                    'billtype'      => 'auto',
                    'branchcode'    => $branch[0],
                    'CI_AcctNo'     => $post['cid'],
                    'LH_PN'         => 'ASI-' . date('Y-m-d') . '-001',
                    'loantrans'     => 'SPEC2',
                    'billdate'      => $billdate,
                    'name'          => $q['ci_name'],
                    'paytype'       => $q['paymenttype'],
                    'bankacctno'    => $q['bankacctno'],
                    'bankbranch'    => $q['bankbranch'],
                    'pentype'       => $q['ptype'],
                    'duration'      => date('F Y') . ' - ' . date('F Y'),
                    'terms'         => 1,
                    'balance'       => 2000,
                    'amtodrawn'     => 2000,
                    'generateby'    => $this->session->userdata('user_name'),
                    'dategenerate'  => date('Y-m-d H:i:s'),
                    'collectby'     => '',
                    'datecollected' => date('0000-00-00 00:00:00'),
                    'status'        => NULL
                );
                $this->db->insert('nhgt_bills.header', $data) or die($this->db->_error_message());
            }
            
            $query = $this->db->query("SELECT bill_id
                FROM nhgt_bills.header
                WHERE loantrans='SPEC2'
                AND CI_AcctNo='" . $post['cid'] . "'");
            $tmps = $query->result_array();
            
            $billref = '';
            
            foreach($tmps as $tmp)
            {
                $billref .= $tmp['bill_id'] . '.';
            }
            
             $datas = array(
                'cid'        => $post['cid'],
                'paytype'    => $post['ptype'],
                'bill_id'    => $billref,
                'tracerefno' => $post['traceno'],
                'atmbegbal'  => n($post['begbal']),
                'amtdrawn'   => n($post['amtdrawn']),
                'atmendbal'  => n($post['endbal']),
                'directpaid' => $post['rfw']?n($post['rfw']):'0.00',
                'duedate'    => date('Y-m-d', strtotime($post['duedate'])),
                'orprtype'   => "",
                'orprno'     => "",
                'encby'      => $this->session->userdata('user_name'),
                'encdate'    => $now,
                'banktopost' => $post['bank'],
                'receipt_image' => $post['receiptimg']
            );
            $this->db->insert('collection_entry', $datas) or die( print_r($datas) );
            
            $this->db->query("
                UPDATE nhgt_bills.header
                SET collectby='{$this->session->userdata('user_name')}',
                    datecollected='$now'
                WHERE loantrans='SPEC2';
            ") or die(mysql_error());
            
            $this->db->trans_complete();
        }

		$this->load->helper('code');

		return array(
			'isOkay' => TRUE,
			'script' => trims("
				var a=$('#{$post['cid']}',parent.opener.document).attr('tag');
				$('.'+a,window.opener.document).css('background-color','#F9F7AD');
				window.close();
			")
		);
	}


	function total_loans($clientcode, $loan_ref, $date)
	{
		$loans = explode('.', $loan_ref);
		
		$or='';
		foreach($loans as $loan)
		{
			if($loan)
			$or .= "'$loan',";
		}
		$or = "IN (".substr($or,0,strlen($or)-1).")";
		$sql = "SELECT SUM(amtodrawn) AS total
			FROM nhgt_bills.header
			WHERE CI_AcctNo='$clientcode'
			AND LH_PN $or
			AND billdate='$date';
		";
		$res = $this->db->query( $sql )->row();
		$total = $res->total;
		unset($res, $or, $loans, $sql);
		return $total;
	}


	function get_client_details($code)
	{
		$this->db->select(array('name', 'pentype', 'paytype', 'bankbranch'));
		$this->db->where('CI_AcctNo', $code);
		$this->db->order_by('billdate', 'desc');
		$res = $this->db->get('nhgt_bills.header')->row();
		return $res;
	}


	


}
