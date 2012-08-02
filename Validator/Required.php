<?php

namespace PetakUmpet\Validator;

class Required extends Base {

  public function check($value=null)
  {
    var_dump($value);
    if ($value === null || $value == '') return false;
    return true;
  }
  
}
