<?php /* COMPANIES $Id$ */
##
##	Companies: View Archived Projects sub-table
##
GLOBAL $AppUI, $company_id, $tpl; 

$q  = new DBQuery;
$q->addTable('projects');
$q->addQuery('project_id, project_name, project_start_date, project_status, project_target_budget,
	project_start_date,
        project_priority,
	contact_first_name, contact_last_name');
$q->addJoin('users', 'u', 'u.user_id = projects.project_owner');
$q->addJoin('contacts', 'con', 'u.user_contact = con.contact_id');
$q->addWhere('projects.project_company = '.$company_id);
$q->addWhere('projects.project_active = 0');
$q->addOrder('project_name');

$tpl->assign('msg', $AppUI->getMsg());
$tpl->assign('current_url', 'index.php?m=companies&a=view&company_id=' . $company_id);

//$smarty->assign('pstatus', $pstatus);
$show = array('project_name', 'project_owner');

$tpl->displayList('projects', $q->loadList(), $show);
?>