<?php

/**
 * The Phetiche request handler
 *
 * @file			phetiche/core/phetiche_request.php
 * @description		The request object. This will handle the request (arguments, methods, input, etc.).
 * @author			Stefan Aichholzer <yo@stefan.ec>
 * @package			Phetiche/core
 * @license			BSD/GPLv2
 *
 * (c) copyright Stefan Aichholzer
 * This source file is subject to the BSD/GPLv2 License.
 */
final class Phetiche_request extends Phetiche_url {

	/**
	 * Request headers
	 * Extract all headers sent in any request.
	 *
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @param	boolean $full_headers Return the full headers
	 * @return	array $headers The headers obtained from the request
	 */
	public static function headers($full_headers = false)
	{
		$headers = array();

		if (function_exists('getallheaders')) {
			$headers = getallheaders();
		} else if (function_exists('apache_request_headers')) {
			$headers = apache_request_headers();
		}

		if ($full_headers) {
			$headers = array_merge($headers, $_SERVER);
		}

		/**
		 * No need to filter the headers, not at this point.
		 * array_walk($headers, function(&$item) { $item = filter_var($item, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); });
		 */

		return $headers;
	}


	/**
	 * Get header
	 * Extract a single header from the request.
	 *
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @param	string $header The name of the header to be fetched
	 * @return	mixed The header found. (Boolean) false if not found
	 */
	public static function getHeader($header = null)
	{
		$headers = self::headers();
		return (isset($headers[$header])) ? $headers[$header] : false;
	}


	/**
	 * Read request input
	 * Extracts the parameters passed in either the GET or the POST
	 * requests.
	 * See the inline documentation for more details.
	 *
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @param	object $data The object containing the input (request)
	 * @param	boolean $get_only If only GET requests are to be processed
	 * @return	array The data read from the request
	 *
	 * @todo	Further request methods should be handled
	 */
	public static function readInput($data, $get_only = false)
	{
		$data['php_input_stream'] = $data['php_input_stream_raw'] = null;
		$request = null;

		switch (Phetiche_server::REQUEST_METHOD()) {
			case 'GET': $request = $_GET; break;
			case 'POST': $request = $_POST; break;
		}

		if ($request != 'GET' && $get_only) {
			return $data;
		}

		if ($request) {
			foreach ($request as $key => $arg) {
				if (!empty($arg)) {

					/**
					 * In case the argument ($arg) is an array of HTML elements,
					 * process it accordingly. Set the key as the parent and each item as
					 * a sub-item of it.
					 */
					if (is_array($arg)) {
						foreach ($arg as $arg_key => $arg_item) {
							$data[$key][$arg_key] = html_entity_decode($arg_item);
						}
					} else {
						$data[$key] = html_entity_decode($arg);
					}
				} else {
					$data[$key] = false;
				}
			}
		}

		$content_type = self::getHeader('Content-Type');

		/**
		 * Only read the PHP input stream if the header is not
		 * multipart/form-data, since it is not compatible with FILES
		 * anyway. It is not possible to PUT to PHP data and images at the same time:
		 * http://php.net/manual/en/features.file-upload.put-method.php
		 */
		if (strpos($content_type, 'multipart/form-data') === false) {
			if ($data['php_input_stream_raw'] = file_get_contents("php://input")) {

				/**
				 * If the request header contains a valid Content-Type
				 * and it happens to be JSON, then save the developer one step.
				 */
				if (strpos($content_type, 'application/json') !== false) {
					if ($decoded_input = json_decode($data['php_input_stream_raw'])) {
						$data['php_input_stream'] = $decoded_input;
					}
				} else {
					$data['php_input_stream'] = $data['php_input_stream_raw'];
				}
			}
		}

		return $data;
	}

}
