<?php

define ('fw_dir_inc', '../');
define ('fw_dir_lib', './');

include ("../Security.php");

var_dump(Security::hash_password("Hello", "salt"));

?>
