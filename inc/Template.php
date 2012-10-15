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
		 *		'__count' => 1, // __count signalises how many times variables have to be replaced (0 is replace all, 1 is default). Does not apply for IF and LOOP statements
		 *		'MyVariable' => 'Hello world!', // a variable to be replaced
		 *		'MyLoop' => array ( // Looping
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
				self::parseLoop (array(
					'value' => $value,
				), $c);
			else
				self::parseVariable (array(
					'key' => $key,
					'value' => $value,
					'count' => $repCount,
				), $c);
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
	private static function parseLoop ($o, &$c)
	{
		if (!isset ($o['value']['type']) || strtolower($o['value']['type']) != 'loop')
			return;
		
		if (!isset ($o['value']['__start']) || !isset ($o['value']['__data']) || !isset ($o['value']['__end']))
			return;
		
		$start = $o['value']['__start'];
		$data = $o['value']['__data'];
		$end = $o['value']['__end'];
		
		unset ($o['value']['__start'], $o['value']['__data'], $o['value']['__end']);
		
		$output = $start;
		foreach ($o['value'] as $var => $val)
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
	
	public function addFile ($content, $name, $extension, $seperate = false)
	{
		if (!isset ($this->files[$extension]))
			$this->files[$extension] = 
				array (array("name" => "combined",
							"content" => "")); // Zeroeth index is combined if !$seperate
		
		$f = &$this->files[$extension];
		
		if ($seperate)
		{
			// Download this file seperately from others
			$f[] = array ("name" => $name, "content" => $content);
		}
		else
		{
			// Join it with other files into one.
			$f[0]["content"] .= $content;
		}
	}
	
	private function saveFiles ()
	{
		if (!is_writable (fw_template_assets_path) || !isset ($this->files))
			return false;
		
		foreach ($this->files as $ext => $arr)
		{
			if (!is_array ($arr)) continue;
			
			foreach ($arr as $content)
			{
				// Unique file name
				$userId = $GLOBALS['loggedIn'] ? $GLOBALS['user']['id'] : NULL;
				$fName = time()."-".md5($content["name"].$ext.$userId);
				$fPath = fw_dir_assets."{$fName}.{$ext}";
				
				$existingFileRecord = DB::getRow ("files_cache", array (
					"where" => array (
						"hash" => $fName,
						"fileName" => $content["name"],
						"extension" => $ext,
						"belongs_to" => $userId
					)
				));
				
				if (file_exists ($fPath) && $existingFileRecord)
				{
					// Already exists
					
					if (file_get_contents ($fPath) != $content["content"])
					{
						// Content has changed, update Database record and file
						DB::update ("files_cache", array (
							"fields" => array (
								"last_changed" => time()
							),
							"where" => array (
								"hash" => $fName,
								"fileName" => $content["name"],
								"extension" => $ext,
								"belongs_to" => $userId
							)
						));
						
						@file_put_contents ($fPath, $content["content"]);
					}
					// else => content hasn't changed. Don't need to update anything
					
					// Skip to next file
					continue;
				}
				
				// Save record to database
				DB::insert ("files_cache", array (
					"hash" => $fName, // Add to DB!
					"fileName" => $content["name"],
					"extension" => $ext,  // Add to DB!
					"last_changed" => time(),
					"belongs_to" => $userId
				));
				
				// Save contents to file
				@file_put_contents ($fPath, $content["content"]);
			}
		}
	}
	
	/*
	 * Cache them!
	 */
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
			"where" => array ("id" => $id)
		));
		
		if ($oDb)
			return $oDb["path"];
		
		return fw_settings_template_path;
	}
	public static function getTemplateNameFromID ($id)
	{
		$oDb = DB::getRow ("sys_templates", array (
			"where" => array ("id" => $id)
		));
		
		if ($oDb)
			return $oDb['name'];
		
		return "template name not found";
	}
}
