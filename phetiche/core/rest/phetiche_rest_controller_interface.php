<?php

/**
 * REST controller interface
 * 
 * @file			phetiche/core/rest/phetiche_rest_controller_interface.php
 * @description		The interface (definition) for the REST controller.
 * @author			Stefan Aichholzer <yo@stefan.ec>
 * @package			Phetiche/core/rest
 * @license			BSD/GPLv2
 *
 * (c) copyright Stefan Aichholzer
 * This source file is subject to the BSD/GPLv2 License.
 */
interface Phetiche_REST_Controller_Interface {

	/**
	 * Remove variables and references
	 *
	 * @author Stefan Aichholzer <yo@stefan.ec>
	 * @return void
	 */
	public function tearDown();

	/**
	 * Load the request arguments and the response into the current object.
	 *
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @param	Phetiche_request $req The request
	 * @param	Phetiche_response $res The response
	 */
	public function loadRequestResponse(Phetiche_request $req, Phetiche_response $res);

}