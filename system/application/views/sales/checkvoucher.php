<script>
	$(document).ready(function()
	{
		$('#cl').attr('src','cvlist');
		$('#cv').attr('src','checkvoucherform');
		var clHeight = $('#cl').contents().height();
		var clHeight = clHeight*2.9;
		$('#cl').css('height', clHeight+'px');
		var cvHeight = $('#cv').contents().height();
		var cvHeight = cvHeight*2.9;
		$('#cv').css('height', cvHeight+'px');
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
<table cellpadding="0"cellspacing="0"width="100%"border="0"height="430">
	<tr valign="top">
		<td width="25%"class="border1"><iframe id="cl"class="lf"width="100%"frameborder="0"></iframe></td>
		<td width="75%"class="border2"><iframe id="cv"width="100%"frameborder="0"></iframe></td>
	</tr>
</table>