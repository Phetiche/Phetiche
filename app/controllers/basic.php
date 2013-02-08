<?php

/**
 * Basic example class.
 */
class basic extends Phetiche_BASIC_Controller {

	/**
	 * Default class responder.
	 *
	 * This method will be called if the controller is called
	 * with no method name.
	 * Every class extending from Phetiche_BASIC_Controller
	 * must implement this method.
	 */
	public function index()
	{
		Phetiche_format::tree($this->req);
	}


	/**
	 * Before call.
	 *
	 * This method will be called before any other method
	 * in this class is called.
	 * Implementing this method is optional but it can be useful,
	 * for example, when having logic which is used in all other methods.
	 */
	public function before()
	{
		echo 'I will be called before every other method in this class...';
	}


	/**
	 * After call.
	 *
	 * This method will be called after any other method
	 * in this class is called.
	 * Implementing this method is optional but it can be useful,
	 * for example, when having to unset variables defined elsewhere in this class.
	 */
	public function after()
	{
		echo '...and I will be called after every other method in this class.';
	}


	/**
	 * http://phetiche/basic/args/a:1/?b=2&c=3
	 * How to use URI arguments
	 */
	public function args()
	{
		echo 'I am a: '. Phetiche_request::load()->a . '<br \>';
		echo 'I am b: '. $this->req->b . '<br \>';
		echo 'I am c: '. $this->req->c . '<br \>';
		echo 'I am a, again: '. $this->req->a . '<br \>';
	}

	public function css()
	{
		$this->res->render('demo');
	}

}
