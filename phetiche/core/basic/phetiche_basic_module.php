<?php

/**
 * Basic module
 *
 * @file			phetiche/core/basic/phetiche_basic_module.php
 * @description		The basic module. Some module use stuff
 * @author			Stefan Aichholzer <play@analogbird.com>
 * @package			Phetiche/core/basic
 * @license			BSD/GPLv2
 *
 * (c) copyright Stefan Aichholzer
 * This source file is subject to the BSD/GPLv2 License.
 */
require_once 'phetiche_basic_module_interface.php';
abstract class Phetiche_BASIC_Module {

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
