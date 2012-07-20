<?php

namespace PetakUmpet\Form;

class Date extends Text {

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    $d = new\DateTime;

    $extra['datepicker'] = 'datepicker';

    // set value default to today
    $extra['value'] = $d->format('d-m-Y'); 

    parent::__construct($name, $extra, $label, $id);
  }

}