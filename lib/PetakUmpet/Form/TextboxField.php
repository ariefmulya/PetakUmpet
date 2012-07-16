<?php

namespace PetakUmpet\Form;

class TextboxField implements FormField {

  private $name;
  private $value;
  private $label;

  public function __construct($name=null)
  {
    if ($name === null ) throw new \Exception('TextboxField need to have name');

    $this->name = $name;
  }

  public function setValue($value='')
  {
    $this->value = $value;
  }

  public function getValue()
  {
    return $this->value;
  }

  public function getName()
  {
    return $this->name;
  }

  public function __toString()
  {
    $nm = $this->name;
    $lb = $this->label === null ? ucfirst($nm) : $this->label;

    $s = '<label for="'.$nm.'">'.$lb.'</label><input id="'.$nm.'" name="'.$nm.'" type="text" value="'.$this->value.'" />';

    return $s;
  }
}