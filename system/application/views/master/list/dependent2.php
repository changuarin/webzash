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
	<table class="list-table">
		<tbody>
		<?php if( ! empty($dependents)) : ?>
			<?php $i = 0; ?>
			<?php foreach($dependents as $dependent) : ?>
				<tr>
					<td class="ccid tag<?php echo $i; ?>""><?php echo $dependent->SysID . ' ' . $dependent->name; ?></td>
				</tr>
			<?php endforeach; ?>
			<?php $i = $i ? 0 : 1; ?>
		<?php endif; ?>
		</tbody>
		</tbody>
	</table>
</body>
</body>

<script src="<?= base_url() ?>system/application/assets/js/jquery-2.1.3.min.js" type="text/javascript"></script>
<script type="text/javascript">
	$(document).ready(function() {
		console.log(123);
	});
</script>