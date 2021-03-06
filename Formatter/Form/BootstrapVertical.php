<?php

namespace PetakUmpet\Formatter\Form;

class BootstrapVertical extends BaseFormFormatter {

  protected $form;

  public function __construct($form)
  {
    $this->form = $form;

    $this->formClass = 'form-vertical';
    $this->formStart = '<fieldset>';
    $this->formEnd = '</fieldset>';

    $this->fieldRowStart['normal'] = '<div class="form-group">';
    $this->fieldRowEnd['normal'] = '</div>';

    $this->fieldRowStart['hide'] = '<div class="form-group" style="display: none;">';

    $this->fieldRowStart['error'] = '<div class="form-group error">';
    $this->fieldRowEnd['error'] = '</div>';

    $this->fieldLabelClass = 'form-label';

    $this->fieldStart = '';
    $this->fieldEnd = '';

    $this->fieldHelpTagFormat = '<span class="help-inline">%s</span>';

    $this->actionStart = '<div class="footer">';
    $this->actionEnd = '</div>';
  }


}