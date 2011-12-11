<?php

require_once(fw_dir_inc . "Langs.php");

// Routing class
// handles the ?path=
// to determine
// - what module
// - what language
// - additional parameters

class Routing {

    private $route;
    private $parsed;
    private $unparsed;

    // public
    // constructor
    public function Routing()
    {
        $this->route = NULL;
        $this->unparsed = (isset($_GET['path'])) ? $_GET['path'] : "";

        $this->getRoute();
        $this->defineRoute($this->route);
    }

    public function getRoute() {
        // Get route from url request
        if ($this->route == NULL) {
            $this->parseRoute($this->unparsed);
        }

        return $this->route;
    }

    // private

    private function defineRoute($route=null) {
        // Defines route constants
        
        if ($route == null)
            $route = $this->route;

        if (!defined("fw_uri_lang")) {
            define("fw_uri_lang", $route["lang"]);
        }
        if (!defined("fw_uri_module"))
        {
        	define ("fw_uri_module", $route["module"]);
        }
        if (!defined("fw_uri_module_args"))
        {
        	$GLOBALS["module_arguments"] = $route["moduleArguments"];
        }
    }

    // Parses URL and decides which module is to be loaded, what language the page is to be displayed in etc.
    private function parseRoute($unparsed) {
    	// Put an unparsed URI string into an array, by the "/" delimiter
        $o = explode("/", $unparsed);
		
		// Filter the array elements, and escape unsafe characters.
        foreach ($o as $key => $string) {
            if (strlen($string) == 0) {
                unset($o[$key]);
                continue;
            }

            $o[$key] = DB::makeSafe($string);
        }
		
        $this->parsed = $o;
        $r = &$this->route;
		
		// Language
		$lang = &$r["lang"];
		if (isset($o[0]))
		{
			// Check language existance
			if ($GLOBALS['fw_langs']->langExists ($o[0]))
				$lang = $o[0];
			
			$lang = fw_settings_lang;
		}
		else
		{
			// Add a language to the beginning of the URL
			array_unshift($o, fw_settings_lang);
			$lang = fw_settings_lang;
		}
		
		// Module
		$module = &$r["module"];
		if (isset($o[1]))
		{
			// Module class -> moduleExists ($o[1])
			$module = "home";
		}
		else
		{
			$module = "home";
		}
		
		// Loop for module Arguments
		$args = &$r["moduleArguments"];
		$args = array();
		for ($i = 2; $i < count($o); $i++)
		{
			$args[] = $o[$i];
		}
	}
}

$fw_routing = new Routing;