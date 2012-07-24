<?php

namespace PetakUmpet\Form\Field;

class RadioGroup extends GroupField {

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    parent::__construct($name, $extra, $label, $id);
    $this->setType('radio');
  }

}