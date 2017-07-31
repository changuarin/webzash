<style type="text/css">
	input {
		border: 1px solid #0099cc;
	}
	
	input[type=text] {
		text-align: center;
	}
</style>
<body>
	<div>
		From:
		<input id="from_date" type="text" name="from_date" value="<?= date('Y-m-d')?>" />
		To:
		<input id="to_date" type="text" name="to_date" value="<?= date('Y-m-d')?>" />
		&nbsp;
		Client Group:
		<select id="group">
			<option value="0" selected="selected">ALL</option>
			<option value="1">NEW</option>
			<option value="2">OLD</option>
		</select>
		<input id="go-btn" type="button" name="go-btn" value="Go" />
		&nbsp;
		<input id="reload-btn" type="button" name="reload-btn" value="Reload" />
	</div>
	
	<div>
		<iframe id="rpcf_list" frameborder="0" height="400px" width="100%"></iframe>
	</div>
	
	<script src="<?= base_url() ?>system/application/assets/js/jquery-2.1.3.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.filterTable.js"></script>
	<script type="text/javascript">
		var main = function() {
			$('#reload-btn').click(function() {
				$('#rpcf_list').attr('src', $('#rpcf_list').attr('src'));
			});
			
			var h = $(document).find('body').height() / 1.35;
			$('#rpcf_list').attr('src', 'rpcf_list/' + $('#from_date').val() + '/' + $('#to_date').val() + '/' + $('#group').val()).attr('height', h);
			
			$('#go-btn').click(function() {
				$('#rpcf_list').attr('src', 'rpcf_list/' + $('#from_date').val() + '/' + $('#to_date').val() + '/' + $('#group').val()).attr('height', h);
			});
			
			$('#reloadButton').click(function() {
				$('#ca').attr('src', $('#ca').attr('src'));
			});
		};
		
		$(document).ready(main);
	</script>
</body>