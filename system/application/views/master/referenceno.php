<style>	.border1 {		border: 1px solid gray;	}		.border2 {		border-top: 1px solid gray;		border-right: 1px solid gray;		border-bottom: 1px solid gray;	}</style><table cellpadding="0" cellspacing="0" width="100%" border="0" height="420">	<tr valign="top">		<td width="30%" class="border1">			<iframe id="agentList3" class="agent-form2" width="100%" frameborder="0"></iframe>		</td>		<td width="70%" class="border2">			<iframe id="refnoForm" width="100%" frameborder="0"></iframe>		</td>	</tr></table><script src="<?= base_url() ?>system/application/assets/js/jquery-2.1.3.min.js" type="text/javascript"></script><script>	$(document).ready(function()	{		$('#agentList3').prop('src', 'agent_list3');				var listHeight = $('#agentList3').contents().height();		var listHeight = listHeight * 2.9;				$('#agentList3').css('height', listHeight + 'px');				$('#refnoForm').prop('src', 'refno_form');				var formHeight = $('#refnoForm').contents().height();		var formHeight = formHeight * 2.9;				$('#refnoForm').css('height', formHeight + 'px');	});</script>