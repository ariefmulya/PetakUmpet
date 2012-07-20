<?php


namespace PetakUmpet\Form;

class RadioGroup extends GroupFormField {

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    parent::__construct($name, $extra, $label, $id);
    $this->setType('radio');
  }

}