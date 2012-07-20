<?php

namespace PetakUmpet\Validator;

class Required extends Base {

  public function check($value=null)
  {
    if ($value === null || $value == '') return false;
    return true;
  }
  
}
