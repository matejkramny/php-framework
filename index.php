<?php
// The directory path accessible over internet
$uri = "/fw-dir/";

// Define URL constants
define('fw_root', realpath(dirname(__FILE__)) . '/');		// absolute server path
define('fw_uri',  $uri,  true);							// browser-friendly path

// include path is set to abs. server path
set_include_path(fw_root);

// Loads all classes
require_once("headers.php");

// Translate function shortcut
function _t ($key) {
	return $GLOBALS['fw_langs']->translate ($key);
}

// Current user - if null no user and not logged in
$user = $GLOBALS['fw_security']->authenticatedUser;

// Load current language
$GLOBALS['fw_langs']->loadLanguage(fw_uri_lang);

// Set up TemplateHelper with user's a template
$templateHelper = new TemplateHelper ($user === null ? fw_settings_template : $user->template);
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

// TODO $template->output();
// TODO remove lines below

// Clean the buffer
ob_clean();

// Output Template generated page
echo $GLOBALS['fw_template']->page;

// Finish
ob_end_flush();
