<?php

namespace PetakUmpet\Form;

class Submit extends BaseFormField {

  public function __construct($name=null, $extra=array('class' => 'btn btn-primary'), $label=null, $id=null)
  {
    if ($name === null) $name = 'Go';
    parent::__construct($name, $extra, $label, $id);
    $this->setType('submit');
    $this->setValue($name);
    $this->setLabel(null);
  }
}