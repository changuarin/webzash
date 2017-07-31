<!DOCTYPE html>
<html>
	<head>
		<title>O.B. Card</title>
		<style type="text/css">
		
		body {border:none;margin-top:20px;margin-left:20px;}
		table {font-family:'Courier New';font-size:13px;}
		table tr th, table tr td {vertical-align:middle;}
		
		.bt {border-top:1px solid #000;}
		.br {border-right:1px solid #000;}
		.bb {border-bottom:1px solid #000;}
		.bl {border-left:1px solid #000;}
		.center {text-align:center;}
		.hide {display:none;}
		.nw {white-space:nowrap;}
		
		#menuTab {background-color:#FFF;width:100%;}
		
		</style>
		<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script>
		<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/format.js"></script>
		<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/functions.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
			
				$('#printButton').click(function(){
					$('#menuTab').css('display', 'none');
					window.print();
					$('#menuTab').css('display', '');
				});
				$('#frontButton').click(function(){
					$('#frontPage').attr('class', '');
					$('#backPage').attr('class', 'hide');
					$('#frontButton').attr('class', 'hide');
					$('#backButton').attr('class', '');
				});
				$('#backButton').click(function(){
					$('#frontPage').attr('class', 'hide');
					$('#backPage').attr('class', '');
					$('#frontButton').attr('class', '');
					$('#backButton').attr('class', 'hide');
				});
				
			});
		</script>
	</head>
	<body>
	<?php
	
	foreach($datas as $d)
	{
		$age = intval(substr(date('Ymd') - date('Ymd', strtotime($d['CI_Bdate'])), 0, -4)); 
		echo '
		<div id="menuTab">
			<input type="button" value="Print" id="printButton">
			<input class="hide" type="button" value="Front" id="frontButton">
			<input type="button" value="Back" id="backButton">
		</div>
		
		<div id="frontPage">
			<table cellpadding="0" cellspacing="0" style="width:100%;">
			<tbody>
				<tr>
					<td class="center" colspan="4">'.strtoupper($companyname).'</td>
				</tr>
				<tr>
					<td colspan="4">&nbsp;</td>
				</tr>
				<tr>
					<td class="nw" rowspan="15" style="border:1px solid #000;width:28%;">&nbsp;</td>
					<td style="padding-left:9px;width:20%;">NAME</td>
					<td colspan="2">:'.$d['name'].'</td>
				</tr>
				<tr>
					<td style="padding-left:9px;">ADDRESS</td>
					<td colspan="2">:'.$d['CI_Add1'].'</td>
				</tr>
				<tr>
					<td style="padding-left:9px;">TEL NO.</td>
					<td colspan="2">:'.$d['CI_TelNo'].'</td>
				</tr>
				<tr>
					<td class="nw" style="padding-left:9px;">BIRTH DATE</td>
					<td colspan="2">:'.date('m/d/y', strtotime($d['CI_Bdate'])).'</td>
				</tr>
				<tr>
					<td style="padding-left:9px;">AGE</td>
					<td colspan="2">:'.$age.'</td>
				</tr>
				<tr>
					<td style="padding-left:9px;">S.S.S. NO.</td>
					<td colspan="2">:'.$d['CI_SSSNo'].'</td>
				</tr>
				<tr>
					<td style="padding-left:9px;">BANK/BRANCH</td>
					<td colspan="2">:'.$d['CP_BankBranch'].'</td>
				</tr>
				<tr>
					<td style="padding-left:9px;">ACCOUNT NO.</td>
					<td colspan="2">:'.$d['CP_BankAcctNo'].'</td>
				</tr>
				<tr>
					<td style="padding-left:9px;">H-F-W NAME</td>
					<td colspan="2">:'.(isset($d['CP_ITF'])?$d['CP_ITF']:'&nbsp;').'</td>
				</tr>
				<tr>
					<td style="padding-left:9px;">DATE OF DEATH</td>
					<td colspan="2">:'.(isset($d['CP_DateOfDeath'])?date('m/d/y', strtotime($d['CP_DateOfDeath'])):'&nbsp;').'</td>
				</tr>
				<tr>
					<td style="padding-left:9px;">CAUSE OF DEATH</td>
					<td colspan="2">:'.(isset($d['CP_CauseOfDeath'])?$d['CP_CauseOfDeath']:'&nbsp;').'</td>
				</tr>
				<tr>
					<td style="padding-left:9px;">PENSION TYPE</td>
					<td colspan="2">:'.(isset($d['CP_PensionType'])?$d['CP_PensionType']:'&nbsp;').'</td>
				</tr>
				<tr>
					<td style="padding-left:9px;">MO. PENSION</td>
					<td colspan="2">:'.number_format($d['CP_Amount'], 2).'</td>
				</tr>
				<tr>
					<td style="padding-left:9px;">WITHDRAWAL DATE</td>
					<td colspan="2">:'.$d['CP_WithdrawalDay'].'</td>
				</tr>
				<tr>
					<td style="padding-left:9px;">AGENT</td>
					<td colspan="2">:'.(isset($agent1name)?$agent1name.'; ':'&nbsp;').(isset($agent2name)?$agent2name:'&nbsp;').'</td>
				</tr>
				<tr>
					<td colspan="4">SPECIMEN SIGNATURE</td>
				</tr>
				<tr>
					<td colspan="4" style="height:30px;vertical-align:bottom;">1._________________________</td>
				</tr>
				<tr>
					<td colspan="4" style="height:30px;vertical-align:bottom;">2._________________________</td>
				</tr>
				<tr>
					<td colspan="2">CO-MAKER</td>
					<td style="width:300px;">NAME OF DEPENDENT</td>
					<td>BIRTH DATE</td>
				</tr>
		';
	}
				if($cd)
				{
					$i = 0;
					foreach($cd as $cd)
					{
						echo '
							<tr>
								<td colspan="2">'.($i==0?(isset($cmname)?$cmname:'&nbsp;'):'&nbsp;').'</td>
								<td style="width:300px;">'.$cd['CD_LName'].', '.$cd['CD_FName'].'</td>
								<td>'.(isset($cd['CD_BDate'])?date('m/d/y', strtotime($cd['CD_BDate'])):'').'</td>
							</tr>
						';
						$i++;
					}
				} else {
					echo '
							<tr>
								<td colspan="2">'.(isset($cmname)?$cmname:'&nbsp;').'</td>
								<td style="width:300px;">&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
						';
				}
		echo '
			</tbody>
		</table>
		</div><!-- end frontPage -->
		
		<div id="backPage" class="hide">
			<table cellpadding="0" cellspacing="0" style="width:95%;">
				<tbody>
					<tr>
						<td class="bt bb bl center" style="padding-left:9px;" width="12%">LOAN NO.</td>
						<td class="bt bb bl" style="padding-left:9px;" width="12%">LOAN DATE</td>
						<td class="bt bb bl" style="padding-left:9px;" width="12%">PENSION</td>
						<td class="bt bb bl" style="padding-left:9px;" width="12%">PAYMENT</td>
						<td class="bt bb bl" style="padding-left:9px;" width="6%">TERM</td>
						<td class="bt bb bl center" style="padding-left:9px;" width="20%">DURATION DATE</td>
						<td class="bt bb bl" style="padding-left:9px;" width="9%">REMARKS</td>
						<td class="bt br bb bl" style="padding-left:9px;" width="17%">BONUS RELEASE</td>
					</tr>
					<tr>
						<td class="bt bl" style="height:400px;">&nbsp;</td>
						<td class="bt bl">&nbsp;</td>
						<td class="bt bl">&nbsp;</td>
						<td class="bt bl">&nbsp;</td>
						<td class="bt bl">&nbsp;</td>
						<td class="bt bl">&nbsp;</td>
						<td class="bt bl">&nbsp;</td>
						<td class="bt br bl">&nbsp;</td>
					</tr>
				</tbody>
			</table>
		</div>
		';
		
	?>
	</body>
</html>