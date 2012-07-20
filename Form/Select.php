<?php


namespace PetakUmpet\Form;

class Select extends BaseFormField {

  private options;

  public function __construct($name=null, $extra=null, $label=null, $id=null)
  {
    $extra['datepicker'] = 'datepicker';

    parent::__construct($name, $extra, $label, $id);
  }

  public function setOptions(Form\Options $options)
  {
    $this->options = $options;
  }

}