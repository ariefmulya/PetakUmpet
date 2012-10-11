<?php

namespace PetakUmpet;

class Validator {

  private $list;

  public function __construct()
  {
    $this->list = array();
  }

  public function add($name=null, \PetakUmpet\Validator\Base $validator)
  {
    if ($name === null || $name == '') throw new \Exception('Validator::set need a target name');

    $this->list[$name] = $validator;
  }

  public function check($name=null, $value=null)
  {
    if ($name === null || $name == '') throw new \Exception('Validator::check need a target name');

    if (!isset($this->list[$name])) return true;

    return $this->list[$name]->check($value);
  }

  public function remove($name)
  {
    if (isset($this->list[$name])) {
      unset($this->list[$name]);
    }
  }

  public function getErrorText($name=null)
  {
    if ($name === null || $name == '') throw new \Exception('Validator::check need a target name');

    return $this->list[$name]->getErrorText();
  }
}