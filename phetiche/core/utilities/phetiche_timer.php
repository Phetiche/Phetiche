<?php

class Phetiche_timer {

	public function __construct()
	{
        $this->start = microtime(true);
	}

    public function stop($echo_elapsed = false)
    {
        $elapsed = (microtime(true) - $this->start);

		if ($echo_elapsed) {
			echo $elapsed;
		} else {
        	return $elapsed;
		}
    }

}