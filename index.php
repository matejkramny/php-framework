<?php

// Define URL constants
define('fw_root', 			dirname($_SERVER['SCRIPT_FILENAME']) . '/');		// absolute server path
define('fw_URI',		 	dirname($_SERVER['PHP_SELF']) . '/',  true);		// browser-friendly path

// include path is set to abs. server path
set_include_path(fw_root);

// configuration
require_once("config.inc.php");

// Translate function
function _t ($key)
{
    return $GLOBALS['fw_langs']->translate ($key);
}

// user class
require_once(fw_dir_inc."User.php");
// security
require_once(fw_dir_inc."Security.php");

// Current user object
$user = $GLOBALS['fw_security']->authenticatedUser;
// LoggedIn GLOBALS variable, stating current-user's status
$loggedIn = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : false;

// Load current language
$GLOBALS['fw_langs']->loadLanguage(($user == null) ? fw_settings_lang : $user->lang);

// user-agent related
require_once(fw_dir_inc."Browser.php");

// Set up modules
// Set up templating
// Activate module
// Apply template to module

// Set up TemplateHelper with user's a template
$template_helper = new TemplateHelper ($user == null ? fw_settings_template : $user->template);
// Template class is in $GLOBALS['fw_template'] variable

