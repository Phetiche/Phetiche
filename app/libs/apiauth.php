<?php

final class apiauth {

	public static function login($user_data = false)
	{	
		$authorized = false;
		if ($user_data) {

			/**
			 * If the login details are provided when calling the method.
			 * This is usefull to build password protected sites.
			 */
			if ($logged = _auth::login($user_data)) {
				$authorized = true;
			}

		} else {

			/**
			 * DEPRECATED
			 * Since (Jan.2012) and should no longer be implemented
			 * on the client side as is poses a potential security threat.
			 */
			if ($api_key = Phetiche_request::load()->apikey()) {

				/**
				 * If the "login" is done via a GET request, in this case no actual
				 * login happens, only a key validation.
				 */
				$api = _control::load('api');
				if ($api->getTokenForAPIKey($api_key)) {
					$authorized = true;
				}

			} else {

				/**
				 * If the login is done from a POST request, in this case a full featured
				 * authentication challenge is made by the server on the requester.
				 *
				 * We can't implicitly check if it a POST request or not, since the authentication
				 * process has a handshake first, in which case the POST vars will be empty.
				 */
				if ($logged = _auth::login()) {
					$authorized = true;
				}

			}
 
		}

		if ($authorized) {
			return true;
		} else {
			throw new _error('UNAUTHORIZED');
		}

	}

}
