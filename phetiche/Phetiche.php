<?php

/**
 * Phetiche core
 *
 * @file			Phetiche.php
 * @description		Phetiche core component and handler.
 *					This class/file is the only file included in the index.php page, it
 * 					takes care of instantiating everything which is needed.
 * @author			Stefan Aichholzer <play@analogbird.com>
 * @package			Phetiche
 * @license			BSD/GPLv2
 *
 * (c) copyright Stefan Aichholzer
 * This source file is subject to the BSD/GPLv2 License.
 */
final class Phetiche {

	/**
	 * The minimum PHP version required to run Phetiche
	 * @var string
	 */
	private $min_php_version = '5.4';

	/**
	 * The path to the public folder.
	 * @var string
	 */
	private $public_path = '';

	/**
	 * The path to the current folder.
	 * @var string
	 */
	private $current_path = '';

	/**
	 * The directories found for path recursion.
	 * @var array
	 */
	private $dirs_found = [];

	/**
	 * On which folders to recurse.
	 * @var array
	 */
	private $locations = ['app', 'phetiche'];

	/**
	 * Which folder, whithin top folders, to skip from search/recursion.
	 * @var array
	 */
	private $skip_folders = [];

	/**
	 * How many levels deep shall the recursion go. And the current depth.
	 * @var integer
	 * @var integer
	 */
	private $folders_deep = 3;
	private $current_deep_level = 1;

	/**
	 * The arguments passed to the command line call.
	 * @var array
	 */
	private $command_args = [];

	/**
	 * Class/object constructor.
	 *
	 * @author	Stefan Aichholzer <play@analogbird.com>
	 * @param	array $argv The arguments passed to the app (in command line call)
	 * @see		makeIncludePaths();
	 * @see		handleCommandCall();
	 * @see		Phetiche_config::set();
	 * @see		Phetiche_config::load();
	 * @see		Phetiche_config::get();
	 * @return	void
	 */
	public function __construct($argv = null)
	{
		/**
		 * Provide a simple option for user to test if the installation is
		 * OK and if basic features are available and can be used.
		 */
		$app_folder = dirname(dirname(__FILE__)) . '/app/';
		if (is_readable($app_folder.'allow.check') && strpos($_SERVER['REQUEST_URI'], '/pheticheck') !== false) {
			$this->pheticheck($app_folder);
		}
		$app_folder = null;

		/**
		 * You can read PHP forums or simply search the web for this problem.
		 * The PHP team is (was) well aware of it and fixed it in version above 5.3.13
		 * (maybe before, but I can't test).
		 * The common problem reported is: Fatal error: Can't inherit abstract function ... (previously declared abstract in ...)
		 */
		if (version_compare(PHP_VERSION, $this->min_php_version) < 0) {
			print "I am sorry. You need at least PHP version: 5.3 to run Phetiche.\nYour current version is: " . PHP_VERSION;
			die();
		}

		global $public_path;
		$this->public_path = $public_path;
		$public_path = $base_location = null;

		/**
		 * Garbage-collect, if possible and if not set to auto.
		 */
		if (function_exists('gc_enable')) {
			gc_enable();
		}

		/**
		 * Did enough arguments come from the command line?
		 */
		if ($argv && count($argv) >= 1) {
			$base_location = dirname($this->public_path) . '/';
			$this->command_args = $argv;
		}

		$this->current_path = ($base_location) ? $base_location : dirname($this->public_path) . '/';

		/**
		 * Try to load the configuration file.
		 * This file must be present.
		 */
		$config = [];
		if (file_exists($this->current_path . 'app/config.inc')) {
			require_once($this->current_path . 'app/config.inc');
		} else {
			die('No configuration file could be found. I cannot continue.');
		}

		/**
		 * Make the include paths and populate the modules
		 * array so the app can make use of it.
		 */
		$app_modules = $this->makeIncludePaths();
		Phetiche_config::set('loadable_modules', $app_modules);

		/**
		 * Load the actual app (whole) configuration.
		 */
		Phetiche_config::load($config);

		/**
		 * Define the time-zone according to the configuration.
		 * Define the default error handler to be used.
		 */
		date_default_timezone_set(Phetiche_config::get('default/time_zone'));
		set_error_handler('Phetiche_error::handler');

		/**
		 * If the application is called from the command line
		 * proceed accordingly. Not here but in the request (URL) instance.
		 */
		Phetiche_config::set('command_arguments', $this->command_args);
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

	/**
	 * Check (basic) the installation.
	 * Performs a basic check on the installation to see if
	 * all required and optional extensions are loaded.
	 * It also check for the basic required files.
	 *
	 * @author Stefan Aichholzer <play@analogbird.com>
	 * @param String $app_folder The path to the /app folder.
	 * @see get_loaded_extensions();
	 * @see version_compare();
	 * @return void
	 */
	private function pheticheck($app_folder)
	{
		$required = ['CORE', 'DATE', 'ZLIB', 'GD', 'HASH', 'OPENSSL', 'FILEINFO', 'JSON', 'PDO', 'APC', 'REFLECTION', 'SQLITE', 'SQLITE3', 'MCRYPT'];
		$recommended = ['GD', 'APC', 'PDO', 'SQLITE'];
		$extensions = get_loaded_extensions();
		array_walk($extensions, function(&$item) { $item = strtoupper($item); });

		header('Content-Type: text/html; charset=utf-8');
		echo '
		<!--
			This is a basic configuration check generated by Phetiche.
			These results might not be as accurate as you expect (and as we hope) but they should
			give you a clear idea of what could fail when you run your application.

			If you are reading this, then you might be the kind of person that could help
			us make Phetiche bigger and better. Have a look: http://phetiche.org/helpus

			Generated on: ' . date('d/m/Y H:i:s') . '
		-->

			<!DOCTYPE HTML>
			<html>
			<head>
				<title>Pheticheck</title>
				<style>
					span { font-size:19px; float:left; line-height:17px; margin-right:8px; }
					#wrapper { width:420px; margin:0 auto; border:1px dashed #666;border-radius:10px; padding:10px; }
					#wrapper div { padding:4px 10px; border-radius:5px; margin:4px 0; font-family:Verdana; font-size:14px; line-height:20px; }
					.ok { background-color:#5bcb68; }
					.err { background-color:#ee8686; }
					.rec { background-color:#ec8dc3; }
				</style>
			</head>
			<body>
			<div id="wrapper">';

			// Configuration file
			$class = 'ok';
			$status = '<span>✔</span>';
			if (!is_readable($app_folder.'config.inc')) {
				$class = 'err';
				$status = '<span>✘</span>';
			}
			echo '<div class="'.$class.'">Configuration settings <i>(app/config.inc)</i>'.$status.'</div>';

			// Error log file
			$class = 'ok';
			$status = '<span>✔</span>';
			if (!file_exists($app_folder.'error.log') || !is_writable($app_folder.'error.log')) {
				$class = 'rec';
				$status = ' (Recommended)';
			}
			echo '<div class="'.$class.'">Log file <i>(app/error.log)</i>'.$status.'</div>';

			// Temp is writeable
			$class = 'ok';
			$status = '<span>✔</span>';
			if (!is_writable('/tmp')) {
				$class = 'err';
				$status = ' <span>✘</span>';
			}
			echo '<div class="'.$class.'">Writeable temporary folder <i>(/tmp)</i>'.$status.'</div>';

			// PHP version
			$class = 'ok';
			$status = '<span>✔</span>';
			if (version_compare(PHP_VERSION, $this->min_php_version) <= 0) {
				$class = 'err';
				$status = ' <span>✘</span> (PHP '.$this->min_php_version.'+ is required)';
			}
			echo '<div class="'.$class.'">PHP version'.$status.'</div>';

			// PHP autoloader
			$class = 'ok';
			$status = '<span>✔</span>';
			if (!function_exists('spl_autoload')) {
				$class = 'err';
				$status = ' <span>✘</span> (PHP autoloading is required)';
			}
			echo '<div class="'.$class.'">PHP autoloading'.$status.'</div>';

			// PHP garbage collection
			$class = 'ok';
			$status = '<span>✔</span>';
			if (!function_exists('gc_enable')) {
				$class = 'rec';
				$status = ' (Recommended)';
			}
			echo '<div class="'.$class.'">PHP garbage collection'.$status.'</div>';

			// PHP magic quotes
			$class = 'ok';
			$status = '<span>✔</span>';
			if (get_magic_quotes_runtime() || get_magic_quotes_gpc()) {
				$class = 'rec';
				$status = ' (Recommended)';
			}
			echo '<div class="'.$class.'">PHP magic quotes should be off'.$status.'</div>';

			// Extensions
			foreach ($required as $req) {
				$class = 'ok';
				$status = '<span>✔</span>';
				if (!in_array($req, $extensions)) {
					$class = 'err';
					$status = '<span>✘</span> (This extension is required)';

					if (in_array($req, $recommended)) {
						$class = 'rec';
						$status = ' (Recommended)';
					}
				}

				print_r('<div class="'.$class.'">PHP <i>' . ucfirst($req) .'</i> extension'. $status . '</div>');
			}

		echo '</div></body></html>';

		exit(1);
	}

	/**
	 * Make paths from which to use (include) files.
	 *
	 * $app_modules is currently not used in the app but
	 * it is collected anyway. Maybe we can use it in the future.
	 *
	 * @author	Stefan Aichholzer <play@analogbird.com>
	 * @see		recursePath();
	 * @see		autoLoader();
	 * @return	array $app_modules The modules found for this app
	 */
	private function makeIncludePaths()
	{
		$app_modules = $final_include_paths = [];

		foreach ($this->locations as $location) {
			$this->recursePath($this->current_path . $location . '/', false);

			foreach ($this->dirs_found as $inc_folder) {
				if (!in_array($inc_folder, $final_include_paths)) {
					$final_include_paths[] = $inc_folder;

					/**
					 * Get all custom modules found in the folder structure.
					 * These will not be used here, they are used in the app itself.
					 */
					if (strpos($inc_folder, 'app/modules/') !== false) {
						$app_modules[] = basename($inc_folder);
					}
				}
			}
		}

		$include_path = implode(PATH_SEPARATOR, $final_include_paths);
		set_include_path(get_include_path() . PATH_SEPARATOR . $include_path);

		spl_autoload_extensions('.php');
		spl_autoload_register('Phetiche::autoLoader', true);

		$final_include_paths = $include_path = $this->folders_deep = $this->current_deep_level = null;
		return $app_modules;
	}

	/**
	 * Recurse each path (folder) to make use of files within.
	 *
	 * @author	Stefan Aichholzer <play@analogbird.com>
	 * @param	string $path The path to be recursed
	 * @param	boolean $sub_folder If subfolders are to be recursed as well
	 * @see		recursePath();
	 * @return	void
	 */
	private function recursePath($path, $sub_folder = false)
	{
		$path = rtrim($path, "/");

		$dirs = array_diff(scandir($path), [".", "..", ".DS_Store"]);
		if (!$sub_folder) {
			$this->dirs_found[] = $path;
		}

		foreach ($dirs as $dir) {
			if (is_dir($path . '/' . $dir) && $this->current_deep_level <= $this->folders_deep) {
				++$this->current_deep_level;
				$this->dirs_found[] = $path . '/' . $dir;
				$this->recursePath($path . '/' . $dir, true);
            }
        }

        if ($this->current_deep_level > 1) {
	        --$this->current_deep_level;
        }
	}

	/**
	 * Autoload the requested resource.
	 *
	 * @author Stefan Aichholzer <play@analogbird.com>
	 * @param String $class_name The name of the class (file) being requested
	 * @see	Phetiche_error();
	 * @throw Phetiche_error() This will result in a 404 error rendered to the response.
	 * @return	void
	 */
	private static function autoLoader($class_name)
	{
		$load_class_file = ($class_name == 'Smarty') ? $class_name . '.class' : $class_name;

		spl_autoload($load_class_file);
		if (!class_exists($class_name)) {
			throw new Phetiche_error(404);
		}
	}
}

/**
 * Let's start the magic.
 * @param array $argv If the application is being loaded from command line.
 */
$phetiche = new Phetiche(((isset($argv)) ? $argv : null));
