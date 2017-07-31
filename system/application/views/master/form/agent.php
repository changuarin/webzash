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
</style>
<body>
	<div class="menu">
		<table>
			<tbody>
				<tr>
					<td>
						<input id="editBtn" type="button" value="Edit">
						<input id="deleteBtn" type="button" value="Delete">
						<input id="newBtn" type="button" value="New">
						<input id="cancelBtn" type="button" value="Cancel">
						<input id="submitBtn" type="button" value="Submit">
					</td>
				</tr>
			</tbody>
		</table>
	</div><!--/.menu-->
	<h3 class="tab-heading">Agent Information</h3>
	<div class="text-success">
		<?php echo $this->session->flashdata('added'); ?>
		<?php echo $this->session->flashdata('updated'); ?>
	</div>
	<div class="text-warning">
		<?php echo $this->session->flashdata('deleted'); ?>
		<ul class="error-list">
			<?php echo validation_errors(); ?>
		</ul>
	</div><!--/.message-->
	<div class="main" style="padding-left: 10px;">
		<form id="agentForm" method="post">
			<table>
				<tbody>
					<tr>
						<td class="text-right">
							<i class="text-warning">*</i>
							<label for="airefno2">Ref. No.</label>
						</td>
						<td colspan="2">
							<input id="aiRefno2" type="text" name="airefno2" value="<?php echo set_value('airefno2'); ?>">
							<input class="hidden" id="agentProcess" type="text" name="agentprocess" value="
<?php echo set_value('agentprocess') ? set_value('agentprocess') : 'addagent'; ?>
">
							<input class="hidden" id="aiRefno" type="text" name="airefno" value="<?php echo set_value('airefno'); ?>">
						</td>
					</tr>
					<tr>
						<td class="text-right">
							<label for="aifname">First Name</label>
						</td>
						<td>
							<input id="aiFname" type="text" name="aifname" value="<?php echo set_value('aifname'); ?>">
						</td>
						<td class="text-right">
							<label for="aibranchcode">Branch</label>
							<select id="aiBranchcode" name="aibranchcode">
								<option value="">-SELECT-</option>
								<?php if( ! empty($branches)) : ?>
								<?php foreach($branches as $branch) : ?>
								<option value="<?php echo $branch->Branch_Code; ?>" <?php echo (set_value('aibranchcode') == $branch->Branch_Code ? 'selected' : ''); ?>><?php echo $branch->Branch_Name; ?></option>
								<?php endforeach; ?>
								<?php endif; ?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="text-right">
							<label for="aimname">Middle Name</label>
						</td>
						<td>
							<input id="aiMname" type="text" name="aimname" value="<?php echo set_value('aimname'); ?>">
						</td>
						<td colspan="2"></td>
					</tr>
					<tr>
						<td class="text-right">
							<label for="ailname">Last Name</label>
						</td>
						<td>
							<input id="aiLname" type="text" name="ailname" value="<?php echo set_value('ailname'); ?>">
						</td>
						<td colspan="2"></td>
					</tr>
					<tr>
						<td class="text-right">
							<label for="aibdate">Birth Date</label>
						</td>
						<td>
							<input class="text-center" id="aiBdate" type="text" name="aibdate" placeholder="YYYY-MM-DD" value="<?php echo set_value('aibdate'); ?>">
						</td>
						<td colspan="2">
							<input class="text-center" id="age" type="text" name="age">
						</td>
					</tr>
					<tr>
						<td class="text-right">
							<label for="aisex">Gender</label>
						</td>
						<td colspan="3">
							<select id="aiSex" name="aisex">
								<option value="">-SELECT-</option>
								<option value="M" <?php echo (set_value('aisex') == 'M' ? 'selected' : ''); ?>>Male</option>
								<option value="F" <?php echo (set_value('aisex') == 'F' ? 'selected' : ''); ?>>Female</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="text-right">
							<label for="aicivilstatus">Civil Status</label>
						</td>
						<td colspan="3">
							<select id="aiCivilstatus" name="aicivilstatus">
								<option value="">-SELECT-</option>
								<option value="S" <?php echo (set_value('aicivilstatus') == 'S' ? 'selected' : ''); ?>>Single</option>
								<option value="M" <?php echo (set_value('aicivilstatus') == 'M' ? 'selected' : ''); ?>>Married</option>
								<option value="SP" <?php echo (set_value('aicivilstatus') == 'SP' ? 'selected' : ''); ?>>Separated</option>
								<option value="W" <?php echo (set_value('aicivilstatus') == 'W' ? 'selected' : ''); ?>>Widow</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="text-right">
							<label for="aitelno">Tel. No</label>
						</td>
						<td colspan="3">
							<input id="aiTelno" type="text" name="aitelno" value="<?php echo set_value('aitelno'); ?>">
						</td>
					</tr>
					<tr>
						<td class="text-right">
							<label for="aimobileno">Mobile No</label>
						</td>
						<td colspan="3">
							<input id="aiMobileno" type="text" name="aimobileno" value="<?php echo set_value('aimobileno'); ?>">
						</td>
					</tr>
					<tr>
						<td class="text-right vertical-top">
							<label for="aiadd1">Present Address</label>
						</td>
						<td colspan="3">
							<textarea class="vertical-top" id="aiAdd1" name="aiadd1" rows="3"><?php echo set_value('aiadd1'); ?></textarea>
						</td>
					</tr>
					<tr>
						<td class="text-right vertical-top">
							<label for="aiadd2">Permanent Address</label>
						</td>
						<td colspan="3">
							<textarea id="aiAdd2" name="aiadd2" rows="3"><?php echo set_value('aiadd2'); ?></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<i class="text-warning">* Not required if New Agent</i>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div><!--./main-->
</body>

<script src="<?= base_url() ?>system/application/assets/js/jquery-2.1.3.min.js" type="text/javascript"></script>
<script type="text/javascript">
	function getAge(birthDate)
	{
		var today = new Date();
		var birthDate = new Date(birthDate);
		var age = today.getFullYear() - birthDate.getFullYear();
		var month = today.getMonth() - birthDate.getMonth();
		
		if(month < 0 || (month === 0 && today.getDate() < birthDate.getDate()))
		{
			age--;
		}
		
		return age;
	}
	
	$(document).ready(function() {
		var menu = $('.menu').contents();
		var main = $('.main').contents();
		
		menu.find('input').hide();
		menu.find('#cancelBtn, #submitBtn').show();
		
		$('#newBtn').click(function() {
			menu.find('input').hide();
			menu.find('#cancelBtn, #submitBtn').show();
			
			main.find('input, select, textarea').prop('disabled', false).val('');
			main.find('input, select, textarea').prop('disabled', false).val('');
			main.find('#agentprocess').val('addagent');
			
			var agentList = $('#agenttList', parent.document).contents();
			
			agentList.find('.active').removeClass('active');
		});
		
		$('#editBtn').click(function() {
			menu.find('input').hide();
			menu.find('#cancelBtn, #submitBtn').show();
			
			main.find('input, select, textarea').prop('disabled', false);
			main.find('#agentprocess').val('updateagent');
		});
		
		$('#deleteBtn').click(function() {
			var deleteClient = confirm('Delete Co-Maker?');
			
			if(deleteClient == true)
			{
				var agentList = $('#agentList', parent.document).contents();
				
				agentList.find('.active').hide();
				
				main.find('#agentprocess').prop('disabled', false).val('deleteagent');
				main.find('#airefno').prop('disabled', false);
				
				var agentProcess = main.find('#agentprocess').val();
				
				$('#agentForm').submit();
			} else {
				return false;
			}
		});
		
		$('#cancelBtn').click(function() {
			menu.find('input').hide();
			menu.find('#newBtn').show();
			
			main.find('input, select, textarea').prop('disabled', true).val('');
			
			var agentList = $('#agentList', parent.document).contents();
			
			agentList.find('.active').removeClass('active');
		});
		
		$('#submitBtn').click(function() {
			var submitForm = confirm('Submit form?')
			
			if(submitForm == true)
			{
				var agentList = $('#agentList', parent.document).contents();
				var aiRefno = $('#aiRefno').val();
				var aiRefno2 = $('#aiRefno2').val();
				
				if(aiRefno == aiRefno2)
				{
					agentList.find('.active').removeClass('active');
				} else {
					agentList.find('.active').hide();
				}
				
				$('#agentForm').submit();
			} else {
				return false;
			}
		});
		
		$('#aiBdate').blur(function() {
			var age = getAge($(this).val());
			
			$('#age').val(age);
		});
			
		main.find('input[type=text], textarea').blur(function() {
			var strUpper =  $(this).val().toUpperCase();
			
			$(this).val(strUpper);
		});
	});
</script>