<?php

class Phetiche_memcache {

	/**
	 * The memcache object
	 * @var object
	 */
	private static $memcache = null;

	public function prepare()
	{
		self::$memcache = new Memcache;
		if (!self::$memcache->connect('localhost', 11211)) {
			throw new Phetiche_error('Could not connect with memcache.');
		}
	}
	
	public static function get($var)
	{
		if (!self::$memcache) {
			self::prepare();
		}

		return self::$memcache->get($var);
	}
	
	public static function set($var, $value, $compress = false, $duration = 3600)
	{
		if (!self::$memcache) {
			self::prepare();
		}

		$compress = $compress ? MEMCACHE_COMPRESSED : 0;
		self::$memcache->set($var, $value, $compress, time() + $duration);
	}
	
	
	public static function delete($var)
	{
		self::$memcache->delete($var);
	}

}