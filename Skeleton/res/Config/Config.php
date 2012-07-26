<?php

// class as configurator

abstract class Config {
  // real constants (all caps) DO NOT CHANGE
  const DBTYPE = 1;
  const DBHOST = 2;
  const DBNAME = 4;
  const DBUSER = 8;
  const DBCRED = 16;

  // configuration constants
  const ProjectTitle = 'Project';
  
  const LoginPage    = 'Login/index';
  const StartPage    = 'Home/index';

  public static function Database($index, $type)
  {
    // database configuration here
    // for multiple connection just add more index
    $config[0] = array(
        self::DBTYPE => 'PostgreSQL',
        self::DBHOST => 'localhost',
        self::DBNAME => 'test',
        self::DBUSER => 'test',
        self::DBCRED => 'test',
      );

    return (isset ($config[$index][$type]) ? $config[$index][$type] : null);
  }

  public static function getAnonymousPages()
  {
    return array(
        'Login/index',
        'Home/about',
        'Home/contact',
      );
  }
}
