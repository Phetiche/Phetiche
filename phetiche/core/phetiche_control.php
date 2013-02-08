<?php

/**
 * The Phetiche control handler
 *
 * @file			phetiche/core/phetiche_control.php
 * @description		The control object. This will handle all requests and instantiate
 * 					the corresponding objects. This object also does object (APC) catching.
 * @author			Stefan Aichholzer <yo@stefan.ec>
 * @package			Phetiche/core
 * @license			BSD/GPLv2
 *
 * (c) copyright Stefan Aichholzer
 * This source file is subject to the BSD/GPLv2 License.
 */
final class Phetiche_control {

	/**
	 * Initialize any given controller
	 * See the inline documentation for more details
	 *
	 * $app_modules is currently not used in controller
	 * but is it passed into it anyway. Maybe one day it makes us happy that it
	 * is around here. ;)
	 *
	 * @author Stefan Aichholzer <yo@stefan.ec>
	 * @param string $controller The controller being called
	 * @param string $method The method being called
	 * @param array $req Any arguments passed to the call (command line)
	 * @param boolean $command_call If the call is being made from command line
	 * @param array $app_modules The modules to be enabled for the app
	 * @throw Phetiche_error();
	 * @return void
	 */
	public static function init($controller = false, $method = false, $req = null, $command_call = false, $app_modules = null)
	{
		/**
		 * Check if the user created a custom URL binding file.
		 * If so, then use those custom routes before throwing an exception.
		 */
		Phetiche_control::routeCustomUrls($controller, $method);
		$controller = ($controller) ? $controller : 'Phetiche_index';

		/**
		 * If the controller already exists in the APC cache, then
		 * use the one from there, else create it here.
		 * Default TTL is 180 seconds.
		 */
		$reflected_properties = new ReflectionClass($controller);
		if (!$control = Phetiche_apc::fetch($controller)) {

			/**
			 * If we still don't have a controller at this point, then something
			 * is defenitely wrong. If a controller which does not exist is called, then
			 * it will be handled by the auto-loader.
			 */
			if (!class_exists($controller)) {
				throw new Phetiche_error(404);
			}

			/*
			 * Look for controller specific configuration
			 * If such configuration is found load it into the
			 * global configuration
			 */
			$class_file_name = $reflected_properties->getFileName();
			$class_file_path = dirname($class_file_name);
			$controller_config = $class_file_path . '/config/' . $controller . '.inc';
			if (file_exists($controller_config)) {
				include_once($controller_config);

				/**
				 * @todo This config should be removed when the controller is no longer in use,
				 * so it is visible to only that one controller.
				 */
				Phetiche_config::merge($config);
			}

			/**
			 * Load the actual controller
			 */
			$control = new $controller();
			$control->reflected_properties = $reflected_properties;

			/**
			 * Store the newly created controller object in the APC cache
			 */
			Phetiche_apc::store($controller, $control);
		}

		/**
		 * Is the current use of the framework a REST implementation?
		 * Then assign the proper method to be used from the controller.
		 * Valid methods are GET, POST, PUT, DELETE.
		 */
		if ($reflected_properties->isSubclassOf('Phetiche_REST_Controller')) {
			$request_method = Phetiche_server::REQUEST_METHOD();
			$method = strtolower((($request_method) ? $request_method : 'GET')) . 'Request';
		}

		/**
		 * If a method was not requested, then we still try to load the
		 * index() method in the requested controller. This sort of takes care
		 * of basic default functionality. REST controllers don't need to implement an
		 * index() method.
		 *
		 * Finally, look if the requested method exists in the requested controller.
		 * If not we still allow the use of an overloading __call() method.
		 * If neither the requested method, or the index() nor the over-loader exist,
		 * then, again, something must be wrong.
		 */
		if (!$method) {
			if (method_exists($controller, 'index')) {
				$method = 'index';
			} else if (method_exists($controller, '__call')) {
				$method = '__call';
			} else {
				throw new Phetiche_error(404);
			}
		}

		/**
		 * If the controller extends any of the Phetiche (BASIC or REST) controllers, then this
		 * function should be available. So, let's call it.
		 */
		if (method_exists($control, 'loadRequestResponse')) {
			$control->loadRequestResponse($req, new Phetiche_response($controller, $req));
		}

		/**
		 * Fire up the before() method, if it exists
		 */
		if (method_exists($control, 'before')) {
			$control->before();
		}

		/**
		 * Invoke the actual requested method
		 */
		$control->$method();

		/**
		 * Fire up the after() method, if it exists
		 */
		if (method_exists($control, 'after')) {
			$control->after();
		}

		/**
		 * Clean up some loose ends and references
		 */
		if (method_exists($control, 'tearDown')) {
			$control->tearDown();
		}
	}

	/**
	 * Loads any given controller
	 * It also puts the object in the APC cache, if it's not
	 * already stored there.
	 *
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @param	string $controller The name of the controller being loaded
	 * @return	object The loaded controller
	 */
	public static function load($controller = null)
	{
		if (!$controller) {
			return false;
		}

		if (!$control = Phetiche_apc::fetch($controller)) {
			$control = new $controller();
			Phetiche_apc::fetch($controller, $control);
		}

		return $control;
	}

	/**
	 * Routes requests to any custom given URL
	 * These custom URLs should be defined in the app/config.inc
	 * file in the app/routes section.
	 *
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @param	string ref. $controller The controller to be used
	 * @param	string ref. $method The method to be used
	 */
	private static function routeCustomUrls(&$controller, &$method)
	{
		if ($app_routes = Phetiche_config::get('app/routes')) {

			if (is_array($app_routes)) {

				$pattern = array('/\//');
				$replacement = array('\/');

				$request_uri = preg_replace('/\/?\?.*/', '', Phetiche_server::REQUEST_URI());
				foreach ($app_routes as $key => $url) {
					$key = preg_replace($pattern, $replacement, $key);
					if (preg_match('/'.$key.'/', $request_uri)) {
						$part = preg_split('/\//', $url, -1, PREG_SPLIT_NO_EMPTY);
						if (count($part) == 2) {
							$controller = $part[0];
							$method = $part[1];
							break;
						}
					}
				}
			}
		}
	}

}
