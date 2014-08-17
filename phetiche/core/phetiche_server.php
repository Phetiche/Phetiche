<?php

/**
 * The Phetiche server handler
 *
 * @file			phetiche/core/phetiche_server.php
 * @description		The server object. This will handle anything related to the server.
 * 					Using this method is optional but recommended.
 * @author			Stefan Aichholzer <play@analogbird.com>
 * @package			Phetiche/core
 * @license			BSD/GPLv2
 *
 * (c) copyright Stefan Aichholzer
 * This source file is subject to the BSD/GPLv2 License.
 */
class Phetiche_server {

	/**
	 * Provides a wrapper to get server ($_SERVER) variables
	 * at runtime. This method should be used over directly using the $_SERVER
	 * variable as this does some filtering of the actual variables.
	 *
	 * Possible options are (As defined for the PHP $_SERVER variable):
	 * 	REDIRECT_STATUS
     * 	HTTP_HOST
     * 	HTTP_USER_AGENT
     * 	HTTP_ACCEPT
     * 	HTTP_ACCEPT_LANGUAGE
     * 	HTTP_ACCEPT_ENCODING
     * 	HTTP_DNT
     * 	HTTP_CONNECTION
     * 	HTTP_CACHE_CONTROL
     * 	PATH
     * 	SERVER_SIGNATURE
     * 	SERVER_SOFTWARE
     * 	SERVER_NAME
     * 	SERVER_ADDR
     * 	SERVER_PORT
     * 	REMOTE_ADDR
	 *  REDIRECT_QUERY_STRING
     * 	DOCUMENT_ROOT
     * 	SERVER_ADMIN
     * 	SCRIPT_FILENAME
     * 	REMOTE_PORT
     * 	REDIRECT_URL
     * 	GATEWAY_INTERFACE
     * 	SERVER_PROTOCOL
     * 	REQUEST_METHOD
     * 	QUERY_STRING
     * 	REQUEST_URI
     * 	SCRIPT_NAME
     * 	PHP_SELF
     * 	REQUEST_TIME_FLOAT
     * 	REQUEST_TIME
	 *
	 * @author	Stefan Aichholzer <play@analogbird.com>
	 * @param	string $method The method being called.
	 * @param	array $args Any parameters passed in the call (Not used here).
	 * @return	mixed $response The value (string) on success or false (boolean) on failure.
	 */
	public static function __callStatic($method = false, $args = null)
	{
		$response = false;

		if ($method && isset($_SERVER[$method])) {
			$response = filter_var($_SERVER[$method], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

			if ($response) {
				switch ($method) {
					case 'REMOTE_ADDR':	$response = filter_var($_SERVER[$method], FILTER_VALIDATE_IP) ? $_SERVER[$method] : false;
						break;

					case 'DOCUMENT_ROOT':
						$response = ($response[strlen($response)-1] == '/') ? $response : $response . '/';
						break;
				}
			}
		}

		return $response;
	}


	/**
	 * Returns the base path of the Phetiche installation
	 * (on level before /public)
	 *
	 * @author	Stefan Aichholzer <play@analogbird.com>
	 * @return	string $base_path The base Phetiche installation path.
	 */
	public static function basePath($append_slash = false)
	{
		$base_path = dirname(self::DOCUMENT_ROOT()) . (($append_slash) ? '/' : '');
		return $base_path;
	}

}
