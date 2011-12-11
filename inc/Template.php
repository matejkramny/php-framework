<?php

// Incomplete

// Abstract Base extended by loaded Template
// TemplateBase contains essential methods a Template should have in order to work
abstract class TemplateBase {
    public static $module_content;
    
    abstract static function message($message);
    
    public static function parse ($c, $v)
    {
    	// Parses variable contents by variables in $v
    	// Variables are surrounded by curly braces
    	// Replacement variable example
    	/*
    	 * $myContents = "<html><body>{MyVariable} {IF MyIf} {LOOP MyLoop}</body></html>";
    	 * Template::parse ($myContents, array (
    	 * 		'__count' => 1, // __count signalises how many times variables have to be replaced (0 is replace all, 1 is default). Does not apply for IF and LOOP statements
    	 *		'MyVariable' => 'Hello world!', // a variable to be replaced
    	 *		'MyIf' => array (	// Evaluated IF statement
    	 *			'type' => 'if',
    	 * 			'condition' => "1 == 2", // gets to eval ("if ( %condition% ) return true; else return false;"
    	 *			'true' => "some content replaced if evaluated to TRUE",
    	 *			'false' => "some content replaced if evaluated to FALSE"
    	 *		),
    	 *		'MyLoop' => array (	// Looping
    	 *			// arrays of data, indexes (keys) are ignored except __start, __data and __end
    	 *			// __start is put at the start of the output, __data is the object being replaced by variables inside arrays below and __end is put at the end of the output
    	 *			'type' => 'loop',
    	 *			'__start' => "some data, like <table>",
    	 *			'__data' => "<td>data repeatedly replaced by arrays below (row for an array)</td>",
    	 *			'__end' => "some end data, like </table>",
    	 *			array (
    	 *				'exampleVariable' => 'to be replaced by this'
    	 *			),
    	 *			array (
    	 *				'exampleVariable' => 'to be replaced by this (second row)'
    	 *			)
    	 *		)
    	 * ));
    	 */
    	
    	// check
    	if (!is_array($v))
    		return $c;
    	
    	// Variable replacement
    	$repCount = 1;
    	if (isset($v['__count']))
    	{
    		$repCount = $v['__count'];
    		unset ($v['__count']);
    	}
    	
		foreach ($v as $key => $value)
    	{
    		// Array can be IF or LOOP statement
    		if (is_array($value))
    			continue;
    		
    		$key = '{'.$key.'}';
    		
    		$count = $repCount; // str_replace takes &count
    		if ($count == 0)
    			$c = str_replace($key, $value, $c);
    		else
    			$c = str_replace($key, $value, $c, $count);
    		
    		unset($v[$key]);
    	}
    	
    	// IF
    	foreach ($v as $key => $value)
    	{
    		// We want an array
    		if (!is_array($value))
    			continue;
    		// Check for IF type
    		if (!isset ($value['type']) || strtolower($value['type']) != 'if')
    			continue;
    		
    		if ($value['condition'] == NULL || $value['true'] == NULL || $value['false'] == NULL)
    			continue;
    		
    		$condition = $value['condition'];
    		
    		// Eval statement
    		$r = @eval("if ({$condition}) return true; else return false;");
    		
    		// Replace variable, either $t or $f based on result of $r
    		$repl = "";
    		
    		if ($r)
    			$repl = $value['true'];
    		else
    			$repl = $value['false'];
    		
    		$one = 1; // str_replace requires a variable, it is taken by reference. Only variables can be taken by referencing
    		$c = str_replace ('{IF '.$key.'}', $repl, $c, $one);
    	}
    	
    	// LOOP
    	foreach ($v as $key => $value)
    	{
    		if (!is_array($value))
    			continue;
    		
    		if (!isset ($value['type']) || strtolower($value['type']) != 'loop')
    			continue;
    		
    		// To be finished!
    	}
    	
    	return $c;
    }
    
    public static function loadFile($fileName, $parse=NULL)
    {
    	// Used for loading Template related files.
    	
    	if (@file_exists(fw_template_path.$fileName))
    	{
    		$file = file_get_contents(fw_template_path.$fileName, true);
    		// Parse results
    		return $parse != NULL ? self::parse ($file, $parse) : $file;
    	}
    	
    	trigger_error("Template file does not exist: {$fileName}");
    	return null;
    }
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
        define("fw_template_path", fw_dir_templates.self::getTemplatePathFromID($template).'/');
        
        self::loadTemplate();
    }
    
    private static function loadTemplate ()
    {
        require_once fw_template_path.'Template.php';
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