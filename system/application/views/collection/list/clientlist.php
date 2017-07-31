<?
	$sql = "SELECT DISTINCT(b.CI_AcctNo) AS CI_AcctNo,
			CONCAT(b.CI_LName, ', ', b.CI_FName, ' ',b.CI_MName) AS name
		FROM client b,
			ln_hdr c
		WHERE b.CI_AcctNo = c.CI_AcctNo
		ORDER BY 
			b.CI_LName,
			b.CI_FName,
			c.LH_LoanDate;";
	$data = $this->db->query( $sql )->result_array();

?>
<link type="text/css" rel="stylesheet" href="<?=base_url()?>system/application/assets/css/forms.css">
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.filterTable.js"></script>
<style type="text/css">
.sel{
	background-color: transparent;
	color: black;
}
.sel:Hover{
	background-color: rgba(255, 173, 0, 0.20);
	cursor: pointer;
	color: darkblue;
}
</style>
<script>$(document).ready(function(){

	var tmp=0;
	$('#glif',parent.document).contents().find('.data').each(function()
	{
		var i0=$($(this).find('td')[1]).attr('id');
		$('table.cl').find('.'+i0).remove();		
	});

	$('table.cl').filterTable({
		autofocus: 1,
		placeholder: 'Search Client'
	});

	$('td.sel').click(function()
	{
		$('#m2', parent.document).attr('src', 'gtpnlst/'+$(this).attr('id')+'/'+<?
		?>$('#yof',parent.document).val()+'/'+<?
		?>(parseFloat($('#mof',parent.document).val())+1));
	});
	
});</script>
<body style="margin:0px;"><table class="cl"width="100%"border="1"cellpadding="0"cellspacing="0">
<?
	$isallbilled=TRUE;
	if(isset($data)&&count($data))
	{
		$j=0;
		foreach ($data as $d)
		{
			echo"<tr class='data {$d['CI_AcctNo']}'>
			<td class='sel'id='{$d['CI_AcctNo']}'>{$d['name']}</td>
			</tr>";
			flush();

		}

	}

?></table>