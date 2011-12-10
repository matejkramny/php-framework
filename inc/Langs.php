<?php

// languages class
class Langs {

    private static $langs;
    private $language;
    private $ready; // language loaded & ready

    // public

    public static function getLangs($langCode=NULL) {
    	// Retrieve all languages or only one in case $langCode != NULL
    	
    	if ($langCode == NULL)
    	{
    		// Build up array of languages consisting of ['lang_code', 'lang_full']
    		
            if (!is_array(self::$langs))
                self::$langs = DB::getRows("sys_languages");

            $return = array();

            foreach (self::$langs as $arr) {
                $return[$arr["lang_code"]] = $arr["lang_full"];
            }

            return $return;
        } else {
            // Return row of wanted language ($langCode)
            
            return DB::getRow("sys_languages", array('where' => array('lang_code' => $langCode)));
        }
    }

    public function loadLanguage($langCode) {
    	// Make sure language exists. If not, load default.
        if(!$this->langExists($langCode))
            $langCode = fw_settings_lang;
        
        // Fetch language .json
        if (!$language = file_get_contents(fw_dir_inc . "Languages/{$langCode}.json"))
            $language = file_get_contents(fw_dir_inc . "Languages/" . fw_settings_lang . ".json");
        
        // Flag language translation as ready
        $this->ready = true;
        
        // $this->language is used for translation
        // Decode json to PHP Array
        $this->language = json_decode($language, true);
    }

    public static function langExists($langCode) {
    	// Check if language exists
        return self::getLangs($langCode);
    }
    
    public function addLanguage($add, $langCode){
    	// Make an addition to the current language translation
    	
    	// Make sure language is flagged as Ready
        if(!$this->ready) $this->loadLanguage (fw_settings_lang);
        
        // Amend the language
        $this->language = array_merge($this->language, $add);
        
        // Save language back to .json
        $this->saveLanguage($langCode,$add);
    }
    
    public function removeLanguage($remove, $langCode){
        // Remove a translation from the current language
        
        // Make sure language is flagged as Ready
        if(!$this->ready) $this->loadLanguage (fw_settings_lang);
        
        // Make a copy of current language
        $language = $this->language;
        
        // Remove every instance of translation wanting to be removed
        foreach($remove as $key){
            unset($language[$key]);
        }
        
        // Save language back to .json
        $this->saveLanguage($langCode, $language);
    }
    
    private function saveLanguage($langCode,$lang){
    	// Save the current translation to .json file
    	
    	// Encode from PHP array to JSON
        $json = json_encode($lang);
        
        // Save the file to disk
        file_put_contents(fw_dir_lib."Languages/{$langCode}.json", $json);
    }
    
    public function translate($key) {
    	// Translate function
    	// Translates $key into its meaning
    	
    	// Make sure language is flagged as Ready
        if (!$this->ready)
            $this->loadLanguage(fw_settings_lang);
        
        // Return what was requested if not found in this language
        if(!isset($this->language[$key]))
            return $key;
        
        // Return the translation
        return $this->language[$key];
    }

}

$fw_langs = new Langs;