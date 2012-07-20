<?php

namespace PetakUmpet\Form;

class CheckboxGroup extends GroupFormField {

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    parent::__construct($name, $extra, $label, $id);
    $this->setType('checkbox');
    $this->multiple = true;
  }

}