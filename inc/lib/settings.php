<?php

require_once(fw_dir_inc."Database.php");
require_once(fw_dir_inc."Browser.php");

// get settings from database
$settings = DB::getRows("sys_settings", array (
	'fields' => array (
		'name',
		'value')
		)
	);

foreach($settings as $arr){
	if(!defined("fw_settings_".$arr['name']))
		define("fw_settings_".$arr['name'], $arr['value']);
}

// browser detection
$user_agent = new Browser();

define("fw_browser_name", $user_agent->getBrowser());
define("fw_browser_version", $user_agent->getVersion());
define("fw_user_ip", $_SERVER['REMOTE_ADDR']);