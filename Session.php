<?php
namespace PetakUmpet;

class Session {

  private $app;

  public function __construct()
  {
    $this->config = Singleton::acquire('\\PetakUmpet\\Config');
    $this->app = '/' . $_SERVER['SERVER_NAME'];

    if (session_id() == '') return $this->start();
  }

  public function setApplication($val)
  {
    if ($val === null || $val == '') {
      die('Session App Error... exiting.');
    } 
    session_destroy();
    $this->app = $val;
    $this->start();
  }

  public function start()
  {
    // FIXME: need a better session_id source, 
    // possibly using random generators
    // also need prevention against session hijacking
    if (session_id() == '') {
      $app = $this->app;
      if ($this->app === null) $app = '/' . $_SERVER['SERVER_NAME'];
      session_name($app);
      session_start();
    }
  }

  public function destroy()
  {
    session_regenerate_id(true);
    session_destroy();
  }

  public function prepareAjaxToken()
  {
    // Set Token for ajax security effort
    // TODO: need more random token
    $this->setToken(sha1(time()));
  }

  public function get($name) 
  {
    if (!isset($_SESSION)) return null;

    if (isset($_SESSION[$name])) {
      return $_SESSION[$name];
    }
    return null;
  }

  public function set($name, $value)
  {
    $_SESSION[$name] = $value;
  }

  public function remove($name)
  {
    $_SESSION[$name] = null;
    unset($_SESSION[$name]);
  }  

  public function getOrSet($name, $value=null)
  {
    if (isset($_SESSION[$name])) {
      return $_SESSION[$name];
    }

    if ($value !== null) {
      $_SESSION[$name] = $value;
    }
    return $value;
  }

  public function setUser($value)
  {
    $_SESSION['user'] = $value;
  }

  public function getUser()
  {
    if (!isset($_SESSION) || !isset($_SESSION['user']) || !is_object($_SESSION['user'])) return null;
    return $_SESSION['user'];
  }
  
  public function setAuthenticated($value=true)
  {
    if ($value === true) {
      session_regenerate_id(true);
    }
    $_SESSION['authenticated'] = $value;
  }
  
  public function getAuthenticated()
  {
    if (!isset($_SESSION['authenticated'])) return null;
    return $_SESSION['authenticated'];
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

  public function setSubNavMenu($value)
  {
    $_SESSION['subNavMenu'] = $value;
  }

  public function getSubNavMenu()
  {
    if (isset($_SESSION['subNavMenu']))
      return $_SESSION['subNavMenu'];
    return null;
  }


}
