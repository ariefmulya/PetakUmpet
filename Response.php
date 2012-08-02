<?php
namespace PetakUmpet;

class Response {

	private $request;
	private $session;
	private $config;
	
	private $baseViewDir;

	public function __construct($responseText=null, $httpStatusCode=200)
	{
		// normal mode
		if ($responseText === null) {
			$this->request = Singleton::acquire('\\PetakUmpet\\Request');
			$this->session = Singleton::acquire('\\PetakUmpet\\Session');
			$this->config  = Singleton::acquire('\\PetakUmpet\\Config');

	    $this->baseViewDir = PU_DIR . DS . 'app' . DS . $this->request->getApplication() . DS . 'View' . DS ;
			return;
		}

		$httpStatus = array(
			200 => 'OK',
			404 => 'Not Found'
			);

		// direct response mode
		// need to implement more status code until php 5.4 is everywhere
		// by then we can just use http_response_code()
		header('HTTP/1.1 ' . $httpStatusCode . ' ' . $httpStatus[$httpStatusCode]);
		echo $responseText;
		exit();
	}

	public function render($view, $variables=array(), Template $T)
	{
		$template = $this->baseViewDir . str_replace('/', DS, $view) . '.php';

		if (!is_file($template)) {
			throw new \Exception("Template file $template does not exist\n");
			return;
		}

		Logger::log('Response: using template '. $template);

		/* XXX at this point existing member vars of 
		this class will be available to template XXX */	
		extract(get_object_vars($this));

		if (count($variables) > 0) {
			extract($variables, EXTR_SKIP);
		} 

		ob_start();
		require($template);
		$this->contents = ob_get_contents();
		ob_end_clean();

		return $this->contents;
	}

}