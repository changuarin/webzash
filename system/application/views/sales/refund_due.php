<script>
	$(document).ready(function()
	{
		var h=$(document).find('body').height()/1.35;
		$('#cl').attr('src','listdue').attr('height',h);
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
	<tr valign="top"id="">
		<td width="70%"class="border1"><iframe id="cl"class="lp"width="100%"frameborder="0"></iframe></td>
	</tr>
</table>