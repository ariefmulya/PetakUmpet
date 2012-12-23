<?php

namespace PetakUmpet;

class Menu {
  const MENU_TOPMENU=1;
  const MENU_SUBLEVEL=2;

  const TYPE_MENUBAR=4;
  const TYPE_DROPDOWN=4;

  private $menu;
  private $formatter;

  public function __construct($menu=array(), $formatter='\\PetakUmpet\\Menu\\Formatter\\MenuBootstrap')
  {
    $this->buildMenu($menu);
    $this->formatter = new $formatter($this);

    var_dump($menu);
    var_dump($this->tree);
    $this->breadcrumb('Rumah Sakit');
  }

  private function buildMenu($menu, $parent=null)
  {
    static $tree = array();
    static $crumbs = array();

    foreach ($menu as $k=>$v) {
      if (is_array($v)) {
        $this->buildMenu($v, $k); 
      }
      if ($parent !== null) {
        $tree[$parent][$k] = $v;
      } else {
        $tree[$k] = $v;
      }
      $crumbs[$k] = $parent;
    }
    $this->tree = $tree;
    $this->crumbs = $crumbs;
  }

  public function breadcrumb($target)
  {
    $bread[] = $target;
    while ($this->crumbs[$target] !== null) {
      $bread[] = $this->crumbs[$target];
      $target = $this->crumbs[$target];
    }
    var_dump($bread);
  }

  public function __toString()
  {
    $cname = $this->formatter;
    $formatter = new $cname($this);

    return (string) $formatter;
  }

  public function setFormatter($formatter)
  {
    $this->formatter = new $formatter($this);
  }

  public function renderNavMenu()
  {
    return $this->formatter->navMenu();
  }

  public function getTopMenu($value='')
  {
  }

}