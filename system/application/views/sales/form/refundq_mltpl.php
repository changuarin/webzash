<? $pt=explode('.', $ptype); $this->load->helper('code'); ?><!DOCTYPE html>
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
		$('#e').css('text-align','left');
		$('#j').val('<?=date('Y-m-d')?>');

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

		/*$('#e').change(function()
		{
			var a=parseFloat(N($('#c').val()));
			var b=parseFloat(N($('#i').val()));
			if(a>=b)
				$('#h').attr('disabled',false);
			else $('#h').attr('disabled',true);
		});

		$('#i').change(function()
		{
			var a=parseFloat(N($('#c').val()));
			var b=parseFloat(N($('#i').val()));
			if(a>=b)
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
<input type="hidden" id="r" name="name"value='<?=$data->name?>'/>
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
		<td align="right">Client ID:</td>
		<td><font color="blue"><?=$data->clientcode?></font></td>
	</tr>
	<tr>
		<td align="right">Name:</td>
		<td><font color="blue"><?=$data->name?></font><?
		?></td>
	</tr>
	<tr>
		<td align="right">PnNo:</td>
		<td><?=$pnno?></td>
	</tr>
	<tr>
		<td colspan="3"align="center">
			<table border="1"width="95%">
				<tr>
					<th align="left">Collection Date</th>
					<th align="right">ATMBeg</th>
					<th align="right">ATMDrawn</th>
					<th align="right">ETMEnd</th>
				</tr><?
				
				$total=0;
				
				foreach($mdatas as $mdata):
					echo "<tr>
						<td align='center'>".date('M d, Y',strtotime($mdata->duedate))."</td>
						<td align=\"right\">".number_format($mdata->atmbegbal,2)."</td>
						<td align=\"right\">".number_format($mdata->amtdrawn,2)."</td>
						<td align=\"right\">".number_format($mdata->atmendbal,2)."</td>
					</tr>";
					$total += $mdata->atmendbal;
				endforeach;

				if($butal):
					$total += $butal;
					echo trims("<tr>
						<td colspan='3'align='right'>Previous ATM Ending balance (<b><i>Butal</i></b>)</td>
						<td align='right'>".number_format(floatval($butal), 2)."
						<input id='a'type='hidden'value='$butal'/></td>
					</tr>");
				endif;

				echo trims("<tr>
					<th colspan='3'align='right'>TOTAL</th>
					<th align='right'>".number_format($total,2)."
					<input id='c'type='hidden'value='$total'/></th>
				</tr>");
			?></table>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="right">Date:</td>
		<td><input type="" id="j"name="date" value="<?=date('Y-m-d')?>"/></td>
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

</table>
</form>

</body>

</html>
