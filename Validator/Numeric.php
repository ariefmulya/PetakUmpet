<?php

namespace PetakUmpet\Validator;

class Numeric extends Base {

  private $lt;
  private $le;
  private $ge;
  private $gt;

  public function __construct()
  {
    $this->errorText = 'Please fill-in numeric value';
    $this->lt = null;
    $this->le = null;
    $this->ge = null;
    $this->gt = null;

    return $this;
  }
  
  public function lt($value) { $this->lt = $value; return $this; }
  public function le($value) { $this->le = $value; return $this; }
  public function ge($value) { $this->ge = $value; return $this; }
  public function gt($value) { $this->gt = $value; return $this; }

  public function min($value) { return $this->ge($value); }
  public function max($value) { return $this->le($value); }

  public function check($value=null, $field=null)
  {
    if (!is_numeric($value)) return false;
    if ($this->gt && $value <= $this->gt) { $this->errorText = 'Value must be greater than ' . $this->gt; return false; } 
    if ($this->ge && $value < $this->ge) { $this->errorText = 'Value must be greater than or equal to ' . $this->ge; return false; } 
    if ($this->le && $value > $this->le) { $this->errorText = 'Value must be lesser than or equal to ' . $this->le; return false; } 
    if ($this->lt && $value >= $this->lt) { $this->errorText = 'Value must be lesser than ' . $this->lt; return false; } 

    return true;
  }
  
}
