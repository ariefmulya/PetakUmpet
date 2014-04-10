<?php

namespace PetakUmpet;

class RoutingEngine {
  const APP    = 'app';
  const PAGE   = 'page';
  const ERROR_404_ROUTE = '404';

  private $routingTable;
  private $reverseTable;
  private $appList;
  private $appMap;
  private $reverseMap;

  /* Never Edit Following Functions, 
     all walking deads will rise up and hunt you ;-) */
  protected function cleanIndex($index)
  {
    $index = preg_replace('/\/+/', '/', $index);
    $index = preg_replace('/\/$/', '', $index);
    if ($index == '') $index = '/';
    return $index;
  }

  protected function compile($table, $map)
  {
    $ab = array();
    $ba = array();
    $rmap = array_flip($map);

    $this->appMap = $map;
    $this->reverseMap = $rmap;

    foreach ($table as $app => $arr) {
      /* collect known apps */
      $this->appList[$app] = $app;

      /* compile routing table */
      foreach ($arr as $route => $config) {
        $ix = $this->cleanIndex('/'. $app.'/'.$route);
        $routePath = $app . '/' . $config[self::PAGE];
        $rc = array(self::PAGE => $routePath , self::APP => $app);

        $ab[$ix] = $rc;
        $ba[$routePath] = $ix;

        /* build alias if any */
        if (isset($rmap[$app])) {
          $rapp = $rmap[$app];
          $this->appList[$rapp] = $rapp;

          $nx = $this->cleanIndex('/' . $rapp . '/' . $route);
          $shortPath = $this->cleanIndex($rapp . '/' . $config[self::PAGE]);
          $ab[$nx] = $rc;
          $ba[$shortPath] = $nx;
        }

      }
    }
    $this->routingTable = $ab;
    $this->reverseTable = $ba;
  }

  public function getApp($path)
  {
    $a = explode('/', $path);
    $ix = '';
    if (count($a) > 1) {
      $ix = $a[1]; /* second item _should_ contain app name ;-) */
    } 
    if ($ix == '') $ix = '/';

    if (isset($this->appList[$ix])) {
      return $ix;
    }
    return null;
  }

  public function getRouting($path)
  {
    $path = preg_replace('/\/$/', '', $path);

    if ($path == '') $path = '/';

    if ($path === null) {
      return $this->routeTo404($path); 
    }

    if (!isset($this->routingTable[$path])) {
      return $this->routeTo404($path);
    }

    return $this->routingTable[$path][self::PAGE];
  }

  public function routeTo404($path)
  {
    $app = $this->getApp($path);

    if (!isset($this->routingTable[$app . '/' . self::ERROR_404_ROUTE])) {
      return 'Error/err404';
    };

    return $this->routingTable[$app . '/' . self::ERROR_404_ROUTE][self::PAGE];
  }

  public function getRoutingLinkFromPage($page, $app)
  {
    if (isset($this->reverseMap[$app])) {
      $app = $this->reverseMap[$app];
    }
    $path = $this->cleanIndex($app . '/' . $page);

    if (isset($this->reverseTable[$path])) {
      return $this->reverseTable[$path];
    }
    return '/'; // preferred set to main page then null
  }

}