<?php 
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

// Add / Edit contact
$risk_id = dPgetParam($_GET, 'risk_id', 0);

// check permissions
$denyView = getDenyRead( $m, $risk_id );

if ($denyView) {
	$AppUI->redirect( "m=help&a=access_denied" );
} 

$riskProbability = dPgetSysVal( 'RiskProbability' );
$riskStatus = dPgetSysVal( 'RiskStatus' );
$riskImpact = dPgetSysVal( 'RiskImpact' );

$riskDuration = array(1=>'Hours', 24=>'Days', 168=>'Weeks');


$q = new DBQuery();
$q->addQuery('user_id');
$q->addQuery('CONCAT( contact_first_name, \' \', contact_last_name)');
$q->addTable('users');
$q->leftJoin('contacts', 'c', 'user_contact = contact_id');
$q->addOrder('contact_first_name, contact_last_name');
$users = $q->loadHashList();
//$users = db_loadHashList( $sql );

$q->clear();
$q->addQuery('project_id, project_name');
$q->addTable('projects');
$projects = $q->loadHashList();
$projects[0] = '';

//Pull contact information
$q->clear();
$q->addQuery('*');
$q->addTable('risks');
$q->addWhere('risk_id = ' . $risk_id);

if (!db_loadHash( $q->prepare(), $risk ) && $risk_id > 0) {
	$titleBlock = new CTitleBlock( 'Invalid Risk ID', 'folder5.png', $m, "$m.$a" );
	$titleBlock->addCrumb( "?m=risks", "Risks list" );
	$titleBlock->show();
	
	$tasks = array();
} else {
	if (isset($risk['risk_project']))
	{
		$q->clear();
		$q->addQuery('task_id, task_name');
		$q->addTable('tasks');
		//$q->addWhere('task_project = ' . $risk['risk_project']);
		$tasks = $q->loadHashList();
	}
	else
		$tasks = array();
	
// setup the title block
	$ttl = $risk_id > 0 ? "Edit Risk" : "Add Risk";
	$titleBlock = new CTitleBlock( $ttl, 'folder5.png', $m, "$m.$a" );
	$titleBlock->addCrumb( "?m=risks", "risks list" );
	$titleBlock->addCrumb( "?m=risks&a=addedit&risk_id=$risk_id", "edit risk" );
	if ($canDelete) {
		$titleBlock->addCrumbRight(
			'<a href="javascript:delIt()">'
				. '<img align="absmiddle" src="' . dPfindImage( 'trash.gif', $m ) . '" width="16" height="16" alt="" border="0" />&nbsp;'
				. $AppUI->_('delete risk') . '</a>'
		);
	}
	$titleBlock->show();
?>
<script language="javascript">
function delIt(){
	var form = document.editrisk;
	if(confirm( "<?php echo $AppUI->_('risksDelete');?>" )) {
		form.del.value = "<?php echo $risk_id;?>";
		form.submit();
	}
}
</script>
<form name="editrisk" action="?m=risks" method="post">
	<input type="hidden" name="dosql" value="do_risk_aed" />
	<input type="hidden" name="del" value="0" />
	<input type="hidden" name="risk_id" value="<?php echo $risk_id;?>" />
</form>
<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">
<tr>
	<td width="50%">
		<table width="100%" cellspacing="1" cellpadding="2">
		<tr>
			<td nowrap="nowrap" colspan=2><strong><?php echo $AppUI->_('Details');?></strong></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Risk Name');?>:</td>
			<td class="hilite"><?php echo $risk["risk_name"];?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Description');?>:</td>
			<td class="hilite"><?php echo $risk["risk_description"];?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Probability');?>:</td>
			<td class="hilite"><?php echo $risk["risk_probability"]; ?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Impact');?>:</td>
			<td class="hilite"><?php echo $risk['risk_impact']; ?> <?php echo $riskDuration[$risk['risk_duration_type']]; ?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Status');?>:</td>
			<td class="hilite"><?php echo $riskStatus[$risk["risk_status"]];?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Owner');?>:</td>
			<td class="hilite"><?php echo $users[$risk["risk_owner"]];?></td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Project');?>:</td>
			<td class="hilite">
				<a href="?m=projects&a=view&project_id=<?php echo $risk['risk_project']; ?>">
					<?php	echo $projects[$risk["risk_project"]];?>
				</a>
			</td>
		</tr>
		<tr>
			<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Task');?>:</td>
			<td class="hilite">
				<a href="?m=tasks&a=view&task_id=<?php echo $risk['risk_task']; ?>">
					<?php echo $tasks[$risk['risk_task']]; ?>
				</a>
			</td>
    </tr>
  	</table>
  </td>
  <td width="50%" valign="top">
		<table cellspacing="1" cellpadding="2" border="0" width="100%">
  <tr><td></td></tr>
  <tr>
  	<td>
		<?php echo $AppUI->_('Notes');?>:<br />
		</td>
	</tr>
	<tr>
		<td class="hilite">
		<?php echo $risk["risk_notes"];?>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>


<?php 
//include($baseDir . '/modules/risks/notes.php');
//$query_string = "?m=tasks&a=view&task_id=$task_id";
// Last parameter makes tab box javascript based.
$tab = dPgetParam($_GET, 'tab', 0);
$tabBox = new CTabBox( "?m=risks&a=view&risk_id=$risk_id", "", $tab, '' );

global $tab;
$tabBox->add( "{$dPconfig['root_dir']}/modules/risks/vw_notes", 'Risk Notes' );
$tabBox->add( "{$dPconfig['root_dir']}/modules/risks/vw_note_add", 'Add Risk Note' );

$tabBox->loadExtras($m, $a);
$tabBox->show('', true);
} ?>