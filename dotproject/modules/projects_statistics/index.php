<?php

##
## statistics module - display project advance graph

$AppUI->savePlace();
$module = dPgetParam( $_GET, "m", 0 );

//if module is not statistics get the project_id for filter
if ($module <> "projects_statistics"){
    $project_id = intval( dPgetParam( $_GET, "project_id", 0 ) );
}

// check for project filter
if ($project_id) {
    $where .= "\n WHERE project_id = '$project_id'";
} else {
    $where="";
}

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
	<th width="10"> Project Statistic </th>
</tr>

<?
ini_set('max_execution_time', 180);
ini_set('memory_limit', $dPconfig['reset_memory_limit']);

include ($AppUI->getLibraryClass('jpgraph/src/jpgraph'));
include ($AppUI->getLibraryClass('jpgraph/src/jpgraph_gantt'));

// get the prefered date format
$df = $AppUI->getPref('SHDATEFORMAT');
$start_date = dPgetParam($_GET, 'start_date', 0);
$end_date   = dPgetParam($_GET, 'end_date', 0);

// Don't push the width higher than about 1200 pixels, otherwise it may not display.
$width = min(dPgetParam($_GET, 'width', 600), 1400);

$graph = new GanttGraph($width);
$graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);

$graph->SetFrame(false);
$graph->SetBox(true, array(0,0,0), 2);
$graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY);

$pLocale = setlocale(LC_TIME, 0); // get current locale for LC_TIME
$res = @setlocale(LC_TIME, $AppUI->user_lang[0]);
if ($res) { // Setting locale doesn't fail
	$graph->scale->SetDateLocale($AppUI->user_lang[0]);
}
setlocale(LC_TIME, $pLocale);

if ($start_date && $end_date) {
	$graph->SetDateRange($start_date, $end_date);
}

$graph->scale->actinfo->SetFont(FF_CUSTOM, FS_NORMAL, 8);
$graph->scale->actinfo->vgrid->SetColor('gray');
$graph->scale->actinfo->SetColor('darkgray');
$graph->scale->actinfo->SetColTitles(array($AppUI->_('Project name', UI_OUTPUT_RAW),
                                           $AppUI->_('Start Date', UI_OUTPUT_RAW),
                                           $AppUI->_('Finish', UI_OUTPUT_RAW),
                                           $AppUI->_('Actual End', UI_OUTPUT_RAW)),
                                     array(160, 70, 70, 70));


$tableTitle = "Title";
$graph->scale->tableTitle->Set($tableTitle);

// Use TTF font if it exists
// try commenting out the following two lines if gantt charts do not display
if (is_file(TTF_DIR . 'FreeSansBold.ttf')) {
	$graph->scale->tableTitle->SetFont(FF_CUSTOM,FS_BOLD,12);
}
$graph->scale->SetTableTitleBackground('#EEEEEE');
$graph->scale->tableTitle->Show(true);

if ($day_diff > 240) {
	//more than 240 days
	$graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH);
} else if ($day_diff > 90) {
	//more than 90 days and less of 241
	$graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HWEEK);
	$graph->scale->week->SetStyle(WEEKSTYLE_WNBR);
}

$row = 0;


if (is_array($projects)) {
	foreach ($projects as $p) {
	}
} // End of check for valid projects array.
unset($projects);

$today = date('y-m-d');
$vline = new GanttVLine($today, $AppUI->_('Today', UI_OUTPUT_RAW));
$vline->title->SetFont(FF_CUSTOM, FS_BOLD, 10);
$graph->Add($vline);
$graph->Stroke();
?>

</table>
