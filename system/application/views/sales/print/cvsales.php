<!DOCTYPE html>
<html>
	<head>
		<title>Check Voucher</title>
		<style type="text/css">
		
		body {font-family:'Century Gothic';margin:0;border:none;}
		b, th {text-align:left;font-weight:bold;}
		small {font-size:10px;}
		
		#cvTable tr th, #cvTable tr td {border:0 solid black;font-size:13px;font-weight:bold;}
		#checkTable tr th, #checkTable tr td {border:0 solid black;font-size:13px;}
		#menuTab {background-color: #0099CC;width:100%;}
		
		.hide {display:none;}
		.center {text-align:center;}
		.left {text-align:left;}
		.right {text-align:right;}
		.nw {white-space:nowrap;}
		
		</style>
		<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script>
		<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/format.js"></script>
		<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/functions.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
			
				$('#selectPrint').change(function(){
					$('.printDoc').css('display', 'none');
					var a = $(this).val();
					$('.printDoc').css('display', 'none');
					$('#'+a).css('display', '');
				});
				$('#rbbutton').click(function(){
					var a = $('#recievedby').val();
					var b = a.toUpperCase();
					$('.recievedby').html(b);
				});
				$('#adbutton').click(function(){
					var a = $('.refunddetail').html();
					if(a!='')
					{
						var b = a + $('#detail').val().toUpperCase() + '<br>';
					} else {
						var b = $('#detail').val().toUpperCase() + '<br>';
					}
					$('.refunddetail').html(b);
				});
				$('#clearbutton').click(function(){
					$('.refunddetail').html('');
					$('#detail').val('');
				});
				$('#printButton').click(function(){
					$('#menuTab').css('display', 'none');
					window.print();
					$('#menuTab').css('display', '');
				});
				$('#processButton').click(function(){
					var a = confirm("Process check voucher?");
					if (a == true) {
						window.opener.$('#submit-btn').click();
						window.close();
					} else {
						return false;
					}
				})
				
				var amt = $('#amt').html();
				var amt_word = toWords(N(amt));
				$("#amtWords").html(amt_word);
				$("#checkAmt").html('***' + amt_word + '***');
				
			});
		</script>
	</head>
	<body>
	
		<?php
		
		$i = 0;
		foreach($datas as $d)
		{
		$a[$i] = $d['value'];
		$i++;
		}
		if(isset($a[2]))$c = explode(';', $a[2]);
		else $c='';
		$d = explode(';', $a[1]);
		$e = explode(';', $a[0]);
		
		echo '
		<div id="menuTab">
			<select id="selectPrint">
				<option value="CV">CHECK VOUCHER</option>
				<option value="CHECK">CHECK</option>
			</select>
			<input class="center" id="recievedby" type="text" name="recievedby">
			<input id="rbbutton" type="button" name="rbbutton" value="RecievedBy">
			'.
			(strpos($re,'EXCESS')!==FALSE||strpos($re,'REFUND')!==FALSE?
				'<input class="center" id="detail" type="text" name="detail">
				<input id="adbutton" type="button" name="adbutton" value="Add Detail">
				<input id="clearbutton" type="button" name="clearbutton" value="Clear Detail">'
				:
				'')
			.'
			<input type="button" value="Print" id="printButton">
			<input type="button" value="Process" id="processButton">
		</div>
		<div class="printDoc" id="CV">
			<table id="cvTable" style="width:100%;">
				<tbody>
					<tr style="height:'.$x.';">
						<td>&nbsp;</td>
					</tr>
					<tr style="vertical-align:bottom;">
						<td>&nbsp;</td>
						<td style="height:22px;padding-left:240px;">'.$payee.'</td>
						<td class="right" style="height:0;padding-right:160px;">'.date('m/d/Y', strtotime($date)).'</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td colspan="2" style="height:0;padding-left:280px;">'.$address.'</td>
					</tr>
				</tbody>
			</table>
			<table id="cvTable" cellpadding="0" cellspacing="0" style="width:100%;">
				<tbody>
					<tr style="height:50px;">
						<td colspan="5">&nbsp;</td>
					</tr>
					<tr style="vertical-align:top;">
						<td class="left" style="width:23%;height:'.$y.';padding-right:10px;padding-left:60px;">';
							if(strpos($re,'NET PROCEEDS')!==FALSE)
							{
								echo '
									Notes Recievable
									<br>
									<label style="padding-left:30px;">Processing Fee</label>
									<br>
									<label style="padding-left:30px;">Other Charges</label>
									<br>
									<label style="padding-left:30px;">Interest Income</label>
									<br>
									<label style="padding-left:30px;">Notes Receivable</label>
									<br>
									<label style="padding-left:30px;">Cash in Bank</label>
									<br>
								';
							} elseif(strpos($re,'EXCESS')!==FALSE||strpos($re,'REFUND')!==FALSE) {
								echo '
									Notes Recievable
									<br>
									<label style="padding-left:50px;">Cash in Bank</label>
								';
							}
						echo
							'<br><br>'.
							$companyname.' '.$branchcode.' '.$bank.'<br>'.
							'<label style="padding-left:50px;">'.'CK#'.$ckno.'</label>
							<br>
						</td>
						<td  class="right" style="width:10%;height:'.$y.';padding-right:0;">'.
							($re=='NET PROCEEDS OF LOAN'?number_format($principal,2):(strpos($re,'EXCESS')!==FALSE||strpos($re,'REFUND')!==FALSE?number_format($amt,2):'')).'
							<br>
						</td>
						<td  class="right" style="width:10%;height:'.$y.';padding-right:0;">';
						if(strpos($re,'NET PROCEEDS')!==FALSE)
						{
							echo '
							<br>'.
							number_format($procfee,2).'
							<br>'.
							number_format($collfee,2).'
							<br>'.
							number_format($interest,2).'
							<br>'.
							number_format($nr,2).'
							<br>'.
							number_format($netproceeds,2).'
							<br>
							';
						} elseif(strpos($re,'EXCESS')!==FALSE||strpos($re,'REFUND')!==FALSE) {
							echo '
							<br>'.
							number_format($amt,2).'
							';
						}
						echo '
						</td>
						<td  style="width:40%;height:'.$y.';padding-left:20px;">'.
							(strpos($loantrans,'ADVANCE BONUS')!==FALSE?'13TH MONTH':strtoupper($re)).'
							<br>
							<span id="amt" class="hide">'.$amt.'</span>';
							if(strpos($re,'EXCESS')!==FALSE||strpos($re,'REFUND')!==FALSE) {
								echo '
								<label class="refunddetail"></label>
								';
							}
							echo '
							<span id="amtWords"></span>
							<br>
							<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.
							(strpos($re,'NET PROCEEDS')!==FALSE?str_replace(';','',$loantrans):'').'
							<br>';
							if(strpos($re,'NET PROCEEDS')!==FALSE)
							{
								if(strpos($loantrans,'ADVANCE BONUS')!==FALSE)
								{
									$terms = 1 - ($interest/$principal);
									echo '<label>'.number_format($loanamt,2).' x '.$terms.'</label>';
								} else {
									echo '<label>'.number_format($loanamt,2).' x '.$terms.'</label> Months';
								}
							}
						echo '
						</td>
						<td  style="width:;height:100px;padding-left:25px;">'.
							number_format($amt, 2).'
						</td>
					</tr>
					<tr style="vertical-align:right;">
						<td class="right">
							&nbsp;
						</td>
						<td class="right" style="height:0;padding-right:0;">'.
							($re=='NET PROCEEDS OF LOAN'?number_format($principal,2):(strpos($re,'EXCESS')!==FALSE||strpos($re,'REFUND')!==FALSE?number_format($amt,2):'')).'
						</td>
						<td class="right" style="height:0;padding-right:0;">'.
							($re=='NET PROCEEDS OF LOAN'?number_format($principal,2):(strpos($re,'EXCESS')!==FALSE||strpos($re,'REFUND')!==FALSE?number_format($amt,2):'')).'
						</td>
						<td class="right">
							&nbsp;
						</td>
						<td style="height:0;padding-left:25px;">'.
							number_format($amt, 2).'
						</td>
					</tr>
					<tr style="height:28px;">
						<td colspan="5">&nbsp;</td>
					</tr>
				</tbody>
			</table>
			<table id="cvTable" cellpadding="0" cellspacing="0" style="width:100%;">
				<tbody>
					<tr>
						<td style="width:45%;">
							<table id="cvTable" cellpadding="0" cellspacing="0" style="width:100%;">
								<tbody>
									<tr style="vertical-align:bottom;">
										<td rowspan="2" style="width:33%;padding-top:0;padding-left:140px;">'.
											strtoupper(substr($preparedby,0,3)).'
										</td>
										<td rowspan="2" style="width:33%;padding-top:0;padding-left:160px;">'.
											(substr($oic,0,3)=='MCB'?'ACR':substr($oic,0,3)).'
										</td>
										<td rowspan="2" style="width:33%;padding-top:0;padding-left:180px;">'.
											$e[0].'
										</td>
									</tr>
								</tbody>
							</table>
						</td>
						<td style="width:55%;">
							<table id="cvTable" cellpadding="0" cellspacing="0" style="width:100%;">
								<tbody>
									<tr style="vertical-align:bottom;">
										<td class="center" colspan="3" style="width:55%;height:35px;padding-left:0;"><label class="recievedby">'.
										$payee.'
										</label></td>
									</tr>
									<tr style="height:30px;vertical-align:bottom;">
										<td class="left nw" style="width:33%;padding-left:260px;">
											<small>&nbsp;</small>
										</td>
										<td class="left nw" style="width:33%;padding-left:120px;">
											<small>&nbsp;</small>
										</td>
										<td class="left nw" style="width:33%;padding-left:50px;">
											<small>&nbsp;</small>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</div><!-- end Check Voucher -->
		
		<div class="printDoc" id="CHECK"" style="display:none;">
			<table id="checkTable" cellpadding="0" cellspacing="0" style="width:100%;">
				<tbody>
					<tr>
						<th>
							&nbsp;
						</th>
						<th class="right" style="padding-top: ' . $z[0] . ';padding-right:135px;">' .
							date('m/d/Y', strtotime($date)) . '
						</th><!-- CV No. -->
					</tr>
					<tr style="vertical-align:bottom;">
						<th class="center nw" style="width: 70%;padding-top: 13px;padding-left: 160px;">
							***<label class="recievedby" style="letter-spacing: 3px;">' . $payee . '</label>***
						</th>
						<th class="nw right" style="padding-top: 2px;padding-right: 120px;">
							***' . number_format($amt, 2) . '***
						</th>
					</tr>
					<tr style="vertical-align:bottom;">
						<th class="center nw" id="checkAmt" colspan="2" style="padding-top: ' . $z[1] . ';padding-right: 120px;padding-left: 80px;">
						</th>
					</tr>
				</tbody>
			</table>
		</div><!-- end Check -->
		
		';
		
		?>
		
	</body>
</html>