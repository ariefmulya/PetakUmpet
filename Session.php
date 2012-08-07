<?php
namespace PetakUmpet;

class Session {

  public function __construct()
  {
    $this->config = Singleton::acquire('\\PetakUmpet\\Config');
  }

  function __call($name, $args)
  {
    if (session_id() == "") $this->start();
    if (substr($name, 0,3) == 'get') 
      return $this->get(strtolower(substr($name, 3)));
    if (substr($name, 0,3) == 'set') { 
      return $this->set(strtolower(substr($name, 3)), $args[0]);
    }
  }

  public function start()
  {
    session_id(sha1($this->config->getProjectTitle())); 
    session_start();
  }

  function destroy()
  {
    if (session_id() == "") $this->start();
    session_destroy();
  }

  function prepareAjaxToken()
  {
    // Set Token for ajax security effort
    // TODO: need more random token
    $this->setToken(sha1(time()));
  }

  function get($name) 
  {
    if (!isset($_SESSION)) return null;


    if (isset($_SESSION[$name])) {
      return $_SESSION[$name];
    }
    return null;
  }

  function set($name, $value)
  {
    $_SESSION[$name] = $value;
  }

  function getOrSet($name, $value=null)
  {
    if (isset($_SESSION[$name])) {
      return $_SESSION[$name];
    }

    if ($value !== null) {
      $_SESSION[$name] = $value;
    }
    return $value;
  }

  public function setFlash($value=null, $name='default')
  {
    $_SESSION['flash'][$name] = $value;
  }

  public function getFlash($name='default')
  {
    if (!isset($_SESSION['flash']) || !isset($_SESSION['flash'][$name])) 
      return null;

    $value = $_SESSION['flash'][$name];
    unset($_SESSION['flash'][$name]);

    return $value;
  }
}