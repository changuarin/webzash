<?php

class z extends Controller
{
	function z()
	{
		parent::Controller();
		
        date_default_timezone_set('Asia/Manila');

		return;
	}


	// 20150806 -> rsm -> remove billing attachment
	function zy()
	{
		$this->output->enable_profiler(TRUE);
		$this->db->trans_start();

		switch (substr($_POST['a'],0,2)) {

			case 'BL':

				$billid = str_replace('BL', '', $_POST['a'] );
				$tr = str_replace('TR', '', $_POST['b'] );
				$this->db->select('uid , bill_id');
				$this->db->where('uid', $tr);
				$this->db->like('bill_id', $billid.'.');
				$r = $this->db->get('collection_entry')->row();
				
				$billtmp = $r->bill_id;
				$billtmp = str_replace($billid.'.', '', $billtmp);

				$this->db->set('bill_id', $billtmp);
				$this->db->where('uid', $tr);
				$this->db->update('collection_entry') or die( $this->db->_message_error() );

				$this->db->where('bill_id', $billid);
				$r = $this->db->get('nhgt_bills.header')->row();

				$data = array(
					'sql' => "INSERT INTO nhgt_bills.header SET bill_id={$r->bill_id},billtype='{$r->billtype}',".
						"branchcode='{$r->branchcode}',CI_AcctNo='{$r->CI_AcctNo}',LH_PN='{$r->LH_PN}',".
						"loantrans='{$r->loantrans}',billdate=".
						($r->billdate?"'{$r->billdate}'":'NULL').",".
						"`name`='{$r->name}',paytype='{$r->paytype}',bankacctno='{$r->bankacctno}',".
						"bankbranch='{$r->bankbranch}',pentype='{$r->pentype}',duration='{$r->duration}',".
						"terms='{$r->terms}',balance='{$r->balance}',amtodrawn='{$r->amtodrawn}',".
						"generateby='{$r->generateby}',dategenerate=".
						($r->dategenerate?"'{$r->dategenerate}'":'NULL').",".
						"collectby='{$r->collectby}',datecollected=".
						($r->datecollected?"'{$r->datecollected}'":'NULL').";",
					'deletedby' => $this->session->userdata('user_name'),
					'datedeleted' => date('Y-m-d H:i:s')
				);
				$this->db->insert('nhgt_master.deleted_trans', $data) or die( $this->db->_message_error() );

				$this->db->where('bill_id', $billid);
				$this->db->delete('nhgt_bills.header') or die( $this->db->_message_error() );

				$this->db->trans_complete();

				die("var a=$('#ifldgr',parent.document).attr('src');".
					"$('#ifldgr',parent.document).attr('src',a);");

			break;
			
		}

	}


	function zz()
	{
		$this->db->select('value');
		$this->db->where('code', 'FT');
		$this->db->where('group', 'APPROVER');
		$this->db->where('status', 1);
		$this->db->limit(1);
		$q = $this->db->get('parameter');
		if($q->num_rows()):
			$this->session->set_userdata('fta', $q->row()->value);
		endif;

		$this->db->where('type', 'ft');
		$this->db->where('mto', $this->session->userdata('user_name') );
		$this->db->where('status', 1);
		$this->db->order_by('uid');
		$this->db->limit(1);
		$q = $this->db->get('nhgt_master.notification');
		if( $q->num_rows() ):
			$t['a'] = TRUE;
			$q = $q->row();
			$t['b'] = $q->uid;
			$t['c'] = $q->message;
			$t['d']	= $q->mfrom;

			$this->db->set('status', 0);
			$this->db->where('status', 1);
			$this->db->where('uid', $t['b']);
			$this->db->update('nhgt_master.notification');
		else:
			$t['a'] = FALSE;
		endif;
		die( json_encode( $t ) );
	}
	
}

