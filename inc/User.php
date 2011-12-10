<?php

require_once(fw_dir_inc."Security.php");

// User class holds a user

class User
{
    private $pool;
    
    public $id;
    private $salt;
    public $name;
    public $surname;
    public $email;
    public $lang;
    public $username;
    
    function User ($id)
    {
    	// User class constructor
    	
    	// Initializes with existing $id or an array to create new
        if(is_array($id))
            self::createUser($id);
        
        // Take data from Database
        $this->pool = DB::getRow("profiles", array("where" => array("id" => $id)));
        
        // Set up User class properties of this User
        $this->id = $this->pool["id"];
        $this->salt = $this->pool["salt"];
        $this->name = $this->pool["name"];
        $this->surname = $this->pool["surname"];
        $this->email = $this->pool["email"];
        $this->lang = $this->pool["lang"];
        $this->username = $this->pool["username"];
    }
    
    function createUser ($details)
    {
    	// ID must be NULL, due to primary key duplicity & auto_increment set up
        $details["id"] = NULL;
        $hash = Security::hash_password ($details["password"]);
        $details["password"] = $hash["hash"];
        $details["salt"] = $hash["salt"];
        
       	// Insert user
        DB::insert("profiles", $details);
    }
    
    function changeDetail ($detail, $new)
    {
   		// Change user's details
        $oDb = DB::update("profiles", array (
        	'fields' => array ($detail => $new),
        	'where' => array ('id' => $this->id)
        ));
    }
    
    static function hash_password ($password)
    {
        return Security::hash_password($password, $this->salt);
    }
}