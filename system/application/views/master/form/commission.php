<style type="text/css">
	body,
	table,
	textarea {
		font-family: 'Arial', sans-serif;
		font-size: 12px;
	}
	
	img,
	input,
	select,
	textarea {
		border: 1px solid #0099cc;
	}
	
	input[type=text]:enabled,
	select:enabled,
	textarea:enabled {
		background-color: #ffff99;
		border: 1px solid #ff0000;
	}
	
	input[type=text]:disabled,
	select:disabled,
	textarea:disabled {
		background-color: #fff !important;
		border: 1px solid #0099cc;
	}
	
	.hidden {
		display: none;
	}
	
	.text-center {
		text-align: center;
	}
	
	.text-right {
		text-align: right;
	}
	
	.vertical-top {
		vertical-align: top;
	}
	
	.text-success {
		color: green;
	}
	
	.text-warning {
		color: #ff0000;
	}
	
	.menu {
		border: 0;
		position: fixed;
		top: 15px;
		right: 15px;
	}
	
	.enabled {
		color: #ff0000;
		cursor: pointer;
	}
	
	.tab-heading {
		background-color: #09c;
		color: #fff;
		font-size: 12px;
		margin-top: 10px;
		margin-bottom: 10px;
		padding: 10px 5px;
	}
	
	.error-list {
		list-style: none;
		padding-left: 5px;
	}
	
	.error-list li {
		padding: 5px 10px;
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
	
	#commnTable {
		border: 1px solid #000;
		width: 800px;
	}
	
	#commnTable th, #commnTable tr, #commnTable td {
		border: 1px solid #000;
	}
</style>
<body>
	<div class="main">
		<h3 class="tab-heading">Commission</h3>
		<form id="commnForm" method="post">
			<table>
				<tbody>
					<tr>
						<td>
							<label for="name">Name</label>
							<input class="text-center" id="name" type="text" name="name" disabled>
							<input class="hidden" id="aiRefno" type="text" name="airefno" value="">
							<label for="startdate">From</label>
							<input class="text-center" id="startDate" type="text" name="startdate" value="<?php echo date('Y-m-d')?>">
							<label for="enddate">To</label>
							<input class="text-center" id="endDate" type="text" name="enddate" value="<?php echo date('Y-m-d')?>">
							<select id="agentType" name="agenttype">
								<option value="0" selected="selected">ALL</option>
								<option value="1">AGENT</option>
								<option value="2">SUB-AGENT</option>
							</select>
							<input id="loadBtn" type="button" value="Load">
						</td>
					</tr>
				</tbody>
			</table>
			<br>
			<div>
				<label for="processdate">Process Date </label>
				<input class="text-center" type="text" name="processdate" value="<?php echo date('Y-m-d') ?>">
				<input id="processBtn" type="button" value="Process">
			</div>
			<br>
			<table class="list-table" id="commnTable">
				<thead>
					<tr>
						<th>No.</th>
						<th>Loan ID</th>
						<th>Loan Date</th>
						<th>Client Name</th>
						<th>Mo Pmnt</th>
						<th>Terms</th>
						<th>Loan Type</th>
						<th>CFP</th>
						<th>CB</th>
						<th>Process Date</th>
						<th>
							<input id="mainCkbox" type="checkbox" name="mainckbox">
						</th>
					</tr>
				</thead>
				<tbody id="commnList">
				</tbody>
			</table>
		</form>
	</div>
</body>

<script src="<?php echo base_url(); ?>system/application/assets/js/jquery-2.1.3.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>system/application/assets/js/numeral.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('#loadBtn').click(function() {
			var aiRefno = $('#aiRefno').val();
			var agentType = $('#agentType').val();
			var startDate = $('#startDate').val();
			var endDate = $('#endDate').val();
			
			$('#commnList').html('');
			
			$.post('list_commissions',
					{
						airefno: aiRefno,
						agenttype: agentType,
						startdate: startDate,
						enddate: endDate
					},
					function(results) {
						var cfpTotal = 0;
						var cbTotal = 0;
						var i = 1;
						
						$.each(results, function(index, result) {
							var aiRate = result.ai_rate / 100;
							var cb = result.lh_monthlyamort * result.lh_terms * aiRate;
							var cfp = result.lh_monthlyamort * result.lh_terms * aiRate;
							
							input = '';
							
							if(result.added_date == '' || result.added_date == null)
							{
								input += `<input type="checkbox" name="commn[]" value="`+ result.agent_type + '|' + result.ci_acctno + '|' +result.lh_pn + '|' + result.lh_loandate + '|' + result.ci_name + '|' + result.lh_monthlyamort + '|' + result.lh_terms + '|' + result.lh_loantrans + '|' + cfp + '|' + cb + `">`;
							}
							
							$('#commnList').append(`
								<tr>
									<td class="text-right">` + i + `)</td>
									<td>` + result.lh_pn + `</td>
									<td>` + result.lh_loandate + `</td>
									<td>` + result.ci_name + `</td>
									<td class="text-right">` + result.lh_monthlyamort + `</td>
									<td class="text-right">` + result.lh_terms + `</td>
									<td class="text-center">` + result.lh_loantrans + `</td>
									<td class="text-right">` + numeral(cfp).format('0,0.00') + `</td>
									<td class="text-right">` + numeral(cb).format('0,0.00') + `</td>
									<td class="text-center">` + result.added_date + `</td>
									<td class="text-center">` + input + `</td>
								</tr>
							`);
							
							cbTotal += cb;
							cfpTotal += cfp;
							i++;
						});
				
				$('#commnList').append(`
					<tr>
						<td class="text-right" colspan="7"><b>Total</b></td>
						<td class="text-right"><b>` + numeral(cbTotal).format('0,0.00') + `</b></td>
						<td class="text-right"><b>` + numeral(cfpTotal).format('0,0.00') + `</b></td>
						<td colspan="2"></td>
					</tr>
				`);
			});
			
		});
		
		$('#processBtn').click(function() {
			var processCmmn = confirm('Process commission?');
			
			if(processCmmn == true) {
				$('#commnForm').submit();
			} else {
				return false;
			}
		})
		
		$('#mainCkbox').click(function() {
			var isChecked = $(this).prop('checked');
			
			$('input:checkbox').prop('checked', isChecked);
		});
	});
</script>