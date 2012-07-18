<?php
namespace PetakUmpet;

class Layout {

	private $layout;
	
	function __construct(Response $response, $variables=null, $layout=null)
	{
		// setting up
		$this->setLayout($layout);

		$this->request = Singleton::acquire('\\PetakUmpet\\Request'); 
		$this->session = Singleton::acquire('\\PetakUmpet\\Session'); 

		/* XXX at this point existing member vars of 
		this class will be available to template XXX */	
		extract(get_object_vars($this));
		extract($variables);

		// configuration variables
		$ProjectTitle = Configuration::ProjectTitle;

		// response contents
		$__mainContents = $response->contents;

		Logger::log('Layout: rendering  using ' . $this->layout);

		// render them
		include_once($this->layout);
	}

	function setLayout($layout=null)
	{
		$this->layout = PU_DIR . DS . 'res' . DS . 'View' . DS . ($layout === null ? 'layout' : $layout) . '.php' ;
	}
}