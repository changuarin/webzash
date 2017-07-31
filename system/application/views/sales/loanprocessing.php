<script>
	$(document).ready(function()
	{
		$('#ll').attr('src','loanList');
		$('#lpf').attr('src','loanProcessingForm');
		var llHeight = $('#ll').contents().height();
		var llHeight = llHeight*2.9;
		$('#ll').css('height', llHeight+'px');
		var lpfHeight = $('#lpf').contents().height();
		var lpfHeight = lpfHeight*2.9;
		$('#lpf').css('height', lpfHeight+'px');
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
<table cellpadding="0"cellspacing="0"width="100%"border="0"height="420">
	<tr valign="top">
		<td width="25%"class="border1"><iframe id="ll"class="lpf"width="100%"frameborder="0"></iframe></td>
		<td width="75%"class="border2"><iframe id="lpf"width="100%"frameborder="0"></iframe></td>
	</tr>
</table>