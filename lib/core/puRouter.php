<?php

class puRouter {
	const INDEX_PAGE = 'default/index';

	protected $Request;
	protected $Session;
	protected $Security;

	function __construct(puRequest $Request, puSession $Session, puSecurity $Security) 
	{	
		$this->Request  = $Request;
		$this->Session  = $Session;
		$this->Security = $Security;
	}

	function __destruct() 
	{
	}

	function handle()
	{
		if ($this->Security->check($this->Request, $this->Session)) {
			return $this->forward();
		} else {
			return $this->forwardToLogin();
		}
	}

	function forward()
	{
		$this->load($this->Request->getPage());
	}

	function forwardToLogin()
	{
		$this->load($this->Security->getLoginPage());
	}

	function load($page)
	{
		if ($page == '/') $page = self::INDEX_PAGE;

		list($mod, $act) = explode('/', $page);

		$page_response = PU_DIR . DS . 'app' . DS . 'mod' . DS . $mod . DS . 'response' . DS . $act . '.php';
		$module_response = PU_DIR . DS . 'app' . DS . 'mod' . DS . $mod . DS . 'response' . DS . 'default.php';

		if (file_exists($page_response)) {
			include_once($page_response);
			$cname = $act.'Response';
			$Response = new $cname;
		} else if (file_exists($module_response)) {
			include_once($module_response);
			$Response = new defaultResponse;
		} else {
			$this->load404();
		}

		if ($Response instanceof puResponse) {
			$Response->execute($mod, $act);
		}
		// should not be reached
	}

	function load404()
	{
		include_once('404.php');
	}
}

