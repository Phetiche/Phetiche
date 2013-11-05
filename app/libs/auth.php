<?php

class auth {

	private static function requestBasicLogin($realm)
	{
		header('WWW-Authenticate: Basic realm="' . $realm . '"');
		header('HTTP/1.0 401 Unauthorized');

		echo 'You need to login in order to continue.';
		exit;
	}


	public static function basic($username = '', $password = '', $realm = 'Please authenticate')
	{
		$auth_user = Phetiche_server::PHP_AUTH_USER();
		$auth_pass = Phetiche_server::PHP_AUTH_PW();

		if ($auth_user != $username || $auth_pass != $password) {
			self::requestBasicLogin($realm);
		}

	}

}
