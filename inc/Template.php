<?php

abstract class TemplateBase {
    public static $module_content;
    
    public static function message($message)
    {
    	
    }
    private static function loadFile($fileName)
    {
    	
    }
}

class TemplateHelper {
    public function TemplateHelper ($template){
        if(defined("fw_template"))
            return false;
        
        define("fw_template", $template);
        
        self::loadTemplate($template);
    }
    
    private static function loadTemplate ($templateName){
        require_once fw_dir_templates.fw_template.'Template.php';
    }
}