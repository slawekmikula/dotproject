<?php /* FILES $Id$ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

//addfile sql
$file_id = intval(dPgetParam($_POST, 'file_id', 0));
$coReason = dPgetParam($_POST, 'file_co_reason', '');


$not = dPgetParam($_POST, 'notify', '0');
$notcont = dPgetParam($_POST, 'notify_contacts', '0');

$obj = new CFile();
$obj->_message = (($file_id) ? 'updated' : 'added');
$obj->file_category = intval(dPgetParam($_POST, 'file_category', 0));

if (!$obj->bind($_POST)) {
	$AppUI->setMsg($obj->getError(), UI_MSG_ERROR);
	$AppUI->redirect();
}

if (!ini_get('safe_mode')) {
	set_time_limit(600);
}
ignore_user_abort(1);

$obj->checkout($AppUI->user_id, $file_id, $coReason);

//Notification
$obj->load($file_id);
if ($not) {
	$obj->notify();
}
if ($notcont) {
	$obj->notifyContacts();
}

// We now have to display the required page
// Destroy the post stuff, and allow the page to display index.php again.
$a = 'index';
unset($_GET['a']);

$params = 'file_id=' . $file_id;
$session_id = SID;
                                                                      
session_write_close();
// are the params empty
// Fix to handle cookieless sessions
if ($session_id != "") {
    $params .= "&" . $session_id;
}

header("Refresh: 0; URL=index.php?" . $AppUI->state["SAVEDPLACE"]);
echo '<script type="text/javascript">
fileloader = window.open("fileviewer.php?'.$params.'", "mywindow",
"location=1,status=1,scrollbars=0,width=5,height=5");
fileloader.moveTo(0,0);
</script>';


?>
