<style>
	body, form, table {
		font-family: 'Arial';
		font-size: 12px;
		cursor: default;
		margin: 0;
		padding: 0;
	}
	
	span {
		color: #ff0000;
	}
	
	.tag1 {
		background-color: #d0d0d0;
	}
	
	.tag1:hover {
		background-color: #99ffff;
	}
	
	.tag0 {
		background-color: #f0f0f0;
	}
	
	.tag0:hover {
		background-color: #99ffff;
	}
	
	.w1 {
		width: 250px;
	}
	
	#list {
	
		margin-top: 100px;
		width: 420px;
	}
	
	table#list {
		border-collapse: collapse;
	}
	
	table#list th {
		border-bottom: 2px solid #999;
		background-color: #eee;
		vertical-align: bottom;
	}
	
	table#list td {
		border-bottom: 1px solid #ccc;
	}
	
	table#list td.alt {
		background-color: #ffc; background-color: rgba(255, 255, 0, 0.2);
	}
	
	.active {
		background-color: #ffff00;
		color: #ff0000;
	}
	
	#menu_form {
		margin-top: 50px
	}
	
	#menu {
		background-color: #fff;
		border: 0;
		position: fixed;
		top: 0;
		width: 420px;
	}
</style>
<body>
	<form id="menu_form" method="post">
		<table id="menu">
			<tbody>
				<tr>
					<td>
						Client Type:&nbsp;
						<select id="ci_type" name="ci_type" >
							<option value="EMP" <?= ($ci_type=='EMP' ? 'selected' : '' ) ?>>ACCOM-EMPLOYEE</option>
							<option value="AGT" <?= ($ci_type=='AGT' ? 'selected' : '' ) ?>>ACCOM-AGENT</option>
							<option value="PEN" <?= ($ci_type=='PEN' ? 'selected' : '' ) ?>>CLIENT-GSIS/SSS</option>
							<option value="SAL" <?= ($ci_type=='SAL' ? 'selected' : '' ) ?>>CLIENT-SALARY</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						Status:&nbsp;
						<select id="ci_status" name="ci_status">
							<option value="A" <?= ($ci_status=='A' ? 'selected' : '' ) ?>>ACTIVE</option>
							<option value="I" <?= ($ci_status=='I' ? 'selected' : '' ) ?>>INACTIVE</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						Last Name:&nbsp;
						<input id="ci_lname" type="text" name="ci_lname" value="<?= isset($ci_lname) ? $ci_lname : 'ANOLIN' ?>" />
						<input type="submit" value="Search" />
					</td>
				</tr>
			</tbody>
		</table>
	</form>
	<table id="list">
		<tbody>
		<?php
		
		if(!empty($results)):
			$i = 0;
			foreach($results as $data):
				if(intval(strlen($data->name)) > 20):
					$ci_name = substr($data->name, 0, 19) . '...';
				else:
					$ci_name = $data->name;
				endif;
				
				echo "
				<tr title='$data->cp_bankbranch'>
					<td class='ccid tag$i'>$data->ci_acctno (<span>$data->cp_pensiontype</span>) $ci_name</td>
				</tr>";
				
				$i = $i ? 0 : 1;
			endforeach;
		endif;
		
		?>
		</tbody>
	</table>
</body>
<script src="<?= base_url() ?>system/application/assets/js/jquery-2.1.3.min.js" type="text/javascript"></script>
<script type="text/javascript">
	var main = function() {
		$('.ccid').click(function()
		{
			var module = $('#client_list', parent.document).prop('class');
			if(module == 'master_client')
			{
				$.post('../../fetch_client_details', {data: $(this).html()}, function(r)
				{
					eval(r);
				});
			} else if(module == '../sales_loanapplication') {
			
				alert(module);
				
			} else if(module == 'sales_refund') {
				$.post('../../../sales/fetch_for_refund', {data: $(this).html()}, function(r)
				{
					eval(r);
				});
			} else if(module == 'sales_atmpb_release') {
				$.post('../../../sales/fetch_atmpb_release_details', {data: $(this).html()}, function(r)
				{
					eval(r);
				});
			} else if(module == 'sales_journal') {
				$.post('../sales/fetch_loans', {data: $(this).html()}, function(r)
				{
					eval(r);
				})
			}
			$('.ccid').removeClass('active');
			$(this).addClass('active');
		});
		
		$('input[type=text], textarea').keyup(function() {
			var string = $(this).val().toUpperCase();
			$(this).val(string);
		});
		
		$('#ci_type, #ci_status').change(function() {
			$('#menu_form').submit();
		});
	}
	
	$(document).ready(main);
</script>