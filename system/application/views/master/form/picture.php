<style type="text/css">
	#main {
		margin: 100px auto;
		width: 200px;
	}
</style>
<body>
	<div id="main">
		<form method="post" action="upload_picture" enctype="multipart/form-data">
			<div>
				<input id="file_to_upload" type="file" name="file_to_upload" />
			</div>
			<div>
				<input type="submit" name="submit-btn" value="Upload" />
				<input id="cancel-btn" type="button" name="cancel-btn" value="Cancel" />
			</div>
		</form>
	</div>
</body>
<script src="<?= base_url() ?>system/application/assets/js/jquery-2.1.3.min.js" type="text/javascript"></script>
<script type="text/javascript">
	var main = function() {
		$('#cancel-btn').click(function() {
			window.close();
		});
	}
	
	$(document).ready(main);
</script>