<?php

namespace PetakUmpet;

// class as configurator

class Config {
  // real constants (all caps) DO NOT CHANGE
  const DBTYPE = 1;
  const DBHOST = 2;
  const DBNAME = 4;
  const DBUSER = 8;
  const DBCRED = 16;

  public function __construct()
  {
    /* project environment setup */
    $this->projectTitle = 'Application Name';
    $this->loginPage    = 'Login/index';
    $this->startPage    = 'Home/index';
    $this->noAccessPage = 'User/noAccess';

    $this->anonPages    = array(
        'Home/about',
        'Home/contact',
        'Login/index',
        'User/noAccess',
      );

    /* available applications, indexed by path */
    $this->pathApps = array(
        '/' => 'AppName',
      ); 

    /* database configuration 
       for multiple connection just add more index */
    $this->dbConfig[0] = array(
        self::DBTYPE => 'PostgreSQL',
        self::DBHOST => 'localhost',
        self::DBNAME => 'test',
        self::DBUSER => 'test',
        self::DBCRED => 'test',
      );
  }

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

  public function getApplication($path)
  {
    if (!isset($this->pathApps[$path])) 
      return null;

    return $this->pathApps[$path];
  }

  public function getDbConfig($index)
  {
    if ($index === null) $index=0;
    if (!isset($this->dbConfig[$index])) return null;

    return $this->dbConfig[$index];
  }

  public function isAnonymousPage($page)
  {
    return in_array($page, $this->anonPages);
  }
}
