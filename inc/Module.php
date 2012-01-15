<?php

// Incomplete

abstract class Module
{
	public function setUp ($m)
	{
		if ($this->setUpDB)
		{
			require_once fw_dir_modules.$m["Path"]."/{$m['ClassPrefix']}Database.php";
		
			$mDb = "{$m['ClassPrefix']}Database";
			$this->db = new $mDb;
		}
	}
	
	public function redirect ($page)
	{
		header("Location: {$page}", true);
	}
	
	public function end ($pageProperties=NULL)
	{
		$GLOBALS['fw_template']->endPage ($pageProperties);
	}
}

final class ModuleHelper
{
	public function ModuleHelper ($m)
	{
		$this->moduleData = self::getModuleData ($m, true);
		
		if ($this->moduleData === NULL)
		{
			// 500 Internal Server Error
			trigger_error("Module not loaded! Module name: {$m}", E_USER_WARNING);
			
			// Clean and end the output
			ob_end_clean();
	
			// Send Status 500, Internal Server Error..
			header('HTTP/1.1 500 Internal Server Error', true, 500);
			die();
		}
		
		if (!defined ('fw_module_path'))
			define('fw_module_path', fw_root.fw_dir_modules.$this->moduleData['Path'].'/');
	}
	
	public function activateModule ()
	{
		// Module data OK, load it up
		$path = fw_dir_modules.$this->moduleData['Path']."/";
		$prefix = $this->moduleData['ClassPrefix'];
		
		require_once $path.$prefix."Module.php";
		
		$mClassName = $prefix."Module";
		
		// Dynamic class instantiation
		$mClass = new $mClassName ();
		
		$mClass->setUp ($this->moduleData);
		
		$this->module = $mClass;
	}
	
	// require_once fw_dir_loaded_module."{$moduleName}Database.php";
	public static function moduleExists ($moduleName, $returnData = false)
	{
		$oDb = DB::getRow ("sys_modules", array (
			"where" => "`Name` LIKE '{$moduleName}'"
		));
		
		if ($oDb === NULL)
			return NULL;
		
		if ($returnData)
			return $oDb;
		
		return true;
	}
	
	public static function getModuleData ($moduleName)
	{
		return self::moduleExists ($moduleName, true);
	}
}