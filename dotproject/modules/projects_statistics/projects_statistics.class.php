<?php
// modified resources class

require_once $AppUI->getSystemClass('dp');
require_once $AppUI->getSystemClass('query');
require_once $AppUI->getSystemClass('date');


class CProjectsStatistics extends CDpObject {
  var $project_statistics_project_id = null;
  var $project_statistics_project_version_id = null; // FIXME
  var $project_statistics_project_finish_date = null;
  var $project_statistics_percent_complete = null;
  var $project_statistics_tasks_total = null;
  var $project_statistics_tasks_complete = null; 
  var $project_statistics_helpdesk_total = null;
  var $project_statistics_helpdesk_total_closed = null;

  var $project_statistics_helpdesk_bugs = null;
  var $project_statistics_helpdesk_features = null;
  var $project_statistics_helpdesk_suggestions = null;
  var $project_statistics_helpdesk_issues = null;

  var $project_statistics_helpdesk_bugs_closed = null;
  var $project_statistics_helpdesk_features_closed = null;
  var $project_statistics_helpdesk_suggestions_closed = null;
  var $project_statistics_helpdesk_issues_closed = null;

  var $project_statistics_helpdesk_bugs_testing = null;
  var $project_statistics_helpdesk_features_testing = null;
  var $project_statistics_helpdesk_suggestions_testing = null;
  var $project_statistics_helpdesk_issues_testing = null;

  function CProjectsStatistics() {
    parent::CDpObject('projects_statistics', 'projects_statistics_id');
  }

  /** create project statistics for given date */
  function createStatistics()
  {
    $final = true;

    $query = new DBQuery;
    $query->addTable("projects", 'p');
    $query->addQuery('p.project_id, p.project_end_date, p.project_percent_complete');

    $projects =& $query->exec();


    for ($projects; ! $projects->EOF; $projects->MoveNext()) {

        // stan procentowy projektu
        $project_statistics_project_id = $projects->fields['project_id'];
        $project_statistics_project_finish_date = $projects->fields['project_end_date'];
        $project_active = $projects->fields['project_active'];

        $working_hours = ((dPgetConfig('daily_working_hours'))
                          ? dPgetConfig('daily_working_hours'):8);

        $q = new DBQuery;
        $q->addTable('projects');
        $q->addQuery(' SUM(t1.task_duration * t1.task_percent_complete'
                     . ' * IF(t1.task_duration_type = 24, ' . $working_hours
                     . ', t1.task_duration_type)) / SUM(t1.task_duration'
                     . ' * IF(t1.task_duration_type = 24, ' . $working_hours
                     . ', t1.task_duration_type)) AS project_percent_complete');
        $q->addJoin('tasks', 't1', 'projects.project_id = t1.task_project');
        $q->addWhere('project_id = ' . $project_statistics_project_id . ' AND t1.task_id = t1.task_parent');
        $result =& $q->exec();
        $project_statistics_percent_complete = $result->fields['project_percent_complete'];
        $q->clear();

        // jesli projekt nie aktywny lub ma oznaczone 100% to pomijamy
        if ($project_active == 0 || $project_statistics_percent_complete == 100) {
            continue;
        }

        // zadania
            // ilosc zadan w projekcie
            $q->addTable('tasks','t');
            $q->addQuery('count(t.task_id) as task_total');
            $q->addWhere('t.task_project =' . $project_statistics_project_id);
            $result =& $q->exec();
            $project_statistics_tasks_total = $result->fields['task_total'];
            $q->clear();

            // ilosc zakonczonych zadan w projekcie
            $q->addTable('tasks','t');
            $q->addQuery('count(task_id) as task_completed');
            $q->addWhere('t.task_project =' . $project_statistics_project_id);
            $q->addWhere('task_percent_complete = 100'); // zakonczone
            $result =& $q->exec();
            $project_statistics_tasks_complete = $result->fields['task_completed'];
            $q->clear();

        // liczba zgloszen na helpdesku
        
            // wszystkie
            $q->addTable('helpdesk_items','hi');
            $q->addQuery('count(item_id) as helpdesk_bugs_count');
            $q->addWhere('hi.item_project_id =' . $project_statistics_project_id);
            $result =& $q->exec();
            $project_statistics_helpdesk_total = $result->fields['helpdesk_bugs_count'];
            $q->clear();           

            // zamkniete
            $q->addTable('helpdesk_items','hi');
            $q->addQuery('count(item_id) as helpdesk_bugs_closed');
            $q->addWhere('hi.item_project_id =' . $project_statistics_project_id);
            $q->addWhere('hi.item_status = 2'); // zamkniete
            $result =& $q->exec();
            $project_statistics_helpdesk_total_closed = $result->fields['helpdesk_bugs_closed'];
            $q->clear();

            // otwarte
                // bug
                $q->addTable('helpdesk_items','hi');
                $q->addQuery('count(item_id) as helpdesk_bugs');
                $q->addWhere('hi.item_project_id =' . $project_statistics_project_id);
                $q->addWhere('hi.item_calltype = 1'); // bug
                $q->addWhere('hi.item_status = 1'); // otwarte
                $result =& $q->exec();
                $project_statistics_helpdesk_bugs = $result->fields['helpdesk_bugs'];
                $q->clear();

                // issue
                $q->addTable('helpdesk_items','hi');
                $q->addQuery('count(item_id) as helpdesk_issues');
                $q->addWhere('hi.item_project_id =' . $project_statistics_project_id);
                $q->addWhere('hi.item_calltype = 3'); // issue
                $q->addWhere('hi.item_status = 1'); // otwarte
                $result =& $q->exec();
                $project_statistics_helpdesk_issues = $result->fields['helpdesk_issues'];
                $q->clear();

                // feature
                $q->addTable('helpdesk_items','hi');
                $q->addQuery('count(item_id) as helpdesk_features');
                $q->addWhere('hi.item_project_id =' . $project_statistics_project_id);
                $q->addWhere('hi.item_calltype = 2'); // feature
                $q->addWhere('hi.item_status = 1'); // otwarte
                $result =& $q->exec();
                $project_statistics_helpdesk_features = $result->fields['helpdesk_features'];
                $q->clear();

                // sugesstion
                $q->addTable('helpdesk_items','hi');
                $q->addQuery('count(item_id) as helpdesk_suggestions');
                $q->addWhere('hi.item_project_id =' . $project_statistics_project_id);
                $q->addWhere('hi.item_calltype = 4'); // sugestia
                $q->addWhere('hi.item_status = 1'); // otwarte
                $result =& $q->exec();
                $project_statistics_helpdesk_suggestions = $result->fields['helpdesk_suggestions'];
                $q->clear();

            // w testach
                // bug
                $q->addTable('helpdesk_items','hi');
                $q->addQuery('count(item_id) as helpdesk_bugs');
                $q->addWhere('hi.item_project_id =' . $project_statistics_project_id);
                $q->addWhere('hi.item_calltype = 1'); // bug
                $q->addWhere('hi.item_status = 4'); // w testach
                $result =& $q->exec();
                $project_statistics_helpdesk_bugs_testing = $result->fields['helpdesk_bugs'];
                $q->clear();

                // issue
                $q->addTable('helpdesk_items','hi');
                $q->addQuery('count(item_id) as helpdesk_issues');
                $q->addWhere('hi.item_project_id =' . $project_statistics_project_id);
                $q->addWhere('hi.item_calltype = 3'); // issue
                $q->addWhere('hi.item_status = 4'); // w testach
                $result =& $q->exec();
                $project_statistics_helpdesk_issues_testing = $result->fields['helpdesk_issues'];
                $q->clear();

                // feature
                $q->addTable('helpdesk_items','hi');
                $q->addQuery('count(item_id) as helpdesk_features');
                $q->addWhere('hi.item_project_id =' . $project_statistics_project_id);
                $q->addWhere('hi.item_calltype = 2'); // feature
                $q->addWhere('hi.item_status = 4'); // w testach
                $result =& $q->exec();
                $project_statistics_helpdesk_features_testing = $result->fields['helpdesk_features'];
                $q->clear();

                // sugesstion
                $q->addTable('helpdesk_items','hi');
                $q->addQuery('count(item_id) as helpdesk_suggestions');
                $q->addWhere('hi.item_project_id =' . $project_statistics_project_id);
                $q->addWhere('hi.item_calltype = 4'); // sugestia
                $q->addWhere('hi.item_status = 4'); // w testach
                $result =& $q->exec();
                $project_statistics_helpdesk_suggestions_testing = $result->fields['helpdesk_suggestions'];
                $q->clear();

            // zamkniete
                // bug
                $q->addTable('helpdesk_items','hi');
                $q->addQuery('count(item_id) as helpdesk_bugs');
                $q->addWhere('hi.item_project_id =' . $project_statistics_project_id);
                $q->addWhere('hi.item_calltype = 1'); // bug
                $q->addWhere('hi.item_status = 2'); // zamkniete
                $result =& $q->exec();
                $project_statistics_helpdesk_bugs_closed = $result->fields['helpdesk_bugs'];
                $q->clear();

                // issue
                $q->addTable('helpdesk_items','hi');
                $q->addQuery('count(item_id) as helpdesk_issues');
                $q->addWhere('hi.item_project_id =' . $project_statistics_project_id);
                $q->addWhere('hi.item_calltype = 3'); // issue
                $q->addWhere('hi.item_status = 2'); // zamkniete
                $result =& $q->exec();
                $project_statistics_helpdesk_issues_closed = $result->fields['helpdesk_issues'];
                $q->clear();

                // feature
                $q->addTable('helpdesk_items','hi');
                $q->addQuery('count(item_id) as helpdesk_features');
                $q->addWhere('hi.item_project_id =' . $project_statistics_project_id);
                $q->addWhere('hi.item_calltype = 2'); // feature
                $q->addWhere('hi.item_status = 2'); // zamkniete
                $result =& $q->exec();
                $project_statistics_helpdesk_features_closed = $result->fields['helpdesk_features'];
                $q->clear();

                // sugesstion
                $q->addTable('helpdesk_items','hi');
                $q->addQuery('count(item_id) as helpdesk_suggestions');
                $q->addWhere('hi.item_project_id =' . $project_statistics_project_id);
                $q->addWhere('hi.item_calltype = 4'); // sugestia
                $q->addWhere('hi.item_status = 2'); // zamkniete
                $result =& $q->exec();
                $project_statistics_helpdesk_suggestions_closed = $result->fields['helpdesk_suggestions'];
                $q->clear();

        // zapisanie do tabeli
        $today = new CDate();
        $q->addTable('projects_statistics','ps');
        $q->addInsert('project_statistics_timestamp', $today->format(FMT_DATETIME_MYSQL));

        $q->addInsert('project_statistics_project_id', $project_statistics_project_id);
        $q->addInsert('project_statistics_project_finish_date', $project_statistics_project_finish_date);
        $q->addInsert('project_statistics_percent_complete', $project_statistics_percent_complete);
        $q->addInsert('project_statistics_tasks_total', $project_statistics_tasks_total);
        $q->addInsert('project_statistics_tasks_complete', $project_statistics_tasks_complete);
        $q->addInsert('project_statistics_helpdesk_total', $project_statistics_helpdesk_total);
        $q->addInsert('project_statistics_helpdesk_total_closed', $project_statistics_helpdesk_total_closed);

        $q->addInsert('project_statistics_helpdesk_bugs', $project_statistics_helpdesk_bugs);
        $q->addInsert('project_statistics_helpdesk_features', $project_statistics_helpdesk_features);
        $q->addInsert('project_statistics_helpdesk_suggestions', $project_statistics_helpdesk_suggestions);
        $q->addInsert('project_statistics_helpdesk_issues', $project_statistics_helpdesk_issues);

        $q->addInsert('project_statistics_helpdesk_bugs_closed', $project_statistics_helpdesk_bugs_closed);
        $q->addInsert('project_statistics_helpdesk_features_closed', $project_statistics_helpdesk_features_closed);
        $q->addInsert('project_statistics_helpdesk_suggestions_closed', $project_statistics_helpdesk_suggestions_closed);
        $q->addInsert('project_statistics_helpdesk_issues_closed', $project_statistics_helpdesk_issues_closed);

        $q->addInsert('project_statistics_helpdesk_bugs_testing', $project_statistics_helpdesk_bugs_testing);
        $q->addInsert('project_statistics_helpdesk_features_testing', $project_statistics_helpdesk_features_testing);
        $q->addInsert('project_statistics_helpdesk_suggestions_testing', $project_statistics_helpdesk_suggestions_testing);
        $q->addInsert('project_statistics_helpdesk_issues_testing', $project_statistics_helpdesk_issues_testing);

        $q->exec();
    }
    return $final;
  }

}

?>
