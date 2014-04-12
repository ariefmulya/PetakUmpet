<?php
namespace PetakUmpet;

class Request {
  const MOD_ACCESSOR = 'm';
  const ACT_ACCESSOR = 'a';
  const APP_ACCESSOR = 'application';

  private $requestData;
  private $base_url;
  private $path;
  private $root_url;
  private $resource_base_url;
  private $query_string;
  private $full_url;
  private $method;
  private $is_post;

  private $app;

  public function __construct()
  {
    $port  =& $_SERVER['SERVER_PORT'];
    $https =& $_SERVER['HTTPS'];
    $protocol = 'http';
      if (!empty($https) && $https != 'off') {
        $protocol = 'https';
      }

    $this->requestData =& $_REQUEST;
    $this->root_url = $protocol . '://' . $_SERVER['SERVER_NAME'] . 
                  ($port == '80' ? '' : ":$port" ) ;  

    $this->base_url = $this->root_url . $_SERVER['SCRIPT_NAME'];

    $this->query_string = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''; 

    $this->path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
    if ($this->path != '/') rtrim($this->path, '/');

    $this->full_url = $this->base_url . $this->path . '?' . $this->query_string;

    $this->resource_base_url = $this->root_url . dirname ($_SERVER['SCRIPT_NAME']) . '/';

    $this->method = $_SERVER['REQUEST_METHOD'];
    $this->is_post = $this->method == 'POST' ;
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
    if (isset($this->requestData[$name]) && $this->requestData[$name] !== false && $this->requestData[$name] != '') {
      return $this->requestData[$name];
    }
    return $default;
  }

  public function set($name, $value)
  {
    $this->requestData[$name] = $value;
  }

  public function setTriplets($app, $mod, $act)
  {
    $this->app = $app;
    /* trade-off for backward compatibility for now */
    if (!isset($this->requestData[self::MOD_ACCESSOR])) {
      $this->requestData[self::MOD_ACCESSOR] = $mod;
    }
    if (!isset($this->requestData[self::ACT_ACCESSOR])) {
      $this->requestData[self::ACT_ACCESSOR] = $act;
    }
  }

  public function getPathInfo()
  {
    return $this->path;
  }

  public function getResourceBaseUrl()
  {
    return $this->resource_base_url;
  }

  public function getFullUrl()
  {
    return $this->full_url;
  }

  public function getData()
  {
    return $this->requestData;
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

  public function getApplication()
  {
    if ($this->app === NULL) {
      die("<p><strong>PetakUmpetErrorMessage:</strong> Something is wrong, please verify your route or config file.</p>");
    }
    return $this->app;
  }

  public function getAppUrl($page, $attr=array())
  {
    $page = str_replace('/', '&' . self::ACT_ACCESSOR .'=', $page);

    foreach ($attr as $k => $v) {
      $page .= "&$k=$v";
    }

    return $this->base_url . $this->getPathInfo() . '?'.self::MOD_ACCESSOR.'=' . $page;
  }

  public function getBaseUrl()
  {
    return $this->base_url;
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

