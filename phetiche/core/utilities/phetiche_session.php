<?php

class Phetiche_session {

	private static $session = NULL;

	public function __construct($lifetime = 1200, $cookie_life = 1200)
	{
		ini_set('session.gc_probability', 1);
		ini_set('session.gc_divisor', 1);
		ini_set('session.gc_maxlifetime', 1200);
		ini_set('session.cookie_lifetime', 1200);
		ini_set('session.use_trans_sid', 0);
		ini_set('session.use_only_cookies', 1);

		// Is this->session is null
		session_start();
	}
	
	public static function get($var)
	{
		return self::$memcache->get($var);
	}
	
	public static function set($var, $value, $compress = false, $duration = 3600)
	{
		$compress = $compress ? MEMCACHE_COMPRESSED : 0;
		self::$memcache->set($var, $value, $compress, time() + $duration);
	}
	
	
	public static function delete($var)
	{
		self::$memcache->delete($var);
	}

}