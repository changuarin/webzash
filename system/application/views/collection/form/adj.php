<link type="text/css" rel="stylesheet" href="<?=base_url()?>system/application/assets/css/forms.css">
<link type="text/css" rel="stylesheet" href="<?=base_url()?>system/application/assets/css/jquery.datepick.css">
<script type="text/javascript"src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script>
<script type="text/javascript"src="<?=base_url()?>system/application/assets/js/jquery.datepick.js"></script><?
	
	ini_set('display_errors','yes');
	$this->load->helper('code');

?><script><?

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
						if(confirm('Are you sure you want to update this transaction?'))doit();	
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
		
		$('#imgdata').click(function() {
			var height = 880;
			var width = 960;
			var top = (screen.height/2) - (height/2);
			var left = (screen.width/2) - (width/2);
			window.open('../receipt_preview/' + $('#uid').val() + '/' + $('#traceno').val() + '/' + $('#begbal').val() + '/' + $('#amtdrawn').val() + '/' + $('#endbal').val() + '/' + $('#rfw').val(), 'receiptpreview', 'width=' + width + ', height=' + height + ', top=' + top + ', left=' + left + ',scrollbars=yes').focus();
		});
		
		$('#receiptbtn').click(function() {
			var height = 480;
			var width = 680;
			var top = (screen.height/2) - (height/2);
			var left = (screen.width/2) - (width/2);
			window.open('../receipt_image_form', 'receiptform', 'width=' + width + ', height=' + height + ', top=' + top + ', left=' + left + ',scrollbars=yes').focus();
		});
		
		$('#clearbtn').click(function() {
			$('#imgdata').attr('src', '" . base_url() . "system/application/assets/images/no_photo.jpg');
			$('#receiptimg').val('');
		});
		
		$('#closebtn').click(function() { window.close(); })
		
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

	.hasDatepick{
		text-align: center;
	}

	.center {
		text-align: center;
	}
	
	.right {
		text-align: right;
	}
	
	.headr {
		font-weight: bold;font-size: 18px;
	}
	
	body {
		padding-top: 10px;
	}
</style>
<body>
<?

if(isset($_POST['ptype'])):

	//print_r($_POST);die();		
		$this->load->model('database');
		$res = $this->database->collection_update($_POST);

		if($res['isOkay'])
			echo trims("
			<script>				
				{$res['script']}
			</script>");
		else echo trims("
		<script>
			alert('Error');
		</script>");

endif;
	
if(!empty($datas)):

	echo '
	<form method="post">
		<table align="center"border="0">
			<tr>
				<td colspan="2" class="center"><label class="headr">Collection Adjustment Form</label></td>
			</tr>
			<tr>
				<td colspan="2"align="center">
					<input type="hidden" name="billref" value="' . $datas->bill_id . '"/>
					<input id="uid" type="hidden" name="uid" value="' . $datas->uid . '"/>
					<input type="hidden" name="cid" value="' . $datas->cid . '"/>
				</td>
			</tr>
			<tr>
				<td align="right">Name:</td>
				<td>' . $ciname->name . '</td>
				<td rowspan="4" align="center">
					<img id="imgdata" src="' . (!empty($datas->receipt_image) ? 'data:image/jpeg; base64, ' . $datas->receipt_image : base_url() . 'system/application/assets/images/no_photo.jpg') . '" height="120" width="120" />
				</td>
			</tr>
			<tr>
				<td align="right">TransType:</td>
				<td>
					<select name="ptype">
						<option value="TR" ' . ($datas->paytype == 'TR' ? 'selected' : '') . '>Payment</option>
						<option value="RR" ' . ($datas->paytype == 'RR' ? 'selected' : '') . '>Remittance</option>
						<option value="RB" ' . ($datas->paytype == 'RB' ? 'selected' : '') . '>Bonus</option>
					</select>
				</td>
			</tr>
			<tr>
				<td align="right">Trace Ref. No:</td>
				<td><input id="traceno" type="text" name="traceno" value="' . $datas->tracerefno . '"/></td>
			</tr>
			<tr>
				<td align="right">ATM Beginning Balance:</td>
				<td><input id="begbal" type="text" name="begbal" class="right" value="' . $datas->atmbegbal . '"/></td>
			</tr>
			<tr>
				<td align="right">Amount to Drawn:</td>
				<td><input id="amtdrawn" type="text" name="amtdrawn" class="right" value="' . $datas->amtdrawn . '"/></td>
			</tr>
			<tr>
				<td align="right">ATM Ending Balance:</td>
				<td><input id="endbal" type="text" name="endbal" class="right" value="' . $datas->atmendbal . '"readonly/></td>
			</tr>
			<tr>
				<td align="right">Directly Paid:</td>
				<td><input id="rfw" type="text" name="rfw" value="' . $datas->directpaid . '"/></td>
			</tr>
			<tr valign="center">
				<td align="right" style="padding-top:15px;">OR/PR Type</td>
				<td style="padding-top:15px;"><select name="orprtype">
					<option value="" ' . ($datas->orprtype == '' ? 'selected' : '') . '></option>
					<option value="OR" ' . ($datas->orprtype == 'OR' ? 'selected' : '') . '>OR</option>
					<option value="PR" ' . ($datas->orprtype == 'PR' ? 'selected' : '') . '>PR</option>
				</select></td>
			</tr>
			<tr>
				<td align="right">OR/PR No.</td>
				<td><input type="text"name="orprno"value="' . $datas->orprno . '"class="center"/></td>
			</tr>
			<tr>
				<td align="right">Due Date:</td>
				<td><input type="text"name="duedate"value="' . date('m/d/Y', strtotime($datas->duedate)) . '"class="center"/></td>
			</tr>
			<tr valign="center">
				<td align="right" style="padding-top:15px;">Post to:</td>
				<td style="padding-top:15px;"><select name="bank">' . $banks . '</select><small id="defbank"><i>&nbsp;Default Bank</i></small></td>
			</tr>
			<tr>
				<td colspan="2" align="center" style="padding-top:30px;">
					<input id="receiptimg" type="hidden" name="receiptimg" value="' . $datas->receipt_image . '" />
					<input type="button"value="Update"class="submit" />
					<input id="receiptbtn" type="button" value="Receipt" />
					<input id="clearbtn" type="button" value="Clear" />
					<input id="closebtn"type="button"value="Close" />
				</td>
			</tr>
		</table>
	</form>
	';
	

endif;

?>
<body>