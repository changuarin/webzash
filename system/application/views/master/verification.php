<style>
	.border1 {
		border: 1px solid gray;
	}
	
	.border2 {
		border-top: 1px solid gray;
		border-right: 1px solid gray;
		border-bottom: 1px solid gray;
	}
</style>
<table cellpadding="0" cellspacing="0" width="100%" border="0" height="420">
	<tr valign="top">
		<td width="25%" class="border1">
			<iframe id="clientList2" class="client-form2" width="100%" frameborder="0"></iframe>
		</td>
		<td width="75%" class="border2">
			<iframe id="clientForm2" width="100%" frameborder="0"></iframe>
		</td>
	</tr>
</table>

<script src="<?= base_url() ?>system/application/assets/js/jquery-2.1.3.min.js" type="text/javascript"></script>
<script>
	$(document).ready(function()
	{
		$('#clientList2').prop('src', 'client_list2');
		
		var listHeight = $('#clientList2').contents().height();
		var listHeight = listHeight * 2.9;
		
		$('#clientList2').css('height', listHeight + 'px');
		
		$('#clientForm2').prop('src', 'client_form2');
		
		var formHeight = $('#clientForm2').contents().height();
		var formHeight = formHeight * 2.9;
		
		$('#clientForm2').css('height', formHeight + 'px');
	});
</script>