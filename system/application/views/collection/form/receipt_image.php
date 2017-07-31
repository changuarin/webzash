<!DOCTYPE HTML>
<html>
	<head>
		<title>Receipt Image Form</title>
		<style type="text/css">
			body {
				font-family: Helvetica, sans-serif;
			}
			
			h2, h3 {
				margin-top: 0;
			}
			
			.container {
				margin: 0 auto;
				padding: 0 10px;
				width: 960px;
			}
			
			.main {
				text-align: center;
			}
			
			.col {
				float: left;
				width: 320px;
			}
			
			.clearfix {
				clear: both;
			}
			
			#output {
				border: 1px solid;
				background: #ccc;
				height: 240px;
			}
		</style>
	</head>
	
	<body>
		<div class="container">
			<h2>Receipt Form</h2>
		</div>
		
		<div class="container">
			<div class="main">
				<div class="col" id="camera"></div>
				<div class="col" id="output"></div>
				<div class="clearfix"></div>
			</div>
		</div>
		
		<script language="JavaScript">
			Webcam.set({
				width: 320,
				height: 240,
				image_format: 'jpeg',
				jpeg_quality: 90
			});
			Webcam.attach('#camera');
		</script>
		
		<div class="container">
			<form>
				<input type=button value="Capture" onClick="capture()">
				<input id="attachbtn" type=button value="Attach">
				<input id="closebtn" type=button value="Close">
			</form>
		</div>
		
		<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script>
		<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/webcam.min.js"></script>
		<script language="JavaScript">
			Webcam.set({
				width: 320,
				height: 240,
				image_format: 'jpeg',
				jpeg_quality: 90
			});
			Webcam.attach('#camera');
					
			function capture() {
				// take snapshot and get image data
				Webcam.snap( function(data_uri) {
					// display results in page
					document.getElementById('output').innerHTML = 
						'<img id="imgdata" src="' + data_uri + '"/>';
				} );
			}
			
			var main = function() {
				$('#attachbtn').click(function() {
					imgsrc = $('#imgdata').attr('src');
					imgdata = imgsrc.split(',');
					
					$('#imgdata', opener.document).attr('src', 'data:image/jpeg; base64, ' + imgdata[1]);
					$('#receiptimg', opener.document).val(imgdata[1]);
					$('.msg', opener.document).text('Image has been attached.');
					window.close();
				});
				
				$('#closebtn').click(function() {
					window.close();
				});
			}
			
			$(document).ready(main);
		</script>
	</body>
</html>