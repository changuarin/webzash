<? $this->load->helper('code'); ?>
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
	.isSelected, .isSelected.alt, .alt.isSelected{background-color:#FFFF00;color:#FF0000;}
</style>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.filterTable.js"></script>
<script type="text/javascript"><?

	echo trims("
	function msgbox(z,w)
	{
		if(z.attr('id')==undefined)
		{
			$('#main-content', parent.document).prepend('<div id=\"error-box\"><ul><li>Select a Branch first.</li></ul></div>');
			var y=function(){
				$('#error-box', parent.document).hide('slow',function()
				{
					$(this).remove();
				});
			};

			var x=setTimeout(y, 3000);

		}else
		{
			z.html('<ul><li>Select a Branch first.</li></ul>');
			var y=function(){
				z.hide('slow',function()
				{
					$(this).remove();
				});
			};
			var x=setTimeout(y, 3000);
		}
		if(w!=undefined)w.focus();
	}

	$(document).ready(function()
	{
	
		$('.ccid').click(function()
		{
			var k = $(this);
			var a = $(this).attr('class');
			var b = a.split(' ');
			var h = $(this).html();
			var j = h.split(' ');

			var cv = $('#cv',parent.document).contents();
			cv.find('#a3, #a4, #a5, #a6, #a7, #a8, #a9, #b1, #b2, #b4, #b5').val('');

			").

			// 20150820 -> rsm -> This code is for refund transfer

			trims("

			var l=[
				cv.find('#sbt'),".   /* 0 */"
				cv.find('#sbmt'),".  /* 1 */"
				cv.find('.trans'),". /* 2 */"
				cv.find('#b0'),".    /* 3 */"
				cv.find('#cv-form')"./* 4 */"
			];

			if(k.attr('from')==undefined)
			{
				l[0].show().val('');
				l[1].val('Submit');
				var p=l[2].find('label');
				p.html('Transfer to:');

				l[2].css('color','black');
				l[1]=cv.find('#sbmt'); 

				l[0].attr('disabled',false);
				l[1].attr('disabled',false).unbind('click').click(function()
				{
					if(l[0].val()!='')
					{
						var m='';
						l[0].find('option').each(function()
						{
							if($(this).is(':selected'))m=$(this).text();
						});

						if(confirm('Are you sure you want to Transfer this transaction ".
						"to \"'+m+'\" branch? '))
						{
							l[0].attr('disabled',true);
							l[1].attr('disabled',true);
							l[2].css('color','grey');

							$.ajax({
								type: 'POST',
								url: 'interBranch',
								data: {a:k.attr('id'),b:l[4].attr('class'),c:m},
								error: function()
								{
									alert('Error');
								},
								success: function(r)
								{
									eval(r);
								}
							});

						}
					}else msgbox($('.error-box', parent.document), l[0]);
					
					this.isDefaultPrevented = true;

				});
				l[2].css('color','black');
			
			}else
			{
				l[0].hide();
				l[1].val('Cancel').attr('disabled',false);
				var n=k.attr('from');
				n=n.split('|');
				var o=n[1];
				var p=l[2].html();

				l[2].html(p.replace('Transfer to:','Transferred from '+o+' branch'));

				l[2].css('color','black');
				l[1]=cv.find('#sbmt'); 
				l[1].unbind('click').click(function()
				{
					if(confirm('Are you sure you want to Cancel this transaction?'))
					{
						$.ajax({
							type: 'POST',
							url: 'brtrcancel',
							data: {a:k.attr('id'),b:o},
							error: function()
							{
								alert('Error!');
							},
							success: function(r)
							{
								eval(r);
							}

						});
					}
					this.isDefaultPrevented=true;
				});
			}
			").

			// End here
			
			trims("

			if($(this).attr('tag')=='sales')
			{
				$.post('getSalesCV', 
				{
					acctno:b[2],
					pnno:j[0],
					isbrtrans:k.attr('from')
				},
				function(data)
				{
					var b = data.split(';');
					var c = $('#cv',parent.document).contents();
					
					c.find('#cv-form').attr('action', 'insertSalesCV').attr('class', 'sales');
					c.find('#b1, #b2, #a3, #a4, #a8, #a9').val('');
					c.find('#tr0').attr('class', 'hide');
					c.find('#tr0').attr('class', '');
					c.find('#desc').html('PN');
					for(i=1;i<20;i++)
					{
						c.find('#pn'+i).html('');
						c.find('#amt'+i).html('');
						c.find('#re'+i).html('');
						c.find('#ck'+i).attr('checked', false);
						c.find('#tr'+i).attr('class', 'hide');
					}
					
					var rows = b[0];
					for(i=0;i<rows;i++)
					{
						var count = i + 1;
						c.find('#tr'+count).attr('class', '');
					}
					var d = parseFloat(rows) + 1;
					for(i=1;i<d;i++)
					{
						c.find('#pn'+i).html(b[i]);
					}
					var e = parseFloat(rows) + parseFloat(d);
					var f = 1;
					while(i<e)
					{
						c.find('#amt'+f).html(b[i]);
						f++;
						i++;
					}
					var g = parseFloat(rows) + parseFloat(e)
					var f = 1;
					while(i<g)
					{
						c.find('#re'+f).html(b[i]);
						f++;
						i++;
					}
					c.find('#a0').val(b[i]);
					var i = parseFloat(i) + 1;
					c.find('#a2').val(b[i]);
					var i = parseFloat(i) + 1;
					c.find('#b0').val(b[i]);
					c.find('#preview-btn, #rpd-btn, #rplc-btn, #cancel-btn').attr('disabled', true);
					c.find('#b4').val(k.attr('from')!=undefined?k.attr('from'):'');
					c.find('#b5').val(k.attr('id'));
				});

			}
			else if($(this).attr('tag')=='refund')
			{

				var clss = $(this).attr('class');
				var clss = clss.split(' ');
				var cntnt = $(this).html();
				var cntnt = cntnt.split(' ');
				$.post('getRefundCV',
				{
					acctno:clss[2],
					pnno:cntnt[0],
					isbrtrans:k.attr('from')
				},
				function(data)
				{
					var data = data.split(';');
					
					cv.find('#cv-form').attr('action', 'insertRefundCV').attr('class', 'refund');
					cv.find('#a0').val(data[0]);
					cv.find('#a2').val(data[1]);
					cv.find('#b0').val(clss[2]);
					cv.find('#a3, #a4, #a5, #a6, #a7, #a8, #a9, #b1, #b2, #b4, #b5').val('');
					cv.find('#tr0').attr('class', 'hide');
					cv.find('#tr0').attr('class', '');
					for(i=1;i<20;i++)
					{
						cv.find('#ck'+i).attr('checked', false);
						cv.find('#pn'+i).html('');
						cv.find('#amt'+i).html('');
						cv.find('#re'+i).html();
						cv.find('#tr'+i).attr('class', 'hide');
					}
					cv.find('#tr1').attr('class', '');
					cv.find('#pn1').html(cntnt[0]);
					cv.find('#amt1').html(data[2]);
					cv.find('#re1').html(data[3]);
					cv.find('#b4').val(k.attr('from')!=undefined?k.attr('from'):'');
					cv.find('#b5').val(k.attr('id'));
				});

			} else if($(this).attr('tag')=='disbursement')
			{
				var clss = $(this).attr('class');
				var clss = clss.split(' ');
				var cntnt = $(this).html();
				var cntnt = cntnt.split(' ');
				if(cntnt[1]=='FT')
				{
					$.post('getDisbursementCV',
						{
							reference: j[0]
						},function(data)
					{
						var b = data.split(';');
						var c = $('#cv',parent.document).contents();
						
						c.find('#cv-form').attr('action', 'insertDisbursementCV').attr('class', 'disbursement');
						c.find('#b1, #b2, #a3, #a4, #a8, #a9').val('');
						c.find('#tr0').attr('class', 'hide');
						c.find('#tr0').attr('class', '');
						c.find('#desc').html('REF NO');
						for(i=1;i<20;i++)
						{
							c.find('#pn'+i).html('');
							c.find('#amt'+i).html('');
							c.find('#re'+i).html('');
							c.find('#ck'+i).attr('checked', false);
							c.find('#tr'+i).attr('class', 'hide');
						}
						
						var rows = b[0];
						for(i=0;i<rows;i++)
						{
							var count = i + 1;
							c.find('#tr'+count).attr('class', '');
						}
						
						var d = parseFloat(rows) + 1;
						for(i=1;i<d;i++)
						{
							c.find('#pn'+i).html(b[i]);
						}
						
						var e = parseFloat(rows) + parseFloat(d);
						var f = 1;
						while(i<e)
						{
							c.find('#amt'+f).html(b[i]);
							f++;
							i++;
						}
						
						var g = parseFloat(rows) + parseFloat(e)
						var f = 1;
						while(i<g)
						{
							c.find('#re'+f).html(b[i]);
							f++;
							i++;
						}
						
						c.find('#a0').val('CASH').attr('readonly', false).css('background-color', 'FFFF99');
						var i = parseFloat(i) + 1;
						c.find('#a2').val(b[i]);
						var i = parseFloat(i) + 1;
						var z = b[i].split('|');
						c.find('#b0').val(z[0]);
						var i = parseFloat(i) + 1;
						c.find('#a11').val(b[i]);
						c.find('#preview-btn').attr('disabled', true);
					});
				}
			}
			$('.ccid').removeClass('isSelected');
			$(this).addClass('isSelected');
		});
		
		$('table.cl').filterTable({
			autofocus: 1,
			placeholder: 'Search Client'
		});
		
	});");
?></script>
<body class="nomargin">
	<table width="100%"class="cl">
	<tr><td class="trfixed">&nbsp;</td></tr><?

		$j=0;
		foreach($datas as $d)
		{
			$id='';$from='';
			$pnno = "pnno='{$d['LH_PN']}'";
			if(isset($d['id']))$id="id=\"{$d['id']}\"";
			if(isset($d['type'])):
				if(isset($d['branch'])&&$d['branch']):
					$tag = "";
					$title = "Transferred to {$d['branch']} branch";
					$branchlabel = "Transferred to {$d['branch']} branch<br>";
				elseif(isset($d['branchcode'])&&$d['branchcode']):
					$tag = "ccid tag$j";
					$title = "Transferred from {$d['frombranch']} branch";
					$branchlabel = "";
					$from = "from='{$d['branchcode']}|{$d['frombranch']}'";
				else:
					$tag = "ccid tag$j";
					$title = $d['type'].'|'.date('F d, Y', strtotime($d['LH_LoanDate']));
					$branchlabel = '';
				endif;
				echo"<tr class='data'>".
				"<td $pnno"."$id"."tag='{$d['type']}'class='$tag {$d['CI_AcctNo']}' title='$title'$from>".
					"$branchlabel{$d['LH_PN']} ".
					(strlen($d['CI_Name'])>40?substr($d['CI_Name'],0,39).'...':$d['CI_Name']).
				"</td></tr>";
				$j=$j?0:1;
			endif;
		}
		
	?>
	</table>
</body>