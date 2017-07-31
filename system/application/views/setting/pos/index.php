<table border=0 cellpadding=5 class="simple-table">
	<thead>
		<tr>
			<th>Code</th>
			<th>Group</th>
			<th>Value</th>
			<th>Command</th>
			<th>Status</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	<?php
		foreach ($datas->result() as $row)
		{
			echo "<tr>";

			echo "<td>" . $row->code . "</td>";
			echo "<td>" . $row->group . "</td>";
			echo "<td>" . $row->value . "</td>";
			echo "<td>" . $row->command . "</td>";
			echo "<td><input type='checkbox'" . ($row->status?'checked':'') . " disabled/></td>";
			echo "<td>" . anchor('setting/postag/edit/' . $row->code , "Edit", 
				array('title' => 'Edit ' . $row->group . ' Parameter', 'class' => 'red-link')) . " ";
			echo " &nbsp;" . anchor('setting/postag/delete/' . $row->code , 
				img(array('src' => asset_url() . "images/icons/delete.png", 
					'border' => '0', 'alt' => 'Delete ' . $row->group . ' Parameter', 
					'class' => "confirmClick", 'title' => "Delete Parameter")
				), array(
						'title' => 'Delete ' . $row->group . ' Parameter')
			) . " ";

			echo "</tr>";
		}
	?>
	</tbody>
</table>
