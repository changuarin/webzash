<script>
	$(document).ready(function()
	{
		var h=$(document).find('body').height()/1.35;
		$('#cl').attr('src','list4refund').attr('height',h);
		
		$('#reloadButton').click(function(){
			$('#cl').attr('src', $('#cl').attr('src'));
		});
		
		$('#list-btn').click(function(){
			if($(this).attr('class') == 'list1')
			{
				$(this).removeClass('list1').addClass('list2').attr('value', 'List1');
				$('#cl').attr('src', 'list4refund2').attr('height',h);
			} else if($(this).attr('class') == 'list2') {
				$(this).removeClass('list2').addClass('list1').attr('value', 'List2');
				$('#cl').attr('src', 'list4refund').attr('height',h);
			}
			
		});		
	});
</script>
<style>
	.border1{
		border:1px solid gray;
	}
	.border2{
		border-top: 1px solid gray;
		border-right: 1px solid gray;
		border-bottom: 1px solid gray;
	}
</style>
<table cellpadding="0"cellspacing="0"width="100%"border="0">
	<tr>
		<td style="padding:0 0 2px 2px;">
			<input type="button" id="list-btn" class="list1" value="List2" />
			<input id="reload-btn" type="button" value="Reload" />
		</td>
	</tr>
	<tr valign="top">
		<td width="70%"class="border1"><iframe id="cl"class="lp"width="100%"frameborder="0"></iframe></td>
	</tr>
</table>