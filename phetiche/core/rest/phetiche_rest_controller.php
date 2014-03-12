<?php

/**
 * REST controller
 * 
 * @file			phetiche/core/rest/phetiche_rest_controller.php
 * @description		The REST controller.
 * @author			Stefan Aichholzer <yo@stefan.ec>
 * @package			Phetiche/core/rest
 * @license			BSD/GPLv2
 *
 * (c) copyright Stefan Aichholzer
 * This source file is subject to the BSD/GPLv2 License.
 */
require_once 'phetiche_rest_controller_interface.php';
abstract class Phetiche_REST_Controller implements Phetiche_REST_Controller_Interface  {

	/**
	 * The request object
	 * @var Phetiche_request
	 */
	protected $req;
	
	/**
	 * The response object
	 * @var Phetiche_response
	 */
	protected $res;

	/**
	 * GET request handler
	 * Must be defined in any inherinting class
	 * 
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 */
	abstract public function getRequest();

	/**
	 * POST request handler
	 * Must be defined in any inherinting class
	 * 
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 */
	abstract public function postRequest();

	/**
	 * PUT request handler
	 * Must be defined in any inherinting class
	 * 
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 */
	abstract public function putRequest();

	/**
	 * DELETE request handler
	 * Must be defined in any inherinting class
	 * 
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 */
	abstract public function deleteRequest();

	/**
	 * The class shut down method. Used to clear variables on shutdown.
	 * 
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 */
	public function tearDown()
	{
		$this->req = null;
		$this->res = null;	
	}

	/**
	 * Load the request and the response into the extending controller.
	 * This allows the request ($req) and the response ($res) to be directly
	 * used in the controller, no questions asked. ;)
	 * 
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @param	Phetiche_request $req (object) The request object.
	 * @param	Phetiche_response $res (object) The response object.
	 * @return	void
	 */
	public function loadRequestResponse(Phetiche_request $req, Phetiche_response $res)
	{
		$this->req = $req;
		$this->res = $res;
	}

}
