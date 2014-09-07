<?php

namespace PetakUmpet\Formatter\Form;

class AdminLTEForm extends BaseFormFormatter {

  protected $form;

  public function __construct($form)
  {
    $this->form = $form;

    $this->formClass = '';
    $this->formStart = '<div class="body bg-gray">';
    $this->formEnd = '</div>';

    $this->fieldRowStart['normal'] = '<div class="form-group">';
    $this->fieldRowEnd['normal'] = '</div>';

    $this->fieldRowStart['hide'] = '<div class="form-group" style="display: none;">';

    $this->fieldRowStart['error'] = '<div class="form-group error">';
    $this->fieldRowEnd['error'] = '</div>';

    $this->fieldLabelClass = '';

    $this->fieldStart = '';
    $this->fieldEnd = '';

    $this->fieldHelpTagFormat = '<span class="help-inline">%s</span>';

    $this->actionStart = '<div class="footer">';
    $this->actionEnd = '</div>';
  }


}