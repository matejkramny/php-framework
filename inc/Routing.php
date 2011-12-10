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
            if (!isset($route["lang"]))
                define("fw_uri_lang", fw_settings_lang);
            else
                define("fw_uri_lang", $route["lang"]);
        }
    }

    // Parses URL and decides which module is to be loaded, what language the page is to be displayed in etc.
    private function parseRoute($unparsed) {
    	// Put an unparsed URI string into an array, by the "/" delimiter
        $oExplode = explode("/", $unparsed);
		
		// Filter the array elements, and escape unsafe characters.
        foreach ($oExplode as $key => $string) {
            if (strlen($string) == 0) {
                unset($oExplode[$key]);
                continue;
            }

            $oExplode[$key] = DB::makeSafe($string);
        }
		
        $this->parsed = $oExplode;
        $route = &$this->route;
		
		// Decide what to do for each array element.
        foreach ($oExplode as $key => $value) {
            // check for language (2 char e.g. 'en')
            // language must be before anything in the uri
            if (array_key_exists($value, Langs::getLangs()) && $key == 0) {
                $route["lang"] = $value;
            }
            
            
            // TODO: module checking. must be in 2 space from / after domain (language before it) 
            // TODO: module actions.. third from / after domain
        }
    }

}

$fw_routing = new Routing;