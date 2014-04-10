<?php
namespace PetakUmpet;

class Process {

	private $request;
	private $session;
	private $config;

	public function __construct(Request $request, Session $session, Config $config)
	{	
		$this->request  = $request;
		$this->session  = $session;
		$this->config   = $config;
	}

	public function sanitize()
	{
		// contain secure/sanity checks
		// and everything else we can do to harden our env

		// if register globals is on and session-injection is tried, get out
		if (isset($_REQUEST['_SESSION'])) { die ("Sanity check failed" . PHP_EOL); }
	}

	public function run()
	{
		$this->sanitize();

		$this->load($this->request->getPathInfo());
	}

	public function load($path)
	{
		$page = $this->config->getRouting($path);

		list($app, $mod, $act) = explode('/', $page);

    $this->request->setTriplets($app, $mod, $act);

    /* we want Request to sanitize the triplets, and re-get the results here 
    	 this is mainly for backward compatibility purpose, keeping it for now*/ 
    $app = $this->request->getApplication();
    $mod = $this->request->getModule();
    $act = $this->request->getAction();

    $this->config->setApplication($app); /* config needs to know what is active app now */
    
		$appfile = 'app' . DS . $app . DS . $mod . 'Application.php';
		$target  = PU_DIR . DS . $appfile;

		if (!is_file($target))
			return $this->load404();

		if (!$this->config->isOpenApp($app)) {
			if (!$this->config->isAnonymousPage($page)) {
        $this->session->start();
        $user = $this->session->getUser();
				if (!$user || !is_object($user)) {
					return $this->redirect($this->config->getLoginPage());
				}
				if (!$user->hasAccess($page)) {
					return $this->redirect($this->config->getNoAccessPage());
				}
			}
		}

		Logger::log("Process: getting application $appfile", Logger::DEBUG);
		include($target);

		$class_name = '\\' . $app . '\\' . $mod .'Application';
		$app = new $class_name($this, $this->request, $this->session, $this->config);

		$function_full_name = $act.'Action';

		if ($app instanceof \PetakUmpet\Application && is_callable(array($app, $function_full_name))) {

			Logger::log("Process: loading $class_name->$function_full_name", Logger::DEBUG);

			Event::log("loading");


			if (!method_exists($app, $function_full_name)) 
				return $this->load404();

			return call_user_func(array($app, $function_full_name));
		}
	}

	public function redirect($page)
	{
    $href = $this->request->getAppUrl($page);
    Header("Location: $href");
    exit();
	}

	public function redirectToStartPage()
	{
		return $this->redirect($this->config->getStartPage());
	}

	public function redirectToLoginPage($extra='')
	{
		return $this->redirect($this->config->getLoginPage() . $extra);
	}

	public function load404()
	{
		$r = new Template($this->request, $this->session, $this->config);
		return $r->render(Response::PetakUmpetView . 'Error/404');
	}
}

