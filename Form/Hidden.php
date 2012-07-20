<?php

namespace PetakUmpet\Form;

class Hidden extends BaseFormField {

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    parent::__construct($name, $extra, $label, $id);
    $this->setType('hidden');
  }

}