<?php

namespace PetakUmpet\Form\Field;

class Textarea extends BaseField {

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    parent::__construct($name, $extra, $label, $id);

    $this->startTag = '<textarea ';
    $this->closeStartTag = '>';
    $this->endTag = '</textarea>';
    $this->useInnerValue = true;
  }

  public function getInnerValue()
  {
    return $this->getValue();
  }
}