<?php
namespace PetakUmpet;

class Session {

  protected $session_data;

	function __construct() 
  {
    $this->session_data =& $_SESSION;
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