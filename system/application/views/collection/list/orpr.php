<?
ini_set('display_errors', 'yes');

$this->load->helper('code');

$edate = $this->uri->segment(3,'');

$data = $this->db->query(
	"SELECT a.*, CONCAT(b.CI_LName, ', ', b.CI_FName, ' ', b.CI_MName) AS fullname, b.CI_BankBranch as bankbranch
	FROM collection_entry a, client b
	WHERE a.cid = b.CI_AcctNo
	AND DATE_FORMAT(a.duedate, '%Y-%m-%d') = '$edate'
	ORDER BY b.CI_LName, b.CI_FName, b.CI_MName;
")->result_array();

?>
<style>
	.bl{border-left:1px solid gray;}
	.br{border-right:1px solid gray;}
	.bt{border-top:1px solid gray;}
	.bb{border-bottom:1px solid gray;}
	table tr td{padding:2px;}
</style>
<link type="text/css" rel="stylesheet" href="<?=base_url()?>system/application/assets/css/forms.css">
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.filterTable.js"></script>
<script><?

echo trims("
	$(document).ready(function()
	{
		$('table.cl').filterTable({
			autofocus: 1,
			placeholder: 'Search Client'
		});
		
		var a=[
			$('#nofr', parent.document),
			$('#bapl', parent.document),
			$('.eop'),
			$('#ort', parent.document),
			$('#prt', parent.document),
			$('.aor'),
			$('.apr'),
			$('.dor'),
			$('.dpr'),
			$('#ldri', parent.document)
		];");

	if(isset($data)&&count($data)):	
		
		echo trims("

		a[0].html('".count($data)." Records(s).');

		a[2].keypress(function(event)
		{
			if(event.which==13)
			{
				var c=parseFloat($(this).parent().parent().attr('id').replace('tr',''));
				c++;
				var d=$('#tr'+c).find('select');
				if(d!=undefined)d.focus();
			}
		});".

		// OR ALL
		"a[5].change(function()
		{
			var z0=parseFloat(a[3].val());
			if($(this).attr('checked'))
			{
				a[6].attr('checked',false);
				a[8].each(function()
				{
					if(!$(this).attr('disabled'))$(this).attr('checked',false);
				});
			}
			a[7].each(function()
			{
				if(!$(this).attr('disabled'))
				{
					if(a[5].attr('checked'))
					{
						$(this).attr('checked',true);
						$(a[2][a[7].index(this)]).val(z0);
						z0++;
					}else
					{
						$(a[2][a[7].index(this)]).val('');
						$(this).attr('checked',false);
					}
				}
			});
		});".

		// PR ALL
		"a[6].change(function()
		{
			var z1=a[4].val();
			if($(this).attr('checked'))
			{
				a[5].attr('checked',false);
				a[7].each(function()
				{
					if(!$(this).attr('disabled'))$(this).attr('checked',false);
				});
			}
			a[8].each(function()
			{
				if(!$(this).attr('disabled'))
				{
					if(a[6].attr('checked'))
					{
						$(this).attr('checked',true);
						$(a[2][a[8].index(this)]).val(z1);
					}
					else
					{
						$(a[2][a[8].index(this)]).val('');
						$(this).attr('checked',false);
					}
				}
			});
		});

		function recompute()
		{
			var z0=parseFloat(a[3].val()),z1=a[4].val();
			a[7].each(function()
			{
				if($(a[8][a[7].index(this)]).attr('checked')&&!$(a[8][a[7].index(this)]).attr('disabled'))
				{
					$(a[2][a[7].index(this)]).val(z1);

				}
				else if($(a[7][a[7].index(this)]).attr('checked')&&!$(a[7][a[7].index(this)]).attr('disabled'))
				{
					$(a[2][a[7].index(this)]).val(z0);
					z0++;
				}
				else if(!$(this).attr('checked')&&!$(this).attr('disabled'))
				{
					$(a[2][a[7].index(this)]).val('');
				}
			});
		}".

		// OR DETAILS
		"a[7].change(function()
		{
			var b=a[7].index(this);
			if($(a[8][b]).attr('checked'))$(a[8][b]).attr('checked',false);
			recompute();
		});".

		// PR DETAILS
		"a[8].change(function()
		{
			var b=a[8].index(this);
			if($(a[7][b]).attr('checked'))$(a[7][b]).attr('checked',false);
			recompute();
		});

		a[1].attr('disabled', false);");

	endif;

		echo trims("
		a[9].hide();

	});
");

?></script>
<body style="margin:0px;"><table width="100%"cellpadding="0"cellspacing="0"class="cl">
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
		<th class="bl bb">Encode By</th>
		<th class="bl bb"><label>O.R.<br><input class="aor"type="checkbox"/></label></th>
		<th class="bl bb"><label>P.R.<br><input class="apr"type="checkbox"/></label></th>
		<th class="bl bb">Ref.#</th>
		<th class="bl bb">Status</th>
	</tr><?

	$j=0;$ctr=1;$atmbegbal=0;$amtdrawn=0;$atmendbal=0;$directpaid=0;
	foreach ($data as $d)
	{			
		$isPR=$d['orprtype']=='PR'?'checked':'';
		$isOR=$d['orprtype']=='OR'?'checked':'';

		$isOP=$isOR?' disabled':($isPR?' disabled':'');

		$refno=$d['orprno']?"value='{$d['orprno']}'disabled":'';
		echo"<tr class='datas data tag$j'id='tr$ctr'>
		<td align='right'class='bb'>$ctr)</td>
		<td id='{$d['cid']}'ui='{$d['uid']}'class='bl bb'>{$d['fullname']}</td>
		<td class='bl bb'>{$d['bankbranch']}</td>
		<td class='bl bb'>{$d['tracerefno']}</td>
		<td class='bl bb'align='right'>".number_format($d['atmbegbal'],2)."</td>
		<td class='bl bb'align='right'>".number_format($d['amtdrawn'],2)."</td>
		<td class='bl bb'align='right'>".number_format($d['atmendbal'],2)."</td>
		<td class='bl bb'align='right'>".number_format($d['directpaid'],2)."</td>
		<td class='bl bb'align='center'>{$d['duedate']}</td>
		<td class='bl bb'align='center'>{$d['encby']}</td>
		<td class='bl bb'align='center'><input class='dor'type='checkbox'$isOR$isOP/></td>
		<td class='bl bb'align='center'><input class='dpr'type='checkbox'$isPR$isOP/></td>
		<td class='bl bb'align='center'><input class='eop'name='eop'placeholder=''$refno/></td>
		<td class='bl bb'id='tdst'>&nbsp;</td>
		</tr>";

		$j++;$ctr++;$atmbegbal+=$d['atmbegbal'];$amtdrawn+=$d['amtdrawn'];$atmendbal+=$d['atmendbal'];$directpaid+=$d['directpaid'];
	}
	// 20,215-05-04 For Checking of Total Amount Withdrawn
		echo"<tr>
			<td colspan='4' style='font-weight:bold;text-align:right;border:1px solid black;'>Total</td>
			<td style='font-weight:bold;text-align:right;border:1px solid black;'>".number_format($atmbegbal, 2)."</td>
			<td style='font-weight:bold;text-align:right;border:1px solid black;'>".number_format($amtdrawn, 2)."</td>
			<td style='font-weight:bold;text-align:right;border:1px solid black;'>".number_format($atmendbal, 2)."</td>
			<td style='font-weight:bold;text-align:right;border:1px solid black;'>".number_format($directpaid, 2)."</td>
			<td colspan='6' style='font-weight:bold;text-align:right;border:1px solid black;'>&nbsp;</td>
		</tr>";

?></table>