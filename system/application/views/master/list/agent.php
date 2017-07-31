<style>
	body,
	table {
		font-family: 'Arial';
		font-size: 12px;
		cursor: default;
		margin: 0;
		padding: 0;
	}
	
	.tag0 {
		background-color: #f0f0f0;
	}
	
	.tag1 {
		background-color: #d0d0d0;
	}
	
	.tag0:hover,
	.tag1:hover {
		background-color: #99ffff;
	}
	
	.search-table {
		background-color: #fff;
		border: 0;
		width: 300px;
	}
	
	.list-table {
		border-collapse: collapse;
		width: 400px;
	}
	
	.list-table th {
		border-bottom: 2px solid #999;
		background-color: #eee;
		vertical-align: bottom;
	}
	
	.list-table td {
		border-bottom: 1px solid #ccc;
	}
	
	.list-table td.alt {
		background-color: #ffc;
		background-color: rgba(255, 255, 0, 0.2);
	}
	
	.red-text {
		color: #ff0000;
	}
	
	.active {
		background-color: #ffff00;
		color: #ff0000;
	}
</style>
<body>
	<form id="searchForm" method="post">
		<table class="search-table">
			<tbody>
				<tr>
					<td>
						<input id="name" type="text" name="name" placeholder="Lastname, Firstname" value="<?php echo isset($name) ? $name : ''; ?>">
						<input type="submit" value="Search">
					</td>
				</tr>
			</tbody>
		</table>
	</form>
	<br>
	<table class="list-table">
		<tbody>
		<?php if( ! empty($agents)) : ?>
			<?php $i = 0; ?>
			<?php foreach($agents as $agent) : ?>
				<tr>
					<td class="ccid tag<?php echo $i . ' ' . $uri_seg; ?>""><?php echo $agent->AI_RefNo . ' ' . $agent->name; ?></td>
				</tr>
			<?php endforeach; ?>
			<?php $i = $i ? 0 : 1; ?>
		<?php endif; ?>
		</tbody>
	</table>
</body>

<script src="<?= base_url() ?>system/application/assets/js/jquery-2.1.3.min.js" type="text/javascript"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('#name').blur(function() {
			var strUpper = $(this).val().toUpperCase();
			
			$(this).val(strUpper);
		});
		
		$('.ccid').click(function()
		{
			var className = $('#agentList', parent.document).prop('class');
			var data = $(this).html().split(' ');
			
			if(className == 'agent-form')
			{
				$.post(
					'show_agent',
					{airefno: data[0]},
					function(res) {});
			} else {
				var data2 = $(this).prop('class').split(' ');
				var name = data[1];
				
				if(data[2] != null || data[2] != '')
				{
					name += ' ' + data[2];
				}
				
				if(data[3] != null || data[3] != '')
				{
					name += ' ' + data[3];
				}
				
				if(data2[2] == 'agent1_list')
				{
					$('#agent1_name', window.opener.document).val(name);
					$('#ci_agent1', window.opener.document).val(data[0]);
				} else if(data2[2] == 'agent2_list') {
					$('#agent2_name', window.opener.document).val(name);
					$('#ci_agent2', window.opener.document).val(data[0]);
				}
				
				window.close();
			}
			
			
			$('.ccid').removeClass('active');
			$(this).addClass('active');
		});
	});
</script>
