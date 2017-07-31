<? 
$this->load->helper('url');

	$branches = $this->db->query("
		SELECT Branch_Code AS code, Branch_Name AS name
		FROM nhgt_master.branch
		WHERE Branch_IsActive=1;
	")->result_array();
?>
<link type="text/css" rel="stylesheet" href="<?=base_url()?>system/application/assets/css/forms.css">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>system/application/assets/css/jquery.datepick.css">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>system/application/assets/css/jquery.ui.1.11.4.css">
<style>
	body {
		margin-top: 0;
	}
	
	input[type=text], select, textarea {
		font-family: 'Arial';
		font-size: 12px;
		border: 1px solid #0099CC;
		padding-left: 3px;
		padding-right: 3px;
	}
	
	select {
		width: 136px;
	}
	
	textarea {
		width: 276px;
	}
	
	.hide {
		display:none;
	}
	
	.center {
		text-align: center;
	}
	
	.date{
		cursor: default;
	}
	
	.right {
		text-align: right;
	}
	
	#menu {
		border: 0;
		position: fixed;
		right: 15;
		top: 17px;
	}
	
	#form-table, #pn-table {
		font-family: 'Arial';
		font-size: 12px;
		border-collapse: collapse;
	}
	
	#form-table tr td {
		padding: 0 2px 0 2px;
	}
	
	#pn-table tr th, #pn-table tr td {
		padding: 3px 10px 3px 10px;
	}
	
	#pn-table tr th {
		border: 1px solid #CCC;
		text-align: left;
	}
	
	#pn-table tr td {
		border: 1px solid #EEE;
		background-color: #99CCFF;
	}
	
	/* branch transfer styling */
	.focus {
		background-color: rgba(255, 252, 0, 0.32);
	}
	
	.trans { 
		width:100px;
		color: grey;
		background-color:#7EDFFF;
		padding: 6px 5px 7px 5px;
		border-bottom-left-radius: 5px;
		border-bottom-right-radius: 5px;
		border-top-left-radius: 5px;
		border-top-right-radius: 5px;
	}
	
	#sbt {
		width: 120px;
	}
</style>
<script type="text/javascript"src="<?=base_url()?>system/application/assets/js/jquery.1.10.2.js"></script>
<script type="text/javascript"src="<?=base_url()?>system/application/assets/js/format.js"></script>
<script type="text/javascript"src="<?=base_url()?>system/application/assets/js/functions.js"></script>
<script type="text/javascript"src="<?=base_url()?>system/application/assets/js/jquery.filterTable.js"></script>
<script type="text/javascript"src="<?=base_url()?>system/application/assets/js/jquery.ui.1.11.4.js"></script>
<script type="text/javascript"src="<?=base_url()?>system/application/assets/js/numeral.js"></script>
<script type="text/javascript"><?

$this->load->helper('code');

echo trims("
	$(document).ready(function()
	{
		"/* RSMATIC FUNCTIONS for Disbursement */."
		var pl_=[$payee];
		function reselect(z, y, x)
		{
			if(z.val()=='')z.addClass('focus');
			z.attr('readonly',false);
			z.blur(function()
			{
				$(this).removeClass('focus');
			});
			if(x!=undefined)z.bind('blur',x);

			if(y=='focus')z.focus();
			if(y=='readonly')z.removeClass('focus').attr('readonly',true).unbind('blur');
		}

		$('#disburse-btn').click(function()
		{
			if($(this).val().substr(0,5)=='New D')
			{
				$('#a0').autocomplete(
				{
					source:pl_,
					select:function(event,ui)
					{
						$('#a0').val(ui.item.value);
					}
				}).val('');
				$('#a3').val('');
				$('#a4').val(0).click(function()
				{
					$('#a9').val(toWords(N($('#a4').val())));
				});
				$('#a5').val('');

				$('#a6').unbind('change').change(function()
				{
					if($('#a4').val()!=0)
					{
						var c=$(this).val();
						var a=$('#den').contents().find('#tden');
						a.find('.cash').remove();
						var f=a.find('.datas').length;
						
						var d=$('.lf',parent.document).contents().find('.cl').find('.data');

						for(g=0;g<d.length;g++)
						{
							if($(d[g]).find('td').attr('id') == c)
							{
								var b=$(d[g]).find('td');
								if(b.attr('title')=='')
									var e=b.html().replace(b.attr('id')+' ','');
								else var e=b.attr('title').replace(b.attr('id')+' ','');
							}
						}

						if(e==undefined&&c!='')
							alert('Account Title not found please inform your System Administrator.');
						else
						{
							var f=a.find('.datas').length+1;
							a.append('<tr class=\'datas cash\'id=\'den'+f+'\'><td>'+b.attr('id')+'</td><td>'+e+'</td>'+
							'<td align=\'right\'>-</td>'+
							'<td align=\'right\'>'+$('#a4').val()+'</td>'+
							'<td align=\'right\'><input id=\'rdat'+f+'\'type=\'button\'value=\'Remove\'/></td>'+
							'</tr>');
							a.find('#rdat'+f).click(function()
							{
								var m=$(this).parent().parent();
								m.hide('slow',function()
								{
									$('#a5').val('');
									$('#a6').val('');
									$('#a7').val('');
									m.remove();
									return false;
								});
							});
						}
					}else
					{
						$('#a6').val('');
						$('#a7').val('');
						$('#a5').val('');
					}
					
				}).val('');
				$('#a7').val('');
				$('#a8').val('');
				$('#a9').val('');
				$(this).val('Cancel');
				reselect($('#a0'), undefined, function()
				{
					$(this).val($(this).val().toUpperCase());
				});

				reselect($('#a1'));
				reselect($('#a3'));

				reselect($('#a5'));
				reselect($('#a8'), undefined, function()
				{
					$(this).val($(this).val().toUpperCase());
				});

				$('#b3').val('disburse');

				$('#preview-btn').attr('disabled', false);
				$('#cl',parent.document).attr('src','atlist');
				$('#cv-form').append('<iframe id=\'den\'width=\'70%\''+
				'style=\'height:120px;border:1px solid black\'src=\'\'></iframe>');
				$('#den').attr('src','disentry');
				$('#loanTab').find('#pn-table').find('tr').addClass('hide');
				$('#cv-form').attr('action','insertDisburse');
				$('#a4').focus();
				$('#submit-btn').unbind('click').click(function()
				{
					$('#cv-form').append('<input type=\"hidden\"id=\"entries\"name=\"entries\"/>');

					var tc=[];
					
					var tr=$('#den').contents().find('#tden').find('.datas').each(function()
					{	
						var tb=$(this).find('td'),ta={};
						ta.code=$(tb[0]).html();
						ta.title=$(tb[1]).html();
						ta.debit=$(tb[2]).html();
						ta.credit=$(tb[3]).html();
						tc.push(ta);
					});

					$('#entries').val( JSON.stringify(tc) );

					$('#cv-form').submit();
				});
			}
			else
			{	
				reselect($('#a0'),'readonly');
				reselect($('#a1'),'readonly');
				$('#a1').attr('readonly',false);
				reselect($('#a3'),'readonly');
				$('#a3').attr('readonly',false);
				reselect($('#a4'),'readonly');
				reselect($('#a5'),'readonly');
				reselect($('#a8'),'readonly');
				$('#a0').val('');
				$('#a3').val('');
				$('#a4').val(0);
				$('#a5').val('');
				$('#a6').val('').unbind('change');
				$('#a7').val('');
				$('#a8').val('');
				$('#a9').val('');
				$('#b3').val('');
				scriptreset();
				$(this).val('New Disbursement');
				$('#preview-btn').attr('disabled', true);
				$('#cl',parent.document).attr('src','cvlist');
				$('#den').hide('slow',function(){\$(this).remove()});
				$('#cv-form').attr('action','');
			}
		});

		"/* KENNETH FUNCTIONS */.
		"
		function scriptreset()
		{
			$('#a2').unbind('datepicker').datepicker({dateFormat: 'yy-mm-dd'});

			$('#preview-btn, #rpd-btn, #rplc-btn, #cancel-btn').attr('disabled', true);
			
			$('#a1').unbind('keypress').keypress(function(e){
				if(e.which == 13) {
					$('#a3').select();
				}
			});

			$('#a3').unbind('keypress').keypress(function(e){
				if(e.which == 13) {
					$('#a5').focus();
				}
			});

			$('#a5').unbind('keypress').keypress(function(e){
				if(e.which == 13) {
					$('#preview-btn').focus();
				}
			});
			
			$('#preview-btn').unbind('click').click(function()
			{
				var a=$('#a0').val(),
				b=$('#a1').val(),
				c=$('#a2').val(),
				d=$('#a3').val(),
				e=$('#a4').val(),
				f=$('#a5').val(),
				g=$('#a8').val(),
				h=$('#b2').val(),
				j=$('#a4').val(),
				i=$('#b3').val(),
				k=$('#b4').val();
				
				var width = 960;
				var height = 560;
				var top = (screen.height/2)-(height/2);
				var left = (screen.width/2)-(width/2);
				
				if (d == ''||f == '') {
					alert('Cannot proceed. Check No. and/or Bank Code is blank.');
				} else {
					var url='cvsales/'+a+'/'+b+'/'+c+'/'+d+'/'+e+'/'+f+'/'+g+'/'+h+'/'+j;
					if(i=='disburse') url+='/dis';
					if(g=='NET PROCEEDS OF LOAN'||g.indexOf('EXCESS')!==-1||g.indexOf('REFUND')!==-1)
					{
						var a = $('#b1').val().split(';');
						url+='/'+$('#b0').val()+'/'+a[0];
					};

					if(k!='')
					{
						url+='/transfer/'+k;
					}

					window.open(url,'popupWindow',
					'width='+width+',height='+height+',top='+top+',left='+left+',scrollbars=no').focus();
				}
			});

			$('#submit-btn').unbind('click').click(function()
			{
				$('#cv-form').submit();
			});
			
			$('.ckbox').unbind('click').click(function()
			{
				var zz = $(this).parent().parent().html();
				
				$('#a8').html('');
				$('#a4').val('');
				$('#b1').val('');
				var amt = 0;
				var pn = '';
				var re = '';
				var desc = '';
				bgval = '#FFF';
				borderval = '1px solid #09C';
				for(i=1;i<30;i++)
				{
					var a = $('#ck'+i).is(':checked');
					
					if(a==true)
					{
						var b = $('#amt'+i).html();
						var amt = parseFloat(amt) + parseFloat(b);
						
						var amtnum = numeral(b).format('0a');
						amtnum = amtnum.toUpperCase();
						
						var d = $('#re'+i).html() + ';';
						var re = re + d;
						var clss = $('#cv-form').attr('class');
						if(clss=='sales')
						{
							if(desc.indexOf('NET PROCEEDS OF LOAN')=== -1)
							{
								desc += 'NET PROCEEDS OF LOAN';
							}
														
							if(zz.indexOf('ADVANCE SSS')!== -1) {
								desc = 'ADVANCE SSS INCREASE';
							}
						} else if(clss=='refund') {
							desc += $('#re1').html();
						} else if(clss=='disbursement') {
							var rem = $('#re'+i).html();
							var z = $('#a8').val();
							rem = rem.replace('FT TO', '');
							if(desc=='')
							{
								rem = 'FT TO ' + rem + ' ' + amtnum;
							} else {
								rem = ', ' + rem + ' ' + amtnum;
							}
							desc += rem;
						}
						var c = $('#pn'+i).html() + ';';
						var pn = pn + c;
						$('#b1').val(pn);
						
						$('#a1').select();
						
						bgval = '#FF9';
						borderval = '1px solid #FF0000';
						pbc = 'false';
					}
					$('#a4').val(amt);
					$('#a8').val(desc);
					$('#b1').val(pn);
					$('#b2').val(re);
					$('#a0, #a1, #a3, #a5').css('background-color', bgval).css('border', borderval);
					$('#a9').val(toWords(N($('#a4').val())));
					if($('#a4').val()!=0||$('#a8').val()!='')
					{
						$('#preview-btn, #rpd-btn, #rplc-btn, #cancel-btn').attr('disabled', false);
					} else if(($('#a4').val()<=0||$('#a8').val()=='')) {
						$('#preview-btn, #rpd-btn, #rplc-btn, #cancel-btn').attr('disabled', true);
					}
				}
			});
			
			$('#a5').unbind('change').change(function(){
				var a = $('#a5').val();
				var b = a.split(';');
				$('#a6').val(b[0]).change();
				$('#a7').val(b[2]);
			});
			
			$('#rplc-btn').unbind('click').click(function(){
				var width = 840;
				var height = 540;
				var top = (screen.height/2)-(height/2);
				var left = (screen.width/2)-(width/2);
				var a = $('#b1').val();
				var b = a.split(';');
				var pn = b[0];
				window.open('loanComputation/0/'+$('#b0').val()+'/'+pn,'popupWindow','width='+width+',height='+height+',top='+top+',left='+left+',scrollbars=no').focus();
			});

			$('#rpd-btn').unbind('click').click(function(){
				var width = 840;
				var height = 540;
				var top = (screen.height/2)-(height/2);
				var left = (screen.width/2)-(width/2);
				var a = $('#b1').val();
				var b = a.split(';');
				window.open('documents/'+$('#b0').val()+'/'+b[0],'popupWindow','width='+width+',height='+height+',top='+top+',left='+left+',scrollbars=yes').focus();
			});
			
			$('#cancel-btn').unbind('click').click(function(){
				var a = $('#b1').val();
				var pn = a.replace(';', '');
				window.location.href='cancelLoan/'+$('#b0').val()+'/'+pn;
			});
		
			$('#submit-btn').unbind('click').click(function(){
				$('#cv-form').submit();
			});
		}

		scriptreset();

	});");
?></script>
<?php

$i = 0;
foreach($datas as $d)
{
	$a[$i] = $d['value'];
	$i++;
}
$c = explode(';', $a[2]);
$d = explode(';', $a[1]);
$e = explode(';', $a[0]);
?>
<body>
	<div id="menu">
		<table>
			<tbody>
				<tr>
					<td>
						<span class="trans">Transfer to: <select id="sbt"disabled><option value=''>{ Select Branch }</option><?

						foreach ($branches as $branch):
							
							$name = strtolower(str_replace(' ', '', $branch['name']));
							if($name!=$current_branch)
							echo"<option value='$name'>{$branch['name']}</option>";

						endforeach;

						?></select><input id="sbmt"type="button"value="Submit"disabled/></span>&nbsp;
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	
	<h3 class="tabHeadings"tag="loanTab">Check Voucher</h3>
		<div id="loanTab">
			<form id="cv-form" method="post">
				<table id="form-table" border="0">
					<tr>
						<td>Paid To</td>
						<td colspan="3">
							<input id="a0"name="a0"type="text"readonly/>
							<input id="b0"name="b0"type="hidden"/>
							<input id="b1"name="b1"type="hidden"/>
							<input id="b2"name="b2"type="hidden"/>
							<input id="b3"type="hidden"/><?
								// this is for branch transfer parameter
							?><input id="b4"name="b4"type="hidden"/>
							<input id="b5"name="b5"type="hidden"/>
						</td>
					</tr>
					<tr>
						<td>Voucher No.</td>
						<td>
							<input id="a1" name="a1" type="text" value="<?=$cvno?>">
						</td>
						<td class="right">Date</td>
						<td>
							<input class="center date" id="a2" name="a2" type="text" value="<?=date('Y-m-d')?>">
						</td>
					</tr>
					<tr>
						<td>Check No.</td>
						<td colspan="3">
							<input id="a3" name="a3" type="text">
						</td>
					</tr>
					<tr>
						<td>Amount</td>
						<td colspan="3">
							<input id="a4" name="a4" type="text"readonly>
						</td>
					</tr>
					<tr>
						<td>Bank</td>
						<td>
							<select id="a5" name="a5">
							<?php
							
							echo '<option value="" selected>&nbsp;</option>';
							$i = 0;
							foreach($banks as $banks)
							{
								$b = explode(';', $banks['value']); 
								echo '<option value="'.$banks['value'].'">'.$b[1].'</option>';
								$i++;
							}
							
							?>
							</select>
						</td>
						<td>
							<input id="a6" name="a6" type="text"readonly/>
						</td>
						<td>
							<input id="a7" name="a7" type="text"readonly/>
						</td>
						<tr>
							<td>Description</td>
							<td colspan="3">
								<textarea id="a8" name="a8" cols="48" rows="3"readonly></textarea>
							</td>
						</tr>
						<tr>
							<td>Amount in Words</td>
							<td colspan="3">
								<textarea id="a9" name="a9" cols="48" rows="3"readonly>
								</textarea>
							</td>
						</tr>
						<tr>
							<td>Prepared By</td>
							<td colspan="3">
								<input id="a10" name="a10" type="text" value="<?=strtoupper($prepby)?>">
							</td>
						</tr>
						<tr>
							<td>Checked By</td>
							<td colspan="3">
								<input id="a11" name="a11" type="text" value="<?=$d[1]?>">
							</td>
						</tr>
						<tr>
							<td>Approved By</td>
							<td colspan="3">
								<input id="a12" name="a12" type="text" value="<?=$e[1]?>">
							</td>
						</tr>
						<tr>
							<td colspan="2">
								&nbsp;
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="button" value="New Disbursement" id="disburse-btn">
								<input type="button" value="Computation" id="rplc-btn"disabled/>
								<input type="button" value="Documents" id="rpd-btn"disabled/>
								<input type="button" value="Preview" id="preview-btn"disabled/>
								<input type="button" value="Cancel" id="cancel-btn"disabled/>
								<input id="submit-btn" class="hide" type="button" value="Submit"disabled/>
							</td>
						</tr>
					</tr>
				</table>
			</form>
			<div id="pn-list">
				<table id="pn-table" style="margin:14px 0 0 10px;">
					<tbody>
						<tr id="tr0" class="hide" style="background-color: #777;color: #FFF;">
							<th width="24"></th>
							<th width="150">PN</th>
							<th style="text-align:right;" width="100">AMOUNT</th>
							<th width="300">REMARKS</th>
						</tr>
						<?php
						
						for($i=1;$i<30;$i++)
						{
							echo '
							<tr class="hide" id="tr'.$i.'">
								<td style="padding-left: 0;text-align: center;">
									<input type="checkbox" id="ck'.$i.'" class="ckbox">
								</td>
								<td id="pn'.$i.'">
									&nbsp;
								</td>
								<td class="right" id="amt'.$i.'">
									&nbsp;
								</td>
								<td id="re'.$i.'">
									&nbsp;
								</td>
							</tr>
							';
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</body>
</html>