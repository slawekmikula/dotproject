<?php
/*
 * Name:      Unit Testing
 * Directory: testing
 * Version:   1.0.0
 * Type:      user
 * UI Name:   Unit Testing
 * UI Icon:
 */

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'Unit Testing';		// name the module
$config['mod_version'] = '1.0.0';		// add a version number
$config['mod_directory'] = 'testing';		// tell dotProject where to find this module
$config['mod_setup_class'] = 'CSetupTesting';	// the name of the PHP setup class (used below)
$config['mod_type'] = 'user';			// 'core' for modules distributed with dP by standard, 'user' for additional modules from dotmods
$config['mod_ui_name'] = 'Unit Testing';		// the name that is shown in the main menu of the User Interface
$config['mod_ui_icon'] = 'communicate.gif';	// name of a related icon
$config['mod_description'] = 'Keep track of unit testing of tasks and projects';	// some description of the module
$config['mod_config'] = true;			// show 'configure' link in viewmods

// show module configuration with the dPframework (if requested via http)
if (@$a == 'setup') {
	echo dPshowModuleConfig( $config );
}

class CSetupTesting {

	function configure() {		// configure this module
	global $AppUI;
		$AppUI->redirect( 'm=testing&a=configure' );	// load module specific configuration page
  		return true;
	}

	function remove() {		// run this method on uninstall process
		db_exec( "DROP TABLE unittest;" );	// remove the unittest table from database

		return null;
	}


	function upgrade( $old_version ) {	// use this to provide upgrade functionality between different versions; not relevant here

		switch ( $old_version )
		{
		case "all":		// upgrade from scratch (called from install)
		case "0.9":
			//do some alter table commands

		case "1.0":
			return true;

		default:
			return false;
		}

		return false;
	}

	function install() {
		$sql = "CREATE TABLE `unittest` ( ".
  	"`unittest_id` int(11) NOT NULL auto_increment, ".
  	"`unittest_name` varchar(250) NOT NULL default '', ".
  	"`unittest_description` text NOT NULL, ".
  	"`unittest_expectedresult` text NOT NULL, ".
  	"`unittest_actualresult` text NOT NULL, ".
  	"`unittest_passed` tinyint(4) default NULL, ".
  	"`unittest_lasttested` datetime NOT NULL default '0000-00-00 00:00:00', ".
  	"`unittest_task_id` int(11) NOT NULL default '0', ".
  	"`unittest_project_id` int(11) NOT NULL default '0', ".
  	"PRIMARY KEY  (`unittest_id`) ".
	") ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ";
        

		db_exec( $sql ); db_error();						// execute the queryString

		return null;
	}

}

?>