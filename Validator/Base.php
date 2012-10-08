<?php

namespace PetakUmpet\Validator;

class Base {

  protected $errorText;

  public function __construct($error='Please fill out this field.')
  {
    $this->errorText = $error;
  }

  public function getErrorText()
  {
    return $this->errorText;
  }
}
