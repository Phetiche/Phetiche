<?php

	//xhprof_enable();

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

/*
	$xhprof_data = xhprof_disable();

	$XHPROF_ROOT = realpath(dirname(__FILE__) . '/..');
	include_once $XHPROF_ROOT . '/xhprof/xhprof_lib/utils/xhprof_lib.php';
	include_once $XHPROF_ROOT . '/xhprof/xhprof_lib/utils/xhprof_runs.php';

	$xhprof_runs = new XHProfRuns_Default();
	$run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_phetiche");

	echo "<p>&nbsp;</p><a target='_blank' href='http://profiler.local/index.php?run=$run_id&source=xhprof_phetiche'>http://profile/index.php?run=$run_id&source=xhprof_phetiche</a>";
*/