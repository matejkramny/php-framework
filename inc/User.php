<?php

require_once(fw_dir_inc."Security.php");

// User class holds a user

class User extends Security{
    private $pool;
    
    public $id;
    private $salt;
    public $name;
    public $surname;
    public $email;
    public $lang;
    public $username;
    
    function User($id){
        if(is_array($id))
            self::createUser($id);
        
        $this->pool = DB::getRow("profiles", array("where" => array("id" => $id)));
        
        $this->id = $this->pool["id"];
        $this->salt = $this->pool["salt"];
        $this->name = $this->pool["name"];
        $this->surname = $this->pool["surname"];
        $this->email = $this->pool["email"];
        $this->lang = $this->pool["lang"];
        $this->username = $this->pool["username"];
    }
    
    function createUser($details){
        $details = array_merge($details, array(
            "id" => NULL,
        ));
        DB::insert("profiles", $details);
    }
    
    function changeDetail($detail, $new){
        $oDb = DB::update("profiles", 
                array('fields' => array($detail => $new), 'where' => array('id' => $this->id)));
    }
    
    static function hash_password($password) {
        return parent::hash_password($password, $this->salt);
    }
}