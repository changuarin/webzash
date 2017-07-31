<style type="text/css">
	input {
		border: 1px solid #0099cc;
	}
</style>
<body>
	<div>
		Month of&nbsp;
		<select id="mof"></select>&nbsp;
		<select id="yof"></select>&nbsp;
		Client Type&nbsp;
		<select id="ci_type">
			<option value="PEN">CLIENT-GSIS/SSS</option>
			<option value="SAL">CLIENT-SALARY</option>
		</select>&nbsp;
		<select id="coll_type">
			<option value="1">COLLECTION</option>
			<option value="2">ADJ/DUE/PAY/REM</option>
		</select>
		<input id="go-btn" type="button" value="Go" />
		<input id="reload-btn" type="button" value="Reload" />&nbsp;&nbsp;
		<input id="uncoll-btn" type="button" value="Uncollected" />
	</div>
	<div>
		<iframe id="monthly_billing_list" frameborder="0" height="400px" width="100%"></iframe>
	</div>
</body>

<script type="text/javascript">
	function reload(d)
	{

		var m=['January','February','March','April','May','June','July','August','September','October','November','December'];
		var y=2000;
		
		var lastDayOfMonth = new Date(d.getFullYear(), d.getMonth()+1, 0);

		$('#ldri').show();
		
		$('#clif').attr('src','collist');

		$('#mof').html('');
		for(a in m)
		{
			$('#mof').append('<option value="'+a+'"'+(d.getMonth()==a?' selected':'')+'>'+m[a]+'</option>');
		}

		$('#yof').html('');
		for(i=y;i<=(d.getFullYear()+1);i++)
		{
			$('#yof').append('<option'+(d.getFullYear()==i?' selected':'')+'>'+i+'</option>');	
		}
		
		$('#monthly_billing_list').attr('src', 'monthly_billing_list/' + $('#yof').val() + '/' + (parseFloat($('#mof').val()) + 1) + '/' + $('#ci_type').val() + '/' + $('#coll_type').val());
	}
	
	var main = function(){
		var d = new Date();
		reload(d);		

		$('#mof').change(function()
		{
			var d = new Date();
			if($(this).val() == 10)
			{
				var date = d.getDate() + 1
				var d = new Date($('#yof').val(), $(this).val(), date);
			} else {
				var d = new Date($('#yof').val(), $(this).val(), d.getDate());
			}
			reload(d);
		});

		$('#yof').change(function()
		{
			var d = new Date();
			var d = new Date($(this).val(), $('#mof').val(), d.getDate());
			reload(d);
		});
		
		$('#ci_type').change(function()
		{
			var d = new Date();
			var d = new Date($('#yof').val(), $('#mof').val(), d.getDate());
			reload(d);
		});
		
		var h = $(document).find('body').height() / 1.35;
		
		$('#go-btn').click(function(){
			var a = $('#yof').val();
			var b = parseInt($('#mof').val()) + 1;
			var c = $('#ci_type').val();
			var d = $('#coll_type').val();
			$('#monthly_billing_list').attr('src', 'monthly_billing_list/' + a + '/' + b + '/' + c + '/' + d).attr('height', h);
		});
		
		/*$('#reload-btn').click(function(){
			$('#monthly_billing_list').attr('src', $('#monthly_billing_list').attr('src'));
		});*/
	};
	
	$(document).ready(main);
</script>