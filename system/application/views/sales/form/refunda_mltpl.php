<? $this->load->helper('code');?>
<html>
<head>
<title>Refund Approval</title>

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
		$('#f').click(function()
		{
			if(confirm('Are you sure you want to Approve this transaction?'))
			{
				var a=$('#tr<?=$data->queno?>',window.opener.document);
				$.post('<?=base_url()?>index.php/sales/refapp',
				{'a':'<?=$data->aid?>','b':'<?=$data->cvrf?>','c':$('#b').val()},
				function(b){
					eval(b);
				});
			}
		});

		$('#g').click(function()
		{
			if(confirm('Are you sure you want to Disapprove this transaction?'))
			{
				var a=$('#tr<?=$data->queno?>',window.opener.document);
				$.post('<?=base_url()?>index.php/sales/refappcan',
				{'a':'<?=$data->aid?>'},
				function(b){
					eval(b);
				});
			}
		});
	});
</script>
<body>
<form id="k" method="post">	
<input type="hidden"name="trid"value="<?=$data->aid?>"/>
<table width="80%" cellpadding="3px" id="table"align="center">
	<tr>
		<td width="21%">Queue No.:</td>
		<th colspan="2">REFUND STATUS</th>
	</tr>
	<tr>
		<td rowspan="3"valign="top">
		<center class="que"><?=$data->queno?></center></td>
		<td align="right">Date:</td>
		<td><input type="date"name="date"id="h"value="<?=$data->transdate?>"/></td>
	</tr>
	<tr>
	
		<td class="w0"align="right">Client ID:</td>
		<td><font color="blue"><?=$data->ci_acctno?></font><?
		?></td>
		
	</tr>
	<tr>
		<td align="right">Name:</td>
		<td><font color="blue"><?=$data->ci_name?></font><?
		?></td>
		
	</tr>
	<tr>
		<td colspan="3"align="center">
			<table border="1"width="80%"style='font-size:12px;'>
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
						<td align='right'>".number_format($butal,2)."
						<input id='a'type='hidden'value='$butal'/></td>
					</tr>");
				endif;

				echo trims("<tr>
					<th colspan='3'align='right'>TOTAL</th>
					<th align='right'>".number_format($total,2)."
					<input id='b'type='hidden'value='$total'/></th>
				</tr>");
			?></table>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="right">AdvanceAmt:</td>
		<td><input type="text" name="advance"id="a"class="disabled"value="<?=$data->atmadvance?>"readonly></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="right">RefundDue:</td>
		<td><input type="text" name="advance"id="c"class="disabled"value="<?=$data->refunddue?>"readonly></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="right">TotalAmount:</td>
		<td><input type="text" name="totalamt" id="d"class="disabled"value="<?=($data->atmadvance+$data->refunddue)?>"readonly></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="right">Remarks:</td>
		<td><input type="text" name="remarks" id="e"class="w1"value="<?=$data->remarks?>"><?
		?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><input id="f"type="button" value="Approved">
		<input id="g"type="button" value="Disapproved"></td>
	</tr>

</table>
</form>

</body>

</html>
