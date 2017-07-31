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
		margin-top: 0px;
		background-color: white;
		width: 100%;
		padding: 2px;
	}
	.trfixed{
		padding-bottom:10px
	}
</style>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.filterTable.js"></script>
<script type="text/javascript">
	$(document).ready(function()
	{
		function refresh(dd)
		{
		var f = dd.find('tr'), i = 0, j = 0, l = 0, m = '';
			if(f !=null)
			{
				for(g in f)
				{
					if(g > 0)
					{
						h = $(f[g]).find('td');
						
						var k = $(h[2]).html();
						if(k != '-')
						{
							i += parseFloat(k);
						}
						
						var k = $(h[3]).html();
						if(k != '-')
						{
							j += parseFloat(k);
						}
					}
				}
				l = parseFloat(i) - parseFloat(j);
				var m = l.toFixed(2);
			}
			$('#cv',parent.document).contents().find('#a4').val(m).click();
		}
		
		$('.ccid').click(function()
		{
			if($('#dfr').html()==undefined)
			{
				var a=$('.ccid').index($(this));
				$($('.ccid')[a]).after("<tr id='dfr'><td align='center'><input id='did'type='text'placeholder='Input Amount'/>"+
				"<select id='dcd'><option value='D'>Debit</option>"+
				"<option value='C'>Credit</option></select></select>"+
				"<input id='dad'type='button'value='Add'/>"+
				"<input id='dcl'type='button'value='Close'></td></tr>");
				$('#did').focus();
				$('#dcl').click(function()
				{
					$('#dfr').hide('slow',function()
					{
						$(this).remove();
					});
				});
				$('#dad').click(function()
				{
					if($('#did').val()!='')
					{
						var b=$($($('.ccid')[a]).find('td')[0]);
						var c=$('#cv',parent.document).contents().find('#den').contents().find('body');					
						var d=c.find('#tden');
						if(b.attr('title')=='')
							var e=b.html().replace(b.attr('id')+' ','');
						else var e=b.attr('title').replace(b.attr('id')+' ','');
						var f=d.find('tr').length;
						d.append('<tr class=\'datas\'id=\'den'+f+'\'><td>'+b.attr('id')+'</td><td>'+e+'</td>'+
						'<td align=\'right\'>'+($('#dcd').val()=='D'?$('#did').val():'-')+'</td>'+
						'<td align=\'right\'>'+($('#dcd').val()=='C'?$('#did').val():'-')+'</td>'+
						'<td align=\'right\'><input id=\'rdat'+f+'\'type=\'button\'value=\'Remove\'/></td>'+
						'</tr>');

						d.find('#rdat'+f).click(function()
						{
							var m=$(this).parent().parent();
							m.hide('slow',function()
							{
								m.remove();
								refresh( d );
								return false;
							});
						});

						$('#dfr').hide('slow',function()
						{
							$(this).remove();
						});

						refresh( d );

						$('.filter-table').find('input').val('').click().focus();
					}
					else
					{
						$('#did').css('background-color','rgba(255, 252, 0, 0.32)').css('border','2px dashed red').blur(function()
						{
							$(this).css('background-color',null).css('border',null);
						}).focus();
					}
				});
			}else
			{
				$('#did').css('background-color','rgba(255, 252, 0, 0.32)').css('border','2px dashed red').blur(function()
				{
					$(this).css('background-color','white').css('border','1px solid gray');
				}).focus();
			}
		});
		
		$('table.cl').filterTable({
			autofocus: 1,
			placeholder: 'Search Code/Title'
		});
		
	});
</script>
<body class="nomargin">
	<table width="100%"class="cl">
	<tr><td class="trfixed">&nbsp;</td></tr><?
		
		$j=0;
		foreach($datas as $d)
		{
			echo"<tr class='data ccid'>".
			"<td class='tag$j'id='{$d['code']}'".
				(strlen($d['name'])>20?
					"title='{$d['name']}'>{$d['code']} ".substr($d['name'],0,25).'...':
					'>'."{$d['code']} {$d['name']}").
			"</td></tr>";
			$j=$j?0:1;
		}
		
		
	?>
	</table>
</body>