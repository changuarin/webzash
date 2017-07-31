<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Xavi<?php if (isset($page_title)) echo ' | ' . $page_title; ?></title>

<?php echo link_tag(asset_url() . 'images/favicon.ico', 'shortcut icon', 'image/ico'); ?>

<link type="text/css" rel="stylesheet" href="<?php echo asset_url(); ?>css/style.css">
<link type="text/css" rel="stylesheet" href="<?php echo asset_url(); ?>css/tables.css">
<link type="text/css" rel="stylesheet" href="<?php echo asset_url(); ?>css/custom.css">
<link type="text/css" rel="stylesheet" href="<?php echo asset_url(); ?>css/menu.css">
<link type="text/css" rel="stylesheet" href="<?php echo asset_url(); ?>css/jquery.datepick.css">
<link type="text/css" rel="stylesheet" href="<?php echo asset_url(); ?>css/thickbox.css">
<style>
.message{
	font-size: 11px;
    border:1px solid #CCCCCC;
	position:absolute;
	width:200px;
	border:1px solid #c93;
	background:#ffc;
	padding:5px;
	right: -230px;
	cursor: default;
	color: black;
	z-index: 1;
}
</style>
<?php
/* Dynamically adding css files from controllers */
if (isset($add_css))
{
	foreach ($add_css as $id => $row)
	{
		echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"" . asset_url() . $row ."\">";
	}
}
?>

<script type="text/javascript">
	var jsSiteUrl = '<?php echo base_url(); ?>';
</script>

<script type="text/javascript" src="<?php echo asset_url(); ?>js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>js/jquery.datepick.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>js/custom.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>js/hoverIntent.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>js/superfish.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>js/supersubs.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>js/thickbox-compressed.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>js/ezpz_tooltip.min.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>js/shortcutslibrary.js"></script>
<script type="text/javascript" src="<?php echo asset_url(); ?>js/shortcuts.js"></script>

<?php

if(isset($others['style'])):

	echo"<style>{$others['style']}</style>";

endif;

if(isset($others['jscript'])):

	echo $others['jscript'];

endif;

/* Dynamically adding javascript files from controllers */
if (isset($add_javascript))
{
	foreach ($add_javascript as $id => $row)
	{
		echo "<script type=\"text/javascript\" src=\"" . asset_url() . $row ."\"></script>";
	}
}
?>

<script type="text/javascript">
/* Loading JQuery Superfish menu */
$(document).ready(function(){
	$("ul.sf-menu").supersubs({ 
		minWidth:12,
		maxWidth:27,
		extraWidth: 1
	}).superfish(); // call supersubs first, then superfish, so that subs are 
	$('.datepicker').datepick({
		dateFormat: '<?php echo $this->config->item('account_date_format'); ?>',
	});
	$('.datepicker-restrict').datepick({
		dateFormat: '<?php echo $this->config->item('account_date_format'); ?>',
		minDate: '<?php echo date_mysql_to_php($this->config->item('account_fy_start')); ?>',
		maxDate: '<?php echo date_mysql_to_php($this->config->item('account_fy_end')); ?>',
	});
	var t_=0; autocheck(t_);
   <?
	if(isset($others)&&isset($others['script'])):
		echo $others['script'];
	endif;
?>});</script>

</head>
<body>
<div id="container">
	<div id="header">
		<div id="logo">
			<?php echo anchor('', 'Xavi', array('class' => 'anchor-link-b')); ?>
		</div>
		<?php
			if ($this->session->userdata('user_name')) {
				echo "<div id=\"admin\">";
				echo anchor('', 'Accounts', array('title' => "Accounts", 'class' => 'anchor-link-b'));
				echo " | ";
				/* Check if allowed administer rights */
				if (check_access('administer')) {
					echo anchor('admin', 'Administer', array('title' => "Administer", 'class' => 'anchor-link-b'));
					echo " | ";
				}
				echo anchor('user/profile', 'Profile', array('title' => "Profile", 'class' => 'anchor-link-b'));
				echo " | ";
				echo anchor('user/logout', 'Logout', array('title' => "Logout", 'class' => 'anchor-link-b'));
				echo "</div>";
			}
		?>
		<div id="info">
			<?php
				echo $this->config->item('account_name');
				echo " (";
				echo anchor('user/account', 'change', array('title' => 'Change active account', 'class' => 'anchor-link-a'));
				echo ")<br />";
				echo "FY : ";
				echo date_mysql_to_php_display($this->config->item('account_fy_start'));
				echo " - ";
				echo date_mysql_to_php_display($this->config->item('account_fy_end'));
			?>
		</div>
	</div>

	<div id="menu">
		<ul class="sf-menu">
			<li class="current">
				<a href="<?php print base_url(); ?>" title="Dashboard">Dashboard</a>
			</li>
			<li>
				<?php echo anchor('master', 'Master', array('title' => 'Master File')); ?>
				<ul>
					<li><?php echo anchor('master/client', 'Client', array('title' => 'Client\'s File')); ?></li>
					<li><?php echo anchor('master/verification', 'Verification', array('title' => 'Verification')); ?></li>
					<li><?php echo anchor('master/comaker', 'Co-Maker', array('title' => 'Co-Maker\'s File')); ?></li>
					<li><?php echo anchor('master/agent', 'Agent', array('title' => 'Agent\'s File')); ?></li><?php
				if( check_access('ref no') ):?>
					<li><?php echo anchor('master/referenceno', 'Agent Ref. No.', array('title' => 'Agent Ref. No.')); ?></li><?
				endif;
				if( check_access('commn') ):?>
					<li><?php echo anchor('master/commission', 'Commission', array('title' => 'Commission')); ?></li><?
				endif;
				if( check_access('ft request') ):?>
					<li><?php echo anchor('master/fndtrnsfr1', 'Fund Transfer Request', array('title' => 'Fund Transfer Request')); ?></li><?
				endif;
				if( check_access('ft approval') ):?>
					<li><?php echo anchor('master/fndtrnsfr2', 'Fund Transfer Approval', array('title' => 'Fund Transfer Approval')); ?></li><?
				endif;
				?></ul>
			</li>
			<? if( check_access('sales group') ):
			?><li>
				<?php echo anchor('sales', 'Sales', array('title' => 'Sales')); ?>
				<ul>
					<li><?php echo anchor('sales/loanapplication', 'Loan Application', array('title' => 'Loan Application')); ?></li>
					<li><?php echo anchor('sales/loanprocessing', 'Loan Processing', array('title' => 'Loan Process')); ?></li>
					<li><?php echo anchor('sales/checkvoucher', 'Check Voucher', array('title' => 'Check Voucher')); ?></li>
					<li><?php echo anchor('sales/refundque', 'Refund Que', array('title' => 'Refund Que')); ?></li>
					<li><?php echo anchor('sales/refundapp', 'Refund Approval', array('title' => 'Refund Approval')); ?></li>
					<li><?php echo anchor('sales/atmpb_release', 'ATM/PB Release', array('title' => 'ATM/PB Release')); ?></li>
					<li><?php echo anchor('sales/rpcf', 'RPCF Refund', array('title' => 'RPCF')); ?></li>
					<li><?php echo anchor('sales/rpcf_expense', 'RPCF Expense', array('title' => 'RPCF Expense')); ?></li>
				</ul>
			</li><?
			endif; ?>
			<? if( check_access('collection group') ):
			?><li>
				<?php echo anchor('collection', 'Collection', array('title' => 'Collection')); ?>
				<ul><?php
					if( check_access('collection entry') ) {
						?><li><?php echo anchor('collection/collectionsched', 'Collection Schedule', array('title' => 'Collection Schedule')); ?></li><?
					}
					
					if( check_access('collection entry') ) {
						?><li><?php echo anchor('collection/collectionschedadpr', 'Collection Adj/Due/Pmt/Rem', array('title' => 'Collection Adj/Due/Pmt/Rem')); ?></li><?
					}
					
					if( check_access('assign OR PR') ) {
						?><li><?php echo anchor('collection/orpr', 'Assign OR / PR', array('title' => 'Assign OR / PR')); ?></li><?
					}?>
					<li><?php echo anchor('collection/monthlycollection', 'Monthly Collection', array('title' => 'Monthly Collection')); ?></li>
					<?php
					if( check_access('collection adjustment') ) {
						?><li><?php echo anchor('collection/colladj', 'Collection Adjustment', array('title' => 'Collection Adjustment')); ?></li><?
					}
					
					if( check_access('collection entry') ) { ?>
					<li><?php echo anchor('collection/bankentry', 'Bank Entry Form', array('title' => 'Interest / Charges / Refund NR / Remittance Charges')); ?></li>
					<?php } ?>
				</ul>
			</li><?
			endif;
			?>
			<? if( check_access('entries group') ):
			?><li>
				<?php echo anchor('account', 'Accounts', array('title' => 'Chart of accounts')); ?>
			</li>
			<li>
				<?php
					/* Showing Entry Type sub-menu */
					$entry_type_all = $this->config->item('account_entry_types');
					$entry_type_count = count($entry_type_all);
					if ($entry_type_count < 1)
					{
						echo "";
					} else if ($entry_type_count == 1) {
						foreach ($entry_type_all as $id => $row)
						{
							echo anchor('entry/show/' . $row['label'], $row['name'], array('title' => $row['name'] . ' Entries'));
						}
					} else {
						echo anchor('entry', 'Entries', array('title' => 'Entries'));
						echo "<ul>";
						echo "<li>" . anchor('entry/show/all', 'All', array('title' => 'All Entries')) . "</li>";
						foreach ($entry_type_all as $id => $row)
						{
							echo "<li>" . anchor('entry/show/' . $row['label'], $row['name'], array('title' => $row['name'] . ' Entries')) . "</li>";
						}
						echo "</ul>";
					}
				?>
			</li><?
			endif;
			?><li>
				<?php echo anchor('report', 'Reports', array('title' => 'Reports')); ?>
				<ul>
					<li><?php echo anchor('report/mo_sales_summary/' . date('m') . '/' . date('Y') . '/1', 'Monthly Sales Summary', array('title' => 'Monthly Sales Summary')); ?></li>
					<?
					if( check_access('monthly collection summary') ) {
						?><li><?php echo anchor('report/mo_coll_summary/' . date('m') . '/' . date('Y') . '/1', 'Monthly Collection Summary', array('title' => 'Monthly Collection Summary')); ?></li><?
					}
					?>
					<li><?php echo anchor('report/sales/' . date('Y-m-01') . '/' . date('Y-m-d') . '/0', 'Sales', array('title' => 'Sales')); ?></li>
					<?
					
					if( check_access('disbursement report') ) {
						?><li><?php echo anchor('report/disbursement/' . date('Y-m-01') . '/' . date('Y-m-d') . '/0/0', 'Disbursement', array('title' => 'Disbursement')); ?></li><?
					}
					if( check_access('collection report - OR') ) {
						?><li><?php echo anchor('report/collectionor/' . date('Y-m-d') . '/0/0/a', 'Collection O.R.', array('title' => 'Collection O.R.')); ?></li><?
					}
					if( check_access('collection report - PR') ) {
						?><li><?php echo anchor('report/collectionpr/' . date('Y-m-d') . '/0/a/0/0', 'Collection P.R.', array('title' => 'Collection P.R.')); ?></li><?
					}
					?>
					<li><?php echo anchor('report/collectionothers/' . date('Y-m-d') . '/0', 'Collection Others', array('title' => 'Collection Others')); ?></li>
					<li><?php echo anchor('report/autodebit_coll/' . date('Y-m-01') . '/' . date('Y-m-t'), 'Collection Auto-Debit', array('title' => 'Collection Auto-Debit')); ?></li>
					<li><?php echo anchor('report/advancebonus/' . date('Y-m-01') . '/' . date('Y-m-d'), 'Advance Bonus Summary', array('title' => 'Advance Bonus')); ?></li>
					<li><?php echo anchor('report/atmpb_release/' . date('Y-m-01') . '/' . date('Y-m-d'), 'ATM/PB Release Summary', array('title' => 'ATM/PB Release')); ?></li>
					<li><?php echo anchor('report/comm_month_week/0/' . date('Y-m-d'), 'Commission Monthly/Weekly', array('title' => 'Commission Monthly/Weekly')); ?></li>
					<li><?php echo anchor('report/commission/0/' . date('Y-m-d'), 'Commission Summary', array('title' => 'Commission Summary')); ?></li>
					<li><?php echo anchor('report/newclient/' . date('Y-m-01') . '/' . date('Y-m-d'), 'New Client Summary', array('title' => 'New Client')); ?></li>
					<li><?php echo anchor('report/returningclient/' . date('Y-m-01') . '/' . date('Y-m-d'), 'Returning Client Summary', array('title' => 'Returning Client')); ?></li>
					<li><?php echo anchor('report/rpcf/' . date('Y-m-d') . '/0', 'RPCF Refund', array('title' => 'RPCF Refund')); ?></li>
					<li><?php echo anchor('report/rpcf_expense/' . date('Y-m-d') . '/0', 'RPCF Expense', array('title' => 'RPCF Expense')); ?></li>
					<li><?php echo anchor('report/balancesheet', 'Balance Sheet', array('title' => 'Balance Sheet')); ?></li>
					<li><?php echo anchor('report/profitandloss', 'Profit & Loss', array('title' => 'Profit & Loss')); ?></li>
					<li><?php echo anchor('report/trialbalance', 'Trial Balance', array('title' => 'Trial Balance')); ?></li>
					<li><?php echo anchor('report/ledgerst', 'Ledger Statement', array('title' => 'Ledger Statement')); ?></li>
					<li><?php echo anchor('report/reconciliation/pending', 'Reconciliation', array('title' => 'Reconciliation')); ?></li>
				</ul>
			</li>
			<li>
				<?php echo anchor('setting', 'Settings', array('title' => 'Settings')); ?>
			</li>
			<!--<li>
				<?php echo anchor('help', 'Help', array('title' => 'Help', 'class' => 'last')); ?>
			</li>-->
		</ul>
	</div>
	<div id="content">
		<div id="sidebar">
			<?php if (isset($page_sidebar)) echo $page_sidebar; ?>
		</div>
		<div id="main">
			<div id="main-title">
				<?php if (isset($page_title)) echo $page_title; ?>
			</div>
			<?php if (isset($nav_links)) {
				echo "<div id=\"main-links\">";
				echo "<ul id=\"main-links-nav\">";
				if(isset($others)):
					echo "<li>{$others['element']}</li>";
				endif;
				foreach ($nav_links as $link => $title)
				{
					if ($title == "Print Preview"):
						echo "<li>" . anchor_popup($link, $title, array('title' => $title, 'class' => 'nav-links-item', 'style' => 'background-image:url(\'' . asset_url() . 'images/buttons/navlink.png\');', 'width' => '1024')) . "</li>";
					elseif(is_array($title)):
						echo "<li>" . anchor_button($title['action'], $title['label'], array('onclick'=>$title['onclick'], 'title' =>$title['label'], 'class' => 'nav-links-item', 'style' => 'background-image:url(\'' . asset_url() . 'images/buttons/navlink.png\');')) . "</li>";
					else:
						echo "<li>" . anchor($link, $title, array('title' => $title, 'class' => 'nav-links-item', 'style' => 'background-image:url(\'' . asset_url() . 'images/buttons/navlink.png\');')) . "</li>";
					endif;
				}
				echo "</ul>";
				echo "</div>";
			} ?>
			<div class="clear">
			</div>
			<div id="main-content">
				<?php
				$messages = $this->messages->get();
				if (is_array($messages))
				{
					if (count($messages['success']) > 0)
					{
						echo "<div id=\"success-box\">";
						echo "<ul>";
						foreach ($messages['success'] as $message) {
							echo ('<li>' . $message . '</li>');
						}
						echo "</ul>";
						echo "</div>";
					}
					if (count($messages['error']) > 0)
					{
						echo "<div id=\"error-box\">";
						echo "<ul>";
						foreach ($messages['error'] as $message) {
							if (substr($message, 0, 4) == "<li>")
								echo ($message);
							else
								echo ('<li>' . $message . '</li>');
						}
						echo "</ul>";
						echo "</div>";
					}
					if (count($messages['message']) > 0)
					{
						echo "<div id=\"message-box\">";
						echo "<ul>";
						foreach ($messages['message'] as $message) {
							echo ('<li>' . $message . '</li>');
						}
						echo "</ul>";
						echo "</div>";
					}
				}
				?>
				<?php echo $contents; ?>
			</div>
		</div>
	</div>
</div>
<div id="footer">
	<?php if (isset($page_footer)) echo $page_footer ?>
	<a href="#" target="_blank">Xavi<a/> is licensed under <a href="http://www.apache.org/licenses/LICENSE-2.0" target="_blank">Apache License, Version 2.0</a>
</div>
</body>
</html>
