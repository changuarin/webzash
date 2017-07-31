<div>
	<div id="left-col">
		<div class="settings-container">
			<div class="settings-title">
				<?php echo anchor('report/mo_sales_summary/' . date('m') . '/' . date('Y') . '/1', 'Monthly Sales Summary', array('title' => 'Monthly Sales Summary')); ?>
			</div>
			<div class="settings-desc">
				&nbsp;
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo anchor('report/mo_coll_summary/' . date('m') . '/' . date('Y') . '/1', 'Monthly Collection Summary', array('title' => 'Monthly Collection Summary')); ?>
			</div>
			<div class="settings-desc">
				&nbsp;
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo anchor('report/sales/' . date('Y-m-01') . '/' . date('Y-m-d') . '/0', 'Sales', array('title' => 'Sales')); ?>
			</div>
			<div class="settings-desc">
				&nbsp;
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo anchor('report/collectionor/' . date('Y-m-d') . '/0/0/a', 'Collection O.R.', array('title' => 'Collection O.R.')); ?>
			</div>
			<div class="settings-desc">
				&nbsp;
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo anchor('report/collectionpr/' . date('Y-m-d').'/0/a/0/0', 'Collection P.R.', array('title' => 'Collection P.R.')); ?>
			</div>
			<div class="settings-desc">
				&nbsp;
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo anchor('report/collectionothers/' . date('Y-m-d').'/0', 'Collection Others', array('title' => 'Collection Others')); ?>
			</div>
			<div class="settings-desc">
				&nbsp;
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo anchor('report/autodebit_coll/' . date('Y-m-01').'/'.date('Y-m-t'), 'Collection Auto-Debit', array('title' => 'Collection Auto-Debit')); ?>
			</div>
			<div class="settings-desc">
				&nbsp;
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo anchor('report/disbursement/' . date('Y-m-01') . '/' . date('Y-m-d').'/0/0', 'Disbursement', array('title' => 'Disbursement')); ?>
			</div>
			<div class="settings-desc">
				&nbsp;
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo anchor('report/advancebonus/' . date('Y-m-01') . '/' . date('Y-m-d'), 'Advance Bonus', array('title' => 'Advance Bonus')); ?>
			</div>
			<div class="settings-desc">
				&nbsp;
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo anchor('report/atmpb_release/' . date('Y-m-01') . '/' . date('Y-m-d'), 'ATM/PB Release Summary', array('title' => 'ATM/PB Release')); ?>
			</div>
			<div class="settings-desc">
				&nbsp;
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo anchor('report/comm_month_week/0/' . date('Y-m-d'), 'Commission Monthly/Weekly', array('title' => 'Commission Monthly/Weekly')); ?>
			</div>
			<div class="settings-desc">
				&nbsp;
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo anchor('report/commission/0/' . date('Y-m-d'), 'Commission Summary', array('title' => 'Commission Summary')); ?>
			</div>
			<div class="settings-desc">
				&nbsp;
			</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo anchor('report/newclient/' . date('Y-m-01') . '/' . date('Y-m-d'), 'New Client Summary', array('title' => 'New Client')); ?>
			</div>
			<div class="settings-desc">
				&nbsp;
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo anchor('report/returningclient/' . date('Y-m-01') . '/' . date('Y-m-d'), 'Returning Client Summary', array('title' => 'Returning Client')); ?>
			</div>
			<div class="settings-desc">
				&nbsp;
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo anchor('report/rpcf/' . date('Y-m-d') . '/0', 'RPCF Summary', array('title' => 'RPCF')); ?>
			</div>
			<div class="settings-desc">
				&nbsp;
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo anchor('report/balancesheet', 'Balance Sheet', array('title' => 'Balance Sheet')); ?>
			</div>
			<div class="settings-desc">
				&nbsp;
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo anchor('report/profitandloss', 'Profit and Loss Statement', array('title' => 'Profit and Loss Statement')); ?>
			</div>
			<div class="settings-desc">
				&nbsp;
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo anchor('report/trialbalance', 'Trial Balance', array('title' => 'Trial Balance')); ?>
			</div>
			<div class="settings-desc">
				&nbsp;
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo anchor('report/ledgerst', 'Ledger Statement', array('title' => 'Ledger Statement')); ?>
			</div>
			<div class="settings-desc">
				&nbsp;
			</div>
		</div>
	</div>
	<div id="right-col">

	</div>
</div>
<div class="clear">
</div>