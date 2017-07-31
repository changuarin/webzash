<style>
	body,table{
		font-family: courier;
		font-size: 12px;
		cursor: default;
	}
	.nomargin{margin:0px;}

	.tag1{background-color: #D0D0D0;}
	.tag1:hover{background-color: #99FFFF;}
	.tag0{background-color: #F0F0F0;}
	.tag0:hover{background-color: #99FFFF;}
	.w1{width: 250px;}
	
	/* generic table styling */
	table { border-collapse: collapse; }
	th { border-bottom: 2px solid #999; background-color: #eee; vertical-align: bottom; }
	td { border-bottom: 1px solid #ccc; }

	/* filter-table specific styling */
	td.alt { background-color: #ffc; background-color: rgba(255, 255, 0, 0.2); }
	.filter-table{
		position: fixed;
		margin-top: 0px;
		background-color: white;
		width: 100%;
		padding: 2px;
	}
	.trfixed{
		padding-bottom:10px
	}
</style>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>system/application/assets/js/jquery.filterTable.js"></script>
<body class="nomargin">
	<table id='tden'width='100%'>
		<tr>
			<th align='left'>A.Code</th>
			<th align='left'>AccountTitle</th>
			<th align='right'>Debit</th>
			<th align='right'>Credit</th>
			<th>&nbsp;</th>
		</tr>
	</table>
</body>