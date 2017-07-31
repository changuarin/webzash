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
	background-color: #FDD8CA;
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
	margin-top: 10px;
	background-color: #FDD8CA;
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
.message{
	color:red;
	font-style: italic;
}

</style>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/rsm.custom.js"></script>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/rsm.js"></script>
<script><?=(isset($script)?$script:'')?></script>
<body>
<form id="a" method="post">	
<center><big>Adjustment Entry</big></center><center class="message">&nbsp;</center>
<table width="80%" cellpadding="3px" id="table"align="center">
	<tr>
	
		<td class="w0"align="right">Client ID:</td>
		<td><font color="blue"><?=$cid?></font><?
		echo"<input type='hidden'name='cid'value='$cid'/><input type='hidden'name='refid'value='$refid'/>";
		?></td>
		
	</tr>
	<tr>
		<td align="right">Name:</td>
		<td><font color="blue"><?=$ci_name?></font><?
		?></td>
	</tr>
	<tr>
		<td align="right">Date:</td>
		<td>
			<input type="date"name="date"value='<?=date('Y-m-d')?>'/>
		</td>
	</tr>
	<tr>
		<td align="right">Type:</td>
		<td>
			<select name="atype">
				<option value="">{ Select Type }</option>
				<option value="0">ATM Balance</option>
				<option value="1">Advance Refund</option>
				<option value="2">13th Month</option>
				<option value="3">Adjustment</option>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right">Debit/Credit:</td>
		<td>
			<select name="dc">
				<option value="">{ D / C }</option>
				<option value="d">Debit</option>
				<option value="c">Credit</option>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right">Amount:</td>
		<td>
			<input name="amount"placeholder="Please Input Amount"/>
		</td>
	</tr>
	<tr valign="top">
		<td align="right">Remarks:</td>
		<td>
			<textarea name="remarks"style="width: 226px; height: 65px;"></textarea>
		</td>
	</tr>
	<tr>
		<td align="center"colspan="2">
			<input id="b"type="button"value="Close"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input id="c"type="button"value="Submit"/>
		</td>
	</tr>
</table>
</form>

</body>

</html>
