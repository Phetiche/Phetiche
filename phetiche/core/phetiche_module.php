<?php

/**
 * The Phetiche module handler
 *
 * @file			phetiche/core/phetiche_module.php
 * @description		The module object. This enables (and handles) custom modules.
 * @author			Stefan Aichholzer <play@analogbird.com>
 * @package			Phetiche/core
 * @license			BSD/GPLv2
 *
 * (c) copyright Stefan Aichholzer
 * This source file is subject to the BSD/GPLv2 License.
 */
class Phetiche_module {

	public static function load($module_name = null, $req = null, $res = null)
	{
		$module_name = $module_name . '_module';

		if (!$module = Phetiche_apc::fetch($module_name)) {

			$module_mirror = new ReflectionClass($module_name);
			$module = $module_mirror->newInstanceArgs();

			if (method_exists($module, 'loadRequestResponse')) {
				$req = (!$req) ? new Phetiche_request() : $req;
				$res = (!$res) ? new Phetiche_response() : $res;
				$module->loadRequestResponse($req, $res);
			}

			Phetiche_apc::store($module_name, $module);
		}

		return $module;
	}

}
