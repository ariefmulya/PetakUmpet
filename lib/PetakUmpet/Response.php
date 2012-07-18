<?php
namespace PetakUmpet;

class Response {

	private $response;

	private $request;
	private $session;

	function __construct($responseText=null, $httpStatusCode='200')
	{
		// normal mode
		if ($responseText === null) {
			$this->response = $this;
			$this->request = Singleton::acquire('\\PetakUmpet\\Request');
			$this->session = Singleton::acquire('\\PetakUmpet\\Session');
			return;
		}

		// direct response mode
		// need to implement more status code until php 5.4 is everywhere
		// by then we can just use http_response_code()
		header('HTTP/1.1 ' . $httpStatusCode . ' OK');
		echo $responseText;
		exit();
	}

	function render($view=null, $variables=null, $layout=null)
	{
		$template = PU_DIR . DS . 'res' . DS . 'View' 
						. DS . $this->request->getModule() . DS . $this->request->getAction() . '.php';

		if ($view !== null) {
			$view = str_replace('/', DS, $view);
			$template = PU_DIR . DS . 'res' . DS . 'View' . DS . $view . '.php';
		}

		Logger::log('Response: using template '. $template);

		/* XXX at this point existing member vars of 
		this class will be available to template XXX */	
		extract(get_object_vars($this));

		if (count($variables) > 0) {
			extract($variables, EXTR_SKIP);
		} 

		ob_start();

		if (is_file ($template)) {
			require($template);
		}
		$this->contents = ob_get_contents();
		ob_end_clean();

		// no layout on Ajax Call
		if ($this->request->isSecureAjax()) {
			echo $this->contents;
			exit();
		}
		return new Layout($this, $variables, $layout);
	}

}