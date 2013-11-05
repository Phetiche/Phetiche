<?php

class cypher {

	/**
	 * If any of these values is changed, all must be changes, as
	 * some types of cypher are not compatible with some cypher modes and
	 * viceversa.
	 * For details see: http://nl3.php.net/manual/en/function.mcrypt-create-iv.php
	 */
	private static $crypt_cypher = MCRYPT_RIJNDAEL_256;
	private static $crypt_mode = MCRYPT_MODE_CBC;
	private static $crypt_random = MCRYPT_RAND;

	private static function encryptText($text = '')
	{
		if (!$text) {
			// No text? - Let's create a hash to put into the cypher
			$text = hash('sha256', microtime(true));
		}

		// Generate a 32 byte salt.
		$salt = mcrypt_create_iv(mcrypt_get_iv_size(self::$crypt_cypher, self::$crypt_mode), self::$crypt_random);

		// Generate a 32 byte token.
		$token = hash('sha256', microtime(true) * mt_rand(), true);
		$token = bin2hex($token);

		/**
		 * Generate a 32 byte key to encrypt the text.
		 * In order to achieve 256-bit encryption the key must be 32 bytes long,
		 * if the key is 16 bytes long the encryption will be 128-bit.
		 */
		$key = Phetiche_config::get('default/private_key') . $token;
		$key = hash('tiger192,3', $key, true);

		// Add proper padding to be PKCS7 complaint.
		$block_size = mcrypt_get_block_size(self::$crypt_cypher, self::$crypt_mode);
		if (($pad = $block_size - (strlen($text) % $block_size)) < $block_size) {
      		$text .= str_repeat(chr($pad), $pad);
    	}

		/**
		 * Encrypt the text with the given algorithm.
		 * This encryption is not AES compliant since the block size used here is
		 * 256 (MCRYPT_RIJNDAEL_256) where AES requires a 128 block size.
		 * Encode the encrypted text and return it as the API key.
		 */
		$crypt = mcrypt_encrypt(self::$crypt_cypher, $key, $text, self::$crypt_mode, $salt);

		$data = [];
		$data['key'] = format::safeEncode($crypt);
		$data['token'] = $token;
		$data['salt'] = format::safeEncode($salt);

		return $data;
	}


	public static function randomCode($text = null, $hash_algo = 'crc32')
	{
		$text = ($text) ? $text : microtime(true);
		$hash = strtoupper(hash($hash_algo, $text, false));

		/**
		 * Return a PHP 5.3 compatible hash
		 */
		if (version_compare(PHP_VERSION, '5.4.0', '>=') && $hash_algo == 'tiger192,3') {
			return implode('', array_map('bin2hex', array_map('strrev', array_map('hex2bin', str_split($hash, 16)))));
		} else {
			return $hash;
		}

	}


	public static function cypherPassword($password = '')
	{
		if (!$password) {
			return false;
		}

		$data = self::encryptText($password);
		return $data['key'];
	}


	public static function authData()
	{
		return self::encryptText();
	}

}
