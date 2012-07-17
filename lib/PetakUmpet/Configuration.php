<?php
namespace PetakUmpet;

// class as configurator

abstract class Configuration {
  // real constants (all caps) DO NOT CHANGE
  const DBTYPE = 1;
  const DBHOST = 2;
  const DBNAME = 4;
  const DBUSER = 8;
  const DBCRED = 16;

  // configuration constants
  const ProjectTitle = 'Informasi Farmasi';
  const StartPage    = 'Login/index';

  public static function Database($index, $type)
  {
    // database configuration here
    // for multiple connection just add more index
    $config[0] = array(
        self::DBTYPE => 'PostgreSQL',
        self::DBHOST => 'localhost',
        self::DBNAME => 'project_sehat',
        self::DBUSER => 'dbsys',
        self::DBCRED => '1',
      );

    return (isset ($config[$index][$type]) ? $config[$index][$type] : null);
  }
}
