<style>
	body,table{
		font-family: courier;
		font-size: 12px;
		cursor: default;
	}
	.nomargin{margin:0px;}

	.tag1{background-color: #D0D0D0;}
	.tag1:hover{background-color: #99FFFF;}
	.tag0{background-color: #F0F0F0;}
	.tag0:hover{background-color: #99FFFF;}
	.w1{width: 250px;}
	
	/* generic table styling */
	table { border-collapse: collapse; }
	th { border-bottom: 2px solid #999; background-color: #eee; vertical-align: bottom; }
	td { border-bottom: 1px solid #ccc; }
	
   	.cancel{
   		padding: 3px 57px 1px 57px;
   		color: black;
   		font-size: 110%
   	}
   	.que{
   		font-weight: bold;
   		font-family: Courier;
   		font-size: 15px;
   		padding-top: 2px;
   		padding-left: 6px;
   		padding-right: 6px;
   		background-color: orange;
   		border: 2px outset white;
   		border-bottom-left-radius: 5px;
		border-bottom-right-radius: 5px;
		border-top-left-radius: 5px;
		border-top-right-radius: 5px;
   	}
</style>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/rsm.js"></script>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.filterTable.js"></script>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/rsm.custom.js"></script>
<script type="text/javascript">
	$(document).ready(function()
	{		
		$('input.submit').popupWindow({ 
			centerBrowser: 1,
			windowName: 'form1',
			windowURL: '<?=base_url()?>index.php/sales/refundq',
			width: 700,
			height: 550
		}); 
		
		$('table.cl').filterTable({
			autofocus: 1,
			placeholder: 'Search Client'
		});
	});
</script><?
echo trims('
<body class="nomargin">
	<form action="list4refund2" method="post">
		<input type="text" name="name" value="' . ($lname == '' && $fname == '' ? '' : strtoupper($lname) . ", " . strtoupper($fname)) . '" placeholder="' . ($lname == '' && $fname == '' ? 'Lastname, Firstname' : '') . '">
		<input type="submit" value="Search">
	</form>
	<table width="100%"class="cl"style="margin:5px auto;background-color:white" cellpadding="3px"align="center">
		<tr>
			<th style="background-color:#A9D8E2; border: 2px outset white">Name</th>
			<th style="background-color:#A9D8E2; border: 2px outset white">PnNo.</th>
			<th style="background-color:#A9D8E2; border: 2px outset white">BankBranch</th>
			<th style="background-color:#A9D8E2; border: 2px outset white"align="right">Amount</th>
			<th width="1%"style="border:hidden"align="right">QueNo.</th>
		</tr>');

		$j = 0;
		$code = ''; $amount = 0;
		$i = 0;
		
		if(!empty($datas))
		{
			foreach($datas as $d)
			{			
				if($d['isQ']['class']!='done'):

					echo trims("<tr class='tag".($d['clientid']!=$code?$j:'')." data'id='tr{$d['trid']}'>
						<td>{$d['name']}</td>
						<td nowrap>{$d['pnno']}( <font color='red'>{$d['ptype']}|".str_replace('SSS ','',$d['pensiontype'])."</font> )</td>
						<td>{$d['bankbranch']}</td>
						<td align='right'id='amount"."$i'>".number_format($d['amount'],2)."</td>
						<td align='center'nowrap>");

						if(is_array($d['isQ']))
						{
							if($d['isQ']['class']!='forvoucher')
							{
								echo"<label class='que'>{$d['isQ']['value']}</label>";
							}else echo"<b><small>* FOR VOUCHER *</small></b>";
						}
						else
						{
							echo"<input id='sb"."$i'type='button'value='SUBMIT'".
							"class='submit'uid='{$d['colid']}'V1='{$d['ptype']}.{$d['bankbranch']}'/>";
						}

					echo trims("</td>
					</tr>");	
					$j=$j?0:1;

				endif;
				
				$code = $d['clientid'];

				if($d['butal']):

					echo trims("
					<script>
						var amt=N($('#amount".$i."').html());
						amt+=parseFloat('{$d['butal']}');
						$('#amount".$i."').html(Format(amt,2));
					</script>");

				endif;

				$i++;
			}
		}
		
	echo trims('</table>
</body>');