<?php

/**
 * The Phetiche application handler
 *
 * @file			phetiche/core/phetiche_app.php
 * @description		The actual application object.
 * @author			Stefan Aichholzer <yo@stefan.ec>
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
	 * @author Stefan Aichholzer <yo@stefan.ec>
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
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @return	void
	 */
	public function __destruct()
	{

   	}

	/**
	 * DB setup.
	 *
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @return	void
	 */
   	private function startDB()
   	{
	   	/**
		 * Start using a DB (if one has been defined in the config file)
		 */
		 $valid_engines = array('mysql', 'pgsql', 'cubrid', 'sqlite');

		$db_settings = Phetiche_config::get('db');
		if ($db_settings && in_array($db_settings['engine'], $valid_engines) ) {

			/**
			 * Setup the ORM (Redbean is not really an ORM, at least not at the time of this writing...)
			 * and define our custom model naming convention.
			 */
			$db_settings['engine'] = (isset($db_settings['engine'])) ? $db_settings['engine'] : 'mysql';
			switch ($db_settings['engine']) {
				case 'cubrid':	$setup_string = sprintf('%s:host=%s;port=%d;dbname=%s', $db_settings['engine'], $db_settings['host'], $db_settings['port'], $db_settings['name']);
								break;

				case 'sqlite':	$setup_string = sprintf('%s:%s', $db_settings['engine'], $db_settings['name']);
								break;

				default:		$setup_string = sprintf('%s:host=%s;dbname=%s', $db_settings['engine'], $db_settings['host'], $db_settings['name']);
			}

			R::setup($setup_string, $db_settings['user'], $db_settings['pass']);
			RedBean_ModelHelper::setModelFormatter(new Phetiche_BASIC_model_formatter());

			/**
			 * This is to avoid issues on tables with underscores in names.
			 * See: http://redbeanphp.com/extra/upgrade_3_2_to_3_3
			 */
			R::setStrictTyping(false);

			/**
			 * Should the DB schema be frozen?
			 */
			R::freeze($db_settings['freeze']);
		}
   	}

}
