<style>	body,table{		font-family: 'Arial';		font-size: 12px;		cursor: default;	}		.nomargin {		margin: 0px;	}	.tag1 {		background-color: #D0D0D0;	}		.tag1:hover {		background-color: #99FFFF;	}		.tag0 {		background-color: #F0F0F0;	}		.tag0:hover {		background-color: #99FFFF;	}		.w1 {		width: 250px;	}		/* generic table styling */	table {		border-collapse: collapse;		border-top: 1px solid #000;	}		th {		border-bottom: 2px solid #999;		background-color: #eee;		vertical-align:  bottom;		padding: 4px;	}		td {		border-bottom: 1px solid #ccc;		padding: 4px;	}	/* filter-table specific styling */	td.alt {		background-color: #ffc;		background-color: rgba(255, 255, 0, 0.2);	}		.filter-table{		position: fixed;		margin-top: 0px;		background-color: white;		width: 100%;		padding: 2px;	}		.trfixed{		padding-bottom:10px;	}		.fixed{		position: ;		background-color: #FFF;		width: 100%;	}		.bl {		border-left:1px solid gray;	}			.br {		border-right:1px solid gray;	}		.bt {		border-top:1px solid gray;	}		.bb {		border-bottom:1px solid gray;	}		.center {		text-align:center;	}		.edit {		color:#FF0000;	}		.edit:hover {		cursor: pointer;		font-weight: bold;	}		.imgdata {		background-color: #f9f7ad;	}</style><script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script><script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.filterTable.js"></script><script>	$(document).ready(function()	{		$('table.cl').filterTable({			autofocus: 1,			placeholder: 'Search Client'		});				$('.edit').click(function(){			var width = 700;var height = 500;var top = (screen.height/2)-(height/2);var left = (screen.width/2)-(width/2);			window.open('../../adjform/'+$(this).attr('id'),'popupWindow','width='+width+',height='+height+',top='+top+',left='+left+',scrollbars=yes').focus();		});				$('#delbtn').click(function(){			var a = confirm("Proceed to delete?");			if (a == true) {				$('#collform').submit();			} else {				return false;			} 		})	})</script><body style="margin:0px;">	<form id="collform" method="post" action="../../colldelete/0/0">		<table width="100%"cellpadding="0"cellspacing="0"class="cl"style="margin-top: ;">			<tbody>				<tr>					<td class="trfixed">&nbsp;</td>				</tr>				<tr>					<td colspan="12"><input id="delbtn" type="button" name="delbtn" value="Delete" /></td>				</tr>				<tr>					<th class="bb">&nbsp;</th>					<th align="left"class="bl bb">Name</th>					<th align="left"class="bl bb">Bank Branch</th>					<th align="left"class="bl bb">Trace Ref.#</th>					<th align="right"class="bl bb">ATM Beg. Bal.</th>					<th align="right"class="bl bb">Amount Drawn</th>					<th align="right"class="bl bb">ATM End</th>					<th align="right"class="bl bb">Directly Paid</th>					<th class="bl bb">Date Due</th>					<th class="bl bb center">Encode By</th>					<th class="bl bb center">O.R./P.R. #</th>					<th class="bl bb center">&nbsp;</th>					<th class="bl bb center">&nbsp;</th>				</tr>				<?								$ctr=1;				$j=0;				foreach ($datas as $d)				{								echo"					<tr class='data tag$j " . (!empty($d['receiptimg']) ? 'imgdata' : '') . "'>						<td align='right'class='bb'>$ctr)</td>						<td class='bl bb'>{$d['fullname']}</td>						<td class='bl bb'>{$d['bankbranch']}</td>						<td class='bl bb'>{$d['tracerefno']}</td>						<td class='bl bb'align='right'>".number_format($d['atmbegbal'],2)."</td>						<td class='bl bb'align='right'>".number_format($d['amtdrawn'],2)."</td>						<td class='bl bb'align='right'>".number_format($d['atmendbal'],2)."</td>						<td class='bl bb'align='right'>".number_format($d['directpaid'],2)."</td>						<td class='bl bb'align='center'>".$d['duedate']."</td>						<td class='bl bb'align='center'>".$d['encby']."</td>						<td class='bl bb'align='center'>".$d['orprno']."</td>						<td class='bl bb'align='center'><label class='edit' id='{$d['uid']}|{$d['acctno']}'>Edit</label></td>						<td class='bl bb'align='center'><input class='coll' type='checkbox' name='coll[]' value='".$d['uid']."' /></td>					</tr>";					$ctr++;					$j=$j?0:1;				}				?>			</tbody>		</table>	</form>