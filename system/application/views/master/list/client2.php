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
		width: 400px;
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
	
	.text-warning {
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
						<label for="citype">Client Type:</label>
						<select id="ciType" name="citype">
							<option value="PEN" <?php echo ($ci_type == 'PEN' ? 'selected' : ''); ?>>CLIENT-GSIS/SSS</option>
							<option value="EMP" <?php echo ($ci_type == 'EMP' ? 'selected' : ''); ?>>ACCOM-EMPLOYEE</option>
							<option value="AGT" <?php echo ($ci_type == 'AGT' ? 'selected' : ''); ?>>ACCOM-AGENT</option>
							<option value="SAL" <?php echo ($ci_type == 'SAL' ? 'selected' : ''); ?>>CLIENT-SALARY</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<label for="cistatus">Client Status:</label>
						<select id="ciStatus" name="cistatus">
							<option value="A" <?php echo ($ci_status == 'A' ? 'selected' : ''); ?>>ACTIVE</option>
							<option value="I" <?php echo ($ci_status == 'I' ? 'selected' : ''); ?>>INACTIVE</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<input id="name" type="text" name="name" placeholder="Lastname, Firstname" value="<?php echo (isset($name) ? $name : ''); ?>" />
						<input type="submit" value="Search" />
					</td>
				</tr>
			</tbody>
		</table>
	</form>
	<br>
	<table class="clients-table">
		<?php if( ! empty($clients)) : ?>
		<tbody>
			<tr>
				<td"><input id="sortBtn" type="button" value="Sort"></td>
			</tr>
		<?php $i = 0; ?>
		<?php foreach($clients as $client) : ?>
			<tr>
				<td class="ccid tag<?php echo $i . ' ' . $client['database']; ?>""><?php echo $client['CI_AcctNo'] . ' ' . $client['name']; ?> (<span class="text-warning"><?php echo $client['CP_PensionType']; ?></span>)</td>
			</tr>
		<?php endforeach; ?>
		<?php $i = $i ? 0 : 1; ?>
		</tbody>
		<?php endif; ?>
	</table>
</body>

<script src="<?= base_url() ?>system/application/assets/js/jquery-2.1.3.min.js" type="text/javascript"></script>
<script type="text/javascript">
	function sortTable() {
		var rows = $('.clients-table tbody tr').get();
		
		rows.sort(function(a, b) {
			var data = $(a).children('td').eq(0).text().split(' ');
			var a = data[1];
			
			var data = $(b).children('td').eq(0).text().split(' ');
			var b = data[1];
			
			if(a < b)
			{
				return -1;
			}
			
			if(a > b)
			{
				return 1;
			}
			
			return 0;
		});
		
		$.each(rows, function(index, row) {
			$('.clients-table').children('tbody').append(row);
		});
	}
	
	$(document).ready(function() {
		$('#sortBtn').click(function() {
			sortTable();
		});
			
		$('#name').blur(function() {
			var strUpper = $(this).val().toUpperCase();
			
			$(this).val(strUpper);
		});
		
		$('#ciType, #ciStatus').change(function() {
			$('#searchForm').submit();
		});
		
		$('.ccid').click(function() {
			var module = $('#clientList', parent.document).prop('class');
			var data = $(this).html().split(' ');
			var data2 = $(this).attr('class').split(' ');
			
			$.post(
				'show_client2',
				{ciacctno: data[0], database: data2[2]},
				function(res) {});
			
			$('.ccid').removeClass('active');
			$(this).addClass('active');
		});
	});
</script>