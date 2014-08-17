<?php

/**
 * The Phetiche configuration handler
 *
 * @file			phetiche/core/phetiche_config.php
 * @description		The configuration object.
 * @author			Stefan Aichholzer <play@analogbird.com>
 * @package			Phetiche/core
 * @license			BSD/GPLv2
 *
 * (c) copyright Stefan Aichholzer
 * This source file is subject to the BSD/GPLv2 License.
 */
final class Phetiche_config {

	/**
	 * The actual configuration for the current application.
	 * @var array
	 */
	private static $configvars = [];


	/**
	 * Configuration loader
	 * Will load the configuration array into the object
	 * so it can be used throughout the entire application.
	 *
	 * @author	Stefan Aichholzer <play@analogbird.com>
	 * @param	array $config The configuration being loaded.
	 * @see		set();
	 * @see		get();
	 * @return	void
	 */
	public static function load($config)
	{
		array_walk($config, function($item, $key) { Phetiche_config::set($key, $item); });
	}


	/**
	 * Set & get a configuration variable
	 * Converts a "path" (/element/element/element) into
	 * a nested array and assigns the value to the last (nested item)
	 * in the array.
	 *
	 * This function also returns any item from the nested structure.
	 *
	 * @author	Stefan Aichholzer <play@analogbird.com>
	 * @param	string $path The path describing the nested structure.
	 * @param	mixed $value The value to be set in the array. If (boolean) false then the value is fetched.
	 * @return	mixed Array of found records. Boolean (false) if not found.
	 */
	private static function set_getArray($path, $value = false)
	{
		$array = &self::$configvars;
		$path = explode('/', $path);
    	$key = array_pop($path);

		foreach ($path as $item) {

			if (isset($array[$item]) && !is_array($array[$item]) && $value) {
				unset($array[$item]);
			}

			if (!isset($array[$item])) {
				$array[$item] = [];
			}

			$array = &$array[$item];
    	}

		if ($value) {
			$array[$key ? $key : count($array)] = $value;
		} else {
			if (!isset($array[$key])) {
				return false;
			} else {
				return $array[$key ? $key : count($array)];
			}
		}
	}


	/**
	 * Get a configuration variable
	 * Returns any item from the current configuration.
	 *
	 * @author	Stefan Aichholzer <play@analogbird.com>
	 * @see		set_getArray();
	 * @param	string $name The name of the element to be fetched.
	 * @return	mixed(bool|string) The element found. (Boolean) false if not found.
	 */
	public static function get($name)
	{
		if (strpos($name, '/') !== false) {
			return self::set_getArray($name);
		} else {
			return (isset(self::$configvars[$name])) ? self::$configvars[$name] : false;
		}
	}


	/**
	 * Set a configuration variable
	 * Sets an element in the current configuration.
	 *
	 * @author	Stefan Aichholzer <play@analogbird.com>
	 * @see		set_getArray();
	 * @param	string $name The name of the element to be set.
	 * @param	mixed $value The value to be set.
	 * @return	void
	 */
	public static function set($name, $value)
	{
		if (strpos($name, '/') !== false) {
			self::set_getArray($name, $value);
		} else {
			self::$configvars[$name] = $value;
		}
	}


	/**
	 * Merge configuration
	 * Merge an array into the existing configuration
	 *
	 * @author	Stefan Aichholzer <play@analogbird.com>
	 * @param	array $extra_config The array to be merged.
	 * @param	mixed $value The value to be set.
	 * @return	boolean false on error.
	 */
	public static function merge($extra_config = false)
	{
		if (!$extra_config) {
			return false;
		}

		self::$configvars = array_merge(self::$configvars, $extra_config);
	}

}
