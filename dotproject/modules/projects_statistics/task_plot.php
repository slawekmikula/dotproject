<?
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

global $AppUI;

include ($AppUI->getLibraryClass('jpgraph/src/jpgraph'));
include ($AppUI->getLibraryClass('jpgraph/src/jpgraph_line'));
include ($AppUI->getLibraryClass('jpgraph/src/jpgraph_date'));

// get the prefered date format
$df = $AppUI->getPref('SHDATEFORMAT');
$project_id = dPgetParam($_GET, 'project_id', 0);

$q = new DBQuery();
$q->addTable('projects');
$q->addQuery('project_start_date, project_end_date');
$q->addWhere('project_id = ' . $project_id);
$result =& $q->exec();
$start_date = new CDate($result->fields['project_start_date']);
$end_date = new CDate($result->fields['project_end_date']);
$current_date = new CDate();
$q->clear();

$q->addTable('projects_statistics','ps');
$q->addQuery('project_statistics_timestamp, project_statistics_project_id, ' .
    'project_statistics_project_finish_date, project_statistics_percent_complete,' .
    'project_statistics_tasks_total, project_statistics_tasks_complete, ' .
    'project_statistics_helpdesk_total, project_statistics_helpdesk_total_closed,' .
    'project_statistics_helpdesk_bugs, project_statistics_helpdesk_features, ' .
    'project_statistics_helpdesk_suggestions, project_statistics_helpdesk_issues, ' .
    'project_statistics_helpdesk_bugs_closed, project_statistics_helpdesk_features_closed, ' .
    'project_statistics_helpdesk_suggestions_closed, project_statistics_helpdesk_issues_closed,' .
    'project_statistics_helpdesk_bugs_testing, project_statistics_helpdesk_features_testing,' .
    'project_statistics_helpdesk_suggestions_testing, project_statistics_helpdesk_issues_testing'
);
$q->addWhere('project_statistics_project_id = ' . $project_id);
$result =& $q->exec();

$xdata = array();
$ydata = array();
$i = 0;
foreach ($result as $item ) {
    $date = new CDate($item['project_statistics_timestamp']);
    $xdata[$i] = $date->getTime();
    $ydata[$i] = $item['project_statistics_percent_complete'];
    $i++;
}

// reference plot
$ref_y = array(1,100);
$ref_x = array($start_date->getTime(), $end_date->getTime());

// ---------------------------------------------------------------------------
// Width and height of the graph
$width = 800; $height = 300;

// Create a graph instance
$graph = new Graph($width, $height);
$graph->img->SetImgFormat('jpeg');

$graph->SetMargin(40,10,10,100);

// Specify what scale we want to use,
$graph->SetScale('datlin');

// Setup a title for the graph
$graph->title->Set('Project progress');

// Setup titles and X-axis labels
$graph->xaxis->SetLabelAngle(90);
$graph->SetTickDensity( TICKD_DENSE);

// Setup Y-axis title
$graph->yaxis->title->Set('%');

if (count($ydata) > 0) {
    // Create the linear plot
    $line=new LinePlot($ydata, $xdata);
    $line->SetColor('green');
    $graph->Add($line);
}

// Add the reference plot to the graph
$line_ref=new LinePlot($ref_y, $ref_x);
$graph->Add($line_ref);

// Display the graph
$graph->Stroke();
?>