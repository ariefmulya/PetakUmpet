<?php
namespace PetakUmpet;

class Layout {

	protected $response;
	
	function __construct(Response $Response, $layout=null)
	{
		$this->response = $Response;
		$this->setLayout($layout);
	}

	function getContents()
	{
		$this->response->contents;
	}

	function setLayout($layout=null)
	{
		$this->layout = PU_DIR . DS . 'res' . DS . 'View' . DS . ($layout === null ? 'layout' : $layout) . '.php' ;
	}

	function render()
	{
		$__mainContents = $this->response->contents;
		Logger::log('Layout: rendering  using ' . $this->layout);
		include_once($this->layout);
	}
}