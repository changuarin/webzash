<style>
	body,table{
		font-family: 'Arial';
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
	table { border-collapse: collapse; border-top: 1px solid #000; }
	th { border-bottom: 2px solid #999; background-color: #eee; vertical-align:  bottom; padding: 4px; }
	td { border-bottom: 1px solid #ccc; padding: 4px; }

	/* filter-table specific styling */
	td.alt { background-color: #ffc; background-color: rgba(255, 255, 0, 0.2); }
	.filter-table{
		position: fixed;
		margin-top: 0px;
		background-color: white;
		width: 100%;
		padding: 2px;
	}
	.trfixed{
		padding-bottom:10px;
	}
	.bl{border-left:1px solid gray;}
	.br{border-right:1px solid gray;}
	.bt{border-top:1px solid gray;}
	.bb{border-bottom:1px solid gray;}
	.center{text-align:center;}
</style>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.filterTable.js"></script>
<script>
	$(document).ready(function()
	{
		$('table.cl').filterTable({
			autofocus: 1,
			placeholder: 'Search Client'
		});
	})
</script>
<body style="margin:0px;">
	
	<table width="100%"cellpadding="0"cellspacing="0"class="cl">
		<tbody>
			<tr>
				<td class="trfixed">&nbsp;</td>
			</tr>
			<tr>
				<th class='bb'>&nbsp;</th>
				<th align="left"class="bl bb">Name</th>
				<th align="left"class="bl bb">Bank Branch</th>
				<th align="left"class="bl bb">Trace Ref.#</th>
				<th align="right"class="bl bb">ATM Beg. Bal.</th>
				<th align="right"class="bl bb">Amount Drawn</th>
				<th align="right"class="bl bb">ATM End</th>
				<th align="right"class="bl bb">Directly Paid</th>
				<th class="bl bb">Date Due</th>
				<th class="bl bb center">Encode By</th>
				<th class="bl bb center">O.R./P.R. #</th>
			</tr>
			<?
			
			$ctr=1;
			$j=0;
			foreach ($datas as $d)
			{			
				echo"<tr class='data tag$j'>
				<td align='right'class='bb'>$ctr)</td>
				<td class='bl bb'>{$d['fullname']}</td>
				<td class='bl bb'>{$d['bankbranch']}</td>
				<td class='bl bb'>{$d['tracerefno']}</td>
				<td class='bl bb'align='right'>".number_format($d['atmbegbal'],2)."</td>
				<td class='bl bb'align='right'>".number_format($d['amtdrawn'],2)."</td>
				<td class='bl bb'align='right'>".number_format($d['atmendbal'],2)."</td>
				<td class='bl bb'align='right'>".number_format($d['directpaid'],2)."</td>
				<td class='bl bb'align='center'>".$d['duedate']."</td>
				<td class='bl bb'align='center'>".$d['encby']."</td>
				<td class='bl bb'align='center'>".$d['orprno']."</td>
				</tr>";

				$ctr++;
				$j=$j?0:1;
			}

			?>
		</tbody>
	</table>