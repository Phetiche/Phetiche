<?php

class Phetiche_format {

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
	 * This function will only, safely, decode string encoded
	 * with Phetiche_format::safeEncode()
	 */
	public static function safeDecode($string)
	{
		$decoded = str_pad(strtr($string, '-_', '+/'), strlen($string) % 4, '=', STR_PAD_RIGHT);
		return base64_decode($decoded);
	}

}





/*
  
 
 <?php
 
// Specified date/time in your computer's time zone.
$date = new DateTime('9999-04-05');
echo $date->format('Y-M-j') ."";
 
// Specified date/time in the specified time zone.
$date = new DateTime('2040-09-08', new DateTimeZone('America/New_York'));
echo $date->format('n / j / Y') . "";
 
// INPUT UNIX TIMESTAMP as float or bigint from database
// Notice the result is in the UTC time zone.
$r = mysql_query("SELECT date FROM test_table");
$obj = mysql_fetch_object($r);
 
$date = new DateTime('@'.$obj->date); // a bigint(8) or FLOAT
echo $date->format('Y-m-d H:i: sP') ."";
 
// OR a constant greater than 2038:
$date = new DateTime('@2894354000'); // 2061-09-19 
echo $date->format('Y-m-d H:i: sP') ."";
?>
  
  
  */
