<?php

namespace PetakUmpet\Validator;

class SameWith extends Base {

  private $compareWith;

  public function __construct($errorText='Please make sure it has the same value')
  {
    $numArgs = func_num_args();
    $compare = $numArgs >= 2 ? func_get_arg(1) : null;
    $this->compareWith = $compare;
    $this->errorText = $errorText . ' with ' . ucwords(str_replace("_", " ", $compare)); 
  }

  public function check($value=null, $field=null)
  {
    // cruel haxx
    if (!isset($_POST[$this->compareWith]) || $_POST[$this->compareWith] == '') return true; // nothing to compare, move along
    if ($value === null || $value == '') return false;
    if ($value != $_POST[$this->compareWith]) return false; 

    return true;
  }
  
}
