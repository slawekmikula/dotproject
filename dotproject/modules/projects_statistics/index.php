<?php
## statistics module - display project advance graph

$AppUI->savePlace();
$module = dPgetParam( $_GET, "m", 0 );
$project_id = intval( dPgetParam( $_GET, "project_id", 0 ) );

?>
<table width="100%" border="0" cellpadding="3" cellspacing="1" class="tbl">
<tr>
	<th width="10"> Project Statistic </th>
</tr>
<tr><td style="text-align: center;">
    <?php echo '<img src="?m=projects_statistics&a=task_plot&suppressHeaders=1&project_id=' . $project_id . '">'; ?>
</td></tr>
</table>
