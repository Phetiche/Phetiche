<?php

class demo extends Phetiche_BASIC_Controller {

	public function cli()
	{
		// php -f public/index.php demo cli game:play age:30 samples:10
		// php -f public/index.php demo cli -u "name=Phetiche&info[author]=Stefan+Aichholzer&info[gender]=male&info[age]=32&city=Amsterdam"

		print_r($this->req);
	}

	public function index()
	{

	}

}
