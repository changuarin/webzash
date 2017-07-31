<script>
	$(document).ready(function()
	{
		var h=$(document).find('body').height()/1.35;
		$('#ca').attr('src','adjlist/'+$('#fromdate').val()+'/'+$('#todate').val()).attr('height',h);
		
		$('#goButton').click(function(){
			var a = $('#fromdate').val();
			var b = $('#todate').val();
			$('#ca').attr('src','adjlist/'+a+'/'+b).attr('height',h);
		});
		
		$('#reloadButton').click(function(){
			$('#ca').attr('src', $('#ca').attr('src'));
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
</style>
<table cellpadding="0"cellspacing="0"width="100%"border="0">
	<tr>
		<td style="padding:0 0 2px 2px;">
			From:&nbsp;<input id="fromdate"name="fromdate"type="date"value="<?=date('Y-m-01')?>"class="date"/>&nbsp;
			To:&nbsp;<input id="todate"name="todate"type="date"value="<?=date('Y-m-t')?>"class="date"/>&nbsp;
			<input id="goButton" type="button" name="goButton" value="Go" />&nbsp;
			<input id="reloadButton" type="button" name="reloadButton" value="Reload" />
		</td>
	</tr>
	<tr valign="top">
		<td width="70%"class="border1"><iframe id="ca"class="lp"width="100%"frameborder="0"></iframe></td>
	</tr>
</table>