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

	/* filter-table specific styling */
	td.alt { background-color: #ffc; background-color: rgba(255, 255, 0, 0.2); }
	.filter-table{
    	position: fixed;
    	top: 0px;
    	background-color: white;
    	width: 100%;
    	padding: 2px;
    }
   	.trfixed{
   		padding-bottom:10px
   	}
   	.cancel{
   		padding: 3px 57px 1px 57px;
   		color: black;
   		font-size: 110%
   	}
   	.advque, .que{
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
		cursor: default;
   	}
</style>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/rsm.js"></script>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.filterTable.js"></script>
<script type="text/javascript">
	$(document).ready(function()
	{		
		$('input.cancel').click(function()
		{
			var a=$(this);
			if(confirm('Are you sure you want to Cancel this transaction?'))
			{
				$.post('<?=base_url()?>index.php/sales/refundcancel',
					{'a': a.attr('aid')},
					function(r)
					{
						eval(r);
					}
				);
			}
		});

		$('.avque, .que').mousedown(function()
		{
			$(this).css('border-style','inset');

		}).mouseup(function()
		{
			$(this).css('border-style','outset');

		});
		
		$('.advque').popupWindow({ 
			centerBrowser: 1,
			windowName: 'form1',
			windowURL: '<?=base_url()?>index.php/sales/advrefunda',
			width: 700,
			height: 550
		});
		
		$('.que').popupWindow({ 
			centerBrowser: 1,
			windowName: 'form1',
			windowURL: '<?=base_url()?>index.php/sales/refunda',
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
	<table width="100%"class="cl"style="margin:5px auto;background-color:white" cellpadding="3px"align="center">
		<tr>
			<th style="background-color:#A9D8E2; border: 2px outset white">Name</th>
			<th style="background-color:#A9D8E2; border: 2px outset white">PnNo.</th>
			<th style="background-color:#A9D8E2; border: 2px outset white">BankBranch</th>
			<th style="background-color:#A9D8E2; border: 2px outset white"align="right">Amount</th>
			<th style="border:hidden"align="center"width="1%">QueNo.</th>
			<th style="border:hidden"align="right"colspan="2">QuedBy</th>
		</tr>');
		
		$j=0;
		foreach($datas as $d)
		{
			$refundamt = $d['advrefund'] + $d['refunddue'];
			echo trims("<tr class='tag$j data'id='tr{$d['queno']}'>
				<td>{$d['ci_name']}</td>
				<td nowrap>{$d['pnno']}( <font color='red'>{$d['transtype']}</font> )</td>
				<td>{$d['ci_bankbranch']}</td>
				<td align='right'>".number_format($refundamt,2)."</td>");
				if($d['status']=='approved'):
					echo trims("<td align='center'colspan='2'nowrap><b><small>* FOR VOUCHER *</small></b></td>");
				else:
					echo trims("<td align='center'nowrap><label class='que'uid='{$d['aid']}'>{$d['queno']}</label></td>
					<td width='1%'><input type='button'value='Cancel'class='cancel'aid='{$d['aid']}'/></td>");
				endif;
				echo trims("<td width='1%'>{$d['queby']}</td>
			</tr>");	
			$j=$j?0:1;
		}
		
	echo trims('</table>
</body>');