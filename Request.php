<?php
namespace PetakUmpet;

class Request {
  const MOD_ACCESSOR = 'm';
  const ACT_ACCESSOR = 'a';

  private $requestData;
  private $base_url;
  private $root_url;
  private $resource_base_url;
  private $query_string;
  private $full_url;
  private $method;
  private $is_post;

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

    $this->query_string = $_SERVER['QUERY_STRING'];

    $this->full_url = $this->base_url . '?' . $this->query_string;

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

  public function setApplication($value)
  {
    return $this->set('application', $value);
  }

  public function getApplication()
  {
    return $this->get('application');
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

  public function getAppUrl($page, $attr=array())
  {
    $page = str_replace('/', '&' . self::ACT_ACCESSOR .'=', $page);

    foreach ($attr as $k => $v) {
      $page .= "&$k=$v";
    }

    return $this->base_url . $this->getPathInfo() . '?'.self::MOD_ACCESSOR.'=' . $page;
  }

  public function getModule()
  {
    return $this->get(self::MOD_ACCESSOR);
  }

  public function getAction()
  {
    return $this->get(self::ACT_ACCESSOR);
  }

  public function setSubNavMenu($value)
  {
    $this->requestData['subNavMenu'] = $value;
  }

  public function getSubNavMenu()
  {
    if (isset($this->requestData['subNavMenu']))
      return $this->requestData['subNavMenu'];
    return null;
  }

}

