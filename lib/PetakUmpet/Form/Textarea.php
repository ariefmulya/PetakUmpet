<?php

namespace PetakUmpet\Form;

class Textarea extends BaseFormField {

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    parent::__construct($name, $extra, $label, $id);

    $this->startTag = '<textarea ';
    $this->endTag = '></textarea>';
    if ($label === null) $this->setLabel($name);
  }

}