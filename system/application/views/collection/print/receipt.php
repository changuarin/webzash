<!DOCTYPE HTML>
<html>
	<head>
		<title>Receipt Preview</title>
		<style type="text/css">
		body {
			font-family: 'Arial';
			font-weight: 400;
			margin-top: 50px;
		}
		
		label {
			color: #ff0000;
		}
		
		.container {
			margin: 0 auto;
			text-align: center;
			width: 940px;
		}
		
		.left-tblcol {
			text-align: right;
			width: 40%;
		}
		
		.right-tblcol {
			text-align: left;
			text-transform: uppercase;
			width: 55%;
		}
		
		#acctname {
			font-size: 16px;
			text-transform: uppercase;
		}
		
		#acctaddress {
			font-size: 14px;
			margin-bottom: 30px;
		}
		
		#receiptimg {
			margin-bottom: 30px;
		}
		
		#colldetbl {
			border-collapse: collapse;
			margin: 0 auto;
			width: 640px;
		}
		
		#colldetbl td {
			font-size: 14px;
			border: 0 solid #000; /* For alignment checking */
			padding: 5px 20px;
		}
		</style>
	</head>
	<body>
		<div class="container">
			<div id="acctname">
				<span class="value"><?php echo $branch_data->name; ?></span>
			</div>
			
			<div id="acctaddress">
				<span class="value"><?php echo $branch_data->address; ?></span>
			</div>
			
			<div id="receiptimg">
				<img src="<?= $coll_data->receipt_image !== NULL ? 'data:image/jpeg; base64, ' . $coll_data->receipt_image : base_url() . 'system/application/assets/images/no_photo.jpg' ?>" width="280" height="240" />
			</div>
		</div>
		
		<div class="container">
			<table id="colldetbl">
				<tbody>
					<tr>
						<td class="left-tblcol">Name:</td>
						<td class="right-tblcol"><?php echo $client_data->name . ' <label>(' . $pension_data->CP_PensionType . ')</label>'; ?></td>
					</tr>
					<tr>
						<td class="left-tblcol">Trace No.:</td>
						<td class="right-tblcol"><?php echo $traceno; ?></td>
					</tr>
					<tr>
						<td class="left-tblcol">ATM Beg Bal:</td>
						<td class="right-tblcol"><?php echo number_format($atmbegbal, 2); ?></td>
					</tr>
					<tr>
						<td class="left-tblcol">Amt Drawn:</td>
						<td class="right-tblcol"><?php echo number_format($amtdrawn, 2); ?></td>
					</tr>
					<tr>
						<td class="left-tblcol">ATM End Bal.:</td>
						<td class="right-tblcol"><?php echo number_format($atmendbal, 2); ?></td>
					</tr>
					<tr>
						<td class="left-tblcol">Directly Paid.:</td>
						<td class="right-tblcol"><?php echo number_format($directpaid, 2); ?></td>
					</tr>
					<tr>
						<td class="left-tblcol">Due Date:</td>
						<td class="right-tblcol"><?php echo date('m/d/Y', strtotime($coll_data->duedate)); ?></td>
					</tr>
					<tr>
						<td class="left-tblcol">Printed By:</td>
						<td class="right-tblcol"><?php echo $user; ?></td>
					</tr>
					<tr id="func-tblrow">
						<td colspan="2">
							<input id="printbtn" type="button" value="Print" />
							<input id="closebtn" type="button" value="Close" />
						</td>
					</tr>
				</tbody>
			</table>
			
			
		</div>
		
		<script src="<?=base_url()?>system/application/assets/js/jquery.min.js" type="text/javascript"></script>
		<script type="text/javascript">
		
		var main = function() {
			// Self explantory functions
			$('#printbtn').click(function() {
				$('#func-tblrow').hide();
				window.print();
				$('#func-tblrow').show();
			});
			
			$('#closebtn').click(function() {
				window.close();
			});
		}
		
		$(document).ready(main);
		</script>
	</body>
</html>