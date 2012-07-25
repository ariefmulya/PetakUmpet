<?php

namespace PetakUmpet\Form\Field;

class Submit extends BaseField {

  public function __construct($name=null, $extra=array('class' => 'btn btn-primary'), $label=null, $id=null)
  {
    $value = $name;
    $name = 'submit';
    parent::__construct($name, $extra, $label, $id);
    $this->setType('submit');
    $this->setValue($value);
    $this->setLabel(null);
  }
}