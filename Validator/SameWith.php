<?php

namespace PetakUmpet\Validator;

class SameWith extends Base {

  private $compareWith;

  public function __construct($errorText='Please make sure it has the same value')
  {
    $numArgs = func_num_args();
    $compare = $numArgs >= 2 ? func_get_arg(1) : null;
    $this->compareWith = $compare;
    $this->errorText = $errorText . ' with ' . $compare; 
  }

  public function check($value=null, $field=null)
  {
    if ($value === null || $value == '') return false;

    // cruel haxx
    if ($value != $_POST[$this->compareWith]) return false;

    return true;
  }
  
}
