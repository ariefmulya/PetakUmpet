<?php

abstract class puResponse {

	protected $module;
	protected $action;

	function __construct() {}
	function __destruct() {}

	function execute($mod=null, $act=null)
	{
		$this->module = $mod; 
		$this->action = $act;

		$function_full_name = 'show' . ucfirst($act);

		if (is_callable(array($this, $function_full_name))) {
			call_user_func(array($this, $function_full_name));
		} else {
			$this->process();
		}
		$view = PU_DIR . DS . 'app' . DS . 'mod' . DS . $mod . DS . 'view' . DS . $act . '.php';

		/* XXX at this point existing member vars of this class will be available to template XXX */	
		extract(get_object_vars($this));

		ob_start();
		include_once($view);
		$this->contents = ob_get_contents();
		ob_end_clean();

		/* FIXME: 
		 *  Add:
		 *   browser type identification 
		 *   ability to set display output format (text, xml, json, etc)
		 */
		$Output = new puLayout($this);
		$Output->display();
	}

	abstract function process();
}