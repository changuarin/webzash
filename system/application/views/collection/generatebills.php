<style>
	.day{
		border: 1px dotted gray;
		padding-left: 2px;
		padding-right: 2px;
	}
	.day:hover{
		border: 1px solid blue;
		background-color: orange;
	}
	.days{
		border: 1px solid blue;
		background-color: orange;
		padding-left: 2px;
		padding-right: 2px;
	}
	#ldri{
		position:absolute;
		background-image: url('<?=asset_url()?>/images/zoomloader.gif');
		background-repeat: no-repeat;
		background-position: center center;
		background-color: gray;
		opacity: 0.7;
	}
	.br{ border-right: 1px solid blue; }
	.bl{ border-left: 1px solid blue; }
	.bt{ border-top: 1px solid blue; }
	.bb{ border-bottom: 1px solid blue; }

</style>	
<script>

	function reload(d)
	{
		$('#ldri').show();

		var m=['January','February','March','April','May','June','July','August','September','October','November','December'];
		var y=2016;
		
		var lastDayOfMonth = new Date(d.getFullYear(), d.getMonth()+1, 0);

		$('#mof').html('');
		for(a in m)
		{
			$('#mof').append('<option value="'+a+'"'+(a=='3'?' selected':'')+'>'+m[a]+'</option>');
		}

		$('#yof').html('');
		for(i=y;i<=(d.getFullYear()+1);i++)
		{
			$('#yof').append('<option'+(d.getFullYear()==i?' selected':'')+'>'+i+'</option>');	
		}

		$.post('ajbmnt',{a:$('#yof').val(),b:parseFloat($('#mof').val())+1},function(d)
		{
			eval(d);
		});

		$('#glif').attr('src','genlist/'+$('#yof').val()+'/'+(parseFloat($('#mof').val())+1));

	}

	$(document).ready(function()
	{
		var d = new Date();
		reload(d);

		$('#ldri').css('height',$('#glif').outerHeight()).css('width',$('#glif').outerWidth());

		$('#mof').change(function()
		{
			var d = new Date();
			var d = new Date($('#yof').val(), $(this).val(), d.getDate());
			reload(d);
		});

		$('#yof').change(function()
		{
			var d = new Date();
			var d = new Date($(this).val(), $('#mof').val(), d.getDate());
			reload(d);
		});

		$('#procon').click(function()
		{
			if(confirm('Are you sure you want to Generate bills for this date?'))
			{
				$(this).attr('disabled',true);
				$('.sel').attr('disabled',true);
				var z0=$('#glif').contents().find('tr');
				for(z1=1;z1<z0.length;z1++)
				{
					var z2=$(z0[z1]).find('td');
					if($(z2[13]).html()!='Billed')
					{
						$.post('billed',{
							a:$(z2[1]).attr('id'),
							b:$(z2[1]).html(),
							c:$(z2[2]).html(),
							d:$(z2[3]).html(),
							e:$(z2[4]).html(),
							f:$(z2[5]).html(),
							g:$(z2[7]).html(),
							h:$(z2[8]).html()+' - '+$(z2[9]).html(),
							i:$(z2[10]).html(),
							j:$(z2[11]).html(),
							k:$(z2[12]).html(),
							l:$('#yof').val()+'-'+(parseFloat($('#mof').val())+1)+'-'+$(z2[0]).html(),
							m:z1,
							n:$(z2[6]).html(),
							o:$(z2[1]).attr('bc')
						},
						function(r)
						{
							eval(r);
						});
					}
				}
				$('.sel').attr('disabled', false);
			}
		});

		$('#manbil').click(function()
		{
			if($(this).val().substr(0,4)=='Manu')
			{
				$(this).val('Cancel');
				if($('#m1').attr('id')==undefined)
				{
					$('#ldri').append('<iframe id="m1"width="30%"<?
						?>frameborder="0"src="manclst"<?
						?>class="bl bt bb"height="100"></iframe><?
						?><iframe id="m2"width="69%"frameborder="0"<?
						?>class="bl br bt bb"height="100"></iframe>');
					$('#m1').insertAfter( $('#ldri') );
					$('#m2').insertAfter( $('#m1') );
					$('#glif').attr('height', '300px');
				}
			}else
			{
				$(this).val('Manual Billing');
				$('#m1').remove();
				$('#m2').remove();
				$('#glif').attr('height', '400px');
			}
		});
		
		$('#reloadButton').click(function(){
			$('#glif').attr('src', $('#glif').attr('src'));
		});

	});
</script>
Generate Bills for <select class="sel"id="mof"></select>&nbsp;<select class="sel"id="yof"></select>
<input id='procon'type='button'value='Process Now'disabled/>&nbsp;<label id="nofr"></label>&nbsp;<input id="reloadButton" type="button" name="reloadButton" value="Reload" />
<span style="padding-left:150px"><input id='manbil'type='button'value='Manual Billing'disabled/></span>
<br>
<center id="ldri">&nbsp;</center>
<iframe id="glif"width="100%"frameborder="0"src="genlist"style="border:1px solid gray;"height="400"></iframe>