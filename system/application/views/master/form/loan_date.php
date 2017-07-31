<style type="text/css">
	body,table{
		font-family: 'Arial';
		font-size: 12px;
		cursor: default;
		margin: 0;
		padding: 0;
	}
	
	div {
		padding-bottom: 10px;
	}
	
	input[type=button], input[type=submit] {
		border: 1px solid #0099cc;
	}
	
	input[type=text] {
		background-color: #ffff99;
		border: 1px solid #ff0000;
	}
	
	.center {
		text-align: center
	}
	
	.error {
		background-color: #fc331c !important;
	}
	
	label.error {
		display: none !important;
	}
	
	#main {
		margin: 40px auto;
		width: 240px;
	}
	
	#main div {
		text-align: center;
	}
</style>
<body>
	<div id="main">
		<div>
			<h2>Loan Date Form</h>
		</div>
		<form action="../../../update_loan_date" id="edit_loan_form" method="post">
			<div>
				<label>Loan Date</label>
				&nbsp;
				<input class="center" id="lh_loandate" type="text" name="lh_loandate" value="<?= $lh_loandate ?>" required />
			</div>
			<div style="padding-left: 60px;">
				<input id="ci_acctno" type="hidden" name="ci_acctno" value="<?= $ci_acctno ?>" />
				<input id="lh_pn" type="hidden" name="lh_pn" value="<?= $lh_pn ?>" />
				<input id="submit-btn" type="button" value="Submit" />
				<input id="close-btn" type="button" value="Close" />
			</div>
		</form>
	</div>
</body>
<script src="<?= base_url() ?>system/application/assets/js/jquery-2.1.3.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>system/application/assets/js/jquery.validate.min.js" type="text/javascript"></script>
<script type="text/javascript">
	var main = function() {
		$('#submit-btn').click(function() {
			if(confirm('Update loan date?')) {
				$('#edit_loan_form').submit();
			} else {
				return false;
			}
		});
		
		$('#close-btn').click(function() {
			window.close();
		});
		
		$('#lh_startdate').select();
		
		$('#edit_loan_form').validate();
	}
	
	$(document).ready(main);
</script>