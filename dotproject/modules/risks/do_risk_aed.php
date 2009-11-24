<?php 
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

$del = dPgetParam($_POST, 'del', 0);
$isNotNew = dPgetParam($_POST, 'risk_id', 0);

$risk = new dotProject_AddOn_Risks();

if (($msg = $risk->bind( $_POST ))) {
	$AppUI->setMsg( $msg, UI_MSG_ERROR );
	$AppUI->redirect();
}

$AppUI->setMsg( 'Risk' );
if ($del) {
	if (($msg = $risk->delete())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
		$AppUI->redirect();
	} else {
		$AppUI->setMsg( "deleted", UI_MSG_ALERT, true );
		$AppUI->redirect( "m=risks" );
	}
} else {
	if (!$isNotNew) {
		$risk->risk_owner = $AppUI->user_id;
	}
	if (($msg = $risk->store())) {
		$AppUI->setMsg( $msg, UI_MSG_ERROR );
	} else {
		$AppUI->setMsg( $isNotNew ? 'updated' : 'added', UI_MSG_OK, true );
	}
	$AppUI->redirect();
}
?>