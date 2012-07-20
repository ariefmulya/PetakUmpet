<?php
namespace PetakUmpet;

class Request {
  const APP_ACCESSOR = 'm';
  const ACT_ACCESSOR = 'a';

  protected $request_data;
  protected $request_base_url;
  protected $query_string;
  protected $request_full_url;
  protected $request_method;
  protected $is_post;

  function __construct()
  {
    $port  =& $_SERVER['SERVER_PORT'];
    $https =& $_SERVER['HTTPS'];
    $protocol = 'http';
      if (!empty($https) && $https != 'off') {
        $protocol = 'https';
      }

    $this->request_data =& $_REQUEST;
    $this->request_base_url = $protocol . '://' . $_SERVER['SERVER_NAME'] . 
                  ($port == '80' ? '' : ":$port" ) .  $_SERVER['SCRIPT_NAME'];
    $this->query_string = $_SERVER['QUERY_STRING'];
    $this->request_full_url = $this->request_base_url . '?' . $this->query_string;

    $this->request_method = $_SERVER['REQUEST_METHOD'];
    $this->is_post = $this->request_method == 'POST' ;
  }

  function __call($name, $args)
  {
    if (substr($name, 0,3) == 'get') 
      return $this->get(strtolower(substr($name, 3)));
    if (substr($name, 0,3) == 'set') 
      return $this->set(strtolower(substr($name, 3)), $args[0]);
  }

  function isSecureAjax()
  {
    $ajax = false;
    $secure = true;

    if( !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) {
      $ajax = true;
    }
    // TODO: Check for secure ajax

    return ($ajax && $secure);
  }

  function isPost()
  {
    return $this->is_post;
  }

  function get($name, $default=null)
  {
    if (isset($this->request_data[$name])) {
      return $this->request_data[$name];
    }
    return $default;
  }

  public function getData()
  {
    return $this->request_data;
  }
  
  function getPage()
  {
    $m = $this->get(self::APP_ACCESSOR);
    $a = $this->get(self::ACT_ACCESSOR);

    if (!$m) {
      return '/';
    }
    if (!$a) {
      return $m . '/index';
    }
    return $m.'/'.$a;
  }

  function getModule()
  {
    return $this->get(self::APP_ACCESSOR);
  }

  function getAction()
  {
    return $this->get(self::ACT_ACCESSOR);
  }

}

