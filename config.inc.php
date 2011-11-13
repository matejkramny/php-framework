<?php

// mysql database config
define("fw_mysql_user", "root");
define("fw_mysql_password", "root");
define("fw_mysql_host", "localhost");
define("fw_mysql_socket", "");
define("fw_mysql_port", 3306);
define("fw_mysql_database", "framework");
define("fw_mysql_table_prefix", "");

// mysql database config for administration
// site mode switchable to front-end & back-end
define("fw_mysql_admin_table_prefix", "admin_");
if(!defined("fw_site_mode"))
	define ("fw_site_mode","user");

// predefined directories
define("fw_dir_inc", "inc/");
define("fw_dir_lib", fw_dir_inc."lib/");
define("fw_dir_logs", "logs/");
define("fw_dir_modules", "modules/");
define("fw_dir_admin", "administration/");
define("fw_dir_templates", "templates/");

// error reporting & logging
@ini_set("display_errors", "On");
@ini_set("error_log", fw_root.fw_dir_logs."error.log");
@ini_set("log_errors", "On");

// instantiate Database class & make a new MySQL connection
require_once(fw_dir_inc."Database.php");
$fw_db = new DB;
$fw_db->connect();
$fw_db->selectDb(fw_mysql_database);

// general settings - more in database
define("fw_site_enabled", true);
require_once(fw_dir_lib."settings.php");

// url routing
require_once(fw_dir_inc."Routing.php");