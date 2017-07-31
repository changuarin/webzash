<style>
	body,
	form,
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
		
	.clients-table {
		border-collapse: collapse;
	}
	
	.clients-table th {
		border-bottom: 2px solid #999;
		background-color: #eee;
		vertical-align: bottom;
	}
	
	.clients-table td {
		border-bottom: 1px solid #ccc;
	}
	
	.clients-table td.alt {
		background-color: #ffc;
		background-color: rgba(255, 255, 0, 0.2);
	}
	
	.list-table {
		border-collapse: collapse;
	}
	
	.list-table th {
		background-color: #eee;
		vertical-align: bottom;
	}
	
	.list-table td {
		border-bottom: 1px solid #ccc;
		padding: 2px;
	}
	
	.list-table td.alt {
		background-color: #ffc;
		background-color: rgba(255, 255, 0, 0.2);
	}
	
	.text-center {
		text-align: center;
	}
	
	.text-warning {
		color: #ff0000;
	}
	
	.active {
		background-color: #ffff00;
		color: #ff0000;
	}
	
	#clientTable {
		border: 1px solid #000;
		width: 600px;
	}
	
	#clientTable th,
	#clientTable tr,
	#clientTable td {
		border: 1px solid #000;
	}
</style>
<body>
	<table class="list-table" id="clientTable">
		<thead>
			<tr>
				<th colspan="2"><?php echo $ai_refno; ?></th>
				<th colspan="5">
					<?php $database = str_replace('nhgt_', '', $database); ?>
					<?php $branch_name = str_replace('.', '', $database); ?>
					<?php echo strtoupper($branch_name); ?>
				</th>
			</tr>
			<tr>
				<th></th>
				<th>Ref. No.</th>
				<th>Client Name</th>
				<th>Is Agent</th>
				<th>Agent Rate</th>
				<th>Is Sub-Agent</th>
				<th>Sub-Agent Rate</th>
			</tr>
		</thead>
		<tbody>
			<?php $i = 1; ?>
			<?php $j = 1; ?>
			<?php if( ! empty($clients)) : ?>
			<?php foreach($clients as $client) : ?>
			<tr class="tag<?php echo $j; ?>">
				<td><?php echo $i; ?>)</td>
				<td><?php echo $client->CI_AcctNo; ?></td>
				<td><?php echo $client->name; ?></td>
				<td class="text-center"><?php echo $client->CI_Agent1 != NULL ? '1' : '0'; ?></td>
				<td class="text-center"><?php echo $client->CI_Agent1_Rate; ?></td>
				<td class="text-center"><?php echo $client->CI_Agent2 != NULL ? '1' : '0'; ?></td>
				<td class="text-center"><?php echo $client->CI_Agent2_Rate; ?></td>
			</tr>
			<?php $i++; ?>
			<?php $j = $j ? 0 : 1; ?>
			<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</body>