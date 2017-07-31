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
	td { border-bottom: 1px solid #ccc; 
		padding: 5px 5px 5px 5px; 
	}

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
		padding-bottom:10px
	}
	.bl{
		border-left: 1px solid black;
	}
	.br{
		border-right: 1px solid black;
	}
	.bt{
		border-top: 1px solid black;
	}
	.bb{
		border-bottom: 1px solid black;
	}
	mes, .mes{
		color: red;
	}
</style>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.filterTable.js"></script>
<script type="text/javascript"><?
$this->load->helper('code');
echo trims("
	$(document).ready(function()
	{
		$('.pst').click(function()
		{
			var o=$(this).parent().parent();
			if(confirm('Are you sure you want to Post this FT request?'))
			{
				var i_=o.attr('id');
				var j_=$(o.find('td')[3]).html();
				$.post('v_',{a:i_,b:j_},function(r_)
				{
					if(r_.ok)
					{
						$(o.find('td')[3]).html(r_.res);
						$(o.find('td')[4]).html('Waiting for Approval');
					}else eval(r_.error);
				},
				'json');
			}
		});

		$('.cnc').click(function()
		{
			var o=$(this).parent().parent();
			if(confirm('Are you sure you want to Cancel this FT request?'))
			{
				var i_=o.attr('id');
				$.post('w_',{a:i_},function(r_)
				{
					if(r_.ok)o.hide('slow');
					else eval(r_.error);
				},
				'json');
				
			}
		});

		$('.prn').click(function()
		{
			parent.document.location='../sales/checkvoucher';
		});

	});");

?></script>
<body class="nomargin">
	<table width="100%"class="cl">
		<tr>
			<td class="trfixed br bb">TransCode<br>
				TransDate</td>
			<td class="trfixed br bb"width='1%'nowrap>Transfer From<br>
				Transfer To</td>
			<td class="trfixed br bb">Amount<br>Remarks</td>
			<td class="trfixed bb">Status<br>RequestedBy</td>
			<td class="trfixed bb">&nbsp;</td>
		</tr><?

		$j=0;  $tid = '';
		foreach($datas as $d)
		{
			$amount = number_format($d['amount'],2);
			$t1 = explode('|', $d['code1']);
			$code1 = $t1[0];
			$t2 = explode('|', $d['code2']);
			$code2 = $t2[0];
			echo trims("<tr class='data tag$j'id='{$d['ftid']}'valign='middle'>
				<td class='ccid br'width='1%'nowrap>".
				($d['ftid']==$tid?'&nbsp;':"{$d['ftid']}<br>{$d['transdate']}")."</td>
				<td class='br'>$code1<br>$code2</td>
				<td class='br'>$amount<br>{$d['remarks']}</td>
				<td class='br'>");
				if($d['ftid']!=$tid):
					if( $d['status']=='rejected' )
						echo"<mes>{$d['status']}</mes>";
					else
						echo"{$d['status']}";
					echo trims("<br>{$d['requestby']}</td><td>");

					if($d['status']=='forApproval'):
						echo"Waiting for Approval";
					elseif($d['status']=='draft'):
						echo trims("<input class='pst'type='button'value='Post'/><br>
						<input class='cnc'type='button'value='Cancel'/>");
					elseif($d['status']=='approved'):
						if($d['transtype']=='CvCheckPrint'):
							echo"<input class='prn'type='button'value='Print CV / Check'/>";
						else:
							echo"Online Transaction Completed.";
						endif;
					elseif($d['status']=='rejected'):
						echo"Note:<br><mes>{$d['note']}</mes>";
					endif;
				else:
					echo'&nbsp;</td><td>&nbsp;';
				endif;
			echo"</td></tr>";
			$j=$j?0:1;
			$tid=$d['ftid'];
		}

		
	?></table>
</body>