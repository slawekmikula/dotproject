<?php

require_once 'base.php';
require_once DP_BASE_DIR.'/includes/config.php';
require_once DP_BASE_DIR.'/includes/main_functions.php';
require_once DP_BASE_DIR.'/includes/db_adodb.php';
require_once DP_BASE_DIR.'/includes/db_connect.php';
require_once DP_BASE_DIR.'/classes/ui.class.php';

$AppUI = new CAppUI;

/* cron tasks */

// project statistics
require_once( $AppUI->getModuleClass( 'projects_statistics' ) );
$ps =& new CProjectsStatistics('projects_statistics', 'projects_statistics_id');
$ps->createStatistics();

?>
