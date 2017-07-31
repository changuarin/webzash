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
	
	.list-table {
		border-collapse: collapse;
		width: 600px;
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
	
	.text-center {
		text-align: center;
	}
	
	.text-right {
		text-align: right;
	}
	
	.text-warning {
		color: #ff0000;
	}
	
	.active {
		background-color: #ffff00;
		color: #ff0000;
	}
</style>
<body>
	<table class="list-table" style="width: 100%;">
		<tbody>
			<tr>
				<th></th>
				<th>PN</th>
				<th>Balance</th>
				<th>M.A.</th>
				<th>Terms</th>
				<th class="text-left">Duration</th>
				<th>Loan Type</th>
			</tr>
				<?php if( ! empty($loans)) : ?>
				<?php $i = 0; ?>
				<?php foreach($loans as $loan) : ?>
				<?php $duration = date('M d, Y', strtotime($loan->LH_StartDate)) . ' - ' . date('M d, Y', strtotime($loan->LH_EndDate)); ?>
				<tr class="ccid tag<?php echo $i;?>">
					<td class="text-center"><?php echo $loan->LH_IsTop; ?></td>
					<td class="text-center"><?php echo $loan->LH_PN; ?></td>
					<td class="text-right"><?php echo number_format($loan->LH_Balance, 2); ?></td>
					<td class="text-right"><?php echo number_format($loan->LH_MonthlyAmort, 2); ?>
					<td class="text-right"><?php echo $loan->LH_Terms; ?></td>
					<td class="text-center"><?php echo $duration; ?></td>
					<td class="text-center"><?php echo $loan->LH_LoanTrans; ?></td>
					</td>
				</tr>
				<?php endforeach; ?>
				<?php $i = $i ? 0 : 1; ?>
				<?php endif; ?>
		</tbody>
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
			var data = $(this).html().split(' ');
			
			$.post(
				'show_comaker',
				{cmrefno: data[0]},
				function(res) {console.log(res)});
			
			$('.ccid').removeClass('active');
			$(this).addClass('active');
		});
	});
</script>