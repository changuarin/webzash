<script>$(document).ready(function(){<?=$script?>});</script>
<style>
	input[type=text],
	textarea,
	select{
		border: 1px solid gray;
		padding: 3px;
	}
	textarea[name=d]{
		width: 250px;
		height: 50px;
	}
	.td1{
		padding-top: 25px;
	}
	.td2{
		padding-top: 15px;
	}
	iframe{
		height: 435px;
		border: 1px solid black;
	}
	.ftmul{
		font-size: 10px;
	}
	.hide{display: none}
	.bl{border-left: 1px solid black}
	.br{border-right: 1px solid black}
	.bt{border-top: 1px solid black}
	.bb{border-bottom: 1px solid black}
	.padr{
		padding-right: 5px;
	}
</style>
<table cellpadding="0"cellspacing="0"width="100%"border="0"height="400"align="center">
<tr>
	<td width="40%"valign="top">
		<form id='frm'method='POST'>
			<table class='ftt'cellpadding="0"cellspacing="0"width="70%"border="0"height="400"align="center">
				<tr>
					<th colspan="3">Fund Transfer Details</th>
			    </tr>
			    <tr>
					<td>Type&nbsp;</td>
					<td><select name="e">
						<option value=''>Select a Type</option>
						<option value='Online'>Online Transaction</option>
						<option value='CvCheckPrint'>with CV / Check printing</option>
					</select></td>
					<td>&nbsp;</td>
			    </tr>
			    <tr>
					<td>Amount</td>
					<td><input name="a"type="text"placeholder="Amount is required"/></td>
					<td>&nbsp;</td>
			    </tr>
			    <tr>
					<td nowrap>Transfer from&nbsp;&nbsp;</td>
					<td><select name="b"></select></td>
					<td>&nbsp;</td>
			    </tr>
			    <tr>
					<td>Transfer to</td>
					<td><select name="c"></select></td>
					<td>&nbsp;</td>
			    </tr>
			    <tr valign="top">
					<td class="td1 rtd">Remarks</td>
					<td class="td1 rtd"><textarea name="d"placeholder="Remarks is required"></textarea></td>
					<td>&nbsp;</td>
			    </tr>
			    <tr>
					<td>&nbsp;</td>
					<td><input id="sbmt" type="button"value="Submit"/></td>
					<td>&nbsp;</td>
			    </tr>
			</table>
			<table class="ftmul hide"width="90%"align="center"cellspacing="0">
			<thead>
				<tr>
					<td class="bl bt">No.</td>
					<td class="bt">Bank</td>
					<td class="bt padr"align="right">Amount</td>
					<td class="bt">Remarks</td>
					<td class="bt br">&nbsp;</td>
				</tr>
			</thead>
			<tbody class="data">
			</tbody>
			<tfooter>
				<tr>
					<td colspan="3"align="right"class="bt padr total">0</td>
					<td colspan="2"align="right"class="bt pd1 td2"valign="middle">
						<input id="submul"type="button"value="Submit"disabled/>
					</td>
				</tr>
			</tfooter>
			</table>
		</form>
	</td>
	<td>
		<iframe src=""width="100%"></iframe>
	</td>
</tr>
</table>