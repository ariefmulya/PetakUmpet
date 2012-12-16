<?php

namespace PetakUmpet;

class Menu {
  const MENU_HEADER=1;
  const MENU_SUBLEVEL=2;

  const TYPE_MENUBAR=4;
  const TYPE_DROPDOWN=4;

  private $menu;
  private $formatter;

  public function __construct($menu=array(), $formatter='\\PetakUmpet\\Menu\\Formatter\\MenuBootstrap')
  {
    $this->menu = $menu;
    $this->formatter = $formatter;
  }

  public function __toString()
  {
    $cname = $this->formatter;
    $formatter = new $cname($this);

    return (string) $formatter;
  }

  public function setFormatter($formatter)
  {
    $this->formatter = $formatter;
  }

}