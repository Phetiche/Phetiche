<?php

class basket_module extends Phetiche_BASIC_Module {
	
	public function makePoint()
	{
		echo '<br />';
		Phetiche_format::tree($this->req);
		echo 'I scored ' . $this->points;
	}

}