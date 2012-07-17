<?php
namespace PetakUmpet;

class Session {

  private $session_data;

	function __construct() 
  {
    $this->session_data =& $_SESSION;
  }

  function get($name) 
  {
    if (isset($this->session_data[$name])) {
      return $this->session_data[$name];
    }

    return null;
  }

  function getOrSet($name, $value=null)
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