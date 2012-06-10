<?php

class puResponse {
	function __construct() {}
	function __destruct() {}

	function execute($mod=null, $act=null)
	{
		$function_full_name = 'show' . ucwords($act);

		if (is_callable(array($this, $function_full_name))) {
			call_user_func(array($this, $function_full_name));
		} else {
			$this->show();
		}
		$view = PU_DIR . DS . 'app' . DS . 'mod' . DS . $mod . DS . 'view' . DS . $act . '.php';

		extract(get_object_vars($this));
		include_once($view);
	}
}