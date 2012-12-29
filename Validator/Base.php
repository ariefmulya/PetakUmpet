<?php

namespace PetakUmpet\Validator;

class Base {

  protected $errorText;

  protected $name;

  public function __construct($error='Please fill out this field.')
  {
    $this->errorText = $error;
  }

  public function setName($value)
  {
    $this->name = $value;
  }

  public function getErrorText()
  {
    return $this->errorText;
  }

  public function check($value=null, $field=null)
  {
    return true;
  }
}
