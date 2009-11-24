<?php /* journal $Id: index.php,v 1.1 2004/03/30 23:21:40 jcgonz Exp $ */
##
## journal module - a quick hack of the history module by HGS 3/16/2004

## (c) Copyright
## J. Christopher Pereira (kripper@imatronix.cl)
## IMATRONIX
## 

$AppUI->savePlace();
$module = dPgetParam( $_GET, "m", 0 );

//if module is not journal get the project_id for filter
if ($module<>"journal"){
    $project_id = intval( dPgetParam( $_GET, "project_id", 0 ) );
}

// check for project filter
if ($project_id){
    $where .= "\n WHERE project_id = '$project_id'";}
else
    {$where="";}

//get stuff from db
$psql =
"SELECT *
FROM journal
LEFT JOIN users ON journal_user = user_id
LEFT JOIN projects on journal.journal_project=project_id
$where
ORDER BY journal_date DESC";
$prc = db_exec( $psql );
echo db_error();

$journal = array();

?>
<table width="100%" border="0" cellpadding="3" cellspacing="1">
<form action=./?m=journal method="post" name="pickCompany">
<tr valign="top">
	<td width="32"><img src="./images/icons/notepad.gif" alt="Tasks" border="0" height="24" width="24"></td>
	<td nowrap><h1><?php echo $AppUI->_('Journal Entries:');?></h1></td>
	
<? if ($module=="journal"){
    echo "<td align=right width=100%>",$AppUI->_( 'Project' ),":</td>";
	echo "<td align=right>";
        // pull the projects list
        $sql = "SELECT project_id,project_name FROM projects ORDER BY project_name";
        $projects = arrayMerge( array( 0 => '('.$AppUI->_('All').')' ), db_loadHashList( $sql ) );
        echo arraySelect( $projects, 'project_id', ' onChange=document.pickCompany.submit() class=text', $project_id );
        echo "</form></td>";
        }
        ?>
	
	<td align="right"><input class="button" type="button" value="<?php echo $AppUI->_('Add note');?>" onclick="window.location='?m=journal&a=addedit&project_id=<?echo $project_id?>'"></td>
</table>

<table width="100%" border="0" cellpadding="3" cellspacing="1" class="tbl">
<tr>
	<th width="10">&nbsp;</th>
	<th ><?php echo $AppUI->_('Date');?></th>
	<th ><?php echo $AppUI->_('Project');?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_('Description');?></th>
	<th nowrap="nowrap"><?php echo $AppUI->_('User');?>&nbsp;&nbsp;</th>
</tr>
<?php
while ($row = db_fetch_assoc( $prc )) {
?>
<tr>	
	<td><a href='<?php echo "?m=journal&a=addedit&journal_id=" . $row["journal_id"] ?>'><img src="./images/icons/pencil.gif" alt="<?php echo $AppUI->_( 'Edit journal' ) ?>" border="0" width="12" height="12"></a></td>
	<td><?php echo $row["journal_date"]?></td>
	<td><?php echo $row["project_name"]?></td>
	<td><?php echo $row["journal_description"]?></td>	
	<td><?php echo $row["user_username"]?></td>
</tr>	
<?php
}
?>
</table>
