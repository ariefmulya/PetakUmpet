<?php

namespace PetakUmpet\Form;

class SubmitButton implements FormButton {

  private $name;

  public function __construct($name=null)
  {
    $this->name = $name === null ? 'Submit' : $name;
  }

  public function getName()
  {
    return $this->name;
  }
  
  public function __toString()
  {
    $nm = $this->name;

    $s = '<input id="'.$nm.'" name="'.$nm.'" value="'.$nm.'" type="submit" />';

    return $s;
  }
}