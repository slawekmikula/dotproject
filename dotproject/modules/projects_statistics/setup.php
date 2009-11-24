<?php
$config = array(
	'mod_name' => 'Projects Statistics',
	'mod_version' => '1.0',
	'mod_directory' => 'projects_statistics',
	'mod_setup_class' => 'SProjectsStatistics',
	'mod_type' => 'user',
	'mod_ui_name' => 'Project_Statistics',
	'mod_ui_icon' => '',
	'mod_description' => 'Statistics for project',
	'permissions_item_table' => 'project_statistics',
	'permissions_item_field' => 'project_statistics_id',
	'permissions_item_label' => 'project_statistics_name'
);

if (@$a == 'setup') {
	echo dPshowModuleConfig($config);
}

class SProjectsStatistics {
	function install() {
		$ok = true;
		$q = new DBQuery;
		$sql = "(
			project_statistics_id integer not null auto_increment,
            project_statistics_project_id integer not null,
            project_statistics_project_version_id integer not null,
            project_statistics_project_finish_date datetime,
            project_statistics_timestamp datetime not null,
            project_statistics_percent_complete float default '0',
            project_statistics_tasks_total integer not null,
            project_statistics_tasks_complete integer not null,
            project_statistics_helpdesk_total integer not null,
            project_statistics_helpdesk_bugs integer not null,
            project_statistics_helpdesk_features integer not null,
            project_statistics_helpdesk_suggestions integer not null,
            project_statistics_helpdesk_issues integer not null,
            project_statistics_helpdesk_total_closed integer not null,
            project_statistics_helpdesk_bugs_closed integer not null,
            project_statistics_helpdesk_features_closed integer not null,
            project_statistics_helpdesk_suggestions_closed integer not null,
            project_statistics_helpdesk_issues_closed integer not null,
            project_statistics_helpdesk_bugs_testing integer not null,
            project_statistics_helpdesk_features_testing integer not null,
            project_statistics_helpdesk_suggestions_testing integer not null,
            project_statistics_helpdesk_issues_testing integer not null,
			primary key (project_statistics_id),
			key (project_statistics_project_id),
			key (project_statistics_timestamp)
		)";
		
		$q->createTable('projects_statistics');
		$q->createDefinition($sql);
		$ok = $ok && $q->exec();
		$q->clear();
		
		if (!$ok) {
			return false;
        }

		return null;
	}

	function remove() {
		$q = new DBQuery;
		$q->dropTable('projects_statistics');
		$q->exec();
		$q->clear();

		return null;
	}

	function upgrade($old_version) {
		switch ($old_version) {
			case "1.0":
				break;
		}
		return true;
	}
}

?>
