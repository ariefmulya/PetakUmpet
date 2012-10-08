<?php

namespace PetakUmpet\Validator;

class Numeric extends Base {

  public function __construct()
  {
    $this->errorText = 'Please fill-in numeric value';
  }
  
  public function check($value=null)
  {
    if (!is_numeric($value)) return false;
    return true;
  }
  
}
