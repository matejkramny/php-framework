<?php

abstract class Database
{
	// public
	
	public function connect(){
		// Connect to mysql server
		$db_connection = mysql_connect(self::getHost(), self::getUsername(), self::getPassword());
		
		if(!defined("fw_mysql"))
			define("fw_mysql", $db_connection);
		
		if (!$db_connection)
			return false;
		
		return true;
	}
	
	public static function close($db_link=fw_mysql){
		// Close connection
		mysql_close($db_link);
	}
	
	public function selectDb($db=null){
		if($db == null)
			$db = self::getDatabase();
		
		// Select database
		mysql_select_db($db, fw_mysql);
	}
	
	// private

	private static function getHost(){
		// Retrieves the most convenient MySQL host.
		// Socket is preferred over log in
		
		if(defined("fw_mysql_host") && defined("fw_mysql_socket")){
			if(fw_mysql_socket != NULL)
			{
				return ':'.fw_mysql_socket;
			}
			else
			{
				$port = (defined("fw_mysql_port")) ? fw_mysql_port : "";
				return fw_mysql_host.":".$port;
			}
		}
	}

	private static function getUsername(){
		// Retrieves MySQL username
		
		if(defined("fw_mysql_user"))
			return fw_mysql_user;
	}

	private static function getPassword(){
		// Retrieves MySQL password
		
		if(defined("fw_mysql_password"))
			return fw_mysql_password;
	}

	private static function getDatabase(){
		// Retrieves MySQL database
		
		if(defined("fw_mysql_database"))
			return fw_mysql_database;
	}
}

// Class to make MySQL usage easier
/*
 * $options to be specified as array
 * array may contain:
 * 'fields' [type array] (if not in $options then all fields are selected),
 * 'where'[type array || string],
 * 'sort'[type array],
 * 'limit' [type array]
 * 
 * arrays may contain field '__comma' which specifies whether the expanding function inserts a comma between the values. e.g. using AND in where clause
 * 
 */

final class DB extends Database {

    // public

    public static function query($query) {
        return mysql_query($query, fw_mysql);
    }
    
    public static function last_insert_id ()
    {
    	return mysql_insert_id(fw_mysql);
    }

    public static function getRow($table, $options=array()) {
        $build = self::buildSelectSQL($table, $options);
		
        $oDb = self::query($build);
        
        if ($oDb && mysql_num_rows($oDb) > 0)
            $oDb = self::query_fetch(array("result" => $oDb, "rows" => 1));
        else
            $oDb = NULL;

        // return first row.
        return $oDb[0];
    }

    public static function getRows($table, $options=array()) {
        $build = self::buildSelectSQL($table, $options);

        $oDb = self::query($build);
        if ($oDb && mysql_num_rows($oDb) > 0)
            $oDb = self::query_fetch(array("result" => $oDb));
        else
            $oDb = NULL;

        // remove blank fields
        if ($oDb != NULL)
            $oDb = array_filter($oDb);

        // return first row.
        return $oDb;
    }

    public static function update($table, $options=array()) {
        $build = self::buildUpdateSQL($table, $options);

        $oDb = self::query($build);
        return $oDb;
    }

    public static function insert($table, $values) {
        $build = "INSERT INTO {$table} SET " . self::expandArray($values, true);
        
        $oDb = self::query($build);
        return $oDb;
    }

    public static function getError() {
        return mysql_error(fw_mysql);
    }

    // strips any unsafe characters from $string
    public static function makeSafe($string, $level='all') {
        $string = urldecode($string);

        if ($level == 'all') {
            $string = strip_tags($string);
        }

        $string = mysql_real_escape_string($string, fw_mysql);

        return $string;
    }

    // private
    // expands an array supplied to SQL compatible string
    protected static function expandArray($array, $expandKey = true, $sort=false, $commas=true) {
        $return = "";

        if (isset($array['__comma']))
            unset($array['__comma']);

        $i = 0;
        if (count($array) > 0) {
            foreach ($array as $key => $value) {
                if ($expandKey)
                    if ($sort)
                        $temp = "{$key} {$value}";
                    else
                        $temp = "{$key}='{$value}'";
                else
                    $temp = "{$value}";
                $comma = ($commas) ? "," : "";
                $return .= ($i == 0) ? $temp : $comma . $temp;
                $i++;
            }
        }

        return $return;
    }

    // fetch array from sql result
    protected static function query_fetch($arr) {
        $return = array();

        if (!$arr["result"])
            return null;

        if (isset($arr['rows']))
            for ($i = 0; $i < $arr['rows']; $i++) {
                $return[] = mysql_fetch_assoc($arr['result']);
            }
        else
            while ($return[] = mysql_fetch_assoc($arr['result']));

        return $return;
    }

    // build sql

    protected static function buildUpdateSQL($table, $options) {
        $build = "UPDATE {$table} SET " . self::expandArray($options['fields'], true);

        if (isset($options["where"]))
            $build .= " WHERE " . self::expandArray($options["where"], true, false, isset($options['where']['__comma']) ? $options['where']['__comma'] : true);

        return $build;
    }

    protected static function buildSelectSQL($table, $options) {
        $build = "SELECT ";

        if (isset($options['fields']))
            $build .= self::expandArray($options['fields'], false);
        else
            $build .= " * ";

        $build .= " FROM {$table}";

        if (isset($options['where']))
        {
        	if (is_array($options['where']))
        		$build .= " WHERE " . self::expandArray($options['where'], true, false, isset($options['where']['__comma']) ? $options['where']['__comma'] : true);
        	else
        		$build .= " WHERE " . $options['where'];
        }

        if (isset($options['sort']))
            $build .= " ORDER BY " . self::expandArray($options['sort'], true, true);

        if (isset($options['limit']))
            $build .= " LIMIT " . self::expandArray($options['limit'], false);

        return $build;
    }

}