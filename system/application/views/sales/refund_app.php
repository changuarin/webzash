<script>
	$(document).ready(function()
	{
		var h=$(document).find('body').height()/1.35;
		$('#cl').attr('src','list4app').attr('height',h);
		
		$('#reloadButton').click(function(){
			$('#cl').attr('src', $('#cl').attr('src'));
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
</style>
<table cellpadding="0"cellspacing="0"width="100%"border="0">
	<tr>
		<td style="padding:0 0 4px 4px;">
			<input id="reloadButton" type="button" name="reloadButton" value="Reload" />
		</td>
	</tr>
	<tr valign="top"id="">
		<td width="70%"class="border1"><iframe id="cl"class="lp"width="100%"frameborder="0"></iframe></td>
	</tr>
</table>