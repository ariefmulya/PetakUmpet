<?php

namespace PetakUmpet\Form\Field;

class Hidden extends BaseField {

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    parent::__construct($name, $extra, $label, $id);
    $this->setType('hidden');
  }

}