<style>
	.date{
		text-align:center;
		margin-bottom: 5px;
	}

	.center{
		text-align: center;
	}
	#ldri{
		position:absolute;
		background-image: url('<?=asset_url()?>/images/zoomloader.gif');
		background-repeat: no-repeat;
		background-position: center center;
		background-color: gray;
		opacity: 0.7;
	}
</style><?
	
	ini_set('display_errors','yes');
	$this->load->helper('code');

?><script><?

echo trims("

	

	$(document).ready(function()
	{
		var a=[
			$('input[name=opdat]'),".	//0
			"$('#opif'),".				//1
			"$('#bapl'),".				//2
			"undefined,".				//3 TV for content
			"$('#ort'),".				//4
			"$('#prt'),".				//5
			"'orprsub',".				//6
			"undefined,".				//7 TV for detail elements
			"$('#ldri')".				//8
		"];

		function dofunc(z)
		{
			a[8].show();
			a[1].attr('src','../collection/orprlist/'+z);
		}

		a[8].css('height',a[1].outerHeight())
		.css('width',a[1].outerWidth());

		a[0].change(function()
		{
			dofunc($(this).val());
		});

		dofunc(a[0].val());

		a[2].click(function()
		{
			a[3]=a[1].contents().find('body').find('.datas');

			if(a[3].length>0)
			{
				if(confirm('Are you sure you want to Apply those OR/PR assigned?'))
				{
					a[0].attr('disabled',true);
					a[2].attr('disabled',true);
					a[4].attr('disabled',true);
					a[5].attr('disabled',true);
					a[3].each(function()
					{
						a[7]=$(this).find('td');
						if(!$(a[7][10]).find('.dor').attr('disabled')&&$(a[7][12]).find('.eop').val()!='')
						{
							$.post(
								a[6],
								{
									a:$(a[7][1]).attr('ui'),
									b:$(a[7][10]).find('.dor').attr('checked')?'OR':(
										$(a[7][11]).find('.dpr').attr('checked')?'PR':''
									),
									c:$(a[7][12]).find('.eop').val(),
									d:parseFloat($(a[3]).index(this))
								},
								function(r)
								{
									a[3]=a[1].contents().find('body').find('.datas');
									a[7]=$(a[3][r.b]).find('td');
									$(a[7][10]).find('.dor').attr('disabled', true);
									$(a[7][11]).find('.dpr').attr('disabled', true);
									$(a[7][12]).find('.eop').attr('disabled', true);
									if($(a[7][10]).find('.dor').attr('checked'))a[4].val(r.c);
								},
								'json'
							);
						}
					});
					a[0].attr('disabled', false);
					a[2].attr('disabled', false);
					a[4].attr('disabled', false);
					a[5].attr('disabled', false);
				}	
			}else
			{
				alert('No List data Select a date.');
			}
		});

	});

");?></script>
OR / PR Assigning&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;| Date: <input name="opdat"type="date"value="<?=date('Y-m-d')?>"class="date"/>&nbsp;&nbsp;<label id="nofr"></label>
&nbsp;&nbsp;| O.R. No: <input id="ort"type="text"class="center"value="<?=$orno?>"/>
&nbsp;&nbsp;| P.R. No: <input id="prt"type="text"class="center"value="<?=$prno?>"/>
&nbsp;&nbsp;<input type="button"id="bapl"value="Apply"disabled/><br>
<center id="ldri">&nbsp;</center>
<iframe id="opif"width="100%"frameborder="0"style="border:1px solid gray;"height="400"></iframe>