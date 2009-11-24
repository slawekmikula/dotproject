<?php 
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
// Add / Edit contact
$risk_id = intval( dPgetParam( $_REQUEST, 'risk_id', 0 ) );

// check permissions
$denyEdit = getDenyEdit( $m, $risk_id );

if ($denyEdit) {
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

$q->clear();
$q->addQuery('project_id, project_name');
$q->addTable('projects');
$projects = $q->loadHashList();
$projects[0] = '[All]';

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
		$q->addWhere('task_project = ' . $risk['risk_project']);
		$tasks = $q->loadHashList();
	}
	else
		$tasks = array();
	
// setup the title block
	$ttl = $risk_id > 0 ? "Edit Risk" : "Add Risk";
	$titleBlock = new CTitleBlock( $ttl, 'folder5.png', $m, "$m.$a" );
	$titleBlock->addCrumb( "?m=risks", "risks list" );
	$titleBlock->addCrumb( "?m=risks&a=view&risk_id=$risk_id", "View Risk" );
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
	var form = document.changecontact;
	if(confirm( "<?php echo $AppUI->_('risksDelete');?>" )) {
		form.del.value = "<?php echo $risk_id;?>";
		form.submit();
	}
}

function updateTasks()
	{
		var proj = document.forms['editrisk'].risk_project.value;
		var tasks = new Array();
		var sel = document.forms['editrisk'].risk_task;
		while ( sel.options.length )
			sel.options[0] = null;
		sel.options[0] = new Option('loading...', -1);
		frames['thread'].location.href = './index.php?m=tasks&a=listtasks&project=' + proj + '&form=editrisk&taskfield=risk_task';
	}
</script>

<table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">

<form name="editrisk" action="?m=risks" method="post">
	<input type="hidden" name="dosql" value="do_risk_aed" />
	<input type="hidden" name="del" value="0" />
	<input type="hidden" name="contact_project" value="0" />
	<input type="hidden" name="risk_id" value="<?php echo $risk_id;?>" />
		<tr>
			<td align="right"><?php echo $AppUI->_('Risk Name');?>:</td>
			<td>
				<input type="text" class="text" size="75" name="risk_name" value="<?php echo @$risk["risk_name"];?>" maxlength="50">
			</td>
		</tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Description');?>:</td>
			<td>
				<textarea cols="73" rows="6" class="textarea" name="risk_description"><?php echo @$risk["risk_description"];?></textarea>
			</td>
		</tr>
		<tr>
			<td align="right"><?php echo $AppUI->_('Probability');?>:</td>
			<td>
				<input type="text" name="risk_probability" value="
				<?php
				echo @$risk["risk_probability"];
				?>
				" />%
			</td>
		</tr>
		<tr>
			<td align="right"><?php echo $AppUI->_('Impact');?>:</td>
			<td>
				<input type="text" name="risk_impact" value="<?php echo $risk['risk_impact']; ?>" /> x 
				<?php echo arraySelect( $riskDuration, 'risk_duration_type', 'size="1" class="text"', @$risk['risk_duration_type'] ); ?>
			</td>
		</tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Status');?>:</td>
			<td>
				<?php
				echo arraySelect( $riskStatus, 'risk_status', 'size="1" class="text"', @$risk["risk_status"] );
				?>
			</td>
		</tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Owner');?>:</td>
			<td>
				<?php
				echo arraySelect( $users, 'risk_owner', 'size="1" class="text"', ($risk["risk_owner"]?$risk['risk_owner']:$AppUI->user_id) );
				?>
			</td>
		</tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Project');?>:</td>
			<td>
				<?php
				echo arraySelect( $projects, 'risk_project', 'size="1" class="text" onChange="updateTasks();"', (!empty($risk["risk_project"])?$risk["risk_project"]:'0') );
				?>
			</td>
		</tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Task');?>:</td>
			<td>
      	<?php echo arraySelect($tasks, 'risk_task', 'size="1" class="text"', $risk['risk_task']); ?>
    	</td>
    </tr>
		<tr>
			<td align="right">&nbsp;&nbsp;<?php echo $AppUI->_('Notes');?>:</td>
			<td>
				<textarea cols="73" rows="6" class="textarea" name="risk_notes"><?php echo @$risk["risk_notes"];?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<input class="text" type="submit" value="back">
			</td>
			<td align="right">
				<input class="text" type="submit" value="submit">
			</td>
		</tr>
</form>
</table>


<?php } ?>