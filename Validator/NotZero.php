<?php

namespace PetakUmpet\Validator;

class NotZero extends Numeric {

  public function __construct()
  {
    $this->errorText = 'Please fill-in non-zero value';
  }
  
  public function check($value=null, $field=null)
  {
    $ret = parent::check($value);
    if (!$ret) return false;
    if ($value == 0) return false;

    return true;
  }
  
}
