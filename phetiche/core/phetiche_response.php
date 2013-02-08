<?php

/**
 * The Phetiche response handler
 * 
 * @file			phetiche/core/phetiche_response.php
 * @description		The response object. This will handle the response related actions.
 * 					Using this method is optional but recommended.
 * @author			Stefan Aichholzer <yo@stefan.ec>
 * @package			Phetiche/core
 * @license			BSD/GPLv2
 *
 * (c) copyright Stefan Aichholzer
 * This source file is subject to the BSD/GPLv2 License.
 */
class Phetiche_response {

	/**
	 * The controller requested in the call
	 * @var string
	 */
	private $request_controller = null;

	/**
	 * The tamplate to be used (if any at all)
	 * @var string
	 */
	private $template = null;
	
	/**
	 * The headers sent along in the response
	 * @var array
	 */
	private $response_headers = null;
	
	/**
	 * The response HTTP status code. Defaults to 200 OK
	 * @var int
	 */
	private $response_code = 200;
	
	/**
	 * The response body to be sent back to the client
	 * @var string
	 */
	private $response_body = null;
	
	/**
	 * If the response was sent set this flag.
	 * @see $this->send()
	 * @see $this->end()
	 * @var string
	 */
	private $response_sent = false;


	/**
	 * The (whole) request
	 * @var Phetiche_request
	 */
	private $req = null;


	public function __set($name, $value) { $this->{$name} = $value; }

	public function __get($name) { return $this->{$name}; }

	public function __call($method, $args)
	{

	}


	/**
	 * Class/object constructor.
	 *
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @param	string $invoking_controller The name of the controller who was called
	 * @param	array $req The data parsed from the request
	 * @return	void
	 */
	public function __construct($invoking_controller = '', $req = array())
	{
		$this->req = $req;
		if ($invoking_controller) {	
			$this->request_controller = $invoking_controller;
		}
	}


	/**
	 * Class/object destructor.
	 * Unset the object itself
	 *
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @return	void
	 */
	public function __destruct()
	{
		unset($this);
	}


	/**
	 * Define a custom method (and arguments) to be called before
	 * sending the response to the client.
	 * 
	 * This comes in handy, since by default sending the response will terminate
	 * the script execution, thus the after() (if defined) will not be triggered.
	 *
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @param	string $function The function to be called before send()
	 * @param	array $args The arguments to be passed to the function
	 * @see		Phetiche_response->send();
	 */
	public function beforeSend($function = null, $args = array())
	{
		$this->beforeSendFunction = ($function) ? $function : null;
		$this->beforeSendArguments = $args;
	}


	/**
	 * Set the HTTP status code.
	 * Throws an error (Phetiche_error) on exceptions.
	 * If the code does not seem to be valid an exception will be thrown.
	 *
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @param	int $code The (HTTP) code to be returned (as a HTTP header)
	 * @return	Self (For chainability)
	 */
	public function httpCode($code)
	{
		if (!is_int($code) || ($code < 100) || ($code > 599)) {
            throw new Phetiche_error(500); //5002
        }

		$this->response_code = $code;
		return $this;
	}


	/**
	 * Set the body to be sent back.
	 *
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @param	string $content The content to be sent back to the client
	 * @return	Self (For chainability)
	 */
	public function body($content = '')
	{
        $this->response_body = $content;
        return $this;
	}

	 
	/**
	 * Send the response to the client.
	 * By default sending a response will end the execution of the application.
	 *
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @return	void
	 */
	public function send()
	{
		if (isset($this->beforeSendFunction)) {
			call_user_func_array($this->beforeSendFunction, $this->beforeSendArguments);
		}

		/**
		 * If the Accept-Encoding is set to either gzip or deflate
		 * then start the compression and let it handle it accordingly
		 * and also the corresponding header.
		 */
		$accept_encoding = Phetiche_request::getHeader('Accept-Encoding');
		if ($accept_encoding == 'gzip' || $accept_encoding == 'deflate') {
			ini_set('zlib.output_compression', 1);
			ini_set('zlib.output_compression_level', 5);
		}

		header('X-Powered-By: The Phetiche open framework');
		header('Content-Type: ' . $this->req->response_format . '; charset=utf-8');

		$code_sent = false;

		if (!$code_sent) {
			if ($this->response_headers) {
				array_walk($this->response_headers, function($header, $header_key) { header($header_key . ': ' . $header['value']); });
				$code_sent = count($this->response_headers) ? true : false;	
			}
		}

        if (!$code_sent) {
            header('HTTP/1.1 ' . $this->response_code);
            $code_sent = true;
        }

		/**
		 * A callback will only be returned when javascript 
		 * (Content-Type: application/javascript; charset=utf-8) is requested.
		 */
		if ($this->response_body) {
			if ($this->req->response_format == 'application/json') {
				print json_encode($this->response_body);
			} else if ($this->req->response_format == 'application/javascript') {
				$callback = (isset($this->req->callback)) ? $this->req->callback : 'pheticheCallback';
				print $callback . '({' . json_encode($this->response_body) . '})';
			} else {

				/**
				 * If any strange Content-Type is received, then it is handled
				 * from here in a separate method. This, basically, to provide flexible
				 * ways to render with any possible Content-Type. 
				 */
				$method = 'format' . ucfirst(str_replace('/', '', $this->req->response_format));
				if (method_exists($this, $method)) {
					print $this->$method();
				} else {
					throw new Phetiche_error(400); //5005
				}
			}
		}
		
		$this->response_sent = true;
	}


	/**
	 * Is an "alias" to send but this one endes the response completely.
	 * This method will end the execution of the application.
	 *
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @see $this->send()
	 * @return	void
	 */
	public function end()
	{
		if (!$this->response_sent) {
			$this->send();
		}
		
		exit(1);
	}


	/**
	 * Format (and print) the output to match the text/html Content-Type
	 *
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @return	void
	 */
	private function formatTexthtml()
	{
		if (is_array($this->response_body)) {
			array_walk($this->response_body, function($item, $key) { print ucfirst($key) . ': ' . $item . '<br />'; });
		} else {
			print $this->response_body;
		}
	}


	/**
	 * Render a template to the client.
	 * Wraps up Smarty as the default template engine.
	 * Throws an error (Phetiche_error) on exceptions.
	 *
	 * Make sure the $variables array is associative, else
	 * an auto-incremental index key will be used by default.
	 *
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @param	string $template_name The name of the template to render
	 * @param	array $variables The variables to be assigned to the template
	 * @see		Smarty();
	 * @see		Phetiche_config::get();
	 * @see 	Smarty->display();
	 * @return	void
	 */
	public function render($template_name = '', $variables = null, $module_template = null)
	{
		if (!$this->template) {

			/**
			 * Instantiate the templating engine (and define its paths -if used)
			 */
			$base_path = Phetiche_server::basePath(true);
			$this->template = new Smarty();
			$this->template->compile_dir = $base_path . Phetiche_config::get('templates/compiled');
			$this->template->cache_dir = $base_path . Phetiche_config::get('templates/cache');
			$this->template->debugging = Phetiche_config::get('templates/debug');
			$this->template->caching = Phetiche_config::get('templates/cache');
			$this->template->cache_lifetime = Phetiche_config::get('templates/cache_lifetime');

			/**
			 * Needed since we have a custom error handler; Phetiche_error()
			 */
			$this->template->muteExpectedErrors();		
		}

		$this->template->template_dir = $base_path . Phetiche_config::get('templates/path');

		/**
		 * If $template_name is an object, most likely it is because we
		 * we are rendering for a module. Take care of that here
		 */
		if (is_object($template_name)) {
			$reflected_properties = new ReflectionClass($template_name);
			$this->template->template_dir = dirname($reflected_properties->getFileName()) . '/views';
			$template_name = ($module_template) ? $module_template : basename(dirname($reflected_properties->getFileName()));
		}

		if (!$template_name && $this->request_controller) {
			$template_name = $this->request_controller;
		}

		if (!$template_name) {
			throw new Phetiche_error(500); // 5001
		}

		/**
		 * Assign any possible variables to the template.
		 */ 
		if ($variables) {
			foreach ($variables as $key => $value) {
				$this->template->assign($key, $value);
			}
		}

		$this->template->display($template_name . '.tpl');
	}

}
