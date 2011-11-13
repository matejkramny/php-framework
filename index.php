<?php

define('fw_root', 			dirname($_SERVER['SCRIPT_FILENAME']) . '/');		// absolute server path
define('fw_URI',		 	dirname($_SERVER['PHP_SELF']) . '/',  true);		// browser-friendly path

// include path is set to abs. server path
set_include_path(fw_root);

// configuration
require_once("config.inc.php");

function _t ($key){
    return $GLOBALS['fw_langs']->translate ($key);
}

// user class
require_once(fw_dir_inc."User.php");
// security
require_once(fw_dir_inc."Security.php");

$user = $GLOBALS['fw_security']->authenticatedUser;
$loggedIn = $_SESSION['loggedIn'];

$GLOBALS['fw_langs']->loadLanguage($user->lang);

// user-agent related
require_once(fw_dir_inc."Browser.php");