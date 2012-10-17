<?php
// The directory path accessible over internet
$uri = "/fw-dir/";

// Define URL constants
define('fw_root', 			realpath(dirname(__FILE__)) . '/');		// absolute server path
define('fw_URI',		 	$uri,  true);							// browser-friendly path

// include path is set to abs. server path
set_include_path(fw_root);

// configuration
require_once("config.inc.php");

// Translate function
function _t ($key)
    return $GLOBALS['fw_langs']->translate ($key);

// user class
require_once(fw_dir_inc."User.php");
// security
require_once(fw_dir_inc."Security.php");

// Current user object
$user = $GLOBALS['fw_security']->authenticatedUser;
// LoggedIn GLOBALS variable, stating current-user's status
$loggedIn = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : false;

// Load current language
$GLOBALS['fw_langs']->loadLanguage(fw_uri_lang);

// user-agent related
require_once(fw_dir_inc."Browser.php");

// Set up TemplateHelper with user's a template
$templateHelper = new TemplateHelper ($user === NULL ? fw_settings_template : $user->template);
// Template class is in $GLOBALS['fw_template'] variable

// Set up modules
// Activate module
$moduleHelper = new ModuleHelper (fw_module_name);

$moduleHelper->activateModule ();
$module = $moduleHelper->module;

$moduleAction = &$GLOBALS["fw_module_action"];

// Execute the module action
if (!method_exists( $module, "_{$moduleAction}" ))
{
	// Action -> first argument.
	array_unshift($GLOBALS['fw_module_arguments'], $moduleAction);
	
	// Overwrite action by module's set up action or default, 'home'
	$moduleAction = property_exists($module, "defaultAction") ? $module->defaultAction : "home";
}

call_user_func (array ( $module, "_{$moduleAction}" ), $GLOBALS['fw_module_arguments'], count($GLOBALS['fw_module_arguments']));

// Clean the buffer
ob_clean();

// Output Template generated page
echo $GLOBALS['fw_template']->page;

// Finish
ob_end_flush();
