<?php

// mysql database config
define("fw_db_user", "root");
define("fw_db_password", "octocat");
define("fw_db_host", "localhost");
define("fw_db_socket", "/var/run/mysqld/mysqld.sock");
define("fw_db_port", 3306);
define("fw_db_database", "framework");
define("fw_db_driver", "mysql");

// database config for administration
// site mode switchable to front-end & back-end
if(!defined("fw_site_mode"))
	define ("fw_site_mode","user");

// predefined directories
define("fw_dir_inc", "inc/");
define("fw_dir_lib", fw_dir_inc."lib/");
define("fw_dir_logs", "logs/");
define("fw_dir_modules", "modules/");
define("fw_dir_admin", "modules/admin/"); // Directory for admin-specific modules
define("fw_dir_templates", "templates/");
define("fw_dir_temp", "temp/");

// error reporting & logging
@ini_set("display_errors", "Off");
@ini_set("error_log", fw_root.fw_dir_logs."error.log");
@ini_set("log_errors", "On");

// Set up timezone
date_default_timezone_set("Europe/London");

// Start buffering output. It is dumped upon instantiation of Template
ob_start();

// Start session
session_start();

switch(fw_db_driver) {
	case 'mysql':
		require_once(fw_dir_inc."MySQL.php");
		$fw_db = new MySQL;
		break;
	case 'mysqli':
		require_once(fw_dir_inc."MySQLi.php");
		$fw_db = new MySQLi;
		break;
	default:
		trigger_error("Unsupported database driver", E_USER_ERROR);
		die();
}

if (!$fw_db->connect())
{
	// Halt
	// Log
	trigger_error("Database not working!", E_USER_WARNING);
	
	// Clean and end the output
	ob_end_clean();
	
	// Send Status 500, Internal Server Error..
	header('HTTP/1.1 500 Internal Server Error', true, 500);
	die();
}

$fw_db->selectDb(fw_db_database);

// general settings loaded from database
require_once(fw_dir_lib."settings.php");

// url routing
require_once(fw_dir_inc."Routing.php");

// Template
require_once(fw_dir_inc."Template.php");

// Module
require_once(fw_dir_inc."Module.php");
// Form class
require_once(fw_dir_inc."Form.php");

$fw_routing = new Routing;
