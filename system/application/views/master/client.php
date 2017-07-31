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
			<iframe id="client_list" class="master_client" width="100%" frameborder="0"></iframe>
		</td>
		<td width="75%" class="border2">
			<iframe id="client_form" width="100%" frameborder="0"></iframe>
		</td>
	</tr>
</table>
<script src="<?= base_url() ?>system/application/assets/js/jquery-2.1.3.min.js" type="text/javascript"></script>
<script>
	$(document).ready(function()
	{
		$('#client_list').prop('src', 'client_list/PEN/A');
		$('#client_form').prop('src', 'client_form');
		var listHeight = $('#client_list').contents().height();
		var listHeight = listHeight * 2.9;
		$('#client_list').css('height', listHeight + 'px');
		var formHeight = $('#client_form').contents().height();
		var formHeight = formHeight * 2.9;
		$('#client_form').css('height', formHeight + 'px');
	});
</script>