<?php

namespace PetakUmpet\Form;

class Button extends BaseFormField {

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    if ($name === null) $name = 'Button';
    parent::__construct($name, $extra, $label, $id);
    $this->setType('button');
    $this->setValue($name);
    $this->setLabel(null);
  }
}