<style type="text/css">
	.center {
		text-align: center;
	}
	
	#main div {
		padding-bottom: 10px;
	}
	
	#system_message {
		color: #ff0000;
	}
</style>
<body>
	<div id="main">
		<h3>Auto Debit Form</h3>
		<div id="system_message">
			<?= isset($system_message) ? $system_message : '' ?>
		</div>
		<form method="post" action="process_auto_debit_file" enctype="multipart/form-data">
			<div>
				<label>Month</label>
				&nbsp;
				<select name="month">
					<?php
						
					for($i = 0; $i <= 11; $i++):
						$date = strtotime('Y-m-01');
						$month_name = date('F', strtotime('+' . $i . ' month', $date));
						$month = date('m', strtotime('+' . $i . ' month', $date));
						echo '<option value="' . $month . '" ' . ($month == date('m') ? 'selected="selected"' : '') . '>' . $month_name . '</option>';
					endfor;
					
					?>
				</select>
				&nbsp;
				<label>Year</label>
				&nbsp;
				<input class="center" id="year" type="text" name="year" value="<?= date('Y') ?>" />
			</div>
			<div>
				<select name="banktopost">
					<option value="1000" selected="selected">PNB2 DIVISORIA(NHFC)</option>
					<option value="1001">PNB3 DIVISORIA(GTLIC)</option>
				</select>
			</div>
			<div>
				<input id="auto_debit_file" type="file" name="auto_debit_file" />
			</div>
			<div>
				<input type="submit" name="submit-btn" value="Process" />
				<input id="clear-btn" type="button" name="clear-btn" value="Clear" />
			</div>
		</form>
	</div>
</body>
<script src="<?= base_url() ?>system/application/assets/js/jquery-2.1.3.min.js" type="text/javascript"></script>
<script type="text/javascript">
	var main = function() {
		$('#clear-btn').click(function() {
			$('#auto_debit_file').val('');
		});
		
		$('input, select').click(function() {
			$('#system_message').hide();
		});
	}
	
	$(document).ready(main);
</script>