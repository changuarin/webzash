<style>
	body, table {
		cursor: default;
		font-family: 'Arial';
		font-size: 12px;
	}
	
	input {
		border: 1px solid #0099cc;
	}
	
	input[type=text] {
		text-align: center;
	}
	
	/* generic table styling */
	table {
		border-collapse: collapse;
		border-top: 1px solid #000;
	}
	
	th {
		border-bottom: 2px solid #999;
		background-color: #eee;
		vertical-align:  bottom;padding: 4px;
	}
	
	td {
		border-bottom: 1px solid #ccc;
		padding: 4px;
	}
	
	/* filter-table specific styling */
	td.alt {
		background-color: #ffc;
		background-color: rgba(255, 255, 0, 0.2);
	}
	
	.filter-table{
		position: fixed;
		margin-top: 0px;
		background-color: white;
		width: 100%;
		padding: 2px;
	}
	
	.trfixed{
		padding-bottom:10px;
	}
	
	.fixed{
		position: ;
		background-color: #FFF;
		width: 100%;
	}
	
	/* Text alignment */
	.center {
		text-align: center;
	}
	
	.left {
		text-align: left;
	}
	
	.right {
		text-align: right;
	}
	
	.bb {
		border-bottom: 1px solid gray;
	}
	
	.bl {
		border-left: 1px solid gray;
	}
	
	.br {
		border-right: 1px solid gray;
	}
	
	.bt {
		border-top: 1px solid gray;
	}
	
	.nomargin {
		margin: 0px;
	}

	.tag1 {
		background-color: #D0D0D0;
	}
	
	.tag1:hover {
		background-color: #99FFFF;
	}
	
	.tag0 {
		background-color: #F0F0F0;
	}
	
	.tag0:hover {
		background-color: #99FFFF;
	}
	
	.w1 {
		width: 250px;
	}
</style>
<body style="margin:0px;">
	<form id="rpcf_form" method="post" action="">
		<table cellpadding="0" cellspacing="0" class="cl" width="100%">
			<tbody>
				<tr>
					<td class="trfixed">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="12">
						Replenish Date:
						<input id="replenish_date" type="text" name="replenish_date" value="<?= date('Y-m-d')?>" />
						&nbsp;
						<input id="process-btn" type="button" id="process-btn" value="Process" />
						&nbsp;
						<input id="clear-btn" type="button" name="clear-btn" value="Clear" />
						<input id="from_date" type="hidden" name="from_date" value="<?= $from_date ?>" />
						<input id="to_date" type="hidden" name="to_date" value="<?= $to_date ?>" />
						<input id="group" type="hidden" name="group" value="<?= $group ?>" />
					</td>
				</tr>
				<tr>
					<th class="bb">&nbsp;</th>
					<th class="bb bl center">Date</th>
					<th class="bb bl center">RFP No.</th>
					<th class="bb bl left">Client Name</th>
					<th class="bb bl left">Bank/Branch</th>
					<th class="bb bl right">Amount</th>
					<th class="bb bl left">Remarks</th>
					<th class="bb bl center">Replenish Date</th>
					<th class="bb bl center"><input id="main-ckbox" type="checkbox" name="main-ckbox" /></th>
				</tr>
				<?
				
				if(!empty($datas)):
					$i = 1;
					$j = 0;
					foreach ($datas as $d):
						echo '
						<tr class="data tag' . $j . '">
							<td class="bb right">' . $i . ')</td>
							<td class="bb bl center">' . date('Y-m-d', strtotime($d->transdate)) . '</td>
							<td class="bb bl center">' . $d->transrefno . '</td>
							<td class="bb bl">' . $d->ci_name . '</td>
							<td class="bb bl">' . $d->ci_bankbranch . '</td>
							<td class="bb bl right">' . number_format($d->refunddue, 2, '.', ',') . '</td>
							<td class="bb bl">' . $d->remarks . '</td>
							<td class="bb bl center">' . ($d->replenish_date ? $d->replenish_date : '&nbsp;') . '</td>
							<td class="bb bl center"><input type="checkbox" name="refund[]" value="' . $d->aid . '" /></td>
						</tr>';
						
						$i++;
						$j = $j ? 0 : 1;
					endforeach;
				endif;

				?>
			</tbody>
		</table>
	</form>
	
	<script src="<?= base_url() ?>system/application/assets/js/jquery-2.1.3.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.filterTable.js"></script>
	<script>
		var main = function() {
			$('table.cl').filterTable({
				autofocus: 1,
				placeholder: 'Search Client'
			});
			
			$('#main-ckbox').click(function() {
				var is_checked = $(this).is(':checked');
				$('input[type=checkbox]').prop('checked', is_checked);
			});
			
			$('#process-btn').click(function() {
				var a = confirm("Proceed to process rpcf list?");
				if (a == true) {
					$('#rpcf_form').prop('action', '../../../process_rpcf_list').submit();
				} else {
					return false;
				} 
			});
			
			$('#clear-btn').click(function() {
				var a = confirm("Proceed to clear rpcf list?");
				if (a == true) {
					$('#rpcf_form').prop('action', '../../../clear_rpcf_list').submit();
				} else {
					return false;
				} 
			})
		}
		
		$(document).ready(main);
	</script>
</body>