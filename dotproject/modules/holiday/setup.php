<?php
##
## holiday module - A dotProject module for keeping track of holidays
##
## Sensorlink AS (c) 2006
## Vegard Fiksdal (fiksdal@sensorlink.no)
##


// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'Holiday';
$config['mod_version'] = '0.1';
$config['mod_directory'] = 'holiday';
$config['mod_setup_class'] = 'CSetupHoliday';
$config['mod_type'] = 'admin';
$config['mod_ui_name'] = 'Holiday';
$config['mod_ui_icon'] = 'notepad.gif';
$config['mod_description'] = 'A module for registering non-working days';

if (@$a == 'setup') {
	echo dPshowModuleConfig( $config );
}

class CSetupHoliday {

	function install() {
		// Create whilelist/blacklist database
		$sql = "CREATE TABLE IF NOT EXISTS holiday ( " .
		"holiday_id int(10) unsigned NOT NULL auto_increment," .
		"holiday_user int(10) NOT NULL default '0'," .
		"holiday_white int(10) NOT NULL default '0'," .
		"holiday_annual int(10) NOT NULL default '0'," .
		"holiday_start_date datetime NOT NULL default '0000-00-00 00:00:00'," .
		"holiday_end_date datetime NOT NULL default '0000-00-00 00:00:00'," .
		"holiday_description text," .
		"PRIMARY KEY  (holiday_id)," .
		"UNIQUE KEY holiday_id (holiday_id)" .
		") TYPE=MyISAM;";
		db_exec( $sql );

		// Create settings database
                $sql = "CREATE TABLE IF NOT EXISTS holiday_settings ( " .
                "holiday_manual int(10) NOT NULL default '0'," .
                "holiday_auto int(10) NOT NULL default '0'," .
                "holiday_driver int(10) NOT NULL default '0'," .
                "UNIQUE KEY holiday_manual (holiday_manual)," .
                "UNIQUE KEY holiday_auto (holiday_auto)," .
                "UNIQUE KEY holiday_driver (holiday_driver)" .
                ") TYPE=MyISAM;";
                db_exec( $sql );

		// Set default settings
        	$sql = "INSERT INTO holiday_settings (holiday_manual,holiday_auto,holiday_driver) ";
        	$sql.= "VALUES ('".($holiday_manual+0)."','".($holiday_auto+0)."','".($holiday_driver+0)."');";
                db_exec( $sql );

		return null;
	}
	
	function remove() {
		db_exec("DROP TABLE holiday;");
		db_exec("DROP TABLE holiday_settings;");
		return null;
	}
	
	function upgrade() {
		return null;
	}
}

?>

