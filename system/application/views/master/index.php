<div>
	<div id="left-col">
		<div class="settings-container">
			<div class="settings-title">
				<?php echo anchor('master/client', 'Client', array('title' => 'Client')); ?>
			</div>
			<div class="settings-desc">
				&nbsp;
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo anchor('master/verification', 'Verification', array('title' => 'Verification')); ?>
			</div>
			<div class="settings-desc">
				&nbsp;
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo anchor('master/comaker', 'Co-Maker', array('title' => 'Co-Maker')); ?>
			</div>
			<div class="settings-desc">
				&nbsp;
			</div>
		</div>
		<div class="settings-container">
			<div class="settings-title">
				<?php echo anchor('master/agent', 'Agent', array('title' => 'Agent')); ?>
			</div>
			<div class="settings-desc">
				&nbsp;
			</div>
		</div><?
		if( check_access('ref no') ):?>
			<div class="settings-container">
		<div class="settings-container">
			<div class="settings-title">
				<?php echo anchor('master/referenceno', 'Agent Ref. No.', array('title' => 'Agent Ref. No.')); ?>
			</div>
			<div class="settings-desc">
				&nbsp;
			</div>
		</div><?
		endif;
		
		if( check_access('commn') ):?>
			<div class="settings-container">
				<div class="settings-title">
					<?php echo anchor('master/commission', 'Commission', array('title' => 'Commission')); ?>
				</div>
				<div class="settings-desc">
					&nbsp;
				</div>
			</div><?
		endif;
		
		if( check_access('ft request') ):?>
			<div class="settings-container">
				<div class="settings-title">
					<?php echo anchor('master/fndtrnsfr1', 'Fund Transfer Request', array('title' => 'Fund Transfer Request')); ?>
				</div>
				<div class="settings-desc">
					&nbsp;
				</div>
			</div><?
		endif;

		if( check_access('ft approval') ):?>
			<div class="settings-container">
				<div class="settings-title">
					<?php echo anchor('master/fndtrnsfr2', 'Fund Transfer Approval', array('title' => 'Fund Transfer Approval')); ?>
				</div>
				<div class="settings-desc">
					&nbsp;
				</div>
			</div><?
		endif;

	?></div>
	<div id="right-col">

	</div>
</div>
<div class="clear">
</div>
