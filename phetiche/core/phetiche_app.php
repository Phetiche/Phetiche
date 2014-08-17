<?php

/**
 * The Phetiche application handler
 *
 * @file			phetiche/core/phetiche_app.php
 * @description		The actual application object.
 * @author			Stefan Aichholzer <play@analogbird.com>
 * @package			Phetiche/core
 * @license			BSD/GPLv2
 *
 * (c) copyright Stefan Aichholzer
 * This source file is subject to the BSD/GPLv2 License.
 */
final class Phetiche_app {

	/**
	 * Class/object constructor.
	 * Returns an error (Phetiche_error) on exceptions.
	 *
	 * $app_modules is currently not used in the app but is it passed into
	 * it -and from here into the controller. Maybe one day it makes us happy that it
	 * is around here. ;)
	 *
	 * @author Stefan Aichholzer <play@analogbird.com>
	 * @see	Phetiche_config::get();
	 * @see	RedBean_ModelHelper::setModelFormatter();
	 * @see	Phetiche_BASIC_model_formatter();
	 * @see	Phetiche_request::load();
	 * @see	Phetiche_error::send();
	 * @return void
	 */
	public function __construct()
	{
		/**
		 * Try to setup what we need and then call
		 * the controller and method being requested.
		 */
		try {

			/**
			 * Make sure we can see errors
			 */
			if (!ini_get('display_errors')) {
				ini_set('display_errors', '1');
			}

			$this->startDB();

			/**
			 * Finally, let's get the real magic going ;)
			 * Yeah!
			 */
			$controller = Phetiche_request::load()->controller();
			$method = Phetiche_request::load()->method();
			Phetiche_control::init($controller, $method, Phetiche_request::load()->allArgs(), false);

		} catch (Exception $e) {
			Phetiche_error::send($e->getMessage());
		}
	}

	/**
	 * Class/object destructor.
	 *
	 * @author	Stefan Aichholzer <play@analogbird.com>
	 * @return	void
	 */
	public function __destruct()
	{

   	}

}
