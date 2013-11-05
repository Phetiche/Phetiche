<?php

/**
 * Simple timing class.
 * Provides a simple way to measure loading times.
 *
 * @author			Stefan Aichholzer <yo@stefan.ec>
 * @license			BSD/GPLv2
 *
 * (c) copyright Stefan Aichholzer
 * This source file is subject to the BSD/GPLv2 License.
 */
class timer {

	/**
	 * Create an instance of the class
	 * and set the start time.
	 *
	 * @author Stefan Aichholzer <yo@stefan.ec>
	 * @return void
	 */
	public function __construct()
	{
        $this->start = microtime(true);
	}

	/**
	 * Stops the timer.
	 *
	 * @author Stefan Aichholzer <yo@stefan.ec>
	 * @param boolean $print_response Whether the ellapsed time should be printed or not.
	 * @return double
	 */
    public function stop($print_response = false)
    {
        $elapsed = (microtime(true) - $this->start);

		if ($print_response) {
			echo $elapsed;
		} else {
        	return $elapsed;
		}
    }

}