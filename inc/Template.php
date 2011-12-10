 <?php

// Incomplete

// Abstract Base extended by loaded Template
// TemplateBase contains essential methods a Template should have in order to work
abstract class TemplateBase {
    public static $module_content;
    
    abstract static function message($message);
    // Used for handling messages sent by module
    
    abstract static function loadFile($fileName);
  	// Used for loading Template related files.
}

class TemplateHelper
{
    public function TemplateHelper ($template)
    {
    	// TemplateHelper constructor
    	// TemplateHelper helps the requested $template load & execute
    	
    	// template already loaded
        if(defined("fw_template_path"))
            return false;
        
        // Load a template
        define("fw_template_path", self::getTemplatePathFromID($template));
        
        self::loadTemplate();
    }
    
    private static function loadTemplate ()
    {
        require_once fw_dir_templates.fw_template_path.'/Template.php';
    	$GLOBALS['fw_template'] = new Template;
    }
    
    public static function getTemplatePathFromID ($id)
    {
    	$oDb = DB::getRow ("sys_templates", array (
    		"id" => $id
    	));
    	
    	if ($oDb)
    		return $oDb["path"];
    	
    	return fw_settings_template_path;
    }
    public static function getTemplateNameFromID ($id)
    {
    	$oDb = DB::getRow ("sys_templates", array (
    		"id" => $id
    	));
    	
    	if ($oDb)
    		return $oDb['name'];
    	
    	return "template name not found";
    }
}

/*
if(fw_browser_name == Browser::BROWSER_IE && fw_browser_version < 8){
	// TODO: Template::displayMessageTop("Incompatible browser. Please upgrade your browser for better experience with the website", "red");
	echo "Incompatible browser";
}
*/