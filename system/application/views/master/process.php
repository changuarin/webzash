<style type="text/css">
	body {
		font-family: 'Arial', sans-serif;
		background-color: #00008b;
		margin: 0;
	}
	
	input, select {
		border: 1px solid #ffff00;
		color: #00008b;
		font-size: 24px;
		background-color: #ffff00;
		height: 5%;
		padding-right: 5px;
		text-align: right;
		width: 100%; 
	}
	
	input:hover, select:hover {
		border: 2px solid #00008b;
	}
	
	select {
		text-transform: uppercase;
	}
	
	.clear {
		clear: both;
	}
	
	.column {
		float: left;
		width: 33%;
	}
	
	.column label {
		color: #ffff00;
		font-size: 24px;
		text-transform: uppercase;
	}
	
	.container {
		margin: 2% 0;
		padding: 0 20px;
	}
	
	#header p {
		color: #ffff00;
		font-size: 18px;
		margin: 0;
		text-transform: uppercase;
	}
	
	#net_proceeds {
		border-bottom: 2px solid #ff0000;
		color: #ff0000;
	}
</style>
<?php 

if(!empty($results)):
	$rates = explode(';', $rates->value);
	
	$interest = 1 - ($results->LH_Terms * $rates[0]);
	
	$net_proceeds = (($results->LH_LoanAmt * $results->LH_Terms) * $interest) - $rates[2] - $results->LH_Balance;
?>
<div id="header">
	<div class="container">
		<div>
			<p>No. of Payments: (No.)</p>
		</div>
		<div>
			<p>Last Payment: (Month and Amount)</p>
		</div>
		<div>
			<p>Last Refund: (Month and Amount)</p>
		</div>
		<div>
			<p>For Extension: (Month</p>
		</div>
		<div>
			<p>For Renewal: (Month)</p>
		</div>
		<div>
			<p>O.B.: <?= $results->LH_Balance ?></p>
		</div>
		<div>
			<p>For Refund: <?= $results->LH_Refund ?></p>
		</div>
	</div>
</div>
<div id="main">
	<div class="container">
		<div>
			<div class="column">
				<label>Date</label>
			</div>
			<div class="column">
				<input id="loan_date" type="text" name="loan_date" value="<?= date('F d, Y') ?>" />
			</div>
			<div class="clear">
			</div>
		</div>
		<div>
			<div class="column">
				<label>Name</label>
			</div>
			<div class="column">
				<input id="client_name" type="text" name="client_name" value="<?= $results->CI_Name ?>" />
			</div>
			<div class="clear">
			</div>
		</div>
		<div>
			<div class="column">
				<label>Remarks</label>
			</div>
			<div class="column">
				<select id="loan_trans" name="loan_trans"/>
					<option value="">Additional</option>
					<option value="">Advance Bonus</option>
					<option value="">Extension</option>
					<option value="">Renewal</option>
					<option value="">Restructure</option>
				</select>
			</div>
			<div class="column">
				<input id="net_proceeds" type="text" name="net_proceeds" value="<?= $net_proceeds ?>" />
			</div>
		</div>
		<div>
			<div class="column">
				<label>Payment</label>
			</div>
			<div class="column">
				<input id="payment" type="text" name="payment" value="<?= $results->LH_LoanAmt ?>" />
			</div>
			<div class="clear">
			</div>
		</div>
		<div>
			<div class="column">
				<label>Terms</label>
			</div>
			<div class="column">
				<input id="terms" type="text" name="terms" value="<?= $results->LH_Terms ?>" />
			</div>
			<div class="clear">
			</div>
		</div>
		<div>
			<div class="column">
				<label>Interest</label>
			</div>
			<div class="column">
				<input id="interest" type="text" name="interest" value="<?= $interest ?>" />
			</div>
			<div class="clear">
			</div>
		</div>
		<div>
			<div class="column">
				<label>Processing Fee</label>
			</div>
			<div class="column">
				<input id="processing_fee" type="text" name="processing_fee" value="<?= $rates[2] ?>" />
			</div>
			<div class="clear">
			</div>
		</div>
		<div>
			<div class="column">
				<label>O.B.</label>
			</div>
			<div class="column">
				<input id="ob" type="text" name="ob" value="<?= $results->LH_Balance ?>" />
			</div>
			<div class="clear">
			</div>
		</div>
		<div>
			<div class="column">
				<label>Duration</label>
			</div>
			<div class="column">
				<input id="duration" type="text" name="duration" value="" />
			</div>
			<div class="clear">
			</div>
		</div>
	</div>
</div>
<?php endif; ?>
<script type="text/javascript">
	
</script>