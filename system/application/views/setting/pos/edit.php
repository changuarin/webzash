<?php
	echo form_open('setting/postag/edit/' . $code_id);

	echo "<p>";
	echo form_label('Code', 'code').':&nbsp;';
	echo form_input($code);
	echo form_input($oldcode);
	echo "<br />";
	echo "</p>";

	echo "<p>";
	echo form_label('Group', 'group');
	echo "<br />";
	echo form_input($group);
	echo "</p>";

	echo "<p>";
	echo form_label('Description', 'value');
	echo "<br />";
	echo form_textarea($value);
	echo "</p>";

	echo "<p>";
	echo form_label('Command', 'command');
	echo "<br />";
	echo form_input($command);
	echo "</p>";

	echo "<p>";
	echo form_input($status).'&nbsp;';
	echo form_label('Status', 'status');
	echo "</p>";

	echo "<p>";
	echo form_submit('submit', 'Update');
	echo " ";
	echo anchor('setting/postag', 'Back', 'Back to Parameter');
	echo "</p>";

	echo form_close();

