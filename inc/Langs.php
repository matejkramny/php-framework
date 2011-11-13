<?php

// languages class
class Langs {

    private static $langs;
    private $language;
    private $ready; // language loaded & ready

    // public

    public static function getLangs($langCode=NULL) {
    	if ($langCode == NULL) {
            if (!is_array(self::$langs))
                self::$langs = DB::getRows("sys_languages");

            $return = array();

            foreach (self::$langs as $arr) {
                $return[$arr["lang_code"]] = $arr["lang_full"];
            }

            return $return;
        } else {
            return DB::getRow("sys_languages", array('where' => array('lang_code' => $langCode)));
        }
    }

    public function loadLanguage($langCode) {
        if(!$this->langExists($langCode))
            $langCode = fw_settings_lang;
        
        if (!$language = file_get_contents(fw_dir_inc . "Languages/{$langCode}.json"))
            $language = file_get_contents(fw_dir_inc . "Languages/" . fw_settings_lang . ".json");
        
        $this->ready = true;
        
        $this->language = json_decode($language, true);
    }

    public static function langExists($langCode) {
        return self::getLangs($langCode);
    }
    
    public function addLanguage($add, $langCode){
        if(!$this->ready) $this->loadLanguage (fw_settings_lang);
        
        $this->language = array_merge($this->language, $add);
        
        $this->saveLanguage($langCode,$add);
    }
    
    public function removeLanguage($remove, $langCode){
        if(!$this->ready) $this->loadLanguage (fw_settings_lang);
        
        $language = $this->language;
        foreach($remove as $key){
            unset($language[$key]);
        }
        
        $this->saveLanguage($langCode, $language);
    }
    
    private function saveLanguage($langCode,$lang){
        $json = json_encode($lang);
        
        file_put_contents(fw_dir_lib."Languages/{$langCode}.json", $json);
    }
    
    public function translate($key) {
        if (!$this->ready)
            $this->loadLanguage(fw_settings_lang);
        
        if(!isset($this->language[$key]))
            return $key;
        
        return $this->language[$key];
    }

}

$fw_langs = new Langs;