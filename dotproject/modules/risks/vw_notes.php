<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
GLOBAL $AppUI;

$risk_id = intval( dPgetParam( $_REQUEST, 'risk_id', 0 ) );
$riskDescription = dPgetParam($_POST, 'risk_note_description', '');
$note = dPgetParam($_POST, 'note', false);

// check permissions
$perms =& $AppUI->acl();
$canEdit = $perms->checkModuleItem( 'risks', 'edit', $risk_id );
if (! $canEdit) {
	$AppUI->redirect("m=public&a=access_denied");
}	

print_r($note);

$viewNotes = false;
$addNotes = false;

$risk = new dotProject_AddOn_Risks($risk_id);
$notes = $risk->getNotes($risk_id);
	
echo '
<table cellpadding="5" width="100%" class="tbl">
<tr>
	<th>Date</th>
	<th>User</th>
	<th>Note</th>
</tr>';
foreach($notes as $n)
{
	echo '
<tr>
	<td nowrap>' . $n['risk_note_date'] . '</td>
	<td nowrap>' . $n['risk_note_owner'] . '</td>
	<td width="100%">' . $n['risk_note_description'] . '</td>
</tr>';
}
echo '</table>';
?>