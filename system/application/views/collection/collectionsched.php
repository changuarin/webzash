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

$this->connectodb();

</style>
<script>

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

		
		$('#clif').attr('src','collist/'+$('#yof').val()+'/'+(parseFloat($('#mof').val())+1));
	}

	$(document).ready(function()
	{
		$('#ldri').css('height',$('#clif').outerHeight()).css('width',$('#clif').outerWidth());

		var d = new Date();
		reload(d);		

		$('#mof').change(function()
		{
			var d = new Date();
			if($(this).val()==10)
			{
				var date = d.getDate() - 1
				var d = new Date($('#yof').val(), $(this).val(), date);
			} else if($(this).val() == 1) {
				var date = d.getDate() - 3
				var d = new Date($('#yof').val(), $(this).val(), date);
			} else if($(this).val() == 2) {
				var date = d.getDate() - 1
				var d = new Date($('#yof').val(), $(this).val(), date);
			} else if($(this).val() == 3) {
				var date = d.getDate() - 1
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
		
		$('#reloadButton').click(function(){
			$('#clif').attr('src', $('#clif').attr('src'));
		});
	});

</script>

Month Of <select id="mof"></select>&nbsp;<select id="yof"></select>&nbsp;<label class="cl"></label>&nbsp;<input id="reloadButton" type="button" name="reloadButton" value="Reload"/>
<br>
<center id="ldri">&nbsp;</center>
<iframe id="clif"width="100%"frameborder="0"src="collist"style="border:1px solid gray;"height="400"></iframe>