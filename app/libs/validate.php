<?php

/**
 * Simple validation class.
 * Provides a few simple (and sample methods) for validation.
 *
 * @author			Stefan Aichholzer <yo@stefan.ec>
 * @license			BSD/GPLv2
 *
 * (c) copyright Stefan Aichholzer
 * This source file is subject to the BSD/GPLv2 License.
 */
class validate {

	/**
	 * Simplest way possible to validate an email address.
	 * Could be improved by actually making a DNS lookup for the given domain.
	 *
	 * @author Stefan Aichholzer <yo@stefan.ec>
	 * @param string $email The email address to be validated.
	 * @return Boolean
	 */
	public static function email($email)
	{
		return (filter_var($email, FILTER_VALIDATE_EMAIL)) ? true : false;
	}

	/**
	 * Simplest way possible to validate an IP address.
	 * Could be improved by actually making a DNS lookup for the given IP address.
	 *
	 * @author Stefan Aichholzer <yo@stefan.ec>
	 * @param string $address The I address to be validated.
	 * @return Boolean
	 */
	public static function ip($address)
	{
		return (filter_var($address, FILTER_VALIDATE_IP)) ? true : false;
	}

}
