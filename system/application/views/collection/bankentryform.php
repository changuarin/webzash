<script>
	$(document).ready(function()
	{
		var z=[$('#dt'),$('#et'),$('#am'),$('#na'),$('#sb'),$('#ef'),$('#ba'),$('.orn'),$('#or')];
		
		z[4].click(function()
		{
			if(z[1].val())
			{
				if(z[2].val())
				{
					if(z[3].val())
					{
						if(z[6].val())
						{
							if(z[1].val()!='6003'&&z[1].val()!='1011')
							{
								if(confirm('Are you sure you want to Submit this transaction?'))
								{
									z[5].attr('action','bnkntrypst').submit();
								}
							}else
							{
								if(z[8].val())
								{
									if(confirm('Are you sure you want to Submit this transaction?'))
									{
										z[5].attr('action','bnkntrypst').submit();
									}
								}else
								{
									alert('Please input a O.R. Number');
									z[8].focus();
								}
							}
						}else
						{
							alert('Please input a bank.');
							z[6].focus();
						}
					}else
					{
						alert('Please input a remarks.');
						z[3].focus();
					}
				}else
				{
					alert('Please input an amount.');
					z[2].focus();
				}
			}else
			{
				alert('Please select a type.');
				z[1].focus();
			}
		});

		
		z[1].change(function()
		{
			if($(this).val()=='6003'||$(this).val()=='1011')
			{
				z[7].show('slow');
			}else{
				z[7].hide('slow').val('');
			}
		});


		z[8].change(function()
		{
			if($(this).val()!='')
			{
				$(this).attr('disabled', true);
				z[4].val('Please Wait O.R. Validation...').attr('disabled', true);
				var or=$(this).val();
				$.ajax({
					type: 'POST',
					url : 'validateOR',
					data: {a: or},
					error: function(er)
					{
						alert(er);
					},
					success: function(res)
					{
						if(res.res) eval(res.eval);
						z[8].attr('disabled', false);
						z[4].val('Submit').attr('disabled', false);
					}
				});
			}
		});
		
		// 2015-09-11 Auto fill narration
		$('#et').change(function()
		{
			var et = $('#et').val();
			var na = '';
			switch(et)
			{
				case '6001':
				na = 'DEPOSIT INTEREST';
				break;
				case '5015':
				na = 'BANK CHARGE';
				break;
				case '1201':
				na = 'REFUND NR';
				break;
				case '6003':
				na = 'REMITTANCE CHARGE';
				break;
				case '1011':
				na = 'REDEPOSIT';
				break;
			}
			z[3].val(na);
		});

	});
</script>
<style>
	.border1{
		border:1px solid gray;
	}
	.border2{
		border-top:1px solid gray;
		border-right:1px solid gray;
		border-bottom:1px solid gray;
	}
	#fromdate, #todate {
		text-align:center;
	}
	.tbl{padding-left: 25px; height: 250px}
	.td0{padding-right: 10px;}
	#amt, #na, #dt{width:170px;}
	#na{height: 75px;}
	#dt{text-align: center;}
	.hide{display: none}
</style>
<form id="ef"method="post">
<big>Bank Entry</big><br><br>
<table cellpadding="0"cellspacing="0"border="0"class="tbl">
<tr>
	<td align="right"class="td0">Date:</td><td>
		<input id="dt"name="a"type="date"value="<?=date('Y-m-d')?>"/>
	</td>
</tr>
<tr>
	<td align="right"class="td0">Bank:</td><td>
	<select id="ba"name="b">
		<option value="">{ Select a bank }</option><?
		echo $bank;	
	?></select></td>
</tr>

<tr>
	<td align="right"class="td0">Entry Type:</td><td>
	<select id="et"name="c">
		<option value="">{ Select a type }</option>
		<option value="6001">Interest on Deposit (Credit)</option>
		<option value="5015">Bank charges (Debit)</option>
		<option value="1201">Refund NR (Debit)</option>
		<option value="6003">Remittance Charges (Credit)</option>
		<option value="1011">Redeposit Petty Cash Fund (Credit)</option>
	</select></td>
</tr>
<tr class="hide orn">
	<td align="right"class="td0">O.R. Number:</td><td>
		<input id="or"name="f"type="text"placeholder="O.R.#"/>
	</td>
</tr>

<tr>
	<td align="right"class="td0">Amount:</td><td>
		<input id="am"name="d"type="text"placeholder="Amount"/>
	</td>
</tr>
<tr valign="top">
	<td align="right"class="td0">Narration:</td><td>
		<textarea id="na"name="e"placeholder="Remarks"></textarea>
	</td>
</tr>
<tr valign="top">
	<td align="right"class="td0">&nbsp;</td><td>
		<input id="sb"type="button"value="Submit"/>
	</td>
</tr>
</table>
</form>