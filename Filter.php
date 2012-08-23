<?php

namespace PetakUmpet;

class Filter {

  private $base;  // base filter, applied to all needs
  private $url;   // filter for url query strings
  private $query; // filter for SQL queries

  const BASE = 1;
  const URL = 2;
  const QUERY = 3;

  public function __construct()
  {
    $this->base  = array();
    $this->url   = array();
    $this->query = array();
  } 

  public function add($key, $value)
  {
    $this->base[$key] = $value;
  }

  public function addUrl($key, $value)
  {
    $this->url[$key] = $value;
  }

  public function addQuery($key, $value)
  {
    $this->query[$key] = $value;
  }

  public function setValue($key, $value, $type=self::BASE)
  {
    if ($type === self::BASE) $data =& $this->base;
    if ($type === self::URL) $data =& $this->url;
    if ($type === self::QUERY) $data =& $this->query;

    if (isset($data[$key])) {
      $data[$key] = $value;
    }
  }

  public function getValue($key, $type=self::BASE)
  { 
    if ($type === self::BASE) $data =& $this->base;
    if ($type === self::URL) $data =& $this->url;
    if ($type === self::QUERY) $data =& $this->query;

    if (isset($data[$key])) {
      return $data[$key];
    }
  }

  public function getUrlFilter($withBase=false)
  {
    $s = '';

    if ($withBase) {
      foreach ($this->base as $k => $v) {
        $s .= "&$k=$v";
      }
    }

    foreach ($this->url as $k => $v) {
      $s .= "&$k=$v";
    }
    return $s;
  }

  public function getFilterData($base=true, $url=true, $query=true)
  {
    $data = array();

    if ($base) array_merge($data, $this->base);
    if ($url) array_merge($data, $this->url);
    if ($query) array_merge($data, $this->query);

    return $data;
  }

  public function getQueryData()
  {
    return $this->getFilterData(false, false, true);
  }

  public function getUrlData()
  {
    return $this->getFilterData(false, true, false);
  }

  public function getQueryValue($key=null)
  {
    if ($key !== null) return $this->getValue($key, self::QUERY);
  }

  public function getUrlValue($key=null)
  {
    if ($key !== null) return $this->getValue($key, self::URL);
  }

}