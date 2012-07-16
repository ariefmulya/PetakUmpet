<?php

namespace PetakUmpet\Form;

class Submit extends BaseFormField {

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    parent::__construct($name === null ? 'Submit' : $name, $extra, $label, $id);
    $this->setType('submit');
    $this->setValue('Go');
  }
}