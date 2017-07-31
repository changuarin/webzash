<link type="text/css" rel="stylesheet" href="<?=base_url()?>system/application/assets/css/forms.css">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>system/application/assets/css/jquery.datepick.css">
<script type="text/javascript" src="<?= base_url() ?>system/application/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="<?= base_url() ?>system/application/assets/js/jquery.datepick.js"></script>
<?php
	
	ini_set('display_errors','yes');
	$this->load->helper('code');

?>
<script><?php

echo trims("
	function N(a){a=String(a).replace(/,/g,'');return Number(a);}
	
	function Format(amount)
	{
		amount=String(amount).replace(/,/g,'');
		amount=String(Number(amount).toFixed(2));
		var delimiter=',';
		var a=amount.split('.',2);
		var d=a[1];
		var i=parseInt(a[0]);
		if(isNaN(i))
		{ return '';}
		var minus='';
		if(i<0)
		{
			minus='-';
		}
		i=Math.abs(i);
		var n=new String(i);
		var a=[];
		while(n.length>3)
		{
			var nn=n.substr(n.length-3);
			a.unshift(nn);
			n=n.substr(0,n.length-3);
		}
		if(n.length>0)
		{
			a.unshift(n);
		}
		n=a.join(delimiter);
		if(d.length<1)
		{
			amount=n;
		}else{
			amount=n+'.'+d;
		}
		amount=minus+amount;
		return amount;
	}

	function keye(z)
	{
		z.a.keyup(function(event)
		{
			if(event.which==13)
			{
				if(z.b.val()!=undefined) z.b.select().focus();
			}
		});
	}
	
	$(document).ready(function()
	{
		$('#resetbtn').click(function() {
			$('#imgdata').attr('src', '" . base_url() . "system/application/assets/images/no_photo.jpg');
		});
		
		var o=[
			$('select[name=ptype]'),
			$('input[name=traceno]'),
			$('input[name=begbal]'),
			$('input[name=amtdrawn]'),
			$('input[name=endbal]'),
			$('input[name=rfw]'),
			$('input[name=duedate]'),
			$('.submit')
		];

		keye({
			a:o[0],
			b:o[1]
		});

		keye({
			a:o[1],
			b:o[2]
		});

		keye({
			a:o[2],
			b:o[3]
		});

		keye({
			a:o[3],
			b:o[6]
		});

		/*keye({
			a:o[6],
			b:o[7]
		});*/
		
		$('input[name=duedate]').keypress(function(e){
			if(e.which == 13) {
				var a = $(this).val();
				if(a.indexOf('/') === -1)
				{
					var b = [a.slice(0, 2), '/', a.slice(2)].join('');
					var c = [b.slice(0, 5), '/', b.slice(5)].join('');
					$(this).val(c);
				}
				$('.submit').select().focus();
			}
		});

		function isok(a,b)
		{
			if(a.val()=='')
			{
				a.select().focus();
				return false;
			}else return true;	
		}

		$(o[2]).change(function()
		{
			if($(o[2]).val())
			{
				t=N($(o[2]).val())-N($(o[3]).val());
				$(o[4]).val(Format(t,2));
				$(this).val(Format(N($(this).val()),2))
			}else $(o[4]).val('');
		});

		$(o[3]).change(function()
		{
			$(o[2]).change();
			$(o[3]).val(Format($(o[3]).val(),2));
		});

		function doit()
		{
			$('form').submit();
		}

		$('.submit').click(function()
		{
			if(isok($(o[0]),'Please select Payment type'))
			{
				if(isok($(o[1]),'Please input trace reference no'))
				{
					if(isok($(o[2]),'Please input amout to drawn'))
					{
						if(confirm('Are you sure you want to submit this transaction?'))doit();	
					}
				}
			}
		});

		$(o[7]).change(function()
		{
			if($(this).val())
			{
				$(o[8]).attr('disabled',false).focus();
			}else{
				$(o[8]).attr('disabled',true);
			}
		});

		$(o[0]).focus();

		$('select[name=bank]').change(function()
		{
			if($(this).attr('title')==$(this).val())
				$('#defbnk').animate({
					opacity:100
				},500);
			else 
				$('#defbnk').animate({
					opacity:0
				},500);
		}).attr('title',$('select[name=bank]').val());
		
		$('#receiptbtn').click(function() {
			var height = 480;
			var width = 680;
			var top = (screen.height/2) - (height/2);
			var left = (screen.width/2) - (width/2);
			window.open('../../../../receipt_image_form', 'receiptform', 'width=' + width + ', height=' + height + ', top=' + top + ', left=' + left + ',scrollbars=yes').focus();
		});
		
		/*$('input[name=duedate]').datepick({dateFormat: 'M d yyyy'});*/
	})
");

?></script>
<style type="text/css">
	
	input[type=text], select
	{
		background-color: white;
		border:1px solid #0099CC;
		padding:3px 3px 3px 3px;
	}

	input[type=text]:focus
	{
		background-color: rgb(255, 255, 153);
		border:1px solid rgb(255, 0, 0);
	}

	select:focus
	{
		background-color: rgb(255, 255, 153);
		border:1px solid rgb(255, 0, 0);
	}
	
	body {
		padding-top: 10px;
	}

	.hasDatepick {
		text-align: center;
	}

	.center {
		text-align: center;
	}
	
	.right {
		text-align: right;
	}
</style>
<body>
<?php

	if(isset($_POST['ptype']))
	{
		//print_r($_POST);die();		
		$this->load->model('rsm');
		$res = $this->rsm->collection_submit($_POST);

		if($res['isOkay'])
			echo trims("
			<script>				
				{$res['script']}
			</script>");
		else echo trims("
		<script>
			alert('Error');
		</script>");
	}
?>
	<form method="post">
		<table align="center"border="0">
			<tr>
				<td colspan="2"align="center">
					<?= $client->paytype ?> - <?= $client->pentype ?>
					<input type="hidden" name="billref" value="<?= $bill_ids ?>"/>
					<input type="hidden" name="cid" value="<?= $this->uri->segment(3, '') ?>"/>
				</td>
			</tr>
			<tr>
				<td align="right">Name:</td>
				<td><?=$client->name?></td>
				<td rowspan="4" align="center">
					<img id="imgdata" src="<?= base_url() ?>system/application/assets/images/no_photo.jpg" height="120" width="120" />
				</td>
			</tr>
			<tr>
				<td align="right">TransType:</td>
				<td>
					<select name="ptype">
						<option value="">- Select here -</option>
						<option value="SI">SSS Increase</option>
						<option value="TR">Payment</option>
						<!--<option value="CP">Cash Payment</option>
						<option value="RR">Remittance</option>
						<option value="DC">Still Due to Client</option>
						<option value="RB">Bonus</option>-->
					</select>
				</td>
			</tr>
			<tr>
				<td align="right">Trace Ref. No:</td>
				<td><input type="text"name="traceno"/></td>
			</tr>
			<tr>
				<td align="right">ATM Beginning Balance:</td>
				<td><input type="text"name="begbal"class="right"/></td>
			</tr>
			<tr>
				<td align="right">Amount to Drawn:</td>
				<td><input type="text"name="amtdrawn"value="<?= number_format($atmpension, 2) ?>"class="right"/></td>
			</tr>
			<tr>
				<td align="right">ATM Ending Balance:</td>
				<td><input type="text"name="endbal"class="right"readonly/></td>
			</tr>
			<tr>
				<td align="right">Directly Paid:</td>
				<td><input type="text"name="rfw"/></td>
			</tr>
			<tr>
				<td align="right">Due Date:</td>
				<td><input type="text"name="duedate"value="<?=date('m/d/Y')?>"class="center"/></td>
			</tr>
			<tr valign="center">
				<td align="right"style="padding-top:15px;">Post to:</td>
				<td style="padding-top:15px;"><select name="bank"><?= $banks ?></select><small id='defbnk'><i>&nbsp;Default Bank</i></small></td>
			</tr>
			<tr>
				<td colspan="2"align="center"style="padding-top:30px;">
					<input id="receiptimg" type="hidden" name="receiptimg" />
					<input type="button" value="Submit" class="submit" />
					<input id="resetbtn" type="reset" value="Reset" />
					<input id="receiptbtn" type="button" value="Receipt" />
				</td>
			</tr>
		</table>
	</form>
</body>