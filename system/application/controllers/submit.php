<?php

class Submit extends Controller
{
	function n($val)
	{
		return str_replace(',', '', $val);
	}

	function a()
	{
		$tmp = explode('.', $_POST['colid']);
		$data = array(
			'queno'			=> $_POST['queno'],
			'colid'			=> $_POST['colid'],
			'cvrf'			=> $_POST['cvrf'],
			'ci_acctno'		=> $_POST['code'],
			'pnno'			=> $_POST['pnno'],
			'ci_name'		=> $_POST['name'],
			'ci_bankbranch'	=> $_POST['bankbranch'],
			'transdate'		=> $_POST['date'],
			'transtype'		=> $_POST['ptype'],
			'transrefno'	=> $_POST['refno'],
			'atmadvance'	=> 0,
			'advrefund'		=> (isset($_POST['isadvance'])?
				$this->n($_POST['advrefund']):NULL),
			'refunddue'		=> $this->n($_POST['refund']),
			'status'		=> 'draft',
			'queby'			=> $this->session->userdata('user_name'),
			'proby'			=> '',
			'appby'			=> '',
			'remarks'		=> $_POST['remarks']
		);

		$this->db->insert('refund_que', $data) or die( $this->db->_error_message() );

		die("<script type=\"text/javascript\"src=\"".base_url().
			"system/application/assets/js/jquery.min.js\"></script>
			<script>$($('#tr{$tmp[0]}',window.opener.document).".
			"find('td')[4]).html('<label class=\'que\'>{$_POST['queno']}</label>');".
			"window.close();</script>");
	}
}