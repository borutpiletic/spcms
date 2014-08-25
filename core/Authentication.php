<?php
/**
 * spcms\core\Authentication
 * 
 * @TODO
 * - Authentication only from allowed list of IP addresses
 * 
 */
namespace spcms\core;

class Authentication extends BaseClass
{
	/**
	 * Authentication succeeded.
	 */
	const STATUS_SUCCEED = 'success';
	
	/**
	 * Invalid authentication data (username or password).
	 */
	const STATUS_ERROR_AUTH = 'auth';
	
	/**
	 * Authentication data is inactive (user was disabled).
	 */
	const STATUS_ERROR_INACTIVE = 'inactive';

	/**
	 * Allow users to login with username or email.
	 * @var boolean
	 */
	public $loginWithEmail = true;

	/**
	 * Method for user authentiaction.
	 * @return array Array with 'status' and 'user' object.
	 */
	public function authenticate($username, $password)
	{ print $password;
		// Build credentials info
		$credentials = array(
			':username' => $username,
			':email' => $username,
			':password' => self::generatePasswordHash($password)
		);
		// Auth response returning user & status		
		$_authReturn = array('user' => null);
		
		// Login allowed with username or email, so we need to check for both
		// else check only for username
		if ($this->loginWithEmail === true) 
			$sth = "SELECT * FROM mod_user WHERE (username = :username OR email = :email) AND passwordHash = :password LIMIT 1";
		else
			$sth = "SELECT * FROM mod_user WHERE username = :username AND passwordHash = :password LIMIT 1";
		
		// Lets try to load user with specified credentials
		$user = \SimplCMS::db()->prepare($sth);		
		
		if ($user->execute($credentials) === true && ($user = $user->fetchObject()) !== false)
		{
			if ($user->active == 0)
				$_authReturn['status'] = self::STATUS_ERROR_INACTIVE;
			else
				$_authReturn['status'] = self::STATUS_SUCCEED;
			
			$_authReturn['user'] = $user;
		}	
		else
			// User not loaded, reporting auth failure
			$_authReturn['status'] = self::STATUS_ERROR_AUTH;
		
		return $_authReturn;
	}
	
	/**
	 * Mechanism for creating secure password hash using Blowfish algorithm.
	 * @param type $clearTextPassword
	 * @return string
	 */
	public static function generatePasswordHash($clearTextPassword)
	{
		// Random salt generation
		// Prefix explained: 
		// $2a$ means we are using Blowfish
		// 10$: 10 cost of computation power (higher number uses more processing power)
		$salt = base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM));
		$salt = '$2a$%10$'. $salt;
		
		return crypt($clearTextPassword, $salt);
	}
}
