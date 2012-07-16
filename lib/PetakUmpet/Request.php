<?php
namespace PetakUmpet;

class Request {
  const MOD_ACCESSOR = 'm';
  const ACT_ACCESSOR = 'r';

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

  function isPost()
  {
    return $this->is_post;
  }

  function getData($name, $default=null)
  {
    if (isset($this->request_data[$name])) {
      return $this->request_data[$name];
    }
    return $default;
  }

  function getPage()
  {
    $m = $this->getData(self::MOD_ACCESSOR);
    $a = $this->getData(self::ACT_ACCESSOR);

    if (!$m) {
      return '/';
    }
    if (!$a) {
      return $m . '/index';
    }
    return '/';
  }

  function getModule()
  {
    return $this->getData(self::MOD_ACCESSOR);
  }

  function getAction()
  {
    return $this->getData(self::ACT_ACCESSOR);
  }

}

