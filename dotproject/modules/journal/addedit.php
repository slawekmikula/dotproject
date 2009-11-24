<?php /* $Id: addedit.php,v 1.1 2004/03/30 23:21:40 jcgonz Exp $ */
##
## journal module - a quick hack of the history module by HGS 3/16/2004

## (c) Copyright
## J. Christopher Pereira (kripper@imatronix.cl)
## IMATRONIX
##

$journal_id = defVal( @$_GET["journal_id"], 0);

$project_id = intval( dPgetParam( $_GET, "project_id", 0 ) );


// check permissions
if (!$canEdit) {
	$AppUI->redirect( "m=public&a=access_denied" );
}


$action = @$_REQUEST["action"];
if($action) {
	$journal_description = $_POST["journal_description"];
	$journal_project = $_POST["journal_project"];
	$userid = $AppUI->user_id;
	
	if( $action == "add" ) {
		$sql = "INSERT INTO journal (journal_date, journal_description, journal_user, journal_project) " .
		  "VALUES (now(), '$journal_description', $userid, $journal_project)";
		$okMsg = "journal added";
	} else if ( $action == "update" ) {
		$sql = "UPDATE journal SET journal_description = '$journal_description', journal_project = '$journal_project' WHERE journal_id = $journal_id";
		$okMsg = "journal updated";
	} else if ( $action == "del" ) {
		$sql = "DELETE FROM journal WHERE journal_id = $journal_id";
		$okMsg = "journal deleted";				
	}
	if(!db_exec($sql)) {
		$AppUI->setMsg( db_error() );
	} else {	
		$AppUI->setMsg( $okMsg );
	}
	$AppUI->redirect();
}

// pull the journal
$sql = "SELECT * FROM journal WHERE journal_id = $journal_id";
db_loadHash( $sql, $journal );

if ($journal["journal_project"]){
    $project_id=$journal["journal_project"];
}

?>

<form name="AddEdit" method="post">				
<table width="100%" border="0" cellpadding="0" cellspacing="1">
<input name="action" type="hidden" value="<?php echo $journal_id ? "update" : "add"  ?>">
<tr>
	<td><img src="./images/icons/notepad.gif" alt="" border="0"></td>
	<td align="left" nowrap="nowrap" width="100%"><h1><?php echo $AppUI->_( $journal_id ? 'Edit Note' : 'New Note' );?></h1></td>
</tr>
</table>

<table border="0" cellpadding="4" cellspacing="0" width="98%">
<tr>
	<td width="50%" align="right">
		<a href="javascript:delIt()"><img align="absmiddle" src="./images/icons/trash.gif" width="16" height="16" alt="" border="0"><?php echo $AppUI->_('delete journal');?></a>
	</td>
</tr>
</table>

<table border="1" cellpadding="4" cellspacing="0" width="50%" class="std">
	
<script>
	function delIt() {
		AddEdit.action.value = "del";
		AddEdit.submit();
	}	
</script>
	
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Project' );?>:</td>
	<td width="60%">
<?php
// pull the projects list
$sql = "SELECT project_id,project_name FROM projects ORDER BY project_name";
$projects = arrayMerge( array( 0 => '('.$AppUI->_('any').')' ), db_loadHashList( $sql ) );
echo arraySelect( $projects, 'journal_project', 'class="text"', $project_id );

?>
	</td>
</tr>
	
<tr>
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_( 'Description' );?>:</td>
	<td width="60%">
		<textarea name="journal_description" class="textarea" cols="60" rows="5" wrap="virtual"><?php echo $journal["journal_description"];?></textarea>
	</td>
</tr>	
		
<table border="0" cellspacing="0" cellpadding="3" width="50%">
<tr>
	<td height="40" width="30%">&nbsp;</td>
	<td  height="40" width="35%" align="right">
		<table>
		<tr>
			<td>
				<input class="button" type="button" name="cancel" value="<?php echo $AppUI->_('cancel'); ?>" onClick="javascript:if(confirm('Are you sure you want to cancel.')){location.href = '?<?php echo $AppUI->getPlace();?>';}">
			</td>
			<td>
				<input class="button" type="button" name="btnFuseAction" value="<?php echo $AppUI->_('save'); ?>" onClick="submit()">
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>	
	
</table>
</form>		
</body>
</html>
