<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
$AppUI->savePlace();

if (isset( $_GET['where'] )) {
	$AppUI->setState( 'ContIdxWhere', $_GET['where'] );
}
$where = $AppUI->getState( 'ContIdxWhere' ) ? $AppUI->getState( 'ContIdxWhere' ) : '%';

$riskStatus = dPgetSysVal( 'RiskStatus' );
$riskStatus[0] = 'All Risks';
$riskFilter = dPgetParam( $_POST, 'riskFilter', 0 );
$durnTypes = array(1=>'Hours', 24=>'Days', 168=>'Weeks');

$order = dPgetParam($_GET, 'sort', 'risk_name');

// get CCompany() to filter risks by company
//require_once( $AppUI->getModuleClass( 'companies' ) );
//$obj = new CCompany();
//$companies = $obj->getAllowRecords( $AppUI->user_id, 'company_id,company_name', 'company_name');
//$filters = arrayMerge( array( 'all' => $AppUI->_('All Companies') ), $companies );

$q = new DBQuery();
$q->addQuery('user_id');
$q->addQuery('CONCAT( contact_first_name, \' \', contact_last_name)');
$q->addTable('users');
$q->leftJoin('contacts', 'c', 'user_contact = contact_id');
$q->addOrder('contact_first_name, contact_last_name');
$users = $q->loadHashList();

$q->clear();
$q->addQuery('project_id');
$q->addQuery('project_name');
$q->addTable('projects');
$projects = $q->loadHashList();

// setup the title block
$titleBlock = new CTitleBlock( 'Risks', 'scales.png', $m, "$m.$a" );
$perms = & $AppUI->acl();
// Use permissions check directly rather than $canEdit, because this 
// file can be included by other modules, in which case the $canEdit will
// have a different context.
if ($perms->checkModule('risks', 'add')) {
	$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('new risk').'">', '',
		'<form action="?m=risks&a=addedit" method="post">', '</form>'
	);
	$titleBlock->addCell(
		arraySelect( $riskStatus, 'riskFilter', 'size=1 class=text onChange="document.riskFilter.submit();"', $riskFilter, true ), '',
		'<form action="?m=risks" method="post" name="riskFilter">', '</form>'
	);
}
$titleBlock->show();
$x = $_GET['project_id'];
$y = $AppUI->user_id;

$q->clear();
$q->addQuery('*');
$q->addTable('projects');
if(isset($_GET['project_id'])) {
	$q->addWhere('project_id = ' . $x);
}	
	
$projects = $q->loadList();
//--- print Table Headers ----//
?>
<TABLE BORDER="0" WIDTH="100%" CELLSPACING="1" CELLPADDING="2" CLASS="tbl">
	<TR bgcolor="#99CCFF" ALIGN="CENTER" VALIGN="TOP">
		<TH ALIGN="center" VALIGN="top"></TH>
		<TH ALIGN="center" VALIGN="top" class="hdr">ID</TH>
		<TH ALIGN="center" VALIGN="top" class="hdr"><a href="?m=risks&sort=task_name">Task</a></TH>
		<TH ALIGN="center" VALIGN="top" class="hdr"><a href="?m=risks&sort=risk_name">Name</a></TH>
		<TH ALIGN="center" VALIGN="top" class="hdr"><a href="?m=risks&sort=risk_probability">Probability</a></TH>
		<TH ALIGN="center" VALIGN="top" class="hdr"><a href="?m=risks&sort=risk_impact">Impact</a></TH>
		<TH ALIGN="center" VALIGN="top" class="hdr"><a href="?m=risks&sort=risk_owner">Owner</a></TH>
		<TH ALIGN="center" VALIGN="top" class="hdr"><a href="?m=risks&sort=risk_status">Status</a></TH>
		<TH ALIGN="center" VALIGN="top" class="hdr"><a href="?m=risks&sort=risk_note_date">Last Note</a></TH>
	</TR>
<?php

$projects = array_merge(array(0=>array('project_id'=>0)), $projects);

	foreach($projects as $p){	
		$q->clear();
		$q->addQuery('risks.*');
		$q->addQuery('max(risk_note_date) as risk_note_date');
		$q->addQuery('task_name');
		$q->addTable('risks');
		$q->leftJoin('risk_notes', 'r', 'risk_id = risk_note_risk');
		$q->leftJoin('tasks', 't', 'task_id = risk_task');
		$q->addWhere('risk_project = ' . $p['project_id']);
		$q->addGroup('risk_id');
		$q->addOrder($order);

		if($riskFilter != 0) {
			$q->addWhere('risk_status = ' . $riskFilter);
		}

		$risks = $q->loadList();
		
		if(!empty($risks)){
//			if(!isset($_GET['project_id'])){
				echo '<TR><TD colspan="12" style="background-color:#' . $p['project_color_identifier'] . '">
					<a href="?m=projects&a=view&project_id=' . $p['project_id'] . '">
						<font color="' . bestColor( $p["project_color_identifier"] ) . '">' . $p['project_name'] . '</font>&nbsp</a>
					</TD></TR>';	
//		  	}
			//---- Print Table Data ----//
			foreach($risks as $row){
				if (!$row['task_name'])
					$row['task_name'] = "No task specified";

				if($row['risk_status'] == 2){
					$bg = "669999";
				} else {
					$bg = "";
				}
				$row['risk_status'] = $riskStatus[$row['risk_status']];
				
				foreach ($users as $k => $v ) {
					if($k==$row['risk_owner']){
						$row['risk_owner'] = $v;
					}
				}
				foreach ($projects as $k => $v ) {
					if($k==$row['risk_project']){
						$row['risk_project'] = $v;
					}
				}
				
				?>
				<TR style="background-color:#<?php echo $bg; ?>">
				<TD nowrap>
					<a href="./index.php?m=risks&a=addedit&risk_id=<? echo($row['risk_id']) ?>">
						<img src="./images/icons/pencil.gif" border="0" width="12" height="12">
					</a>
				</TD>
				<TD ><?php echo($row['risk_id']); ?>&nbsp</TD>
				<TD><?php echo($row['task_name']); ?>&nbsp</TD>
				<TD>
					<a href="?m=risks&a=view&risk_id=<?php echo $row['risk_id']; ?>">
						<?php echo($row['risk_name']); ?>
					</a> &nbsp
				</TD>
				<TD><?php echo($row['risk_probability']); ?>% &nbsp</TD>
				<TD><?php echo $row['risk_impact'] . ' ' . $durnTypes[$row['risk_duration_type']]; ?> &nbsp</TD>
				<TD><?php echo($row['risk_owner']); ?> &nbsp</TD>
				<TD><?php echo($row['risk_status']); ?> &nbsp</TD>
				<TD><?php echo($row['risk_note_date']); ?> &nbsp</TD>
				</TR>
				<?
			}
			//print spacer row
			print"<tr><TD COLSPAN='12'>&nbsp</TD></tr>";	
		}
}
?>
</table>
