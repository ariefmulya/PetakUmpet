<?php

namespace PetakUmpet\Form;

class Date extends Text {

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    $extra['datepicker'] = 'datepicker';

    parent::__construct($name, $extra, $label, $id);
  }

}