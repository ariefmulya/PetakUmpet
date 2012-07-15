<?php

namespace PetakUmpet\Form;

class TextboxField {

  private $name;
  private $label;

  public function __construct($name=null)
  {
    if ($name === null ) throw new \Exception('TextboxField need to have name');

    $this->name = $name;
  }

  public function __toString()
  {
    $nm = $this->name;
    $lb = $this->label === null ? ucfirst($nm) : $this->label;

    $s = '<label for="'.$nm.'">'.$lb.'</label><input id="'.$nm.'" name="'.$this->name.'" type="text" />';

    return $s;
  }
}