<?php

/**
 * Basic controller
 *
 * @file			phetiche/core/basic/phetiche_basic_controller.php
 * @description		The basic controller.
 * @author			Stefan Aichholzer <play@analogbird.com>
 * @package			Phetiche/core/basic
 * @license			BSD/GPLv2
 *
 * (c) copyright Stefan Aichholzer
 * This source file is subject to the BSD/GPLv2 License.
 */
require_once 'phetiche_basic_controller_interface.php';
abstract class Phetiche_BASIC_Controller implements Phetiche_BASIC_Controller_Interface {

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
	 * Index
	 * Must be defined in any inherinting class
	 *
	 * @author	Stefan Aichholzer <play@analogbird.com>
	 */
	abstract public function index();

	/**
	 * The class shut down method. Used to clear variables on shutdown.
	 *
	 * @author	Stefan Aichholzer <play@analogbird.com>
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
	 * @author	Stefan Aichholzer <play@analogbird.com>
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
