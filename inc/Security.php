<?php

// Dependancies
require_once (fw_dir_inc."Browser.php");
require_once (fw_dir_lib."settings.php");

class Security{
	var $authenticatedUser;
	
	public static function hash_password($password, $salt=NULL)
	{
		// Hashing algorithm for passwords
		// Produces a 512bit password hash
		
		if($salt == NULL) $salt = self::get_salt();
		
		// Encrypt the $password with obtained $salt
		$oHash = crypt($password, '$6$rounds=3249$'.$salt.'$');
		
		// Extract essential strings from hashed string
		$oExplode = explode("$",$oHash, 5);
		
		$hash = $oExplode[4];
		$salt = $oExplode[3];
		
		// Return array with Salt and Hashed password
		return array("salt" => $salt, "hash" => $hash);
	}
	
	public static function get_salt($user=NULL)
	{
		// Retrieves salt
		// or generates new if user is not logged in
		
		if($user == NULL)
		{
			// generate new & assign
			
			$oRand = uniqid();
			$oHash = sha1(md5($oRand));
			
                        // cut string to <= 16 chars
			$oSalt = substr($oHash, (int)rand(0,3), (int)rand(9,16));
			
			return $oSalt;
		}
		else
		{
			return DB::getRow("profiles",array("fields" => array('salt'), 'where' => array("id" => $user)));
		}
	}
	
	public function checkBan($ip=NULL, $user=NULL)
	{
		// Checks if a user/ip is banned from using the system
		
		if($ip == NULL)
			$ip = fw_user_ip;
		
		if($user == NULL);
			//$user = fw_user_id;
		
		// check
		if(!defined("fw_settings_ban"))
			define("fw_settings_ban", "ip");
		
		// Ban is set on user
		if(fw_settings_ban == "user")
		{
			if($this->check_uid($user))
			{
				$this->displayBanned ($uid);
			}
		}
		// Ban is set on IP addresses
		else if(fw_settings_ban == "ip")
		{
			if($this->check_ip($ip))
			{
				$this->displayBanned($ip);
			}
		}
	}
	
	public function check_ip ($ip = NULL)
	{
		// Checks IP address for Ban purposes
		
		if($ip == NULL)
			$ip = fw_user_ip;
		
		// Retrieve records from Ban table
		$oDb = DB::getRow("sys_bans", array("where" => array( "ip" => $ip, "AND Expires >" => time(), "__comma" => false )));
		
		// returns true upon ip being found and thus banned
		if($oDb != NULL)
			return true;
		
		return false;
	}
	
	public function check_uid ($uid = NULL)
	{
		// Checks a user ID for Ban purposes
		
		if($uid == NULL)
			$uid = fw_user_id;
		
		$oDb = DB::getRow("sys_bans", array("where" => array( "uid" => $uid, "AND Expires >" => time(), "__comma" => false )));
		
		if($oDb != NULL){
			// returns true if uid is banned
			return true;
		}
		
		return false;
	}
	
	// private
	
	private function displayBanned($data)
	{
		// Tells the user that they have been banned
		
		// TODO: template displays banned msg
		
		// check if ip or user-id (ip is in string format)
		if(is_numeric($data))
			$msg = "User ID {$data} is blocked on our systems.";
		else
			$msg = "IP Address {$data} is blocked on our systems.";
		
		// Display plain answer
		echo $msg;
		
		// Exit script
		die();
	}
	
	public function verifyUser ()
	{
		// Veryfies user session
		
		// cookie verification
		$token = (isset($_SESSION['token'])) ? $_SESSION['token'] : false;
		if (!$token)
		{
			$this->createToken();
		}
		
		$expiry = (isset($_SESSION['expiry'])) ? $_SESSION['token'] : 1;
		
		if ($_SESSION['expiry']-1 < time())
		{
			$this->recreateSession ();
			return;
		}
		
		if (isset($_SESSION['u']) && is_array($_SESSION['u']) && $_SESSION['loggedIn'])
			if ($this->verifyLogin ())
				$this->authenticatedUser = $_SESSION['u'];
		else
			$this->failedVerification();
	}
	
	protected function verifyLogin ()
	{
		// Verify login credentials
		// Could have been deleted while user is logged in
		
		$oDb = DB::getRow ("profiles", array (
			"where" => array (
				"id" => $_SESSION['u']['id']
			)
		));
		
		if (!$oDb)
			return false;
		
		// security matter
		unset ($oDb['salt']);
		unset ($oDb['password']);
		
		// reload session fresh from database
		$_SESSION['u'] = $oDb;
	}
	
	public function recreateSession ()
	{
		// Recreate session by logging out
		$_SESSION['loggedIn'] = false;
	}
	
	protected function failedVerification ()
	{
		// User has failed authentication
		$this->authenticatedUser = null;
	}
	
	private function createToken ()
	{
		// token is randomly generated string
		$token = substr( sha1( md5( uniqid() ) ), 2, 34);
		$expiry = time() + (60 * 30);
		
		$_SESSION['token'] = $token;
		$_SESSION['expiry'] = $expiry;
		
		$this->recreateSession();
	}
}

$fw_security = new Security;

$fw_security->checkBan();
$fw_security->verifyUser();