<?php

class Phetiche_session {

	/**
	 * 691200 seconds = 8 days
	 *
	 * @param int $lifetime
	 * @param int $cookie_life
	 */
	private static function init($lifetime = 691200, $cookie_life = 691200)
	{
		ini_set('session.gc_divisor', 1);
		ini_set('session.gc_maxlifetime', $lifetime);
		ini_set('session.cookie_lifetime', $cookie_life);
		ini_set('session.use_trans_sid', 0);
		ini_set('session.use_only_cookies', 1);

		session_start();
	}

	public static function getID()
	{
		if (!(boolean)session_id()) {
			self::init();
		}

		return session_id();
	}

}