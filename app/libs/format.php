<?php

class format {

	public static function tree($data, $title = '')
	{
		print('<pre>' . $title . "\n" . print_r($data, true) . '</pre>');
	}
	
	
	public static function date($date, $format = DATE_ISO8601)
	{
		return ($date) ? date($format, strtotime($date)) : NULL;
	}
	
	
	/**
	 * Provide a URL-safe base64 encoded string.
	 */
	public static function safeEncode($string)
	{
		return rtrim(strtr(base64_encode($string), '+/', '-_'), '=');
	}


	/**
	 * Decode a URL-safe base64 encoded string.
	 * This function will only, safely, decode strings encoded
	 * with format::safeEncode()
	 */
	public static function safeDecode($string)
	{
		$decoded = str_pad(strtr($string, '-_', '+/'), strlen($string) % 4, '=', STR_PAD_RIGHT);
		return base64_decode($decoded);
	}

}

