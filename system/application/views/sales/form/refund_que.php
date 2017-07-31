<? $pt=explode('.', $ptype) ?><!DOCTYPE html>
<html>
<head>
<title>Refund Que</title>

</head>
<style>

table{
	border-collapse: collapse;
	}
#table{
	background-color: #FEFEA6;
	}
#wrap{
	width:750px;
	margin:0px auto;	
	background:white;
	}

.w0{
	width:100px;
}
.w1{
	width:250px;
}

#input{
	width: 70%;
	}



body{
	margin-top: 30px;
	background-color: #FEFEA6;
	}

.lhov{
	text-decoration: none;
	cursor: pointer;
	color: darkblue;
}

.lhov:Hover{
	text-decoration: underline;
	color: blue;
}
.que{
	font-weight: bold;
	font-size: 250%;
	border:3px outset #A9A9A9;
	background-color:#F6A735;
	color:#058431;
}
.disabled{
	background-color: #FFFFAA;
	border:1px inset gray;
}
.hide{
	display: none;
}

</style>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/rsm.custom.js"></script>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/rsm.js"></script>
<script>
	$(document).ready(function()
	{
		$('input').css('text-align','center');
		$('#a').val(Format('<?=$data->atmbegbal?>',2));
		$('#b').val(Format('<?=$data->amtdrawn?>',2));
		$('#v').val(Format('<?=$data->atmendbal?>',2));
		$('#d').val('<?=date('Y-m-d')?>');
		$('#e').css('text-align','left');
		$('#j').val('<?=date('Y-m-d')?>');
		$('#m').val('<?=$butal?>');
		$('#c').val('<?=$data->atmendbal + $butal?>');

		$('#f').change(function()
		{
			if($(this).is(':checked'))
			{
				var z=$('#e').val();
				if(z.indexOf('ADVANCE REFUND ')==-1)
				z+='ADVANCE REFUND ';
				$('#e').val(z);
				$('.adv').show('slow');
				$('#g').focus();
			}else
			{
				var z=$('#e').val();
				if(z.indexOf('ADVANCE REFUND')>-1)
				z=z.replace(/ADVANCE REFUND /g,'');
				$('#e').val(z);
				$('.adv').hide('slow');
				$('#g').val('');
				$('#i').focus();
			}
		});

		/*$('#i').keyup(function()
		{
			var a=parseFloat(N($('#c').val()));
			var b=parseFloat(N($('#i').val()));
			if(a>=b&&$('#e').val()!='')
				$('#h').attr('disabled',false);
			else $('#h').attr('disabled',true);
		});

		$('#e').keyup(function()
		{
			var a=parseFloat(N($('#c').val()));
			var b=parseFloat(N($('#i').val()));
			if(a<=b&&$(this).val()!='')
				$('#h').attr('disabled',false);
			else $('#h').attr('disabled',true);
		});*/
		
		$('input#h').popupWindow({ 
			centerBrowser: 1,
			windowName: 'form2',
			windowURL: '<?=base_url()?>index.php/sales/printform',
			width: 950,
			height: 500
		});

		$('#i').focus();
		
		$('#e').blur(function(){
			var a = $(this).val().toUpperCase();
			$(this).val(a);
		});

		$('#ifldgr').attr('src','../../viewledgerque/<?=$trid?>');

	});
</script>
<body>
<form id="k" method="post">	
<input type="hidden" id="s" name="colid"value='<?=$trid?>'/>
<input type="hidden" id="l" name="queno"value='<?=$queno?>'/>
<input type="hidden" id="n" name="cvrf"/>
<input type="hidden" name="ptype"value='<?=$pt[0]?>'/>
<input type="hidden" id="o" name="bankbranch"value='<?=$pt[1]?>'/>
<input type="hidden" id="p" name="code"value='<?=$data->clientcode?>'/>
<input type="hidden" id="q" name="pnno"value='<?=$pnno?>'/>
<input type="hidden" id="r" name="name"value='<?=$data->clientname?>'/>
<input type="hidden" id="t" name="refund"/>
<input type="hidden" id="u" name="refno"/>
<table width="80%" cellpadding="3px" id="table"align="center">
	<tr>
		<td width="21%">Queue No.:</td>
		<th colspan="2">REFUND STATUS</th>
	</tr>
	<tr>
		<td rowspan="3"valign="top">
		<center class="que"><?=$queno?></center></td>
		<td align="right">Date:</td>
		<td><input type="date"name="date"id="j"/></td>
	</tr>
	<tr>
		<td class="w0"align="right">Client ID:</td>
		<td><font color="blue"><?=$data->clientcode?></font><?
		?></td>
	</tr>
	<tr>
		<td align="right">Name:</td>
		<td><font color="blue"><?=$data->clientname?></font><?
		?></td>
		
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="right">PnNo:</td>
		<td><?=$pnno?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="right">ATMBeg:</td>
		<td><input type="text" name="ATMBeg"id="a"class="disabled"readonly></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="right">ATMDrawn:</td>
		<td><input type="text" name="ATMDrawn"id="b"class="disabled"readonly></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="right">ATMEnd:</td>
		<td><input type="text" name="ATMEnd" id="v"class="disabled"readonly></td>
	</tr>
	<tr>
		<td colspan='2'align='right'><small>Previous ATM Ending<br>balance (<b><i>Butal</i></b>)</small></td>
		<td><input type="text" name="butal" id="m"class="disabled"readonly></td>
	</tr>
	<tr>
		<td colspan='2'align='right'>Total</small></td>
		<td><input type="text" name="butal" id="c"class="disabled"readonly></td>
	</tr>
	<tr>
		<td colspan="2"id="lref"align="right">* Refund Due:</td>
		<td><input id="i"type="text"><?
		?><label>&nbsp;<input type="checkbox"name="isadvance"id="f"/><?
		?>&nbsp;<span class="lhov">ADVANCE?</span></label></td>
	</tr>
	<tr class="hide adv">
		<td colspan="2"id="lref"align="right">* Advance:</td>
		<td><input id="g"type="text" name="advrefund"></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="right">* Remarks:</td>
		<td><input type="text" name="remarks" id="e"class="w1"><?
		?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><input id="h"type="button" value="Print Form"></td>
	</tr>
	<tr>
		<td colspan="3"align="center"style="padding-top:15px;">
			<iframe id="ifldgr"frameborder="0"style="width:100%;border:1px solid gray"></iframe>
		</td>
	</tr>
</table>
</form>

</body>

</html>
