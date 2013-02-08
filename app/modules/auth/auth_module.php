<?php

class auth_module {

	/**
	 * The error message
	 * @var string
	 */
	private $error = '';

	/**
	 * Create the users
	 * Creates the users table if it does not exist yet
	 *
	 * @author Stefan Aichholzer <yo@stefan.ec>
	 * @return boolean True on success. False on error.
	 */
	public function init()
	{
		
	}

	/**
	 * Do a basic user login
	 * If the user table does not exist yet it will be
	 * created based on the users.sql included with this
	 * module.
	 *
	 * @author Stefan Aichholzer <yo@stefan.ec>
	 * @param string $username The username.
	 * @param string $password The password.
	 * @return int User ID on success. 0 on error. 
	 */
	public function login($username = '', $password = '')
	{
		echo 'I scored...';
	}

	/**
	 * Do a basic user logout
	 *
	 * @author Stefan Aichholzer <yo@stefan.ec>
	 * @param mixed $user The username or user ID to be logged out
	 * @return boolean True on success. False on error.
	 */
	public function logout($user = null)
	{
		
	}

}