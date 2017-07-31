<link type="text/css" rel="stylesheet" href="<?=base_url()?>system/application/assets/css/forms.css">
<script src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script><?
	ini_set('display_errors','on');
	$this->load->helper('code');

?><script><?

echo rexjsfunction().trims("
	
	$(document).ready(function()
	{
		for(i=1;i<32;i++)
		{
			$('select[name=wdate]').append('<option>'+(i.length==1?'0'+i:i)+'</option>');
		}

		
		$('select[name=wdate]').val('<?=$wdate?>').focus();

		$('#a').click(function()
		{
			window.close();
		});

		$('#b').click(function()
		{
			if(confirm('Are you sure you want to Update this withdrawal date?'))
			{
				$('form').submit();
			}
		});
	})
");

?></script>
<style type="text/css">
	.center{text-align: center;}
	.right{text-align: right;}
</style>
<body>
<?

	if(isset($_POST['wdate']))
	{
		
		$this->load->model('rsm');
		$res = $this->rsm->cwd_submit($_POST);

		if($res)
			echo trims("
			<script>
				window.opener.document.location.reload(true);
				window.close();
			</script>");
		else echo trims("
		<script>
			alert('Error');
		</script>");
	}
?>
<form method="post"><br><br><br><br><br><input name="cid"type="hidden"value="<?=$cid?>"/>
<input name="bid"type="hidden"value="<?=$bill_ids?>"/>
<table align="center"border="0">
<tr>
	<td>Withdrawal Date: <select name="wdate"></select></td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td><input id="a"type="button"value="Cancel"/>&nbsp;
	<input id="b"type="button"value="Submit"/></td>
</tr>
</table>
</form>
</body>