<?php

	/**
	 * For command line help (application related) enter 'helpme'
	 * in the command line call: "php -f path_to_file/index.php helpme
	 */
	$public_path = dirname(__FILE__);
	require_once(dirname($public_path) . '/phetiche/Phetiche.php');

	/**
	 * Start the actual application.
	 *
	 * If no valid controller and method are specified
	 * then the application will respond with the phetiche_index controller and the index() method.
	 * If only a valid controller is specified, it will respond with the index() method
	 * defined in that (the requested) controller, this, of course, if the application implementation is not
	 * for REST purposes, else -as you know- the getRequest() method will respond.
	 */

	new Phetiche_app();
