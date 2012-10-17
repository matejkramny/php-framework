<?php

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

abstract class DatabaseDriver () {
	public function connect ();
	public function query ($query);
}

