<?php

/**
 * Rest example class.
 */
class rest extends Phetiche_REST_Controller {

	/**
	 * GET responder.
	 *
	 * This method will be called on any GET request.
	 * Every class extending from Phetiche_REST_Controller
	 * must implement this method.
	 */
	public function getRequest()
	{
		echo 'I am a: '. Phetiche_request::load()->a . '<br \>';
		echo 'I am b: '. $this->req->b . '<br \>';
		echo 'I am c: '. $this->req->c . '<br \>';
		echo 'I am a, again: '. $this->req->a . '<br \>';
	}


	/**
	 * POST responder.
	 *
	 * This method will be called on any POST request.
	 * Every class extending from Phetiche_REST_Controller
	 * must implement this method.
	 */
	public function postRequest()
	{

	}


	/**
	 * PUT responder.
	 *
	 * This method will be called on any PUT request.
	 * Every class extending from Phetiche_REST_Controller
	 * must implement this method.
	 */
	public function putRequest()
	{

	}


	/**
	 * DELETE responder.
	 *
	 * This method will be called on any DELETE request.
	 * Every class extending from Phetiche_REST_Controller
	 * must implement this method.
	 */
	public function deleteRequest()
	{

	}

}
