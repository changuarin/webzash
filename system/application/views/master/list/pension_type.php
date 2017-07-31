<style>
	body, form, table {
		font-family: 'Arial';
		font-size: 12px;
		cursor: default;
		margin: 0;
		padding: 0;
	}
	
	span {
		color: #ff0000;
	}
	
	.tag1 {
		background-color: #d0d0d0;
	}
	
	.tag1:hover {
		background-color: #99ffff;
	}
	
	.tag0 {
		background-color: #f0f0f0;
	}
	
	.tag0:hover {
		background-color: #99ffff;
	}
	
	.w1 {
		width: 250px;
	}
	
	#list {
	
		margin-top: 50px;
		width: 420px;
	}
	
	table#list {
		border-collapse: collapse;
	}
	
	table#list th {
		border-bottom: 2px solid #999;
		background-color: #eee;
		vertical-align: bottom;
	}
	
	table#list td {
		border-bottom: 1px solid #ccc;
	}
	
	table#list td.alt {
		background-color: #ffc; background-color: rgba(255, 255, 0, 0.2);
	}
</style>
<body>
	<table id="list">
		<tbody>
		<?php
		
		$i = 0;
		foreach($results as $data):
			echo "
			<tr>
				<td class='ccid tag$i'>$ci_source {$data['code']}|{$ci_source} {$data['name']}</td>
			</tr>";
			
			$i = $i ? 0 : 1;
		endforeach;
		
		?>
		</tbody>
	</table>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
	var main = function() {
		$('.ccid').click(function()
		{
			$.post('../fill_pension_type_input', {data:$(this).html()}, function(r)
			{
				eval(r);
			})
		});
	};
	
	$(document).ready(main);
</script>