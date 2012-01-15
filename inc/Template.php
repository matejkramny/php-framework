<?php

// Incomplete

// Abstract Base extended by loaded Template
// TemplateBase contains essential methods a Template should have in order to work
abstract class TemplateBase {
    public static $module_content;
    
    abstract function message($message);
    
    public static function parse (&$c, $v)
    {
    	// Parses variable contents by variables in $v
    	// Variables are surrounded by curly braces
    	// Replacement variable example
    	/*
    	 * $myContents = "<html><body>{MyVariable} {IF MyIf} {LOOP MyLoop}</body></html>";
    	 * Template::parse ($myContents, array (
    	 * 		'__count' => 1, // __count signalises how many times variables have to be replaced (0 is replace all, 1 is default). Does not apply for IF and LOOP statements
    	 *		'MyVariable' => 'Hello world!', // a variable to be replaced
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
    		if (is_array($value))
    			self::parseLoop ();
    		else
    			self::parseVariable (array(
    				'key' => $key,
    				'value' => $value,
    				'count' => $repCount,
    			), $c, $v);
    	}
    	    	
    	// LOOP
    	foreach ($v as $key => $value)
    	{
    		if (!is_array($value))
    			continue;
    		
    		if (!isset ($value['type']) || strtolower($value['type']) != 'loop')
    			continue;
    		
			if (!isset ($value['__start']) || !isset ($value['__data']) || !isset ($value['__end']))
				continue;

			$start = $value['__start'];
			$data = $value['__data'];
			$end = $value['__end'];

			unset ($value['__start'], $value['__data'], $value['__end']);

			$output = $start;
			foreach ($value as $var => $val)
			{
				if (!is_array ($val)) continue;

				$temp = $data;
				foreach ($val as $rKey => $rVal)
				{
					$temp = str_replace ('{'.$rKey.'}', $rVal, $temp);
				}

				$output .= $temp;
			}

			$output .= $end;
	
			$one = 1;
			$c = str_replace ('{LOOP '.$key.'}', $output, $c, $one);
    	}
    	
    	return $c;
    }
    
    private static function parseVariable ($o, &$c)
    {
    	$o['key'] = '{'.$o['key'].'}';
    		
    	$count = $o['count']; // str_replace takes &count
    	if ($count == 0)
    		$c = str_replace($o['key'], $o['value'], $c);
    	else
    		$c = str_replace($o['key'], $o['value'], $c, $count);
    }
    private static function parseLoop ()
    {
    	
    }
    
    public static function loadFile($fileName, $parse=NULL)
    {
    	// Used for loading Template related files.
    	
    	if (@file_exists(fw_template_path.$fileName))
    	{
    		$file = file_get_contents(fw_template_path.$fileName, true);
    		// Parse results
    		return $parse !== NULL ? self::parse ($file, $parse) : $file;
    	}
    	
    	trigger_error("Template file does not exist: {$fileName}", E_USER_WARNING);
    	return null;
    }
    
    public static function getTooltipColour ($rawColour)
    {
    	$colours = json_decode(self::loadFile ('Tooltip/colours.json'));
		foreach ($colours as $k => $v)
		{
			if ($k == $rawColour)
				return $v;
		}
		
		return NULL;
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
