<?php
namespace PetakUmpet;

class Request {
  const MOD_ACCESSOR = 'm';
  const ACT_ACCESSOR = 'a';

  protected $request_data;
  protected $request_base_url;
  protected $resource_base_url;
  protected $query_string;
  protected $request_full_url;
  protected $request_method;
  protected $is_post;

  public function __construct()
  {
    $port  =& $_SERVER['SERVER_PORT'];
    $https =& $_SERVER['HTTPS'];
    $protocol = 'http';
      if (!empty($https) && $https != 'off') {
        $protocol = 'https';
      }

    $this->request_data = $_REQUEST;
    $this->request_root_url = $protocol . '://' . $_SERVER['SERVER_NAME'] . 
                  ($port == '80' ? '' : ":$port" ) ;  

    $this->request_base_url = $this->request_root_url . $_SERVER['SCRIPT_NAME'];

    $this->query_string = $_SERVER['QUERY_STRING'];

    $this->request_full_url = $this->request_base_url . '?' . $this->query_string;

    $this->resource_base_url = $this->request_root_url . dirname ($_SERVER['SCRIPT_NAME']) . '/';

    $this->request_method = $_SERVER['REQUEST_METHOD'];
    $this->is_post = $this->request_method == 'POST' ;
  }

  public function __call($name, $args)
  {
    if (substr($name, 0,3) == 'get') 
      return $this->get(strtolower(substr($name, 3)));
    if (substr($name, 0,3) == 'set') 
      return $this->set(strtolower(substr($name, 3)), $args[0]);
  }

  public function isSecureAjax()
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

  public function isPost()
  {
    return $this->is_post;
  }

  public function get($name, $default=null)
  {
    if (isset($this->request_data[$name]) && $this->request_data[$name] !== false && $this->request_data[$name] != '') {
      return $this->request_data[$name];
    }
    return $default;
  }

  public function set($name, $value)
  {
    $this->request_data[$name] = $value;
  }

  public function getPathInfo()
  {
    $path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
    if ($path != '/') rtrim($path, '/');

    return $path;
  }

  public function getResourceBaseUrl()
  {
    return $this->resource_base_url;
  }

  public function getFullUrl()
  {
    return $this->request_full_url;
  }

  public function getData()
  {
    return $this->request_data;
  }
  
  public function getPage()
  {
    $m = $this->get(self::MOD_ACCESSOR);
    $a = $this->get(self::ACT_ACCESSOR);

    if (!$m) {
      return '/';
    }
    if (!$a) {
      return $m . '/index';
    }
    return $m.'/'.$a;
  }

  public function getAppUrl($page, $attr=array())
  {
    $page = str_replace('/', '&' . self::ACT_ACCESSOR .'=', $page);

    foreach ($attr as $k => $v) {
      $page .= "&$k=$v";
    }

    return $this->request_base_url . $this->getPathInfo() . '?'.self::MOD_ACCESSOR.'=' . $page;
  }

  public function getModule()
  {
    return $this->get(self::MOD_ACCESSOR);
  }

  public function getAction()
  {
    return $this->get(self::ACT_ACCESSOR);
  }

}

