<?php

namespace PetakUmpet\Form\Field;

class CheckboxGroup extends GroupField {

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    parent::__construct($name, $extra, $label, $id);
    $this->setType('checkbox');
    $this->multiple = true;
  }

  public function setMultiple($bool)
  {
    $this->multiple = $bool;
  }
}