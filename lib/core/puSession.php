<?php

class puSession {

  protected $session_data;

	function __construct() 
  {
    $this->session_data =& $_SESSION;
  }

  function __destruct() {}

  function getDataOrSet($name, $value=null)
  {
    if (isset($this->session_data[$name])) {
      return $this->session_data[$name];
    }

    if ($value !== null) {
      $this->session_data[$name] = $value;
    }
    return $value;
  }

}