<style>
	body,table {
		font-family: 'Arial';
		font-size: 12px;
		cursor: default;
	}
	.nomargin {margin:0px;}

	.tag1 {background-color:#D0D0D0;}
	.tag1:hover {background-color:#99FFFF;}
	.tag0 {background-color:#F0F0F0;}
	.tag0:hover {background-color:#99FFFF;}
	.w1 {width:250px;}
	
	/* generic table styling */
	table {border-collapse: collapse;}
	th {border-bottom:2px solid #999; background-color: #eee; vertical-align: bottom;}
	td {border-bottom:1px solid #ccc;}

	/* filter-table specific styling */
	td.alt {background-color:#ffc; background-color: rgba(255, 255, 0, 0.2);}
	.filter-table {
		position: fixed;
		margin-top: 0px;
		background-color: white;
		width: 100%;
		padding: 2px;
	}
	.trfixed {
		padding-bottom: 10px;
	}
	.isSelected, .isSelected.alt, .alt.isSelected{background-color:#FFFF00;color:#FF0000;}
</style>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.filterTable.js"></script>
<script type="text/javascript">
	$(document).ready(function()
	{
	
		$('.ccid').click(function(){
			$.post('clickLoan',{data:$(this).attr('class')},function(r)
			{
				//alert(r);
				eval(r);
			});
			$('.ccid').removeClass('isSelected');
			$(this).addClass('isSelected');
		});
		
		$('table.cl').filterTable({
			autofocus: 1,
			placeholder: 'Search Client'
		});
	});
</script>
<body class="nomargin">
	<table width="100%"class="cl">
	<tr><td class="trfixed">&nbsp;</td></tr><?
		
		$j=0;
		foreach($datas as $d)
		{
			echo"<tr class='data'><td class='ccid tag$j {$d['CI_AcctNo']} {$d['LH_PN']}'>{$d['LH_PN']} {$d['CI_Name']}</td></tr>";	
			$j=$j?0:1;
		}

		
	?></table>
</body>