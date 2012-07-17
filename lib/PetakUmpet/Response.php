<?php
namespace PetakUmpet;

class Response {

	private $response;

	protected $request;
	protected $session;

	private $layout;

	function __construct(Request $request, Session $session)
	{
		$this->response = $this;
		$this->request = $request;
		$this->session = $session;

		$this->layout = null;
	}

	function setLayout($layout)
	{
		$this->layout = $layout;
		return $this;
	}

	function render($view=null, $variables=null)
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

		return new Layout($this, $layout, $variables);
	}

}