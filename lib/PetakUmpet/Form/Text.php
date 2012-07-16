<?php

namespace PetakUmpet\Form;

class Text extends BaseFormField {

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    parent::__construct($name, $extra, $label, $id);
    $this->setType('text');
    if ($label === null) $this->setLabel($name);
  }

}