<?php

/**
 * BASIC module interface
 * 
 * @file			phetiche/core/basic/phetiche_basic_module_interface.php
 * @description		The interface (definition) for the basic module.
 * @author			Stefan Aichholzer <yo@stefan.ec>
 * @package			Phetiche/core/basic
 * @license			BSD/GPLv2
 *
 * (c) copyright Stefan Aichholzer
 * This source file is subject to the BSD/GPLv2 License.
 */
interface Phetiche_BASIC_Module_Interface {

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
	 * @author Stefan Aichholzer <yo@stefan.ec>
	 * @param Phetiche_request $req The request
	 * @param Phetiche_response $res The response
	 */
	public function loadRequestResponse(Phetiche_request $req, Phetiche_response $res);

}