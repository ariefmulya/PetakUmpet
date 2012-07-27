<?php
namespace PetakUmpet;

class Process {

	protected $request;
	protected $session;
	protected $config;

	function __construct(Request $request, Session $session, Config $config)
	{	
		$this->request  = $request;
		$this->session  = $session;
		$this->config   = $config;
	}

	function run()
	{
		$this->load($this->request->getPage());
	}

	function load($page)
	{
		$app = $this->request->getApplication();

		if ($page == '/') $page = $this->config->getStartPage();

		// TODO: Add abilities to make all pages accessible 
		if (!$this->session->getUser() && !$this->config->isAnonymousPage($page)) {
			return $this->redirect($this->config->getLoginPage());
		}

		list($mod, $act) = explode('/', $page);

		$appfile = 'app' . DS . $app . DS . $mod . 'Application.php';
		$target  = PU_DIR . DS . $appfile;

		if (is_file($target)) {
			Logger::log("Process: getting application $appfile");
			include($target);

			$class_name = '\\' . $app . '\\' . $mod .'Application';
			$app = new $class_name($this, $this->request, $this->session, $this->config);

			$function_full_name = $act.'Action';

			if ($app instanceof \PetakUmpet\Application && is_callable(array($app, $function_full_name))) {

				Logger::log("Process: loading $class_name->$function_full_name");

				Event::log("loading");

				return call_user_func(array($app, $function_full_name));
			}
		}
		return $this->load404();
	}

	function redirect($page)
	{
    $href = $this->request->getAppUrl($page);
    Header("Location: $href");
    exit();
	}

	function redirectToStartPage()
	{
		return $this->redirect($this->config->getStartPage());
	}

	function redirectToLoginPage()
	{
		return $this->redirect($this->config->getLoginPage());
	}

	function load404()
	{
		// TODO
		die('404');
	}
}

