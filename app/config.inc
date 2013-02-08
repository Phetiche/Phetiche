<?php

	// General & default settings
	$config['default']['private_key'] = '321158f1e8b0f68197ac75b2b029158b06798efa9dd077ce5164de6de7765418';
	$config['default']['time_zone'] = 'Europe/Amsterdam';
	$config['default']['debug'] = true;
	$config['default']['log_errors'] = true;
	$config['default']['apc_cache'] = false;

	// Templating engine settings
	$config['templates']['path'] = 'app/views/';
	$config['templates']['compiled'] = 'app/views/compiled/';
	$config['templates']['cache'] = 'app/views/cache/';
	$config['templates']['debug'] = false;
	$config['templates']['cache'] = false;
	$config['templates']['cache_lifetime'] = 120;

	// Database settings
	// See http://redbeanphp.com/manual/freeze
	$config['db']['freeze'] = true;
	$config['db']['engine'] = false;
	$config['db']['user'] = 'username';
	$config['db']['pass'] = 'password';
	$config['db']['host'] = 'host';
	$config['db']['name'] = 'database';

	// URL routing (even more) settings
	$config['app']['routes'] = array('/empty/spaces.*' => '/basic/index');


	/**
	 * Custom (application specific settings)
	 * should (could) go below this point.
	 */

	$config['your_variable'] = 'Your value';
	$config['your_section']['your_variable'] = 'Your value';
