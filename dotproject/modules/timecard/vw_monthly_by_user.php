<?php /* HELPDESK $Id: vw_weekly_by_project.php,v 1.1 2007/08/22 07:47:47 arcoz67 Exp $ */
	global $tab, $TIMECARD_CONFIG;

    $show_other_worksheets = $TIMECARD_CONFIG['minimum_see_level']>=$AppUI->user_type;

	if (isset( $_GET['month'] )) {
		$AppUI->setState( 'TimecardMonthlyMonth', $_GET['month'] );
	} else {
        $current_date = new CDate();
        $AppUI->setState( 'TimecardMonthlyMonth', $current_date->getMonth() );
    }
    $month = $AppUI->getState('TimecardMonthlyMonth');

    if ($show_other_worksheets) {
        if (isset( $_GET['user_id'] )) {
            $AppUI->setState( 'TimecardMonthlyUserId', $_GET['user_id'] );
        } else {
            $AppUI->setState( 'TimecardMonthlyUserId', $AppUI->user_id );
        }
        $user_id = $AppUI->getState( 'TimecardMonthlyUserId' ) ? $AppUI->getState( 'TimecardMonthlyUserId' ) : 0;
    } else {
        $user_id = $AppUI->user_id;
    }

    $start_report = new CDate();
    $start_report->setMonth($month);
    $start_report->setDay(1);
    $end_report = new CDate();
    $end_report->copy($start_report);
    $end_report->setMonth($month+1);
    $end_report->addDays(-1);

	//Get hash of users
	$sql = "SELECT user_id, contact_email, concat(contact_last_name,' ',contact_first_name) as name FROM users LEFT JOIN contacts AS c ON users.user_contact = contact_id ORDER BY contact_last_name, contact_first_name;";
	$result = db_loadList($sql);	
	$people = array();

    // users list
	foreach($result as $row){
		$people[$row['user_id']] = $row;
		$users[$row['user_id']] = $row['name'];
	}
	unset($result);

    $sql = "
        select distinct(project_id), project_name from task_log
        left join tasks on tasks.task_id = task_log.task_log_task
        left join projects on projects.project_id = tasks.task_project
        where
            task_log_date >= '" . $start_report->format( FMT_DATETIME_MYSQL ) . "'
        and task_log_date <= '" . $end_report->format( FMT_DATETIME_MYSQL ) . "'
        and task_log_task != 0
        and task_log_creator = " . $user_id . "
        order by project_name
		";

    $projects = array();
    $result = db_loadList($sql);
    foreach($result as $row){
        $projects[$row['project_id']] = $row['project_name'];
    }
	unset($result);

    foreach($projects as $id => $project_name) {
        $sql = "
        select sum(task_log.task_log_hours) as sum, task_log.task_log_date from task_log
            left join tasks on tasks.task_id = task_log.task_log_task
            left join projects on projects.project_id = tasks.task_project
        where
            task_log_date >= '" . $start_report->format( FMT_DATETIME_MYSQL ) . "'
        and task_log_date <= '" . $end_report->format( FMT_DATETIME_MYSQL ) . "'
        and task_log_task != 0
        and task_log_creator = " . $user_id . "
        and project_id = " . $id . "
        group by date(task_log_date)
        order by task_log_date
        ";

        $result = db_loadList($sql);
        foreach($result as $row){
            $date_insert = new CDate($row['task_log_date']);
            $projects_hours[$id][$date_insert->format('%Y-%m-%d')] = $row['sum'];
            $user_by_day[$date_insert->format('%Y-%m-%d')] += $projects_hours[$id][$date_insert->format('%Y-%m-%d')];
        }
        unset($result);

    }

    $months = array(
		'1' => $AppUI->_('January'),
        '2' => $AppUI->_('February'),
        '3' => $AppUI->_('March'),
        '4' => $AppUI->_('April'),
        '5' => $AppUI->_('Mai'),
        '6' => $AppUI->_('June'),
        '7' => $AppUI->_('July'),
        '8' => $AppUI->_('August'),
        '9' => $AppUI->_('September'),
        '10' => $AppUI->_('October'),
        '11' => $AppUI->_('November'),
        '12' => $AppUI->_('December'),
		);

?>

<form name="frmSelect" action="" method="get">
	<input type="hidden" name="m" value="timecard">
	<input type="hidden" name="report_type" value="monthly_by_user">
	<input type="hidden" name="tab" value="<?php echo $tab; ?>">
	<table cellspacing="1" cellpadding="2" border="0" width="100%">
	<tr>
        <?php if($show_other_worksheets){ ?>
        <td><?php echo $AppUI->_('User'); ?>:</td>
		<td width="1%" valign="top" nowrap="nowrap">
        <?php echo arraySelect( $users, 'user_id', 'size="1" class="text" id="medium" onchange="document.frmSelect.submit()"', $user_id )?>
        </td>
        <?php } ?>
        <td><?php echo $AppUI->_('Date'); ?></td>
		<td width="98%" align="left" valign="top">
        <?php echo arraySelect( $months, 'month', 'size="1" class="text" id="medium" onchange="document.frmSelect.submit()"', $month )?>
		</td>
	</tr>
	</table>
</form>
<table cellspacing="1" cellpadding="2" border="0" class="std" width="100%">
<tr>
    <th width="150px"><?php echo $AppUI->_('Project')?></th>
    <th width="50px"><?php echo $AppUI->_('Days:')?></th>
<?php
	for($i=1;$i<=$start_report->getDaysInMonth();$i++){
?>
	<th width="20px"><?php echo $i?></th>
<?php
	}
?>
    <th width="50px"><?php echo $AppUI->_('Total')?>:</th>
</tr>

<?php
    $total_sum = 0;
	foreach($projects as $id => $project_name) {
?>
<tr>
	<td style="background:#8AC6FF; text-align:right;"><?php echo $project_name?></td>
    <td style="background:#8AC6FF;">&nbsp;</td>
<?php
    $date = new CDate($start_report);
    $sum_project = 0;
    
	for($i=0;$i<$start_report->getDaysInMonth();$i++) {

    if ($date->isWorkingDay()) {
    ?>
        <td style="background:#8AC6FF;text-align:center;" align="right">
    <?php
    } else {
    ?>
        <td style="background:#EF95CB;text-align:center;" align="right">
    <?php
    }
?>	
        <?php
            if (isset($projects_hours[$id][$date->format('%Y-%m-%d')])) {
                printf("%.1f",$projects_hours[$id][$date->format('%Y-%m-%d')]);
                $sum_project += $projects_hours[$id][$date->format('%Y-%m-%d')];
            }
            $date->addDays(1);
        ?>
    </td>
<?php
	}
?>
    <td style="text-align:center;"><strong><?php printf("%.1f",$sum_project); ?></strong></td>
</tr>
<?php
    $total_sum += $sum_project;
    }
?>
    <tr>
        <td>&nbsp;</th>
        <td style="text-align:center; font-weight:bold;"><?php echo $AppUI->_('Total'); ?>:</td>
<?php
    $date = new CDate($start_report);
	for($i=1;$i<=$start_report->getDaysInMonth();$i++){
?>
	<td style="text-align:center; font-weight:bold; "><?php echo sprintf("%.1f",$user_by_day[$date->format('%Y-%m-%d')]); ?></td>
<?php
        $date->addDays(1);
	}
?>
    <td style="text-align:center;"><strong> <?php echo sprintf("%.1f",$total_sum); ?></strong></td>
    </tr>
</table>
	
	
