<style type="text/css">
	body,
	table,
	textarea {
		font-family: 'Arial', sans-serif;
		font-size: 12px;
	}
	
	img,
	input,
	select,
	textarea {
		border: 1px solid #0099cc;
	}
	
	input[type=text]:enabled,
	select:enabled,
	textarea:enabled {
		background-color: #ffff99;
		border: 1px solid #ff0000;
	}
	
	input[type=text]:disabled,
	select:disabled,
	textarea:disabled {
		background-color: #fff !important;
		border: 1px solid #0099cc;
	}
	
	.hidden {
		display: none;
	}
	
	.tag0 {
		background-color: #f0f0f0;
	}
	
	.tag1 {
		background-color: #d0d0d0;
	}
	
	.tag0:hover,
	.tag1:hover {
		background-color: #99ffff;
	}
	
	.text-center {
		text-align: center;
	}
	
	.text-right {
		text-align: right;
	}
	
	.vertical-top {
		vertical-align: top;
	}
	
	.text-success {
		color: green;
	}
	
	.text-warning {
		color: #ff0000;
	}
	
	.menu {
		border: 0;
		position: fixed;
		top: 15px;
		right: 15px;
	}
	
	.enabled {
		color: #ff0000;
		cursor: pointer;
	}
	
	.tab-heading {
		background-color: #09c;
		color: #fff;
		font-size: 12px;
		margin-top: 10px;
		margin-bottom: 10px;
		padding: 10px 5px;
	}
	
	.error-list {
		list-style: none;
		padding-left: 5px;
	}
	
	.error-list li {
		padding: 5px 10px;
	}
	
	.list-table {
		border-collapse: collapse;
	}
	
	.list-table th,
	.list-table td {
		border-bottom: 1px solid #ccc;
		padding: 2px;
	}
	
	#commnTable {
		border: 1px solid #000;
		width: 600px;
	}
	
	#commnTable th,
	#commnTable tr,
	#commnTable td {
		border: 1px solid #000;
	}
</style>
<body>
	<div class="main">
		<h3 class="tab-heading">Update Agents</h3>
		<form id="agentsForm" method="post">
			<table>
				<tbody>
					<tr>
						<td>
							<label for="name">Name</label>
							<input id="name" type="text" name="name" placeholder="Lastname, Firstname">
							<input id="searchBtn" type="button" value="Search">
						</td>
					</tr>
				</tbody>
			</table>
			<br>
			<div>
				<label for="airefno">Ref. No.</label>
				<input class="text-center" id="aiRefno" type="text" name="airefno">
				<input id="updateBtn" type="button" value="Update">
			</div>
			<br>
			<table class="list-table" id="commnTable">
				<thead>
					<tr>
						<th>No.</th>
						<th>Ref. No.</th>
						<th>Name</th>
						<th>Branch</th>
						<th>
							<input id="mainCkbox" type="checkbox" name="mainckbox">
						</th>
					</tr>
				</thead>
				<tbody id="agentList">
				</tbody>
			</table>
		</form>
	</div>
</body>

<script src="<?php echo base_url(); ?>system/application/assets/js/jquery-2.1.3.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>system/application/assets/js/numeral.js"></script>
<script type="text/javascript">
	function clientList3(e) {
		var data = e.getAttribute('class');
		var height = 600;
		var width = 680;
		var link = 'client_list3/' + data;
		
		popupWindow(height, width, link);
	}
	
	function popupWindow(height, width, link)
  {
      var top = (screen.height/2) - (height/2);
      var left = (screen.width/2) - (width/2);
      
      return window.open(
      	link,
      	'popupWindow',
      	'width=' + width + ', height=' + height + ', top=' + top + ', left=' + left + ', scrollbars=yes').focus();
  }
	
	$(document).ready(function() {
		$('#name').blur(function() {
			var strUpper = $(this).val().toUpperCase();
			
			$(this).val(strUpper);
		});
		
		$('#searchBtn').click(function() {
			var name = $('#name').val();
			
			$('#agentList').html('');
			
			$.post('list_agents',
					{
						name: name
					},
					function(results) {
						var i = 1;
						var j = 1;
						
						$.each(results, function(index, result) {
							
							input = '';
							
							if(result.added_date == '' || result.added_date == null)
							{
								input += `<input type="checkbox" name="agent[]" value="` + result.AI_RefNo + '|' + result.database + `">`;
							}
							
							$('#agentList').append(`
								<tr class="tag` + j + `">
									<td class="text-right">` + i + `)</td>
									<td class="` + result.AI_RefNo + '|' + result.database + `" onclick="clientList3(this)">
										<span class="text-warning">` + result.AI_RefNo + `</span>
									</td>
									<td>` + result.name + `</td>
									<td>` + result.database + `</td>
									<td class="text-center">` + input + `</td>
								</tr>
							`);
							
							i++;
							j = j ? 0 : 1;
						});
			});
			
		});
		
		$('#updateBtn').click(function() {
			var updateAgents = confirm('Update Agents?');
			
			if(updateAgents == true) {
				$('#agentsForm').submit();
			} else {
				return false;
			}
		})
		
		$('#mainCkbox').click(function() {
			var isChecked = $(this).prop('checked');
			
			$('input:checkbox').prop('checked', isChecked);
		});
	});
</script>