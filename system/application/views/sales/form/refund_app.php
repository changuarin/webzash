<!DOCTYPE html>
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
<script><?=$script?></script>
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
		<td>&nbsp;</td>
		<td align="right">RFP No:</td>
		<td><input type="text" name="refno"id="a"class="disabled"value="<?=$data->transrefno?>"readonly></td>
	</tr>
	<tr>
		<td colspan='2'align='right'><small>Previous ATM Ending<br>balance (<b><i>Butal</i></b>)</small></td>
		<td><input type="text" name="butal"value="<?=number_format($butal,2);?>"class="disabled"readonly></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="right">ATMEnd:</td>
		<td><input type="text" name="atmend"value="<?=$atmendbal?>"class="disabled"readonly></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="right">AdvanceAmt:</td>
		<td><input type="text" name="advance"class="disabled"value="<?=$data->atmadvance?>"readonly></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="right">Total:</td>
		<td><input type="text" name="total"id="b"class="disabled"value="<?=$data->atmadvance + $butal + $atmendbal?>"readonly></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="right">RefundDue:</td>
		<td><input type="text" name="due"class="disabled"value="<?=$data->refunddue?>"readonly></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="right">Remarks:</td>
		<td><input type="text" name="remarks" id="e"class="w1"value="<?=$data->remarks?>"><?
		?></td>
	</tr>
	<tr>
		<td colspan="3"align="center">
		<input id="i"type="button" value="Insert Adjustment"/>&nbsp;&nbsp;&nbsp;<?
		?><input id="g"type="button" value="Disapproved"/>&nbsp;&nbsp;&nbsp;<?
		?><input id="f"type="button" value="Approved"/>
		</td>
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
