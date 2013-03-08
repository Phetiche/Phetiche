<?php

/**
 * Default class and method
 * If the app is called with no controller name this
 * controller will handle the response. The index() method
 * will be the method to handle the request.
 * Since this class extends from Phetiche_BASIC_Controller it
 * must implement the index() method.
 *
 * Removing this class is not recommended, since it should be used
 * for handling undocumented calls or any other default response.
 */
class Phetiche_index extends Phetiche_BASIC_Controller {

	/**
	 * Default responder.
	 *
	 * Will be used to respond to any requests which do not specify
	 * a valid controller.
	 *
	 * http://phetiche/?a=1&b=2&c=3
	 */
	public function index()
	{
		Phetiche_format::tree($this->req->test);
		$this->res->render('demo', array('content' => 'render|content', 'footer' => 'render|footer', 'name' => 'strrev|Stefan'));
		exit();

		//echo Phetiche_server::DOCUMENT_ROOT();
		//Phetiche_format::tree(Phetiche_config::dump());

		//echo 'I am the default responder. <br />';

		/**
		 * The request and response objects are available here as well.
		 * Request arguments can be read and responses can be sent.
		 */
		//Phetiche_format::tree($this->req);

		// Create a simple image
		$image = new Phetiche_image();
		echo $image->render(195, 30, 'Welcome to my world!', 5, 10, 7, '0:41:93', '107:179:101', 1, true) . '<br /><br />';

		$image = new Phetiche_image();
		echo $image->render(195, 30, 'Another image...', 5, 10, 7, '107:179:101', '0:41:93', 1, true) . '<br /><br />';

		// Get the GET arguments

		echo 'I am a, again: '. $this->req->a . '<br \>';
		echo 'I am b: '. $this->req->b . '<br \>';
		echo 'I am c: '. $this->req->c . '<br \>';
		echo 'I am a: '. Phetiche_request::load()->a . '<br \><br />';

		//echo Phetiche_format::tree($this->req);

		//$auth = Phetiche_module::load('auth');
		//$auth->login('stefan', 'password');

		//$this->res->render('demo');

		$nn = Phetiche_module::load('basket', $this->req, $this->res);
		$nn->points = 5;
		$nn->makePoint();

		// Send a response, nicely
		//$this->res->httpCode(200)->body()->send(false);
	}

}
