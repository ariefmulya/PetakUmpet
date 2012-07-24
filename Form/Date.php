<?php

namespace PetakUmpet\Form;

class Date extends Text {

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    $d = new\DateTime;

    $extra['datepicker'] = 'datepicker';

    // set value default to today
    $extra['value'] = $d->format('Y-m-d'); 

    parent::__construct($name, $extra, $label, $id);
  }

  public function setValue($value)
  {
    if ($value) {
      $v = \DateTime::createFromFormat('Y-m-d', $value);
      parent::setValue($v->format('Y-m-d'));
    }
  }
}