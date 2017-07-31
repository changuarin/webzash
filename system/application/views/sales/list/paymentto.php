<style>
	body,table {font-family:courier;font-size:12px;cursor:default;}
	table {border-collapse: collapse;}
	table tr td{border:1px solid #000;padding-top:3px;padding-right:2px;padding-left:2px;vertical-align:middle;}
	
	.nomargin {margin:0px;}
	.right {text-align:right;}
	.tag1 {background-color:#D0D0D0;}
	.tag1:hover {background-color:#99FFFF;}
	.tag0 {background-color:#F0F0F0;}
	.tag0:hover {background-color:#99FFFF;}
	
</style>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.filterTable.js"></script>
<script type="text/javascript">
	$(document).ready(function()
	{
	
		$('#CI_Name').focus();
		
		$('.ccid').click(function(){
			var clss = $(this).attr('class');
			var data = clss.split(' ');
			var i = data[4];
			var a = $('#ciname'+i).html();
			$('#PaymentToName', window.opener.document).html(a);
			$('#LH_PaymentTo_Ref', window.opener.document).val(data[2]+'|'+data[3]);
			$('#cancelPayToButton', window.opener.document).attr('class', '');
			window.close();
		});

	});
</script>
<body class="nomargin">
	<form method="post" action="paymentToList">
		<table style="width:100%;">
			<tr style="position:fixed;">
				<td colspan="3" style='background-color:#FF9;border:0;padding-top:1px;padding-bottom:1px;width:1600px'>
					<input id="CI_Name" type="text" name="CI_Name">
					<input id="searchButton" type="submit" name="searchButton" value="Search">
				</td>
			</tr>
			
		<?
			if($datas)
			{
				echo"
				<tr>
					<td colspan='3' style='height:26px;'>&nbsp;</td>
				</tr>";
				$i=0;
				$j=0;
				foreach($datas as $d)
				{
					echo"
					<tr class='ccid tag$j {$d['CI_AcctNo']} {$d['LH_PN']} $i'>
						<td id='ciname$i' style='width:40%;'>{$d['CI_Name']}</td>
						<td style='width:40%;'>{$d['LH_PN']}({$d['LH_LoanTrans']})</td>
						<td class='right' style='width:20%;'>".number_format($d['LH_Balance'], 2)."</td>
					</tr>";
					$i++;	
					$j=$j?0:1;
				}
			}
			
		?></table>
	</form>
</body>