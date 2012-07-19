<?php
namespace PetakUmpet;

class Router {

	protected $request;
	protected $session;

	function __construct(Request $request, Session $session)
	{	
		$this->request  = $request;
		$this->session  = $session;
	}

	function handle()
	{
		Logger::log('Router->handle() called');
		
		$this->load($this->request->getPage());
	}

	function load($page)
	{
		if ($page == '/') $page = Configuration::StartPage;

		// FIXME: this is not yet elegant ;-)
		if (!$this->session->getAuthenticated() && !in_array($page, Configuration::getAnonymousPages())) {
			return $this->redirect(Configuration::LoginPage);
		}

		list($mod, $act) = explode('/', $page);

		$target  = PU_DIR . DS . 'src' . DS . $mod . 'Application.php';

		Logger::log("Router: getting application $target");

		if (is_file($target)) {
			include($target);
			$class_name = $mod.'Application';
			$app = new $class_name($this, $this->request, $this->session);
			$function_full_name = $act.'Action';

			if ($app instanceof \PetakUmpet\Application && is_callable(array($app, $function_full_name))) {

				Logger::log("Router: loading $class_name->$function_full_name");

				Event::log("loading");

				return call_user_func(array($app, $function_full_name));
			}
		}
		return $this->load404();
	}

	function redirect($page)
	{
    $page = str_replace('/', '&a=', $page);
    Header("Location: index.php?m=$page");
    exit();
	}

	function load404()
	{
		// TODO
		die('404');
	}
}

