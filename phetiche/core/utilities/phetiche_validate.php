<?php

/**
 * @todo Make a method to implement this:
 * $arg = filter_var($arg, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
 * Maybe Phetiche_filter::()
 */
class Phetiche_validate {

	public static function email($email)
	{
		$result = (filter_var($email, FILTER_VALIDATE_EMAIL)) ? true : false;
		return $result;
	}


	public static function url($url)
	{
		return $url;
	}


	public static function phone($phone)
	{
		return $phone;
	}


	public static function ip($address)
	{
		$result = (filter_var($address, FILTER_VALIDATE_IP)) ? true : false;
		return $result;
	}

	/**
	* Validates a given variable
	*
	* It can take 2 arguments, one to validate and the second one to be returned
	* if the statement yields a true value. The variable is validated
	* for being set and for not being empty.
	*
	* Last modified: 17/02/2011 08:59
	*
	* @param	integer/string/array The variable to be validated
	* @access 	public
	* @author 	Stefan Aichholzer <yo@stefan.ec>
	* @version	0.1
	* @since	17/02/2011
	*/
	public static function setnfull($var)
	{
		if (is_array($var)) {
			$test_array = $var[0];
			$test_element = $var[1];
			return (isset($test_array[$test_element]) && !empty($test_array[$test_element])) ? $test_array[$test_element] : false;
		} else {
			return (isset($var) && !empty($var)) ? $var : false;
		}
	}

}
