<?php

/**
 * Basic controller
 * 
 * @file			phetiche/core/basic/phetiche_basic_model_formatter.php
 * @description		The basic modela name formatter.
 * @author			Stefan Aichholzer <yo@stefan.ec>
 * @package			Phetiche/core/basic
 * @license			BSD/GPLv2
 *
 * (c) copyright Stefan Aichholzer
 * This source file is subject to the BSD/GPLv2 License.
 */
class Phetiche_BASIC_model_formatter implements RedBean_IModelFormatter {

	/**
	 * Format the model name
	 * This file can be changed to suite any custom needs (rather wishes);
	 * just change '_model' to whatever you want. This will define your model
	 * naming convention.
	 * 
	 * @author	Stefan Aichholzer <yo@stefan.ec>
	 * @param string $model The name of the model to be loaded.
	 */
	public function formatModel($model) {
		return $model . '_model';
	}

}