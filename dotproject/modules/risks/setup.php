<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
/**
 *  Name: MS-Project Import
 *  Directory: projectimporter
 *  Version 1.4
 *  Type: user
 *  UI Name: Project Importer
 *  UI Icon: ?

 
Initial Work:	Richard Thompson - Belfast, Northern Ireland 
Developers:		Keith Casey - Washington, DC keith@caseysoftware.com 
				Ivan Peevski - Adelaide, Australia cyberhorse@users.sourceforge.net
*/

global $AppUI;

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'Risks';
$config['mod_version'] = '3.0';
$config['mod_directory'] = 'risks';
$config['mod_setup_class'] = 'dotProject_AddOn_Setup_Risks';
$config['mod_type'] = 'user';
$config['mod_ui_name'] = 'Risks';
$config['mod_ui_icon'] = '';
$config['mod_description'] = 'Risk Management';

if (@$a == 'setup') {
	echo dPshowModuleConfig( $config );
}

class dotProject_AddOn_Setup_Risks {   

	function install() {
		$q = new DBQuery();
		$q->createTable('risks');
		$sql = '(
			`risk_id` int(10) unsigned NOT NULL auto_increment,
			`risk_name` varchar(50) default NULL,
			`risk_description` text,
			`risk_probability` tinyint(3) default 100,
			`risk_status` text default NULL,
			`risk_owner` int(10) default NULL,
			`risk_project` int(10) default NULL,
			`risk_task` int(10) default NULL,
			`risk_impact` int(10) default NULL,
			`risk_duration_type` tinyint(10) default 1,
			`risk_notes` text,
			PRIMARY KEY  (`risk_id`),
			UNIQUE KEY `risk_id` (`risk_id`),
			KEY `risk_id_2` (`risk_id`))
			TYPE=MyISAM';
		$q->createDefinition($sql);
		$q->exec();
		$q->clear();

		$q->createTable('risk_notes');
		$sql = '(
			`risk_note_id` int(11) NOT NULL auto_increment,
			`risk_note_risk` int(11) NOT NULL default \'0\',
			`risk_note_creator` int(11) NOT NULL default \'0\',
			`risk_note_date` datetime NOT NULL default \'0000-00-00 00:00:00\',
			`risk_note_description` text NOT NULL,
			PRIMARY KEY  (`risk_note_id`)
			) TYPE=MyISAM';
		$q->createDefinition($sql);
		$q->exec();
		$q->clear();
		
		$q->addTable('sysvals');
		$q->addInsert('sysval_key_id', 1);
		$q->addInsert('sysval_title', 'RiskProbability');
		$q->addInsert('sysval_value', "0|Not Specified\n1|Low\n2|Medium\n3|High");
		$q->exec();
		$q->clear();

		$q->addTable('sysvals');
		$q->addInsert('sysval_key_id', 1);
		$q->addInsert('sysval_title', 'RiskStatus');
		$q->addInsert('sysval_value', "0|Not Specified\n1|Open\n2|Closed\n3|Not Applicable");
		$q->exec();
		$q->clear();

		$q->addTable('sysvals');
		$q->addInsert('sysval_key_id', 1);
		$q->addInsert('sysval_title', 'RiskImpact');
		$q->addInsert('sysval_value', "0|Not Specified\n1|Low\n2|Medium\n3|High\n4|Super High");
		$q->exec();
		$q->clear();

		return true;
	}
	
	function remove() {
		$q = new DBQuery;
		$q->dropTable('risks');
		$q->exec();
		$q->clear();
		$q->dropTable('risk_notes');
		$q->exec();
		$q->clear();
		
		return true;
	}
	
	function upgrade() {
		return null;
	}
}

?>	
	
