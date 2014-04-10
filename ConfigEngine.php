<?php

namespace PetakUmpet;

// class as configurator

class ConfigEngine {

  const DBTYPE = 1;
  const DBHOST = 2;
  const DBNAME = 4;
  const DBUSER = 8;
  const DBCRED = 16;

  public function __call($name, $args)
  {
    if (substr($name, 0,3) == 'get') {
      $var = lcfirst(substr($name, 3));
      if (isset($this->$var)) {
        return $this->$var;
      }
      return null;
    }
  }

  public function setApplication($value)
  {
    $this->application = $value;
  }

  public function isOpenApp($app)
  {
    return $this->appsOpenState[$app];
  }

  public function getDbConfig($index)
  {
    if ($index === null) $index=0;
    if (!isset($this->dbConfig[$index])) return null;

    return $this->dbConfig[$index];
  }
  
  public function getLogLevel()
  {
    return $this->logLevel;
  }

  public function getEventLogging()
  {
    return $this->eventLogging; // false to turn off app/mod/act logging to database
  }

  public function isDevelopment()
  {
    return $this->development; // false to deactivate development functionalities
  }

  public function isAnonymousPage($page)
  {
    return in_array($page, $this->anonPages);
  }

  public function getUploadFolder()
  { 
    return PU_DIR . DS . 'web' . DS . 'uploads' . DS;
  }

  public function getRouting($path)
  {
    return $this->rc->getRouting($path);
  }

  public function getRoutingLinkFromPage($page)
  {
    return $this->rc->getRoutingLinkFromPage($page, $this->getApplication());
  }

}
