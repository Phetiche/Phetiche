<?php

/**
 * The Phetiche url handler
 *
 * @file			phetiche/core/phetiche_url.php
 * @description		The URL (URI) object. This will handle anything related to the URI.
 * @author			Stefan Aichholzer <play@analogbird.com>
 * @package			Phetiche/core
 * @license			BSD/GPLv2
 *
 * (c) copyright Stefan Aichholzer
 * This source file is subject to the BSD/GPLv2 License.
 */
class Phetiche_url {

	/**
	 * The structure of the URI
	 * //controller/method
	 * @var array
	 */
	private static $url_components = ['controller', 'method'];

	/**
	 * The URI (parts) being loaded
	 * @var array
	 */
	public static $loaded_url = [];

	public function __set($name, $value) { $this->{$name} = $value; }

	public function __get($name) { return (isset($this->{$name}) ? $this->{$name} : null); }

	public function __call($method, $args)
	{
		/**
		 * Return all items extracted from the URL, so they can be
		 * easily mapped to any method.
		 */
		if ($method == 'allArgs' && is_array($this->part)) {
			foreach ($this->part as $key => $arg) {
				$this->{$key} = $arg;
			}

			/**
			 * $this->req->a();
			 * Phetiche_request::load()->b();
			 * $this->req->c;
			 *
			 * Unsetting this element only allows the first type of variable access,
			 * which is prefered anyway.
			 */
			unset($this->part);
			return $this;
		}

		return (isset($this->part[$method])) ? $this->part[$method] : false;
	}

	public function __construct()
	{

	}

	/**
	 * Class/object destructor.
	 * Unset the object itself
	 *
	 * @author	Stefan Aichholzer <play@analogbird.com>
	 * @return	void
	 */
	public function __destruct()
	{
		unset($this);
	}

	/**
	 * Create an instance of "itself" for
	 * overall use as an object.
	 *
	 * @author	Stefan Aichholzer <play@analogbird.com>
	 * @param	string $url	The URL to be processed
	 * @see		Phetiche_request();
	 * @see 	processURL();
	 * @return	object An instance of self
	 */
	public static function load($url = '')
	{

		if (!isset(self::$loaded_url[$url])) {
			self::$loaded_url[$url] = new Phetiche_request();
			self::$loaded_url[$url]->part = self::processURL();

			/**
			 * Load and process the command line call -and arguments-, if any
			 */
			if ($command_call = Phetiche_config::get('command_arguments')) {
				 self::processCommandCall($command_call, self::$loaded_url[$url]->part);
			}
		}

		return self::$loaded_url[$url];
	}

	/**
	 * Process the URL into a valid, usable, Phetiche_request object.
	 * This also sets the requested controller and method.
	 *
	 * @author	Stefan Aichholzer <play@analogbird.com>
	 * @param	string $url	The URL to be processed
	 * @see		explodeURL();
	 * @see 	processURL();
	 * @see		Phetiche_request::readInput();
	 * @return	array The data parsed from the request URI
	 */
	public static function processURL($url = '')
	{
		$parsed_url = self::explodeURL($url);
		foreach($parsed_url as $key => $url) {

			/**
			 * We don't want to process the input stream content.
			 */
			if ($key === 'php_input_stream') {
				continue;
			} else {

				$custom_url_part = explode(':', $url);
				if (isset($custom_url_part[1])) {
					$parsed_url[$custom_url_part[0]] = $custom_url_part[1];
				}

				// Remove the default (empty) values from the parsed array
				if (is_int($key) || empty($parsed_url[$key])) {
					unset($parsed_url[$key]);
				}

				/**
				 * Collect all "fancy" parameters
				 * http://phetiche/controller/method/arg[0]/arg[1]/arg[2]/arg[3] ...
				 */
				if (!in_array($url, $parsed_url) && !empty($url) && $key != 0) {
					$parsed_url['uri_args'][] = $url;
				}
			}
		}

		/**
		 * If no request ID is found, then NULL it so we can still use (check) it
		 */
		$parsed_url['controller'] = (isset($parsed_url['controller'])) ? $parsed_url['controller'] : null;
		$parsed_url['method'] = (isset($parsed_url['method'])) ? $parsed_url['method'] : null;
		$parsed_url['request_id'] = (isset($parsed_url['request_id'])) ? $parsed_url['request_id'] : null;
		$parsed_url['routed_to'] = null;

		/**
		 * Now that we have a final array on (request) data, we can merge the other
		 * request method's data, such as POST and PUT.
		 */
		return Phetiche_request::readInput($parsed_url);
	}

	/**
	 * Parse the command line arguments.
	 *
	 * @author	Stefan Aichholzer <play@analogbird.com>
	 * @param string $command_call The command line (cli) call being issued
	 * @param array $part (Reference) The parsed URL array.
	 * @return	void
	 */
	private static function processCommandCall($command_call, &$part)
	{
		if (in_array('helpme', $command_call)) {
			echo "\n" . ' This is the Phetiche command line help.' . "\n" .
				 ' =======================================' . "\n\n" .
				 ' In order to properly call any controller/method from the command line,' . "\n" .
				 ' you must provide, at least, two arguments after your script:' . "\n\n" .
				 '  - the controller and' . "\n" .
				 '  - the method.' . "\n\n" .
				 ' An example call would look like this:' . "\n\n" .
				 '    php -f path_to_your-script/your_script.php controller method arg:value' . "\n\n\n";
		} else {
			$part['controller'] = (isset($command_call[1])) ? $command_call[1] : null;
			$part['method'] = (isset($command_call[2])) ? $command_call[2] : null;

			/**
			 * Map an array of parameters to the method being called
			 */
			$args = [];
			if (count($command_call) > 3) {
				$args = array_slice($command_call, 3, count($command_call));
				$part['argv'] = [];
				if ($args[0] == '-u' && $args[1]) {
					parse_str($args[1], $part['argv']);
				} else {
					foreach ($args as $key => $arg) {
						list($name, $value) = explode(':', $arg);
						$part['argv'][$name] = $value;
					}
				}

				$part['argv'] = (object)$part['argv'];
			}
		}

		$part['response_format'] = $part['request_id'] = $part['php_input_stream'] = null;
		unset($part['response_format'], $part['request_id'], $part['php_input_stream']);
		$command_call = null;
	}

	/**
	 * Handles both types of GET requests; old-school and the
	 * Phetiche format. It does a regular expression split on the URL
	 * to determine the basic arguments being passed in.
	 *
	 * @author	Stefan Aichholzer <play@analogbird.com>
	 * @param	string $url	The URL to be exploded
	 * @see		Phetiche_server::REQUEST_URI();
	 * @see 	Phetiche_request::readInput();
	 * @see		Phetiche_request::getHeader();
	 * @return	array The data parsed (exploded) from the request URI
	 */
	public static function explodeURL($url = '')
	{
		$request_uri = Phetiche_server::REQUEST_URI();
		$parsed_url = [];

		$query_position = strpos($request_uri, '?');
		$url = ($url) ? $url : (($query_position) ? substr($request_uri, 0, $query_position) : $request_uri);
		$part = preg_split('/\//', $url, -1, PREG_SPLIT_NO_EMPTY); // Deprecate this, as this techniques does not provide $_SERVER[REDIRECT_QUERY_STRING]

		/**
		 * Get all GET parameters and put them into the object to be processed.
		 * Only do this for GET parameters since others, such as POST or PUT, could get
		 * messed up.
		 */
		$part = Phetiche_request::readInput($part, true);

		$parsed_url['response_format'] = 'application/json';

		/**
		 * This can (and will) be overwritten if the type is specified in the requesting URI.
		 * If * is accepted, JSON will still be used as the default.
		 */
		$accept_header = Phetiche_request::getHeader('Accept');
		if (strpos($accept_header, ',') !== false) {
			$first_response = substr($accept_header, 0, strpos($accept_header, ','));
			$parsed_url['response_format'] = $first_response;
		}

		foreach($part as $key => $url) {

			/**
			 * Get the response type
			 * Only process actual strings.
			 */
			if (is_string($url) && preg_match('/(javascript|json|txt|html|xml)/i', $url, $matches)) {

				// Set the proper response format.
				switch (strtolower($matches[0])) {
					case 'javascript':	$parsed_url['response_format'] = 'application/javascript'; break;
					case 'json':		$parsed_url['response_format'] = 'application/json'; break;
					case 'txt':			$parsed_url['response_format'] = 'text/plain'; break;
					case 'html':		$parsed_url['response_format'] = 'text/html'; break;
					case 'xml':			$parsed_url['response_format'] = 'application/xml'; break;
				}

			} else {

				if (!isset($parsed_url[self::$url_components[0]]) && strpos($url, ':') === false && !is_numeric($url)) {
					$parsed_url[self::$url_components[0]] = $url;
				} else if (!isset($parsed_url[self::$url_components[1]]) && strpos($url, ':') === false && !is_numeric($url)) {
					$parsed_url[self::$url_components[1]] = $url;
				} else {
					$parsed_url[$key] = $url;
				}

				/**
				 * In RESTful applications we may want to rely on the fact
				 * that just a number is provided in any given URI, to be able to
				 * use this ID, check for it here and create the "request_id" element.
				 * The first number (int) found will be used as the request_id.
				 */
				if (is_numeric($url) && !isset($parsed_url['request_id'])) {
					$parsed_url['request_id'] = $url;
				}
			}
		}

		return $parsed_url;
	}

}
