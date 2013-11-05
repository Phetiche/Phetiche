<?php

/**
 * The Phetiche error handler
 *
 * @file			phetiche/core/phetiche_error.php
 * @description		The error object. This will handle all errors and failures, hopefully.
 * 					This class extendes the native Exception object in order for it to be able
 * 					to throw valid exceptions -throw new Phetiche_error();
 * @author			Stefan Aichholzer <yo@stefan.ec>
 * @package			Phetiche/core
 * @license			BSD/GPLv2
 *
 * @todo			Clean code
 * @todo			Document properly
 *
 * (c) copyright Stefan Aichholzer
 * This source file is subject to the BSD/GPLv2 License.
 */
class Phetiche_error extends Exception {

	/**
	 * Error code and strings definition
	 * @var array
	 */
	private static $phetiche_errors = ['4041' => 'You are trying to load an undefined module.'];

	/**
	 * Generate a nice debug trace from the error stack.
	 * This very function will not be named in this stack trace, of course. ;)
	 *
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @return	array $simple_trace An array with the error trace
	 */
	private static function getDebugTrace()
	{
		$trace = debug_backtrace();
		$simple_trace = [];
		foreach ($trace as $node) {
			$file = (Phetiche_server::basePath() !== '/') ? str_replace(Phetiche_server::basePath(), '', $node['file']) : $node['file'];
			if ($node['function'] != 'getDebugTrace') {
				$simple_trace[] = ['file' => $file, 'line' => $node['line'], 'function' => $node['function']];
			}
		}

		return $simple_trace;
	}

	public static function handler($code, $msg, $file = '', $line = '')
	{
		$throw_errors = [E_NOTICE, E_ERROR, E_WARNING, E_PARSE];

		$error_msg = $msg;
		if ($file) { $error_msg .= ' in "' . $file . '"'; }
		if ($line) { $error_msg .= ' at line "' . $line . '"'; }

		if (in_array($code, $throw_errors)) {
			throw new Phetiche_error($error_msg);
		} else {
			echo $error_msg;
			if (Phetiche_config::get('default/debug')) {
				format::tree(self::getDebugTrace(), "Debug trace:\n");
			}
		}
	}

	/**
	 * Sends the error message or code to the client.
	 * It makes use of the pre-defined error pages which can be over ruled if custom
	 * pages are found (in the right location).
	 *
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @param	integer $err The error code being returned
	 * @return	void
	 *
	 * @todo	Implement the use of custom error pages
	 */
	public static function send($err)
	{
		$err = ($err) ? $err : 500;
		if (!headers_sent()) {
			header('HTTP/1.0 ' . $err);
		}

		$base_path = Phetiche_server::basePath(true);
		if (file_exists($base_path . 'phetiche/core/error_pages/'.$err.'.htm')) {
			$page = file_get_contents($base_path . 'phetiche/core/error_pages/'.$err.'.htm');
			$page = str_replace('<!--home-->', ucfirst(Phetiche_server::SERVER_NAME()), $page);
			$page = str_replace('<!--url-->', Phetiche_server::REQUEST_URI(), $page);

			$traces = self::getDebugTrace();
			if (Phetiche_config::get('default/debug')) {
				$debug_msg = '<h4>Debug trace:</h4><h5>(This should be disabled in production environments)</h5><pre>';

				foreach ($traces as $trace) {
					$debug_msg .= 'File: ' . $trace['file'] . "\nLine: " .  $trace['line'] . ' | Function: ' . $trace['function'] . "\n\n";
				}

				$debug_msg .= '</pre>';
				$page = str_replace('<!--debug-->', $debug_msg, $page);
			}

 			$last_trace = end($traces);
			self::log($err, $last_trace['file'], $last_trace['line']);
			print $page;
		} else {
			if (isset(self::$phetiche_errors[$err])) {
				print self::$phetiche_errors[$err];
			} else {
				print $err;
			}
		}
	}

	/**
	 * Logs an error to the error.log file
	 *
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @param	integer $err The error code being logged
	 * @param	string $file The file which reported the error
	 * @param	string $line The line at which the error took place
	 * @param	boolean $truncate The log file should be truncated at each write
	 * @return	void
	 */
	public static function log($err = '', $file = '', $line = '', $truncate = false)
	{
		try {
			if (Phetiche_config::get('default/log_errors')) {
				$filename = dirname(Phetiche_server::DOCUMENT_ROOT()) . '/app/error.log';
				if ($fh = ((!$truncate) ? fopen($filename, 'a+') : fopen($filename, 'w+'))) {
					$stringData = sprintf("%s | File: %s | Line: %s | Error: %s", date('d/m/Y H:i:s'), $file, $line, $err);

					if ($request_method = Phetiche_server::REQUEST_METHOD()) { $stringData .= ' | Request method: '	. $request_method; }
					if ($request_uri = Phetiche_server::REQUEST_URI()) { $stringData .= ' | Request uri: '	. $request_uri; }

					$stringData .= "\n";

					fwrite($fh, $stringData);
					fclose($fh);
				}
			}

		} catch (Exception $e) {
			print $e->getMessage();
			exit(1);
		}
	}

}
