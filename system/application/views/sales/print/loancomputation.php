<!DOCTYPE html>
<html>
	<head>
		<title>Loan Computation</title>
		<style type="text/css">
		
		body {font-size:16px;font-family: 'Courier New';border:none;margin:0;}
		table {width:90%;border:1px solid black;margin:0 auto;}
		table td:first-child {width:200px;padding-left:2px;}
		table td:last-child {padding-right:2px;}
		table tr th, table tr td {white-space:nowrap;height:16px;}
		table td small {font-size: 13px;font-weight: bold;white-space:nowrap;}
		
		.bb {border-bottom:1px solid black;}
		.center {text-align:center;}
		.right {text-align:right;}
		.line {border-bottom:1px solid black;}
		.netproceeds {font-weight:bold;border-bottom: 2px solid black;}
		
		#menu {width:100%;background-color:#0099CC;padding:6px 0;}
		
		</style>
		<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				
				var a = window.opener.$('#rplc-btn').attr('id');
				var b = window.opener.$('#reprintLCButton').attr('id');
				if(a=='rplc-btn'||b=='reprintLCButton')
				{
					$('#submitButton').attr('disabled', true).css('display', 'none');
				}
				
				$('#printButton').click(function(){
					$('#menu').css('display', 'none');
					window.print();
					$('#menu').css('display', '');
				});
				
			});
		</script>
	</head>
	<body>
		<div id="menu">
			<input id="printButton" type="button" value="Print">
		</div>
		
		<div style="padding-top: 2px;"></div>
		
		<table cellpadding="0" cellspacing="0">
			<tbody>
				<tr>
					<td class="center" colspan="2"><?=(!empty($lhloandate)?date('F d, Y', strtotime($lhloandate)):'')?></td>
				</tr>
				<tr>
					<td>NAME</td>
					<td><?=(!empty($ciname)?$ciname:'')?></td>
				</tr>
				<tr>
					<td>ADDRESS</td>
					<td><?=(!empty($lhaddress)?$lhaddress:'')?></td>
				</tr>
				<tr>
					<td style="border-bottom:1px solid #000;">MONTHLY PENSION</td>
					<td class="right" style="border-bottom:1px solid #000;"><?=(!empty($cpamount)?number_format($cpamount, 2):'0.00')?></td>
				</tr>
				<tr>
					<td>REMARKS</td>
					<td class="center"><small><?=(!empty($lhreference)?$lhreference:'')?></small> <?=(!empty($lhloantrans)?$lhloantrans:'')?></td>
				</tr>
				<tr>
					<td>DURATION</td>
					<td class="center"><?=(!empty($lhstartdate)&&!empty($lhenddate)?$lhstartdate.' to '.$lhenddate:'')?></td>
				</tr>
				<tr class="right">
					<td>MONTHLY PAYMENT</td>
					<td><?=(!empty($lhmonthlyamort)?number_format($lhmonthlyamort, 2):'')?></td>
				</tr>
				<tr class="right">
					<td>TERMS</td>
					<td class="line"><?=(!empty($lhterms)?$lhterms.' MONTHS':'')?></td>
				</tr>
				<tr class="right">
					<td>PN AMOUNT</td>
					<td><?=(!empty($lhprincipal)?number_format($lhprincipal, 2):'0.00')?></td>
				</tr>
				<tr class="right">
					<td>INTEREST (%)</td>
					<td class="line"><?=(!empty($interest)?number_format($interest, 3):'0.00')?></td>
				</tr>
				<tr class="right">
					<td>TOTAL</td>
					<td><?=(!empty($total1)?number_format($total1, 2):'0.00')?></td>
				</tr>
				<tr class="right">
					<td>PROCESSING FEE</td>
					<td class="line"><?=(!empty($lhprocfee)?number_format($lhprocfee, 2):'0.00')?></td>
				</tr>
				<tr class="right">
					<td>TOTAL</td>
					<td><?=(!empty($total2)?number_format($total2, 2):'0.00')?></td>
				</tr>
				<tr class="right">
					<td>OTHER CHARGES</td>
					<td class="line"><?=(!empty($lhcollfee)?number_format($lhcollfee, 2):'0.00')?></td>
				</tr>
				<tr class="right">
					<td>TOTAL</td>
					<td><?=(!empty($total3)?number_format($total3, 2):'0.00')?></td>
				</tr>
				<tr class="right">
					<td>OUTSTANDING BALANCE</td>
					<td class="line"><?=(!empty($lhobc)?number_format($lhobc, 2):'0.00')?></td>
				</tr>
				<tr class="right">
					<td>TOTAL</td>
					<td><?=(!empty($total4)?number_format($total4, 2):'')?></td>
				</tr>
				<tr class="right">
					<td><small style="font-weight: normal;">ADVANCE/PAYMENT TO</small></td>
					<td class="line">
						<small style="margin-right:200px;">
							<i>
								<?=(!empty($lhpaytorefpn)&&!empty($paytoname)?$lhpaytorefpn.':'.$paytoname:'')?>
							</i>
						</small>
						<?=(!empty($advpaymentto)?number_format($advpaymentto, 2):'0.00')?>
					</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr class="right">
					<td>NET PROCEEDS</td>
					<td class="netproceeds"><?=(!empty($netproceeds)?number_format($netproceeds, 2):'')?></td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr class="line">
					<td class="center" colspan="2"><?=(!empty($ciname)?$ciname:'')?></td>
				</tr>
				<tr>
					<td class="center" colspan="2">SIGNATURE OVER PRINTED NAME</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
			</tbody>
		</table><!-- end table -->
	</body>
</html>