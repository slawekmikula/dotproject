<?php /* PROJECTS $Id$ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

GLOBAL $AppUI, $project_id, $deny, $canRead, $canEdit, $dPconfig, $cfObj, $m;
require_once($AppUI->getModuleClass('files'));

global $allowed_folders_ary, $denied_folders_ary, $limited;

$AppUI->savePlace();

$cfObj = new CFileFolder();
$allowed_folders_ary = $cfObj->getAllowedRecords($AppUI->user_id);
$denied_folders_ary = $cfObj->getDeniedRecords($AppUI->user_id);

$limited = ((count($allowed_folders_ary) < $cfObj->countFolders()) ? true : false);

if (!$limited) {
	$canEdit = true;
} else if ($limited && array_key_exists($folder, $allowed_folders_ary)) {
	$canEdit = true;
} else {
	$canEdit = false;
}

$showProject = false;


if (getPermission('files', 'edit')) { 
	echo ('<a href="./index.php?m=files&a=addedit&project_id=' . $project_id . '">' 
	      . $AppUI->_('Attach a file') . '</a>');
	echo dPshowImage(dPfindImage('stock_attach-16.png', $m), 16, 16, ''); 
}

$canAccess_folders = getPermission('file_folders', 'access');
if ($canAccess_folders) {
	$folder = dPgetParam($_GET, 'folder', 0);
	require(DP_BASE_DIR . '/modules/files/folders_table.php');
} else if (getPermission('files', 'view')) {
	require(DP_BASE_DIR . '/modules/files/index_table.php');
}
?>
