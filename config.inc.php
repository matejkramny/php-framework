<?php

// mysql database config
define("fw_mysql_user", "root");
define("fw_mysql_password", "octocat");
define("fw_mysql_host", "localhost");
define("fw_mysql_socket", "/var/run/mysqld/mysqld.sock");
define("fw_mysql_port", 3306);
define("fw_mysql_database", "framework");

// mysql database config for administration
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
@ini_set("display_errors", "On");
@ini_set("error_log", fw_root.fw_dir_logs."error.log");
@ini_set("log_errors", "On");

// Set up timezone
date_default_timezone_set("Europe/London");

// Start buffering output. It is released upon instantiation of Template
ob_start();

// Start session
session_start();

// instantiate Database class & make a new MySQL connection
require_once(fw_dir_inc."Database.php");
$fw_db = new DB;

if (!$fw_db->connect())
{
	// Halt
	// Log
	trigger_error("MySQL Database not working!", E_USER_WARNING);
	
	// Clean and end the output
	ob_end_clean();
	
	// Send Status 500, Internal Server Error..
	header('HTTP/1.1 500 Internal Server Error', true, 500);
	die();
}

$fw_db->selectDb(fw_mysql_database);

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
