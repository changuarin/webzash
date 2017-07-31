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
	color: darkblue;
}
</style>
<script>$(document).ready(function(){
	$('input.mbill').click(function()
	{
		var b_=$($(this).parent().parent().find('td')[1]).html();
		var c_=$('input.mbill').index(this);
		var d_=$('#yof',parent.document).val();
		var e_=parseFloat($('#mof',parent.document).val())+1;
		if(confirm('Are you sure you want to Bill this Client?'))
		{
			$.post(
				'../../../a_',
				{a:'<?=$cid?>',b:b_,c:c_,d:d_,e:e_},
				function(z)
				{
					eval(z);
				}
			);
		}
	});	
});</script>
<body style="margin:0px;"><table width="100%"border="1"cellpadding="0"cellspacing="0">
	<tr>
		<th align="left">Bank Branch</th>
		<th align="left"colspan="2">Loan Reference</th>
		<th align="left">Loan Date</th>
		<th>Duration</th>
		<th align="left">Terms</th>
		<th align="right">Balance</th>
		<th align="right">Loan Amount</th>
		<th>&nbsp;</th>
	</tr><?

	if(isset($data)&&count($data))
	{
		foreach ($data as $d)
		{
			$duration = substr(date('F', strtotime($d['LH_StartDate'])),0,3).
				date(' Y', strtotime($d['LH_StartDate'])).' - '.
				substr(date('F', strtotime($d['LH_EndDate'])),0,3).
				date(' Y', strtotime($d['LH_EndDate']));
			echo"<tr class='data sel'>
			<td>{$d['LH_BankBranch']}</td>
			<td>{$d['LH_PN']}</td>
			<td>{$d['LH_LoanTrans']}</td>
			<td>".date('m/d/Y',strtotime($d['LH_LoanDate']))."</td>
			<td>$duration</td>
			<td align='right'>{$d['LH_Terms']}</td>
			<td align='right'>{$d['LH_Balance']}</td>
			<td align='right'>{$d['LH_MonthlyAmort']}</td>
			<td align='right'>".
				($d['isBilled']?'Billed':"<input class='mbill'type='button'value='Bill this'/>").
			"</td>
			</tr>";
			flush();
		}

	}

?></table>